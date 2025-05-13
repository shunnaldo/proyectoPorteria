<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM portones WHERE id = $id";
    if ($conexion->query($sql) === TRUE) {
        header("Location: ../pages/portones.php");
        exit();
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    echo "ID no proporcionado.";
}
?>
