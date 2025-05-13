<?php
require 'conexion.php'; // Conectar a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];  // ID del portero
    $porton_id = $_POST['porton_id'];    // ID del portón

    // Verificar si la asignación ya existe
    $stmt = $conexion->prepare("SELECT * FROM usuario_porton WHERE usuario_id = ? AND porton_id = ?");
    $stmt->bind_param("ii", $usuario_id, $porton_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Si ya existe, mostrar un mensaje de error
        echo "El portero ya está asignado a este portón.";
    } else {
        // Insertar la relación en la tabla intermedia
        $stmt = $conexion->prepare("INSERT INTO usuario_porton (usuario_id, porton_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $usuario_id, $porton_id);

        if ($stmt->execute()) {
            echo "Portero asignado exitosamente al portón.";
        } else {
            echo "Error al asignar portero al portón: " . $conexion->error;
        }
    }

    $stmt->close();
    $conexion->close();
}
?>
