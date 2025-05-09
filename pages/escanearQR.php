<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Escanear QR de la Cédula Chilena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- jsQR -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../css/escanearQR.css">
</head>
<body>
    <div class="scanner-container">
        <header class="scanner-header">
            <button id="closeCamera" class="btn-close" aria-label="Cerrar cámara">
                <i class="bi bi-camera-video"></i>
            </button>
            <h1>Escanear QR de la Cédula Chilena</h1>
            <p class="instructions">Apunta la cámara al código QR de la cédula. Asegúrate de que esté bien iluminado y completamente dentro del marco.</p>
        </header>
        
        <main class="scanner-main">
            <div class="video-container">
                <video id="video" autoplay playsinline></video>
                <div class="scan-frame"></div>
            </div>
            
            <div id="status" class="status-message">
                <div class="loading-spinner"></div>
                <span>📷 Esperando cámara...</span>
            </div>
        </main>
        
        <footer class="scanner-footer">
            <button id="toggleCamera" class="btn-secondary">Cambiar cámara</button>
            <button id="cancelScan" class="btn-primary">Cancelar</button>
        </footer>

        <form id="qrForm" action="../php/procesarQR.php" method="POST">
            <input type="hidden" name="qrData" id="qrData">
        </form>
    </div>

    <script>
        const video = document.getElementById('video');
        const qrDataInput = document.getElementById('qrData');
        const status = document.getElementById('status');
        const statusText = status.querySelector('span');
        const toggleCameraBtn = document.getElementById('toggleCamera');
        const cancelScanBtn = document.getElementById('cancelScan');
        const closeCameraBtn = document.getElementById('closeCamera');
        const icon = closeCameraBtn.querySelector('i');
        let currentFacingMode = "environment";
        let stream = null;

        function startCamera(facingMode) {
            stopCamera();
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: facingMode,
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            })
            .then(s => {
                stream = s;
                video.srcObject = stream;
                video.play();
                statusText.textContent = "🔍 Buscando QR...";
                status.querySelector('.loading-spinner').style.display = 'block';
                requestAnimationFrame(scanQRCode);
            })
            .catch(err => {
                console.error("No se pudo acceder a la cámara: ", err);
                statusText.textContent = "❌ Error: No se pudo acceder a la cámara";
                status.id = "error";
                status.querySelector('.loading-spinner').style.display = 'none';
            });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                stream = null;
            }
        }

        function closeCamera() {
            stopCamera();
            video.style.display = 'none';
            document.querySelector('.scan-frame').style.display = 'none';
            statusText.textContent = "Cámara cerrada";
            status.querySelector('.loading-spinner').style.display = 'none';
        }

        function openCamera() {
            video.style.display = 'block';
            document.querySelector('.scan-frame').style.display = 'block';
            startCamera(currentFacingMode);
        }

        function scanQRCode() {
            if (stream && video.readyState === video.HAVE_ENOUGH_DATA) {
                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");

                const videoRatio = video.videoWidth / video.videoHeight;
                const containerWidth = video.parentElement.clientWidth;
                const containerHeight = video.parentElement.clientHeight;

                if (containerHeight * videoRatio > containerWidth) {
                    canvas.height = containerWidth / videoRatio;
                    canvas.width = containerWidth;
                } else {
                    canvas.height = containerHeight;
                    canvas.width = containerHeight * videoRatio;
                }

                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const scanRegion = {
                    x: Math.max(0, (canvas.width - 300) / 2),
                    y: Math.max(0, (canvas.height - 300) / 2),
                    width: Math.min(300, canvas.width),
                    height: Math.min(300, canvas.height)
                };

                const imageData = context.getImageData(scanRegion.x, scanRegion.y, scanRegion.width, scanRegion.height);
                const qrCode = jsQR(imageData.data, scanRegion.width, scanRegion.height);

                if (qrCode) {
                    statusText.textContent = "✅ QR leído correctamente. Procesando...";
                    status.id = "success";
                    status.querySelector('.loading-spinner').style.display = 'none';
                    qrDataInput.value = qrCode.data;
                    stopCamera();
                    document.getElementById('qrForm').submit();
                } else {
                    requestAnimationFrame(scanQRCode);
                }
            } else {
                requestAnimationFrame(scanQRCode);
            }
        }

        toggleCameraBtn.addEventListener('click', () => {
            currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
            startCamera(currentFacingMode);
        });

        closeCameraBtn.addEventListener('click', () => {
            if (stream) {
                closeCamera();
                closeCameraBtn.classList.add('btn-open');
                icon.className = 'bi bi-camera-video';
            } else {
                openCamera();
                closeCameraBtn.classList.remove('btn-open');
                icon.className = 'bi bi-x-lg';
            }
        });

        cancelScanBtn.addEventListener('click', () => {
            stopCamera();
            window.location.href = '../index.html'; // Ajusta si es necesario
        });

        // Iniciar cámara por defecto
        startCamera(currentFacingMode);

        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                if (stream) {
                    video.style.width = '';
                    video.style.height = '';
                }
            }, 200);
        });
    </script>
</body>
</html>
