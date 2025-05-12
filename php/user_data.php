<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    // Si no hay sesión iniciada, puedes redirigir o manejarlo como quieras
    die("Acceso no autorizado. Usuario no ha iniciado sesión.");
}

$usuario_id = $_SESSION["usuario"]["id"];
$usuario_nombre = $_SESSION["usuario"]["nombre"];
$usuario_apellido = $_SESSION["usuario"]["apellido"];
$usuario_rol = $_SESSION["usuario"]["rol"];

// Si quieres, puedes juntar todo en un array también:
$usuario_data = [
    "id" => $usuario_id,
    "nombre" => $usuario_nombre,
    "apellido" => $usuario_apellido,
    "rol" => $usuario_rol
];
?>
