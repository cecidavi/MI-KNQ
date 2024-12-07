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

// Manejo de formulario para agregar o editar unidad
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unidad_action'])) {
    $numero_unidad = $_POST['numero_unidad'];

    if (isset($_POST['id_unidad']) && !empty($_POST['id_unidad'])) {
        // Editar unidad
        $id_unidad = $_POST['id_unidad'];
        $sql_update = "UPDATE unidades SET numero_unidad = ? WHERE id_unidad = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $numero_unidad, $id_unidad);

        if ($stmt_update->execute()) {
            $success_message = "Unidad actualizada correctamente.";
        } else {
            $error_message = "Error al actualizar la unidad: " . $conn->error;
        }

        $stmt_update->close();
    } else {
        // Agregar nueva unidad
        $sql_insert = "INSERT INTO unidades (numero_unidad) VALUES (?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("s", $numero_unidad);

        if ($stmt_insert->execute()) {
            $success_message = "Unidad agregada correctamente.";
        } else {
            $error_message = "Error al agregar la unidad: " . $conn->error;
        }

        $stmt_insert->close();
    }
}

// Manejo de eliminación de unidad
if (isset($_GET['delete_id'])) {
    $id_unidad = $_GET['delete_id'];
    $sql_delete = "DELETE FROM unidades WHERE id_unidad = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_unidad);

    if ($stmt_delete->execute()) {
        $success_message = "Unidad eliminada correctamente.";
    } else {
        $error_message = "Error al eliminar la unidad: " . $conn->error;
    }

    $stmt_delete->close();
}

// Consulta para obtener los datos de las unidades
$sql = "SELECT id_unidad, numero_unidad FROM unidades";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Unidades - MI KNQ</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Asegúrate de que esta ruta es correcta -->
    <link rel="stylesheet" href="path/to/Unidades.css"> <!-- Asegúrate de que esta ruta es correcta -->
</head>
<body>
    <div class="container">
        <h2>Gestión de Unidades</h2>
        <p>Aquí puedes gestionar las unidades.</p>

        <?php
        if (!empty($success_message)) {
            echo "<div class='alert alert-success'>$success_message</div>";
        }
        if (!empty($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }
        ?>

        <h3>Agregar/Editar Unidad</h3>
        <form action="gestionar_unidades.php" method="post">
            <input type="hidden" name="id_unidad" id="id_unidad">
            <input type="hidden" name="unidad_action" value="manage_unidad">
            <label>Número de la Unidad:</label>
            <input type="text" name="numero_unidad" id="numero_unidad" required class="form-control"><br>
            <input type="submit" value="Guardar" class="btn btn-primary">
            <input type="reset" value="Cancelar" class="btn btn-secondary">
        </form>

        <h3>Lista de Unidades</h3>
        <?php
        if ($result->num_rows > 0) {
            echo "<table class='table'>";
            echo "<thead><tr><th>ID</th><th>Número</th><th>Acciones</th></tr></thead>";
            echo "<tbody>";

            // Salida de datos de cada fila
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id_unidad"] . "</td>";
                echo "<td>" . $row["numero_unidad"] . "</td>";
                echo "<td>
                        <button class='btn btn-info' onclick='editarUnidad(" . $row["id_unidad"] . ", \"" . $row["numero_unidad"] . "\")'>Editar</button>
                        <a href='gestionar_unidades.php?delete_id=" . $row["id_unidad"] . "' class='btn btn-danger' onclick='return confirm(\"¿Estás seguro de que deseas eliminar esta unidad?\")'>Eliminar</a>
                      </td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron unidades.</p>";
        }
        ?>
    </div>
    <script src="path/to/bootstrap.bundle.min.js"></script> <!-- Asegúrate de que esta ruta es correcta -->
    <script>
        function editarUnidad(id, numero) {
            document.getElementById('id_unidad').value = id;
            document.getElementById('numero_unidad').value = numero;
        }
    </script>
</body>
</html>
