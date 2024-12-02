<?php
session_start();

// Verifica si el usuario ya está autenticado
if (isset($_SESSION['username'])) {
    header("Location: Inicio.php");
    exit();
} else {
    header("Location: login.html");
    exit();
}
?>
