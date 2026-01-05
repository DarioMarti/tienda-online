<?php
require_once "../../config/conexion.php";
ob_start();

// Verificar permisos
restringirAccesoAPI();

try {
    $conn = conectar();
    $id = $_GET['id'] ?? null;

    if (!$id) {
        throw new Exception("ID de categoría no proporcionado.");
    }

    // COMPROBAR SI ES UNA CATEGORÍA PADRE

    $stmtComprobarEsPadre = $conn->prepare("SELECT COUNT(*) FROM categorias WHERE categoria_padre_id = :id");
    $stmtComprobarEsPadre->execute([':id' => $id]);
    if ($stmtComprobarEsPadre->fetchColumn() > 0) {
        throw new Exception("No se puede eliminar esta categoría porque tiene subcategorías asociadas.");
    }

    $stmt = $conn->prepare("UPDATE categorias SET activo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $msg = "Categoría desactivada correctamente";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=categorias");
    exit;

} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error al eliminar: " . $e->getMessage()) . "&tab=categorias");
    exit;
}
?>