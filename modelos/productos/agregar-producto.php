<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";
session_start();

header('Content-Type: application/json');
ob_start();

// Validar seguridad
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['admin', 'empleado'])) {
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

    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = floatval($_POST['precio'] ?? 0);
    $descuento = floatval($_POST['descuento'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;

    // Manejo de la imagen
    $imagenRuta = '';

    // Procesar nueva imagen si se ha subido
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../img/productos/';
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
            $imagenRuta = 'img/productos/' . $newFileName;
        } else {
            throw new Exception("Error al guardar la imagen.");
        }
    } else {
        throw new Exception("La imagen es obligatoria para nuevos productos.");
    }

    // Insertar nuevo producto
    $sentencia = "INSERT INTO productos (nombre, descripcion, precio, descuento, stock, imagen, categoria_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sentencia);
    $stmt->execute([$nombre, $descripcion, $precio, $descuento, $stock, $imagenRuta, $categoria_id]);
    $producto_id = $conn->lastInsertId();

    // Procesar tallas
    $tallas_stock = isset($_POST['tallas_stock']) ? json_decode($_POST['tallas_stock'], true) : [];

    if (empty($tallas_stock)) {
        throw new Exception("Debes introducir al menos una talla obligatoriamente.");
    }

    if (!empty($tallas_stock)) {
        $sqlTalla = "INSERT INTO producto_tallas (producto_id, talla, stock) VALUES (?, ?, ?)";
        $stmtTalla = $conn->prepare($sqlTalla);

        foreach ($tallas_stock as $item) {
            $talla = trim($item['talla']);
            $stockTalla = 0; // Ya no usamos stock por talla
            if (!empty($talla)) {
                $stmtTalla->execute([$producto_id, $talla, $stockTalla]);
            }
        }
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Producto creado correctamente.'
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