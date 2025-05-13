<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM usuarios WHERE id = $id";

    if ($conexion->query($sql) === TRUE) {
        header("Location: ../pages/ver_usuarios.php");
        exit();
    } else {
        echo "Error al eliminar usuario: " . $conexion->error;
    }
} else {
    echo "ID no proporcionado.";
}
?>
