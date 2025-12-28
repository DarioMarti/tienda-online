<?php
session_start();
require_once dirname(__DIR__, 2) . "/config/conexion.php";

// Verificación de seguridad: Solo administradores
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
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
            // Eliminar registro de la DB
            $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
            if ($stmt->execute([$id])) {
                // Si tenía imagen, borrar el archivo
                if (!empty($producto['imagen'])) {
                    $imgPath = dirname(__DIR__, 2) . '/' . $producto['imagen'];
                    if (file_exists($imgPath)) {
                        unlink($imgPath);
                    }
                }
                $success = true;
                $message = 'Producto eliminado con éxito.';
            } else {
                $message = 'Error al eliminar el producto de la base de datos.';
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