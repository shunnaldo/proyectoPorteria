<?php
// Incluir el autoload de Composer
require '../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta
require 'conexion.php'; // Ajusta la ruta a tu archivo de conexión

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Obtener los datos del formulario
$rut = $_POST['rut'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$genero = $_POST['genero'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$direccion = $_POST['direccion'];
$medio_transporte = $_POST['medio_transporte'];

// Verificar si se ha enviado patente
$patente = null; // Inicializamos patente en NULL por defecto

if ($medio_transporte !== 'Pie' && isset($_POST['patente']) && !empty($_POST['patente'])) {
    $patente = $_POST['patente']; // Solo asignamos patente si el medio no es "Pie"
}

// Insertar los datos en la tabla personas
$sql = "INSERT INTO personas (rut, nombre, apellido, genero, fecha_nacimiento, direccion, medio_transporte, patente) 
        VALUES ('$rut', '$nombre', '$apellido', '$genero', '$fecha_nacimiento', '$direccion', '$medio_transporte', " . 
        ($patente !== null ? "'$patente'" : "NULL") . ")";  // Si patente no es null, la inserta, de lo contrario pone NULL

if ($conexion->query($sql) === TRUE) {
    // Obtener el id de la persona recién insertada
    $persona_id = $conexion->insert_id;

    // Generar el código QR con la información de la persona
    $qrCodeData = "Nombre: $nombre $apellido\nRUT: $rut\nFecha de Nacimiento: $fecha_nacimiento\nDirección: $direccion";
    $qrCode = new QrCode($qrCodeData);

    // Usar PngWriter para generar el código QR en formato PNG
    $writer = new PngWriter();

    // Especificar las opciones de compresión (si lo necesitas)
    $options = [
        PngWriter::WRITER_OPTION_COMPRESSION_LEVEL => -1, // Nivel de compresión
        PngWriter::WRITER_OPTION_NUMBER_OF_COLORS => 16 // Usar 16 colores si no hay logo
    ];

    // Llamar al método write() para obtener el resultado
    $result = $writer->write($qrCode, null, null, $options); // No se usa logo ni label en este caso

    // Obtener los datos binarios de la imagen generada
    $imageData = $result->getString(); // Cambié getData() a getString()

    // Guardar el código QR como un archivo PNG
    $qr_file = 'qr_codes/qr_persona_' . $persona_id . '.png'; // Carpeta donde se almacenan los QR

    // Guardar los datos en el archivo
    file_put_contents($qr_file, $imageData); // Guarda el archivo PNG con los datos

    // Insertar el código QR en la tabla qr_temporal
    $qr_code_path = $qr_file; // Guardar la ruta del archivo
    $fecha_generacion = date('Y-m-d H:i:s'); // Fecha y hora de generación del QR

    $sql_qr = "INSERT INTO qr_temporal (persona_id, qr_code, fecha_generacion) 
               VALUES ('$persona_id', '$qr_code_path', '$fecha_generacion')";

    if ($conexion->query($sql_qr) === TRUE) {
        // Redirigir a personas_qr.php después de la creación y generación del QR
        header("Location: ../pages/personas_qr.php");
        exit; // Asegúrate de detener la ejecución después de la redirección
    } else {
        echo "Error al guardar el código QR: " . $conexion->error;
    }
} else {
    echo "Error al guardar la persona: " . $conexion->error;
}

$conexion->close();
?>
