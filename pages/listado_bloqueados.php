<?php
require_once '../php/conexion.php';

// Obtener lista de personas bloqueadas con información de portones
$query = "SELECT b.id, b.rut, b.motivo, b.fecha_bloqueo, p.nombre as porton_nombre 
          FROM blacklist b
          JOIN portones p ON b.porton_id = p.id
          ORDER BY b.fecha_bloqueo DESC";
$personas_bloqueadas = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Bloqueados | Sistema de Acceso</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <style>

        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            background-color: white;
            margin-right: 20px;
        }
        
        .table-header {
            background-color: #212529;
            color: white;
        }
        
        .table th {
            font-weight: 500;
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .table td {
            padding: 8px 12px;
            vertical-align: middle;
            font-size: 0.85rem;
        }
        
        .btn-action {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            padding: 0;
        }
        
        .material-icons {
            font-size: 1rem;
        }
        
        .page-title {
            font-weight: 600;
            border-left: 4px solid #212529;
            padding-left: 12px;
            font-size: 1.25rem;
        }
        
        .main-content {
            margin-left: 20px;
            width: calc(100% - 270px);
        }
        
        .compact-table {
            width: auto !important;
            max-width: 100%;
        }
        
        /* Ocultar elementos del length menu y info de DataTables */
        .dataTables_length, .dataTables_info {
            display: none;
        }
        
        @media (max-width: 992px) {
            body {
                padding-left: 0;
            }
            .main-content {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    
    <?php include 'sidebar.php'; ?>

    <div class="main-content py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="page-title mb-0">
                                    <i class="material-icons align-middle me-2">block</i>
                                    Personas Bloqueadas
                                </h2>
                                <a href="bloquear_persona.php" class="btn btn-dark btn-sm">
                                    <i class="material-icons align-middle me-1">person_add_disabled</i>
                                    Nuevo Bloqueo
                                </a>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="tablaBloqueados" class="table table-hover align-middle compact-table">
                                    <thead class="table-header">
                                        <tr>
                                            <th>RUT</th>
                                            <th>Portón</th>
                                            <th>Motivo</th>
                                            <th>Fecha Bloqueo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $personas_bloqueadas->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= strtoupper($row['rut']) ?></td>
                                            <td><?= htmlspecialchars($row['porton_nombre']) ?></td>
                                            <td><?= $row['motivo'] ? htmlspecialchars($row['motivo']) : '<span class="text-muted">Sin motivo</span>' ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['fecha_bloqueo'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-action btn-outline-danger" title="Eliminar bloqueo" onclick="confirmarEliminacion(<?= $row['id'] ?>)">
                                                    <i class="material-icons">delete</i>
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
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-6">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body fs-6">
                    ¿Estás seguro que deseas eliminar este bloqueo? La persona podrá volver a acceder al portón.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-danger" id="confirmDeleteBtn">Eliminar</button>
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
       
        
        // Variables para eliminación
        let bloqueoIdToDelete = null;
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        
        // Función para confirmar eliminación
        function confirmarEliminacion(id) {
            bloqueoIdToDelete = id;
            confirmModal.show();
        }
        
        // Configurar botón de confirmación
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (bloqueoIdToDelete) {
                window.location.href = `../php/eliminar_bloqueo.php?id=${bloqueoIdToDelete}`;
            }
        });
    </script>
</body>
</html>