<?php
require 'conexion.php'; // Conectar a la base de datos

$usuario_id = 1; // Puedes cambiar este valor para recibirlo dinámicamente via GET o por alguna otra lógica

// Consultar los portones asignados a un portero
$stmt = $conexion->prepare("SELECT p.id, p.nombre FROM portones p
                             JOIN usuario_porton up ON p.id = up.porton_id
                             WHERE up.usuario_id = ?");
$stmt->bind_param("i", $usuario_id);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Portón: " . $row['nombre'] . "<br>";
    }
} else {
    echo "Este portero no está asignado a ningún portón.";
}

$stmt->close();
$conexion->close();
?>
