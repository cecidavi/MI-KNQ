<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');
include('Almacen_header.php');

// Verifica si se ha enviado una búsqueda
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Prepara la consulta SQL
$sql = "SELECT p.id_pieza, p.nombre, p.descripcion, p.cantidad, u.codigo_ubicacion
        FROM piezas p
        LEFT JOIN ubicaciones u ON p.id_ubicacion = u.id_ubicacion
        WHERE p.nombre LIKE ? OR p.descripcion LIKE ?";

// Prepara la declaración
$stmt = $conn->prepare($sql);
$search_term = "%" . $search . "%";
$stmt->bind_param("ss", $search_term, $search_term);

// Ejecuta la consulta
$stmt->execute();

// Obtiene los resultados
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Piezas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<h2>Listado de Almacen</h2>

<div class="container">
    <!-- Formulario de búsqueda -->
    <form method="POST" action="Piezas.php" class="mb-4">
        <div class="form-group">
            <input type="text" name="search" class="form-control" placeholder="Nombre o descripción" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <!-- Tabla de piezas -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Ubicación</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Verifica si hay resultados
            if ($result->num_rows > 0) {
                // Muestra los datos
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id_pieza'] . "</td>";
                    echo "<td>" . $row['nombre'] . "</td>";
                    echo "<td>" . $row['descripcion'] . "</td>";
                    echo "<td>" . $row['cantidad'] . "</td>";
                    echo "<td>" . $row['codigo_ubicacion'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No se encontraron piezas.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>


<?php
// Cierra la declaración y la conexión
$stmt->close();
$conn->close();
?>
