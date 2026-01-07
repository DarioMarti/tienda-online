<?php
session_start();
require_once "../../config/conexion.php";
header('Content-Type: application/json');

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

$items = [];
$subtotal = 0;

if (!empty($carrito)) {
    $conn = conectar();

    foreach ($carrito as $index => $item) {
        $stmt = $conn->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id = ?");
        $stmt->execute([$item['producto_id']]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        $precioProductoTotal = $producto['precio'] * $item['cantidad'];
        $subtotal += $precioProductoTotal;

        $items[] = [
            'key' => $index, // index es ahora el variante_id
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'imagen' => $producto['imagen'],
            'talla' => $item['talla'],
            'cantidad' => $item['cantidad'],
            'total_f' => number_format($precioProductoTotal, 2, ',', '.') . ' €'
        ];
    }
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'subtotal' => $subtotal,
    'subtotal_f' => number_format($subtotal, 2, ',', '.') . ' €'
]);
?>