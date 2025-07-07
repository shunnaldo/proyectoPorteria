<?php
$host = "15.235.114.116"; 
$usuario = "fomentol_practica";  
$clave = "CASAEMPRENDER2025**";         
$base_de_datos = "fomentol_CORAJE"; 

$conn = new mysqli($host, $usuario, $clave, $base_de_datos);
$conn->set_charset("utf8");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>