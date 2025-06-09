<?php
require_once '../php/conexion.php';

$rut = $_GET['rut'] ?? '';
$dv = $_GET['dv'] ?? '';

// Ya no usamos fecha/hora en PHP porque lo haremos en JS
// $fecha = date('Y-m-d');
// $hora = date('H:i:s');

$rut_completo = $rut . $dv;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de nueva persona</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/registro.css">
</head>

<body>
    <h2>Registro de nueva persona</h2>

    <form action="../php/guardarRegistro.php" method="POST" onsubmit="return validarFormulario();">
        <!-- Inputs ocultos para fecha y hora, se llenan con JS -->
        <input type="hidden" name="fecha_ingreso" id="fecha_ingreso" value="">
        <input type="hidden" name="hora_ingreso" id="hora_ingreso" value="">

        <div class="form-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" value="<?= htmlspecialchars($rut_completo) ?>" readonly>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" required>
        </div>

        <div class="form-group">
            <label for="genero">Género</label>
            <select id="genero" name="genero" required>
                <option value="">Seleccione</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
        </div>

        <!-- Agrega este bloque dentro del <form>, donde quieras que aparezca -->
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>


        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" required>
        </div>

        <div class="form-group">
            <label for="medio_transporte">Medio de transporte</label>
            <select id="medio_transporte" name="medio_transporte" onchange="togglePatente()" required>
                <option value="">Seleccione</option>
                <option value="Auto">Auto</option>
                <option value="A pie">A pie</option>
            </select>
        </div>

        <div id="campo_patente" class="hidden-field" style="display:none;">
            <div class="form-group">
                <label for="patente">Patente</label>
                <input type="text" id="patente" name="patente" placeholder="Ej: AB1234">
            </div>
        </div>

        <button type="submit">Guardar Registro</button>
    </form>

    <script>
        function togglePatente() {
            const medio = document.getElementById('medio_transporte').value;
            const patenteDiv = document.getElementById('campo_patente');
            const patenteInput = document.getElementById('patente');

            if (medio === 'Auto') {
                patenteDiv.style.display = 'block';
                patenteInput.setAttribute('required', '');
            } else {
                patenteDiv.style.display = 'none';
                patenteInput.removeAttribute('required');
            }
        }

        function validarFormulario() {
            const medio = document.getElementById('medio_transporte').value;
            const patente = document.getElementById('patente').value;

            if (medio === 'Auto' && patente.trim() === '') {
                alert('Por favor ingrese la patente del vehículo');
                return false;
            }
            return true;
        }

        // Insertar fecha y hora del navegador justo antes de enviar el formulario
        document.querySelector('form').addEventListener('submit', () => {
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