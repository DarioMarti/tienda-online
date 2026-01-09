<?php
session_start();
require_once "../../config/conexion.php";
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

// CONECTAR Y BUSCAR EL ID DE LA VARIANTE DEL PRODUCTO
try {
    $conn = conectar();

    // BUSCAR VARIANTE DEL PRODUCTO
    $stmtVariante = $conn->prepare("SELECT pt.id, t.id as talla_id 
        FROM producto_tallas pt 
        JOIN tallas t ON pt.talla_id = t.id 
        WHERE pt.producto_id = ? AND t.nombre = ?
    ");
    $stmtVariante->execute([$producto_id, $talla]);
    $productoVariante = $stmtVariante->fetch(PDO::FETCH_ASSOC);

    if (!$productoVariante) {
        echo json_encode(['success' => false, 'message' => 'La combinación de producto/talla no existe.']);
        exit();
    }

    $variante_id = $productoVariante['id'];
    $talla_id = $productoVariante['talla_id'];

    // INICIALIZAR EL CARRITO EN SESIÓN SI NO EXISTE
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // 3. ACTUALIZAR SESIÓN (Usamos el ID de la variante como clave única)
    if (isset($_SESSION['carrito'][$variante_id])) {
        $_SESSION['carrito'][$variante_id]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito'][$variante_id] = [
            'producto_id' => $producto_id,
            'variante_id' => $variante_id,
            'talla' => $talla,
            'talla_id' => $talla_id,
            'cantidad' => $cantidad
        ];
    }

    // 4. PERSISTENCIA EN BASE DE DATOS SI EL USUARIO ESTÁ LOGUEADO
    if (isset($_SESSION['usuario'])) {
        $usuario_id = $_SESSION['usuario']['id'];

        $stmtProductoCarrito = $conn->prepare("SELECT id FROM carrito WHERE usuario_id = ?
        AND producto_id = ? AND (talla_id = ? OR (talla_id IS NULL AND ? IS NULL))");
        $stmtProductoCarrito->execute([$usuario_id, $producto_id, $talla_id, $talla_id]);
        $productoCarrito = $stmtProductoCarrito->fetch(PDO::FETCH_ASSOC);

        if ($productoCarrito) {
            $actualizarCantidad = $conn->prepare("UPDATE carrito SET cantidad = cantidad + ? WHERE id = ?");
            $actualizarCantidad->execute([$cantidad, $productoCarrito['id']]);
        } else {
            $insertarProducto = $conn->prepare("
                INSERT INTO carrito (usuario_id, producto_id, talla_id, cantidad) 
                VALUES (?, ?, ?, ?)
            ");
            $insertarProducto->execute([$usuario_id, $producto_id, $talla_id, $cantidad]);
        }
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de servidor: ' . $e->getMessage()]);
    exit();
}

// CALCULAR TOTAL DE ITEMS EN EL CARRITO PARA LA RESPUESTA

$totalItems = 0;
if (isset($_SESSION['usuario'])) {
    // Para usuarios autenticados, contar desde la base de datos
    $stmt = $conn->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario']['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalItems = $result['total'] ?? 0;
} else {
    // Para usuarios no autenticados, contar desde la sesión
    foreach ($_SESSION['carrito'] as $item) {
        $totalItems += $item['cantidad'];
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Producto añadido a la cesta.',
    'total_items' => $totalItems
]);
?>