<?php
require_once 'conexion.php';

$sql = "SELECT * FROM portones";
$resultado = $conexion->query($sql);

$portones = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $portones[] = $fila;
    }
}

header('Content-Type: application/json');
echo json_encode($portones);
?>
