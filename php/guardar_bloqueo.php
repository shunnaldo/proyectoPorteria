<?php
require 'conexion.php';

// Limpiar el RUT recibido (eliminar puntos, guión y espacios)
$rut = strtoupper(str_replace(['.', '-', ' '], '', trim($_POST['rut'])));
$porton_id = intval($_POST['porton_id']);
$motivo = $_POST['motivo'] ?? 'Sin especificar';

// Validar formato de RUT (opcional pero recomendado)
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

// Insertar bloqueo con RUT limpio
$stmt = $conexion->prepare("INSERT INTO blacklist (rut, porton_id, motivo) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $rut, $porton_id, $motivo);

if ($stmt->execute()) {
    echo "✅ Bloqueo guardado correctamente.";
} else {
    echo "❌ Error al bloquear: " . $conexion->error;
}