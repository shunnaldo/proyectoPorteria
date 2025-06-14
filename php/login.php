<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo_electronico"];
    $contrasena = $_POST["contrasena"];

    // Buscar usuario por correo
    $stmt = $conexion->prepare("SELECT id, alias, nombre, contrasena, rol FROM usuarios WHERE correo_electronico = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña
        if (password_verify($contrasena, $usuario["contrasena"])) {
            // Guardar los datos en sesión
            $_SESSION["usuario"] = [
                "id" => $usuario["id"],
                "alias" => $usuario["alias"],
                "nombre" => $usuario["nombre"],
                "rol" => $usuario["rol"],
                "correo_electronico" => $correo // este sí lo tenís arriba
            ];

            // Devolver respuesta según el rol
            if ($usuario["rol"] === "admin") {
                echo "success_admin";
                exit;
            } elseif ($usuario["rol"] === "portero") {
                echo "success_portero";
                exit;
            } elseif ($usuario["rol"] === "owner") {
                echo "success_owner";
                exit;
            } else {
                echo "Rol no reconocido. Contacta al administrador.";
                exit;
            }
        } else {
            echo "Contraseña incorrecta.";
            exit;
        }
    } else {
        echo "No se encontró un usuario con ese correo.";
        exit;
    }

    $stmt->close();
    $conexion->close();
}
