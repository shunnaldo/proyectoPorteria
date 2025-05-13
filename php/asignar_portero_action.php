<?php
require 'conexion.php'; // Conectar a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $porton_id = $_POST['porton_id'];

    // Verificar si ya existe
    $stmt = $conexion->prepare("SELECT id FROM usuario_porton WHERE usuario_id = ? AND porton_id = ?");
    $stmt->bind_param("ii", $usuario_id, $porton_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Ya existe: redirigir con mensaje de error
        header("Location: ../pages/asignar_portero.php?error=existente");
        exit();
    }

    $stmt->close();

    // Insertar nueva asignación
    $stmt = $conexion->prepare("INSERT INTO usuario_porton (usuario_id, porton_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $usuario_id, $porton_id);

    if ($stmt->execute()) {
        header("Location: ../pages/asignar_portero.php?success=1");
    } else {
        header("Location: ../pages/asignar_portero.php?error=sql");
    }

    $stmt->close();
    $conexion->close();
}
?>
