<?php

require_once dirname(__DIR__, 2) . "/config/conexion.php";

function mostrarUsuarios()
{

    $conn = conectar();

    try {

        $consulta = "SELECT * FROM usuarios ORDER BY fecha_creacion DESC";
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