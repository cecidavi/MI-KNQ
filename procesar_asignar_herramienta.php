<?php
session_start();
include('conexion.php');

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Obtiene los datos del formulario
$id_empleado = $_POST['empleado'];
$id_herramienta = $_POST['herramienta_asignar'];
$fecha_prestamo = date('Y-m-d'); // Puedes cambiar la forma en que se obtiene la fecha si es necesario

// Verificar que los datos no estén vacíos
if (empty($id_empleado) || empty($id_herramienta)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit();
}

// Insertar el préstamo en la base de datos
$sql_asignar_herramienta = "INSERT INTO prestamos_herramientas (id_herramienta, nombre_persona, fecha_prestamo) 
                            VALUES ('$id_herramienta', (SELECT nombre FROM empleados WHERE id_empleado = '$id_empleado'), '$fecha_prestamo')";

if ($conn->query($sql_asignar_herramienta) === TRUE) {
    // Actualizar el estado de la herramienta
    $sql_actualizar_estado = "UPDATE herramientas SET estado='prestada' WHERE id_herramienta='$id_herramienta'";
    if ($conn->query($sql_actualizar_estado) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Herramienta asignada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado de la herramienta: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al asignar la herramienta: ' . $conn->error]);
}

$conn->close();
?>
