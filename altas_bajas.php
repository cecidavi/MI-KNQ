<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('includes/header.php');
?>

<div class="container">
    <h2>Altas y Bajas</h2>
    <p>Aquí puedes gestionar las altas y bajas de los empleados.</p>
</div>

<?php include('includes/footer.php'); ?>
