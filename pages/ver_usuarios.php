<?php
require_once '../php/conexion.php';

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

$sql = "SELECT id, nombre, correo_electronico, rol FROM usuarios";
$resultado = $conexion->query($sql);

$usuarios = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios Registrados</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/sidebard.css">
  <link rel="stylesheet" href="../css/ver_usuarios.css">
</head>
<body>
  <!-- Incluir el sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Contenido principal -->
  <main class="main-content">
    <div class="container">
      <h2>Listado de Cuentas</h2>

      <?php if (count($usuarios) > 0): ?>
        <table>
          <thead>
            <tr>
       
              <th>Nombre</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($usuarios as $usuario): ?>
              <tr>

                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['correo_electronico']) ?></td>
                <td>
                  <span class="badge">
                    <?= htmlspecialchars($usuario['rol']) ?>
                  </span>
                </td>
                <td>
                  <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn">
                    <i class="fas fa-edit"></i> Modificar
                  </a>
                  <a href="../php/eliminar_usuario.php?id=<?= $usuario['id'] ?>" 
                    class="btn" 
                    style="background-color: #dc2626; margin-left: 8px;"
                    onclick="return confirm('¿Estás seguro que quieres eliminar este usuario?');">
                    <i class="fas fa-trash-alt"></i> Eliminar
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="no-data">No hay usuarios registrados.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="../js/sidebaropen.js"></script>
</body>
</html>