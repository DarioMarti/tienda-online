<?php
header('Content-Type: application/json');
require('../../config/conexion.php');

session_start();

// Verificar permisos de admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit;
}

try {
    $conn = conectar();

    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $categoria_padre_id = !empty($_POST['categoria_padre_id']) ? $_POST['categoria_padre_id'] : null;

    if (empty($nombre)) {
        throw new Exception("El nombre es obligatorio.");
    }

    // Validar duplicados
    $sqlCheck = "SELECT id FROM categorias WHERE nombre = :nombre";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([':nombre' => $nombre]);

    if ($stmtCheck->fetch()) {
        throw new Exception("Ya existe una categoría con el nombre '$nombre'.");
    }

    // Validar conflicto con nombre de padre
    if ($categoria_padre_id) {
        $stmtParent = $conn->prepare("SELECT nombre FROM categorias WHERE id = :id");
        $stmtParent->execute([':id' => $categoria_padre_id]);
        $parent = $stmtParent->fetch(PDO::FETCH_ASSOC);

        if ($parent && strcasecmp($parent['nombre'], $nombre) === 0) {
            throw new Exception("El nombre de la categoría no puede ser igual al de su categoría padre.");
        }
    }

    $sentencia = 'INSERT INTO categorias (nombre, descripcion, categoria_padre_id) VALUES (:nombre, :descripcion, :categoria_padre_id)';
    $stmt = $conn->prepare($sentencia);
    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':categoria_padre_id' => $categoria_padre_id
    ]);

    echo json_encode([
        'success' => true,
        'message' => "Categoría creada correctamente"
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Error: " . $e->getMessage()
    ]);
    exit;
}
?>