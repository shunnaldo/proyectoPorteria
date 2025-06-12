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

    // Iniciar la eliminación del usuario
    $sql = "DELETE FROM usuarios WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        // Registrar el historial de eliminación
        $usuario_id = $_SESSION["usuario"]["id"]; // El ID del admin que realiza la eliminación
        $accion = 'eliminación';
        $tabla_afectada = 'usuarios';
        $descripcion = "Se eliminó el usuario con ID $id.";

        $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt_historial->bind_param("issis", $usuario_id, $accion, $tabla_afectada, $id, $descripcion);
        $stmt_historial->execute();

        // Redirigir a la página de usuarios
        header("Location: ../pages/ver_usuarios.php?msg=Usuario eliminado correctamente");
        exit();
    } else {
        echo "Error al eliminar usuario: " . $conexion->error;
    }
} else {
    echo "ID no proporcionado.";
}
?>
