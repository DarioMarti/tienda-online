<?php
ob_start();
require_once dirname(__DIR__, 2) . "/config/conexion.php";
session_start();

header('Content-Type: application/json');

restringirAccesoAPI();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

try {
    $conn = conectar();
    $conn->beginTransaction();

    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = floatval($_POST['precio'] ?? 0);
    $descuento = floatval($_POST['descuento'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
    $imagenRuta = '';

    // VALIDAR IMAGEN SUBIDA, SE MEJORA LA SEGURIDAD Y SE AÑADE A LA RUTA DEFINITIVA
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioSubida = '../../img/productos/';
        if (!is_dir($directorioSubida)) {
            mkdir($directorioSubida, 0755, true);
        }

        $extensionFichero = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extensionFichero, $extensionesPermitidas)) {
            throw new Exception("Extensión de archivo no permitida.");
        }

        $nombreFichero = md5(time() . $_FILES['imagen']['name']) . '.' . $extensionFichero;
        $rutaCompleta = $directorioSubida . $nombreFichero;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
            $imagenRuta = 'img/productos/' . $nombreFichero;
        } else {
            throw new Exception("Error al guardar la imagen.");
        }
    } else {
        throw new Exception("La imagen es obligatoria para nuevos productos.");
    }

    // INSERTAR EL PRODUCTO
    $sentencia = "INSERT INTO productos (nombre, descripcion, precio, descuento, stock, imagen, categoria_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sentencia);
    $stmt->execute([$nombre, $descripcion, $precio, $descuento, $stock, $imagenRuta, $categoria_id]);
    $producto_id = $conn->lastInsertId();

    // Procesar tallas
    $tallas_stock = isset($_POST['tallas_stock']) ? json_decode($_POST['tallas_stock'], true) : [];

    if (empty($tallas_stock)) {
        throw new Exception("Debes introducir al menos una talla obligatoriamente.");
    }

    $stmtCheckTalla = $conn->prepare("SELECT id FROM tallas WHERE nombre = ?");
    $stmtInsertTalla = $conn->prepare("INSERT INTO tallas (nombre) VALUES (?)");
    $stmtTalla = $conn->prepare("INSERT INTO producto_tallas (producto_id, talla_id, stock) VALUES (?, ?, ?)");

    foreach ($tallas_stock as $item) {
        $nombreTalla = trim($item['talla']);
        if (empty($nombreTalla))
            continue;

        $stmtCheckTalla->execute([$nombreTalla]);
        $tallaSeleccionada = $stmtCheckTalla->fetch(PDO::FETCH_ASSOC);

        if ($tallaSeleccionada) {
            $talla_id = $tallaSeleccionada['id'];
        } else {
            $stmtInsertTalla->execute([$nombreTalla]);
            $talla_id = $conn->lastInsertId();
        }

        $stmtTalla->execute([$producto_id, $talla_id, 0]);
    }

    $conn->commit();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Producto creado correctamente.'
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    if (ob_get_length())
        ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}