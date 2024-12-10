<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');

// Verifica si se ha enviado el ID de la pieza a eliminar
if (isset($_GET['id'])) {
    $id_pieza = $_GET['id'];

    // Elimina la pieza
    $sql = "DELETE FROM piezas WHERE id_pieza = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pieza);

    if ($stmt->execute()) {
        header("Location: Piezas.php");
        exit();
    } else {
        echo "Error al eliminar la pieza.";
    }
} else {
    echo "No se ha proporcionado el ID de la pieza.";
}

$conn->close();
?>
