<?php
// INICIAR SESIÓN
session_start();

// DESTUIR TODAS LAS VARIABLES DE SESIÓN
$_SESSION = array();

// DESTUIR LA SESIÓN
session_destroy();

header("Location: ../../src/index.php");
exit;
?>