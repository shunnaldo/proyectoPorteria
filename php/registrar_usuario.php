<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $correo = $_POST["correo_electronico"];
    $contrasena = $_POST["contrasena"];
    $rol = $_POST["rol"];

    $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, correo_electronico, contrasena, rol) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Fallo al preparar la consulta: " . $conexion->error);
        }

        $stmt->bind_param("sssss", $nombre, $apellido, $correo, $contrasena_hashed, $rol);
        $stmt->execute();

        header("Location: ../pages/registrotrabajador.php?mensaje=success");
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            header("Location: ../pages/registrotrabajador.php?mensaje=correo_duplicado");
        } else {
            error_log("Error al registrar usuario: " . $e->getMessage());
            header("Location: ../pages/registrotrabajador.php?mensaje=error");
        }
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conexion->close();
    }
}

?>
