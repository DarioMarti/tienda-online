<?php
session_start();

include 'Cabecera.php';
?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Pago Cancelado - Aetherea</title>
    <link rel='stylesheet' href='../styles/pago-resultado.css'>
</head>
<body>
    <div class='resultado-container'>
        <div class='resultado-card cancelado'>
            <div class='icono-cancelado'></div>
            <h1>Pago Cancelado</h1>
            <p class='mensaje-principal'>Has cancelado el proceso de pago.</p>
            
            <div class='acciones'>
                <a href='carrito-page.php' class='btn btn-primary'>Volver al Carrito</a>
                <a href='../index.php' class='btn btn-secondary'>Seguir Comprando</a>
            </div>
            
            <div class='info-adicional'>
                <p>Tus productos siguen en el carrito.</p>
                <p>Puedes completar tu compra cuando estés listo.</p>
            </div>
        </div>
    </div>
</body>
</html>

<?php include '../src/Footer.html'; ?>
