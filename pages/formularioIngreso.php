<?php
require_once '../php/conexion.php';

// Limpiar y estandarizar RUT
$rut = strtoupper(preg_replace('/[^0-9K]/', '', $_GET['rut'] ?? ''));

// Buscar persona solo por el RUT completo
$stmt = $conexion->prepare("SELECT * FROM personas WHERE rut = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $rut);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo '<div class="error-message">Persona no encontrada.</div>';
    exit;
}

$persona = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de persona registrada</title>
    <link rel="stylesheet" href="../css/formulario.css">
</head>
<body>
    <h2>Ingreso de persona ya registrada</h2>

    <form action="../php/guardarRegistro.php" method="POST" id="ingresoForm">
        <input type="hidden" name="rut" value="<?= htmlspecialchars($persona['rut']) ?>">
        <input type="hidden" name="nombre" value="<?= htmlspecialchars($persona['nombre']) ?>">
        <input type="hidden" name="apellido" value="<?= htmlspecialchars($persona['apellido']) ?>">
        <input type="hidden" name="genero" value="<?= htmlspecialchars($persona['genero']) ?>">

        <!-- Fecha y hora vacíos que se llenan con JS -->
        <input type="hidden" name="fecha_ingreso" id="fecha_ingreso" value="">
        <input type="hidden" name="hora_ingreso" id="hora_ingreso" value="">

        <div class="info-section">
            <div class="info-item">
                <strong>Nombre:</strong> <?= htmlspecialchars($persona['nombre']) ?>
            </div>
            <div class="info-item">
                <strong>Apellido:</strong> <?= htmlspecialchars($persona['apellido']) ?>
            </div>
            <div class="info-item">
                <strong>Género:</strong> <?= htmlspecialchars($persona['genero']) ?>
            </div>
        </div>

        <div class="form-group">
            <label for="direccion">Dirección actual</label>
            <input type="text" id="direccion" name="direccion" required>
        </div>

        <div class="form-group">
            <label for="medio_transporte">Medio de transporte</label>
            <select id="medio_transporte" name="medio_transporte" required>
                <option value="">Selecciona una opción</option>
                <option value="Auto">Auto</option>
                <option value="Pie">A pie</option>
            </select>
        </div>

        <div id="patente_field" class="hidden" style="display:none;">
            <div class="form-group">
                <label for="patente">Patente</label>
                <input type="text" id="patente" name="patente">
            </div>
        </div>

        <button type="submit">Registrar nuevo ingreso</button>
    </form>

    <script>
    document.getElementById('medio_transporte').addEventListener('change', function() {
        const patenteField = document.getElementById('patente_field');
        const patenteInput = document.getElementById('patente');

        if (this.value === 'Auto') {
            patenteField.style.display = 'block';
            patenteInput.setAttribute('required', '');
        } else {
            patenteField.style.display = 'none';
            patenteInput.removeAttribute('required');
        }
    });

    // Capturar fecha y hora local antes de enviar
    document.getElementById('ingresoForm').addEventListener('submit', () => {
        const now = new Date();

        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        document.getElementById('fecha_ingreso').value = `${yyyy}-${mm}-${dd}`;

        const hh = String(now.getHours()).padStart(2, '0');
        const min = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('hora_ingreso').value = `${hh}:${min}:${ss}`;
    });
    </script>
</body>
</html>
