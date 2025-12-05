<?php
require("../../config/conexion.php");
session_start();

$conn = conectar();

$emailUsuario = $_POST["email"] ?? "";
$passUsuario = $_POST["pass"] ?? "";

// Buscar usuario solo por email
$consulta = $conn->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
$consulta->execute([":email" => $emailUsuario]);

// Obtener el usuario
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

// Verificar si existe el usuario y la contraseña es correcta
if ($usuario && password_verify($passUsuario, $usuario["password"])) {
    // Crear sesión con todos los datos necesarios
    $_SESSION["usuario"] = [
        "nombre" => $usuario["nombre"],
        "apellidos" => $usuario["apellidos"] ?? "",
        "email" => $usuario["email"],
        "rol" => $usuario["rol"] ?? "cliente",
        "telefono" => $usuario["telefono"] ?? "",
        "direccion" => $usuario["direccion"] ?? ""
    ];

    // Redirigir a la página principal
    header("Location: ../../src/index.php");
    exit;
} else {
    // Login incorrecto
    echo "<p style='color:red; text-align:center; margin-top:50px;'>Email o contraseña incorrectos.</p>";
    echo "<p style='text-align:center;'><a href='../../src/index.php'>Volver al inicio</a></p>";
}
?>