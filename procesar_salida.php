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
$unidad = $_POST['unidad'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$nombre_persona = $_POST['nombre_persona'];

// 1. Insertar la salida en la tabla `salidas`
$sql_salidas = "INSERT INTO salidas (id_pieza, cantidad, fecha, hora, unidad, nombre_persona) 
                VALUES ('$pieza_id', '$cantidad', '$fecha', '$hora', '$unidad', '$nombre_persona')";

if ($conn->query($sql_salidas) === TRUE) {
    // 2. Actualizar la cantidad de piezas en la tabla `piezas`
    $sql_actualizar_pieza = "UPDATE piezas 
                             SET cantidad = cantidad - $cantidad 
                             WHERE id_pieza = '$pieza_id' AND cantidad >= $cantidad";

    if ($conn->query($sql_actualizar_pieza) === TRUE) {
        echo "Salida registrada correctamente y cantidad de piezas actualizada.";
    } else {
        echo "Error al actualizar la cantidad de piezas: " . $conn->error;
    }
} else {
    echo "Error al registrar la salida: " . $conn->error;
}

// Cierra la conexión
$conn->close();
?>
