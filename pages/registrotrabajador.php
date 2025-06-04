<?php
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
    <title>Registro de Trabajador</title>
    <!-- Fuentes de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="../css/registrotrabajador.css">

</head>
<body>
    <!-- Incluimos el sidebar completo -->
    <?php include 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="register-container">
            <div class="register-header">
                <h1>Crear Nueva Cuenta</h1>
                <p>Completa el formulario para registrarte</p>
            </div>
            
            <form action="../php/registrar_usuario.php" method="POST" class="register-form" id="registerForm">
                
                <!-- Mostrar mensaje de error si existe -->
                <?php if (isset($_GET['mensaje'])): ?>
                    <div class="form-group mensaje">
                        <?php
                        switch ($_GET['mensaje']) {
                            case 'success':
                                echo "<div class='mensaje exito'><i class='fas fa-check-circle'></i> ¡Usuario registrado exitosamente!</div>";
                                break;
                            case 'correo_duplicado':
                                echo "<div class='mensaje error'><i class='fas fa-exclamation-circle'></i> El correo electrónico ya está registrado.</div>";
                                break;
                            case 'fallo_preparacion':
                                echo "<div class='mensaje error'><i class='fas fa-bug'></i> Error al preparar la consulta.</div>";
                                break;
                            default:
                                echo "<div class='mensaje error'><i class='fas fa-times-circle'></i> Ocurrió un error inesperado.</div>";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Campo de Alias -->
                <div class="form-group">
                    <label for="alias">Cargo</label>
                    <div class="input-field">
                        <i class="fas fa-user left-icon"></i>
                        <input type="text" id="alias" name="alias" class="form-control" placeholder="Ingresa tu cargo" required>
                    </div>
                </div>
                
                <!-- Resto de los campos -->
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <div class="input-field">
                        <i class="fas fa-user left-icon"></i>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingresa tu nombre completo" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-field">
                        <i class="fas fa-envelope left-icon"></i>
                        <input type="email" id="email" name="correo_electronico" class="form-control" placeholder="tucorreo@ejemplo.com" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-field password-container">
                        <i class="fas fa-lock left-icon"></i>
                        <input type="password" id="password" name="contrasena" class="form-control password-field" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="rol">Rol de usuario</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="" disabled selected>Selecciona un rol</option>
                        <option value="admin">Administrador</option>
                        <option value="portero">Portero</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                
                <button type="submit" class="btn" id="registerBtn">Registrarse</button>
            </form>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../js/sidebaropen.js"></script>
    <script>
            document.getElementById('togglePassword').addEventListener('click', function() {
                const icon = this.querySelector('i');
                const password = document.getElementById('password');
                
                // Cambiar el tipo de input
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Cambiar el icono
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
                
                // Cambiar el color
                icon.style.color = type === 'text' ? '#1A1A1A' : '#5A5A5A';
            });

            // Animación de carga al enviar el formulario
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                const btn = document.getElementById('registerBtn');
                btn.classList.add('loading');
                btn.disabled = true;
                
                // Simulamos un retraso para mostrar la animación (quitar en producción)
                setTimeout(() => {
                    btn.classList.remove('loading');
                    btn.disabled = false;
                }, 2000);
            });
    </script>
</body>
</html>
