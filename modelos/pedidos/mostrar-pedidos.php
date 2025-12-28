<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";

function mostrarPedidos($usuario_id = null)
{
    try {
        $conn = conectar();
        $sql = "SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email 
                FROM pedidos p 
                LEFT JOIN usuarios u ON p.usuario_id = u.id";

        if ($usuario_id !== null) {
            $sql .= " WHERE p.usuario_id = ?";
        }

        $sql .= " ORDER BY p.fecha DESC";

        $stmt = $conn->prepare($sql);

        if ($usuario_id !== null) {
            $stmt->execute([$usuario_id]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>