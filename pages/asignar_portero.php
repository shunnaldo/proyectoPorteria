<?php
require '../php/conexion.php'; // Conectar a la base de datos
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
// Obtener los porteros disponibles
$porteros_result = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol = 'portero'");

// Obtener los portones disponibles
$portones_result = $conexion->query("SELECT id, nombre FROM portones");
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Portero a Portón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/asignarportero.css">
</head>
<body>
    <!-- Incluir el sidebar -->
    <?php include 'sidebar.php'; ?>


    <!-- Contenido principal -->
    <main class="main-content">
        <div class="form-container">
            <div class="page-header">
                <h1><i class="fas fa-user-shield"></i> Asignar Portero a Portón</h1>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert success">✅ Portero asignado correctamente al portón.</div>
                    <?php elseif (isset($_GET['error']) && $_GET['error'] === 'existente'): ?>
                        <div class="alert error">⚠️ El portero ya está asignado a ese portón.</div>
                    <?php elseif (isset($_GET['error']) && $_GET['error'] === 'sql'): ?>
                        <div class="alert error">❌ Error al asignar el portero. Intenta nuevamente.</div>
                    <?php endif; ?>

                <a href="mostrar_portones_con_porteros.php" class="btn btn-secondary">
                    <i class="fas fa-door-open"></i> Ver Portones
                </a>
            </div>

            <form action="../php/asignar_portero_action.php" method="POST">
                <div class="form-group">
                    <label for="usuario_id"><i class="fas fa-user"></i> Seleccionar Portero:</label>
                    <select name="usuario_id" id="usuario_id" required>
                        <?php while ($row = $porteros_result->fetch_assoc()): ?>
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
                    <a href="mostrar_portones_con_porteros.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-link"></i> Asignar Portero
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/sidebaropen.js"></script>
</body>
</html>