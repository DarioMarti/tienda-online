<?php
require_once dirname(__DIR__, 2) . "/config/conexion.php";

function obtenerIngresosMensuales()
{
    $conn = conectar();
    // Agrupar por Mes-Año
    // Formato fecha: 2024-01
    $sql = "SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, SUM(coste_total) as total 
            FROM pedidos 
            WHERE estado != 'cancelado' 
            GROUP BY mes 
            ORDER BY mes DESC 
            LIMIT 12";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosMasVendidos()
{
    $conn = conectar();
    // Sumar cantidad total vendida por producto
    $sql = "SELECT p.nombre, p.imagen, SUM(dp.cantidad) as total_vendido 
            FROM detalles_pedido dp
            JOIN pedidos ped ON dp.pedido_id = ped.id
            JOIN productos p ON dp.producto_id = p.id
            WHERE ped.estado != 'cancelado'
            GROUP BY dp.producto_id 
            ORDER BY total_vendido DESC 
            LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>