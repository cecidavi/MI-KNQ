<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('includes/header.php');
include('conexion.php'); // Incluir la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - MI KNQ</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Asegúrate de que esta ruta es correcta -->
    <link rel="stylesheet" href="path/to/Empleados.css"> <!-- Asegúrate de que esta ruta es correcta -->
</head>
<body>
    <div class="container">
        <h2>Gestión de Empleados</h2>
        <p>Aquí puedes gestionar los empleados.</p>

        <?php
        // Consulta para obtener los datos de los empleados y la unidad asignada actual (si existe)
        $sql = "SELECT e.id_empleado, e.nombre, e.apellido_paterno, e.apellido_materno, e.telefono, u.numero_unidad 
                FROM empleados e 
                LEFT JOIN asignaciones a ON e.id_empleado = a.id_operador AND a.fecha_desasignacion IS NULL
                LEFT JOIN unidades u ON a.id_unidad = u.id_unidad";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table'>";
            echo "<thead><tr><th>ID</th><th>Unidad</th><th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Teléfono</th><th>Acciones</th><th>Modificar Unidad</th></tr></thead>";
            echo "<tbody>";

            // Salida de datos de cada fila
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id_empleado"] . "</td>";
                echo "<td>" . ($row["numero_unidad"] ? $row["numero_unidad"] : "Sin unidad asignada") . "</td>";
                echo "<td>" . $row["nombre"] . "</td>";
                echo "<td>" . $row["apellido_paterno"] . "</td>";
                echo "<td>" . $row["apellido_materno"] . "</td>";
                echo "<td>" . $row["telefono"] . "</td>";
                echo "<td><a href='empleado.php?id=" . $row["id_empleado"] . "' class='btn btn-info'>Ver/Modificar</a></td>";
                echo "<td><a href='unidades_empleado.php?id=" . $row["id_empleado"] . "' class='btn btn-danger'>Modificar Unidad</a></td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron empleados.</p>";
        }

        $conn->close();
        ?>
    </div>
    <?php include('includes/footer.php'); ?>
</body>
</html>
