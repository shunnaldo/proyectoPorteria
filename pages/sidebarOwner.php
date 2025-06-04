<?php
// Obtener el nombre de la página actual
$current_page = basename($_SERVER['PHP_SELF']);

// Función para verificar si el enlace está activo
function isActive($page, $current) {
    return $page === $current ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebard.css">
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