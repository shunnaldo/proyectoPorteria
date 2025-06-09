<?php
require_once '../php/conexion.php';
session_start();

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de usuario no especificado.");
}

$id = intval($_GET['id']);

// Manejar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alias = $_POST['alias'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $rut = $_POST['rut'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    // Validaciones básicas
    if (empty($alias) || empty($nombre) || empty($correo) || empty($rol)) {
        $error = "Por favor completa todos los campos obligatorios.";
    } elseif (!empty($nueva_contrasena) && ($nueva_contrasena !== $confirmar_contrasena)) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Preparar la consulta SQL según si se cambió la contraseña o no
        if (!empty($nueva_contrasena)) {
            // Hash de la nueva contraseña
            $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("UPDATE usuarios SET alias=?, nombre=?, correo_electronico=?, rut=?, fecha_nacimiento=?, rol=?, contrasena=? WHERE id=?");
            $stmt->bind_param("sssssssi", $alias, $nombre, $correo, $rut, $fecha_nacimiento, $rol, $hashed_password, $id);
        } else {
            $stmt = $conexion->prepare("UPDATE usuarios SET alias=?, nombre=?, correo_electronico=?, rut=?, fecha_nacimiento=?, rol=? WHERE id=?");
            $stmt->bind_param("ssssssi", $alias, $nombre, $correo, $rut, $fecha_nacimiento, $rol, $id);
        }
        
        if ($stmt->execute()) {
            header("Location: ver_usuarios.php?msg=Usuario actualizado correctamente");
            exit;
        } else {
            $error = "Error al actualizar usuario: " . $conexion->error;
        }
    }
}

// Consultar datos actuales del usuario
$stmt = $conexion->prepare("SELECT alias, nombre, correo_electronico, rut, fecha_nacimiento, rol FROM usuarios WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Usuario no encontrado.");
}
$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuario</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border: none;
    }
    .card-header {
      background-color: #212529;
      color: white;
      border-radius: 10px 10px 0 0 !important;
    }
    .btn-primary {
      background-color: #212529;
      border-color: #212529;
    }
    .btn-primary:hover {
      background-color: #343a40;
      border-color: #343a40;
    }
    .form-control:focus {
      border-color: #212529;
      box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
    }
    .material-icons {
      vertical-align: middle;
    }
    .password-section {
      background-color: #f1f1f1;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
    
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="material-icons me-2">edit</i>Editar Usuario</h3>
            <a href="ver_usuarios.php" class="btn btn-sm btn-outline-light">
              <i class="material-icons">arrow_back</i> Volver
            </a>
          </div>
          
          <div class="card-body">
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="material-icons me-2">error</i>
                <div><?= htmlspecialchars($error) ?></div>
              </div>
            <?php endif; ?>

            <form method="post" action="">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="alias" class="form-label">
                    <i class="material-icons me-1">badge</i> Alias (cargo)
                  </label>
                  <input type="text" class="form-control" id="alias" name="alias" 
                         value="<?= htmlspecialchars($usuario['alias']) ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label for="nombre" class="form-label">
                    <i class="material-icons me-1">person</i> Nombre completo
                  </label>
                  <input type="text" class="form-control" id="nombre" name="nombre" 
                         value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label for="correo" class="form-label">
                    <i class="material-icons me-1">email</i> Correo electrónico
                  </label>
                  <input type="email" class="form-control" id="correo" name="correo" 
                         value="<?= htmlspecialchars($usuario['correo_electronico']) ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label for="rut" class="form-label">
                    <i class="material-icons me-1">assignment_ind</i> RUT
                  </label>
                  <input type="text" class="form-control" id="rut" name="rut" 
                         value="<?= htmlspecialchars($usuario['rut']) ?>">
                </div>
                
                <div class="col-md-6">
                  <label for="fecha_nacimiento" class="form-label">
                    <i class="material-icons me-1">cake</i> Fecha de nacimiento
                  </label>
                  <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" 
                         value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>">
                </div>
                
                <div class="col-md-6">
                  <label for="rol" class="form-label">
                    <i class="material-icons me-1">admin_panel_settings</i> Rol
                  </label>
                  <select class="form-select" id="rol" name="rol" required>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="portero" <?= $usuario['rol'] === 'portero' ? 'selected' : '' ?>>Portero</option>
                    <option value="owner" <?= $usuario['rol'] === 'owner' ? 'selected' : '' ?>>Owner</option>
                  </select>
                </div>
              </div>

              <div class="password-section mt-4">
                <h5 class="mb-3"><i class="material-icons me-2">lock</i>Cambiar contraseña</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="nueva_contrasena" class="form-label">
                      <i class="material-icons me-1">vpn_key</i> Nueva contraseña
                    </label>
                    <input type="password" class="form-control" id="nueva_contrasena" name="nueva_contrasena">
                    <div class="form-text">Dejar en blanco para mantener la contraseña actual</div>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="confirmar_contrasena" class="form-label">
                      <i class="material-icons me-1">vpn_key</i> Confirmar contraseña
                    </label>
                    <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena">
                  </div>
                </div>
              </div>

              <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-primary">
                  <i class="material-icons me-1">save</i> Actualizar Usuario
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>