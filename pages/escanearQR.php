<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Escanear QR de la Cédula Chilena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        #video { border: 2px solid #444; max-width: 100%; }
        #status { margin-top: 10px; font-size: 1.2em; font-weight: bold; color: #555; }
        #success { color: green; }
        #error { color: red; }
    </style>
</head>
<body>
    <h2>Escanear QR de la Cédula Chilena</h2>

    <video id="video" autoplay></video>
    <div id="status">📷 Esperando cámara...</div>

    <form id="qrForm" action="../php/procesarQR.php" method="POST">
        <input type="hidden" name="qrData" id="qrData">
        <!-- El botón solo para pruebas manuales -->
        <!-- <button type="submit">Enviar Datos</button> -->
    </form>

    <script>
        const video = document.getElementById('video');
        const qrDataInput = document.getElementById('qrData');
        const status = document.getElementById('status');

        // Iniciar la cámara
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(stream => {
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                status.textContent = "🔍 Buscando QR...";
                requestAnimationFrame(scanQRCode);
            })
            .catch(err => {
                console.error("No se pudo acceder a la cámara: ", err);
                status.textContent = "❌ Error: No se pudo acceder a la cámara";
                status.id = "error";
            });

        function scanQRCode() {
            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");

            if (video.videoWidth > 0 && video.videoHeight > 0) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

                if (qrCode) {
                    status.textContent = "✅ QR leído correctamente. Enviando...";
                    status.id = "success";
                    qrDataInput.value = qrCode.data;
                    video.pause();
                    document.getElementById('qrForm').submit();
                } else {
                    status.textContent = "🔄 Escaneando...";
                    requestAnimationFrame(scanQRCode);
                }
            } else {
                requestAnimationFrame(scanQRCode);
            }
        }
    </script>
</body>
</html>
