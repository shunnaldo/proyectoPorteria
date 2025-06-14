<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Personas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/personaQr.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2"><i class="fas fa-user-plus me-2"></i>Registro de Personas</h1>
                    <a href="personas_qr_list.php"
                        class="btn btn-outline-dark ms-5"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Escanear QR">
                        <i class="fas fa-qrcode fa-2x"></i>
                    </a>
                </div>

                <div class="card p-4 mb-4">
                    <form method="POST" action="../php/guardar_persona.php">
                        <!-- Sección Información Personal -->
                        <h4 class="section-title"><i class="fas fa-id-card me-2"></i>Información Personal</h4>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="rut" class="form-label">RUT</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control" id="rut" name="rut" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="genero" class="form-label">Género</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                    <select class="form-select" id="genero" name="genero" required>
                                        <option value="" selected disabled>Seleccione...</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Transporte -->
                        <h4 class="section-title"><i class="fas fa-car me-2"></i>Información de Transporte</h4>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="medio_transporte" class="form-label">Medio de Transporte</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bus"></i></span>
                                    <select class="form-select" id="medio_transporte" name="medio_transporte" required onchange="togglePatenteField()">
                                        <option value="Auto">Auto</option>
                                        <option value="Pie">A pie</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="patente" class="form-label">Patente</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-car-alt"></i></span>
                                    <input type="text" class="form-control" id="patente" name="patente">
                                </div>
                            </div>
                        </div>

                        <!-- Botón de envío -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Persona
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar u ocultar el campo patente según el medio de transporte
        function togglePatenteField() {
            var medioTransporte = document.getElementById("medio_transporte").value;
            var patenteField = document.getElementById("patente");
            var patenteIcon = document.querySelector("label[for='patente'] + .input-group .input-group-text i");

            if (medioTransporte === "Pie") {
                patenteField.disabled = true;
                patenteField.value = '';
                patenteField.removeAttribute("required");
                patenteField.classList.add("bg-light");
                patenteIcon.classList.remove("fa-car-alt");
                patenteIcon.classList.add("fa-walking");
            } else {
                patenteField.disabled = false;
                patenteField.setAttribute("required", "required");
                patenteField.classList.remove("bg-light");
                patenteIcon.classList.remove("fa-walking");
                patenteIcon.classList.add("fa-car-alt");
            }
        }

        // Inicializar el estado del campo patente al cargar la página
        document.addEventListener("DOMContentLoaded", function() {
            togglePatenteField();
        });
    </script>
</body>

</html>