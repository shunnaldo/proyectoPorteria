<?php
require_once 'conexion.php';

// 1. Primero actualizamos los registros con más de 24 horas
$updateSql = "UPDATE bitacora_ingresos 
              SET opciones = 'finalizada' 
              WHERE opciones = 'ingresada' 
              AND fecha_hora < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
$conexion->query($updateSql);

// 2. Luego obtenemos los datos actualizados
$selectSql = "SELECT 
                bi.id,
                bi.fecha_hora,
                bi.opciones,
                p.rut,
                p.nombre AS persona_nombre,
                p.apellido AS persona_apellido,
                p.genero,
                p.patente,
                p.medio_transporte,
                u.nombre AS usuario_nombre,
                u.apellido AS usuario_apellido,
                port.nombre AS porton_nombre,
                port.ubicacion
            FROM bitacora_ingresos bi
            INNER JOIN personas p ON bi.persona_id = p.id
            INNER JOIN usuarios u ON bi.usuario_id = u.id
            INNER JOIN portones port ON bi.porton_id = port.id
            ORDER BY bi.fecha_hora DESC";

$resultado = $conexion->query($selectSql);
$datos = [];

if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }
}

header('Content-Type: application/json');
echo json_encode($datos);

$conexion->close();
?>