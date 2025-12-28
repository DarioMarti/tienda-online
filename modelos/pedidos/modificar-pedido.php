<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . "/config/conexion.php";
session_start();

// Verificar seguridad: Solo administradores
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

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
        $stmtUser = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmtUser->execute([$usuario_email]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            throw new Exception("No existe ningún usuario con el email: " . $usuario_email);
        }
        $usuario_id = $user['id'];
    }

    // 2. Actualizar Detalles (Gestión de stock basada en estado)

    // Obtener estado anterior del pedido
    $stmtStatus = $conn->prepare("SELECT estado FROM pedidos WHERE id = ?");
    $stmtStatus->execute([$pedido_id]);
    $prevOrder = $stmtStatus->fetch(PDO::FETCH_ASSOC);
    $estado_anterior = $prevOrder['estado'] ?? 'pendiente';

    // 1. Si el estado anterior NO era cancelado, restauramos el stock de los items antiguos
    if ($estado_anterior !== 'cancelado') {
        $stmtOld = $conn->prepare("SELECT producto_id, cantidad FROM detalles_pedido WHERE pedido_id = ?");
        $stmtOld->execute([$pedido_id]);
        $oldItems = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

        $stmtRestoreStock = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
        foreach ($oldItems as $oldItem) {
            $stmtRestoreStock->execute([$oldItem['cantidad'], $oldItem['producto_id']]);
        }
    }

    // Cabecera ya ha sido actualizada arriba, pero para el stock nos interesa el nuevo estado
    // Actualizar Cabecera (Movido aquí para que el stock use la lógica correcta)
    $sql = "UPDATE pedidos SET usuario_id = ?, coste_total = ?, estado = ?, nombre_destinatario = ?, direccion_envio = ?, ciudad = ?, provincia = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usuario_id, $coste_total, $estado, $nombre_destinatario, $direccion_envio, $ciudad, $provincia, $pedido_id]);

    // Borrar items antiguos
    $conn->prepare("DELETE FROM detalles_pedido WHERE pedido_id = ?")->execute([$pedido_id]);

    // Insertar nuevos items
    $stmtItem = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmtProduct = $conn->prepare("SELECT precio, stock, nombre FROM productos WHERE id = ? FOR UPDATE");
    $stmtUpdateStock = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

    foreach ($producto_ids as $index => $prod_id) {
        if (empty($prod_id))
            continue;

        $cantidad = intval($cantidades[$index] ?? 1);

        $stmtProduct->execute([$prod_id]);
        $prod = $stmtProduct->fetch(PDO::FETCH_ASSOC);

        if (!$prod)
            throw new Exception("El producto con ID $prod_id no existe.");

        // Solo validamos stock si el nuevo estado NO es cancelado
        if ($estado !== 'cancelado' && $prod['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para '{$prod['nombre']}'. Disponible: {$prod['stock']}, Solicitado: $cantidad");
        }

        $precio_unitario = floatval($prod['precio']);
        $stmtItem->execute([$pedido_id, $prod_id, $cantidad, $precio_unitario]);

        // 2. Si el NUEVEO estado NO es cancelado, restamos stock
        if ($estado !== 'cancelado') {
            $stmtUpdateStock->execute([$cantidad, $prod_id]);
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