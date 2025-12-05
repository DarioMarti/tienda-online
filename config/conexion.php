<?php

define("HOST", "localhost");
define("USER", "root");
define("PASS", "");
define("DB", "tienda_online");

try {
    $dsn = "mysql:host=" . HOST . ";dbname=" . DB . ";charset=utf8mb4";

    $conn = new PDO($dsn, USER, PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $er) {
    die("Error en la conexión: " . $er->getMessage());
}

?>