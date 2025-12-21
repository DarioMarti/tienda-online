<?php
require('../../config/conexion.php');

try {
    $conn = conectar();

    $id = $_POST['category_id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $categoria_padre_id = !empty($_POST['categoria_padre_id']) ? $_POST['categoria_padre_id'] : null;

    if (!$id) {
        throw new Exception("ID de categoría no proporcionado.");
    }

    // Validar duplicados (excluyendo la propia categoría)
    $sqlCheck = "SELECT id FROM categorias WHERE nombre = :nombre AND id != :id";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([
        ':nombre' => $nombre,
        ':id' => $id
    ]);

    if ($stmtCheck->fetch()) {
        throw new Exception("Ya existe una categoría con el nombre '$nombre'.");
    }

    // Validar conflicto con nombre de padre
    if ($categoria_padre_id) {
        // Evitar ser padre de uno mismo (ciclado simple)
        if ($categoria_padre_id == $id) {
            throw new Exception("Una categoría no puede ser su propio padre.");
        }

        $stmtParent = $conn->prepare("SELECT nombre FROM categorias WHERE id = :id");
        $stmtParent->execute([':id' => $categoria_padre_id]);
        $parent = $stmtParent->fetch(PDO::FETCH_ASSOC);

        if ($parent && strcasecmp($parent['nombre'], $nombre) === 0) {
            throw new Exception("El nombre de la categoría no puede ser igual al de su categoría padre.");
        }
    }

    $sentencia = 'UPDATE categorias SET nombre = :nombre, descripcion = :descripcion, categoria_padre_id = :categoria_padre_id WHERE id = :id';
    $stmt = $conn->prepare($sentencia);
    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':categoria_padre_id' => $categoria_padre_id,
        ':id' => $id
    ]);

    $msg = "Categoría actualizada correctamente";
    header("Location: ../../src/admin-page.php?status=success&message=" . urlencode($msg) . "&tab=categorias");
    exit;

} catch (Exception $e) {
    header("Location: ../../src/admin-page.php?status=error&message=" . urlencode("Error: " . $e->getMessage()) . "&tab=categorias");
    exit;
}
?>