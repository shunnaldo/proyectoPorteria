<?php
require 'conexion.php';
session_start(); // Asegúrate de iniciar la sesión para obtener el usuario admin

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alias = $_POST["alias"];
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo_electronico"];
    $contrasena = $_POST["contrasena"];
    $rol = $_POST["rol"];

    $rut = !empty($_POST["rut"]) ? $_POST["rut"] : null;
    $fecha_nacimiento = !empty($_POST["fecha_nacimiento"]) ? $_POST["fecha_nacimiento"] : null;

    $contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        // Insertar el nuevo usuario
        $stmt = $conexion->prepare("INSERT INTO usuarios (alias, nombre, correo_electronico, contrasena, rol, rut, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Fallo al preparar la consulta: " . $conexion->error);
        }

        $stmt->bind_param("sssssss", $alias, $nombre, $correo, $contrasena_hashed, $rol, $rut, $fecha_nacimiento);
        $stmt->execute();

        // Obtener el ID del nuevo usuario
        $nuevo_usuario_id = $stmt->insert_id;

        // Registrar el historial de creación
        // Suponemos que $_SESSION["usuario"]["id"] contiene el ID del administrador que realiza la acción
        $usuario_id = $_SESSION["usuario"]["id"];  // El ID del admin que realizó la creación
        $accion = 'creación';
        $tabla_afectada = 'usuarios';
        $descripcion = "Se creó el usuario con alias $alias y nombre $nombre.";

        // Insertar en la tabla historial_cambios
        $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt_historial->bind_param("issis", $usuario_id, $accion, $tabla_afectada, $nuevo_usuario_id, $descripcion);
        $stmt_historial->execute();

        // Redirigir al formulario con éxito
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
        if (isset($stmt_historial)) {
            $stmt_historial->close();
        }
        $conexion->close();
    }
}
?>
