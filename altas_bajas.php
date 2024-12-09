<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('includes/header.php');
include('conexion.php'); // Incluir la conexión a la base de datos

// Variables para los mensajes de éxito o error
$success_message = '';
$error_message = '';

// Manejo de formulario para agregar o editar empleado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['empleado_action'])) {
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
    $fecha_baja = $_POST['fecha_baja'];
    $estado = $_POST['estado'];
    $id_departamento = $_POST['id_departamento'];

    if (isset($_POST['id_empleado']) && !empty($_POST['id_empleado'])) {
        // Editar empleado
        $id_empleado = $_POST['id_empleado'];
        $sql_update = "UPDATE empleados SET nombre = ?, apellido_paterno = ?, apellido_materno = ?, fecha_nacimiento = ?, rfc = ?, nss = ?, curp = ?, edad = ?, telefono = ?, correo = ?, domicilio = ?, salario = ?, fecha_ingreso = ?, fecha_baja = ?, estado = ?, id_departamento = ? WHERE id_empleado = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssssssssdssii", $nombre, $apellido_paterno, $apellido_materno, $fecha_nacimiento, $rfc, $nss, $curp, $edad, $telefono, $correo, $domicilio, $salario, $fecha_ingreso, $fecha_baja, $estado, $id_departamento, $id_empleado);

        if ($stmt_update->execute()) {
            $success_message = "Empleado actualizado correctamente.";
        } else {
            $error_message = "Error al actualizar el empleado: " . $conn->error;
        }

        $stmt_update->close();
    } else {
        // Agregar nuevo empleado
        $sql_insert = "INSERT INTO empleados (nombre, apellido_paterno, apellido_materno, fecha_nacimiento, rfc, nss, curp, edad, telefono, correo, domicilio, salario, fecha_ingreso, fecha_baja, estado, id_departamento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssssssssssssdssi", $nombre, $apellido_paterno, $apellido_materno, $fecha_nacimiento, $rfc, $nss, $curp, $edad, $telefono, $correo, $domicilio, $salario, $fecha_ingreso, $fecha_baja, $estado, $id_departamento);

        if ($stmt_insert->execute()) {
            $success_message = "Empleado agregado correctamente.";
        } else {
            $error_message = "Error al agregar el empleado: " . $conn->error;
        }

        $stmt_insert->close();
    }
}

// Manejo de eliminación de empleado
if (isset($_GET['delete_id'])) {
    $id_empleado = $_GET['delete_id'];
    $sql_delete = "DELETE FROM empleados WHERE id_empleado = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_empleado);

    if ($stmt_delete->execute()) {
        $success_message = "Empleado eliminado correctamente.";
    } else {
        $error_message = "Error al eliminar el empleado: " . $conn->error;
    }

    $stmt_delete->close();
}

// Consulta para obtener los datos de los empleados
$sql = "SELECT id_empleado, nombre, apellido_paterno, apellido_materno, fecha_nacimiento, rfc, nss, curp, edad, telefono, correo, domicilio, salario, fecha_ingreso, fecha_baja, estado, id_departamento FROM empleados";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - MI KNQ</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Asegúrate de que esta ruta es correcta -->
    <link rel="stylesheet" href="path/to/AltasBajas.css"> <!-- Asegúrate de que esta ruta es correcta -->
