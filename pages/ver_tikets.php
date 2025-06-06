<?php
require '../php/conexion.php';
session_start();

if (!isset($_SESSION["usuario"]) || !in_array($_SESSION["usuario"]["rol"], ["admin"])) {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

// Consulta con JOIN para traer toda la info del usuario
$sql = "SELECT 
            t.id, 
            t.categoria, 
            t.motivo, 
            t.estado, 
            DATE(t.fecha_creacion) AS fecha,
            TIME(t.fecha_creacion) AS hora,
            u.nombre AS nombre_usuario,
            u.rut AS rut_usuario,
            u.correo_electronico AS correo_usuario,
            u.rol AS rol_usuario
        FROM tickets t
        LEFT JOIN usuarios u ON t.usuario_id = u.id
        ORDER BY t.fecha_creacion DESC";

$resultado = $conexion->query($sql);

// Verificar si hubo error en la consulta
if ($resultado === false) {
    die("<div class='error-message'>Error en la consulta: " . htmlspecialchars($conexion->error) . "</div>");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Recibidos | Panel de Administración</title>
    <!-- Incluir iconos de Google -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/ver_tikets.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <h2><span class="material-icons">assignment</span> Tickets Recibidos</h2>

            <?php if (!isset($resultado) || $resultado === false): ?>
                <div class="error-message">
                    Error al cargar los tickets. Por favor, intente nuevamente.
                </div>
            <?php elseif ($resultado->num_rows === 0): ?>
                <div class="no-tickets">
                    No se encontraron tickets en la base de datos.
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Categoría</th>
                                <th>Motivo</th>
                                <th>Usuario</th>
                                <th>RUT</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($ticket = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ticket['id']) ?></td>
                                    <td><?= htmlspecialchars($ticket['categoria']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($ticket['motivo'])) ?></td>
                                    <td><?= htmlspecialchars($ticket['nombre_usuario'] ?? 'Desconocido') ?></td>
                                    <td><?= htmlspecialchars($ticket['rut_usuario'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($ticket['correo_usuario'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($ticket['rol_usuario'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($ticket['fecha']) ?></td>
                                    <td><?= htmlspecialchars($ticket['hora']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= str_replace(' ', '_', strtolower($ticket['estado'])) ?>">
                                            <?= htmlspecialchars($ticket['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <?php if ($ticket['estado'] === 'en_proceso'): ?>
                                                <form method="POST" action="../php/cambiar_estado_ticket.php" onsubmit="return confirm('¿Finalizar este ticket?');">
                                                    <input type="hidden" name="id" value="<?= $ticket['id'] ?>">
                                                    <input type="hidden" name="nuevo_estado" value="finalizado">
                                                    <button type="submit" class="btn btn-primary">
                                                        <span class="material-icons">check_circle</span>
                                                        Finalizar
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <button type="button" class="btn btn-outline" onclick="toggleResponseForm('<?= $ticket['id'] ?>')">
                                                <span class="material-icons">chat</span>
                                                Responder
                                            </button>
                                        </div>

                                        <div id="respuesta-<?= $ticket['id'] ?>" class="response-form">
                                            <form action="../php/responder_ticket.php" method="POST">
                                                <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                                <textarea name="mensaje" placeholder="Escribe tu respuesta profesional aquí..." required></textarea>
                                                <button type="submit" class="btn btn-primary">
                                                    <span class="material-icons">send</span>
                                                    Enviar respuesta
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function toggleResponseForm(ticketId) {
            const form = document.getElementById(`respuesta-${ticketId}`);
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>

</html>