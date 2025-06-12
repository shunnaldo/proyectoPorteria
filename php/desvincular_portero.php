<?php
require 'conexion.php';


session_start();

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}


if (isset($_GET['asignacion_id']) && is_numeric($_GET['asignacion_id'])) {
    $asignacion_id = intval($_GET['asignacion_id']);

    // Consultar los detalles de la asignación antes de eliminarla
    $stmt = $conexion->prepare("SELECT usuario_id, porton_id FROM usuario_porton WHERE id = ?");
    $stmt->bind_param("i", $asignacion_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obtener los detalles de la asignación
        $row = $result->fetch_assoc();
        $usuario_id = $row['usuario_id'];
        $porton_id = $row['porton_id'];

        // Eliminar la asignación
        $stmt->close();
        $deleteStmt = $conexion->prepare("DELETE FROM usuario_porton WHERE id = ?");
        $deleteStmt->bind_param("i", $asignacion_id);

        if ($deleteStmt->execute()) {
            // Registrar el historial de eliminación
            $admin_id = $_SESSION["usuario"]["id"];  // El ID del admin que realizó la eliminación
            $accion = 'eliminación';
            $tabla_afectada = 'usuario_porton';
            $descripcion = "Se eliminó la asignación del portero con ID $usuario_id al portón con ID $porton_id.";

            // Insertar en la tabla historial_cambios
            $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                                  VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $asignacion_id, $descripcion);
            $stmt_historial->execute();

            // Redirigir con éxito
            header("Location: ../pages/mostrar_portones_con_porteros.php?exito=1");
            exit();
        } else {
            // Redirigir con error al eliminar
            header("Location: ../pages/mostrar_portones_con_porteros.php?error=sql");
            exit();
        }

        $deleteStmt->close();
    } else {
        // Si la asignación no se encuentra
        header("Location: ../pages/mostrar_portones_con_porteros.php?error=no_encontrado");
        exit();
    }

    $stmt->close();
} else {
    // Parámetro inválido
    header("Location: ../pages/mostrar_portones_con_porteros.php?error=1");
    exit();
}
?>
