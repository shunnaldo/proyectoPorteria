<?php
require '../php/conexion.php';

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

// Consulta de portones con porteros
$query = "
    SELECT 
        p.id AS porton_id, 
        p.nombre AS porton_nombre, 
        p.ubicacion,
        u.id AS portero_id, 
        u.nombre AS portero_nombre, 
        up.id AS asignacion_id
    FROM 
        portones p
    LEFT JOIN 
        usuario_porton up ON p.id = up.porton_id
    LEFT JOIN 
        usuarios u ON up.usuario_id = u.id
    WHERE 
        u.rol = 'portero' OR u.id IS NULL
    ORDER BY 
        p.nombre ASC
";

$result = $conexion->query($query);

// Preparamos los datos agrupados por portón
$portones = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $portonId = $row['porton_id'];

        if (!isset($portones[$portonId])) {
            $portones[$portonId] = [
                'nombre' => $row['porton_nombre'] ?? 'Sin nombre',
                'ubicacion' => $row['ubicacion'] ?? 'Sin ubicación',
                'porteros' => []
            ];
        }

        if (!empty($row['portero_id'])) {
            $portones[$portonId]['porteros'][] = [
                'nombre' => $row['portero_nombre'],
                'asignacion_id' => $row['asignacion_id']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portones con Porteros</title>
    <link rel="stylesheet" href="../css/sidebard.css">
    <link rel="stylesheet" href="../css/mostrarporteroconportones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-door-closed"></i> Portones con Porteros Asignados</h1>
            <a href="asignar_portero.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Asignar Portero
            </a>
        </div>

        <?php if (!empty($portones)): ?>
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
                    <?php foreach ($portones as $portonId => $porton): ?>
                        <tr>
                            <td><?= htmlspecialchars($porton['nombre']) ?></td>
                            <td><?= htmlspecialchars($porton['ubicacion']) ?></td>
                            <td>
                                <?php if (!empty($porton['porteros'])): ?>
                                    <?php foreach ($porton['porteros'] as $portero): ?>
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
                                <a href="asignar_portero.php?porton_id=<?= $portonId ?>" class="action-link">
                                    <i class="fas fa-user-plus"></i> Asignar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No hay portones registrados aún.</p>
        <?php endif; ?>
    </div>
</main>

<script src="../js/sidebaropen.js"></script>
</body>
</html>
