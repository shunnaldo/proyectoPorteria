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

    <link rel="stylesheet" href="../css/mostrarporteroconportones.css">
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