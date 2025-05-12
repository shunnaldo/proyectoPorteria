<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $correo = $_POST["correo_electronico"];
    $contrasena = $_POST["contrasena"];
    $rol = $_POST["rol"];

    // Hashear la contraseña
    $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);

    // Preparar la consulta
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, correo_electronico, contrasena, rol) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssss", $nombre, $apellido, $correo, $contrasena_hashed, $rol);
        if ($stmt->execute()) {
            echo "Usuario registrado exitosamente.";
        } else {
            // Error de ejecución (por ejemplo, correo duplicado)
            if ($conexion->errno == 1062) {
                echo "El correo electrónico ya está registrado.";
            } else {
                echo "Error al registrar: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conexion->error;
    }

    $conexion->close();
}
?>
