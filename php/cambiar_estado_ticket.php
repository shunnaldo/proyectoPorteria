<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? null;

    if ($id && $nuevo_estado) {
        $stmt = $conexion->prepare("UPDATE tickets SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);

        if ($stmt->execute()) {
            header("Location: ../pages/ver_tikets.php?estado=cambiado");
            exit;
        } else {
            echo "❌ Error al cambiar el estado del ticket.";
        }

        $stmt->close();
    } else {
        echo "❌ Faltan datos para cambiar el estado.";
    }
}
?>
