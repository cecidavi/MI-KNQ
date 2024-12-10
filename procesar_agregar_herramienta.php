<?php
session_start();
include('conexion.php');

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Obtiene los datos del formulario
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$estado = $_POST['estado'];

// Insertar la nueva herramienta en la base de datos
$sql_insertar_herramienta = "INSERT INTO herramientas (nombre, descripcion, estado)
                              VALUES ('$nombre', '$descripcion', '$estado')";

if ($conn->query($sql_insertar_herramienta) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Herramienta agregada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar la herramienta: ' . $conn->error]);
}

$conn->close();
?>
