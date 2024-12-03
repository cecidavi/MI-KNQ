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
    <h2>Bienvenido, <?php echo $_SESSION['username']; ?>, al Sistema de Recursos Humanos</h2>
    <p>Use el menú para navegar por las diferentes secciones.</p>
    
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Empleados</h5>
                    <p class="card-text">Administre la información de los empleados.</p>
                    <a href="empleados.php" class="btn btn-primary">Ir a Empleados</a>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text">Genere y visualice reportes.</p>
                    <a href="reportes.php" class="btn btn-primary">Ir a Reportes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="logout.php" class="btn btn-secondary mt-3">Cerrar sesión</a>

<?php include('includes/footer.php'); ?>
