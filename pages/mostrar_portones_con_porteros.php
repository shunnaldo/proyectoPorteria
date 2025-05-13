<?php
require '../php/conexion.php';

// Consulta para obtener los portones con los porteros asignados
$query = "
    SELECT 
        p.id AS porton_id, 
        p.nombre AS porton_nombre, 
        p.ubicacion,
        u.id AS portero_id, 
        u.nombre AS portero_nombre, 
        u.apellido AS portero_apellido,
        up.id AS asignacion_id
    FROM 
        portones p
    LEFT JOIN 
        usuario_porton up ON p.id = up.porton_id
    LEFT JOIN 
        usuarios u ON up.usuario_id = u.id
    ORDER BY 
        p.nombre ASC
";

// Ejecutar la consulta
$result = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portones con Porteros Asignados</title>
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
            margin-left: 280px;
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

        h1 {
            color: var(--primary-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
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

        .badge-secondary {
            background-color: #e2e8f0;
            color: #475569;
        }

        .action-link {
            padding: 6px 10px;
            border-radius: 6px;
            transition: var(--transition);
            text-decoration: none;
            font-weight: 500;
            margin: 0 4px;
            white-space: nowrap;
        }

        .action-link.danger {
            color: var(--danger-color);
            background-color: rgba(220, 53, 69, 0.1);
        }

        .action-link.danger:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #64748b;
        }

        .portero-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .portero-item:last-child {
            border-bottom: none;
        }

        .portero-info {
            display: flex;
            align-items: center;
            gap: 8px;
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
                <h1><i class="fas fa-door-closed"></i> Portones con Porteros Asignados</h1>
                <a href="asignar_portero.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Asignar Portero
                </a>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Portón</th>
                            <th>Ubicación</th>
                            <th>Porteros Asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentPortonId = null;
                        $currentPortonNombre = null;
                        $currentPortonUbicacion = null;
                        $porteros = [];
                        
                        while ($row = $result->fetch_assoc()): 
                            if ($currentPortonId !== $row['porton_id'] && $currentPortonId !== null): ?>
                                <tr>
                                    <td><?= htmlspecialchars($currentPortonNombre) ?></td>
                                    <td><?= htmlspecialchars($currentPortonUbicacion) ?></td>
                                    <td>
                                        <?php if (!empty($porteros)): ?>
                                            <?php foreach ($porteros as $portero): ?>
                                                <div class="portero-item">
                                                    <div class="portero-info">
                                                        <i class="fas fa-user-shield"></i>
                                                        <span><?= htmlspecialchars($portero['nombre']) ?></span>
                                                    </div>
                                                    <a href="../php/desvincular_portero.php?asignacion_id=<?= $portero['asignacion_id'] ?>" 
                                                       class="action-link danger"
                                                       onclick="return confirm('¿Estás seguro de desvincular este portero?');">
                                                        <i class="fas fa-unlink"></i> Desvincular
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Sin asignar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="asignar_portero.php?porton_id=<?= $currentPortonId ?>" class="action-link">
                                            <i class="fas fa-user-plus"></i> Asignar
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                            endif;
                            
                            if ($currentPortonId !== $row['porton_id']) {
                                $currentPortonId = $row['porton_id'];
                                $currentPortonNombre = $row['porton_nombre'];
                                $currentPortonUbicacion = $row['ubicacion'];
                                $porteros = [];
                            }
                            
                            if ($row['portero_id']) {
                                $porteros[] = [
                                    'nombre' => $row['portero_nombre'] . ' ' . $row['portero_apellido'],
                                    'asignacion_id' => $row['asignacion_id']
                                ];
                            }
                        endwhile;
                        
                        // Mostrar el último portón
                        if ($currentPortonId !== null): ?>
                            <tr>
                                <td><?= htmlspecialchars($currentPortonNombre) ?></td>
                                <td><?= htmlspecialchars($currentPortonUbicacion) ?></td>
                                <td>
                                    <?php if (!empty($porteros)): ?>
                                        <?php foreach ($porteros as $portero): ?>
                                            <div class="portero-item">
                                                <div class="portero-info">
                                                    <i class="fas fa-user-shield"></i>
                                                    <span><?= htmlspecialchars($portero['nombre']) ?></span>
                                                </div>
                                                <a href="../php/desvincular_portero.php?asignacion_id=<?= $portero['asignacion_id'] ?>" 
                                                   class="action-link danger"
                                                   onclick="return confirm('¿Estás seguro de desvincular este portero?');">
                                                    <i class="fas fa-unlink"></i> Desvincular
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="asignar_portero.php?porton_id=<?= $currentPortonId ?>" class="action-link">
                                        <i class="fas fa-user-plus"></i> Asignar
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No hay portones con porteros asignados</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="../js/sidebaropen.js"></script>
</body>
</html>