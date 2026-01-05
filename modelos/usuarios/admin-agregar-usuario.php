<?php
require_once "../../config/conexion.php";
ob_start();

// COMPROBAR SI SE TIENE ACCESO
restringirSoloAdminAPI();

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
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            throw new Exception("Error: El email ya está registrado.");
        }

        if (empty($password)) {
            throw new Exception("Error: La contraseña es obligatoria para nuevos usuarios.");
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

        // NO SE PUEDE CAMBIAR EL ROL DE UN ADMINISTRADOR
        if ($id == $_SESSION['usuario']['id'] && $rol !== 'admin') {
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

        // CAMBIAR CONTRASEÑA SI SE INTRODUCE UNA NUEVA
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