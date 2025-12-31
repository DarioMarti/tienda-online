<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Sin datos recibidos']);
    exit();
}

// Guardar en sesión para usarlo después de la confirmación de Stripe
$_SESSION['datos_envio'] = [
    'nombre_destinatario' => $input['nombre_destinatario'] ?? '',
    'email_contacto' => $input['email_contacto'] ?? '',
    'direccion' => $input['direccion'] ?? '',
    'cp' => $input['cp'] ?? '',
    'ciudad' => $input['ciudad'] ?? ''
];

echo json_encode(['success' => true]);
?>