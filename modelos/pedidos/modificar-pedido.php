<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . "/config/conexion.php";
ob_start();

// Verificación de seguridad
restringirAccesoAPI();

try {
    $conn = conectar();
    $conn->beginTransaction();

    $pedido_id = isset($_POST['pedido_id']) ? intval($_POST['pedido_id']) : null;
    $usuario_email = !empty($_POST['usuario_email']) ? trim($_POST['usuario_email']) : null;
    $coste_total = floatval($_POST['coste_total'] ?? 0);
    $estado = $_POST['estado'] ?? 'pendiente';
    $nombre_destinatario = $_POST['nombre_destinatario'] ?? '';
    $direccion_envio = $_POST['direccion_envio'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $provincia = $_POST['provincia'] ?? '';

    // Datos de productos
    $producto_ids = $_POST['producto_id'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];

    if (!$pedido_id) {
        throw new Exception("ID de pedido no proporcionado.");
    }

    if (empty($producto_ids)) {
        throw new Exception("El pedido debe tener al menos un producto.");
    }

    $usuario_id = null;
    if ($usuario_email) {
        $stmtUsuario = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmtUsuario->execute([$usuario_email]);
        $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        if (!$usuario) {
            throw new Exception("No existe ningún usuario con el email: " . $usuario_email);
        }
        $usuario_id = $usuario['id'];
    }

    // 2. Actualizar Detalles (Gestión de stock basada en estado)

    // Obtener estado anterior del pedido
    $stmtEstadoAnterior = $conn->prepare("SELECT estado FROM pedidos WHERE id = ?");
    $stmtEstadoAnterior->execute([$pedido_id]);
    $estadoAnterior = $stmtEstadoAnterior->fetch(PDO::FETCH_ASSOC);
    $estado_anterior = $estadoAnterior['estado'] ?? 'pendiente';

    // 1. Si el estado anterior NO era cancelado, restauramos el stock de los items antiguos
    if ($estado_anterior !== 'cancelado') {
        $stmtItemsAntiguos = $conn->prepare("SELECT producto_id, cantidad FROM detalles_pedido WHERE pedido_id = ?");
        $stmtItemsAntiguos->execute([$pedido_id]);
        $itemsAntiguos = $stmtItemsAntiguos->fetchAll(PDO::FETCH_ASSOC);

        $stmtRestoreStock = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
        foreach ($itemsAntiguos as $itemAntiguo) {
            $stmtRestoreStock->execute([$itemAntiguo['cantidad'], $itemAntiguo['producto_id']]);
        }
    }

    // ACTUALIZA EL PEDIDO
    $sql = "UPDATE pedidos SET usuario_id = ?, coste_total = ?, estado = ?, nombre_destinatario = ?, direccion_envio = ?, ciudad = ?, provincia = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usuario_id, $coste_total, $estado, $nombre_destinatario, $direccion_envio, $ciudad, $provincia, $pedido_id]);

    // BORRA LOS ITEMS ANTERIORES
    $conn->prepare("DELETE FROM detalles_pedido WHERE pedido_id = ?")->execute([$pedido_id]);

    // INSERTA LOS NUEVOS ITEMS
    $stmtItem = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmtProducto = $conn->prepare("SELECT precio, stock, nombre FROM productos WHERE id = ? FOR UPDATE");
    $stmtActualizarStock = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

    foreach ($producto_ids as $index => $prod_id) {
        if (empty($prod_id))
            continue;

        $cantidad = intval($cantidades[$index] ?? 1);

        $stmtProducto->execute([$prod_id]);
        $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

        if (!$producto)
            throw new Exception("El producto con ID $prod_id no existe.");

        // Solo validamos stock si el nuevo estado NO es cancelado
        if ($estado !== 'cancelado' && $producto['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para '{$producto['nombre']}'. Disponible: {$producto['stock']}, Solicitado: $cantidad");
        }

        $precio_unitario = floatval($producto['precio']);
        $stmtItem->execute([$pedido_id, $prod_id, $cantidad, $precio_unitario]);

        // 2. Si el NUEVEO estado NO es cancelado, restamos stock
        if ($estado !== 'cancelado') {
            $stmtActualizarStock->execute([$cantidad, $prod_id]);
        }
    }

    $conn->commit();


    echo json_encode([
        'success' => true,
        'message' => 'Pedido actualizado correctamente.'
    ]);

} catch (Exception $e) {
    if (isset($conn))
        $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>