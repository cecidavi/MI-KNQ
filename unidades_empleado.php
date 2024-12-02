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

    // Consulta para obtener los datos de la unidad asignada al operador
    $sql_unidad = "SELECT u.numero_unidad, f.nombre_fabrica, ou.fecha_asignacion
                   FROM unidades u
                   JOIN operador_unidad ou ON u.id_unidad = ou.id_unidad
                   JOIN fabricas f ON u.id_fabrica = f.id_fabrica
                   WHERE ou.id_operador = ? AND ou.fecha_desasignacion IS NULL";
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
    $sql_historial = "SELECT u.numero_unidad, f.nombre_fabrica, ou.fecha_asignacion, ou.fecha_desasignacion
                      FROM unidades u
                      JOIN operador_unidad ou ON u.id_unidad = ou.id_unidad
                      JOIN fabricas f ON u.id_fabrica = f.id_fabrica
                      WHERE ou.id_operador = ?
                      ORDER BY ou.fecha_asignacion";
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
    // Actualizar los datos del empleado en la base de datos
    if (isset($_POST['actualizar_empleado'])) {
        $nombre = $_POST['nombre'];
        $apellido_paterno = $_POST['apellido_paterno'];
        $apellido_materno = $_POST['apellido_materno'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $rfc = $_POST['rfc'];
        $nss = $_POST['nss'];
        $curp = $_POST['curp'];
        $edad = $_POST['edad'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $domicilio = $_POST['domicilio'];
        $salario = $_POST['salario'];
        $fecha_ingreso = $_POST['fecha_ingreso'];
        $estado = $_POST['estado'];
        $id_departamento = $_POST['id_departamento'];

        $sql = "UPDATE empleados SET 
                nombre = ?, 
                apellido_paterno = ?, 
                apellido_materno = ?, 
                fecha_nacimiento = ?, 
                rfc = ?, 
                nss = ?, 
                curp = ?, 
                edad = ?, 
                telefono = ?, 
                correo = ?, 
                domicilio = ?, 
                salario = ?, 
                fecha_ingreso = ?, 
                estado = ?, 
                id_departamento = ? 
                WHERE id_empleado = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssssssi", $nombre, $apellido_paterno, $apellido_materno, $fecha_nacimiento, $rfc, $nss, $curp, $edad, $telefono, $correo, $domicilio, $salario, $fecha_ingreso, $estado, $id_departamento, $id_empleado);

        if ($stmt->execute()) {
            echo "<p>Datos actualizados correctamente.</p>";
        } else {
            echo "<p>Error al actualizar los datos: " . $conn->error . "</p>";
        }
    }

    // Asignar nueva unidad al operador
    if (isset($_POST['asignar_unidad'])) {
        $id_unidad = $_POST['id_unidad'];
        $fecha_asignacion = $_POST['fecha_asignacion'];

        // Desasignar la unidad actual
        if ($unidad) {
            $sql_desasignar = "UPDATE operador_unidad SET fecha_desasignacion = ? WHERE id_operador_unidad = ?";
            $stmt_desasignar = $conn->prepare($sql_desasignar);
            $stmt_desasignar->bind_param("si", $fecha_asignacion, $unidad['id_operador_unidad']);
            $stmt_desasignar->execute();
        }

        // Asignar la nueva unidad
        $sql_asignar = "INSERT INTO operador_unidad (id_operador, id_unidad, fecha_asignacion) VALUES (?, ?, ?)";
        $stmt_asignar = $conn->prepare($sql_asignar);
        $stmt_asignar->bind_param("iis", $id_empleado, $id_unidad, $fecha_asignacion);

        if ($stmt_asignar->execute()) {
            echo "<p>Unidad asignada correctamente.</p>";
        } else {
            echo "<p>Error al asignar la unidad: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Empleado - MI KNQ</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Asegúrate de que esta ruta es correcta -->
    <link rel="stylesheet" href="path/to/Empleados.css"> <!-- Asegúrate de que esta ruta es correcta -->
</head>
<body>
    <div class="container">
        <h2>Unidades del Empleado</h2>

        <?php if ($unidad): ?>
            <h3>Unidad Asignada</h3>
            <p><strong>Número de Unidad:</strong> <?php echo $unidad['numero_unidad']; ?></p>
            <p><strong>Fábrica:</strong> <?php echo $unidad['nombre_fabrica']; ?></p>
            <p><strong>Fecha de Asignación:</strong> <?php echo $unidad['fecha_asignacion']; ?></p>
        <?php else: ?>
            <p>Este empleado no tiene una unidad asignada actualmente.</p>
        <?php endif; ?>

        <h3>Asignar Nueva Unidad</h3>
        <form method="POST" action="">
            <input type="hidden" name="asignar_unidad" value="1">
            <div class="form-group">
                <label for="id_unidad">Unidad:</label>
                <select class="form-control" id="id_unidad" name="id_unidad" required>
                    <?php
                    // Consulta para obtener las unidades disponibles
                    $sql_unidades = "SELECT id_unidad, numero_unidad FROM unidades";
                    $result_unidades = $conn->query($sql_unidades);

                    if ($result_unidades->num_rows > 0) {
                        while ($unidad = $result_unidades->fetch_assoc()) {
                            echo "<option value='" . $unidad['id_unidad'] . "'>" . $unidad['numero_unidad'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_asignacion">Fecha de Asignación:</label>
                <input type="date" class="form-control" id="fecha_asignacion" name="fecha_asignacion" required>
            </div>
            <button type="submit" class="btn btn-primary">Asignar Unidad</button>
        </form>

        <h3>Historial de Unidades</h3>
        <?php if (count($historial) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Número de Unidad</th>
                        <th>Fábrica</th>
                        <th>Fecha de Asignación</th>
                        <th>Fecha de Desasignación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $item): ?>
                        <tr>
                            <td><?php echo $item['numero_unidad']; ?></td>
                            <td><?php echo $item['nombre_fabrica']; ?></td>
                            <td><?php echo $item['fecha_asignacion']; ?></td>
                            <td><?php echo $item['fecha_desasignacion'] ?: 'N/A'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay historial de unidades asignadas para este empleado.</p>
        <?php endif; ?>
    </div>
    <script src="path/to/bootstrap.min.js"></script> <!-- Asegúrate de que esta ruta es correcta -->
</body>
</html>

<?php
include('includes/footer.php');
?>
