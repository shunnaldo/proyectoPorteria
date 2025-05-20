<?php
require_once 'conexion.php';
header('Content-Type: application/json');

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener los datos del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['estado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id = $data['id'];
$estado = $data['estado'];

// Validar estado permitido
if (!in_array($estado, ['ingresada', 'finalizada'])) {
    echo json_encode(['success' => false, 'message' => 'Estado no válido']);
    exit;
}

// Establecer zona horaria de Chile
date_default_timezone_set('America/Santiago');
$hora_salida = date('Y-m-d H:i:s');

if ($estado === 'finalizada') {
    // Actualiza estado y hora_salida
    $sql = "UPDATE bitacora_ingresos SET opciones = ?, hora_salida = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('ssi', $estado, $hora_salida, $id);
} else {
    // Solo actualiza estado
    $sql = "UPDATE bitacora_ingresos SET opciones = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('si', $estado, $id);
}

// Ejecutar y responder
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar en la base de datos']);
}

$stmt->close();
$conexion->close();
?>
