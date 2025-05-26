<?php
require_once '../php/obtener_usuario.php';

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../css/editar_usuario.css">
</head>
<body>
    <!-- Incluir el sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="edit-container">
            <h2><i class="fas fa-user-edit"></i> Editar Usuario</h2>

            <form method="POST" action="../php/actualizar_usuario.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo_electronico" value="<?= htmlspecialchars($usuario['correo_electronico']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" required>
                        <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <option value="portero" <?= $usuario['rol'] === 'portero' ? 'selected' : '' ?>>Portero</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="contrasena">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                    <div style="position: relative;">
                        <input type="password" id="contrasena" name="contrasena">
                        <i class="fas fa-eye password-toggle" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8;"></i>
                    </div>
                </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
                <a href="ver_usuarios.php" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
            </form>
        </div>
    </main>

    <script src="../js/sidebaropen.js"></script>
    <script>
        // Toggle para mostrar/ocultar contraseña
        document.querySelector('.password-toggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('contrasena');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            this.style.color = type === 'text' ? 'var(--primary-color)' : '#94a3b8';
        });
    </script>
</body>
</html>