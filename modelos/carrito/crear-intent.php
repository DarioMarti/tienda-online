<?php
session_start();
require_once "../../config/conexion.php";
require_once "../../config/stripe-config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo json_encode(['error' => 'El carrito está vacío']);
    exit();
}

// Calcular total
$carrito = $_SESSION['carrito'];
$subtotal = 0;
$envio = 9.90;

$conn = conectar();
foreach ($carrito as $item) {
    $stmt = $conn->prepare("SELECT precio FROM productos WHERE id = ?");
    $stmt->execute([$item['producto_id']]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($producto) {
        $subtotal += ($producto['precio'] * $item['cantidad']);
    }
}

$total_cents = round(($subtotal + $envio) * 100);

// Llamada directa a la API de Stripe vía cURL (para evitar depender de Composer)
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'amount' => $total_cents,
    'currency' => 'eur',
    'automatic_payment_methods[enabled]' => 'true',
    'description' => 'Pedido en Aetheria Store'
]));
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');

$responseRaw = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
    exit();
}
curl_close($ch);

$stripeResponse = json_decode($responseRaw, true);

if (isset($stripeResponse['error'])) {
    echo json_encode(['error' => $stripeResponse['error']['message']]);
} else {
    echo json_encode([
        'clientSecret' => $stripeResponse['client_secret']
    ]);
}
?>