<?php

require_once dirname(__DIR__, 2) . "/config/conexion.php";

function mostrarUsuarios($soloActivos = true)
{

    $conn = conectar();

    try {
        $where = "";
        if ($soloActivos) {
            $where = " WHERE activo = 1";
        }

        $consulta = "SELECT * FROM usuarios $where ORDER BY fecha_creacion DESC";
        $stmt = $conn->prepare($consulta);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $usuarios;

    } catch (PDOException $e) {
        $usuarios = [];
        $error = "Error al cargar usuarios: " . $e->getMessage();
    }


}



?>