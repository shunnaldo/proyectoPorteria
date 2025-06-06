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

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #ffffff;
            color: #212529;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .form-control,
        .form-select {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #212529;
            box-shadow: 0 0 0 2px rgba(33, 37, 41, 0.1);
        }

        .btn-custom {
            background-color: #212529;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #343a40;
            transform: translateY(-1px);
        }

        .header-icon {
            font-size: 2.5rem;
            color: #212529;
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
        }

        .material-icons {
            vertical-align: middle;
            color: #212529;
        }

        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 24px 0;
        }
    </style>
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