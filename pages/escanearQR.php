<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
require '../php/conexion.php';

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== 'portero') {
    header("Location: logintrabajador.php");
    exit;
}

$porton_id = $_GET["porton_id"] ?? null;
if (!$porton_id || !is_numeric($porton_id)) {
    $_SESSION['error'] = "Portón no válido";
    header("Location: portero_portones.php");
    exit;
}

$_SESSION['porton_id'] = $porton_id;
$usuario_id = $_SESSION["usuario"]["id"];
$nombre_usuario = $_SESSION["usuario"]["nombre"];

$stmt = $conexion->prepare("SELECT nombre, estado FROM portones WHERE id = ?");
$stmt->bind_param("i", $porton_id);
$stmt->execute();
$result = $stmt->get_result();
$porton = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Escanear QR de la Cédula Chilena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/escanearQR.css">
    <style>
        .camera-info {
            text-align: center;
            margin-top: 10px;
            font-size: 0.8rem;
            color: #666;
        }
        
        .zoom-controls {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .zoom-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        @keyframes pulse {
            0% { border-color: rgba(52, 152, 219, 0.8); }
            50% { border-color: rgba(52, 152, 219, 0.4); }
            100% { border-color: rgba(52, 152, 219, 0.8); }
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido, <?= htmlspecialchars($nombre_usuario) ?></h1>
        <p>Portón: <?= htmlspecialchars($porton['nombre']) ?></p>
    </header>

    <div class="scanner-container">
        <header class="scanner-header">
            <button id="closeCamera" class="btn-close" aria-label="Cerrar cámara">
                <i class="bi bi-camera-video"></i>
            </button>
            <h1>Escanear QR de la Cédula Chilena</h1>
            <p class="instructions">Acerca el QR del carnet a la cámara (2-5 cm de distancia)</p>
        </header>

        <main class="scanner-main">
            <div class="video-container">
                <video id="video" autoplay playsinline></video>
                <div class="scan-frame"></div>
                <div class="zoom-controls">
                    <button id="zoomIn" class="zoom-btn">+</button>
                    <button id="zoomOut" class="zoom-btn">-</button>
                </div>
            </div>
            
            <div id="cameraInfo" class="camera-info">
                <span id="cameraName">Cámara posterior</span>
                <span id="cameraResolution"></span>
            </div>

            <div id="status" class="status-message">
                <div class="loading-spinner"></div>
                <span>📷 Acercando el carnet...</span>
            </div>
        </main>

        <footer class="scanner-footer">
            <button id="toggleCamera" class="btn-secondary">Cambiar cámara</button>
            <button id="cancelScan" class="btn-primary">Cancelar</button>
        </footer>

        <form id="qrForm" action="../php/procesarQR.php" method="POST">
            <input type="hidden" name="qrData" id="qrData">
            <input type="hidden" name="porton_id" value="<?= $porton_id ?>">
            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
        </form>
    </div>

<script>
// Reemplaza todo el código JavaScript con esta versión mejorada
document.addEventListener('DOMContentLoaded', async () => {
    const video = document.getElementById('video');
    const qrDataInput = document.getElementById('qrData');
    const status = document.getElementById('status');
    const statusText = status.querySelector('span');
    const toggleCameraBtn = document.getElementById('toggleCamera');
    const cancelScanBtn = document.getElementById('cancelScan');
    const closeCameraBtn = document.getElementById('closeCamera');
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const cameraName = document.getElementById('cameraName');
    const cameraResolution = document.getElementById('cameraResolution');

    let stream = null;
    let currentCameraIndex = 0;
    let videoDevices = [];
    let currentZoom = 1;
    let scanActive = false;
    let scanInterval;

    // Configuración optimizada para QR pequeños
    const SCAN_AREA_PERCENT = 0.25; // 25% del área central
    const SCAN_INTERVAL = 300; // ms entre escaneos
    const CONTRAST_FACTOR = 1.8; // Aumento de contraste

    // Función para procesamiento de imagen
    function enhanceImage(imageData) {
        const data = new Uint8ClampedArray(imageData.data.length);
        
        // Convertir a escala de grises con mejor contraste
        for (let i = 0; i < imageData.data.length; i += 4) {
            const r = imageData.data[i];
            const g = imageData.data[i + 1];
            const b = imageData.data[i + 2];
            
            // Fórmula de luminosidad mejorada
            const gray = Math.min(255, Math.max(0, 
                (0.299 * r + 0.587 * g + 0.114 * b) * CONTRAST_FACTOR - 128 * (CONTRAST_FACTOR - 1)
            ));
            
            data[i] = data[i + 1] = data[i + 2] = gray;
            data[i + 3] = imageData.data[i + 3]; // Alpha channel
        }
        
        return data;
    }

    // Función de escaneo optimizada
    function scanQRCode() {
        if (!scanActive) return;
        
        try {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            // Usar resolución nativa del video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Definir área de escaneo más pequeña
            const scanSize = Math.min(canvas.width, canvas.height) * SCAN_AREA_PERCENT;
            const scanRegion = {
                x: (canvas.width - scanSize) / 2,
                y: (canvas.height - scanSize) / 2,
                width: scanSize,
                height: scanSize
            };
            
            // Procesar imagen
            const imageData = context.getImageData(
                scanRegion.x, scanRegion.y, 
                scanRegion.width, scanRegion.height
            );
            const enhancedData = enhanceImage(imageData);
            
            // Intentar leer QR
            const qrCode = jsQR(enhancedData, scanRegion.width, scanRegion.height);
            
            if (qrCode) {
                stopScanning();
                statusText.textContent = "✅ QR detectado! Procesando...";
                status.id = "success";
                qrDataInput.value = qrCode.data;
                document.getElementById('qrForm').submit();
            }
        } catch (error) {
            console.error("Error en escaneo:", error);
        }
    }

    // Iniciar escaneo periódico
    function startScanning() {
        if (scanActive) return;
        scanActive = true;
        scanInterval = setInterval(scanQRCode, SCAN_INTERVAL);
    }

    // Detener escaneo
    function stopScanning() {
        scanActive = false;
        clearInterval(scanInterval);
    }

    // Iniciar cámara con configuración optimizada
    async function startCamera(deviceId = null) {
        stopCamera();
        
        const constraints = {
            video: {
                width: { ideal: 1920 },
                height: { ideal: 1080 },
                frameRate: { ideal: 60 },
                advanced: [{
                    focusMode: "continuous",
                    exposureMode: "continuous"
                }]
            }
        };
        
        if (deviceId) {
            constraints.video.deviceId = { exact: deviceId };
        } else {
            constraints.video.facingMode = { ideal: 'environment' };
        }
        
        try {
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;
            
            // Mostrar información de la cámara
            const track = stream.getVideoTracks()[0];
            const settings = track.getSettings();
            cameraName.textContent = track.label || "Cámara trasera";
            cameraResolution.textContent = `${settings.width}×${settings.height} @ ${settings.frameRate}fps`;
            
            await video.play();
            statusText.textContent = "🔍 Enfoque en el QR del carnet...";
            startScanning();
            
        } catch (error) {
            console.error("Error en cámara:", error);
            showCameraError(error);
        }
    }

    // Manejo de errores
    function showCameraError(error) {
        let errorMessage = "❌ Error en cámara";
        
        if (error.name === 'NotAllowedError') {
            errorMessage = "❌ Permisos de cámara denegados";
        } else if (error.name === 'NotFoundError') {
            errorMessage = "❌ Cámara no encontrada";
        } else if (error.name === 'NotReadableError') {
            errorMessage = "❌ Cámara no accesible";
        }
        
        statusText.textContent = errorMessage;
        status.id = "error";
    }

    // Control de cámara
    function stopCamera() {
        stopScanning();
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
            stream = null;
        }
    }

    // Event listeners
    zoomInBtn.addEventListener('click', () => {
        currentZoom = Math.min(currentZoom + 0.25, 3);
        video.style.transform = `scale(${currentZoom})`;
    });

    zoomOutBtn.addEventListener('click', () => {
        currentZoom = Math.max(currentZoom - 0.25, 1);
        video.style.transform = `scale(${currentZoom})`;
    });

    toggleCameraBtn.addEventListener('click', async () => {
        if (videoDevices.length < 2) return;
        currentCameraIndex = (currentCameraIndex + 1) % videoDevices.length;
        startCamera(videoDevices[currentCameraIndex].deviceId);
    });

    closeCameraBtn.addEventListener('click', () => {
        if (stream) {
            stopCamera();
            video.style.transform = 'scale(1)';
            currentZoom = 1;
        } else {
            startCamera();
        }
    });

    cancelScanBtn.addEventListener('click', () => {
        stopCamera();
        window.location.href = 'portero_portones.php';
    });

    // Inicialización
    async function init() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            videoDevices = devices.filter(d => d.kind === 'videoinput');
            toggleCameraBtn.style.display = videoDevices.length > 1 ? 'block' : 'none';
            await startCamera();
        } catch (error) {
            console.error("Error inicial:", error);
            statusText.textContent = "❌ Error al iniciar escáner";
        }
    }

    init();
});
</script>
</body>
</html>