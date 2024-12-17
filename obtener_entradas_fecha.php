<?php
include('conexion.php');

if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];

    $sql_entradas = "SELECT entradas.*, piezas.nombre AS nombre_pieza FROM entradas INNER JOIN piezas ON entradas.id_pieza = piezas.id_pieza WHERE entradas.fecha = ?";
    $stmt_entradas = $conn->prepare($sql_entradas);
    $stmt_entradas->bind_param("s", $fecha);
    $stmt_entradas->execute();
    $result_entradas = $stmt_entradas->get_result();

    if ($result_entradas->num_rows > 0) {
        $entradas = [];
        while ($row_entrada = $result_entradas->fetch_assoc()) {
            $entradas[] = [
                'id_entrada' => $row_entrada['id_entrada'],
                'nombre_pieza' => $row_entrada['nombre_pieza'],
                'cantidad' => $row_entrada['cantidad'],
                'fecha' => $row_entrada['fecha'],
                'hora' => $row_entrada['hora']
            ];
        }
        echo json_encode(['success' => true, 'entradas' => $entradas]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron entradas para esta fecha.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Fecha no proporcionada.']);
}
?>
