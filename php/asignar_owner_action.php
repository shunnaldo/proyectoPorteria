<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $porton_id = $_POST['porton_id'];

    // Validar si ya está asignado
    $stmt = $conexion->prepare("SELECT id FROM usuario_porton WHERE usuario_id = ? AND porton_id = ?");
    $stmt->bind_param("ii", $usuario_id, $porton_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: ../pages/ownerPortones.php?error=existente");
        exit();
    }

    $stmt->close();

    // Insertar nueva asignación
    $stmt = $conexion->prepare("INSERT INTO usuario_porton (usuario_id, porton_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $usuario_id, $porton_id);

    if ($stmt->execute()) {
        header("Location: ../pages/ownerPortones.php?success=1");
    } else {
        header("Location: ../pages/ownerPortones.php?error=sql");
    }

    $stmt->close();
    $conexion->close();
}
?>
