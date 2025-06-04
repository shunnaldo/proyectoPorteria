<?php
require 'conexion.php';
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['asignacion_id'])) {
    $asignacion_id = intval($_POST['asignacion_id']);

    // Eliminar la asignación
    $stmt = $conexion->prepare("DELETE FROM usuario_porton WHERE id = ?");
    $stmt->bind_param("i", $asignacion_id);

    if ($stmt->execute()) {
        header("Location: ../pages/ownerListadoPortones.php?success=desvinculado");
    } else {
        header("Location: ../pages/ownerListadoPortones.php?error=sql");
    }

    $stmt->close();
    $conexion->close();
} else {
    header("Location: ../pages/ownerListadoPortones.php");
    exit;
}
?>
