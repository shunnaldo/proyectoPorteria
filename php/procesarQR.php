<?php
session_start();
require_once 'conexion.php';       // $conexion
require_once 'conexionCoraje.php'; // $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = $_POST['qrData'] ?? '';
    $rut_manual = $_POST['rut_manual'] ?? '';
    $porton_id = $_POST['porton_id'] ?? null;

    if ((!empty($qrData) || !empty($rut_manual)) && is_numeric($porton_id)) {
        $rut = '';

        // Caso 1: viene desde QR
        if (!empty($qrData)) {
            $parsedUrl = parse_url($qrData);

            // QR formato cédula chilena con parámetro RUN
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $params);
                $rutCompleto = $params['RUN'] ?? '';
                if (!empty($rutCompleto)) {
                    $rut = strtoupper(preg_replace('/[^0-9K]/', '', $rutCompleto));
                }
            }

            // QR personalizado tipo "RUT: 12345678K"
            if (empty($rut)) {
                if (preg_match('/RUT:\s*([0-9Kk]+)/i', $qrData, $matches)) {
                    $rut = strtoupper(preg_replace('/[^0-9K]/', '', $matches[1]));
                }
            }
        }

        // Caso 2: entrada manual
        if (empty($rut) && !empty($rut_manual)) {
            $rut = strtoupper(preg_replace('/[^0-9K]/', '', $rut_manual));
        }

        // Validación final del RUT
        if (!empty($rut)) {
            // Verificar si está bloqueado (en $conexion)
            $stmt = $conexion->prepare("SELECT 1 FROM blacklist WHERE rut = ? AND porton_id = ?");
            $stmt->bind_param("si", $rut, $porton_id);
            $stmt->execute();
            $blacklistResult = $stmt->get_result();

            if ($blacklistResult->num_rows > 0) {
                // Acceso denegado por blacklist
                echo '
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Acceso Denegado</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f8f9fa;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            margin: 0;
                        }
                        .container {
                            text-align: center;
                            background-color: white;
                            padding: 2rem;
                            border-radius: 10px;
                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                            max-width: 500px;
                        }
                        .icon {
                            font-size: 3rem;
                            color: #dc3545;
                            margin-bottom: 1rem;
                        }
                        h1 {
                            color: #dc3545;
                            margin-bottom: 1rem;
                        }
                        p {
                            color: #6c757d;
                            margin-bottom: 2rem;
                        }
                        .btn {
                            background-color: #007bff;
                            color: white;
                            border: none;
                            padding: 0.5rem 1rem;
                            border-radius: 5px;
                            text-decoration: none;
                            font-size: 1rem;
                            cursor: pointer;
                            transition: background-color 0.3s;
                        }
                        .btn:hover {
                            background-color: #0056b3;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Acceso Denegado</h1>
                        <p>Lo sentimos, este usuario no tiene permiso para acceder a este portón.</p>
                        <p>Por favor, contacta al administrador si crees que esto es un error.</p>
                        <a href="../pages/portero_portones.php" class="btn">Volver al Inicio</a>
                    </div>
                </body>
                </html>';
                exit;
            }

            if ($porton_id == 16) {
                // Portón exclusivo para preinscripciones (Expo)
                // Limpiar rut para comparar (sin puntos ni guiones)
                $rut_simple = str_replace(['.', '-'], '', strtoupper($rut));

                $stmt = $conn->prepare("SELECT * FROM preinscripciones WHERE REPLACE(REPLACE(UPPER(rut), '.', ''), '-', '') = ? LIMIT 1");
                $stmt->bind_param("s", $rut_simple);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    // Si existe en preinscripciones, iniciar sesión con datos
                    $_SESSION['preinscripcion'] = $resultado->fetch_assoc();

                    // Redirigir a formulario (sin pasar rut en URL, está en sesión)
                    header("Location: ../pages/formularioIngresoExpo.php?porton_id=$porton_id");
                    exit;
                } else {
                    // No está inscrito en preinscripciones
                    echo '
                    <div style="text-align:center; padding:2rem;">
                        <div style="font-size:3rem; color:#dc3545;">🚫</div>
                        <h2 style="color:#343a40;">Acceso Denegado</h2>
                        <p style="color:#6c757d;">El RUT escaneado no está inscrito en el evento.</p>
                        <a href="../index.php" style="background:#007bff; color:#fff; padding:0.5rem 1rem; border-radius:5px; text-decoration:none;">Volver al inicio</a>
                    </div>';
                    exit;
                }
            } else {
                // Flujo normal para portones comunes
                $stmt = $conexion->prepare("SELECT * FROM personas WHERE rut = ? ORDER BY id DESC LIMIT 1");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    header("Location: ../pages/formularioIngreso.php?rut=$rut&porton_id=$porton_id");
                } else {
                    header("Location: ../pages/registro.php?rut=$rut&porton_id=$porton_id");
                }
                exit;
            }
        } else {
            // RUT no válido
            echo '
            <div style="text-align:center; padding:2rem;">
                <div style="font-size:3rem; color:#ffc107;">⚠️</div>
                <h2 style="color:#343a40;">RUT no válido</h2>
                <p style="color:#6c757d;">No se pudo obtener un RUT válido ni desde el QR ni desde el campo manual.</p>
                <a href="../index.php" style="background:#007bff; color:#fff; padding:0.5rem 1rem; border-radius:5px; text-decoration:none;">Volver a intentar</a>
            </div>';
        }
    } else {
        // Faltan datos
        echo '
        <div style="text-align:center; padding:2rem;">
            <div style="font-size:3rem; color:#ffc107;">⚠️</div>
            <h2 style="color:#343a40;">Datos incompletos</h2>
            <p style="color:#6c757d;">Falta información del QR, del RUT manual o del portón para continuar.</p>
            <a href="../index.php" style="background:#007bff; color:#fff; padding:0.5rem 1rem; border-radius:5px; text-decoration:none;">Volver a intentar</a>
        </div>';
    }
} else {
    // Método no permitido
    echo '
    <div style="text-align:center; padding:2rem;">
        <div style="font-size:3rem; color:#dc3545;">❌</div>
        <h2 style="color:#343a40;">Método no permitido</h2>
        <p style="color:#6c757d;">Esta acción no puede realizarse de esta manera.</p>
        <a href="../index.php" style="background:#007bff; color:#fff; padding:0.5rem 1rem; border-radius:5px; text-decoration:none;">Volver al inicio</a>
    </div>';
}
