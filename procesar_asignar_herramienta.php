<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');

if (!empty($_POST['empleado']) && !empty($_POST['herramienta_asignar'])) {
    $empleado_id = $_POST['empleado'];
    $herramienta_id = $_POST['herramienta_asignar'];

    // Insertar el préstamo en la base de datos
    $sql_prestamo = "INSERT INTO prestamos (empleado, herramienta, fecha_prestamo) VALUES ((SELECT nombre FROM empleados WHERE id_empleado = ?), (SELECT nombre FROM herramientas WHERE id_herramienta = ?), NOW())";
    $stmt_prestamo = $conn->prepare($sql_prestamo);
    $stmt_prestamo->bind_param("ii", $empleado_id, $herramienta_id);

    if ($stmt_prestamo->execute()) {
        // Actualizar el estado de la herramienta a 'prestada'
        $sql_actualizar = "UPDATE herramientas SET estado = 'prestada' WHERE id_herramienta = ?";
        $stmt_actualizar = $conn->prepare($sql_actualizar);
        $stmt_actualizar->bind_param("i", $herramienta_id);
        $stmt_actualizar->execute();

        echo json_encode(['success' => true, 'message' => 'Herramienta asignada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al asignar la herramienta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
}
?>
