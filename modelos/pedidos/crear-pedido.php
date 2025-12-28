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

    if (empty($nombre_destinatario) || empty($direccion_envio)) {
        throw new Exception("Nombre del destinatario y dirección son obligatorios.");
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

    // 1. Insertar Cabecera del Pedido
    $sql = "INSERT INTO pedidos (usuario_id, coste_total, estado, nombre_destinatario, direccion_envio, ciudad, provincia) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$usuario_id, $coste_total, $estado, $nombre_destinatario, $direccion_envio, $ciudad, $provincia]);

    $pedido_id = $conn->lastInsertId();

    // 2. Insertar Detalles del Pedido y Actualizar Stock
    $stmtItem = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmtProduct = $conn->prepare("SELECT precio, stock, nombre FROM productos WHERE id = ? FOR UPDATE");
    $stmtUpdateStock = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

    foreach ($producto_ids as $index => $prod_id) {
        if (empty($prod_id))
            continue;

        $cantidad = intval($cantidades[$index] ?? 1);

        // Obtener datos actuales del producto (BLOQUEANDO la fila para evitar condiciones de carrera)
        $stmtProduct->execute([$prod_id]);
        $prod = $stmtProduct->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            throw new Exception("El producto con ID $prod_id no existe.");
        }

        if ($prod['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para '{$prod['nombre']}'. Disponible: {$prod['stock']}, Solicitado: $cantidad");
        }

        $precio_unitario = floatval($prod['precio']);

        // Insertar detalle
        $stmtItem->execute([$pedido_id, $prod_id, $cantidad, $precio_unitario]);

        // Restar stock solo si el pedido NO nace cancelado
        if ($estado !== 'cancelado') {
            $stmtUpdateStock->execute([$cantidad, $prod_id]);
        }
    }

    $conn->commit();


    echo json_encode([
        'success' => true,
        'message' => 'Pedido creado correctamente.'
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