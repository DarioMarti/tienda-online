<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe_config.php';
require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Debes iniciar sesi�n para realizar un pago']);
    exit;
}

try {
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $usuario_id = $_SESSION['usuario']['id'];

    $stmt = $pdo->prepare("
        SELECT c.id, c.cantidad, p.nombre, p.precio, p.stock, t.nombre as talla
        FROM carrito c
        JOIN productos p ON c.producto_id = p.id
        LEFT JOIN tallas t ON c.talla_id = t.id
        WHERE c.usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    $items_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items_carrito)) {
        http_response_code(400);
        echo json_encode(['error' => 'El carrito est� vac�o']);
        exit;
    }

    // VERIFICA SI HAY STOCK DISPONIBLE
    foreach ($items_carrito as $item) {
        if ($item['cantidad'] > $item['stock']) {
            http_response_code(400);
            echo json_encode([
                'error' => "Stock insuficiente para {$item['nombre']}. Disponible: {$item['stock']}"
            ]);
            exit;
        }
    }

    // PREPARA LOS ITEMS PARA STRIPE
    $line_items = [];
    foreach ($items_carrito as $item) {
        $nombre_producto = $item['nombre'];
        if ($item['talla']) {
            $nombre_producto .= " - Talla {$item['talla']}";
        }

        $line_items[] = [
            'price_data' => [
                'currency' => STRIPE_CURRENCY,
                'product_data' => [
                    'name' => $nombre_producto,
                ],
                'unit_amount' => (int) ($item['precio'] * 100), // Stripe usa centavos
            ],
            'quantity' => $item['cantidad'],
        ];
    }

    // CREA LA SESION DE STRIPE
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'billing_address_collection' => 'required',
        'shipping_address_collection' => [
            'allowed_countries' => ['ES'],
        ],
        'success_url' => SITE_URL . '/src/pago-exitoso.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => SITE_URL . '/src/pago-cancelado.php',
        'client_reference_id' => $usuario_id,
        'metadata' => [
            'usuario_id' => $usuario_id,
        ],
    ]);

    // DEVUELVE EL ID DE LA SESION
    echo json_encode(['id' => $checkout_session->id]);

} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al procesar el pago: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>