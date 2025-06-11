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

    <link rel="stylesheet" href="../css/listabloqueao.css">
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
                                            <th>Nombre</th>
                                            <th>Género</th>
                                            <th>Fecha Nac.</th>
                                            <th>Edad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $personas_bloqueadas->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= strtoupper($row['rut']) ?></td>
                                                <td><?= htmlspecialchars($row['porton_nombre']) ?></td>
                                                <td><?= htmlspecialchars($row['motivo'] ?: 'Sin motivo') ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($row['fecha_bloqueo'])) ?></td>
                                                <td><?= htmlspecialchars($row['nombre'] ?: '—') ?></td>
                                                <td><?= htmlspecialchars($row['genero'] ?: '—') ?></td>
                                                <td><?= $row['fecha_nacimiento'] ? date('d/m/Y', strtotime($row['fecha_nacimiento'])) : '—' ?></td>
                                                <td><?= calcularEdad($row['fecha_nacimiento']) ?></td>
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
        let bloqueoIdToDelete = null;
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

        function confirmarEliminacion(id) {
            bloqueoIdToDelete = id;
            confirmModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (bloqueoIdToDelete) {
                window.location.href = `../php/eliminar_bloqueo.php?id=${bloqueoIdToDelete}`;
            }
        });
    </script>
</body>

</html>
