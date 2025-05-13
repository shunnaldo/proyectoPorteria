<?php
require_once '../php/obtener_usuario.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
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
            --transition: all 0.3s ease;
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
            transition: var(--transition);
        }

        .edit-container {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 30px;
            max-width: 600px;
            margin: 20px auto;
        }

        h2 {
            color: var(--primary-dark);
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background-color: var(--light-gray);
            transition: var(--transition);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
            background-color: white;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            flex: 1;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-secondary:hover {
            background-color: #f5f3ff;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding-top: 70px;
            }
            
            .edit-container {
                padding: 20px;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                    <a href="ver_usuarios.php" class="btn btn-secondary">
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