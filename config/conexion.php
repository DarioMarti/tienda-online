<?php
require_once __DIR__ . '/seguridad.php';

function conectar()
{
    if (!defined("HOST"))
        define("HOST", "localhost");
    if (!defined("USER"))
        define("USER", "root");
    if (!defined("PASS"))
        define("PASS", "");
    if (!defined("DB"))
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