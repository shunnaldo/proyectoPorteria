<?php
require_once 'conexion.php';
session_start();

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if (isset($_GET['id'])) {  // Aquí faltaba el paréntesis de cierre
    $id = intval($_GET['id']);
    
    // Verificar que el bloqueo existe
    $stmt = $conexion->prepare("SELECT id, rut, porton_id FROM blacklist WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Obtener los detalles del bloqueo antes de eliminarlo
        $row = $result->fetch_assoc();
        $rut = $row['rut'];
        $porton_id = $row['porton_id'];
        
        // Eliminar el bloqueo
        $deleteStmt = $conexion->prepare("DELETE FROM blacklist WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        
        if ($deleteStmt->execute()) {
            // Registrar el historial de eliminación
            $admin_id = $_SESSION["usuario"]["id"];  // El ID del admin que realizó la eliminación
            $accion = 'eliminación';
            $tabla_afectada = 'blacklist';
            $descripcion = "Se eliminó el bloqueo de la persona con RUT $rut en el portón con ID $porton_id.";

            // Insertar en la tabla historial_cambios
            $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                                  VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $id, $descripcion);
            $stmt_historial->execute();

            header("Location: ../pages/listado_bloqueados.php?success=1");
        } else {
            header("Location: ../pages/listado_bloqueados.php?error=1");
        }
    } else {
        header("Location: ../pages/listado_bloqueados.php?error=2");
    }
} else {
    header("Location: ../pages/listado_bloqueados.php");
}
exit;
?>
