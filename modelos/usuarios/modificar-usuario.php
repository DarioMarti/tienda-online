<?php
require_once "../../config/conexion.php";
ob_start();

header('Content-Type: application/json');

// Verificar que el usuario está logueado
restringirInvitadosAPI();

try {
    $conn = conectar();

    $nombre = trim($_POST["nombre"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $email = $_SESSION["usuario"]["email"];

    // COMPROBAR NOMBRE
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
        exit;
    }

    // ACTUALIZAR BASE DE DATOS
    $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, telefono = :telefono, direccion = :direccion WHERE email = :email";

    $statement = $conn->prepare($sql);
    $resultado = $statement->execute([
        ":nombre" => $nombre,
        ":apellidos" => $apellidos,
        ":telefono" => $telefono,
        ":direccion" => $direccion,
        ":email" => $email
    ]);

    $filasAfectadas = $statement->rowCount();

    // ACTUALIZAR SESIÓN
    $_SESSION["usuario"]["nombre"] = $nombre;
    $_SESSION["usuario"]["apellidos"] = $apellidos;
    $_SESSION["usuario"]["telefono"] = $telefono;
    $_SESSION["usuario"]["direccion"] = $direccion;

    $respuesta = [
        'success' => true,
        'message' => 'Datos actualizados correctamente',
        'data' => [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'telefono' => $telefono,
            'direccion' => $direccion
        ]
    ];

    echo json_encode($respuesta);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
}
?>