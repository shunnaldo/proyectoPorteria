<?php
session_start();
require '../php/conexion.php';

// Verificamos que el usuario esté logueado y sea portero
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== 'portero') {
    header("Location: logintrabajador.php");
    exit;
}

$usuario_id = $_SESSION["usuario"]["id"];

// Consultamos los portones asignados al portero
$stmt = $conexion->prepare("
    SELECT p.id, p.nombre, p.estado
    FROM portones p
    INNER JOIN usuario_porton up ON p.id = up.porton_id
    WHERE up.usuario_id = ?
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$portones = [];
while ($row = $resultado->fetch_assoc()) {
    $portones[] = $row;
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Portones</title>
    <link rel="stylesheet" href="../css/portones_portero.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-door-open"></i> Portones asignados</h1>
        </header>

        <main>
            <?php if (empty($portones)): ?>
                <div class="empty-state">
                    <i class="fas fa-door-closed"></i>
                    <p>No tienes portones asignados todavía.</p>
                </div>
            <?php else: ?>
                <div class="portones-grid">
                    <?php foreach ($portones as $porton): ?>
                        <div class="porton-card">
                            <div class="porton-info">
                                <h3><?= htmlspecialchars($porton['nombre']) ?></h3>
                                <span class="status-badge <?= $porton['estado'] == 'abierto' ? 'open' : 'closed' ?>">
                                    <?= ucfirst($porton['estado']) ?>
                                </span>
                            </div>
                            <a href="escanearQR.php?porton_id=<?= $porton['id'] ?>" class="btn-entrar">Registra entrada</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <?php include 'botom-nav.php'; ?>
    </div>
</body>
</html>