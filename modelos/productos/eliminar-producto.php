<?php
session_start();
require_once dirname(__DIR__, 2) . "/config/conexion.php";

// Verificación de seguridad: Solo administradores y empleados
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'empleado'])) {
    header("Location: ../../src/index.php");
    exit();
}

$success = false;
$message = '';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn = conectar();

    try {
        // Primero obtenemos la ruta de la imagen para borrar el archivo físico
        $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Desactivar registro de la DB (Borrado Lógico)
            $stmt = $conn->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = true;
                $message = 'Producto desactivado con éxito.';
            } else {
                $message = 'Error al desactivar el producto.';
            }
        } else {
            $message = 'El producto no existe.';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
} else {
    $message = 'ID de producto no proporcionado.';
}

// Redirigir con feedback (usando el sistema de notificaciones de admin-page.php)
$_SESSION['feedback'] = [
    'tipo' => $success ? 'exito' : 'error',
    'mensaje' => $message
];

header("Location: ../../src/admin-page.php?tab=productos");
exit();
?>