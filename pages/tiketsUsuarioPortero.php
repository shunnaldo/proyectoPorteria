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
    <title>Mis Tickets</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4285F4;
            --primary-dark: #3367D6;
            --success: #34A853;
            --warning: #FBBC05;
            --danger: #EA4335;
            --gray-light: #F5F5F5;
            --gray-medium: #E0E0E0;
            --gray-dark: #757575;
            --text-primary: #212121;
            --text-secondary: #757575;
            --white: #FFFFFF;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--gray-light);
            color: var(--text-primary);
            line-height: 1.5;
        }

        .container {
            max-width: 100%;
            padding: 0;
            margin: 0 auto;
        }

        .header {
            position: sticky;
            top: 0;
            background-color: var(--white);
            padding: 16px;
            box-shadow: var(--shadow);
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.25rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header .icon {
            color: var(--primary);
        }

        .main-content {
            padding: 16px;
            padding-bottom: 80px;
        }

        .create-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background-color: var(--primary);
            color: var(--white);
            padding: 10px 16px;
            border-radius: 24px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(66, 133, 244, 0.2);
            transition: all 0.3s;
            border: none;
            font-size: 0.875rem;
        }

        .create-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-top: 20px;
        }

        .empty-state .icon {
            font-size: 48px;
            color: var(--gray-dark);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 1.125rem;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .ticket-list {
            margin-top: 20px;
        }

        .ticket-card {
            background-color: var(--white);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: var(--shadow);
            transition: transform 0.2s;
        }

        .ticket-card:hover {
            transform: translateY(-2px);
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .ticket-category {
            font-weight: 500;
            color: var(--primary);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ticket-status {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 500;
        }

        .status-pendiente {
            background-color: rgba(251, 188, 5, 0.1);
            color: var(--warning);
        }

        .status_en_proceso {
            background-color: rgba(66, 133, 244, 0.1);
            color: var(--primary);
        }

        .status-resuelto {
            background-color: rgba(52, 168, 83, 0.1);
            color: var(--success);
        }

        .status-cerrado {
            background-color: rgba(117, 117, 117, 0.1);
            color: var(--gray-dark);
        }

        .ticket-message {
            font-size: 0.875rem;
            color: var(--text-primary);
            margin-bottom: 12px;
            white-space: pre-line;
        }

        .ticket-response {
            background-color: var(--gray-light);
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
        }

        .response-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .response-content {
            font-size: 0.875rem;
            color: var(--text-primary);
            white-space: pre-line;
        }

        .no-response {
            color: var(--gray-dark);
            font-style: italic;
            font-size: 0.875rem;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: var(--white);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 100;
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <h1><span class="material-icons icon">contact_support</span> Mis Tickets</h1>
            <a href="tiketsPortero.php" class="create-btn">
                <span class="material-icons">add</span> Nuevo
            </a>
        </header>

        <main class="main-content">
            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <span class="material-icons icon">inbox</span>
                    <h3>No tienes tickets registrados</h3>
                    <p>Cuando crees un ticket, aparecerá aquí con las respuestas del administrador.</p>
                </div>
            <?php else: ?>
                <div class="ticket-list">
                    <?php
                    // Agrupar respuestas por ticket
                    $grouped_tickets = [];
                    foreach ($tickets as $ticket) {
                        $ticket_id = $ticket['ticket_id'];
                        if (!isset($grouped_tickets[$ticket_id])) {
                            $grouped_tickets[$ticket_id] = [
                                'ticket' => [
                                    'id' => $ticket['ticket_id'],
                                    'categoria' => $ticket['categoria'],
                                    'motivo' => $ticket['motivo'],
                                    'estado' => $ticket['estado']
                                ],
                                'respuestas' => []
                            ];
                        }
                        if ($ticket['mensaje']) {
                            $grouped_tickets[$ticket_id]['respuestas'][] = [
                                'mensaje' => $ticket['mensaje'],
                                'fecha_envio' => $ticket['fecha_envio']
                            ];
                        }
                    }
                    ?>

                    <?php foreach ($grouped_tickets as $group): ?>
                        <div class="ticket-card">
                            <div class="ticket-header">
                                <span class="ticket-category">
                                    <span class="material-icons" style="font-size: 18px;">category</span>
                                    <?= htmlspecialchars($group['ticket']['categoria']) ?>
                                </span>
                                <span class="ticket-status status-<?= str_replace(' ', '_', strtolower($group['ticket']['estado'])) ?>">
                                    <?= htmlspecialchars($group['ticket']['estado']) ?>
                                </span>
                            </div>

                            <p class="ticket-message"><?= nl2br(htmlspecialchars($group['ticket']['motivo'])) ?></p>

                            <?php if (!empty($group['respuestas'])): ?>
                                <?php foreach ($group['respuestas'] as $respuesta): ?>
                                    <div class="ticket-response">
                                        <div class="response-header">
                                            <span>Respuesta del soporte</span>
                                            <span><?= date('d/m/Y H:i', strtotime($respuesta['fecha_envio'])) ?></span>
                                        </div>
                                        <p class="response-content"><?= nl2br(htmlspecialchars($respuesta['mensaje'])) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-response">Aún no hay respuesta para este ticket</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>


    </div>
    <?php include 'botom-nav.php'; ?>

</body>

</html>