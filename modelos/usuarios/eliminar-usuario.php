<?php

require("../../config/conexion.php");

try {
    session_start();
    $conn = conectar();

    if (!isset($_SESSION["usuario"])) {
        header("Location: ../../src/index.php");
        exit;
    }

    $id = $_SESSION["usuario"]["id"];

    $stmt = $conn->prepare("UPDATE usuarios SET activo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    session_destroy();
    header("Location: ../../src/index.php");
    exit;

} catch (Exception $e) {
    error_log("Error al eliminar usuario: " . $e->getMessage());
    // In case of error, we might want to redirect back to profile with error or show JSON if it was an AJAX call.
    // But since this is a direct link navigation (as per previous step), we should probably redirect or show error.
    // For now, keeping it simple as per user request.
    echo "Error al eliminar usuario.";
}
?>