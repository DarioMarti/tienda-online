<?php
header('Content-Type: application/json');
require_once dirname(__DIR__, 2) . "/config/conexion.php";
ob_start();

// Verificación de seguridad
restringirAccesoAPI();

try {
    $conn = conectar();
    $conn->beginTransaction();

    $id = $_GET['id'] ?? null;
    if (!$id)
        throw new Exception("ID de pedido no proporcionado.");

    $stmtEstado = $conn->prepare("SELECT estado FROM pedidos WHERE id = ?");
    $stmtEstado->execute([$id]);
    $pedido = $stmtEstado->fetch(PDO::FETCH_ASSOC);

    if ($pedido && $pedido['estado'] !== 'cancelado') {
        $stmtItems = $conn->prepare("SELECT producto_id, cantidad FROM detalles_pedido WHERE pedido_id = ?");
        $stmtItems->execute([$id]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $stmtRestore = $conn->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
        foreach ($items as $item) {
            $stmtRestore->execute([$item['cantidad'], $item['producto_id']]);
        }
    }


    $stmt = $conn->prepare("UPDATE pedidos SET activo = 0 WHERE id = ?");
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