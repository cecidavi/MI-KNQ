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

    // Agregar depuración para verificar los valores
    //echo "Usuario: " . $username . "<br>";
    //echo "Contraseña: " . $password . "<br>";

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        header("Location: Inicio.php");
        exit();
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}

$conn->close();
?>
