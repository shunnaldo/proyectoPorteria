<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {  // Aquí faltaba el paréntesis de cierre
    $id = intval($_GET['id']);
    
    // Verificar que el bloqueo existe
    $stmt = $conexion->prepare("SELECT id FROM blacklist WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Eliminar el bloqueo
        $deleteStmt = $conexion->prepare("DELETE FROM blacklist WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        
        if ($deleteStmt->execute()) {
            header("Location: ../pages/listado_bloqueados.php?success=1");
        } else {
            header("Location: ../pages/listado_bloqueados.php?error=1");
        }
    } else {
        header("Location: ../pages/listado_bloqueados.php?error=2");
    }
} else {
    header("Location: ../pages/listado_bloqueados.php");
}
exit;
?>