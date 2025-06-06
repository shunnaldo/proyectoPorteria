<?php
require '../php/conexion.php';
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

$usuario_id = $_SESSION["usuario"]["id"];

// Obtener tickets con sus respuestas
$sql = "SELECT 
            t.id AS ticket_id, 
            t.categoria, 
            t.motivo, 
            t.estado,
            r.mensaje, 
            r.fecha_envio
        FROM tickets t
        LEFT JOIN respuestas_ticket r ON t.id = r.ticket_id
        WHERE t.usuario_id = ?
        ORDER BY t.fecha_creacion DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

// Almacenar todos los resultados en un array antes de cerrar la conexión
$tickets = [];
while ($row = $resultado->fetch_assoc()) {
    $tickets[] = $row;
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets y Respuestas</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4285f4;
            --success-color: #34a853;
            --warning-color: #fbbc05;
            --danger-color: #ea4335;
            --dark-color: #202124;
            --light-color: #f8f9fa;
            --gray-color: #dadce0;
            --text-color: #3c4043;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: white;
            margin-left: 250px;
            /* Ajustar según el ancho de tu sidebar */
        }

        h2 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 0.5rem;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--gray-color);
        }

        th {
            background-color: var(--dark-color);
            color: white;
            font-weight: 500;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-en_proceso {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-finalizado {
            background-color: #d4edda;
            color: #155724;
        }

        .no-response {
            color: #6c757d;
            font-style: italic;
        }

        .material-icons {
            font-size: 20px;
            vertical-align: middle;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state .material-icons {
            font-size: 48px;
            margin-bottom: 15px;
            color: var(--gray-color);

        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            th,
            td {
                padding: 8px 10px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'sidebarOwner.php'; ?>

        <main class="main-content">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <h2><span class="material-icons">contact_support</span> Mis Tickets y Respuestas</h2>
                <a href="tikets.php"
                    style="display: inline-flex; align-items: center; gap: 6px; background-color: transparent; color: #000; padding: 8px 16px; border: 2px solid #000; border-radius: 6px; text-decoration: none; font-weight: 500; box-shadow: 0 2px 6px rgba(66, 133, 244, 0.2); transition: all 0.3s;">
                    <span class="material-icons">add_circle_outline</span> Crear Ticket
                </a>

            </div>
            <div class="table-container">
                <?php if (empty($tickets)): ?>
                    <div class="empty-state">
                        <span class="material-icons">inbox</span>
                        <h3>No tienes tickets registrados</h3>
                        <p>Cuando crees un ticket, aparecerá aquí con las respuestas del administrador.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th>Respuesta</th>
                                <th>Fecha Respuesta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['categoria']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['motivo'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= str_replace(' ', '_', strtolower($row['estado'])) ?>">
                                            <?= htmlspecialchars($row['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['mensaje']): ?>
                                            <?= nl2br(htmlspecialchars($row['mensaje'])) ?>
                                        <?php else: ?>
                                            <span class="no-response">Sin respuesta aún</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $row['fecha_envio'] ? date('d/m/Y H:i', strtotime($row['fecha_envio'])) : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>