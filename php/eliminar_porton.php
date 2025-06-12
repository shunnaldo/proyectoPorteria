<?php
require_once 'conexion.php';

session_start(); // Asegúrate de iniciar la sesión para obtener el usuario admin

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Consultar los detalles del portón antes de eliminarlo
    $stmt = $conexion->prepare("SELECT nombre, ubicacion, estado FROM portones WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $ubicacion = $row['ubicacion'];
        $estado = $row['estado'];

        // Eliminar el portón
        $stmt->close();
        $deleteStmt = $conexion->prepare("DELETE FROM portones WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        
        if ($deleteStmt->execute()) {
            // Registrar el historial de eliminación
            $admin_id = $_SESSION["usuario"]["id"];  // El ID del admin que realizó la eliminación
            $accion = 'eliminación';
            $tabla_afectada = 'portones';
            $descripcion = "Se eliminó el portón con nombre '$nombre', ubicación '$ubicacion', y estado '$estado'.";

            // Insertar en la tabla historial_cambios
            $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                                  VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $id, $descripcion);
            $stmt_historial->execute();

            // Redirigir con éxito
            header("Location: ../pages/portones.php?success=1");
            exit();
        } else {
            echo "Error al eliminar el portón: " . $conexion->error;
        }
    } else {
        echo "Portón no encontrado.";
    }
} else {
    echo "ID no proporcionado.";
}
?>
