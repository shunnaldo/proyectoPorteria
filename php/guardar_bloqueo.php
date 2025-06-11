<?php
require 'conexion.php';

// Limpiar el RUT recibido
$rut = strtoupper(str_replace(['.', '-', ' '], '', trim($_POST['rut'])));
$porton_id = intval($_POST['porton_id']);
$motivo = $_POST['motivo'] ?? 'Sin especificar';
$nombre = trim($_POST['nombre'] ?? '');
$genero = $_POST['genero'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;

// Validar formato de RUT
if (!preg_match('/^[0-9]+[0-9kK]{1}$/', $rut)) {
    die("❌ Formato de RUT inválido");
}

// Verificar si ya está bloqueado
$verificar = $conexion->prepare("SELECT 1 FROM blacklist WHERE rut = ? AND porton_id = ?");
$verificar->bind_param("si", $rut, $porton_id);
$verificar->execute();
$ver_result = $verificar->get_result();

if ($ver_result->num_rows > 0) {
    die("⚠️ Esta persona ya está bloqueada en ese portón.");
}

// Insertar datos completos
$stmt = $conexion->prepare("INSERT INTO blacklist (rut, porton_id, motivo, nombre, genero, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissss", $rut, $porton_id, $motivo, $nombre, $genero, $fecha_nacimiento);


if ($stmt->execute()) {
    header("Location: ../pages/listado_bloqueados.php?success=1");
} else {
    header("Location: ../pages/listado_bloqueados.php?error=db_error");
}
exit;