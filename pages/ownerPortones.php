<?php
require '../php/conexion.php';
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

// Obtener los owners
$owners_result = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol = 'owner'");

// Obtener los portones
$portones_result = $conexion->query("SELECT id, nombre FROM portones");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Owner a Portón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/asignarportero.css"> <!-- Reutiliza el mismo CSS -->
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="form-container">
            <div class="page-header">
                <h1><i class="fas fa-user-tie"></i> Asignar Owner a Portón</h1>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert success">✅ Owner asignado correctamente al portón.</div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'existente'): ?>
                    <div class="alert error">⚠️ El owner ya está asignado a ese portón.</div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'sql'): ?>
                    <div class="alert error">❌ Error al asignar el owner. Intenta nuevamente.</div>
                <?php endif; ?>

                <a href="ownerListadoPortones.php" class="btn btn-secondary">
                    <i class="fas fa-door-open"></i> Ver Asignaciones
                </a>
            </div>

            <form action="../php/asignar_owner_action.php" method="POST">
                <div class="form-group">
                    <label for="usuario_id"><i class="fas fa-user"></i> Seleccionar Owner:</label>
                    <select name="usuario_id" id="usuario_id" required>
                        <?php while ($row = $owners_result->fetch_assoc()): ?>
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

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-link"></i> Asignar Owner
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/sidebaropen.js"></script>
</body>
</html>
