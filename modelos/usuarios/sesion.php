<?php
require("../../config/conexion.php");

// CONFIGURAR COOKIE PARA TODOS LOS DOMINIOS Y SE INICIA LA SESIÓN

session_set_cookie_params(0, '/');
session_start();

$conn = conectar();

$emailUsuario = $_POST["email"] ?? "";
$passUsuario = $_POST["pass"] ?? "";

// BUSCAR USUARIO
$consulta = $conn->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
$consulta->execute([":email" => $emailUsuario]);
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

// VERIFICAR SI EXISTE EL USUARIO Y LA CONTRASEÑA ES CORRECTA
if ($usuario && password_verify($passUsuario, $usuario["password"]) && $usuario["activo"] == 1) {
    $_SESSION["usuario"] = [
        "id" => $usuario["id"],
        "nombre" => $usuario["nombre"],
        "apellidos" => $usuario["apellidos"] ?? "",
        "email" => $usuario["email"],
        "rol" => $usuario["rol"] ?? "cliente",
        "telefono" => $usuario["telefono"] ?? "",
        "direccion" => $usuario["direccion"] ?? "",
        "fecha_creacion" => $usuario["fecha_creacion"] ?? "",
        "activo" => $usuario["activo"] ?? ""
    ];

    header("Location: ../../src/index.php");
    exit;
} else {
    echo "<p style='color:red; text-align:center; margin-top:50px;'>Email o contraseña incorrectos.</p>";
    echo "<p style='text-align:center;'><a href='../../src/index.php'>Volver al inicio</a></p>";
}
?>