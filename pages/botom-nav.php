<?php
// Obtener el nombre de la página actual
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Incluir Bootstrap CSS (para íconos y algunos estilos básicos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/botom-nav.css">
</head>
<body>
    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="portero_portones.php" class="tab <?php echo $current_page == 'portero_portones.php' ? 'active' : ''; ?>">
            <i class="bi bi-house-door"></i>
            <span class="text">Portones</span>
        </a>
        <a href="bitacoras.php" class="tab <?php echo $current_page == 'bitacoras.php' ? 'active' : ''; ?>">
            <i class="bi bi-file-earmark-text"></i>
            <span class="text">Visitas</span>
        </a>
        <a href="../php/logout.php" class="tab">
            <i class="bi bi-box-arrow-right"></i> <!-- Icono de salida -->
            <span class="text">Cerrar sesión</span>
        </a>
    </nav>

    <script>
        // Solo manejar clicks para añadir clase active (opcional)
        document.querySelectorAll('.bottom-nav .tab:not([href*="logout.php"])').forEach(tab => {
            tab.addEventListener('click', function(e) {
                // No es necesario para logout
                if (this.href.includes('logout.php')) return;
                
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>