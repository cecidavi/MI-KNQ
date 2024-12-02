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
    <h2>Reportes</h2>
    <p>Aquí puedes ver los reportes.</p>
</div>

<?php include('includes/footer.php'); ?>
