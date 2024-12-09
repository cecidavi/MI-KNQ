<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');
include('Almacen_header.php');

// Obtener las piezas para el formulario
$sql_piezas = "SELECT * FROM piezas";
$result_piezas = $conn->query($sql_piezas);

// Obtener la fecha actual
$fecha_actual = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Entrada de Piezas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Almacen_unidades.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Registrar Entrada de Piezas</h2>
<div class="container">
    <form id="formEntrada">
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
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" value="<?= $fecha_actual ?>" required>
        </div>

        <div class="form-group">
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" class="form-control" required>
        </div>

        <!-- Botón con diseño de Bootstrap -->
        <input type="submit" value="Registrar Entrada" class="btn btn-primary">
    </form>

    <h3>Ver Entradas por Fecha</h3>
    <form id="formFechaEntrada">
        <div class="form-group">
            <label for="fecha_consulta_entrada">Seleccionar Fecha:</label>
            <input type="date" id="fecha_consulta_entrada" name="fecha_consulta_entrada" class="form-control" value="<?= $fecha_actual ?>" required>
        </div>
        <input type="submit" value="Consultar Entradas" class="btn btn-secondary">
    </form>

    <h3>Entradas de la Fecha Seleccionada</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Entrada</th>
                <th>Pieza</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody id="entradasFechaSeleccionada">
            <!-- Las entradas se llenarán aquí vía AJAX -->
        </tbody>
    </table>
</div>

<script>
$('#formEntrada').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    $.ajax({
        type: "POST",
        url: "procesar_entrada.php",
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload(); // Recarga la página para ver las entradas actualizadas
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert("Error al registrar la entrada.");
        }
    });
});

$('#formFechaEntrada').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    var fecha = $('#fecha_consulta_entrada').val();

    $.ajax({
        type: "GET",
        url: "obtener_entradas_fecha.php",
        data: { fecha: fecha },
        success: function(data) {
            $('#entradasFechaSeleccionada').html(data);
        },
        error: function() {
            alert("Error al obtener las entradas de la fecha seleccionada.");
        }
    });
});

// Llamar a la función para cargar las entradas de la fecha actual al cargar la página
$(document).ready(function() {
    var fecha_actual = $('#fecha_consulta_entrada').val();

    $.ajax({
        type: "GET",
        url: "obtener_entradas_fecha.php",
        data: { fecha: fecha_actual },
        success: function(data) {
            $('#entradasFechaSeleccionada').html(data);
        },
        error: function() {
            alert("Error al obtener las entradas de la fecha actual.");
        }
    });
});
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>
