<?php

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Portón</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebard.css">
    <link rel="stylesheet" href="../css/crearporton.css">
</head>
<body>
    <!-- Incluir el sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Contenido principal -->
  <!-- Contenido principal -->
<main class="main-content">

    <!-- Botón externo a la tarjeta -->
    <div style="max-width: 600px; margin: 0 auto 20px auto; text-align: right;">
        <a href="portones.php" class="btn-ver-portones">
            <i class="fas fa-list"></i> Ver Portones
        </a>
    </div>

    <div class="form-container">
        <h2><i class="fas fa-door-open"></i> Crear nuevo Portón</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <span>Portón creado correctamente</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="../php/crear_porton.php">
            <div class="form-group">
                <label for="nombre">Nombre del portón:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" id="ubicacion" name="ubicacion" required>
            </div>

            <div class="form-group">
                <label for="estado">Estado inicial:</label>
                <select id="estado" name="estado">
                    <option value="abierto">Abierto</option>
                    <option value="cerrado" selected>Cerrado</option>
                </select>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-plus-circle"></i> Crear Portón
            </button>
        </form>
    </div>
</main>


    <script src="../js/sidebaropen.js"></script>
</body>
</html>