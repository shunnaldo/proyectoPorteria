<?php
require_once 'conexion.php';

function formatearRut($rut, $dv) {
    $rut = number_format($rut, 0, '', '.');
    return $rut . '-' . strtoupper($dv);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut_completo = $_POST['rut'];
    $rut = substr($rut_completo, 0, -1);
    $dv = substr($rut_completo, -1);

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $direccion = $_POST['direccion'];
    $medio_transporte = $_POST['medio_transporte'];
    $patente = $medio_transporte === 'Auto' ? $_POST['patente'] : null;
    $fecha = $_POST['fecha_ingreso'];
    $hora = $_POST['hora_ingreso'];

    $stmt = $conexion->prepare("INSERT INTO personas (
        rut, dv, nombre, apellido, genero, direccion, medio_transporte, patente, fecha_ingreso, hora_ingreso
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isssssssss", $rut, $dv, $nombre, $apellido, $genero, $direccion, $medio_transporte, $patente, $fecha, $hora);

    if ($stmt->execute()) {
        echo "<h2>✅ Registro guardado correctamente.</h2>";
        echo "<p><strong>RUT:</strong> " . formatearRut($rut, $dv) . "</p>";
        echo "<a href='../pages/escanearQR.php'>Escanear otro</a>";
    } else {
        echo "<h2>❌ Error al guardar: " . $stmt->error . "</h2>";
    }
} else {
    echo "Método no permitido.";
}
?>
