<?php
require_once '../php/conexion.php';

$rut = $_GET['rut'] ?? '';
$dv = $_GET['dv'] ?? '';

$stmt = $conexion->prepare("SELECT * FROM personas WHERE rut = ? AND dv = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("is", $rut, $dv);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "Persona no encontrada.";
    exit;
}

$persona = $resultado->fetch_assoc();
$fecha = date('Y-m-d');
$hora = date('H:i:s');
?>

<h2>Ingreso de persona ya registrada</h2>
<form action="../php/guardarRegistro.php" method="POST">
    <input type="hidden" name="rut" value="<?= $persona['rut'] ?>">
    <input type="hidden" name="dv" value="<?= $persona['dv'] ?>">
    <input type="hidden" name="nombre" value="<?= htmlspecialchars($persona['nombre']) ?>">
    <input type="hidden" name="apellido" value="<?= htmlspecialchars($persona['apellido']) ?>">
    <input type="hidden" name="genero" value="<?= $persona['genero'] ?>">
    <input type="hidden" name="fecha_ingreso" value="<?= $fecha ?>">
    <input type="hidden" name="hora_ingreso" value="<?= $hora ?>">

    <p><strong>Nombre:</strong> <?= htmlspecialchars($persona['nombre']) ?></p>
    <p><strong>Apellido:</strong> <?= htmlspecialchars($persona['apellido']) ?></p>
    <p><strong>Género:</strong> <?= $persona['genero'] ?></p>

    <label>Dirección actual:
        <input type="text" name="direccion" required>
    </label><br>

    <label>Medio de transporte:
        <select name="medio_transporte" id="medio_transporte" required>
            <option value="">Selecciona una opción</option>
            <option value="Auto">Auto</option>
            <option value="Pie">A pie</option>
        </select>
    </label><br>

    <div id="patente_field" style="display:none;">
        <label>Patente:
            <input type="text" name="patente">
        </label><br>
    </div>

    <button type="submit">Registrar nuevo ingreso</button>
</form>

<script>
    document.getElementById('medio_transporte').addEventListener('change', function () {
        const patenteField = document.getElementById('patente_field');
        if (this.value === 'Auto') {
            patenteField.style.display = 'block';
        } else {
            patenteField.style.display = 'none';
        }
    });
</script>
