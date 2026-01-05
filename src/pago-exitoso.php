<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

$session_id = $_GET['session_id'] ?? null;

include 'Cabecera.php';
?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Pago Exitoso - Aetherea</title>
    <link rel='stylesheet' href='../styles/pago-resultado.css'>
</head>
<body>
    <div class='resultado-container'>
        <div class='resultado-card exito'>
            <div class='icono-exito'></div>
            <h1>¡Pago Realizado con Éxito!</h1>
            <p class='mensaje-principal'>Gracias por tu compra. Tu pedido ha sido procesado correctamente.</p>
            
            <?php if ($session_id): ?>
                <p class='session-info'>ID de sesión: <code><?= htmlspecialchars($session_id) ?></code></p>
            <?php endif; ?>
            
            <div class='acciones'>
                <a href='perfil-page.php' class='btn btn-primary'>Ver Mis Pedidos</a>
                <a href='../index.php' class='btn btn-secondary'>Volver a la Tienda</a>
            </div>
            
            <div class='info-adicional'>
                <p>Recibirás un correo electrónico con los detalles de tu pedido.</p>
                <p>El tiempo estimado de entrega es de 3-5 días hábiles.</p>
            </div>
        </div>
    </div>
</body>
</html>

<?php include '../src/Footer.html'; ?>
