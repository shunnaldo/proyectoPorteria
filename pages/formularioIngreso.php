<?php
require_once '../php/conexion.php';

$rut = $_GET['rut'] ?? '';
$dv = $_GET['dv'] ?? '';

$stmt = $conexion->prepare("SELECT * FROM personas WHERE rut = ? AND dv = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("is", $rut, $dv);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo '<div class="error-message">Persona no encontrada.</div>';
    exit;
}

$persona = $resultado->fetch_assoc();
$fecha = date('Y-m-d');
$hora = date('H:i:s');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de persona registrada</title>
    <style>
        :root {
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-color: #e0e0e0;
            --primary-color: #2c7be5;
            --success-color: #28a745;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f9f9f9;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            font-weight: 400;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: var(--text-color);
        }

        form {
            background: white;
            padding: 2rem;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .info-section {
            background-color: var(--light-gray);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .info-item {
            margin-bottom: 0.5rem;
        }

        .info-item strong {
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }

        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(44, 123, 229, 0.1);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--success-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .hidden {
            display: none;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <h2>Ingreso de persona ya registrada</h2>

    <form action="../php/guardarRegistro.php" method="POST">
        <input type="hidden" name="rut" value="<?= $persona['rut'] ?>">
        <input type="hidden" name="dv" value="<?= $persona['dv'] ?>">
        <input type="hidden" name="nombre" value="<?= htmlspecialchars($persona['nombre']) ?>">
        <input type="hidden" name="apellido" value="<?= htmlspecialchars($persona['apellido']) ?>">
        <input type="hidden" name="genero" value="<?= $persona['genero'] ?>">
        <input type="hidden" name="fecha_ingreso" value="<?= $fecha ?>">
        <input type="hidden" name="hora_ingreso" value="<?= $hora ?>">

        <div class="info-section">
            <div class="info-item">
                <strong>Nombre:</strong> <?= htmlspecialchars($persona['nombre']) ?>
            </div>
            <div class="info-item">
                <strong>Apellido:</strong> <?= htmlspecialchars($persona['apellido']) ?>
            </div>
            <div class="info-item">
                <strong>Género:</strong> <?= $persona['genero'] ?>
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

        <div id="patente_field" class="hidden">
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
                patenteField.classList.remove('hidden');
                patenteInput.setAttribute('required', '');
            } else {
                patenteField.classList.add('hidden');
                patenteInput.removeAttribute('required');
            }
        });
    </script>
</body>
</html>