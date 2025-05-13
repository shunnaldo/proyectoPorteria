<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo_electronico'];
    $rol = $_POST['rol'];
    $nueva_contrasena = $_POST['contrasena'];

    if (!empty($nueva_contrasena)) {
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre = ?, correo_electronico = ?, rol = ?, contrasena = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $correo, $rol, $hash, $id);
    } else {
        $sql = "UPDATE usuarios SET nombre = ?, correo_electronico = ?, rol = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../pages/ver_usuarios.php");
        exit;
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
}
?>
