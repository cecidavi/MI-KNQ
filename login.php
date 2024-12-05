<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'check');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Encriptar la contraseña
    $departamento = $_POST['departamento']; // Obtener el departamento elegido

    // Consulta para validar usuario y contraseña
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Verificar si el usuario pertenece al departamento seleccionado
        $user = $result->fetch_assoc();
        $user_departamento = $user['departamento_id'];

        // Si el departamento seleccionado es el mismo que el del usuario
        if ($user_departamento == $departamento) {
            $_SESSION['username'] = $username;
            $_SESSION['departamento'] = $departamento; // Guardamos el departamento en la sesión

            // Redirigir a la página correspondiente
            switch ($departamento) {
                case 1:
                    header("Location: Inicio.php");
                    break;
                case 2:
                    header("Location: Inicio.php");
                    break;
                case 3:
                    header("Location: Almacen_inicio.php");
                    break;
                case 4:
                    header("Location: Inicio.php");
                    break;
                default:
                    echo "Departamento no válido.";
                    break;
            }
            exit();
        } else {
            echo "No tienes acceso a este departamento.";
        }
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}

$conn->close();
?>
