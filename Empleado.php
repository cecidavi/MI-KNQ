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
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> 
    <link rel="stylesheet" href="path/to/Empleados.css"> 
</head>
<body>
    <div class="container">
        <h2>Detalles del Empleado</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $empleado['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido_paterno">Apellido Paterno:</label>
                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="<?php echo $empleado['apellido_paterno']; ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido_materno">Apellido Materno:</label>
                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="<?php echo $empleado['apellido_materno']; ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $empleado['fecha_nacimiento']; ?>" required>
            </div>
            <div class="form-group">
                <label for="rfc">RFC:</label>
                <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $empleado['rfc']; ?>" required>
            </div>
            <div class="form-group">
                <label for="nss">NSS:</label>
                <input type="text" class="form-control" id="nss" name="nss" value="<?php echo $empleado['nss']; ?>" required>
            </div>
            <div class="form-group">
                <label for="curp">CURP:</label>
                <input type="text" class="form-control" id="curp" name="curp" value="<?php echo $empleado['curp']; ?>" required>
            </div>
            <div class="form-group">
                <label for="edad">Edad:</label>
                <input type="number" class="form-control" id="edad" name="edad" value="<?php echo $empleado['edad']; ?>" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $empleado['telefono']; ?>" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $empleado['correo']; ?>" required>
            </div>
            <div class="form-group">
                <label for="domicilio">Domicilio:</label>
                <input type="text" class="form-control" id="domicilio" name="domicilio" value="<?php echo $empleado['domicilio']; ?>" required>
            </div>
            <div class="form-group">
                <label for="salario">Salario:</label>
                <input type="number" step="0.01" class="form-control" id="salario" name="salario" value="<?php echo $empleado['salario']; ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso:</label>
                <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo $empleado['fecha_ingreso']; ?>" required>
            </div>
            <div class="form-group">
                <label for="estado">Estado:</label>
                <select class="form-control" id="estado" name="estado" required>
                    <option value="activo" <?php if ($empleado['estado'] == 'activo') echo 'selected'; ?>>Activo</option>
                    <option value="inactivo" <?php if ($empleado['estado'] == 'inactivo') echo 'selected'; ?>>Inactivo</option>
                </select>
            </div>
            <div class="form-group">
                <label for="id_departamento">Departamento:</label>
                <select class="form-control" id="id_departamento" name="id_departamento" required>
                    <?php
                    // Consulta para obtener los departamentos
                    $sql_departamentos = "SELECT id_departamento, nombre_departamento FROM departamentos";
                    $result_departamentos = $conn->query($sql_departamentos);

                    if ($result_departamentos->num_rows > 0) {
                        while ($departamento = $result_departamentos->fetch_assoc()) {
                            echo "<option value='" . $departamento['id_departamento'] . "'";
                            if ($departamento['id_departamento'] == $empleado['id_departamento']) echo " selected";
                            echo ">" . $departamento['nombre_departamento'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay departamentos disponibles</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>

        <?php if ($unidad): ?>
            <h3>Unidad Asignada</h3>
            <p><strong>Número de Unidad:</strong> <?php echo $unidad['numero_unidad']; ?></p>
            <p><strong>Fábrica:</strong> <?php echo $unidad['nombre_fabrica']; ?></p>
            <p><strong>Fecha de Asignación:</strong> <?php echo $unidad['fecha_asignacion']; ?></p>
        <?php else: ?>
            <p>Este empleado no tiene una unidad asignada actualmente.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php include('includes/footer.php'); ?>
