<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . "/config/conexion.php";
ob_start();

restringirAccesoAPI();

try {
    $conn = conectar();

    $id = $_GET['id'] ?? null;
    if (!$id)
        throw new Exception("ID de pedido no proporcionado.");

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