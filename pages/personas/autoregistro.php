<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Persona</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../../css/autoregistro.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <!-- Título y logo -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="form-title m-0">Registro de Persona</h2>
                <img src="../../images/LogoFitNegro.png" alt="Logo Empresa" style="height: 80px; width: 80px; object-fit: cover; border-radius: 12px;">
            </div>

            <form action="../../php/registrar_persona.php" method="POST">
                <!-- RUT -->
                <div class="mb-2">
                    <label for="rut" class="form-label">
                        <i class="fas fa-id-card input-icon"></i>RUT
                    </label>
                    <input type="text" class="form-control" id="rut" name="rut"
                        maxlength="9" placeholder="Ej: 123456789" required
                        pattern="[0-9]{7,8}[0-9kK]{1}">
                    <div class="rut-hint">Sin puntos ni guión</div>
                </div>

                <!-- Nombre -->
                <div class="mb-2">
                    <label for="nombre" class="form-label">
                        <i class="fas fa-user input-icon"></i>Nombre
                    </label>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                        maxlength="50" placeholder="Ej: Benjamín" required>
                </div>

                <!-- Apellido -->
                <div class="mb-2">
                    <label for="apellido" class="form-label">
                        <i class="fas fa-user input-icon"></i>Apellido
                    </label>
                    <input type="text" class="form-control" id="apellido" name="apellido"
                        maxlength="50" placeholder="Ej: Parra" required>
                </div>

                <!-- Género -->
                <div class="mb-2">
                    <label for="genero" class="form-label">
                        <i class="fas fa-venus-mars input-icon"></i>Género
                    </label>
                    <select class="form-control" id="genero" name="genero" required>
                        <option value="" disabled selected>Seleccionar</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <!-- Fecha de nacimiento -->
                <div class="mb-3">
                    <label for="fecha_nacimiento" class="form-label">
                        <i class="fas fa-calendar input-icon"></i>Fecha de nacimiento
                    </label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                </div>

                <!-- Campos ocultos -->
                <input type="hidden" name="medio_transporte" value="Pie">
                <input type="hidden" name="patente" value="">

                <!-- Botón de enviar -->
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-user-check"></i> Registrar
                </button>
                <div class="text-center mt-3">
                    <img src="../../images/FloridaLogo.png" alt="Registro exitoso" class="img-fluid" style="max-width: 100%; height: auto;">
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación y formato del RUT
        document.getElementById('rut').addEventListener('input', function(e) {
            let rut = this.value.replace(/[\.\-]/g, '');
            if (rut.length > 9) rut = rut.substring(0, 9);
            if (rut.length > 0) {
                const lastChar = rut.charAt(rut.length - 1).toUpperCase();
                rut = rut.substring(0, rut.length - 1) + lastChar;
            }
            this.value = rut;
        });
    </script>
</body>

</html>