<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

$producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
$variante_id = isset($_POST['variante_id']) ? intval($_POST['variante_id']) : 0;
$talla = isset($_POST['talla']) ? trim($_POST['talla']) : '';
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

if ($producto_id <= 0 || $variante_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos de producto o variante no válidos.']);
    exit();
}

// INICILIZAR EL CARRITO SI NO EXISTE
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// USAMOS EL ID DE LA VARIANTE COMO CLAVE ÚNICA (Reemplaza a $carritoProducto)
if (isset($_SESSION['carrito'][$variante_id])) {
    $_SESSION['carrito'][$variante_id]['cantidad'] += $cantidad;
} else {
    $_SESSION['carrito'][$variante_id] = [
        'producto_id' => $producto_id,
        'variante_id' => $variante_id,
        'talla' => $talla,
        'cantidad' => $cantidad
    ];
}

// CALCULAR TOTAL DE ITEMS EN EL CARRITO PARA LA RESPUESTA

$totalItems = 0;
foreach ($_SESSION['carrito'] as $item) {
    $totalItems += $item['cantidad'];
}

echo json_encode([
    'success' => true,
    'message' => 'Producto añadido a la cesta.',
    'total_items' => $totalItems
]);
?>