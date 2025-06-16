<?php
require_once 'conexion.php';

// Verificar si se ha recibido el ID de la persona
if (isset($_GET['id_persona'])) {
    $personaId = $_GET['id_persona'];

    // Consulta para obtener el nombre del archivo QR
    $sql = "SELECT qr_code FROM qr_temporal WHERE persona_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $personaId);
    $stmt->execute();
    $stmt->bind_result($qrCode);
    $stmt->fetch();
    
    // Cerrar la consulta SELECT
    $stmt->close();
    
    // Si existe el QR, proceder a eliminarlo
    if ($qrCode) {
        // Eliminar el archivo QR de la carpeta
        $qrFilePath = 'qr_codes/' . basename($qrCode);
        if (file_exists($qrFilePath)) {
            unlink($qrFilePath); // Elimina el archivo QR
        }

        // Eliminar el registro de la base de datos
        $deleteSql = "DELETE FROM qr_temporal WHERE persona_id = ?";
        $deleteStmt = $conexion->prepare($deleteSql);
        $deleteStmt->bind_param("i", $personaId);
        $deleteStmt->execute();
        
        // Cerrar la consulta DELETE
        $deleteStmt->close();

        // Redirigir al listado de personas después de la eliminación exitosa
        header("Location: ../pages/personas_qr_list.php?success=1");
        exit; // Asegúrate de detener la ejecución del script después de la redirección
    } else {
        echo "No se encontró el QR para esta persona.";
    }

    // Cerrar la conexión de base de datos
    $conexion->close();
} else {
    echo "ID de persona no proporcionado.";
}
?>
