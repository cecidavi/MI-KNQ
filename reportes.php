<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('includes/header.php');
include('conexion.php'); // Incluir la conexión a la base de datos

// Consultas para reportes de empleados
$sql_empleados = "SELECT e.id_empleado, e.nombre, e.apellido_paterno, e.apellido_materno, u.numero_unidad, f.nombre_fabrica
                  FROM empleados e
                  LEFT JOIN operador_unidad ou ON e.id_empleado = ou.id_operador
                  LEFT JOIN unidades u ON ou.id_unidad = u.id_unidad
                  LEFT JOIN fabricas f ON u.id_fabrica = f.id_fabrica";
$result_empleados = $conn->query($sql_empleados);

// Consultas para estadísticas de uso de unidades
$sql_estadisticas = "SELECT u.numero_unidad, f.nombre_fabrica, COUNT(ou.id_operador) as num_asignaciones, 
                            MIN(ou.fecha_asignacion) as primera_asignacion, 
                            MAX(ou.fecha_asignacion) as ultima_asignacion, 
                            AVG(DATEDIFF(ou.fecha_desasignacion, ou.fecha_asignacion)) as uso_promedio
                     FROM unidades u
                     LEFT JOIN operador_unidad ou ON u.id_unidad = ou.id_unidad
                     LEFT JOIN fabricas f ON u.id_fabrica = f.id_fabrica
                     GROUP BY u.id_unidad";
$result_estadisticas = $conn->query($sql_estadisticas);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Estadísticas - MI KNQ</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Asegúrate de que esta ruta es correcta -->
    <link rel="stylesheet" href="path/to/Reportes.css"> <!-- Asegúrate de que esta ruta es correcta -->
</head>
<body>
    <div class="container">
        <h2>Reportes y Estadísticas</h2>
        <p>Aquí puedes ver reportes detallados sobre los empleados y estadísticas de uso de unidades.</p>

        <h3>Reportes de Empleados</h3>
        <?php
        if ($result_empleados->num_rows > 0) {
            echo "<table class='table'>";
            echo "<thead><tr><th>ID Empleado</th><th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Unidad</th><th>Fábrica</th></tr></thead>";
            echo "<tbody>";

            while ($row = $result_empleados->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id_empleado"] . "</td>";
                echo "<td>" . $row["nombre"] . "</td>";
                echo "<td>" . $row["apellido_paterno"] . "</td>";
                echo "<td>" . $row["apellido_materno"] . "</td>";
                echo "<td>" . $row["numero_unidad"] . "</td>";
                echo "<td>" . $row["nombre_fabrica"] . "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron datos de empleados.</p>";
        }
        ?>

        <h3>Estadísticas de Uso de Unidades</h3>
        <?php
        if ($result_estadisticas->num_rows > 0) {
            echo "<table class='table'>";
            echo "<thead><tr><th>Unidad</th><th>Fábrica</th><th>Número de Asignaciones</th><th>Primera Asignación</th><th>Última Asignación</th><th>Uso Promedio (días)</th></tr></thead>";
            echo "<tbody>";

            while ($row = $result_estadisticas->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["numero_unidad"] . "</td>";
                echo "<td>" . $row["nombre_fabrica"] . "</td>";
                echo "<td>" . $row["num_asignaciones"] . "</td>";
                echo "<td>" . $row["primera_asignacion"] . "</td>";
                echo "<td>" . $row["ultima_asignacion"] . "</td>";
                echo "<td>" . round($row["uso_promedio"], 2) . "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron estadísticas de uso de unidades.</p>";
        }
        ?>
    </div>
    <script src="path/to/bootstrap.bundle.min.js"></script> <!-- Asegúrate de que esta ruta es correcta -->
</body>
</html>
