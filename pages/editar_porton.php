<?php
require_once '../php/conexion.php';

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = intval($_GET['id']);

// Obtener datos actuales del portón
$sql = "SELECT * FROM portones WHERE id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows === 0) {
    die("Portón no encontrado.");
}

$porton = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Portón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebard.css">
    <style>
        :root {
            --primary-color: #7c3aed;
            --primary-dark: #5b21b6;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --danger-color: #dc3545;
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
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
            transition: var(--transition);
        }

        .form-container {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 30px;
            max-width: 500px;
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

        h2 {
            color: var(--primary-dark);
            margin: 0;
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
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background-color: var(--light-gray);
            transition: var(--transition);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
            background-color: white;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 15px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: #7c3aed;
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            background-color: #7c3aed;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
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
                <h2><i class="fas fa-door-open"></i> Editar Portón</h2>
                <a href="portones.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <form action="../php/actualizar_porton.php" method="POST">
                <input type="hidden" name="id" value="<?= $porton['id'] ?>">

                <div class="form-group">
                    <label for="nombre"><i class="fas fa-signature"></i> Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($porton['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="ubicacion"><i class="fas fa-map-marker-alt"></i> Ubicación:</label>
                    <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($porton['ubicacion']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="estado"><i class="fas fa-power-off"></i> Estado:</label>
                    <select id="estado" name="estado" required>
                        <option value="abierto" <?= $porton['estado'] === 'abierto' ? 'selected' : '' ?>>Abierto</option>
                        <option value="cerrado" <?= $porton['estado'] === 'cerrado' ? 'selected' : '' ?>>Cerrado</option>
                    </select>
                </div>

                <div class="action-buttons">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>

            <div style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                <form action="../php/eliminar_porton.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este portón? Esta acción no se puede deshacer.');">
                    <input type="hidden" name="id" value="<?= $porton['id'] ?>">
                    <button type="submit" class="btn btn-danger" style="width: 100%;">
                        <i class="fas fa-trash-alt"></i> Eliminar Portón
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script src="../js/sidebaropen.js"></script>
</body>
</html>