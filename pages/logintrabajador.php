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
        
        <!-- Contenedor para mensajes de error -->
        <div id="error-message" class="error-message" style="display: none;"></div>
        
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
            e.preventDefault(); // Prevenir el envío normal del formulario
            
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
            
            // Enviar datos mediante AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === "success_admin") {
                    window.location.href = "../pages/dashboard.php";
                } else if (data === "success_portero") {
                    window.location.href = "../pages/portero_portones.php";
                }  else if (data === "success_owner") {
                    window.location.href = "../pages/ownerVista.php";
                } else {
                    // Mostrar mensaje de error
                    const errorMessage = document.getElementById('error-message');
                    errorMessage.textContent = data;
                    errorMessage.style.display = 'block';
                    
                    // Ocultar mensaje después de 5 segundos
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 5000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                btn.classList.remove('loading');
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>