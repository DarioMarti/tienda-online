<?php
require_once '../../config/conexion.php';
ob_start();

// Verificar permisos
restringirAccesoAPI();

try {
    $conn = conectar();

    $id = $_POST['category_id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $categoria_padre_id = !empty($_POST['categoria_padre_id']) ? $_POST['categoria_padre_id'] : null;

    if (!$id) {
        throw new Exception("ID de categoría no proporcionado.");
    }

    // COMPROBAR DUPLICADOS
    $sqlComprobar = "SELECT id FROM categorias WHERE nombre = :nombre AND id != :id";
    $stmtComprobar = $conn->prepare($sqlComprobar);
    $stmtComprobar->execute([
        ':nombre' => $nombre,
        ':id' => $id
    ]);

    if ($stmtComprobar->fetch()) {
        throw new Exception("Ya existe una categoría con el nombre '$nombre'.");
    }

    // COMPROBAR SI LA CATEGORIA ES SU PROPIO PADRE
    if ($categoria_padre_id) {
        if ($categoria_padre_id == $id) {
            throw new Exception("Una categoría no puede ser su propio padre.");
        }

        $stmtPadre = $conn->prepare("SELECT nombre FROM categorias WHERE id = :id");
        $stmtPadre->execute([':id' => $categoria_padre_id]);
        $padre = $stmtPadre->fetch(PDO::FETCH_ASSOC);

        if ($padre && strcasecmp($padre['nombre'], $nombre) === 0) {
            throw new Exception("El nombre de la categoría no puede ser igual al de su categoría padre.");
        }
    }

    $sentencia = 'UPDATE categorias
    SET nombre = :nombre, descripcion = :descripcion, categoria_padre_id = :categoria_padre_id
    WHERE id = :id';
    $stmt = $conn->prepare($sentencia);
    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':categoria_padre_id' => $categoria_padre_id,
        ':id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => "Categoría actualizada correctamente"
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