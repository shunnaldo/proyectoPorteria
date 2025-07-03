<?php
require_once '../php/conexion.php';

// Obtener lista de personas bloqueadas con información de portones
$query = "SELECT 
            b.id, 
            b.rut, 
            b.nombre, 
            b.genero, 
            b.fecha_nacimiento, 
            b.motivo, 
            b.fecha_bloqueo, 
            p.nombre as porton_nombre 
          FROM blacklist b
          JOIN portones p ON b.porton_id = p.id
          ORDER BY b.fecha_bloqueo DESC";
$personas_bloqueadas = $conexion->query($query);

function calcularEdad($fecha_nacimiento) {
    if (!$fecha_nacimiento) return '—';
    $hoy = new DateTime();
    $nacimiento = new DateTime($fecha_nacimiento);
    $edad = $hoy->diff($nacimiento);
    return $edad->y . ' años';
}

// Obtener lista de patentes bloqueadas con información del portón
$queryPatentes = "SELECT 
                    pb.id, 
                    pb.patente, 
                    pb.motivo_bloqueo, 
                    pb.fecha_bloqueo, 
                    p.nombre AS porton_nombre 
                 FROM tbl_patentes_bloqueadas pb
                 JOIN portones p ON pb.porton_id = p.id
                 ORDER BY pb.fecha_bloqueo DESC";
$patentes_bloqueadas = $conexion->query($queryPatentes);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Bloqueados | Sistema de Acceso</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --accent-color: #343a40;
        }
        
        body {
            background-color: #f5f5f5;
            color: var(--dark-color);
        }
        
        .sidebar {
            background-color: var(--dark-color);
            color: white;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 16px 20px;
        }
        
        .card-title {
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .card-title i {
            margin-right: 10px;
            color: var(--dark-color);
        }
        
        .btn-dark {
            background-color: var(--dark-color);
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn-dark:hover {
            background-color: var(--accent-color);
        }
        
        .btn-dark i {
            margin-right: 6px;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        
        .table td {
            vertical-align: middle;
            border-top: 1px solid #f0f0f0;
            padding: 12px 16px;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .badge-status {
            font-size: 12px;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            padding: 0;
        }
        
        .btn-action i {
            font-size: 16px;
        }
        
        /* Tabs style */
        .nav-tabs {
            border-bottom: 1px solid #e9ecef;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--dark-color);
            border-bottom: 2px solid var(--dark-color);
            background-color: transparent;
        }
        
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: var(--dark-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .btn-dark {
                margin-top: 10px;
                width: 100%;
            }
        }
    </style>
</head>

<body class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content flex-grow-1 p-4">
        <div class="container-fluid">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="blockedTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="people-tab" data-bs-toggle="tab" data-bs-target="#people-tab-pane" type="button" role="tab">
                        <i class="fas fa-user-slash me-2"></i>Personas Bloqueadas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="plates-tab" data-bs-toggle="tab" data-bs-target="#plates-tab-pane" type="button" role="tab">
                        <i class="fas fa-car me-2"></i>Patentes Bloqueadas
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="blockedTabsContent">
                <!-- Personas Bloqueadas Tab -->
                <div class="tab-pane fade show active" id="people-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-user-slash"></i>
                                Listado de Personas Bloqueadas
                            </h3>
                            <a href="bloquear_persona.php" class="btn btn-dark">
                                <i class="fas fa-user-times"></i>
                                Nuevo Bloqueo
                            </a>
                        </div>
                        
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tablaPersonas" class="table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>RUT</th>
                                            <th>Nombre</th>
                                            <th>Portón</th>
                                            <th>Motivo</th>
                                            <th>Fecha Bloqueo</th>
                                            <th>Género</th>
                                            <th>Edad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $personas_bloqueadas->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= strtoupper($row['rut']) ?></td>
                                                <td><?= htmlspecialchars($row['nombre'] ?: '—') ?></td>
                                                <td><?= htmlspecialchars($row['porton_nombre']) ?></td>
                                                <td><?= htmlspecialchars($row['motivo'] ?: 'Sin motivo') ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($row['fecha_bloqueo'])) ?></td>
                                                <td><?= htmlspecialchars($row['genero'] ?: '—') ?></td>
                                                <td><?= calcularEdad($row['fecha_nacimiento']) ?></td>
                                                <td>
                                                    <button class="btn btn-action btn-outline-danger" title="Eliminar bloqueo" onclick="confirmarEliminacion(<?= $row['id'] ?>, 'persona')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Patentes Bloqueadas Tab -->
                <div class="tab-pane fade" id="plates-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-car"></i>
                                Listado de Patentes Bloqueadas
                            </h3>
                            <a href="patentes.php" class="btn btn-dark">
                                <i class="fas fa-plus-circle"></i>
                                Nueva Patente
                            </a>
                        </div>
                        
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tablaPatentes" class="table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Patente</th>
                                            <th>Portón</th>
                                            <th>Motivo</th>
                                            <th>Fecha Bloqueo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $patentes_bloqueadas->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= strtoupper($row['patente']) ?></td>
                                                <td><?= htmlspecialchars($row['porton_nombre']) ?></td>
                                                <td><?= htmlspecialchars($row['motivo_bloqueo']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($row['fecha_bloqueo'])) ?></td>
                                                <td>
                                                    <button class="btn btn-action btn-outline-danger" title="Eliminar bloqueo" onclick="confirmarEliminacion(<?= $row['id'] ?>, 'patente')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        Confirmar acción
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage">¿Estás seguro que deseas eliminar este bloqueo?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash-alt me-2"></i>Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery y DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('#tablaPersonas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                order: [[4, 'desc']] // Ordenar por fecha de bloqueo descendente
            });
            
            $('#tablaPatentes').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true,
                order: [[3, 'desc']] // Ordenar por fecha de bloqueo descendente
            });
        });
        
        // Variables para el modal de confirmación
        let itemIdToDelete = null;
        let itemTypeToDelete = null;
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        
        function confirmarEliminacion(id, type) {
            itemIdToDelete = id;
            itemTypeToDelete = type;
            
            // Actualizar mensaje según el tipo
            const message = type === 'persona' 
                ? "¿Estás seguro que deseas eliminar este bloqueo? La persona podrá volver a acceder al portón."
                : "¿Estás seguro que deseas eliminar este bloqueo? La patente podrá volver a acceder al portón.";
            
            document.getElementById('modalMessage').textContent = message;
            confirmModal.show();
        }
        
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (itemIdToDelete && itemTypeToDelete) {
                const url = itemTypeToDelete === 'persona' 
                    ? `../php/eliminar_bloqueo.php?id=${itemIdToDelete}`
                    : `../php/eliminar_patente.php?id=${itemIdToDelete}`;
                
                window.location.href = url;
            }
        });
    </script>
</body>

</html>