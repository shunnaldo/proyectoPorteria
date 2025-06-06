<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $mensaje = $_POST['mensaje'];

    $stmt = $conexion->prepare("INSERT INTO respuestas_ticket (ticket_id, mensaje) VALUES (?, ?)");
    $stmt->bind_param("is", $ticket_id, $mensaje);

    if ($stmt->execute()) {
        header("Location: ../pages/ver_tikets.php?respuesta=enviada");
        exit;
    } else {
        echo "Error al enviar la respuesta.";
    }

    $stmt->close();
    $conexion->close();
}
?>
