<?php
require 'conexion.php';
session_start();

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['asignacion_id'])) {
    $asignacion_id = intval($_POST['asignacion_id']);

    // Consultar para obtener detalles de la asignación antes de eliminarla
    $stmt = $conexion->prepare("SELECT usuario_id, porton_id FROM usuario_porton WHERE id = ?");
    $stmt->bind_param("i", $asignacion_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Obtener detalles de la asignación
        $stmt->bind_result($usuario_id, $porton_id);
        $stmt->fetch();

        // Eliminar la asignación
        $stmt->close();
        $stmt = $conexion->prepare("DELETE FROM usuario_porton WHERE id = ?");
        $stmt->bind_param("i", $asignacion_id);

        if ($stmt->execute()) {
            // Registrar el historial de eliminación
            $admin_id = $_SESSION["usuario"]["id"];  // El ID del admin que realizó la eliminación
            $accion = 'eliminación';
            $tabla_afectada = 'usuario_porton';
            $descripcion = "Se eliminó la asignación del usuario con ID $usuario_id al portón con ID $porton_id.";

            // Insertar en la tabla historial_cambios
            $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                                  VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $asignacion_id, $descripcion);
            $stmt_historial->execute();

            // Redirigir con éxito
            header("Location: ../pages/ownerListadoPortones.php?success=desvinculado");
        } else {
            header("Location: ../pages/ownerListadoPortones.php?error=sql");
        }

        $stmt->close();
    } else {
        // Si no se encuentra la asignación, redirigir con un error
        header("Location: ../pages/ownerListadoPortones.php?error=no_encontrado");
    }

    $conexion->close();
} else {
    header("Location: ../pages/ownerListadoPortones.php");
    exit;
}
