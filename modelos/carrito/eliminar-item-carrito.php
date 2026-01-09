<?php
session_start();
require_once "../../config/conexion.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['key'])) {
    echo json_encode(['success' => false, 'error' => 'Índice de producto no proporcionado']);
    exit();
}

$key = $data['key'];

if (isset($_SESSION['usuario'])) {
    try {
        $conn = conectar();
        $stmt = $conn->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$key, $_SESSION['usuario']['id']]);

        $exito = $stmt->rowCount() > 0;

        if ($exito) {
            unset($_SESSION['carrito'][$key]);

            // Calcular total de items en el carrito desde la base de datos
            $stmt = $conn->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
            $stmt->execute([$_SESSION['usuario']['id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_items = $result['total'] ?? 0;

            echo json_encode([
                'success' => true,
                'message' => 'Producto eliminado del carrito.',
                'total_items' => $total_items
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el producto o no existe.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
    }
} else {
    if (isset($_SESSION['carrito'][$key])) {
        unset($_SESSION['carrito'][$key]);

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
}
?>