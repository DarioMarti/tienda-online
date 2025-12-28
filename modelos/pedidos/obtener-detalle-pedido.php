<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";

/**
 * Obtiene los detalles de un pedido y sus productos asociados.
 * 
 * @param int $pedido_id El ID del pedido.
 * @return array|null Un array con 'pedido' e 'items', o null si no se encuentra.
 */
function obtenerDetallePedido($pedido_id)
{
    try {
        $conn = conectar();

        // 1. Obtener información básica del pedido
        $stmtPedido = $conn->prepare("SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email 
                                     FROM pedidos p 
                                     LEFT JOIN usuarios u ON p.usuario_id = u.id 
                                     WHERE p.id = ?");
        $stmtPedido->execute([$pedido_id]);
        $pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            return null;
        }

        // 2. Obtener los productos (items) del pedido
        $sqlItems = "SELECT d.*, p.nombre as producto_nombre, p.imagen as producto_imagen 
                     FROM detalles_pedido d
                     JOIN productos p ON d.producto_id = p.id
                     WHERE d.pedido_id = ?";

        $stmtItems = $conn->prepare($sqlItems);
        $stmtItems->execute([$pedido_id]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        return [
            'pedido' => $pedido,
            'items' => $items
        ];

    } catch (Exception $e) {
        return null;
    }
}

// MODO API: Si el archivo se accede directamente (no incluido)
// Detectamos esto comprobando si no hay funciones llamantes o si se solicita via GET/POST
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json');

    // Iniciar sesión si no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar seguridad básica (Asegurarse de que el usuario está logueado)
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Inicie sesión.']);
        exit;
    }

    $pedido_id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$pedido_id) {
        echo json_encode(['success' => false, 'message' => 'ID de pedido no proporcionado.']);
        exit();
    }

    $resultado = obtenerDetallePedido($pedido_id);

    if (!$resultado) {
        echo json_encode(['success' => false, 'message' => 'Pedido no encontrado.']);
        exit();
    }

    // Si no es admin, verificar que el pedido le pertenece
    if ($_SESSION['usuario']['rol'] !== 'admin' && $resultado['pedido']['usuario_id'] != $_SESSION['usuario']['id']) {
        echo json_encode(['success' => false, 'message' => 'No tiene permiso para ver este pedido.']);
        exit();
    }

    echo json_encode([
        'success' => true,
        'pedido' => $resultado['pedido'],
        'items' => $resultado['items']
    ]);
}
?>