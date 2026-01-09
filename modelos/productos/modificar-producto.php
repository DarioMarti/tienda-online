<?php
ob_start();
require_once dirname(__DIR__, 2) . "/config/conexion.php";
session_start();

header('Content-Type: application/json');

// COMPROBAR SI SE TIENE ACCESO
restringirAccesoAPI();

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
    $descuento = floatval($_POST['descuento'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
    $imagenRuta = '';
    if (!$product_id) {
        throw new Exception("ID de producto no proporcionado.");
    }

    //OBTENER IMAGEN ACTUAL
    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->execute([$product_id]);
    $prod_actual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prod_actual) {
        throw new Exception("Producto no encontrado.");
    }

    $imagenRuta = $prod_actual['imagen'];

    // ACTUALIZAR IMAGEN
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorioDestino = '../../img/productos/';
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        $extensionFichero = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extensionFichero, $extensionesPermitidas)) {
            throw new Exception("Extensión de archivo no permitida.");
        }

        $nuevoNombreFichero = md5(time() . $_FILES['imagen']['name']) . '.' . $extensionFichero;
        $rutaDestino = $directorioDestino . $nuevoNombreFichero;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            // BORRAR IMAGEN ANTERIOR SI EXISTÍA
            if (!empty($imagenRuta)) {
                $rutaImagenAnterior = dirname(__DIR__, 2) . '/' . $imagenRuta;
                if (file_exists($rutaImagenAnterior)) {
                    unlink($rutaImagenAnterior);
                }
            }
            $imagenRuta = 'img/productos/' . $nuevoNombreFichero;
        } else {
            throw new Exception("Error al guardar la imagen.");
        }
    }

    // ACTUALIZAR EL PRODUCTO
    $sentencia = "UPDATE productos
    SET nombre = ?, descripcion = ?, precio = ?, descuento = ?, stock = ?, imagen = ?, categoria_id = ?
    WHERE id = ?";
    $stmt = $conn->prepare($sentencia);
    $stmt->execute([$nombre, $descripcion, $precio, $descuento, $stock, $imagenRuta, $categoria_id, $product_id]);

    // ACTUALIZAR LAS TALLAS
    $tallas_stock = isset($_POST['tallas_stock']) ? json_decode($_POST['tallas_stock'], true) : [];

    if (empty($tallas_stock)) {
        throw new Exception("Debes introducir al menos una talla obligatoriamente.");
    }

    // BORRAMOS TALLAS EXISTENTES PARA EVITAR DUPLICADOS Y ERRORES

    $stmtDelete = $conn->prepare("DELETE FROM producto_tallas WHERE producto_id = ?");
    $stmtDelete->execute([$product_id]);

    // INSERTAMOS LAS NUEVAS
    if (!empty($tallas_stock)) {
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

            $stmtTalla->execute([$product_id, $talla_id, 0]);
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