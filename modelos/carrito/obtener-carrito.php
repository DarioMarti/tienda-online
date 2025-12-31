<?php
session_start();
require_once "../../config/conexion.php";
header('Content-Type: application/json');

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$items = [];
$subtotal = 0;

if (!empty($carrito)) {
    $conn = conectar();

    foreach ($carrito as $key => $item) {
        $stmt = $conn->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id = ?");
        $stmt->execute([$item['producto_id']]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $total_item = $producto['precio'] * $item['cantidad'];
            $subtotal += $total_item;

            $items[] = [
                'key' => $key,
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen'],
                'talla' => $item['talla'],
                'cantidad' => $item['cantidad'],
                'total_f' => number_format($total_item, 2, ',', '.') . ' €'
            ];
        }
    }
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'subtotal' => $subtotal,
    'subtotal_f' => number_format($subtotal, 2, ',', '.') . ' €'
]);
?>