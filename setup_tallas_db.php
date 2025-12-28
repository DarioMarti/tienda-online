<?php
require_once "config/conexion.php";

try {
    $conn = conectar();

    $sql = "CREATE TABLE IF NOT EXISTS producto_tallas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        producto_id INT NOT NULL,
        talla VARCHAR(50) NOT NULL,
        stock INT DEFAULT 0,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
    )";

    $conn->exec($sql);
    echo "Tabla 'producto_tallas' creada correctamente (o ya existía).";

} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage();
}
?>