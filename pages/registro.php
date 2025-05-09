<?php
require_once '../php/conexion.php';

$rut = $_GET['rut'] ?? '';
$dv = $_GET['dv'] ?? '';
$fecha = date('Y-m-d');
$hora = date('H:i:s');

// Juntar RUT + DV
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
        <input type="hidden" name="fecha_ingreso" value="<?= $fecha ?>">
        <input type="hidden" name="hora_ingreso" value="<?= $hora ?>">

        <div class="form-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" value="<?= $rut_completo ?>" readonly>
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

        <div id="campo_patente" class="hidden-field">
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
            patenteDiv.classList.remove('hidden-field');
            patenteInput.setAttribute('required', '');
        } else {
            patenteDiv.classList.add('hidden-field');
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
    </script>
</body>
</html>