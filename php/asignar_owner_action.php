<?php
require 'conexion.php';
session_start(); // Asegúrate de iniciar la sesión para obtener el usuario admin

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $porton_id = $_POST['porton_id'];

    // Validar si ya está asignado
    $stmt = $conexion->prepare("SELECT id FROM usuario_porton WHERE usuario_id = ? AND porton_id = ?");
    $stmt->bind_param("ii", $usuario_id, $porton_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: ../pages/ownerPortones.php?error=existente");
        exit();
    }

    $stmt->close();

    // Insertar nueva asignación
    $stmt = $conexion->prepare("INSERT INTO usuario_porton (usuario_id, porton_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $usuario_id, $porton_id);

    if ($stmt->execute()) {
        // Obtener el ID de la asignación recién insertada
        $asignacion_id = $stmt->insert_id;

        // Registrar el historial de asignación
        $admin_id = $_SESSION["usuario"]["id"];  // El ID del admin que realizó la asignación
        $accion = 'asignación';
        $tabla_afectada = 'usuario_porton';
        $descripcion = "Se asignó el usuario con ID $usuario_id al portón con ID $porton_id.";

        // Insertar el historial en la tabla historial_cambios
        $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $asignacion_id, $descripcion);
        $stmt_historial->execute();

        // Redirigir con éxito
        header("Location: ../pages/ownerPortones.php?success=1");
    } else {
        header("Location: ../pages/ownerPortones.php?error=sql");
    }

    $stmt->close();
    $conexion->close();
}
?>
