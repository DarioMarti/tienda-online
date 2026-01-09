<?php
require '../vendor/autoload.php'; // Asegúrate de que esta ruta sea correcta
require_once '../config/stripe_config.php';
require_once __DIR__ . '/../config/conexion.php';

$conn = conectar();

\Stripe\Stripe::setApiKey(STRIPE_CLAVE_SECRETA);

header('Content-Type: application/json');

//OBTENER LOS PRODUCTOS DEL CARRITO
$stmtCarrito = $conn->prepare("SELECT c.id, c.cantidad, p.nombre, p.precio, p.stock, t.nombre as talla
        FROM carrito c
        JOIN productos p ON c.producto_id = p.id
        LEFT JOIN tallas t ON c.talla_id = t.id
        WHERE c.usuario_id = ?
    ");
$stmtCarrito->execute([$usuario_id]);
$productosCarrito = $stmtCarrito->fetchAll(PDO::FETCH_ASSOC);

// PREPARA LOS ITEMS PARA STRIPE
$items = [];
foreach ($productosCarrito as $productoCarrito) {
    $nombre_producto = $productoCarrito['nombre'];
    if ($productoCarrito['talla']) {
        $nombre_producto .= " - Talla {$productoCarrito['talla']}";
    }

    $items[] = [
        'price_data' => [
            'currency' => MONEDA_STRIPE,
            'product_data' => [
                'name' => $nombre_producto,
            ],
            'unit_amount' => (int) ($productoCarrito['precio'] * 100),
        ],
        'quantity' => $productoCarrito['cantidad'],
    ];
}



try {
    $YOUR_DOMAIN = DOMINIO_URL;
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $items,
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '../src/pago-exitoso.php',
        'cancel_url' => $YOUR_DOMAIN . '../src/pago-cancelado.php',
    ]);

    echo json_encode(['id' => $checkout_session->id]);
} catch (Error $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>