<?php

session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
require '../php/conexion.php';

require '../php/dashboard_functions.php';


// Rut unico
$sql = "SELECT COUNT(DISTINCT rut) AS total_unicos FROM personas";
$resultado = $conexion->query($sql);

$total_unicos = 0;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    $total_unicos = $fila['total_unicos'];
}

// Genero x rut
$sql = "SELECT genero, COUNT(DISTINCT rut) AS total FROM personas GROUP BY genero";
$resultado = $conexion->query($sql);


$generos = [
    'Masculino' => 0,
    'Femenino' => 0,
    'Otro' => 0
];

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $generos[$fila['genero']] = $fila['total'];
    }
}

// Edad x rut
$sql = "
    SELECT AVG(TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())) AS edad_promedio
    FROM (
        SELECT rut, MIN(fecha_nacimiento) AS fecha_nacimiento
        FROM personas
        GROUP BY rut
    ) AS personas_unicas
";

$resultado = $conexion->query($sql);

$edad_promedio = 0;

$edad_promedio = 0;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    // Verifica si el valor no es NULL y si es numérico
    $edad_promedio = isset($fila['edad_promedio']) && is_numeric($fila['edad_promedio']) ? round($fila['edad_promedio'], 1) : 0;
}


// Conteo total de registros en la tabla blacklist
$sql = "SELECT COUNT(*) AS total_bloqueados FROM blacklist";
$resultado = $conexion->query($sql);

$total_bloqueados = 0;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    $total_bloqueados = $fila['total_bloqueados'];
}

// Obtener lista de portones
$sql = "SELECT id, nombre, estado FROM portones";
$resultado = $conexion->query($sql);

$portones = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $portones[] = $fila;
    }
}

$sql = "
    SELECT COUNT(*) AS total_dentro
    FROM (
        SELECT persona_id
        FROM bitacora_ingresos
        GROUP BY persona_id
        HAVING 
            SUM(opciones = 'ingresada') > SUM(opciones IN ('finalizada', 'expirada'))
    ) AS dentro
";


$resultado = $conexion->query($sql);
$total_dentro = 0;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    $total_dentro = $fila['total_dentro'];
}



// Tiempo de las personas dentro de los recintos
// Tiempo de las personas dentro de los recintos (todas las entradas con salida válida)
$sql = "
    SELECT 
        AVG(TIMESTAMPDIFF(SECOND, fecha_hora, hora_salida)) AS promedio_segundos
    FROM 
        bitacora_ingresos
    WHERE 
        (opciones = 'expirada' OR opciones = 'finalizada')
        AND hora_salida IS NOT NULL
        AND hora_salida > fecha_hora
";

$resultado = $conexion->query($sql);

