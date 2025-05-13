<?php
require '../php/conexion.php'; // Conectar a la base de datos

// Obtener los porteros disponibles
$porteros_result = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol = 'portero'");

// Obtener los portones disponibles
$portones_result = $conexion->query("SELECT id, nombre FROM portones");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Portero a Portón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebard.css">
    <style>
        :root {
            --primary-color: #7c3aed;
            --primary-dark: #5b21b6;
            --success-color: #28a745;
            --info-color: #17a2b8;
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

        .form-container {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 30px;
            max-width: 600px;
            margin: 20px auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h1 {
            color: var(--primary-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            font-size: 1rem;
        }

        select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background-color: var(--light-gray);
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
        }

        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
            background-color: white;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 1rem;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background-color: var(--info-color);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #138496;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding-top: 70px;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Incluir el sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <main class="main-content">
        <div class="form-container">
            <div class="page-header">
                <h1><i class="fas fa-user-shield"></i> Asignar Portero a Portón</h1>
                <a href="mostrar_portones_con_porteros.php" class="btn btn-secondary">
                    <i class="fas fa-door-open"></i> Ver Portones
                </a>
            </div>

            <form action="../php/asignar_portero_action.php" method="POST">
                <div class="form-group">
                    <label for="usuario_id"><i class="fas fa-user"></i> Seleccionar Portero:</label>
                    <select name="usuario_id" id="usuario_id" required>
                        <?php while ($row = $porteros_result->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="porton_id"><i class="fas fa-door-closed"></i> Seleccionar Portón:</label>
                    <select name="porton_id" id="porton_id" required>
                        <?php while ($row = $portones_result->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="action-buttons">
                    <a href="mostrar_portones_con_porteros.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-link"></i> Asignar Portero
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/sidebaropen.js"></script>
</body>
</html>