<?php
require_once '../php/conexion.php';

$rut = $_GET['rut'] ?? '';
$dv = $_GET['dv'] ?? '';
$fecha = date('Y-m-d');
$hora = date('H:i:s');

// Juntar RUT + DV
$rut_completo = $rut . $dv;
?>

<h2>Registro de nueva persona</h2>

<form action="../php/guardarRegistro.php" method="POST" onsubmit="return validarFormulario();">
    <input type="hidden" name="fecha_ingreso" value="<?= $fecha ?>">
    <input type="hidden" name="hora_ingreso" value="<?= $hora ?>">

    <label>RUT: <input type="text" name="rut" value="<?= $rut_completo ?>" required readonly></label><br>

    <label>Nombre: <input type="text" name="nombre" required></label><br>
    <label>Apellido: <input type="text" name="apellido" required></label><br>

    <label>Género:
        <select name="genero" required>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
            <option value="Otro">Otro</option>
        </select>
    </label><br>

    <label>Dirección: <input type="text" name="direccion" required></label><br>

    <label>Medio de transporte:
        <select name="medio_transporte" id="medio_transporte" onchange="togglePatente()" required>
            <option value="">Seleccione</option>
            <option value="Auto">Auto</option>
            <option value="A pie">A pie</option>
        </select>
    </label><br>

    <div id="campo_patente" style="display:none;">
        <label>Patente: <input type="text" name="patente" id="patente"></label><br>
    </div>

    <button type="submit">Guardar Registro</button>
</form>

<script>
function togglePatente() {
    const medio = document.getElementById('medio_transporte').value;
    const patenteDiv = document.getElementById('campo_patente');
    patenteDiv.style.display = medio === 'Auto' ? 'block' : 'none';
}

function validarFormulario() {
    const medio = document.getElementById('medio_transporte').value;
    const patente = document.getElementById('patente').value;
    if (medio === 'Auto' && patente.trim() === '') {
        alert('Debe ingresar la patente si elige Auto.');
        return false;
    }
    return true;
}
</script>
