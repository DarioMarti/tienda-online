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

    // NO PERMMITE LA ELIMINACION DE SU PROPIO USUARIO

    if ($id == $_SESSION['usuario']['id']) {
        throw new Exception("No puedes eliminar tu propia cuenta de administrador desde aquí.");
    }

    $stmt = $conn->prepare("UPDATE usuarios SET activo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $msg = "Usuario desactivado correctamente";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=usuarios");
    exit;

} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error al eliminar: " . $e->getMessage()) . "&tab=usuarios");
    exit;
}
?>