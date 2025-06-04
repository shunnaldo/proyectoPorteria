<?php
require_once 'conexion.php';
session_start();

// Solo accesible para owners
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "owner") {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit;
}

$usuarioId = $_SESSION["usuario"]["id"];

// Obtener los portones asociados desde usuario_porton
$sql = "
    SELECT 
        p.id, 
        p.nombre, 
        p.ubicacion 
    FROM portones p
    INNER JOIN usuario_porton up ON p.id = up.porton_id
    WHERE up.usuario_id = ?
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$result = $stmt->get_result();

$portones = [];
while ($row = $result->fetch_assoc()) {
    $portones[] = $row;
}

header('Content-Type: application/json');
echo json_encode($portones);
$conexion->close();
