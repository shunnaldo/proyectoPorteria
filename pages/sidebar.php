<?php
// Obtener el nombre de la página actual
$current_page = basename($_SERVER['PHP_SELF']);

// Función para verificar si el enlace está activo
function isActive($page, $current)
{
    return $page === $current ? 'active' : '';
}

require '../php/conexion.php';

// Consulta el número de tickets en proceso
$result = $conexion->query("SELECT COUNT(*) AS pendientes FROM tickets WHERE estado = 'en_proceso'");
$pendientes = ($row = $result->fetch_assoc()) ? intval($row['pendientes']) : 0;
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
            background: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .badge-pendientes .fa-bell {
            margin-right: 2px;
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
            <div class="menu-title">Principal</div>
            <a href="dashboard.php" class="menu-item <?php echo isActive('dashboard.php', $current_page); ?>">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>

            <div class="menu-title">Gestión</div>
            <div class="menu-item has-dropdown <?php echo (in_array($current_page, ['ver_usuarios.php', 'registrotrabajador.php']) ? 'active' : ''); ?>" id="usersMenu">
                <i class="fas fa-users"></i>
                <span class="menu-text">Usuarios</span>
            </div>
            <div class="menu-dropdown" id="usersDropdown">
                <a href="ver_usuarios.php" class="menu-item <?php echo isActive('ver_usuarios.php', $current_page); ?>">
                    <i class="fas fa-circle-notch"></i>
                    <span class="menu-text">Todos los usuarios</span>
                </a>

                <a href="registrotrabajador.php" class="menu-item <?php echo isActive('registrotrabajador.php', $current_page); ?>">
                    <i class="fas fa-user-plus"></i>
                    <span class="menu-text">Agregar nuevo</span>
                </a>

                <a href="personas_Qr.php" class="menu-item <?php echo isActive('personas_Qr.php', $current_page); ?>">
                    <i class="fas fa-address-card"></i>
                    <span class="menu-text">Añadir Persona</span>
                </a>

                <a href="ownerPortones.php" class="menu-item <?php echo isActive('ownerPortones.php', $current_page); ?>">
                    <i class="fas fa-user-tag"></i>

                    <span class="menu-text">Asignar owner</span>
                </a>

                <a href="listado_bloqueados.php" class="menu-item <?php echo isActive('listado_bloqueados.php', $current_page); ?>">
                    <i class="fas fa-user-slash"></i>
                    <span class="menu-text">Black List</span>
                </a>

            </div>

            <a href="crear_porton.php" class="menu-item <?php echo isActive('crear_porton.php', $current_page); ?>">
                <i class="fas fa-building"></i>
                <span class="menu-text">Portones</span>
            </a>

            <a href="asignar_portero.php" class="menu-item <?php echo isActive('asignar_portero.php', $current_page); ?>">
                <i class="fas fa-user-tie"></i>
                <span class="menu-text">Porteros</span>
            </a>

            <a href="bitacoraAdmin.php" class="menu-item <?php echo isActive('bitacoraAdmin.php', $current_page); ?>">
                <i class="fas fa-clipboard-list"></i>
                <span class="menu-text">Registros</span>
            </a>


            <div class="menu-title">Configuración</div>

            <a href="ver_tikets.php" class="menu-item <?php echo isActive('ver_tikets.php', $current_page); ?>">
                <i class="fas fa-ticket-alt"></i>
                <span class="menu-text">Tickets</span>
                <?php if ($pendientes > 0): ?>
                    <span class="badge-pendientes">
                        <i class="fas fa-bell"></i>
                        <span class="contador"><?= $pendientes ?></span>
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
                <div class="user-avatar">AD</div>
                <div class="user-info">
                    <h4>Admin</h4>
                    <p>Administrador</p>
                </div>
            </div>
        </div>
    </aside>

    <script src="../js/sidebaropen.js"></script>
</body>

</html>