<?php
require 'conexion.php';
session_start();

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

// Limpiar el RUT recibido
$rut = strtoupper(str_replace(['.', '-', ' '], '', trim($_POST['rut'])));  // Limpiar el RUT
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

// Insertar datos completos en blacklist
$stmt = $conexion->prepare("INSERT INTO blacklist (rut, porton_id, motivo, nombre, genero, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissss", $rut, $porton_id, $motivo, $nombre, $genero, $fecha_nacimiento);

if ($stmt->execute()) {
    // Obtener el ID del admin que realizó la acción
    $admin_id = $_SESSION["usuario"]["id"]; // El ID del admin que realizó el bloqueo
    $accion = 'bloqueo';
    $tabla_afectada = 'blacklist';
    $descripcion = "Se bloqueó a la persona con RUT $rut en el portón con ID $porton_id. Motivo: $motivo.";

    // Obtener el ID del último registro insertado en la tabla blacklist
    $inserted_id = $conexion->insert_id;

    // Insertar en la tabla historial_cambios
    $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                          VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $inserted_id, $descripcion);
    $stmt_historial->execute();

    header("Location: ../pages/listado_bloqueados.php?success=1");
} else {
    header("Location: ../pages/listado_bloqueados.php?error=db_error");
}

$stmt->close();
$conexion->close();
exit;
?>
