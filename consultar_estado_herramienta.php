<?php
include('conexion.php');

// Obtiene el ID de la herramienta
$id_herramienta = $_GET['id_herramienta'];

// Consulta el estado de la herramienta
$sql_estado_herramienta = "SELECT * FROM herramientas WHERE id_herramienta = '$id_herramienta'";
$result_estado = $conn->query($sql_estado_herramienta);

if ($result_estado->num_rows > 0) {
    $row = $result_estado->fetch_assoc();
    echo "Estado de la herramienta: " . $row['estado'];
} else {
    echo "No se encontró la herramienta.";
}

$conn->close();
?>
