<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $ubicacion = $conexion->real_escape_string($_POST['ubicacion']);
    $estado = $conexion->real_escape_string($_POST['estado']);

    $sql = "UPDATE portones SET 
                nombre = '$nombre', 
                ubicacion = '$ubicacion', 
                estado = '$estado' 
            WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        header("Location: ../pages/portones.php");
        exit();
    } else {
        echo "Error al actualizar el portón: " . $conexion->error;
    }
} else {
    echo "Acceso inválido.";
}
?>
