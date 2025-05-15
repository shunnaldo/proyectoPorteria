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
    <link rel="stylesheet" href="../css/editar_porton.css">
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