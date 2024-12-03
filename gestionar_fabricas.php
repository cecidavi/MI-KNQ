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

// Manejo de formulario para agregar o editar fábrica
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_fabrica = $_POST['nombre_fabrica'];
    $ubicacion = $_POST['ubicacion'];

    if (isset($_POST['id_fabrica']) && !empty($_POST['id_fabrica'])) {
        // Editar fábrica
        $id_fabrica = $_POST['id_fabrica'];
        $sql_update = "UPDATE fabricas SET nombre_fabrica = ?, ubicacion = ? WHERE id_fabrica = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $nombre_fabrica, $ubicacion, $id_fabrica);

        if ($stmt_update->execute()) {
            $success_message = "Fábrica actualizada correctamente.";
        } else {
            $error_message = "Error al actualizar la fábrica: " . $conn->error;
        }

        $stmt_update->close();
    } else {
        // Agregar nueva fábrica
        $sql_insert = "INSERT INTO fabricas (nombre_fabrica, ubicacion) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ss", $nombre_fabrica, $ubicacion);

        if ($stmt_insert->execute()) {
            $success_message = "Fábrica agregada correctamente.";
        } else {
            $error_message = "Error al agregar la fábrica: " . $conn->error;
        }

        $stmt_insert->close();
    }
}

// Manejo de eliminación de fábrica
if (isset($_GET['delete_id'])) {
    $id_fabrica = $_GET['delete_id'];
    $sql_delete = "DELETE FROM fabricas WHERE id_fabrica = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_fabrica);

    if ($stmt_delete->execute()) {
        $success_message = "Fábrica eliminada correctamente.";
    } else {
        $error_message = "Error al eliminar la fábrica: " . $conn->error;
    }

    $stmt_delete->close();
}

// Consulta para obtener los datos de las fábricas
$sql = "SELECT id_fabrica, nombre_fabrica, ubicacion FROM fabricas";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Fábricas - MI KNQ</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Asegúrate de que esta ruta es correcta -->
    <link rel="stylesheet" href="path/to/Fabricas.css"> <!-- Asegúrate de que esta ruta es correcta -->
</head>
<body>
    <div class="container">
        <h2>Gestión de Fábricas</h2>
        <p>Aquí puedes gestionar las fábricas.</p>

        <?php
        if (!empty($success_message)) {
            echo "<div class='alert alert-success'>$success_message</div>";
        }
        if (!empty($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }
        ?>

        <h3>Agregar/Editar Fábrica</h3>
        <form action="gestionar_fabricas.php" method="post">
            <input type="hidden" name="id_fabrica" id="id_fabrica">
            <label>Nombre de la Fábrica:</label>
            <input type="text" name="nombre_fabrica" id="nombre_fabrica" required class="form-control"><br>
            <label>Ubicación:</label>
            <input type="text" name="ubicacion" id="ubicacion" required class="form-control"><br>
            <input type="submit" value="Guardar" class="btn btn-primary">
            <input type="reset" value="Cancelar" class="btn btn-secondary">
        </form>

        <h3>Lista de Fábricas</h3>
        <?php
        if ($result->num_rows > 0) {
            echo "<table class='table'>";
            echo "<thead><tr><th>ID</th><th>Nombre</th><th>Ubicación</th><th>Acciones</th></tr></thead>";
            echo "<tbody>";

            // Salida de datos de cada fila
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id_fabrica"] . "</td>";
                echo "<td>" . $row["nombre_fabrica"] . "</td>";
                echo "<td>" . $row["ubicacion"] . "</td>";
                echo "<td>
                        <button class='btn btn-info' onclick='editarFabrica(" . $row["id_fabrica"] . ", \"" . $row["nombre_fabrica"] . "\", \"" . $row["ubicacion"] . "\")'>Editar</button>
                        <a href='gestionar_fabricas.php?delete_id=" . $row["id_fabrica"] . "' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta fábrica?\")'>Eliminar</a>
                      </td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron fábricas.</p>";
        }
        ?>
    </div>
    <script>
        function editarFabrica(id, nombre, ubicacion) {
            document.getElementById('id_fabrica').value = id;
            document.getElementById('nombre_fabrica').value = nombre;
            document.getElementById('ubicacion').value = ubicacion;
        }
    </script>
    <?php include('includes/footer.php'); ?>
</body>
</html>
