<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');
include('Almacen_header.php');

// Obtener las herramientas existentes para ver si están disponibles
$sql_herramientas = "SELECT * FROM herramientas";
$result_herramientas = $conn->query($sql_herramientas);

// Obtener los empleados disponibles para asignar herramientas
$sql_empleados = "SELECT * FROM empleados";
$result_empleados = $conn->query($sql_empleados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Equipos y Herramientas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Almacen_unidades.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Agregar Equipo al Almacén</h2>
<div class="container">
    <form id="formAgregarHerramienta">
        <div class="form-group">
            <label for="nombre">Nombre de la Herramienta:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" class="form-control">
                <option value="disponible">Disponible</option>
                <option value="prestada">Prestada</option>
            </select>
        </div>

        <input type="submit" value="Agregar Herramienta" class="btn btn-primary">
    </form>

    <h2>Asignar Herramienta a Empleado</h2>
    <form id="formAsignarHerramienta">
        <div class="form-group">
            <label for="empleado">Seleccionar Empleado:</label>
            <select id="empleado" name="empleado" class="form-control" required>
                <option value="">Seleccionar Empleado</option>
                <?php while ($row_empleado = $result_empleados->fetch_assoc()): ?>
                    <option value="<?= $row_empleado['id_empleado'] ?>"><?= $row_empleado['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="herramienta_asignar">Seleccionar Herramienta:</label>
            <select id="herramienta_asignar" name="herramienta_asignar" class="form-control" required>
                <option value="">Seleccionar Herramienta</option>
                <?php while ($row_herramienta = $result_herramientas->fetch_assoc()): ?>
                    <option value="<?= $row_herramienta['id_herramienta'] ?>"><?= $row_herramienta['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <input type="submit" value="Asignar Herramienta" class="btn btn-secondary">
    </form>

    <h2>Consultar Estado del Equipo en Taller</h2>
    <form id="formEstadoHerramienta">
        <div class="form-group">
            <label for="herramienta">Seleccionar Herramienta:</label>
            <select id="herramienta" name="herramienta" class="form-control">
                <option value="">Seleccionar Herramienta</option>
                <?php while ($row_herramienta = $result_herramientas->fetch_assoc()): ?>
                    <option value="<?= $row_herramienta['id_herramienta'] ?>"><?= $row_herramienta['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <input type="submit" value="Consultar Estado" class="btn btn-secondary">
    </form>

    <h3>Estado de Herramienta</h3>
    <div id="estadoHerramienta">
        <!-- Aquí se mostrará el estado de la herramienta seleccionada -->
    </div>

    <h2>Historial de Préstamos</h2>
    <form id="formHistorialPrestamos">
        <div class="form-group">
            <label for="fecha_prestamo">Seleccionar Fecha:</label>
            <input type="date" id="fecha_prestamo" name="fecha_prestamo" class="form-control" required>
        </div>

        <input type="submit" value="Ver Historial" class="btn btn-info">
    </form>

    <div id="historialPrestamos">
        <!-- Aquí se mostrará el historial de préstamos -->
    </div>
</div>

<script>
$('#formAgregarHerramienta').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    $.ajax({
        type: "POST",
        url: "procesar_agregar_herramienta.php",
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload(); // Recarga la página para ver las herramientas actualizadas
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert("Error al agregar la herramienta.");
        }
    });
});

$('#formAsignarHerramienta').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    $.ajax({
        type: "POST",
        url: "procesar_asignar_herramienta.php",
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload(); // Recarga la página para ver las herramientas actualizadas
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert("Error al asignar la herramienta.");
        }
    });
});

$('#formEstadoHerramienta').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    $.ajax({
        type: "GET",
        url: "consultar_estado_herramienta.php",
        data: $(this).serialize(),
        success: function(response) {
            $('#estadoHerramienta').html(response);
        },
        error: function() {
            alert("Error al consultar el estado de la herramienta.");
        }
    });
});

$('#formHistorialPrestamos').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    $.ajax({
        type: "GET",
        url: "consultar_historial_prestamos.php",
        data: $(this).serialize(),
        success: function(response) {
            $('#historialPrestamos').html(response);
        },
        error: function() {
            alert("Error al consultar el historial de préstamos.");
        }
    });
});
</script>

</body>
</html>
