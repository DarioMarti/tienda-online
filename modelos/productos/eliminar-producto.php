<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";
ob_start();

// Verificación de seguridad
restringirAccesoAPI();

$exito = false;
$mensaje = '';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn = conectar();

    try {
        // Desactivar registro de la DB (Borrado Lógico)
        $stmt = $conn->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $exito = true;
            $mensaje = 'Producto desactivado con éxito.';
        } else {
            $mensaje = 'El producto no existe o ya estaba desactivado.';
        }
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
    }
} else {
    $mensaje = 'ID de producto no proporcionado.';
}

// Redirigir con feedback (usando el sistema de notificaciones de admin-page.php)
$_SESSION['feedback'] = [
    'tipo' => $exito ? 'exito' : 'error',
    'mensaje' => $mensaje
];

header("Location: ../../src/admin-page.php?tab=productos");
exit();
?>