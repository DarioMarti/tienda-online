<?php
require_once "../../config/conexion.php";
ob_start();

header('Content-Type: application/json');

// Verificar que el usuario está logueado
restringirInvitadosAPI();

try {
    $conn = conectar();

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = $_SESSION['usuario']['email'];

    // Validaciones básicas
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'las contraseñas nuevas no coinciden']);
        exit;
    }

    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres']);
        exit;
    }

    // Obtener la contraseña actual de la base de datos
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
        exit;
    }

    // Actualizar la contraseña
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE usuarios SET password = :password WHERE email = :email");
    $updateStmt->execute([
        ':password' => $newPasswordHash,
        ':email' => $email
    ]);

    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);

} catch (Exception $e) {
    error_log("Error al cambiar contraseña: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error del servidor al actualizar la contraseña']);
}
?>