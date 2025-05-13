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
            <div class="menu-title">Principal</div>
            <a href="#" class="menu-item active">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>
            
            <div class="menu-title">Gestión</div>
            <div class="menu-item has-dropdown" id="usersMenu">
                <i class="fas fa-users"></i>
                <span class="menu-text">Usuarios</span>
            </div>
            <div class="menu-dropdown" id="usersDropdown">
                <a href="ver_usuarios.php" class="menu-item">
                    <i class="fas fa-circle-notch"></i>
                    <span class="menu-text">Todos los usuarios</span>
                </a>
                <a href="registrotrabajador.php" class="menu-item">
                    <i class="fas fa-user-plus"></i>
                    <span class="menu-text">Agregar nuevo</span>
                </a>

            </div>
            
            <a href="crear_porton.php" class="menu-item">
                <i class="fas fa-building"></i>
                <span class="menu-text">Portones</span>
            </a>
            
            <a href="asignar_portero.php" class="menu-item">
                <i class="fas fa-user-tie"></i>
                <span class="menu-text">Porteros</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="fas fa-clipboard-list"></i>
                <span class="menu-text">Registros</span>
            </a>
            
            <div class="menu-title">Configuración</div>

            <a href="#" class="menu-item">
                <i class="fas fa-cog"></i>
                <span class="menu-text">Ajustes</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="fas fa-bell"></i>
                <span class="menu-text">Notificaciones</span>
            </a>

        </div>
        
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">AD</div>
                <div class="user-info">
                    <h4>Admin User</h4>
                    <p>Administrador</p>
                </div>
            </div>
        </div>
    </aside>


 <script src="../js/sidebaropen.js"></script>  
</body>
</html>