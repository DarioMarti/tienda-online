<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";
session_start();

header('Content-Type: application/json');
ob_start();

// Validar seguridad
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

try {
    $conn = conectar();

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;

    if (!$product_id) {
        throw new Exception("ID de producto no proporcionado.");
    }

    // Manejo de la imagen
    $imagenRuta = '';
    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->execute([$product_id]);
    $prod_actual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prod_actual) {
        throw new Exception("Producto no encontrado.");
    }

    $imagenRuta = $prod_actual['imagen'];

    // Procesar nueva imagen si se ha subido
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/productos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Extensión de archivo no permitida.");
        }

        $newFileName = md5(time() . $_FILES['imagen']['name']) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destPath)) {
            // Borrar imagen anterior si existía
            if (!empty($imagenRuta)) {
                $oldImgPath = dirname(__DIR__, 2) . '/' . $imagenRuta;
                if (file_exists($oldImgPath)) {
                    unlink($oldImgPath);
                }
            }
            $imagenRuta = 'uploads/productos/' . $newFileName;
        } else {
            throw new Exception("Error al guardar la imagen.");
        }
    }

    // Actualizar producto
    $sentencia = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?, categoria_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sentencia);
    $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagenRuta, $categoria_id, $product_id]);

    // Actualizar Tallas (Borrar y Recrear)
    $tallas_stock = isset($_POST['tallas_stock']) ? json_decode($_POST['tallas_stock'], true) : [];

    // Primero borramos las existentes para evitar duplicados o inconsistencias
    $sqlDeleteTallas = "DELETE FROM producto_tallas WHERE producto_id = ?";
    $stmtDelete = $conn->prepare($sqlDeleteTallas);
    $stmtDelete->execute([$product_id]);

    // Insertamos las nuevas
    if (!empty($tallas_stock)) {
        $sqlTalla = "INSERT INTO producto_tallas (producto_id, talla, stock) VALUES (?, ?, ?)";
        $stmtTalla = $conn->prepare($sqlTalla);

        foreach ($tallas_stock as $item) {
            $talla = trim($item['talla']);
            $stockTalla = 0;
            if (!empty($talla)) {
                $stmtTalla->execute([$product_id, $talla, $stockTalla]);
            }
        }
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Producto actualizado correctamente.'
    ]);

} catch (Exception $e) {
    if (ob_get_length())
        ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>