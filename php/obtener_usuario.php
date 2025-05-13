<?php
require_once 'conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID no proporcionado.");
}

$sql = "SELECT id, nombre, correo_electronico, rol FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}
?>
