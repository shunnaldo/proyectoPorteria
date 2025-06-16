<?php
require_once 'conexion.php';

// Verificar si se ha recibido el ID de la persona y el nuevo RUT
if (isset($_POST['personaId']) && isset($_POST['nuevoRut'])) {
    $personaId = $_POST['personaId'];
    $nuevoRut = $_POST['nuevoRut'];

    // Validar que el nuevo RUT no esté vacío
    if (!empty($nuevoRut)) {
        // Verificar si el nuevo RUT ya existe en la base de datos
        $sqlCheck = "SELECT id FROM personas WHERE rut = ?";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $nuevoRut);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        
        if ($stmtCheck->num_rows > 0) {
            echo "El RUT ingresado ya existe en el sistema.";
        } else {
            // Actualizar el RUT de la persona
            $sqlUpdate = "UPDATE personas SET rut = ? WHERE id = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $nuevoRut, $personaId);
            $stmtUpdate->execute();
            $stmtUpdate->close();
            
            // Redirigir al listado con un mensaje de éxito
            header("Location: ../pages/personas_qr_list.php?success_edit_rut=1");
            exit;
        }
        
        $stmtCheck->close();
    } else {
        echo "El nuevo RUT no puede estar vacío.";
    }
} else {
    echo "ID de persona o nuevo RUT no proporcionado.";
}

$conexion->close();
?>
