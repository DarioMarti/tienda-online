<?php
require_once '../../config/conexion.php';
ob_start();

// Verificar permisos
restringirAccesoAPI();

try {
    $conn = conectar();

    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $categoria_padre_id = !empty($_POST['categoria_padre_id']) ? $_POST['categoria_padre_id'] : null;

    if (empty($nombre)) {
        throw new Exception("El nombre es obligatorio.");
    }

    // COMPROBAR SI LA CATEGORIA YA EXISTE
    $sqlComprobar = "SELECT id FROM categorias WHERE nombre = :nombre";
    $stmtComprobar = $conn->prepare($sqlComprobar);
    $stmtComprobar->execute([':nombre' => $nombre]);

    if ($stmtComprobar->fetch()) {
        throw new Exception("Ya existe una categoría con el nombre '$nombre'.");
    }

    // COMPROBAR SI EL NOMBRE DE LA CATEGORIA ES IGUAL AL DE SU CATEGORIA PADRE

    if ($categoria_padre_id) {
        $stmtPadre = $conn->prepare("SELECT nombre FROM categorias WHERE id = :id");
        $stmtPadre->execute([':id' => $categoria_padre_id]);
        $padre = $stmtPadre->fetch(PDO::FETCH_ASSOC);

        if ($padre && strcasecmp($padre['nombre'], $nombre) === 0) {
            throw new Exception("El nombre de la categoría no puede ser igual al de su categoría padre.");
        }
    }

    $sentencia = 'INSERT INTO categorias (nombre, descripcion, categoria_padre_id)
    VALUES (:nombre, :descripcion, :categoria_padre_id)';
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