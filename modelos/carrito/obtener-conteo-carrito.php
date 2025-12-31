<?php
session_start();
header('Content-Type: application/json');

$total_items = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_items += $item['cantidad'];
    }
}

echo json_encode([
    'success' => true,
    'total_items' => $total_items
]);
?>