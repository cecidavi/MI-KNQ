<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');
include('Almacen_header.php');

// Establecer la zona horaria
date_default_timezone_set('America/Mexico_City');

// Obtener las piezas para el formulario
$sql_piezas = "SELECT * FROM piezas";
$result_piezas = $conn->query($sql_piezas);

// Obtener las entradas registradas
$sql_entradas = "SELECT entradas.*, piezas.nombre AS nombre_pieza FROM entradas INNER JOIN piezas ON entradas.id_pieza = piezas.id_pieza";
$result_entradas = $conn->query($sql_entradas);

// Obtener las ubicaciones para el formulario
$sql_ubicaciones = "SELECT * FROM ubicaciones";
$result_ubicaciones = $conn->query($sql_ubicaciones);

if (!$result_ubicaciones) {
    echo "Error al obtener ubicaciones: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Entradas y Agregar Piezas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Almacen_unidades.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Registrar Entradas y Agregar Piezas</h2>
<div class="container">
    <!-- Formulario para registrar entradas -->
    <form id="formEntrada" action="procesar_entrada.php" method="POST">
        <h3>Registrar Entrada de Piezas</h3>
        <div class="form-group">
            <label for="pieza">Seleccionar Pieza:</label>
            <select id="pieza" name="pieza" class="form-control" required>
                <option value="">Seleccionar Pieza</option>
                <?php while ($row_pieza = $result_piezas->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row_pieza['id_pieza']) ?>"><?= htmlspecialchars($row_pieza['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación:</label>
            <input type="text" id="ubicacion" name="ubicacion" class="form-control" readonly required>
        </div>

        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" class="form-control" required value="<?php echo date('H:i'); ?>">
        </div>

        <input type="submit" value="Registrar Entrada" class="btn btn-primary">
    </form>

    <!-- Formulario para agregar nuevas piezas -->
    <form id="formNuevaPieza">
        <h3>Agregar Nueva Pieza</h3>
        <div class="form-group">
            <label for="nombre">Nombre de la Pieza:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="cantidad_inicial">Cantidad Inicial:</label>
            <input type="number" id="cantidad_inicial" name="cantidad_inicial" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="ubicacion_nueva">Seleccionar Ubicación:</label>
            <select id="ubicacion_nueva" name="ubicacion_nueva" class="form-control" required>
                <option value="">Seleccionar Ubicación</option>
                <?php
                // Restablecer el resultado de ubicaciones
                $result_ubicaciones->data_seek(0);
                while ($row_ubicacion = $result_ubicaciones->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row_ubicacion['id_ubicacion']) ?>"><?= htmlspecialchars($row_ubicacion['codigo_ubicacion']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <input type="submit" value="Agregar Pieza" class="btn btn-primary">
    </form>

    <!-- Tabla para mostrar las entradas registradas -->
    <h3>Entradas Registradas</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Entrada</th>
                <th>Pieza</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row_entrada = $result_entradas->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row_entrada['id_entrada']) ?></td>
                    <td><?= htmlspecialchars($row_entrada['nombre_pieza']) ?></td>
                    <td><?= htmlspecialchars($row_entrada['cantidad']) ?></td>
                    <td><?= htmlspecialchars($row_entrada['fecha']) ?></td>
                    <td><?= htmlspecialchars($row_entrada['hora']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Manejo del formulario de nueva pieza
    $('#formNuevaPieza').on('submit', function(e) {
        e.preventDefault(); // Previene la recarga de la página

        $.ajax({
            type: "POST",
            url: "procesar_nueva_pieza.php",
            data: $(this).serialize(),
            success: function(response) {
                let res = JSON.parse(response);
                if (res.success) {
                    alert(res.message);
                    location.reload(); // Recarga la página
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert("Error al agregar la pieza.");
            }
        });
    });

    // Manejo del formulario de entrada
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
                    location.reload(); // Recarga la página
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert("Error al registrar la entrada.");
            }
        });
    });

    // Autocompletar ubicación al seleccionar una pieza
    $('#pieza').on('change', function() {
        let idPieza = $(this).val();
        if (idPieza) {
            $.ajax({
                type: "GET",
                url: "obtener_ubicacion.php",
                data: { id_pieza: idPieza },
                success: function(response) {
                    let res = JSON.parse(response);
                    if (res.success) {
                        $('#ubicacion').val(res.ubicacion); // Rellenar campo de ubicación
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    alert("Error al obtener la ubicación.");
                }
            });
        } else {
            $('#ubicacion').val(''); // Limpiar campo si no se selecciona una pieza
        }
    });
});
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>
