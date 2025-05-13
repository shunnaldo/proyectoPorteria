<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $estado = $_POST['estado'] ?? 'cerrado';

    if (empty($nombre) || empty($ubicacion)) {
        die("Nombre y ubicación son obligatorios.");
    }

    $sql = "INSERT INTO portones (nombre, ubicacion, estado) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $nombre, $ubicacion, $estado);

    if ($stmt->execute()) {
        header("Location: ../pages/crear_porton.php?success=1");
        exit;
    } else {
        die("Error al insertar el portón: " . $conexion->error);
    }
}
?>
