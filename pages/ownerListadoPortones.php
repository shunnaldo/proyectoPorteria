<?php
require '../php/conexion.php';
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

// Obtener asignaciones owner-portón
$sql = "
    SELECT 
        up.id AS asignacion_id,
        u.id AS owner_id,
        u.nombre AS owner_nombre,
        p.id AS porton_id,
        p.nombre AS porton_nombre,
        p.ubicacion,
        p.estado
    FROM usuario_porton up
    INNER JOIN usuarios u ON up.usuario_id = u.id
    INNER JOIN portones p ON up.porton_id = p.id
    WHERE u.rol = 'owner'
    ORDER BY u.nombre, p.nombre
";

$resultado = $conexion->query($sql);

// Agrupar datos por owner
$asignaciones = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $ownerId = $fila['owner_id'];
        if (!isset($asignaciones[$ownerId])) {
            $asignaciones[$ownerId] = [
                'nombre' => $fila['owner_nombre'],
                'portones' => []
            ];
        }

        $asignaciones[$ownerId]['portones'][] = [
            'asignacion_id' => $fila['asignacion_id'],
            'nombre' => $fila['porton_nombre'],
            'ubicacion' => $fila['ubicacion'],
            'estado' => $fila['estado']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Asignaciones Owner - Portón</title>
    <link rel="stylesheet" href="../css/mostrarporteroconportones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    <div class="form-container">
        <div class="page-header">
            <h1><i class="fas fa-list"></i> Asignaciones Owner - Portón</h1>
            <a href="ownerPortones.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Asignación
            </a>
        </div>

        <?php if (!empty($asignaciones)): ?>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Owner</th>
                        <th>Portón</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asignaciones as $owner): ?>
                        <?php foreach ($owner['portones'] as $index => $porton): ?>
                            <tr>
                                <td><?= $index === 0 ? htmlspecialchars($owner['nombre']) : '' ?></td>
                                <td><?= htmlspecialchars($porton['nombre']) ?></td>
                                <td><?= htmlspecialchars($porton['ubicacion']) ?></td>
                                <td><?= htmlspecialchars($porton['estado']) ?></td>
                                <td>
                                    <form method="POST" action="../php/desvincular_owner_action.php" onsubmit="return confirm('¿Seguro que quieres desvincular este owner del portón?');">
                                        <input type="hidden" name="asignacion_id" value="<?= $porton['asignacion_id'] ?>">
                                        <button type="submit" class="btn btn-danger" title="Desvincular">
                                            <i class="fas fa-unlink"></i> Desvincular
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay asignaciones registradas.</p>
        <?php endif; ?>
    </div>
</main>

<script src="../js/sidebaropen.js"></script>
</body>
</html>
