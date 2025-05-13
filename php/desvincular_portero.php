<?php
require 'conexion.php';

if (isset($_GET['asignacion_id']) && is_numeric($_GET['asignacion_id'])) {
    $asignacion_id = intval($_GET['asignacion_id']);

    // Prepara la consulta para eliminar la asignación
    $stmt = $conexion->prepare("DELETE FROM usuario_porton WHERE id = ?");
    $stmt->bind_param("i", $asignacion_id);

    if ($stmt->execute()) {
        // Redirige de vuelta a la página de asignaciones con mensaje opcional
        header("Location: ../pages/mostrar_portones_con_porteros.php?exito=1");
        exit();
    } else {
        // Redirige con error
        header("Location: ../pages/mostrar_portones_con_porteros.php?error=1");
        exit();
    }

    $stmt->close();
} else {
    // Parámetro inválido
    header("Location: ../pages/mostrar_portones_con_porteros.php?error=1");
    exit();
}
?>
