<?php
require '../php/conexion.php';
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

// Obtener asignaciones owner-portón
$sql = "SELECT 
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
        ORDER BY u.nombre, p.nombre";

$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Asignaciones Owner - Portón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/mostrarporteroconportones.css">
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

            <?php if ($result && $result->num_rows > 0): ?>
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
                        <?php
                        $ultimo_owner = null;
                        while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= ($ultimo_owner !== $row['owner_id']) ? htmlspecialchars($row['owner_nombre']) : '' ?></td>
                            <td><?= htmlspecialchars($row['porton_nombre']) ?></td>
                            <td><?= htmlspecialchars($row['ubicacion']) ?></td>
                            <td><?= htmlspecialchars($row['estado']) ?></td>
                            <td>
                                <form method="POST" action="../php/desvincular_owner_action.php" onsubmit="return confirm('¿Seguro que quieres desvincular este owner del portón?');">
                                    <input type="hidden" name="asignacion_id" value="<?= $row['asignacion_id'] ?>">
                                    <button type="submit" class="btn btn-danger" title="Desvincular">
                                        <i class="fas fa-unlink"></i> Desvincular
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                        $ultimo_owner = $row['owner_id'];
                        endwhile;
                        ?>
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
