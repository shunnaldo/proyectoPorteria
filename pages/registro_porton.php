<?php
session_start();
require '../php/conexion.php';

if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== 'portero') {
    header("Location: logintrabajador.php");
    exit;
}

$usuario_id = $_SESSION["usuario"]["id"];
$nombre_usuario = $_SESSION["usuario"]["nombre"];
$porton_id = $_GET["porton_id"] ?? null;

if (!$porton_id) {
    echo "ID de portón no válido";
    exit;
}

// Obtener nombre del portón
$stmt2 = $conexion->prepare("SELECT nombre, estado FROM portones WHERE id = ?");
$stmt2->bind_param("i", $porton_id);
$stmt2->execute();
$result = $stmt2->get_result();
$porton = $result->fetch_assoc();
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso al Portón</title>
    <link rel="stylesheet" href="../css/portonesvisual.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Toolbar Superior -->
    <header class="app-toolbar">
        <div class="user-info">
            <div class="avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-details">
                <span class="username"><?= htmlspecialchars($nombre_usuario) ?></span>
                <span class="user-role">Portero</span>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="porton-header">
            <a href="portero_portones.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1><i class="fas fa-door-closed"></i> <?= htmlspecialchars($porton['nombre']) ?></h1>
            <span class="status-indicator <?= $porton['estado'] === 'abierto' ? 'open' : 'closed' ?>">
                <?= ucfirst($porton['estado']) ?>
            </span>

        </div>

        <form action="escanearQr.php" method="GET">
            <input type="hidden" name="porton_id" value="<?= htmlspecialchars($porton_id) ?>">
            <button type="submit">Escanear QR</button>
        </form>



    </main>
</body>
</html>