</head>
<body>
    <div class="container">
        <h2>Altas y Bajas</h2>
        <p>Aquí puedes gestionar las altas y bajas de los empleados.</p>

        <?php
        if (!empty($success_message)) {
            echo "<div class='alert alert-success'>$success_message</div>";
        }
        if (!empty($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }
        ?>

        <h3>Agregar/Editar Empleado</h3>
        <form action="altas_bajas.php" method="post">
            <input type="hidden" name="id_empleado" id="id_empleado">
            <input type="hidden" name="empleado_action" value="manage_empleado">
            <label>Nombre:</label>
            <input type="text" name="nombre" id="nombre" required class="form-control" autocomplete="off"><br>
            <label>Apellido Paterno:</label>
            <input type="text" name="apellido_paterno" id="apellido_paterno" required class="form-control" autocomplete="off"><br>
            <label>Apellido Materno:</label>
            <input type="text" name="apellido_materno" id="apellido_materno" required class="form-control" autocomplete="off"><br>
            <label>Fecha de Nacimiento:</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required class="form-control" autocomplete="off"><br>
            <label>RFC:</label>
            <input type="text" name="rfc" id="rfc" required class="form-control" autocomplete="off" required maxlength="13"><br>
            <label>NSS:</label>
            <input type="text" name="nss" id="nss" required class="form-control" autocomplete="off" required maxlength="11"><br>
            <label>CURP:</label>
            <input type="text" name="curp" id="curp" required class="form-control" autocomplete="off" required maxlength="18"><br>
            <label>Edad:</label>
            <input type="number" name="edad" id="edad" class="form-control" autocomplete="off" required maxlength="10"><br>
            <label>Teléfono:</label>
            <input type="text" name="telefono" id="telefono" class="form-control" autocomplete="off" require_once maxlength="10"><br>
            <label>Correo:</label>
            <input type="email" name="correo" id="correo" class="form-control" autocomplete="off"><br>
            <label>Domicilio:</label>
            <input type="text" name="domicilio" id="domicilio" class="form-control" autocomplete="off"><br>
            <label>Salario:</label>
            <input type="number" step="0.01" name="salario" id="salario" class="form-control" autocomplete="off"><br>
            <label>Fecha de Ingreso:</label>
            <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" autocomplete="off"><br>
            <label>Fecha de Baja:</label>
            <input type="date" name="fecha_baja" id="fecha_baja" class="form-control" autocomplete="off"><br>
            <label>Estado:</label>
            <select name="estado" id="estado" class="form-control">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select><br>
            <select name="id_departamento" id="id_departamento" class="form-control">
                <?php
                $conn = new mysqli("localhost", "root", "", "check");
                $sql_departamentos = "SELECT id_departamento, nombre_departamento FROM departamentos";
                $result_departamentos = $conn->query($sql_departamentos);
                while ($row_departamento = $result_departamentos->fetch_assoc()) {
                    echo "<option value='" . $row_departamento['id_departamento'] . "'>" . $row_departamento['nombre_departamento'] . "</option>";
                }
                $conn->close();
                ?>
            </select><br>
        </form>

        <h3>Lista de Empleados</h3>
        <?php
        if ($result->num_rows > 0) {
            echo "<table class='table'>";
            echo "<thead><tr><th>ID</th><th>Nombre</th><th>Apellido Paterno</th><th>Apellido Materno</th><th>Fecha de Nacimiento</th><th>RFC</th><th>NSS</th><th>CURP</th><th>Edad</th><th>Teléfono</th><th>Correo</th><th>Domicilio</th><th>Salario</th><th>Fecha de Ingreso</th><th>Fecha de Baja</th><th>Estado</th><th>Departamento</th><th>Acciones</th></tr></thead>";
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_empleado'] . "</td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . $row['apellido_paterno'] . "</td>";
                echo "<td>" . $row['apellido_materno'] . "</td>";
                echo "<td>" . $row['fecha_nacimiento'] . "</td>";
                echo "<td>" . $row['rfc'] . "</td>";
                echo "<td>" . $row['nss'] . "</td>";
                echo "<td>" . $row['curp'] . "</td>";
                echo "<td>" . $row['edad'] . "</td>";
                echo "<td>" . $row['telefono'] . "</td>";
                echo "<td>" . $row['correo'] . "</td>";
                echo "<td>" . $row['domicilio'] . "</td>";
                echo "<td>" . $row['salario'] . "</td>";
                echo "<td>" . $row['fecha_ingreso'] . "</td>";
                echo "<td>" . $row['fecha_baja'] . "</td>";
                echo "<td>" . $row['estado'] . "</td>";
                echo "<td>" . $row['id_departamento'] . "</td>";
                echo "<td>";
                echo "<a href='altas_bajas.php?delete_id=" . $row['id_empleado'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este empleado?\");'>Eliminar</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No hay empleados registrados.</p>";
        }
        ?>
    </div>
</body>
</html>
