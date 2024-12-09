<?php
session_start();

include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad_inicial = $_POST['cantidad_inicial'];

    if (!empty($nombre) && !empty($descripcion) && !empty($cantidad_inicial)) {
        $sql = "INSERT INTO piezas (nombre, descripcion, cantidad) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombre, $descripcion, $cantidad_inicial);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Pieza agregada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al agregar la pieza."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
    }
}

$conn->close();
?>
