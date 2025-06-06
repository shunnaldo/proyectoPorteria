<?php
require 'conexion.php';
session_start();

// Verificar que haya sesión activa
if (!isset($_SESSION["usuario"])) {
    die("No hay sesión iniciada.");
}

// Datos del usuario desde la sesión
$usuario_id = $_SESSION["usuario"]["id"];
$correo = $_SESSION["usuario"]["correo_electronico"];

// Datos del formulario
$categoria = $_POST['categoria'] ?? '';
$motivo = $_POST['motivo'] ?? '';

// Validación básica
if (!$categoria || !$motivo) {
    die("Faltan datos obligatorios.");
}

// Preparar y ejecutar la consulta
$sql = "INSERT INTO tickets (categoria, motivo, correo, estado, fecha_creacion, usuario_id) 
        VALUES (?, ?, ?, 'en_proceso', NOW(), ?)";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$stmt->bind_param('sssi', $categoria, $motivo, $correo, $usuario_id);

if ($stmt->execute()) {
    header("Location: ../pages/tiketsUsuario.php?enviado=ok");
    exit;
} else {
    echo "❌ Error al guardar el ticket: " . $conexion->error;
}

$stmt->close();
$conexion->close();
?>
