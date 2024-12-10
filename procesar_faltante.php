<?php
session_start();
include('conexion.php');

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Obtiene los datos del formulario
$pieza_id = $_POST['pieza'];
$cantidad = $_POST['cantidad'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

// 1. Insertar el faltante en la tabla `faltantes`
$sql_faltante = "INSERT INTO faltantes (id_pieza, cantidad_faltante, descripcion, fecha, hora) 
                 VALUES ('$pieza_id', '$cantidad', '$descripcion', '$fecha', '$hora')";

if ($conn->query($sql_faltante) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Faltante registrado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar el faltante: ' . $conn->error]);
}

// Cierra la conexión
$conn->close();
?>
