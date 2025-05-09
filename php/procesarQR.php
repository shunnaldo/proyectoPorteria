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
                $rut = (int) preg_replace('/[^0-9]/', '', $rutCompleto);
                $dv = strtoupper(substr($rutCompleto, -1));

                // Buscar persona
                $stmt = $conexion->prepare("SELECT * FROM personas WHERE rut = ? AND dv = ? ORDER BY id DESC LIMIT 1");
                $stmt->bind_param("is", $rut, $dv);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    header("Location: ../pages/formularioIngreso.php?rut=$rut&dv=$dv");
                    exit;
                } else {
                    header("Location: ../pages/registro.php?rut=$rut&dv=$dv");
                    exit;
                }
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
