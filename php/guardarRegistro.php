<?php
require_once 'conexion.php';
session_start();

// Validar que venga la data necesaria
if (!isset($_POST['rut'], $_POST['fecha_ingreso'], $_POST['hora_ingreso'])) {
    die("❌ Datos incompletos.");
}

// Procesar RUT
$rut_completo = preg_replace('/[^0-9kK]/', '', $_POST['rut']);
$rut = strtoupper($rut_completo);

// Obtener datos del formulario
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$genero = $_POST['genero'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
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

// Obtener nombre del portero
$stmt_portero = $conexion->prepare("SELECT nombre FROM usuarios WHERE id = ?");
$stmt_portero->bind_param("i", $usuario_id);
$stmt_portero->execute();
$stmt_portero->bind_result($nombre_portero);
$stmt_portero->fetch();
$stmt_portero->close();

if (!$nombre_portero) {
    die("❌ No se encontró el nombre del portero.");
}
// Crear nueva persona
$stmt_insert = $conexion->prepare("
    INSERT INTO personas (rut, nombre, apellido, genero, fecha_nacimiento, direccion, medio_transporte, patente)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt_insert->bind_param("ssssssss", $rut, $nombre, $apellido, $genero, $fecha_nacimiento, $direccion, $medio_transporte, $patente);

if (!$stmt_insert->execute()) {
    die("❌ Error al registrar nueva persona: " . $stmt_insert->error);
}

$persona_id = $stmt_insert->insert_id;
$stmt_insert->close();

// Insertar en la bitácora
$stmt = $conexion->prepare("
    INSERT INTO bitacora_ingresos (persona_id, usuario_id, nombre_portero, porton_id, fecha_hora)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iisis", $persona_id, $usuario_id, $nombre_portero, $porton_id, $fecha_hora);


if ($stmt->execute()) {
    // Obtener los datos completos de la persona
    $query = "SELECT * FROM personas WHERE id = $persona_id";
    $result = $conexion->query($query);
    $persona = $result->fetch_assoc();

    // HTML mejorado
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro Exitoso</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Poppins", sans-serif;
            }
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .success-card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 600px;
                overflow: hidden;
                animation: fadeIn 0.6s ease-in-out;
            }
            .card-header {
                background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
                color: white;
                padding: 25px;
                text-align: center;
                position: relative;
            }
            .card-header h2 {
                font-weight: 600;
                font-size: 24px;
                margin-bottom: 5px;
            }
            .card-header .icon {
                font-size: 48px;
                margin-bottom: 15px;
                display: inline-block;
            }
            .card-body {
                padding: 30px;
            }
            .info-row {
                display: flex;
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }
            .info-label {
                font-weight: 500;
                color: #555;
                width: 40%;
            }
            .info-value {
                font-weight: 400;
                color: #333;
                width: 60%;
            }
            .btn-container {
                text-align: center;
                margin-top: 30px;
            }
            .btn-home {
                background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 50px;
                font-size: 16px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(107, 115, 255, 0.4);
                text-decoration: none;
                display: inline-block;
            }
            .btn-home:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(107, 115, 255, 0.6);
            }
            .badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                background: #e3f2fd;
                color: #1976d2;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @media (max-width: 576px) {
                .info-row {
                    flex-direction: column;
                }
                .info-label, .info-value {
                    width: 100%;
                }
                .info-label {
                    margin-bottom: 5px;
                }
            }
        </style>
    </head>
    <body>
        <div class="success-card">
            <div class="card-header">
                <div class="icon">✓</div>
                <h2>Registro Exitoso</h2>
                <p>El ingreso ha sido registrado correctamente</p>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-label">ID de Registro:</div>
                    <div class="info-value"><span class="badge">#' . htmlspecialchars($persona_id) . '</span></div>
                </div>
                <div class="info-row">
                    <div class="info-label">RUT:</div>
                    <div class="info-value">' . htmlspecialchars($persona['rut']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nombre Completo:</div>
                    <div class="info-value">' . htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Género:</div>
                    <div class="info-value">' . htmlspecialchars($persona['genero']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dirección:</div>
                    <div class="info-value">' . htmlspecialchars($persona['direccion']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Medio de Transporte:</div>
                    <div class="info-value">' . htmlspecialchars($persona['medio_transporte']) . '</div>
                </div>';

    if ($persona['medio_transporte'] === 'Auto') {
        echo '<div class="info-row">
                <div class="info-label">Patente:</div>
                <div class="info-value">' . htmlspecialchars($persona['patente']) . '</div>
              </div>';
    }

    echo '      <div class="info-row">
                    <div class="info-label">Fecha y Hora:</div>
                    <div class="info-value">' . htmlspecialchars($fecha_hora) . '</div>
                </div>
                <div class="btn-container">
                    <a href="../pages/portero_portones.php" class="btn-home">Volver al Inicio</a>
                </div>
            </div>
        </div>
    </body>
    </html>';
} else {
    echo "❌ Error al registrar ingreso: " . $stmt->error;
}

$stmt->close();
$conexion->close();
