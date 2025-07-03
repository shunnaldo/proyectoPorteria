<?php
require_once 'conexion.php';

function mostrarMensaje($tipo, $mensaje, $mostrarBoton = false) {
    $iconos = [
        'error' => 'times-circle',
        'warning' => 'exclamation-circle',
        'success' => 'check-circle'
    ];
    
    $clases = [
        'error' => 'border-danger',
        'warning' => 'border-warning',
        'success' => 'border-success'
    ];
    
    $html = '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resultado de Registro</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: "Montserrat", sans-serif;
                background-color: #f5f5f5;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .message-container {
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                padding: 30px;
                max-width: 500px;
                width: 100%;
                border-left: 4px solid;
            }
            .message-icon {
                font-size: 2.5rem;
                margin-bottom: 20px;
            }
            .message-title {
                font-weight: 600;
                margin-bottom: 15px;
            }
            .btn-new-register {
                background-color: #333;
                color: white;
                border: none;
                padding: 10px 20px;
                font-weight: 500;
                border-radius: 6px;
                margin-top: 20px;
                transition: all 0.3s;
            }
            .btn-new-register:hover {
                background-color: #222;
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="message-container '.$clases[$tipo].'">
            <div class="text-center">
                <i class="fas fa-'.$iconos[$tipo].' text-'.$tipo.' message-icon"></i>
                <h3 class="message-title">'.($tipo == 'error' ? 'Error' : ($tipo == 'warning' ? 'Advertencia' : 'Éxito')).'</h3>
                <p>'.$mensaje.'</p>';
    
    if ($mostrarBoton) {
        $html .= '<a href="../pages/personas/autoregistro.php" class="btn btn-new-register">
                    <i class="fas fa-user-plus me-2"></i>Registrar Nueva Persona
                  </a>';
    }
    
    $html .= '</div>
        </div>
    </body>
    </html>';
    
    return $html;
}

// Validar que venga la data necesaria
if (!isset($_POST['rut'], $_POST['nombre'], $_POST['apellido'], $_POST['genero'], $_POST['fecha_nacimiento'])) {
    die(mostrarMensaje('error', 'Datos incompletos. Por favor complete todos los campos requeridos.', true));
}

// Procesar RUT
$rut = strtoupper(preg_replace('/[^0-9kK]/', '', $_POST['rut']));

// Recibir datos del formulario
$nombre = htmlspecialchars(trim($_POST['nombre']));
$apellido = htmlspecialchars(trim($_POST['apellido']));
$genero = $_POST['genero'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$direccion = isset($_POST['direccion']) ? htmlspecialchars(trim($_POST['direccion'])) : '';
$medio_transporte = $_POST['medio_transporte'] ?? 'Pie';
$patente = isset($_POST['patente']) ? strtoupper(trim($_POST['patente'])) : '';
$id_comuna = $_POST['id_comuna'] ?? null;

// Verificar si el RUT ya existe
$stmt_check = $conexion->prepare("SELECT id, nombre, apellido FROM personas WHERE rut = ?");
$stmt_check->bind_param("s", $rut);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    $persona_existente = $result->fetch_assoc();
    die(mostrarMensaje('warning', "El RUT $rut ya está registrado a nombre de {$persona_existente['nombre']} {$persona_existente['apellido']}.", true));
}
$stmt_check->close();

// Insertar nueva persona
$stmt_insert = $conexion->prepare("
    INSERT INTO personas 
    (rut_completo, rut, nombre, apellido, genero, fecha_nacimiento, direccion, medio_transporte, patente, id_comuna)
    VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt_insert) {
    die(mostrarMensaje('error', 'Error al preparar la consulta de inserción: ' . $conexion->error, true));
}

$stmt_insert->bind_param(
    "ssssssssi", // ← antes estaba "sssssssssi"
    $rut,
    $nombre,
    $apellido,
    $genero,
    $fecha_nacimiento,
    $direccion,
    $medio_transporte,
    $patente,
    $id_comuna
);


if ($stmt_insert->execute()) {
    $nuevo_id = $stmt_insert->insert_id;
    echo mostrarMensaje('success', "¡Registro exitoso!<br><br>
              <strong>Nombre:</strong> $nombre $apellido<br>
              <strong>RUT:</strong> $rut<br>
              <strong>ID Registro:</strong> $nuevo_id", true);
} else {
    echo mostrarMensaje('error', 'Ocurrió un error al registrar la persona: ' . $stmt_insert->error, true);
}

$stmt_insert->close();
$conexion->close();
?>