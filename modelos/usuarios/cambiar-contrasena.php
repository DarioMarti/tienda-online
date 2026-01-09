<?php
require_once "../../config/conexion.php";
ob_start();
header('Content-Type: application/json');

restringirInvitadosAPI();

try {
    $conn = conectar();

    $contrasenaActual = $_POST['current_password'] ?? '';
    $contrasenaNueva = $_POST['new_password'] ?? '';
    $confirmarContrasenaNueva = $_POST['confirm_password'] ?? '';
    $email = $_SESSION['usuario']['email'];

    // VALIDACIONES
    if (empty($contrasenaActual) || empty($contrasenaNueva) || empty($confirmarContrasenaNueva)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }

    if ($contrasenaNueva !== $confirmarContrasenaNueva) {
        echo json_encode(['success' => false, 'message' => 'las contraseñas nuevas no coinciden']);
        exit;
    }

    if (strlen($contrasenaNueva) < 6) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres']);
        exit;
    }

    // OBTENER LA CONTRASEÑA ACTUAL DE LA BASE DE DATOS
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuarioContrasenaActual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioContrasenaActual || !password_verify($contrasenaActual, $usuarioContrasenaActual['password'])) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
        exit;
    }

    // ACTUALIZAR LA CONTRASEÑA
    $contrasenaNuevaHash = password_hash($contrasenaNueva, PASSWORD_DEFAULT);
    $actualizarStmt = $conn->prepare("UPDATE usuarios SET password = :password WHERE email = :email");
    $actualizarStmt->execute([
        ':password' => $contrasenaNuevaHash,
        ':email' => $email
    ]);

    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);

} catch (Exception $e) {
    error_log("Error al cambiar contraseña: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error del servidor al actualizar la contraseña']);
}
?>