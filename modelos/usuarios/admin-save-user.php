<?php
session_start();
require("../../config/conexion.php");

// Verificar permisos de admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: ../../src/index.php");
    exit;
}

$conn = conectar();

$action = $_POST['action'] ?? '';
$id = $_POST['user_id'] ?? null;
$nombre = $_POST['nombre'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$email = $_POST['email'] ?? '';
$rol = $_POST['rol'] ?? 'cliente';
$activo = $_POST['activo'] ?? 1;
$password = $_POST['password'] ?? '';

try {
    if ($action === 'create') {
        // Validar que el email no exista
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            die("Error: El email ya está registrado.");
        }

        if (empty($password)) {
            die("Error: La contraseña es obligatoria para nuevos usuarios.");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, apellidos, email, password, rol, activo, fecha_creacion) 
                VALUES (:nombre, :apellidos, :email, :password, :rol, :activo, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':password' => $passwordHash,
            ':rol' => $rol,
            ':activo' => $activo
        ]);

    } elseif ($action === 'update' && $id) {
        // Protección: No cambiar el propio rol
        if ($id == $_SESSION['usuario']['id'] && $rol !== 'admin') {
            // Si intenta cambiarse a sí mismo a no-admin, forzamos admin
            $rol = 'admin';
        }

        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email, rol = :rol, activo = :activo";
        $params = [
            ':nombre' => $nombre,
            ':apellidos' => $apellidos,
            ':email' => $email,
            ':rol' => $rol,
            ':activo' => $activo,
            ':id' => $id
        ];

        // Si hay contraseña nueva, actualizarla
        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }

    // Respuesta de éxito
    $msg = ($action === 'create') ? 'Usuario creado correctamente' : 'Usuario actualizado correctamente';
    echo json_encode([
        'success' => true,
        'message' => $msg
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => "Error de base de datos: " . $e->getMessage()
    ]);
    exit;
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>