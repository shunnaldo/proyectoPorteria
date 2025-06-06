<?php
// Obtener el nombre de la página actual
$current_page = basename($_SERVER['PHP_SELF']);

// Función para verificar si el enlace está activo
function isActive($page, $current) {
    return $page === $current ? 'active' : '';
}

require '../php/conexion.php';

// Consulta el número de tickets finalizados no vistos
$result = $conexion->query("SELECT COUNT(*) AS finalizados FROM tickets WHERE estado = 'finalizado' AND visto = 0");
$finalizados = ($row = $result->fetch_assoc()) ? intval($row['finalizados']) : 0;

// Si estamos en la página de tickets, marcamos todos como vistos
if ($current_page === 'tiketsUsuario.php') {
    $conexion->query("UPDATE tickets SET visto = 1 WHERE estado = 'finalizado'");
    $finalizados = 0; // Actualizamos el contador a 0
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebard.css">
    <style>
        .badge-pendientes {
            display: inline-flex;
            align-items: center;
            margin-left: auto;
            background: #28a745; /* Cambiado a verde para tickets finalizados */
            color: white;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 0.75rem;
            transition: all 0.3s ease;
        }

        .badge-pendientes .fa-check-circle {
            margin-right: 4px;
        }

        .badge-pendientes .contador {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Mobile Header -->
    <header class="mobile-header">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h3><i class="fas fa-shield-alt"></i> Panel Admin</h3>
    </header>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-shield-alt"></i> <span>Panel Admin</span></h3>
            <i class="fas fa-bars" id="closeSidebar"></i>
        </div>

        <div class="sidebar-menu">
            <div class="menu-title">Gestión</div>

            <a href="ownerVista.php" class="menu-item <?php echo isActive('ownerVista.php', $current_page); ?>">
                <i class="fas fa-clipboard-list"></i>
                <span class="menu-text">Registros</span>
            </a>

            <div class="menu-title">Configuración</div>

            <a href="tiketsUsuario.php" class="menu-item <?php echo isActive('tiketsUsuario.php', $current_page); ?>">
                <i class="fas fa-ticket-alt"></i>
                <span class="menu-text">Tickets</span>
                <?php if ($finalizados > 0): ?>
                    <span class="badge-pendientes" title="Tickets finalizados pendientes de revisión">
                        <i class="fas fa-check-circle"></i>
                        <span class="contador"><?= $finalizados ?></span>
                    </span>
                <?php endif; ?>
            </a>

            <a href="../php/logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-text">Cerrar sesión</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">OW</div>
                <div class="user-info">
                    <h4>Owner</h4>
                </div>
            </div>
        </div>
    </aside>

    <script src="../js/sidebaropen.js"></script>
</body>
</html>