<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('Almacen_header.php');
include('conexion.php');

// Verifica si se ha enviado el ID de la pieza a editar
if (isset($_GET['id'])) {
    $id_pieza = $_GET['id'];

    // Obtiene la información de la pieza
    $sql = "SELECT * FROM piezas WHERE id_pieza = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pieza);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cierra el resultado antes de continuar
    $stmt->close();

    if ($result->num_rows > 0) {
        $pieza = $result->fetch_assoc();
        $id_ubicacion = $pieza['id_ubicacion'];

        // Obtiene el nombre de la ubicación desde la tabla "ubicaciones"
        $sql_ubicacion = "SELECT codigo_ubicacion FROM ubicaciones WHERE id_ubicacion = ?";
        $stmt_ubicacion = $conn->prepare($sql_ubicacion);
        $stmt_ubicacion->bind_param("i", $id_ubicacion);
        $stmt_ubicacion->execute();
        $stmt_ubicacion->bind_result($codigo_ubicacion);
        $stmt_ubicacion->fetch();
        $stmt_ubicacion->close();
    } else {
        echo "Pieza no encontrada.";
        exit();
    }
} else {
    echo "No se ha proporcionado el ID de la pieza.";
    exit();
}

// Verifica si se ha enviado el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $ubicacion = $_POST['ubicacion'];

    // Verifica si la ubicación existe en la tabla de ubicaciones
    $sql_check_ubicacion = "SELECT COUNT(*) FROM ubicaciones WHERE id_ubicacion = ?";
    $stmt_check_ubicacion = $conn->prepare($sql_check_ubicacion);
    $stmt_check_ubicacion->bind_param("i", $ubicacion);
    $stmt_check_ubicacion->execute();
    $stmt_check_ubicacion->bind_result($ubicacion_existente);
    $stmt_check_ubicacion->fetch();

    // Cierra la consulta de validación
    $stmt_check_ubicacion->close();

    // Si la ubicación no existe, muestra un mensaje de error
    if ($ubicacion_existente == 0) {
        echo "La ubicación seleccionada no existe.";
    } else {
        // Si la ubicación existe, actualiza la pieza
        $sql_update = "UPDATE piezas SET nombre = ?, descripcion = ?, cantidad = ?, id_ubicacion = ? WHERE id_pieza = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssiii", $nombre, $descripcion, $cantidad, $ubicacion, $id_pieza);

        if ($stmt_update->execute()) {
            header("Location: Piezas.php");
            exit();
        } else {
            echo "Error al actualizar la pieza.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pieza</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2>Editar Pieza</h2>

    <form method="POST" action="editar_pieza.php?id=<?php echo $pieza['id_pieza']; ?>">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($pieza['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" class="form-control" value="<?php echo htmlspecialchars($pieza['descripcion']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" value="<?php echo htmlspecialchars($pieza['cantidad']); ?>" required>
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación:</label>
            <!-- Muestra el código de la ubicación en lugar del ID -->
            <input type="text" id="ubicacion" name="ubicacion" class="form-control" value="<?php echo htmlspecialchars($codigo_ubicacion); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

</body>
<?php include('includes/footer.php'); ?>
</html>

<?php
$conn->close();
?>
