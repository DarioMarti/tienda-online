<?php
session_start();
require("../../config/conexion.php");

// Mostrar TODOS los datos recibidos
echo "<h2>TEST - Datos recibidos:</h2>";
echo "<pre>";
echo "POST:\n";
print_r($_POST);
echo "\n\nSESSION:\n";
print_r($_SESSION);
echo "</pre>";

// Intentar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = conectar();

        $nombre = trim($_POST["nombre"] ?? "");
        $apellidos = trim($_POST["apellidos"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $direccion = trim($_POST["direccion"] ?? "");
        $email = $_SESSION["usuario"]["email"] ?? "";

        echo "<h3>Datos a actualizar:</h3>";
        echo "<pre>";
        echo "Nombre: $nombre\n";
        echo "Apellidos: $apellidos\n";
        echo "Teléfono: $telefono\n";
        echo "Dirección: $direccion\n";
        echo "Email (de sesión): $email\n";
        echo "</pre>";

        if (empty($email)) {
            echo "<p style='color:red;'>ERROR: No hay email en la sesión</p>";
            exit;
        }

        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, telefono = :telefono, direccion = :direccion WHERE email = :email";

        echo "<h3>SQL:</h3>";
        echo "<pre>$sql</pre>";

        $statement = $conn->prepare($sql);
        $result = $statement->execute([
            ":nombre" => $nombre,
            ":apellidos" => $apellidos,
            ":telefono" => $telefono,
            ":direccion" => $direccion,
            ":email" => $email
        ]);

        $rowsAffected = $statement->rowCount();

        echo "<h3>Resultado:</h3>";
        echo "<pre>";
        echo "Execute result: " . ($result ? 'true' : 'false') . "\n";
        echo "Rows affected: $rowsAffected\n";
        echo "</pre>";

        if ($rowsAffected > 0) {
            echo "<p style='color:green;'>✅ ACTUALIZACIÓN EXITOSA</p>";

            // Actualizar sesión
            $_SESSION["usuario"]["nombre"] = $nombre;
            $_SESSION["usuario"]["apellidos"] = $apellidos;
            $_SESSION["usuario"]["telefono"] = $telefono;
            $_SESSION["usuario"]["direccion"] = $direccion;

            echo "<h3>Sesión actualizada:</h3>";
            echo "<pre>";
            print_r($_SESSION["usuario"]);
            echo "</pre>";
        } else {
            echo "<p style='color:orange;'>⚠️ No se actualizó ninguna fila (puede que los datos sean iguales)</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color:red;'>ERROR: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}
?>