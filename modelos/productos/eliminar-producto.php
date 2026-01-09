<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";
ob_start();

restringirAccesoPagina();

try {
    if (!isset($_GET['id'])) {
        throw new Exception("ID de producto no proporcionado.");
    }

    $id = intval($_GET['id']);
    $conn = conectar();

    $stmt = $conn->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);

    $msg = "Producto desactivado con éxito.";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=productos");
} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error al eliminar: " . $e->getMessage()) . "&tab=productos");
}
exit();