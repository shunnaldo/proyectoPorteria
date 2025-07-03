<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$term = $_GET['term'] ?? '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $conexion->prepare("SELECT id, nombre FROM comunas WHERE nombre LIKE ? LIMIT 10");
$like = "%$term%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$comunas = [];
while ($row = $result->fetch_assoc()) {
    $comunas[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre']
    ];
}

echo json_encode($comunas);
