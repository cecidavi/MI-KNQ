<?php
include('conexion.php');

if (isset($_GET['id_pieza'])) {
    $id_pieza = $_GET['id_pieza'];

    // Consulta para obtener la ubicación asociada con la pieza
    $sql = "SELECT ubicaciones.codigo_ubicacion 
            FROM piezas 
            INNER JOIN ubicaciones ON piezas.id_ubicacion = ubicaciones.id_ubicacion 
            WHERE piezas.id_pieza = ?";
    
    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pieza);
    $stmt->execute();
    $stmt->bind_result($codigo_ubicacion);
    $stmt->fetch();

    // Si la ubicación se encuentra, devolverla
    if ($codigo_ubicacion) {
        echo json_encode(['success' => true, 'ubicacion' => $codigo_ubicacion]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ubicación no encontrada']);
    }

    // Cerrar la consulta
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de pieza no proporcionado']);
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
