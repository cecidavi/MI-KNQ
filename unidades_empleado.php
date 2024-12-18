<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('includes/header.php');
include('conexion.php'); // Incluir la conexión a la base de datos

// Verifica si se ha proporcionado un id_empleado en la URL
if (isset($_GET['id'])) {
    $id_empleado = $_GET['id'];

    // Consulta para obtener los datos del empleado
    $sql = "SELECT * FROM empleados WHERE id_empleado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $empleado = $result->fetch_assoc();
    } else {
        echo "<p>No se encontraron datos para el empleado con ID $id_empleado.</p>";
        exit();
    }

    // Consulta para obtener los datos de la unidad asignada al operador (utilizando la tabla 'asignaciones')
    $sql_unidad = "SELECT u.numero_unidad, f.nombre_fabrica, a.fecha_asignacion, a.id_asignacion
                   FROM unidades u
                   JOIN asignaciones a ON u.id_unidad = a.id_unidad
                   JOIN fabricas f ON a.id_fabrica = f.id_fabrica
                   WHERE a.id_operador = ? AND a.fecha_desasignacion IS NULL";
    $stmt_unidad = $conn->prepare($sql_unidad);
    $stmt_unidad->bind_param("i", $id_empleado);
    $stmt_unidad->execute();
    $result_unidad = $stmt_unidad->get_result();

    if ($result_unidad->num_rows > 0) {
        $unidad = $result_unidad->fetch_assoc();
    } else {
        $unidad = null;
    }

    // Consulta para obtener el historial de unidades asignadas
    $sql_historial = "SELECT u.numero_unidad, f.nombre_fabrica, a.fecha_asignacion, a.fecha_desasignacion, a.turno
    FROM unidades u
    JOIN asignaciones a ON u.id_unidad = a.id_unidad
    JOIN fabricas f ON a.id_fabrica = f.id_fabrica
    WHERE a.id_operador = ?
    ORDER BY a.fecha_asignacion";

    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->bind_param("i", $id_empleado);
    $stmt_historial->execute();
    $result_historial = $stmt_historial->get_result();
    $historial = $result_historial->fetch_all(MYSQLI_ASSOC);
} else {
    echo "<p>ID de empleado no proporcionado.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Asignar nueva unidad y fábrica al operador
    if (isset($_POST['asignar_unidad'])) {
        $id_unidad = $_POST['id_unidad'];
        $id_fabrica = $_POST['id_fabrica'];
        $turno = $_POST['turno'];
        $fecha_asignacion = $_POST['fecha_asignacion'];

        // Desasignar la unidad actual si existe
        if ($unidad) {
            $sql_desasignar = "UPDATE asignaciones SET fecha_desasignacion = ? WHERE id_operador = ? AND fecha_desasignacion IS NULL";
            $stmt_desasignar = $conn->prepare($sql_desasignar);
            $stmt_desasignar->bind_param("si", $fecha_asignacion, $id_empleado);
            $stmt_desasignar->execute();
        }

        // Asignar la nueva unidad y actualizar la fábrica
        $sql_asignar = "INSERT INTO asignaciones (id_operador, id_unidad, id_fabrica, turno, fecha_asignacion) VALUES (?, ?, ?, ?, ?)";
        $stmt_asignar = $conn->prepare($sql_asignar);
        $stmt_asignar->bind_param("iiiss", $id_empleado, $id_unidad, $id_fabrica, $turno, $fecha_asignacion);

        if ($stmt_asignar->execute()) {
            echo "<p>Unidad y fábrica asignadas correctamente.</p>";
            // Redirigir para evitar resubmisión del formulario
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id_empleado);
            exit();
        } else {
            echo "<p>Error al asignar la unidad o fábrica: " . $conn->error . "</p>";
        }
    }

    // Desasignar la unidad
    if (isset($_POST['desasignar'])) {
        if ($unidad) {
            $fecha_desasignacion = date("Y-m-d"); // O la fecha que desees
            $sql_desasignar = "UPDATE asignaciones SET fecha_desasignacion = ? WHERE id_operador = ? AND fecha_desasignacion IS NULL";
            $stmt_desasignar = $conn->prepare($sql_desasignar);
            $stmt_desasignar->bind_param("si", $fecha_desasignacion, $id_empleado);
            $stmt_desasignar->execute();

            echo "<p>Unidad desasignada correctamente.</p>";
            // Redirigir para evitar resubmisión del formulario
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id_empleado);
            exit();
        } else {
            echo "<p>No hay unidad asignada para desasignar.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidades KNQ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/unidades_empleado.css"> <!-- Asegúrate de que la ruta es correcta -->
</head>
<body>
    <div class="container">
        <h2>Asignación de Unidad y Fábrica</h2>
        <?php if ($unidad): ?>
            <p>Unidad actual: <?php echo htmlspecialchars($unidad['numero_unidad']); ?> - <?php echo htmlspecialchars($unidad['nombre_fabrica']); ?> (Asignada el <?php echo htmlspecialchars($unidad['fecha_asignacion']); ?>)</p>
            <form action="" method="post">
                <input type="hidden" name="desasignar" value="1">
                <input type="submit" value="Desasignar Unidad" class="btn btn-danger">
            </form>
        <?php else: ?>
            <p>No hay unidad asignada actualmente.</p>
        <?php endif; ?>

        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Unidad:</label>
                <select name="id_unidad" class="form-control">
                    <?php
                    $sql_unidades = "SELECT id_unidad, numero_unidad FROM unidades";
                    $result_unidades = $conn->query($sql_unidades);
                    while ($row = $result_unidades->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id_unidad']) . "'>" . htmlspecialchars($row['numero_unidad']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Fábrica:</label>
                <select name="id_fabrica" class="form-control">
                    <?php
                    $sql_fabricas = "SELECT id_fabrica, nombre_fabrica FROM fabricas";
                    $result_fabricas = $conn->query($sql_fabricas);
                    while ($row = $result_fabricas->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id_fabrica']) . "'>" . htmlspecialchars($row['nombre_fabrica']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Turno:</label>
                <select name="turno" class="form-control">
                    <option value="mañana">Mañana</option>
                    <option value="tarde">Tarde</option>
                    <option value="noche">Noche</option>
                    <option value="Completo">Completo</option>
                    <option value="Apoyo">Apoyo</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de Asignación:</label>
                <input type="date" name="fecha_asignacion" class="form-control" required>
            </div>
            <input type="submit" name="asignar_unidad" value="Asignar" class="btn btn-success">
        </form>

        <h2>Historial de Unidades Asignadas</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Unidad</th>
                    <th>Fábrica</th>
                    <th>Turno</th>
                    <th>Fecha de Asignación</th>
                    <th>Fecha de Desasignación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial as $registro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registro['numero_unidad']); ?></td>
                        <td><?php echo htmlspecialchars($registro['nombre_fabrica']); ?></td>
                        <td><?php echo htmlspecialchars($registro['turno']); ?></td>
                        <td><?php echo htmlspecialchars($registro['fecha_asignacion']); ?></td>
                        <td><?php echo htmlspecialchars($registro['fecha_desasignacion']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

