<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Personas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/personaqr-list">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>


            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">

                    <?php
                    if (isset($_GET['success']) && $_GET['success'] == 1) {
                        echo '<div class="alert alert-success">QR eliminado con éxito.</div>';
                    }
                    ?>
                    <?php
                    if (isset($_GET['success_edit_rut']) && $_GET['success_edit_rut'] == 1) {
                        echo '<div class="alert alert-success">RUT editado con éxito.</div>';
                    }
                    ?>


                    <h1 class="h2">
                        <i class="fas fa-users me-2"></i>Listado de Personas
                    </h1>
                    <div>
                        <a href="registro_persona.php" class="btn btn-outline-dark">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card p-4 mb-4">
                    <h4 class="section-title"><i class="fas fa-list me-2"></i>Personas Registradas</h4>

                    <?php
                    // Incluir el archivo de conexión a la base de datos
                    require '../php/conexion.php';

                    // Función para calcular la edad
                    function calcularEdad($fecha_nacimiento)
                    {
                        $fecha_actual = new DateTime();
                        $fecha_nac = new DateTime($fecha_nacimiento);
                        $edad = $fecha_actual->diff($fecha_nac);
                        return $edad->y;
                    }

                    // Consulta para obtener las personas
                    $sql = "
                        SELECT p.id AS persona_id, p.rut, p.nombre, p.apellido, p.fecha_nacimiento, qt.qr_code
                        FROM personas p
                        INNER JOIN qr_temporal qt ON p.id = qt.persona_id
                        ORDER BY qt.fecha_generacion DESC
                    ";

                    $result = $conexion->query($sql);

                    if ($result->num_rows > 0) {
                    ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-id-card me-1"></i> RUT</th>
                                        <th><i class="fas fa-user me-1"></i> Nombre</th>
                                        <th><i class="fas fa-calendar-alt me-1"></i> Edad</th>
                                        <th class="text-center"><i class="fas fa-qrcode me-1"></i> Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $edad = calcularEdad($row['fecha_nacimiento']);
                                        $qrCodePath = '../php/qr_codes/' . basename($row['qr_code']);
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['rut']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nombre']) . ' ' . htmlspecialchars($row['apellido']); ?></td>
                                            <td><?php echo $edad; ?> años</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-dark"
                                                    onclick="verQR('<?php echo htmlspecialchars($qrCodePath); ?>', <?php echo $row['persona_id']; ?>)"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Ver QR">
                                                    <i class="fas fa-qrcode"></i> Ver QR
                                                </button>


                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="eliminarQR(<?php echo $row['persona_id']; ?>)"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Eliminar QR">
                                                    <i class="fas fa-trash"></i> Eliminar QR
                                                </button>

                                                <button class="btn btn-sm btn-outline-warning"
                                                    onclick="editarRut(<?php echo $row['persona_id']; ?>, '<?php echo htmlspecialchars($row['rut']); ?>')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Editar RUT">
                                                    <i class="fas fa-edit"></i> Editar RUT
                                                </button>


                                            </td>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    } else {
                        echo '<div class="alert alert-info">No se encontraron personas registradas.</div>';
                    }
                    $conexion->close();
                    ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para editar el RUT -->
    <div class="modal fade" id="editarRutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar RUT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarRutForm" action="../php/editarqr_persona.php" method="POST">
                        <input type="hidden" id="personaId" name="personaId">
                        <div class="mb-3">
                            <label for="nuevoRut" class="form-label">Nuevo RUT</label>
                            <input type="text" class="form-control" id="nuevoRut" name="nuevoRut" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Actualizar RUT</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para editar el QR -->
    <div class="modal fade" id="editarQRModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Código QR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarQRForm" action="../php/editarqr_persona.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="personaId" name="personaId">
                        <div class="mb-3">
                            <label for="nuevoQR" class="form-label">Seleccionar nuevo QR</label>
                            <input type="file" class="form-control" id="nuevoQR" name="nuevoQR" accept="image/*" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Subir Nuevo QR</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar el QR en el modal de Bootstrap
        function verQR(qrPath, personaId) {
            document.getElementById('qrImage').src = qrPath;
            document.getElementById('downloadQR').href = qrPath;
            document.getElementById('downloadQR').download = 'qr_persona_' + personaId + '.png';

            // Mostrar el modal
            var qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
            qrModal.show();
        }

        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <script>
        function eliminarQR(personaId) {
            if (confirm("¿Estás seguro de que deseas eliminar el QR de esta persona?")) {
                // Redirigir a la URL de eliminación pasando el ID de la persona
                window.location.href = '../php/eliminarqr_persona?id_persona=' + personaId;
            }
        }
    </script>
    <script>
        function editarRut(personaId, rutActual) {
            // Asignar el ID de la persona y el RUT actual al formulario
            document.getElementById('personaId').value = personaId;
            document.getElementById('nuevoRut').value = rutActual;

            // Mostrar el modal
            var editarRutModal = new bootstrap.Modal(document.getElementById('editarRutModal'));
            editarRutModal.show();
        }
    </script>
</body>

</html>