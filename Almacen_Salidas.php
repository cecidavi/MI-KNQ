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
<form action="procesar_salida.php" method="POST">
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
        <select id="trabajador" name="trabajador" class="form-control">
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

    <!-- Botón con diseño de Bootstrap -->
    <input type="submit" value="Registrar Salida" class="btn btn-primary">
</form>
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
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>

