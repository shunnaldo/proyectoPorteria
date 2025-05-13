<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Incluir Bootstrap CSS (para íconos y algunos estilos básicos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/botom-nav.css">
</head>
<body>
    

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="portero_portones.php" class="tab active">
            <i class="bi bi-house-door"></i>
            <span class="text">Portones</span>
        </a>
        <a href="escanearQR.php" class="tab">
            <i class="bi bi-person-plus"></i>
            <span class="text">Registrar</span>
        </a>
        <a href="visitas.php" class="tab">
            <i class="bi bi-file-earmark-text"></i>
            <span class="text">Visitas</span>
        </a>
        <a href="../php/logout.php" class="tab">
            <i class="bi bi-person-circle"></i>
            <span class="text">Cerrar sesion</span>
        </a>
    </nav>

    <script>
        // Añadir clase active al hacer clic
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>

</body>
</html>