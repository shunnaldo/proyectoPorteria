<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conexion->prepare("DELETE FROM tbl_patentes_bloqueadas WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: ../pages/listado_bloqueados.php?patente_eliminada=1");
        exit;
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
