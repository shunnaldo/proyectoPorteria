<?php
require_once '../php/conexion.php';

if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = intval($_GET['id']);

// Obtener datos actuales del portón
$sql = "SELECT * FROM portones WHERE id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows === 0) {
    die("Portón no encontrado.");
}

$porton = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Portón</title>
  <style>
    form {
      width: 400px;
      margin: 50px auto;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #f9f9f9;
    }

    label {
      display: block;
      margin-top: 10px;
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
    }

    button {
      margin-top: 15px;
      padding: 10px;
      background-color: #28a745;
      border: none;
      color: white;
      width: 100%;
      cursor: pointer;
    }

    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

  <form action="../php/actualizar_porton.php" method="POST">
    <h2>Editar Portón</h2>

    <input type="hidden" name="id" value="<?= $porton['id'] ?>">

    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($porton['nombre']) ?>" required>

    <label for="ubicacion">Ubicación:</label>
    <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($porton['ubicacion']) ?>" required>

    <label for="estado">Estado:</label>
    <select id="estado" name="estado" required>
      <option value="abierto" <?= $porton['estado'] === 'abierto' ? 'selected' : '' ?>>Abierto</option>
      <option value="cerrado" <?= $porton['estado'] === 'cerrado' ? 'selected' : '' ?>>Cerrado</option>
    </select>

    <button type="submit">Guardar Cambios</button>
  </form>

</body>
</html>
