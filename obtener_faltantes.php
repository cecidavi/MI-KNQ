<?php
include('conexion.php');

// Obtener los faltantes registrados
$sql_faltantes = "SELECT f.id_faltante, p.nombre, f.cantidad_faltante, f.descripcion, f.fecha, f.hora
                  FROM faltantes f
                  INNER JOIN piezas p ON f.id_pieza = p.id_pieza
                  ORDER BY f.fecha DESC, f.hora DESC";
$result_faltantes = $conn->query($sql_faltantes);

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

$conn->close();
?>
