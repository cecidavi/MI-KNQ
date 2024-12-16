<?php
include('conexion.php');

$fecha_seleccionada = $_GET['fecha'] ?? date("Y-m-d");

// Obtener los faltantes registrados para la fecha seleccionada
$sql_faltantes = "SELECT f.id_faltante, p.nombre, f.cantidad_faltante, f.descripcion, f.fecha, f.hora
                  FROM faltantes f
                  INNER JOIN piezas p ON f.id_pieza = p.id_pieza
                  WHERE f.fecha = ?
                  ORDER BY f.fecha DESC, f.hora DESC";
$stmt = $conn->prepare($sql_faltantes);
$stmt->bind_param("s", $fecha_seleccionada);
$stmt->execute();
$result_faltantes = $stmt->get_result();

while ($row_faltante = $result_faltantes->fetch_assoc()) {
    echo "<tr>
            <td>{$row_faltante['id_faltante']}</td>
            <td>{$row_faltante['nombre']}</td>
            <td>{$row_faltante['cantidad_faltante']}</td>
            <td>{$row_faltante['descripcion']}</td>
            <td>{$row_faltante['fecha']}</td>
            <td>{$row_faltante['hora']}</td>
          </tr>";
}

$stmt->close();
$conn->close();
?>
