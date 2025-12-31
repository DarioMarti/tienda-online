<?php
/**
 * Script para enviar mensajes del formulario de contacto
 */

header('Content-Type: application/json');

// Cargar configuración de email
$emailConfig = require_once dirname(__DIR__, 2) . '/config/email-config.php';

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Obtener y validar datos del formulario
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validaciones
    $errors = [];

    if (empty($name)) {
        $errors[] = 'El nombre es obligatorio';
    } elseif (strlen($name) < 2) {
        $errors[] = 'El nombre debe tener al menos 2 caracteres';
    }

    if (empty($email)) {
        $errors[] = 'El email es obligatorio';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email no es válido';
    }

    if (empty($message)) {
        $errors[] = 'El mensaje es obligatorio';
    } elseif (strlen($message) < 10) {
        $errors[] = 'El mensaje debe tener al menos 10 caracteres';
    }

    // Si hay errores, devolverlos
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode('. ', $errors)
        ]);
        exit;
    }

    // Sanitizar datos
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Preparar el email
    $to = $emailConfig['to_email'];
    $subject = $emailConfig['subject_prefix'] . ' Nuevo mensaje de ' . $name;

    // Construir el cuerpo del mensaje en HTML
    $htmlMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #111111; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .field { margin-bottom: 20px; }
            .label { font-weight: bold; color: #666; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
            .value { margin-top: 5px; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>NUEVO MENSAJE DE CONTACTO</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Nombre</div>
                    <div class='value'>{$name}</div>
                </div>
                <div class='field'>
                    <div class='label'>Email</div>
                    <div class='value'>{$email}</div>
                </div>
                <div class='field'>
                    <div class='label'>Mensaje</div>
                    <div class='value'>" . nl2br($message) . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>Este mensaje fue enviado desde el formulario de contacto de Aetheria</p>
                <p>Fecha: " . date('d/m/Y H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Headers del email
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=' . $emailConfig['charset'],
        'From: ' . $emailConfig['from_name'] . ' <' . $emailConfig['from_email'] . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
        'X-Mailer: PHP/' . phpversion()
    ];

    // Intentar enviar el email
    $sent = @mail($to, $subject, $htmlMessage, implode("\r\n", $headers));

    if ($sent) {
        echo json_encode([
            'success' => true,
            'message' => 'Gracias. Tu mensaje ha sido enviado correctamente.'
        ]);
    } else {
        // Si mail() falla, guardar en archivo como respaldo
        $messagesDir = dirname(__DIR__, 2) . '/logs';
        if (!file_exists($messagesDir)) {
            mkdir($messagesDir, 0755, true);
        }

        $logFile = $messagesDir . '/contact-messages.txt';
        $logEntry = sprintf(
            "[%s]\nNombre: %s\nEmail: %s\nMensaje: %s\n%s\n\n",
            date('Y-m-d H:i:s'),
            $name,
            $email,
            $message,
            str_repeat('-', 80)
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND);

        // Devolver éxito ya que se guardó el mensaje
        echo json_encode([
            'success' => true,
            'message' => 'Gracias. Tu mensaje ha sido recibido correctamente.'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hubo un error al procesar tu mensaje. Por favor, inténtalo de nuevo más tarde.'
    ]);

    // Log del error
    error_log('Error en formulario de contacto: ' . $e->getMessage());
}
