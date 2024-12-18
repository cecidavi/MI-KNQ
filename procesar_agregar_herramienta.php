<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

include('conexion.php');

if (!empty($_POST['nombre']) && !empty($_POST['descripcion']) && !empty($_POST['estado'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    // Insertar la nueva herramienta en la base de datos
    $sql_insertar = "INSERT INTO herramientas (nombre, descripcion, estado) VALUES (?, ?, ?)";
    $stmt_insertar = $conn->prepare($sql_insertar);
    $stmt_insertar->bind_param("sss", $nombre, $descripcion, $estado);

    if ($stmt_insertar->execute()) {
        echo json_encode(['success' => true, 'message' => 'Herramienta agregada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar la herramienta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
}
?>
