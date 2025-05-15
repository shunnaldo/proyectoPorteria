<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = $_POST['qrData'] ?? '';

    if (!empty($qrData)) {
        $parsedUrl = parse_url($qrData);

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);

            $rutCompleto = $params['RUN'] ?? '';

            if (!empty($rutCompleto)) {
                $rut = strtoupper(preg_replace('/[^0-9K]/', '', $rutCompleto));

                // Buscar persona
                $stmt = $conexion->prepare("SELECT * FROM personas WHERE rut = ? ORDER BY id DESC LIMIT 1");
                $stmt->bind_param("s", $rut);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    header("Location: ../pages/formularioIngreso.php?rut=$rut");
                } else {
                    header("Location: ../pages/registro.php?rut=$rut");
                }
                exit;
            } else {
                echo "⚠️ El QR no contiene un RUT válido.";
            }
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
