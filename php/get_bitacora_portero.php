<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "portero") {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit;
}

$porteroId = $_SESSION["usuario"]["id"];

// Obtener portones asignados al portero desde usuario_porton
$sqlPortones = "SELECT porton_id FROM usuario_porton WHERE usuario_id = ?";
$stmt = $conexion->prepare($sqlPortones);
$stmt->bind_param("i", $porteroId);
$stmt->execute();
$resultPortones = $stmt->get_result();

$portonesIds = [];
while ($row = $resultPortones->fetch_assoc()) {
    $portonesIds[] = $row["porton_id"];
}

// Si no tiene portones asignados, retornar vacío
if (empty($portonesIds)) {
    echo json_encode([]);
    exit;
}

// Actualizar estados expirados solo para sus portones
$conexion->query("
    UPDATE bitacora_ingresos 
    SET opciones = 'expirada' 
    WHERE opciones = 'ingresada' 
    AND fecha_hora < DATE_SUB(NOW(), INTERVAL 24 HOUR)
    AND porton_id IN (".implode(',', $portonesIds).")
");

// Consulta principal para obtener los registros
$inPlaceholders = implode(',', array_fill(0, count($portonesIds), '?'));
$types = str_repeat('i', count($portonesIds));

$sql = "
    SELECT 
        bi.id,
        DATE(bi.fecha_hora) as fecha,
        TIME(bi.fecha_hora) as hora_registro,
        bi.hora_salida,
        bi.opciones as estado,
        p.rut,
        p.rut_completo,
        p.nombre AS persona_nombre,
        p.apellido AS persona_apellido,
        p.genero,
        p.medio_transporte,
        p.patente,
        p.hora_ingreso,
        u.nombre AS usuario_nombre,
        u.alias AS usuario_alias,
        port.nombre AS porton_nombre,
        port.ubicacion
    FROM bitacora_ingresos bi
    INNER JOIN personas p ON bi.persona_id = p.id
    LEFT JOIN usuarios u ON bi.usuario_id = u.id
    INNER JOIN portones port ON bi.porton_id = port.id
    WHERE bi.porton_id IN ($inPlaceholders)
    ORDER BY bi.fecha_hora DESC
    LIMIT 1000
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$portonesIds);
$stmt->execute();
$result = $stmt->get_result();

$datos = [];
while ($fila = $result->fetch_assoc()) {
    // Formatear datos
    $fila['fecha'] = date('d/m/Y', strtotime($fila['fecha']));
    $fila['hora_ingreso'] = !empty($fila['hora_ingreso']) ? date('H:i', strtotime($fila['hora_ingreso'])) : $fila['hora_registro'];
    $fila['hora_salida'] = !empty($fila['hora_salida']) ? date('H:i', strtotime($fila['hora_salida'])) : '--:--';
    $fila['medio_transporte'] = $fila['medio_transporte'] === 'Auto' ? 'Auto' : 'A pie';
    $fila['estado'] = ucfirst($fila['estado']);
    $datos[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($datos);
$conexion->close();