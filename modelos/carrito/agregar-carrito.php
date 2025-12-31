<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

$producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
$talla = isset($_POST['talla']) ? trim($_POST['talla']) : '';
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

if ($producto_id <= 0 || empty($talla)) {
    echo json_encode(['success' => false, 'message' => 'Datos de producto o talla no válidos.']);
    exit();
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Identificador único para el par producto-talla
$cart_key = $producto_id . '_' . $talla;

if (isset($_SESSION['carrito'][$cart_key])) {
    $_SESSION['carrito'][$cart_key]['cantidad'] += $cantidad;
} else {
    $_SESSION['carrito'][$cart_key] = [
        'producto_id' => $producto_id,
        'talla' => $talla,
        'cantidad' => $cantidad
    ];
}

// Calcular total de items en el carrito para la respuesta
$total_items = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_items += $item['cantidad'];
}

echo json_encode([
    'success' => true,
    'message' => 'Producto añadido a la cesta.',
    'total_items' => $total_items
]);
?>