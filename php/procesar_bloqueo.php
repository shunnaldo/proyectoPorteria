<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patente = strtoupper(trim($_POST['patente']));
    $motivo = trim($_POST['motivo']);
    $porton_id = intval($_POST['porton_id']);

    $stmt = $conexion->prepare("INSERT INTO tbl_patentes_bloqueadas (patente, motivo_bloqueo, porton_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $patente, $motivo, $porton_id);

    if ($stmt->execute()) {
        header("Location: ../pages/patentes.php?success=1");
        exit;
    } else {
        echo "Error al insertar: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
}
