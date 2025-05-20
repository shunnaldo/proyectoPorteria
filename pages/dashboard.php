<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
} 
require '../php/conexion.php';
require '../php/dashboard_functions.php';

// Obtener el portón seleccionado si existe
$porton_seleccionado = isset($_GET['porton_id']) ? intval($_GET['porton_id']) : 0;
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
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <header class="dashboard-header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['usuario']['nombre'] . ' ' . $_SESSION['usuario']['apellido']); ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>
            
            <!-- Selector de portones -->
            <div class="porton-selector mb-4">
                <form id="portonForm" method="get">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="portonSelect" class="form-label">Seleccionar Portón:</label>
                            <select class="form-select" id="portonSelect" name="porton_id">
                                <option value="0" <?php echo $porton_seleccionado == 0 ? 'selected' : ''; ?>>Todos los Portones (Vista Global)</option>
                                <?php echo getPortonesParaSelector($conexion); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <main class="dashboard-container">
                <!-- Sección de Estadísticas -->
                <section class="stats-section">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Personas</h3>
                            <span><?php echo getTotalPersonas($conexion); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Ingresos Hoy</h3>
                            <span><?php echo $porton_seleccionado ? getIngresosHoyPorPorton($conexion, $porton_seleccionado) : getIngresosHoy($conexion); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-door-closed"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Portones Abiertos</h3>
                            <span><?php echo getPortonesAbiertos($conexion); ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-danger">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Con Patente</h3>
                            <span><?php echo getPersonasConPatente($conexion); ?></span>
                        </div>
                    </div>
                </section>
                
                <!-- Gráficos -->
                <section class="charts-section">
                    <div class="chart-container">
                        <h2>Ingresos por Hora</h2>
                        <div class="chart-wrapper">
                            <canvas id="ingresosHoraChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <h2>Actividad de Portones</h2>
                        <div class="chart-wrapper">
                            <canvas id="portonesChart"></canvas>
                        </div>
                    </div>
                </section>
                
                <!-- Si hay un portón seleccionado, mostrar sus datos específicos -->
                <?php if ($porton_seleccionado): ?>
                <section class="specific-porton-section mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">Datos específicos del Portón seleccionado</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Ingresos por Hora</h4>
                                    <div class="chart-wrapper">
                                        <canvas id="ingresosHoraPortonChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4>Últimos Ingresos</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Portón</th>
                                                    <th>Fecha/Hora</th>
                                                    <th>Patente</th>
                                                    <th>Portero</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php echo getUltimosIngresos($conexion, $porton_seleccionado); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- Últimos Ingresos (globales) -->
                <section class="recent-entries">
                    <h2>Últimos Ingresos</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Portón</th>
                                    <th>Fecha/Hora</th>
                                    <th>Patente</th>
                                    <th>Portero</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo getUltimosIngresos($conexion); ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- Estado de Portones -->
                <section class="gate-status">
                    <h2>Estado de Portones</h2>
                    <div class="gates-container">
                        <?php echo getEstadoPortones($conexion); ?>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configuración responsiva para gráficos
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Gráfico de ingresos por hora (global)
        const ingresosHoraCtx = document.getElementById('ingresosHoraChart').getContext('2d');
        const ingresosHoraChart = new Chart(ingresosHoraCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(getHorasDelDia()); ?>,
                datasets: [{
                    label: 'Ingresos por Hora',
                    data: <?php echo json_encode(getIngresosPorHora($conexion)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });

        // Gráfico de actividad de portones (global)
        const portonesCtx = document.getElementById('portonesChart').getContext('2d');
        const portonesChart = new Chart(portonesCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(getNombresPortones($conexion)); ?>,
                datasets: [{
                    data: <?php echo json_encode(getUsoPortones($conexion)); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                ...chartOptions,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Gráfico de ingresos por hora para portón específico
        <?php if ($porton_seleccionado): ?>
        const ingresosHoraPortonCtx = document.getElementById('ingresosHoraPortonChart').getContext('2d');
        const ingresosHoraPortonChart = new Chart(ingresosHoraPortonCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(getHorasDelDia()); ?>,
                datasets: [{
                    label: 'Ingresos por Hora',
                    data: <?php echo json_encode(getIngresosPorHora($conexion, $porton_seleccionado)); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
        <?php endif; ?>

        // Redimensionar gráficos al cambiar tamaño de pantalla
        window.addEventListener('resize', function() {
            ingresosHoraChart.resize();
            portonesChart.resize();
            <?php if ($porton_seleccionado): ?>
            ingresosHoraPortonChart.resize();
            <?php endif; ?>
        });
    </script>
</body>
</html>