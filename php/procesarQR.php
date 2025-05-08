<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = $_POST['qrData'] ?? '';

    if (!empty($qrData)) {
        echo "<h2>Contenido completo del QR:</h2>";
        echo "<p>" . htmlspecialchars($qrData) . "</p>";

        // Verificamos si es una URL con parámetros
        $parsedUrl = parse_url($qrData);
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);

            echo "<h3>Datos extraídos:</h3>";
            echo "RUT: " . htmlspecialchars($params['RUN'] ?? 'No disponible') . "<br>";
            echo "Tipo Documento: " . htmlspecialchars($params['type'] ?? 'No disponible') . "<br>";
            echo "N° de Serie: " . htmlspecialchars($params['serial'] ?? 'No disponible') . "<br>";
            echo "MRZ: " . htmlspecialchars($params['mrz'] ?? 'No disponible') . "<br>";
        } else {
            echo "⚠️ No se encontraron parámetros en el QR.";
        }
    } else {
        echo "No se recibió contenido del QR.";
    }
} else {
    echo "Método no permitido.";
}
?>
