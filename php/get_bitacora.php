<?php
require_once 'conexion.php';

// Marcar como 'expirada' las entradas con más de 24 horas
$updateSql = "UPDATE bitacora_ingresos 
              SET opciones = 'expirada' 
              WHERE opciones = 'ingresada' 
              AND fecha_hora < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
$conexion->query($updateSql);

// Traer datos de la bitácora
$selectSql = "SELECT 
                bi.id,
                bi.fecha_hora,
                bi.hora_salida,
                bi.opciones AS estado,
                bi.nombre_portero,
                p.rut_completo,
                p.rut,
                p.nombre AS persona_nombre,
                p.apellido AS persona_apellido,
                p.genero,
                p.direccion,
                p.medio_transporte,
                p.patente,
                port.nombre AS porton_nombre,
                port.ubicacion,
                u.alias
            FROM bitacora_ingresos bi
            INNER JOIN personas p ON bi.persona_id = p.id
            INNER JOIN portones port ON bi.porton_id = port.id
            INNER JOIN usuarios u ON bi.usuario_id = u.id
            ORDER BY bi.fecha_hora DESC";

$resultado = $conexion->query($selectSql);
$datos = [];

if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $fechaHora = explode(' ', $fila['fecha_hora']);
        $fila['fecha'] = date('d/m/Y', strtotime($fechaHora[0]));
        $fila['hora_registro'] = $fechaHora[1] ?? '--:--';
        $fila['hora_ingreso'] = $fila['hora_registro'];
        $fila['hora_salida'] = $fila['hora_salida'] ? date('H:i', strtotime($fila['hora_salida'])) : '--:--';
        $fila['medio_transporte'] = $fila['medio_transporte'] === 'Auto' ? 'Auto' : 'A pie';
        $fila['estado'] = ucfirst($fila['estado']);
        $datos[] = $fila;
    }
}

header('Content-Type: application/json');
echo json_encode($datos);

$conexion->close();
?>
