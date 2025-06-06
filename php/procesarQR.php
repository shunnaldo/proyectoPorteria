<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = $_POST['qrData'] ?? '';
    $porton_id = $_POST['porton_id'] ?? null;

    if (!empty($qrData) && is_numeric($porton_id)) {
        $parsedUrl = parse_url($qrData);

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);

            $rutCompleto = $params['RUN'] ?? '';

            if (!empty($rutCompleto)) {
                $rut = strtoupper(preg_replace('/[^0-9K]/', '', $rutCompleto));

                // Verificar si está bloqueado
                $stmt = $conexion->prepare("SELECT 1 FROM blacklist WHERE rut = ? AND porton_id = ?");
                $stmt->bind_param("si", $rut, $porton_id);
                $stmt->execute();
                $blacklistResult = $stmt->get_result();

                if ($blacklistResult->num_rows > 0) {
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
                            <div class="icon">🚫</div>
                            <h1>Acceso Denegado</h1>
                            <p>Lo sentimos, este usuario no tiene permiso para acceder a este portón.</p>
                            <p>Por favor, contacta al administrador si crees que esto es un error.</p>
                            <a href="../index.php" class="btn">Volver al Inicio</a>
                        </div>
                    </body>
                    </html>';
                    exit;
                }

                // Resto del código...
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

            } else {
                // Mensaje de error mejorado para RUT inválido
                echo '
                <div style="text-align: center; padding: 2rem;">
                    <div style="font-size: 3rem; color: #ffc107;">⚠️</div>
                    <h2 style="color: #343a40;">QR no válido</h2>
                    <p style="color: #6c757d;">El código QR escaneado no contiene un RUT válido.</p>
                    <a href="../index.php" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Volver a intentar</a>
                </div>';
            }
        } else {
            // Mensaje de error mejorado para parámetros faltantes
            echo '
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; color: #ffc107;">⚠️</div>
                <h2 style="color: #343a40;">Error en el QR</h2>
                <p style="color: #6c757d;">No se encontraron parámetros válidos en el código QR escaneado.</p>
                <a href="../index.php" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Volver a intentar</a>
            </div>';
        }
    } else {
        // Mensaje de error mejorado para datos incompletos
        echo '
        <div style="text-align: center; padding: 2rem;">
            <div style="font-size: 3rem; color: #ffc107;">⚠️</div>
            <h2 style="color: #343a40;">Datos incompletos</h2>
            <p style="color: #6c757d;">Falta información del QR o del portón para continuar.</p>
            <a href="../index.php" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Volver a intentar</a>
        </div>';
    }
} else {
    // Mensaje de error mejorado para método no permitido
    echo '
    <div style="text-align: center; padding: 2rem;">
        <div style="font-size: 3rem; color: #dc3545;">❌</div>
        <h2 style="color: #343a40;">Método no permitido</h2>
        <p style="color: #6c757d;">Esta acción no puede realizarse de esta manera.</p>
        <a href="../index.php" style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Volver al inicio</a>
    </div>';
}