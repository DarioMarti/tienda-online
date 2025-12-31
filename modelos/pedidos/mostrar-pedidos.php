<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";

function mostrarPedidos($usuario_id = null, $incluirInactivos = false)
{
    try {
        $conn = conectar();
        $sql = "SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email 
                FROM pedidos p 
                LEFT JOIN usuarios u ON p.usuario_id = u.id";

        $conditions = [];
        $params = [];

        if ($usuario_id !== null) {
            $conditions[] = "p.usuario_id = ?";
            $params[] = $usuario_id;
        }

        if (!$incluirInactivos) {
            $conditions[] = "p.activo = 1";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY p.fecha DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener los items para cada pedido
        foreach ($pedidos as &$pedido) {
            $stmtItems = $conn->prepare("SELECT d.*, p.nombre as producto_nombre, p.imagen as producto_imagen 
                                        FROM detalles_pedido d 
                                        JOIN productos p ON d.producto_id = p.id 
                                        WHERE d.pedido_id = ?");
            $stmtItems->execute([$pedido['id']]);
            $pedido['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }

        return $pedidos;
    } catch (PDOException $e) {
        return [];
    }
}
?>