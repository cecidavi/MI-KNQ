<?php
include('conexion.php');

$id_herramienta = $_POST['herramienta_devolver'];

// Actualizar el estado de la herramienta a "disponible"
$sql = "UPDATE herramientas SET estado = 'disponible' WHERE id_herramienta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_herramienta);

if ($stmt->execute()) {
    // Eliminar el registro del préstamo de la herramienta
    $sql_delete_prestamo = "DELETE FROM prestamos WHERE id_herramienta = ?";
    $stmt_delete = $conn->prepare($sql_delete_prestamo);
    $stmt_delete->bind_param("i", $id_herramienta);
    $stmt_delete->execute();
    
    echo json_encode(['success' => true, 'message' => 'Herramienta devuelta con éxito.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al devolver la herramienta.']);
}

$stmt->close();
$conn->close();
?>
