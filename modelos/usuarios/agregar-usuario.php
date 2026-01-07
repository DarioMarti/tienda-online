<?php

//RECIBIR DATOS

$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$pass = $_POST["contraseña"] ?? "";


//VALIDAR DATOS

$errores = [];

if ($nombre === "") {
    $errores[] = "El nombre es obligatorio.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El email no es válido.";
}

//VALIDAR CONTRASEÑA

if (strlen($pass) < 6 && strlen($pass)) {
    $errores[] = "La contraseña debe tener al menos 6 caracteres.";
}

if (!preg_match('/[A-Z]/', $pass)) {
    $errores[] = "La contraseña debe contener al menos una mayúscula.";
}

if (!preg_match('/[0-9]/', $pass)) {
    $errores[] = "La contraseña debe contener al menos un número.";
}



if (!empty($errores)) {
    foreach ($errores as $e) {
        echo "<p style='color:red;'>$e</p>";
    }
    exit;
}



// CONECTAR A BASE DE DATOS

require("../../config/conexion.php");

$conn = conectar();


//COMPROBAR SI EL EMAIL YA EXISTE

$consultaEmail = $conn->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
$consultaEmail->execute([":email" => $email]);

if ($consultaEmail->fetch()) {
    echo "<p style='color:red;'>Este email ya está registrado.</p>";
    exit;
}


//ENCRYPTAR CONTRASEÑA


$hash = password_hash($pass, PASSWORD_DEFAULT);


//INSERTAR NUEVO USUARIO
try {

    $consulta = "INSERT INTO usuarios (nombre, email, password) 
                 VALUES (:nombre, :email, :pass)";

    $stmt = $conn->prepare($consulta);
    $stmt->execute([
        ":nombre" => $nombre,
        ":email" => $email,
        ":pass" => $hash
    ]);

    header("location:../../src/usuario-registrado-page.php");

} catch (PDOException $e) {
    echo '
<div id="errorModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Error</h2>
        <p>Error al registrar usuario: ' . $e->getMessage() . '</p>
    </div>
</div>
';
}

?>