$tiempo_promedio = 0;
if ($resultado && $fila = $resultado->fetch_assoc()) {
    $tiempo_promedio = round($fila['promedio_segundos'] / 3600, 2); // resultado en horas
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Control de Portones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="dashboard-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="fas fa-tachometer-alt me-2"></i>Resumen Estadístico</h1>
                        <p class="text-muted">Datos clave del sistema</p>
                    </div>
                    <a href="historial.php" class="btn btn-outline-primary">
                        <i class="fas fa-history me-2"></i>Historial
                    </a>
                </div>
            </header>

            <div class="stats-grid">
                <!-- Tarjeta principal - Usuarios únicos -->
                <div class="stat-card primary">
                    <div class="card-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-content">
                        <h3>Usuarios Únicos</h3>
                        <span class="stat-value"><?php echo $total_unicos; ?></span>
                        <p class="stat-description">Personas registradas</p>
                    </div>
                </div>

                <!-- Tarjetas de género -->
                <div class="stat-card info">
                    <div class="card-icon">
                        <i class="fas fa-mars"></i>
                    </div>
                    <div class="card-content">
                        <h3>Masculino</h3>
                        <span class="stat-value"><?php echo $generos['Masculino']; ?></span>
                        <p class="stat-description">Usuarios</p>
                    </div>
                </div>

                <div class="stat-card danger">
                    <div class="card-icon">
                        <i class="fas fa-venus"></i>
                    </div>
                    <div class="card-content">
                        <h3>Femenino</h3>
                        <span class="stat-value"><?php echo $generos['Femenino']; ?></span>
                        <p class="stat-description">Usuarios</p>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="card-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="card-content">
                        <h3>Edad Promedio</h3>
                        <span class="stat-value"><?php echo $edad_promedio; ?></span>
                        <p class="stat-description">Años</p>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="card-icon">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <div class="card-content">
                        <h3>Usuarios Bloqueados</h3>
                        <span class="stat-value"><?php echo $total_bloqueados; ?></span>
                        <p class="stat-description">Registros en blacklist</p>
                    </div>
                </div>


            </div>

            <!-- Lista de Portones registrados -->
            <div class="stat-card neutral">
                <div class="card-icon">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="card-content">
                    <h3>Portones Registrados</h3>
                    <ul style="list-style-type:none; padding-left:0; max-height: 150px; overflow-y: auto;">
                        <?php foreach ($portones as $porton): ?>
                            <div class="stat-card porton-card">
                                <div class="card-icon">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="card-content">
                                    <div class="porton-header">
                                        <h4><?php echo htmlspecialchars($porton['nombre']); ?></h4>
                                        <span class="porton-status <?php echo ($porton['estado'] === 'abierto') ? 'open' : 'closed'; ?>">
                                            <?php echo ucfirst($porton['estado']); ?>
                                        </span>
                                    </div>
                                    <p class="stat-description">Portón registrado</p>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>

            <!-- Portones -->
            <div class="portones-container">

                <div class="section-header">
                    <h2><i class="fas fa-door-open"></i> Control de Portones</h2>
                    <p class="text-muted">Monitoreo y estadísticas de acceso</p>
                </div>

                <!-- Selector de portón -->
                <div class="porton-selector">
                    <label for="select-porton"><i class="fas fa-door-closed me-2"></i>Selecciona un portón:</label>
                    <select id="select-porton" class="form-select">
                        <option value="">-- Selecciona un portón --</option>
                        <?php foreach ($portones as $porton): ?>
                            <option value="<?php echo $porton['id']; ?>"><?php echo htmlspecialchars($porton['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div id="no-selection-message" class="no-selection">
                    <i class="fas fa-door-open"></i>
                    <p>Selecciona un portón para ver las estadísticas</p>
                </div>

                <!-- Spinner de carga -->
                <div id="loading-spinner" class="loading-spinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>

                <!-- Container de estadísticas -->
                <div id="stats-section">
                    <div class="stats-container">

                        <!-- Tarjeta de total de ingresos -->
                        <div id="tarjeta-ingresos-porton" class="stat-card ingresos">
                            <div class="card-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="card-content">
                                <h4>Total de ingresos</h4>
                                <span id="ingresos-total" class="stat-value">0</span>
                                <p class="stat-description" id="nombre-porton-ingresos">Portón no seleccionado</p>
                            </div>
                        </div>

                        <!-- Tarjeta de personas dentro -->
                        <div id="tarjeta-personas-dentro" class="stat-card personas-dentro">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-content">
                                <h4>Personas dentro</h4>
                                <span id="personas-dentro-total" class="stat-value">0</span>
                                <p class="stat-description" id="nombre-porton-dentro">Portón no seleccionado</p>
                            </div>
                        </div>

                        <div class="stat-card bg-secondary text-white">
                            <div class="card-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="card-content">
                                <h3>Tiempo Promedio</h3>
                                <span class="stat-value"><?php echo $tiempo_promedio; ?> h</span>
                                <p class="stat-description">Estancia en el recinto</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>

    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#stats-section').hide();
            $('#loading-spinner').hide();

            $('#select-porton').on('change', function() {
                const portonId = $(this).val();
                const portonNombre = $(this).find('option:selected').text();

                if (!portonId) {
                    $('#no-selection-message').fadeIn();
                    $('#stats-section').hide();
                    $('.stat-card').removeClass('show');
                    return;
                }

                $('#no-selection-message').hide();
                $('#stats-section').hide();
                $('#loading-spinner').show();
                $('.stat-card').removeClass('show');

                let loadedCount = 0;

                function checkIfAllLoaded() {
                    loadedCount++;
                    if (loadedCount === 3) {
                        $('#loading-spinner').hide();
                        $('#stats-section').fadeIn();
                    }
                }

                function showError() {
                    $('#loading-spinner').hide();
                    $('#no-selection-message').html('<i class="fas fa-exclamation-triangle"></i><p>Error al cargar los datos. Intente nuevamente.</p>').fadeIn();
                }

                // Total ingresos por portón
                $.get('../php/contar_ingresos_porton.php', {
                    porton_id: portonId
                }, function(response) {
                    try {
                        const data = JSON.parse(response);
                        $('#ingresos-total').text(data.total);
                        $('#nombre-porton-ingresos').text(`Portón: ${portonNombre}`);
                        $('#tarjeta-ingresos-porton').addClass('show');
                        checkIfAllLoaded();
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        showError();
                    }
                }).fail(showError);

                // Personas dentro por portón
                $.get('../php/contar_personas_dentro_porton.php', {
                    porton_id: portonId
                }, function(response) {
                    try {
                        const data = JSON.parse(response);
                        $('#personas-dentro-total').text(data.total);
                        $('#nombre-porton-dentro').text(`Portón: ${portonNombre}`);
                        $('#tarjeta-personas-dentro').addClass('show');
                        checkIfAllLoaded();
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        showError();
                    }
                }).fail(showError);

                // Tiempo promedio por portón
                $.get('../php/tiempo_promedio_porton.php', {
                    porton_id: portonId
                }, function(response) {
                    try {
                        const data = JSON.parse(response);
                        $('.stat-card.bg-secondary .stat-value').text(`${data.tiempo_promedio} h`);
                        $('.stat-card.bg-secondary .stat-description').text(`Estancia en el recinto - ${portonNombre}`);
                        $('.stat-card.bg-secondary').addClass('show');
                        checkIfAllLoaded();
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        showError();
                    }
                }).fail(showError);
            });
        });
    </script>



</body>

</html>