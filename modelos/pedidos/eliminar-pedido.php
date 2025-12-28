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

    $id = $_GET['id'] ?? null;
    if (!$id)
        throw new Exception("ID de pedido no proporcionado.");

    // 1. Obtener estado e items para restaurar stock si no estaba cancelado
    $stmtStatus = $conn->prepare("SELECT estado FROM pedidos WHERE id = ?");
    $stmtStatus->execute([$id]);
    $order = $stmtStatus->fetch(PDO::FETCH_ASSOC);

    if ($order && $order['estado'] !== 'cancelado') {
        $stmtItems = $conn->prepare("SELECT producto_id, cantidad FROM detalles_pedido WHERE pedido_id = ?");
        $stmtItems->execute([$id]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $stmtRestore = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
        foreach ($items as $item) {
            $stmtRestore->execute([$item['cantidad'], $item['producto_id']]);
        }
    }

    // 2. Eliminar pedido (los detalles se eliminan por ON DELETE CASCADE o manualmente si no hay FK)
    // Asumiremos que es mejor borrar detalles explícitamente si no estamos seguros de la FK
    $conn->prepare("DELETE FROM detalles_pedido WHERE pedido_id = ?")->execute([$id]);
    $stmt = $conn->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmt->execute([$id]);

    $conn->commit();

    if (isset($_GET['redirect'])) {
        header("Location: ../../src/admin-page.php?status=success&message=" . urlencode('Pedido eliminado correctamente.') . "&tab=pedidos");
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Pedido eliminado correctamente.'
    ]);

} catch (Exception $e) {
    if (isset($conn))
        $conn->rollBack();

    if (isset($_GET['redirect'])) {
        header("Location: ../../src/admin-page.php?status=error&message=" . urlencode('Error: ' . $e->getMessage()) . "&tab=pedidos");
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}


?>