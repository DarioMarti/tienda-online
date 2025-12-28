<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";


// Función para obtener todas las tallas únicas (para el filtro)
function mostrarTallas()
{
    $conn = conectar();
    $sql = "SELECT DISTINCT talla FROM producto_tallas ORDER BY talla";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Bloque de ejecución principal (SOLO si se llama directamente via AJAX)
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    header('Content-Type: application/json');

    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
        exit;
    }

    try {
        $conn = conectar();
        $id = intval($_GET['id']);

        $stmt = $conn->prepare("SELECT * FROM producto_tallas WHERE producto_id = ?");
        $stmt->execute([$id]);
        $tallas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'tallas' => $tallas]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>