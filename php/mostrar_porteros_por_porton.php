<?php
require 'conexion.php'; // Conectar a la base de datos

$porton_id = 1; // Puedes cambiar este valor para recibirlo dinámicamente via GET o por alguna otra lógica

// Consultar los porteros asignados a un portón
$stmt = $conexion->prepare("SELECT u.id, u.nombre FROM usuarios u
                             JOIN usuario_porton up ON u.id = up.usuario_id
                             WHERE up.porton_id = ?");
$stmt->bind_param("i", $porton_id);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Portero: " . $row['nombre'] . "<br>";
    }
} else {
    echo "Este portón no tiene porteros asignados.";
}

$stmt->close();
$conexion->close();
?>
