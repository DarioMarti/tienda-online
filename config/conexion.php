<?php

function conectar()
{
    define("HOST", "localhost");
    define("USER", "root");
    define("PASS", "");
    define("DB", "tienda_ropa");

    try {
        $dsn = "mysql:host=" . HOST . ";dbname=" . DB . ";charset=utf8mb4";

        $conn = new PDO($dsn, USER, PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;

    } catch (PDOException $er) {
        die("Error en la conexión: " . $er->getMessage());
    }

}

?>