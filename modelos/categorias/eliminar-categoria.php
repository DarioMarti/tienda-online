<?php
require('../../config/conexion.php');
session_start();

// Verificar rol de admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

try {
    $conn = conectar();
    $id = $_GET['id'] ?? null;

    if (!$id) {
        throw new Exception("ID de categoría no proporcionado.");
    }

    // Opcional: Verificar si tiene hijos antes de borrar
    $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM categorias WHERE categoria_padre_id = :id");
    $stmtCheck->execute([':id' => $id]);
    if ($stmtCheck->fetchColumn() > 0) {
        throw new Exception("No se puede eliminar esta categoría porque tiene subcategorías asociadas.");
    }

    $stmt = $conn->prepare("DELETE FROM categorias WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $msg = "Categoría eliminada correctamente";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=categorias");
    exit;

} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error al eliminar: " . $e->getMessage()) . "&tab=categorias");
    exit;
}
?>