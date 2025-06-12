<?php
require 'conexion.php';

$porton_id = isset($_GET['porton_id']) ? intval($_GET['porton_id']) : 0;

if ($porton_id <= 0) {
    echo json_encode(['total' => 0]);
    exit;
}

$sql = "SELECT COUNT(*) AS total FROM bitacora_ingresos WHERE porton_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $porton_id);
$stmt->execute();
$resultado = $stmt->get_result();

$total = 0;
if ($fila = $resultado->fetch_assoc()) {
    $total = $fila['total'];
}

echo json_encode(['total' => $total]);
