<?php
require_once '../php/conexion.php';

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

$sql = "SELECT id, alias, nombre, correo_electronico, rol FROM usuarios";
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
  <link rel="stylesheet" href="../css/ver_usuarios.css">
</head>
<body>
  <!-- Incluir el sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Contenido principal -->
  <main class="main-content">
    <div class="container">
      <h1><i class="fas fa-users"></i> Listado de Usuarios</h1>

      <?php if (count($usuarios) > 0): ?>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Cargo</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $usuario): ?>
                <tr>
                  <td><?= htmlspecialchars($usuario['alias']) ?></td>
                  <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                  <td><?= htmlspecialchars($usuario['correo_electronico']) ?></td>
                  <td>
                    <span class="badge <?= $usuario['rol'] === 'admin' ? 'admin' : 'portero' ?>">
                      <?= htmlspecialchars($usuario['rol']) ?>
                    </span>
                  </td>
                  <td class="actions">
                    <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn edit">
                      <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="../php/eliminar_usuario.php?id=<?= $usuario['id'] ?>" 
                      class="btn delete"
                      onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                      <i class="fas fa-trash-alt"></i> Eliminar
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="no-data">
          <i class="fas fa-users-slash"></i>
          <p>No hay usuarios registrados</p>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script src="../js/sidebar.js"></script>
</body>
</html>