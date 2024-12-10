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
    <title>Registrar Faltante de Piezas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Almacen_unidades.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Registrar Faltante de Piezas</h2>
<div class="container">
    <form id="formFaltante">
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
            <label for="cantidad">Cantidad Faltante:</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción del Faltante:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" value="<?= $fecha_actual ?>" required>
        </div>

        <div class="form-group">
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" class="form-control" required>
        </div>

        <input type="submit" value="Registrar Faltante" class="btn btn-danger">
    </form>

    <h3>Faltantes Registrados</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Faltante</th>
                <th>Pieza</th>
                <th>Cantidad Faltante</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody id="faltantesRegistrados">
            <!-- Los faltantes se llenarán aquí vía AJAX -->
        </tbody>
    </table>
</div>

<script>
// Enviar el formulario para registrar un faltante
$('#formFaltante').on('submit', function(e) {
    e.preventDefault(); // Previene la recarga de la página

    $.ajax({
        type: "POST",
        url: "procesar_faltante.php",
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload(); // Recarga la página para ver los faltantes actualizados
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert("Error al registrar el faltante.");
        }
    });
});

// Obtener los faltantes registrados para mostrar en la tabla
$(document).ready(function() {
    $.ajax({
        type: "GET",
        url: "obtener_faltantes.php",
        success: function(data) {
            $('#faltantesRegistrados').html(data);
        },
        error: function() {
            alert("Error al obtener los faltantes registrados.");
        }
    });
});
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>
