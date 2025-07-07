<?php
session_start();

if (!isset($_SESSION['preinscripcion'])) {
    // No hay datos, redirigir a inicio
    header("Location: ../index.php");
    exit;
}

$persona = $_SESSION['preinscripcion'];

// Función para calcular edad a partir de fecha de nacimiento
function calcularEdad($fechaNacimiento) {
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento)->y;
    return $edad;
}

$edad = calcularEdad($persona['fecha_nacimiento']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ingreso Expo - Ticket</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .ticket {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            margin-bottom: 1rem;
            color: #333;
        }
        .info {
            text-align: left;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            color: #555;
        }
        .info strong {
            display: inline-block;
            width: 140px;
            color: #222;
        }
        button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="ticket">
    <h2>Ticket de Ingreso - Expo</h2>
    <div class="info">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($persona['nombre']) ?></p>
        <p><strong>RUT:</strong> <?= htmlspecialchars($persona['rut']) ?></p>
        <p><strong>Edad:</strong> <?= $edad ?> años</p>
        <p><strong>Fecha de nacimiento:</strong> <?= date('d-m-Y', strtotime($persona['fecha_nacimiento'])) ?></p>
        <p><strong>Dirección:</strong> <?= htmlspecialchars($persona['direccion']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($persona['correo']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($persona['telefono']) ?></p>
        <p><strong>Exposición:</strong> <?= htmlspecialchars($persona['exposicion']) ?></p>
    </div>
    <button onclick="window.location.href='../index.php'">Volver al inicio</button>
</div>

</body>
</html>
