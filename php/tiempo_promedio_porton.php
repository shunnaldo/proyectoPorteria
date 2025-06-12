<?php
require 'conexion.php';

if (!isset($_GET['porton_id']) || !is_numeric($_GET['porton_id'])) {
    echo json_encode(['error' => 'ID de portón inválido']);
    exit;
}

$porton_id = intval($_GET['porton_id']);

$sql = "
    SELECT 
        AVG(TIMESTAMPDIFF(SECOND, fecha_hora, hora_salida)) AS promedio_segundos
    FROM 
        bitacora_ingresos
    WHERE 
        porton_id = ?
        AND (opciones = 'expirada' OR opciones = 'finalizada')
        AND hora_salida IS NOT NULL
        AND hora_salida > fecha_hora
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $porton_id);
$stmt->execute();
$resultado = $stmt->get_result();

$tiempo_promedio = 0;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    $tiempo_promedio = round($fila['promedio_segundos'] / 3600, 2); // convertir a horas
}

echo json_encode([
    'tiempo_promedio' => $tiempo_promedio
]);
