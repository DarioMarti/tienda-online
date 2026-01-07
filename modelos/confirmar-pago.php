<?php
// modelos/confirmar-pago.php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe_config.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        STRIPE_WEBHOOK_SECRET
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

if ($event->type == 'checkout.session.completed') {
    $session = $event->data->object;

    $usuario_id = $session->metadata->usuario_id;
    $stripe_session_id = $session->id;
    $amount_total = $session->amount_total / 100; // Stripe devuelve en céntimos
    $customer_email = $session->customer_details->email;

    // INICIA LA TRANSACCION
    if ($conexion instanceof mysqli) {
        $conexion->begin_transaction();
    }

    try {
        // CREA EL PEDIDO
        $stmt = $conexion->prepare("INSERT INTO pedidos (id_usuario, fecha_pedido, total, estado, metodo_pago, id_transaccion_stripe, email_contacto) VALUES (?, NOW(), ?, 'completado', 'stripe', ?, ?)");

        $stmt->bind_param("idss", $usuario_id, $amount_total, $stripe_session_id, $customer_email);
        $stmt->execute();
        $pedido_id = $conexion->insert_id;
        $stmt->close();

        // MUEVE LOS ITEMS DEL CARRITO A DETALLE PEDIDOS
        $stmt_carrito = $conexion->prepare("SELECT id_producto, cantidad FROM carrito WHERE id_usuario = ?");
        $stmt_carrito->bind_param("i", $usuario_id);
        $stmt_carrito->execute();
        $result_carrito = $stmt_carrito->get_result();

        $stmt_detalle = $conexion->prepare("INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, (SELECT precio FROM productos WHERE id = ?))");

        while ($row = $result_carrito->fetch_assoc()) {
            $stmt_detalle->bind_param("iiid", $pedido_id, $row['id_producto'], $row['cantidad'], $row['id_producto']);
            $stmt_detalle->execute();
        }
        $stmt_carrito->close();
        $stmt_detalle->close();

        // VACIA EL CARRITO
        $stmt_borrar = $conexion->prepare("DELETE FROM carrito WHERE id_usuario = ?");
        $stmt_borrar->bind_param("i", $usuario_id);
        $stmt_borrar->execute();
        $stmt_borrar->close();

        // CONFIRMAR LA TRANSACCION
        if ($conexion instanceof mysqli) {
            $conexion->commit();
        }
        http_response_code(200);

    } catch (Exception $e) {
        if ($conexion instanceof mysqli) {
            $conexion->rollback();
        }
        error_log("Error al procesar pedido Stripe: " . $e->getMessage());
        http_response_code(500);
    }
} else {
    http_response_code(200);
}
?>