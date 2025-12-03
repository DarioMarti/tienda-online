<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "tienda_online";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// echo "Conexión exitosa"; // Descomentar para probar
?>
