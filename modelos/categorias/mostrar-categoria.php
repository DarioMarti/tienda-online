<?php

function mostrarCategorias()
{

    require_once dirname(__DIR__, 2) . "/config/conexion.php";

    try {
        $conn = conectar();

        $sentencia = 'SELECT * FROM categorias';

        $stmt = $conn->prepare($sentencia);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $categorias;
    } catch (Exception $e) {
        return $e->getMessage();
    }

}
?>