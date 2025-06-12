<?php
require 'conexion.php';

$porton_id = isset($_GET['porton_id']) ? intval($_GET['porton_id']) : 0;

if ($porton_id === 0) {
    echo json_encode(['total' => 0]);
    exit;
}

$sql = "
    SELECT COUNT(*) AS total_dentro
    FROM (
        SELECT persona_id
        FROM bitacora_ingresos
        WHERE porton_id = ?
        GROUP BY persona_id
        HAVING 
            SUM(opciones = 'ingresada') > SUM(opciones IN ('finalizada', 'expirada'))
    ) AS dentro
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $porton_id);
$stmt->execute();
$resultado = $stmt->get_result();

$total = 0;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    $total = $fila['total_dentro'];
}

echo json_encode(['total' => $total]);
