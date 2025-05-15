<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
require '../php/conexion.php';
require '../php/dashboard_functions.php';
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
                            <span><?php echo getIngresosHoy($conexion); ?></span>
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
                
                <!-- Últimos Ingresos -->
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

        // Gráfico de ingresos por hora
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

        // Gráfico de actividad de portones
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

        // Redimensionar gráficos al cambiar tamaño de pantalla
        window.addEventListener('resize', function() {
            ingresosHoraChart.resize();
            portonesChart.resize();
        });
    </script>
</body>
</html>