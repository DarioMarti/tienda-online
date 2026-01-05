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
    echo "Error al eliminar usuario.";
}
?>