<?php
require_once 'conexion.php';
session_start();

// Mostrar lo que llega por POST (debug opcional)
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Validar que venga la data necesaria
if (!isset($_POST['rut'], $_POST['fecha_ingreso'], $_POST['hora_ingreso'])) {
    die("❌ Datos incompletos.");
}

// Procesar RUT (sin dv)
$rut_completo = preg_replace('/[^0-9kK]/', '', $_POST['rut']);
$rut = strtoupper($rut_completo); // Usamos todo como string, incluido dv si lo tiene

// Obtener datos del formulario
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$genero = $_POST['genero'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$medio_transporte = $_POST['medio_transporte'] ?? 'Otro';
$patente = ($medio_transporte === 'Auto') ? ($_POST['patente'] ?? 'N/A') : 'N/A';
$fecha = $_POST['fecha_ingreso'];
$hora = $_POST['hora_ingreso'];
$fecha_hora = "$fecha $hora";

// Datos del usuario y portón desde sesión
$usuario_id = $_SESSION["usuario"]["id"] ?? null;
$porton_id = $_SESSION["porton_id"] ?? null;

if (!$usuario_id || !$porton_id) {
    die("❌ Error: sesión expirada o datos del usuario/portón no disponibles.");
}

// Crear nueva persona SIEMPRE, sin buscar duplicados
$stmt_insert = $conexion->prepare("
    INSERT INTO personas (rut, nombre, apellido, genero, direccion, medio_transporte, patente)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt_insert->bind_param("sssssss", $rut, $nombre, $apellido, $genero, $direccion, $medio_transporte, $patente);

if (!$stmt_insert->execute()) {
    die("❌ Error al registrar nueva persona: " . $stmt_insert->error);
}

$persona_id = $stmt_insert->insert_id;
$stmt_insert->close();

// Insertar en la bitácora
$stmt = $conexion->prepare("
    INSERT INTO bitacora_ingresos (persona_id, usuario_id, porton_id, fecha_hora)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiis", $persona_id, $usuario_id, $porton_id, $fecha_hora);

if ($stmt->execute()) {
    echo "✅ Ingreso registrado correctamente.";
} else {
    echo "❌ Error al registrar ingreso: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
