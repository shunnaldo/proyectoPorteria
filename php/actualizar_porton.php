<?php
require_once 'conexion.php';

session_start(); // Asegúrate de iniciar la sesión para obtener el usuario admin

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $ubicacion = $conexion->real_escape_string($_POST['ubicacion']);
    $estado = $conexion->real_escape_string($_POST['estado']);

    // Consultar los valores anteriores del portón
    $stmt = $conexion->prepare("SELECT nombre, ubicacion, estado FROM portones WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_data = $result->fetch_assoc();
    $stmt->close();

    // Actualizar el portón
    $sql = "UPDATE portones SET 
                nombre = '$nombre', 
                ubicacion = '$ubicacion', 
                estado = '$estado' 
            WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        // Registrar el historial de actualización
        $admin_id = $_SESSION["usuario"]["id"]; // El ID del admin que realizó la actualización
        $accion = 'actualización';
        $tabla_afectada = 'portones';
        $descripcion = "Se actualizó el portón con ID $id. De nombre '{$old_data['nombre']}' a '$nombre', de ubicación '{$old_data['ubicacion']}' a '$ubicacion', y de estado '{$old_data['estado']}' a '$estado'.";

        // Insertar en la tabla historial_cambios
        $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $id, $descripcion);
        $stmt_historial->execute();

        // Redirigir con éxito
        header("Location: ../pages/portones.php?success=1");
        exit();
    } else {
        echo "Error al actualizar el portón: " . $conexion->error;
    }
} else {
    echo "Acceso inválido.";
}
?>
