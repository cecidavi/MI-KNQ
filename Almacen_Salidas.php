<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');
include('Almacen_header.php');

// Obtener los departamentos (áreas) de la base de datos
$sql_departamentos = "SELECT * FROM departamentos";
$result_departamentos = $conn->query($sql_departamentos);

// Obtener las piezas para el formulario
$sql_piezas = "SELECT * FROM piezas";
$result_piezas = $conn->query($sql_piezas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Salida de Piezas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Almacen_unidades.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Registrar Salida de Piezas</h2>
<div class="container">
    <form id="formSalida">
        <div class="form-group">
            <label for="area">Seleccionar Área (Departamento):</label>
            <select id="area" name="area" class="form-control" onchange="cargarEmpleados()">
                <option value="">Seleccionar Área</option>
                <?php while ($row_departamento = $result_departamentos->fetch_assoc()): ?>
                    <option value="<?= $row_departamento['id_departamento'] ?>"><?= $row_departamento['nombre_departamento'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="trabajador">Seleccionar Trabajador:</label>
            <select id="trabajador" name="trabajador" class="form-control" onchange="actualizarNombrePersona()">
                <option value="">Seleccionar Trabajador</option>
            </select>
        </div>

        <div class="form-group">
            <label for="pieza">Seleccionar Pieza:</label>
            <select id="pieza" name="pieza" class="form-control">
                <option value="">Seleccionar Pieza</option>
                <?php while ($row_pieza = $result_piezas->fetch_assoc()): ?>
                    <option value="<?= $row_pieza['id_pieza'] ?>"><?= $row_pieza['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="unidad">Unidad:</label>
            <input type="text" id="unidad" name="unidad" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" class="form-control" required>
        </div>

        <!-- Campo oculto para el nombre de la persona (trabajador) -->
        <input type="hidden" id="nombre_persona" name="nombre_persona">

        <!-- Botón con diseño de Bootstrap -->
        <input type="submit" value="Registrar Salida" class="btn btn-primary">
    </form>

    <h3>Ver Salidas por Fecha</h3>
    <form id="formFecha">
        <div class="form-group">
            <label for="fecha_consulta">Seleccionar Fecha:</label>
            <input type="date" id="fecha_consulta" name="fecha_consulta" class="form-control" required>
        </div>
        <input type="submit" value="Consultar Salidas" class="btn btn-secondary">
    </form>

    <h3>Salidas de la Fecha Seleccionada</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Salida</th>
                <th>Pieza</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Nombre Persona</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody id="salidasFechaSeleccionada">
            <!-- Las salidas se llenarán aquí vía AJAX -->
        </tbody>
    </table>
</div>

<script>
function cargarEmpleados() {
    var area = document.getElementById("area").value;
    
    if (area == "") {
        document.getElementById("trabajador").innerHTML = "<option value=''>Seleccionar Trabajador</option>";
        return;
    }

    // Enviar el área al servidor para obtener los empleados
    $.ajax({
        url: 'obtener_empleados.php',
        type: 'GET',
        data: { area: area },
        success: function(data) {
            var trabajadorSelect = document.getElementById("trabajador");
            trabajadorSelect.innerHTML = "<option value=''>Seleccionar Trabajador</option>" + data;
        }
    });
}

function actualizarNombrePersona() {
    var trabajadorSelect = document.getElementById("trabajador");
    var nombrePersonaInput = document.getElementById("nombre_persona");
    
    // Obtener el nombre del trabajador seleccionado
    var nombrePersona = trabajadorSelect.options[trabajadorSelect.selectedIndex].text;
    
    // Asignar el nombre al campo oculto
    nombrePersonaInput.value = nombrePersona;
}

$('#formSalida').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    $.ajax({
        type: "POST",
        url: "procesar_salida.php",
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload(); // Recarga la página para ver las salidas actualizadas
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert("Error al registrar la salida.");
        }
    });
});

$('#formFecha').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    var fecha = $('#fecha_consulta').val();

    $.ajax({
        type: "GET",
        url: "obtener_salidas_fecha.php",
        data: { fecha: fecha },
        success: function(data) {
            $('#salidasFechaSeleccionada').html(data);
        },
        error: function() {
            alert("Error al obtener las salidas de la fecha seleccionada.");
        }
    });
});
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>
