<?php
session_start();
require_once "../../config/conexion.php";
header('Content-Type: application/json');

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

$items = [];
$subtotal = 0;
$conn = conectar();

if (isset($_SESSION['usuario'])) {
    $usuario_id = $_SESSION['usuario']['id'];
    $stmt = $conn->prepare("SELECT c.id as carrito_id, p.id as producto_id, p.nombre, p.precio, p.imagen, t.nombre as talla, c.cantidad 
        FROM carrito c
        JOIN productos p ON c.producto_id = p.id
        LEFT JOIN tallas t ON c.talla_id = t.id
        WHERE c.usuario_id = ?
    ");
    $stmt->execute([$usuario_id]);
    $productosCarrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productosCarrito as $productoCarrito) {
        $precioProductoTotal = $productoCarrito['precio'] * $productoCarrito['cantidad'];
        $subtotal += $precioProductoTotal;

        $items[] = [
            'key' => $productoCarrito['carrito_id'],
            'id' => $productoCarrito['producto_id'],
            'nombre' => $productoCarrito['nombre'],
            'precio' => $productoCarrito['precio'],
            'imagen' => $productoCarrito['imagen'],
            'talla' => $productoCarrito['talla'],
            'cantidad' => $productoCarrito['cantidad'],
            'total_f' => number_format($precioProductoTotal, 2, ',', '.') . ' €'
        ];
    }
} else {
    if (!empty($carrito)) {
        foreach ($carrito as $index => $itemCarrito) {
            $stmt = $conn->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id = ?");
            $stmt->execute([$itemCarrito['producto_id']]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                $precioProductoTotal = $producto['precio'] * $itemCarrito['cantidad'];
                $subtotal += $precioProductoTotal;

                $items[] = [
                    'key' => $index,
                    'id' => $producto['id'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'imagen' => $producto['imagen'],
                    'talla' => $itemCarrito['talla'],
                    'cantidad' => $itemCarrito['cantidad'],
                    'total_f' => number_format($precioProductoTotal, 2, ',', '.') . ' €'
                ];
            }
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