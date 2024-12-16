<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');
include('Almacen_header.php');

// Obtener los empleados disponibles para asignar pilas
$sql_empleados = "SELECT * FROM empleados";
$result_empleados = $conn->query($sql_empleados);
$empleados = [];
while ($row_empleado = $result_empleados->fetch_assoc()) {
    $empleados[] = $row_empleado;
}

// Obtener la fecha seleccionada
$fecha_seleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Obtener el historial de entregas de pilas para la fecha seleccionada
$sql_entregas = "SELECT e.id_empleado, e.fecha_entrega, e.foto_poliza, emp.nombre AS nombre_empleado
                 FROM entregas_pilas e
                 JOIN empleados emp ON e.id_empleado = emp.id_empleado
                 WHERE e.fecha_entrega = '$fecha_seleccionada'";
$result_entregas = $conn->query($sql_entregas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Entrega</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Almacen_unidades.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Registrar Entrega de Pila</h2>
<div class="container">
    <form id="formRegistrarEntrega" enctype="multipart/form-data">
        <div class="form-group">
            <label for="empleado">Seleccionar Empleado:</label>
            <select id="empleado" name="empleado" class="form-control" required>
                <option value="">Seleccionar Empleado</option>
                <?php foreach ($empleados as $empleado): ?>
                    <option value="<?= $empleado['id_empleado'] ?>"><?= $empleado['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="fecha_entrega">Fecha de Entrega:</label>
            <input type="date" id="fecha_entrega" name="fecha_entrega" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="foto_poliza">Foto de la Póliza de Garantía:</label>
            <input type="file" id="foto_poliza" name="foto_poliza" class="form-control" required>
        </div>

        <input type="submit" value="Registrar Entrega" class="btn btn-primary">
    </form>
</div>

<h2>Historial de Entregas de Pilas</h2>
<div class="container">
    <form method="GET" action="">
        <div class="form-group">
            <label for="fecha">Seleccionar Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" value="<?= $fecha_seleccionada ?>" required>
        </div>
        <input type="submit" value="Ver Entregas" class="btn btn-secondary">
    </form>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Fecha de Entrega</th>
                <th>Foto de la Póliza</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_entregas->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['nombre_empleado'] ?></td>
                    <td><?= $row['fecha_entrega'] ?></td>
                    <td><img src="<?= $row['foto_poliza'] ?>" alt="Foto Póliza" style="width: 100px; height: 100px;"></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
$('#formRegistrarEntrega').on('submit', function(e) {
    e.preventDefault();

    // Captura los valores de los campos
    var empleado = $('#empleado').val();
    var fechaEntrega = $('#fecha_entrega').val();
    var fotoPoliza = $('#foto_poliza')[0].files.length;

    // Depuración: muestra los valores
    console.log('Empleado:', empleado);
    console.log('Fecha de entrega:', fechaEntrega);
    console.log('Foto de póliza:', fotoPoliza);

    // Validación de los campos
    if (!empleado || !fechaEntrega || !fotoPoliza) {
        alert("Todos los campos son requeridos.");
        return; // Detiene el envío del formulario
    }

    var formData = new FormData(this);

    $.ajax({
        type: "POST",
        url: "procesar_registrar_entrega.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload();
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert("Error al registrar la entrega.");
        }
    });
});
</script>

</body>
</html>

<?php include('includes/footer.php'); ?>
