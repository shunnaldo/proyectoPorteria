<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/logintrabajador.css">
</head>
<body>
        <div class="login-container">
        <div class="login-header">
            <h1>Bienvenido de vuelta</h1>
            <p>Ingresa tus credenciales para continuar</p>
        </div>
        
        <form action="../php/login.php" method="POST" class="login-form" id="loginForm">
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
            
            <button type="submit" class="btn" id="loginBtn">Iniciar sesión</button>
            

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
        });

        // Animación de carga al enviar el formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
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