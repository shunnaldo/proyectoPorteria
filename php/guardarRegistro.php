<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminar cualquier caracter no numérico ni K/k
    $rut_completo = preg_replace('/[^0-9kK]/', '', $_POST['rut']);
    $dv = strtoupper(substr($rut_completo, -1));
    $rut = substr($rut_completo, 0, -1); // Mantener como string para preservar ceros iniciales

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $genero = $_POST['genero'];
    $direccion = $_POST['direccion'];
    $medio_transporte = $_POST['medio_transporte'];
    $patente = ($medio_transporte === 'Auto') ? $_POST['patente'] : 'N/A';
    $fecha = $_POST['fecha_ingreso'];
    $hora = $_POST['hora_ingreso'];

    $stmt = $conexion->prepare("INSERT INTO personas (
        rut, dv, nombre, apellido, genero, direccion, medio_transporte, patente, fecha_ingreso, hora_ingreso
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isssssssss", $rut, $dv, $nombre, $apellido, $genero, $direccion, $medio_transporte, $patente, $fecha, $hora);

    if ($stmt->execute()) {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Comprobante de Registro</title>
            <style>
                :root {
                    --primary-color: #4a6bdf;
                    --success-color: #28a745;
                    --text-color: #333;
                    --light-gray: #f8f9fa;
                    --border-color: #dee2e6;
                }
                
                body {
                    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    background-color: #f5f7ff;
                    color: var(--text-color);
                    line-height: 1.6;
                    padding: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                }
                
                .receipt-container {
                    max-width: 500px;
                    width: 100%;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }
                
                .receipt-header {
                    background: linear-gradient(135deg, var(--primary-color), #6c8eff);
                    color: white;
                    padding: 20px;
                    text-align: center;
                }
                
                .receipt-header h2 {
                    margin: 0;
                    font-weight: 600;
                    font-size: 1.5rem;
                }
                
                .receipt-status {
                    display: inline-flex;
                    align-items: center;
                    background-color: rgba(255, 255, 255, 0.2);
                    padding: 5px 15px;
                    border-radius: 20px;
                    margin-top: 10px;
                    font-size: 0.9rem;
                }
                
                .receipt-body {
                    padding: 25px;
                }
                
                .receipt-item {
                    display: flex;
                    justify-content: space-between;
                    padding: 12px 0;
                    border-bottom: 1px solid var(--border-color);
                }
                
                .receipt-item:last-child {
                    border-bottom: none;
                }
                
                .receipt-label {
                    font-weight: 500;
                    color: #666;
                }
                
                .receipt-value {
                    font-weight: 600;
                    text-align: right;
                    font-family: monospace;
                }
                
                .receipt-divider {
                    height: 1px;
                    background: linear-gradient(to right, transparent, var(--border-color), transparent);
                    margin: 20px 0;
                }
                
                .receipt-footer {
                    padding: 0 25px 25px;
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }
                
                .btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    padding: 10px 20px;
                    border-radius: 6px;
                    font-weight: 500;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }
                
                .btn-primary {
                    background-color: var(--primary-color);
                    color: white;
                }
                
                .btn-outline {
                    border: 1px solid var(--primary-color);
                    color: var(--primary-color);
                    background: white;
                }
                
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                
                .btn i {
                    margin-right: 8px;
                }
                
                @media (max-width: 480px) {
                    .receipt-body, .receipt-footer {
                        padding: 20px;
                    }
                    
                    .receipt-item {
                        flex-direction: column;
                        gap: 5px;
                    }
                    
                    .receipt-value {
                        text-align: left;
                    }
                }
            </style>
        </head>
        <body>
            <div class="receipt-container">
                <div class="receipt-header">
                    <h2>Comprobante de Registro</h2>
                    <div class="receipt-status">
                        <i class="fas fa-check-circle"></i> Registro exitoso
                    </div>
                </div>
                
                <div class="receipt-body">
                    <div class="receipt-item">
                        <span class="receipt-label">RUT</span>
                        <span class="receipt-value">' . $rut . $dv . '</span>
                    </div>
                    
                    <div class="receipt-item">
                        <span class="receipt-label">Nombre Completo</span>
                        <span class="receipt-value">' . htmlspecialchars($nombre) . ' ' . htmlspecialchars($apellido) . '</span>
                    </div>
                    
                    <div class="receipt-item">
                        <span class="receipt-label">Género</span>
                        <span class="receipt-value">' . htmlspecialchars($genero) . '</span>
                    </div>
                    
                    <div class="receipt-item">
                        <span class="receipt-label">Dirección</span>
                        <span class="receipt-value">' . htmlspecialchars($direccion) . '</span>
                    </div>
                    
                    <div class="receipt-item">
                        <span class="receipt-label">Medio de Transporte</span>
                        <span class="receipt-value">' . htmlspecialchars($medio_transporte) . '</span>
                    </div>
                    
                    <div class="receipt-item">
                        <span class="receipt-label">Patente</span>
                        <span class="receipt-value">' . htmlspecialchars($patente) . '</span>
                    </div>
                    
                    <div class="receipt-divider"></div>
                    
                    <div class="receipt-item">
                        <span class="receipt-label">Fecha de Ingreso</span>
                        <span class="receipt-value">' . $fecha . '</span>
                    </div>
                    
                    <div class="receipt-item">
                        <span class="receipt-label">Hora de Ingreso</span>
                        <span class="receipt-value">' . $hora . '</span>
                    </div>
                </div>
                
                <div class="receipt-footer">
                    <a href="../pages/escanearQR.php" class="btn btn-outline">
                        <i class="fas fa-camera"></i> Escanear otro QR
                    </a>
                    <a href="../index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Ir al Inicio
                    </a>
                </div>
            </div>
            
            <!-- Font Awesome para iconos -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        </body>
        </html>';
    } else {
        echo '<div style="max-width: 500px; margin: 40px auto; padding: 20px; background: #ffecec; border: 1px solid #ffb3b3; border-radius: 8px; color: #d32f2f;">
                <h2 style="margin-top: 0;"><i class="fas fa-exclamation-circle"></i> Error al guardar</h2>
                <p>' . $stmt->error . '</p>
                <a href="javascript:history.back()" style="display: inline-block; margin-top: 15px; padding: 8px 15px; background: #d32f2f; color: white; text-decoration: none; border-radius: 4px;">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
              </div>
              <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">';
    }
} else {
    echo '<div style="max-width: 500px; margin: 40px auto; padding: 20px; background: #fff3e0; border: 1px solid #ffcc80; border-radius: 8px; color: #e65100;">
            <h2 style="margin-top: 0;"><i class="fas fa-exclamation-triangle"></i> Método no permitido</h2>
            <p>Esta página solo acepta solicitudes POST.</p>
            <a href="../index.php" style="display: inline-block; margin-top: 15px; padding: 8px 15px; background: #e65100; color: white; text-decoration: none; border-radius: 4px;">
                <i class="fas fa-home"></i> Ir al Inicio
            </a>
          </div>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">';
}
?>