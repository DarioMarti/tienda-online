<?php

function mostrarCategorias($soloActivos = true)
{
    require_once dirname(__DIR__, 2) . "/config/conexion.php";

    try {
        $conn = conectar();

        $sentencia = 'SELECT * FROM categorias';
        if ($soloActivos) {
            $sentencia .= ' WHERE activo = 1';
        }

        $stmt = $conn->prepare($sentencia);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $categorias;
    } catch (Exception $e) {
        return $e->getMessage();
    }

}
?>