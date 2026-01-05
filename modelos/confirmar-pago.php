<?php
/**
 * Webhook de Stripe para confirmar pagos
 * 
 * Este endpoint recibe notificaciones de Stripe cuando un pago se completa.
 * IMPORTANTE: Configura este webhook en tu dashboard de Stripe:
 * https://dashboard.stripe.com/webhooks
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe_config.php';
require_once __DIR__ . '/../config/conexion.php';

// Stripe Webhook Secret (obtén esto del dashboard de Stripe)
// Por seguridad, debería estar en stripe_config.php
define('STRIPE_WEBHOOK_SECRET', 'whsec_TU_WEBHOOK_SECRET_AQUI');

header('Content-Type: application/json');

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    // Verificar la firma del webhook
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        STRIPE_WEBHOOK_SECRET
    );

    // Manejar el evento
    switch ($event->type) {
        case 'checkout.session.completed':
            $session = $event->data->object;
            
            // Procesar el pago exitoso
            procesarPagoExitoso($session);
            break;

        case 'payment_intent.succeeded':
            // Pago confirmado
            $paymentIntent = $event->data->object;
            break;

        case 'payment_intent.payment_failed':
            // Pago fallido
            $paymentIntent = $event->data->object;
            registrarPagoFallido($paymentIntent);
            break;

        default:
            // Evento no manejado
            error_log('Evento de Stripe no manejado: ' . $event->type);
    }

    http_response_code(200);
    echo json_encode(['status' => 'success']);

} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Firma inválida
    http_response_code(400);
    echo json_encode(['error' => 'Firma de webhook inválida']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    error_log('Error en webhook: ' . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor']);
    exit;
}

/**
 * Procesa un pago exitoso
 */
function procesarPagoExitoso($session) {
    $pdo = conectar();
    
    try {
        $pdo->beginTransaction();
        
        $usuario_id = $session->metadata->usuario_id ?? $session->client_reference_id;
        $session_id = $session->id;
        $payment_intent_id = $session->payment_intent;
        $total = $session->amount_total / 100; // Convertir de centavos
        
        // Obtener items del carrito
        $stmt = $pdo->prepare("
            SELECT c.producto_id, c.talla_id, c.cantidad, p.precio, p.stock
            FROM carrito c
            JOIN productos p ON c.producto_id = p.id
            WHERE c.usuario_id = ?
        ");
        $stmt->execute([$usuario_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($items)) {
            throw new Exception('Carrito vacío');
        }
        
        // Crear el pedido
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (
                usuario_id, 
                total, 
                estado, 
                fecha_pedido,
                stripe_session_id,
                stripe_payment_intent_id,
                metodo_pago
            ) VALUES (?, ?, 'pendiente', NOW(), ?, ?, 'card')
        ");
        $stmt->execute([$usuario_id, $total, $session_id, $payment_intent_id]);
        $pedido_id = $pdo->lastInsertId();
        
        // Insertar detalles del pedido y actualizar stock
        foreach ($items as $item) {
            // Verificar stock
            if ($item['cantidad'] > $item['stock']) {
                throw new Exception("Stock insuficiente para producto {$item['producto_id']}");
            }
            
            // Insertar detalle del pedido
            $stmt = $pdo->prepare("
                INSERT INTO detalles_pedido (
                    pedido_id, 
                    producto_id, 
                    talla_id, 
                    cantidad, 
                    precio_unitario
                ) VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $pedido_id,
                $item['producto_id'],
                $item['talla_id'],
                $item['cantidad'],
                $item['precio']
            ]);
            
            // Actualizar stock
            $stmt = $pdo->prepare("
                UPDATE productos 
                SET stock = stock - ? 
                WHERE id = ?
            ");
            $stmt->execute([$item['cantidad'], $item['producto_id']]);
        }
        
        // Vaciar el carrito
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        
        $pdo->commit();
        
        error_log("Pedido #{$pedido_id} creado exitosamente para usuario #{$usuario_id}");
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Error al procesar pago: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Registra un pago fallido
 */
function registrarPagoFallido($paymentIntent) {
    error_log('Pago fallido: ' . $paymentIntent->id);
    // Aquí podrías enviar un email al usuario o registrar en base de datos
}
?>
