<?php
require_once "../../config/conexion.php";
ob_start();

// COMPROBAR SI SE TIENE ACCESO
restringirSoloAdminAPI();

try {
    $conn = conectar();
    $id = $_GET['id'] ?? null;

    if (!$id) {
        throw new Exception("ID de usuario no proporcionado.");
    }

    $stmt = $conn->prepare("UPDATE usuarios SET activo = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $msg = "Usuario reactivado correctamente";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=usuarios");
} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error al activar: " . $e->getMessage()) . "&tab=usuarios");
}
exit;
?>