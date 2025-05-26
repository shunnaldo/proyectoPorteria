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
    <link rel="stylesheet" href="../css/portones.css">
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