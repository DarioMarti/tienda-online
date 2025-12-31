<?php
session_start();
require_once "../../config/conexion.php";
require_once "../../config/stripe-config.php";

// Si venimos de Stripe, recibiremos payment_intent por GET
if (isset($_GET['payment_intent'])) {
    $payment_intent_id = $_GET['payment_intent'];

    // Verificar el estado del pago con Stripe mediante la API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payment_intents/$payment_intent_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');

    $result = curl_exec($ch);
    $intent = json_decode($result, true);
    curl_close($ch);

    if (isset($intent['status']) && $intent['status'] === 'succeeded') {
        // --- 1. PAGO CONFIRMADO CON ÉXITO ---

        $conn = conectar();

        try {
            $conn->beginTransaction();

            $usuario_id = isset($_SESSION['usuario']) ? $_SESSION['usuario']['id'] : null;
            $datos_envio = isset($_SESSION['datos_envio']) ? $_SESSION['datos_envio'] : [];
            $carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

            if (empty($carrito)) {
                throw new Exception("El carrito está vacío tras el pago.");
            }

            // Calcular total real
            $total_coste = 0;
            $items_procesados = [];
            foreach ($carrito as $item) {
                $stmt = $conn->prepare("SELECT precio, stock, nombre FROM productos WHERE id = ? FOR UPDATE");
                $stmt->execute([$item['producto_id']]);
                $prod = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($prod) {
                    $total_coste += ($prod['precio'] * $item['cantidad']);
                    $items_procesados[] = [
                        'id' => $item['producto_id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $prod['precio'],
                        'nombre' => $prod['nombre']
                    ];
                }
            }
            $total_coste += 9.90; // Gastos de envío constantes

            // 2. Insertar Cabecera del Pedido
            $sqlPedido = "INSERT INTO pedidos (usuario_id, coste_total, estado, nombre_destinatario, direccion_envio, ciudad, provincia) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtPedido = $conn->prepare($sqlPedido);

            $nombre_dest = $datos_envio['nombre_destinatario'] ?? 'Anónimo';
            $direccion = $datos_envio['direccion'] ?? 'No proporcionada';
            $ciudad = $datos_envio['ciudad'] ?? '';
            $cp = $datos_envio['cp'] ?? '';
            $provincia = $cp; // Usamos CP como provincia si no hay campo específico

            $stmtPedido->execute([
                $usuario_id,
                $total_coste,
                'pagado',
                $nombre_dest,
                $direccion,
                $ciudad,
                $provincia
            ]);

            $pedido_id = $conn->lastInsertId();

            // 3. Insertar Detalles y Actualizar Stock
            $stmtDetalle = $conn->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmtStock = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

            foreach ($items_procesados as $item) {
                $stmtDetalle->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio_unitario']]);
                $stmtStock->execute([$item['cantidad'], $item['id']]);
            }

            $conn->commit();

            // --- 4. SIMULACIÓN DE ENVÍO DE EMAIL ---
            // En una app real usaríamos mail() o PHPMailer.
            // Aquí simulamos guardando el registro en un log o sesión.
            $log_pago = "[" . date('Y-m-d H:i:s') . "] Email de confirmación enviado a: " . ($datos_envio['email_contacto'] ?? 'N/A') . " para el pedido #$pedido_id\n";
            file_put_contents(__DIR__ . "/log_emails.txt", $log_pago, FILE_APPEND);

            $_SESSION['ultimo_pedido_id'] = $pedido_id;
            $_SESSION['mensaje_exito'] = "Tu pedido exclusivo ha sido procesado. Se ha enviado un email de confirmación a " . ($datos_envio['email_contacto'] ?? '');

            // 5. Limpiar sesión
            unset($_SESSION['carrito']);
            unset($_SESSION['datos_envio']);

            header("Location: ../../src/confirmacion-pago-page.php");
            exit();

        } catch (Exception $e) {
            $conn->rollBack();
            header("Location: ../../src/checkout-page.php?error=" . urlencode("Error al guardar el pedido: " . $e->getMessage()));
            exit();
        }
    } else {
        // Fallo en el pago o estado no exitoso
        $error_msg = isset($intent['last_payment_error']) ? $intent['last_payment_error']['message'] : 'Tu pago no pudo procesarse. Por favor, inténtalo de nuevo.';
        header("Location: ../../src/checkout-page.php?error=" . urlencode($error_msg));
        exit();
    }
}

// Fallback
header("Location: ../../src/index.php");
exit();
?>