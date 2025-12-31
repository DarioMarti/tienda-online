<?php

require_once dirname(__DIR__, 2) . "/config/conexion.php";

function mostrarProductos($orden = '', $tallas = [], $categoria = '', $precio = null, $soloActivos = true, $search = '', $soloRebajas = false)
{
    $conn = conectar();

    $where = [];
    $params = [];

    // FILTRADO POR ESTADO (BORRADO LÓGICO)
    if ($soloActivos) {
        $where[] = 'p.activo = 1';
    }

    // FILTRADO POR REBAJAS
    if ($soloRebajas) {
        $where[] = 'p.descuento > 0';
    }

    // FILTRAR POR VARIAS TALLAS
    if (!empty($tallas)) {
        $placeholders = implode(',', array_fill(0, count($tallas), '?'));
        // Usamos una subconsulta porque las tallas están en otra tabla
        $where[] = "p.id IN (SELECT producto_id FROM producto_tallas WHERE talla IN ($placeholders))";
        $params = array_merge($params, $tallas);
    }

    // FILTRAR POR CATEGORÍA
    if ($categoria) {
        $where[] = 'p.categoria_id = ?';
        $params[] = $categoria;
    }

    // FILTRAR POR PRECIO MÁXIMO
    if ($precio !== null && $precio !== '') {
        $where[] = '(p.precio * (1 - COALESCE(p.descuento, 0) / 100)) <= ?';
        $params[] = floatval($precio);
    }

    // FILTRAR POR BÚSQUEDA (NOMBRE O CATEGORÍA)
    if ($search) {
        $where[] = "(p.nombre LIKE ? OR c.nombre LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $whereSQL = '';
    if ($where) {
        $whereSQL = 'WHERE ' . implode(' AND ', $where);
    }

    // ORDEN
    $ordenSQL = '';
    switch ($orden) {
        case 'precio_asc':
            $ordenSQL = 'ORDER BY (p.precio * (1 - COALESCE(p.descuento, 0) / 100)) ASC';
            break;
        case 'precio_desc':
            $ordenSQL = 'ORDER BY (p.precio * (1 - COALESCE(p.descuento, 0) / 100)) DESC';
            break;
        case 'recientes':
            $ordenSQL = 'ORDER BY p.id DESC';
            break;
    }

    $sql = "SELECT p.* FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            $whereSQL $ordenSQL";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>