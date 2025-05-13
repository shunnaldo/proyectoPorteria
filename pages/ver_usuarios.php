<?php
require_once '../php/conexion.php';

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
  <style>
    :root {
      --primary-color: #7c3aed;
      --primary-dark: #5b21b6;
      --text-color: #334155;
      --light-gray: #f8fafc;
      --border-color: #e2e8f0;
      --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background-color: #f1f5f9;
      color: var(--text-color);
      margin: 0;
      padding: 0;
    }

    .main-content {
      margin-left: 280px; /* Ajuste para el sidebar */
      padding: 20px;
      min-height: 100vh;
      transition: all 0.3s ease;
    }

    .container {
      background-color: white;
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 25px;
      margin: 20px auto;
      max-width: 1200px;
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: var(--primary-dark);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid var(--border-color);
    }

    th {
      background-color: var(--primary-color);
      color: white;
      font-weight: 600;
    }

    tr:hover {
      background-color: rgba(124, 58, 237, 0.05);
    }

    .btn {
      display: inline-block;
      padding: 8px 16px;
      background-color: var(--primary-color);
      color: white;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .no-data {
      text-align: center;
      padding: 20px;
      color: #64748b;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding-top: 70px;
      }
      
      table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
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