<?php
require_once 'conexion.php';
session_start(); // Asegúrate de iniciar la sesión para obtener el usuario admin

// Verificar sesión y rol
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
    header("Location: ../pages/logintrabajador.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $estado = $_POST['estado'] ?? 'cerrado';

    if (empty($nombre) || empty($ubicacion)) {
        die("Nombre y ubicación son obligatorios.");
    }

    $sql = "INSERT INTO portones (nombre, ubicacion, estado) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $nombre, $ubicacion, $estado);

    if ($stmt->execute()) {
        // Obtener el ID del portón recién creado
        $porton_id = $stmt->insert_id;

        // Registrar el historial de creación
        $admin_id = $_SESSION["usuario"]["id"]; // El ID del admin que realizó la creación
        $accion = 'creación';
        $tabla_afectada = 'portones';
        $descripcion = "Se creó el portón con nombre $nombre y ubicación $ubicacion.";

        // Insertar en la tabla historial_cambios
        $stmt_historial = $conexion->prepare("INSERT INTO historial_cambios (usuario_id, accion, tabla_afectada, registro_id, descripcion, fecha) 
                                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt_historial->bind_param("issis", $admin_id, $accion, $tabla_afectada, $porton_id, $descripcion);
        $stmt_historial->execute();

        // Redirigir con éxito
        header("Location: ../pages/crear_porton.php?success=1");
        exit;
    } else {
        die("Error al insertar el portón: " . $conexion->error);
    }
}
?>
