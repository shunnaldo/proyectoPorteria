<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/registrotrabajador.css">
</head>
    <div class="register-container">
        <div class="register-header">
            <h1>Crear Nueva Cuenta</h1>
            <p>Completa el formulario para registrarte</p>
        </div>
        
        <form action="../php/registrar_usuario.php" method="POST" class="register-form" id="registerForm">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <div class="input-field">
                    <i class="fas fa-user left-icon"></i>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingresa tu nombre" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <div class="input-field">
                    <i class="fas fa-user left-icon"></i>
                    <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Ingresa tu apellido" required>
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
                <div class="input-field">
                    <i class="fas fa-lock left-icon"></i>
                    <input type="password" id="password" name="contrasena" class="form-control password-field" placeholder="••••••••" required>
                    <i class="fas fa-eye right-icon" id="togglePassword"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="rol">Rol de usuario</label>
                <select id="rol" name="rol" class="form-control" required>
                    <option value="" disabled selected>Selecciona un rol</option>
                    <option value="admin">Administrador</option>
                    <option value="portero">Portero</option>
                </select>
            </div>
            
            <button type="submit" class="btn" id="registerBtn">Registrarse</button>
            

        </form>
    </div>

    <script>
        // Mostrar/ocultar contraseña
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            // Cambiar el tipo de input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Cambiar el icono
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
            
            // Cambiar el color cuando está activo
            if (type === 'text') {
                this.style.color = 'var(--primary-color)';
            } else {
                this.style.color = '#94a3b8';
            }
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
</html>