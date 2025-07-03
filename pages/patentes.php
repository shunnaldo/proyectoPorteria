<?php
require_once '../php/conexion.php';
$portones = $conexion->query("SELECT id, nombre FROM portones");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Bloquear Patente</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
        }
        .sidebar {
            background-color: #212529;
            color: white;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #212529;
            box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
        }
        .btn-custom {
            background-color: #212529;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background-color: #343a40;
            transform: translateY(-2px);
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
        }
    </style>
</head>

<body class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="container-fluid p-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Patente bloqueada correctamente!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card p-4">
                    <div class="card-body">
                        <h2 class="mb-4 text-center">
                            <i class="fas fa-ban me-2"></i>Bloquear Patente
                        </h2>

                        <form action="../php/procesar_bloqueo.php" method="POST">
                            <div class="mb-3">
                                <label for="patente" class="form-label">
                                    <i class="fas fa-car me-2"></i>Patente
                                </label>
                                <input type="text" class="form-control" id="patente" name="patente" maxlength="10" required>
                            </div>

                            <div class="mb-3">
                                <label for="motivo" class="form-label">
                                    <i class="fas fa-comment me-2"></i>Motivo del bloqueo
                                </label>
                                <textarea class="form-control" id="motivo" name="motivo" rows="4" required></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="porton_id" class="form-label">
                                    <i class="fas fa-door-closed me-2"></i>Portón
                                </label>
                                <select class="form-select" id="porton_id" name="porton_id" required>
                                    <option value="" selected disabled>Selecciona un portón</option>
                                    <?php while ($row = $portones->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-custom">
                                    <i class="fas fa-save me-2"></i>Guardar bloqueo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>