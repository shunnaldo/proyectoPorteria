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
            --primary: #4285F4;
            --primary-dark: #3367D6;
            --success: #34A853;
            --error: #EA4335;
            --warning: #FBBC05;
            --gray-light: #F5F5F5;
            --gray-medium: #E0E0E0;
            --gray-dark: #757575;
            --text-primary: #212121;
            --text-secondary: #757575;
            --white: #FFFFFF;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--gray-light);
            color: var(--text-primary);
            line-height: 1.5;
            padding-bottom: 80px;
        }

        .container {
            max-width: 100%;
            padding: 16px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .header .icon {
            color: var(--primary);
            font-size: 28px;
        }

        .header h2 {
            font-size: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(52, 168, 83, 0.1);
            color: var(--success);
            padding: 12px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 0.875rem;
        }

        .alert-success .icon {
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            font-family: 'Roboto', sans-serif;
            font-size: 0.875rem;
            background-color: var(--white);
            transition: border 0.3s;
        }

        .form-group select:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.875rem;
            flex: 1;
            border: none;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--error);
            color: var(--error);
        }

        .btn-outline:hover {
            background-color: rgba(234, 67, 53, 0.1);
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
            border: 1px solid var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: var(--white);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 100;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-item .icon {
            font-size: 24px;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <span class="material-icons icon">send</span>
            <h2>Enviar Ticket</h2>
        </div>

        <?php if (isset($_GET['enviado']) && $_GET['enviado'] === 'ok'): ?>
            <div class="alert-success">
                <span class="material-icons">check_circle</span>
                <span>Ticket enviado correctamente.</span>
            </div>
        <?php endif; ?>

        <form action="../php/procesar_ticketPortero.php" method="POST">
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

            <div class="form-actions">
                <a href="tiketsUsuarioPortero.php" class="btn btn-outline">
                    <span class="material-icons">arrow_back</span>
                    Volver
                </a>

                <button type="submit" class="btn btn-primary">
                    <span class="material-icons">send</span>
                    Enviar
                </button>
            </div>
        </form>
    </div>

    <?php include 'botom-nav.php'; ?>

</body>

</html>