<?php
require_once 'conexion.php';

// Actualizar registros con más de 24 hrs a 'expirada'
$updateSql = "UPDATE bitacora_ingresos 
              SET opciones = 'expirada' 
              WHERE opciones = 'ingresada' 
              AND fecha_hora < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
$conexion->query($updateSql);

// Obtener datos de la bitácora
$selectSql = "SELECT 
                bi.id,
                bi.fecha_hora,
                bi.hora_salida,
                bi.opciones as estado,
                p.rut_completo,
                p.rut,
                p.nombre AS persona_nombre,
                p.apellido AS persona_apellido,
                p.genero,
                p.direccion,
                p.medio_transporte,
                p.patente,
                p.fecha_ingreso,
                p.hora_ingreso,
                u.nombre AS usuario_nombre,
                u.alias AS usuario_alias,
                u.rol AS usuario_rol,
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
        // Formatear fechas y horas
        $fechaHora = explode(' ', $fila['fecha_hora']);
        $fila['fecha'] = date('d/m/Y', strtotime($fechaHora[0]));
        $fila['hora_registro'] = $fechaHora[1] ?? '--:--';
        $fila['hora_ingreso'] = $fila['hora_ingreso'] ? date('H:i', strtotime($fila['hora_ingreso'])) : $fila['hora_registro'];
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
