<?php
require_once "../../config/conexion.php";
session_start();

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'empleado'])) {
    header("Location: ../../index.php");
    exit;
}

try {
    $conn = conectar();
    $id = $_GET['id'] ?? null;

    if (!$id) {
        throw new Exception("ID de producto no proporcionado.");
    }

    $stmt = $conn->prepare("UPDATE productos SET activo = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $msg = "Producto reactivado correctamente";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=productos");
} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error al activar: " . $e->getMessage()) . "&tab=productos");
}
exit;
?>