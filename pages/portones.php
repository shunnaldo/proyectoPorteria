<?php
require_once '../php/conexion.php';

$sql = "SELECT * FROM portones";
$resultado = $conexion->query($sql);

$portones = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $portones[] = $fila;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Portones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebard.css">
    <style>
        :root {
            --primary-color: #7c3aed;
            --primary-dark: #5b21b6;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --success-color: #28a745;
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

        .container {
            background-color: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin: 20px auto;
            max-width: 1200px;
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

        .btn {
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(124, 58, 237, 0.05);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .action-link {
            padding: 6px 10px;
            border-radius: 6px;
            transition: var(--transition);
            text-decoration: none;
            font-weight: 500;
            margin: 0 4px;
        }

        .action-link.edit {
            color: var(--primary-color);
            background-color: rgba(124, 58, 237, 0.1);
        }

        .action-link.edit:hover {
            background-color: rgba(124, 58, 237, 0.2);
        }

        .action-link.delete {
            color: var(--danger-color);
            background-color: rgba(220, 53, 69, 0.1);
        }

        .action-link.delete:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #64748b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding-top: 70px;
            }
            
            table {
                display: block;
                overflow-x: auto;
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
        <div class="container">
            <div class="page-header">
                <h2><i class="fas fa-door-closed"></i> Listado de Portones</h2>
                <a href="crear_porton.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Portón
                </a>
            </div>

            <?php if (count($portones) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($portones as $porton): ?>
                            <tr>

                                <td><?= htmlspecialchars($porton['nombre']) ?></td>
                                <td><?= htmlspecialchars($porton['ubicacion']) ?></td>
                                <td>
                                    <span class="badge <?= $porton['estado'] == 'abierto' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= ucfirst(htmlspecialchars($porton['estado'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="editar_porton.php?id=<?= $porton['id'] ?>" class="action-link edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="../php/eliminar_porton.php?id=<?= $porton['id'] ?>" 
                                       class="action-link delete"
                                       onclick="return confirm('¿Estás seguro de eliminar este portón?');">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No hay portones registrados</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="../js/sidebaropen.js"></script>
</body>
</html>