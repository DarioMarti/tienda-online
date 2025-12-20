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
        throw new Exception("ID de usuario no proporcionado.");
    }

    // Seguridad: No permitir auto-eliminación
    if ($id == $_SESSION['usuario']['id']) {
        throw new Exception("No puedes eliminar tu propia cuenta de administrador desde aquí.");
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $msg = "Usuario eliminado correctamente";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=usuarios");
    exit;

} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error al eliminar: " . $e->getMessage()) . "&tab=usuarios");
    exit;
}
?>