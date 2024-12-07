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

    // Consulta ajustada para obtener los datos de la unidad asignada al operador desde la nueva tabla 'asignaciones'
    $sql_unidad = "SELECT u.numero_unidad, f.nombre_fabrica, a.turno, a.fecha_asignacion
                   FROM asignaciones a
                   JOIN unidades u ON a.id_unidad = u.id_unidad
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
} else {
    echo "<p>ID de empleado no proporcionado.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Actualizar los datos del empleado en la base de datos
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Empleado - MI KNQ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/Empleado.css"> <!-- Asegúrate de que esta ruta sea correcta -->
</head>
<body>
    <div class="container">
        <h2>Detalles del Empleado</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $empleado['nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellido_paterno" class="form-label">Apellido Paterno:</label>
                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?php echo $empleado['apellido_paterno']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellido_materno" class="form-label">Apellido Materno:</label>
                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?php echo $empleado['apellido_materno']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $empleado['fecha_nacimiento']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="rfc" class="form-label">RFC:</label>
                <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $empleado['rfc']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nss" class="form-label">NSS:</label>
                <input type="text" class="form-control" id="nss" name="nss" value="<?php echo $empleado['nss']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="curp" class="form-label">CURP:</label>
                <input type="text" class="form-control" id="curp" name="curp" value="<?php echo $empleado['curp']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="edad" class="form-label">Edad:</label>
                <input type="number" class="form-control" id="edad" name="edad" value="<?php echo $empleado['edad']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $empleado['telefono']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo:</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $empleado['correo']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="domicilio" class="form-label">Domicilio:</label>
                <input type="text" class="form-control" id="domicilio" name="domicilio" value="<?php echo $empleado['domicilio']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="salario" class="form-label">Salario:</label>
                <input type="number" class="form-control" id="salario" name="salario" value="<?php echo $empleado['salario']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha_ingreso" class="form-label">Fecha de Ingreso:</label>
                <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo $empleado['fecha_ingreso']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select class="form-control" id="estado" name="estado">
                    <option value="activo" <?php echo ($empleado['estado'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                    <option value="inactivo" <?php echo ($empleado['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_departamento" class="form-label">Departamento:</label>
                <input type="text" class="form-control" id="id_departamento" name="id_departamento" value="<?php echo $empleado['id_departamento']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>

        <h2>Unidad Asignada</h2>
        <?php if ($unidad): ?>
            <p>Número de Unidad: <?php echo $unidad['numero_unidad']; ?></p>
            <p>Fábrica: <?php echo $unidad['nombre_fabrica']; ?></p>
            <p>Turno: <?php echo $unidad['turno']; ?></p>
            <p>Fecha de Asignación: <?php echo $unidad['fecha_asignacion']; ?></p>
        <?php else: ?>
            <p>El operador no tiene una unidad asignada actualmente.</p>
        <?php endif; ?>
    </div>
</body>
</html>
