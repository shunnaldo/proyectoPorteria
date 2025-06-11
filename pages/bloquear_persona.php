<?php
require_once '../php/conexion.php';

// Obtener portones para el select
$portones = $conexion->query("SELECT id, nombre FROM portones");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloquear Usuario | Sistema de Acceso</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts - Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="../css/bloquear_persona.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <i class="material-icons header-icon">block</i>
                        <h2 class="mb-3">Bloquear Acceso</h2>
                        <p class="text-muted">Restringir acceso a un usuario específico</p>
                        <div class="divider"></div>
                    </div>

                    <form method="POST" action="../php/guardar_bloqueo.php" onsubmit="return cleanRutBeforeSubmit()">

                        <div class="mb-4">
                            <label for="rut" class="form-label">
                                <i class="material-icons me-2">fingerprint</i>
                                RUT (sin puntos ni guión)
                            </label>
                            <input type="text" class="form-control" id="rut" name="rut" required
                                placeholder="Ej: 12345678-K">
                        </div>

                        <div class="mb-4">
                            <label for="nombre" class="form-label">
                                <i class="material-icons me-2">person</i>
                                Nombre completo
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Ana González">
                        </div>


                        <div class="mb-4">
                            <label for="genero" class="form-label">
                                <i class="material-icons me-2">wc</i>
                                Género
                            </label>
                            <select class="form-select" id="genero" name="genero" required>
                                <option value="" selected disabled>Selecciona un género</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="fecha_nacimiento" class="form-label">
                                <i class="material-icons me-2">calendar_today</i>
                                Fecha de nacimiento
                            </label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>

                        <div class="mb-4">
                            <label for="porton_id" class="form-label">
                                <i class="material-icons me-2">meeting_room</i>
                                Portón
                            </label>
                            <select class="form-select" id="porton_id" name="porton_id" required>
                                <option value="" selected disabled>Selecciona un portón</option>
                                <?php while ($row = $portones->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="motivo" class="form-label">
                                <i class="material-icons me-2">notes</i>
                                Motivo del bloqueo (opcional)
                            </label>
                            <input type="text" class="form-control" id="motivo" name="motivo"
                                placeholder="Ej: Acceso revocado temporalmente">
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-custom">
                                <i class="material-icons me-2">lock</i>
                                Confirmar Bloqueo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- RUT formatter script -->
    <script>
        function cleanRutBeforeSubmit() {
            const rutInput = document.getElementById('rut');
            // Eliminar puntos, guión y espacios, convertir a mayúsculas
            rutInput.value = rutInput.value.replace(/[\.\-]/g, '').toUpperCase();
            return true; // Continuar con el envío del formulario
        }

        // Mantén el formateo visual para el usuario
        document.getElementById('rut').addEventListener('input', function(e) {
            let rut = this.value.replace(/[^0-9kK]/g, '');

            if (rut.length > 1) {
                rut = rut.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + rut.slice(-1);
            }

            this.value = rut.toUpperCase();
        });
    </script>
</body>

</html>