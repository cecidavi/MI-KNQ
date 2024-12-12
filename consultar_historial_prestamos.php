<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');

// Habilitar el registro de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];

    // Consulta para obtener el historial de préstamos
    $sql_historial = "
        SELECT 
            prestamos_herramientas.fecha_prestamo,
            prestamos_herramientas.nombre_persona,
            herramientas.nombre AS nombre_herramienta,
            herramientas.estado
        FROM 
            prestamos_herramientas
        JOIN 
            herramientas ON prestamos_herramientas.id_herramienta = herramientas.id_herramienta
        WHERE 
            prestamos_herramientas.fecha_prestamo = ?
    ";
    $stmt = $conn->prepare($sql_historial);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result_historial = $stmt->get_result();

    if ($result_historial->num_rows > 0) {
        echo "<table class='table'>";
        echo "<thead><tr><th>Fecha</th><th>Empleado</th><th>Herramienta</th><th>Estado</th></tr></thead>";
        echo "<tbody>";
        while ($row_historial = $result_historial->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row_historial['fecha_prestamo'] . "</td>";
            echo "<td>" . $row_historial['nombre_persona'] . "</td>";
            echo "<td>" . $row_historial['nombre_herramienta'] . "</td>";
            echo "<td>" . $row_historial['estado'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "No se encontraron registros para la fecha seleccionada.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Fecha no proporcionada.";
}
?>
