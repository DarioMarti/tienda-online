<?php
// Script temporal para verificar la estructura de la tabla usuarios

require("../../config/conexion.php");

$conn = conectar();

try {
    $stmt = $conn->query("DESCRIBE usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Estructura de la tabla 'usuarios':</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($column['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>