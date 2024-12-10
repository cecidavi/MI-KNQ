<?php
session_start();

include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $cantidad_inicial = $_POST['cantidad_inicial'];
    $ubicacion_nueva = $_POST['ubicacion_nueva']; // Recibir la ubicación

    if (!empty($nombre) && !empty($descripcion) && !empty($cantidad_inicial) && !empty($ubicacion_nueva)) {
        $sql = "INSERT INTO piezas (nombre, descripcion, cantidad, id_ubicacion) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $nombre, $descripcion, $cantidad_inicial, $ubicacion_nueva);

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
