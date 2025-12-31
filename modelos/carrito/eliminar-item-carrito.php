<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['key'])) {
    echo json_encode(['success' => false, 'error' => 'Índice de producto no proporcionado']);
    exit();
}

$key = $data['key'];

if (isset($_SESSION['carrito'][$key])) {
    unset($_SESSION['carrito'][$key]);

    // Opcional: Reindexar el array para evitar huecos si se usa como lista simple
    // Pero como usamos claves asociativas dinámicas del bucle original, unset es suficiente.

    // Calcular nuevo total de items
    $total_items = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total_items += $item['cantidad'];
    }

    echo json_encode([
        'success' => true,
        'total_items' => $total_items
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Producto no encontrado en la cesta']);
}
?>