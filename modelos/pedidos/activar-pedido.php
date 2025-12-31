<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . "/config/conexion.php";
session_start();

// Verificar seguridad: Solo administradores y empleados
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'empleado'])) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

try {
    $conn = conectar();

    $id = $_GET['id'] ?? null;
    if (!$id)
        throw new Exception("ID de pedido no proporcionado.");

    // Reactivar el pedido
    $stmt = $conn->prepare("UPDATE pedidos SET activo = 1 WHERE id = ?");
    $stmt->execute([$id]);

    if (isset($_GET['redirect'])) {
        header("Location: ../../src/admin-page.php?status=success&message=" . urlencode('Pedido reactivado correctamente.') . "&tab=pedidos");
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Pedido reactivado correctamente.'
    ]);

} catch (Exception $e) {
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