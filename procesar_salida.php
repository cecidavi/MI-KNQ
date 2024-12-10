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

// 1. Verificar la cantidad disponible en la tabla piezas
$sql_verificar_cantidad = "SELECT cantidad FROM piezas WHERE id_pieza = '$pieza_id'";
$result_verificar = $conn->query($sql_verificar_cantidad);
$row_pieza = $result_verificar->fetch_assoc();

if ($row_pieza['cantidad'] < $cantidad) {
    echo json_encode(['success' => false, 'message' => 'No hay suficiente cantidad de piezas disponibles.']);
    exit();
}

// 2. Insertar la salida en la tabla `salidas`
$sql_salidas = "INSERT INTO salidas (id_pieza, cantidad, fecha, hora, unidad, nombre_persona) 
                VALUES ('$pieza_id', '$cantidad', '$fecha', '$hora', '$unidad', '$nombre_persona')";

if ($conn->query($sql_salidas) === TRUE) {
    // 3. Actualizar la cantidad de piezas en la tabla `piezas`
    $sql_actualizar_pieza = "UPDATE piezas 
                             SET cantidad = cantidad - $cantidad 
                             WHERE id_pieza = '$pieza_id'";

    if ($conn->query($sql_actualizar_pieza) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Salida registrada correctamente y cantidad de piezas actualizada.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la cantidad de piezas: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar la salida: ' . $conn->error]);
}

// Cierra la conexión
$conn->close();
?>
