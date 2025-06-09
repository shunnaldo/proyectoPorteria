<?php
require_once '../php/conexion.php';

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
  header("Location: ../pages/logintrabajador.php");
  exit;
}

$sql = "SELECT id, alias, nombre, correo_electronico, rol, rut, fecha_nacimiento FROM usuarios";
$resultado = $conexion->query($sql);

$usuarios = [];

if ($resultado && $resultado->num_rows > 0) {
  while ($fila = $resultado->fetch_assoc()) {
    // Calcular edad si tiene fecha de nacimiento
    if (!empty($fila['fecha_nacimiento'])) {
      $fechaNacimiento = new DateTime($fila['fecha_nacimiento']);
      $hoy = new DateTime();
      $edad = $hoy->diff($fechaNacimiento)->y;
      $fila['edad'] = $edad;
    } else {
      $fila['edad'] = '—'; // No disponible
    }

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
  <style>
    .filter-container {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 20px;
    }

    .filter-container label {
      margin-right: 8px;
      font-weight: bold;
      align-self: center;
    }

    .form-control {
      padding: 8px;
      font-size: 1rem;
    }

    .badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-weight: bold;
      color: #fff;
      text-transform: capitalize;
    }

    .badge.admin {
      background-color: #007bff;
    }

    .badge.portero {
      background-color: #28a745;
    }

    .badge.owner {
      background-color: #6f42c1;
    }
  </style>
</head>

<body>
  <!-- Incluir el sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Contenido principal -->
  <main class="main-content">
    <div class="container">
      <h1><i class="fas fa-users"></i> Listado de Usuarios</h1>

      <!-- Filtro por rol alineado a la derecha -->
      <div class="filter-container">
        <label for="filtroRol">Filtrar por rol:</label>
        <select id="filtroRol" class="form-control">
          <option value="admin" selected>Administrador</option>
          <option value="portero">Portero</option>
          <option value="owner">Owner</option>
          <option value="todos">Mostrar todos</option>
        </select>
      </div>

      <?php if (count($usuarios) > 0): ?>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Cargo</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>RUT</th>
                <th>Fecha Nac.</th>
                <th>Edad</th>
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
                  <td><?= htmlspecialchars($usuario['rut'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($usuario['fecha_nacimiento'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($usuario['edad']) ?></td>
                  <td>
                    <span class="badge <?= htmlspecialchars($usuario['rol']) ?>">
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
  <script>
    document.getElementById('filtroRol').addEventListener('change', function() {
      const filtro = this.value;
      const filas = document.querySelectorAll('tbody tr');

      filas.forEach(fila => {
        const rol = fila.querySelector('td:nth-child(7) span').textContent.trim();
        if (filtro === 'todos' || rol === filtro) {
          fila.style.display = '';
        } else {
          fila.style.display = 'none';
        }
      });
    });

    // Aplicar filtro por defecto al cargar
    window.addEventListener('DOMContentLoaded', () => {
      document.getElementById('filtroRol').dispatchEvent(new Event('change'));
    });
  </script>
</body>

</html>