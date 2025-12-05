<?php
session_start();
require("../../config/conexion.php");

// Log para depuración
error_log("=== INICIO modificar-usuario.php ===");
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

header('Content-Type: application/json');

// Verificar que el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    error_log("ERROR: Usuario no autenticado");
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit;
}

try {
    $conn = conectar();
    error_log("Conexión establecida");

    $nombre = trim($_POST["nombre"] ?? "");
    $apellidos = trim($_POST["apellidos"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $email = $_SESSION["usuario"]["email"];

    error_log("Datos recibidos - Nombre: $nombre, Apellidos: $apellidos, Email: $email");

    // Validaciones básicas
    if (empty($nombre)) {
        error_log("ERROR: Nombre vacío");
        echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
        exit;
    }

    // Actualizar en la base de datos
    $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, telefono = :telefono, direccion = :direccion WHERE email = :email";

    error_log("SQL: $sql");

    $statement = $conn->prepare($sql);
    $result = $statement->execute([
        ":nombre" => $nombre,
        ":apellidos" => $apellidos,
        ":telefono" => $telefono,
        ":direccion" => $direccion,
        ":email" => $email
    ]);

    $rowsAffected = $statement->rowCount();
    error_log("Filas afectadas: $rowsAffected");

    if ($rowsAffected === 0) {
        error_log("ADVERTENCIA: No se actualizó ninguna fila");
    }

    // Actualizar la sesión con los nuevos datos
    $_SESSION["usuario"]["nombre"] = $nombre;
    $_SESSION["usuario"]["apellidos"] = $apellidos;
    $_SESSION["usuario"]["telefono"] = $telefono;
    $_SESSION["usuario"]["direccion"] = $direccion;

    error_log("Sesión actualizada");
    error_log("Nueva sesión: " . print_r($_SESSION['usuario'], true));

    $response = [
        'success' => true,
        'message' => 'Datos actualizados correctamente',
        'data' => [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'telefono' => $telefono,
            'direccion' => $direccion
        ],
        'debug' => [
            'rowsAffected' => $rowsAffected,
            'email' => $email
        ]
    ];

    error_log("Respuesta: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("EXCEPCIÓN: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
}

error_log("=== FIN modificar-usuario.php ===");
?>