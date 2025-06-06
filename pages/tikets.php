<?php
require '../php/conexion.php';
session_start();

// Enhanced session verification
if (!isset($_SESSION["usuario"]) || !is_array($_SESSION["usuario"])) {
    header("Location: logintrabajador.php");
    exit;
}

// Verify required session fields exist
$required_fields = ['id', 'correo_electronico', 'rol'];
foreach ($required_fields as $field) {
    if (!isset($_SESSION["usuario"][$field])) {
        header("Location: logintrabajador.php");
        exit;
    }
}

// Verify user role
if (!in_array($_SESSION["usuario"]["rol"], ["owner", "portero"])) {
    header("Location: logintrabajador.php");
    exit;
}

$usuario = $_SESSION["usuario"];
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Ticket | Sistema de Portería</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4285f4;
            --success-color: #34a853;
            --warning-color: #fbbc05;
            --error-color: #ea4335;
            --dark-color: #202124;
            --light-color: #f8f9fa;
            --gray-color: #dadce0;
            --text-color: #3c4043;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }



        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h2 {
            color: var(--dark-color);
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--gray-color);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray-color);
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }

        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background-color: #3367d6;
        }

        .material-icons {
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            body {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebarOwner.php'; ?>
    <br><br>
    <div class="container">
        <h2><span class="material-icons">send</span> Enviar Ticket</h2>

        <?php if (isset($_GET['enviado']) && $_GET['enviado'] === 'ok'): ?>
            <div class="alert-success">
                <span class="material-icons">check_circle</span>
                <span>Ticket enviado correctamente.</span>
            </div>
        <?php endif; ?>

        <form action="../php/procesar_ticket.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario['id'] ?? '') ?>">
            <input type="hidden" name="correo" value="<?= htmlspecialchars($usuario['correo_electronico'] ?? '') ?>">

            <div class="form-group">
                <label for="categoria">Motivo del ticket:</label>
                <select name="categoria" id="categoria" required>
                    <option value="">-- Selecciona un motivo --</option>
                    <option value="blacklist">Blacklist</option>
                    <option value="error_sistema">Error en el sistema</option>
                    <option value="sugerencia">Sugerencia</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="motivo">Descripción detallada:</label>
                <textarea name="motivo" id="motivo" required placeholder="Describe el problema con el mayor detalle posible..."></textarea>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <a href="tiketsUsuario.php"
                    style="display: inline-flex; align-items: center; gap: 6px; background-color: transparent; color: var(--error-color); border: 2px solid var(--error-color); padding: 8px 16px; border-radius: 6px; font-weight: 500; text-decoration: none; transition: all 0.3s;">
                    <span class="material-icons">cancel</span>
                    Cancelar
                </a>

                <button type="submit"
                    style="display: inline-flex; align-items: center; gap: 6px; background-color: transparent; color: #000; border: 2px solid #000; padding: 8px 16px; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.backgroundColor='#000'; this.style.color='#fff';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#000';">
                    <span class="material-icons">send</span>
                    Enviar Ticket
                </button>

            </div>


        </form>
    </div>
</body>

</html>