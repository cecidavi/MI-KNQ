<?php
include('conexion.php');

$fecha = $_GET['fecha'];

$sql_salidas_fecha = "SELECT * FROM salidas WHERE fecha = '$fecha'";
$result_salidas_fecha = $conn->query($sql_salidas_fecha);

$output = '';

if ($result_salidas_fecha->num_rows > 0) {
    while ($row_salida = $result_salidas_fecha->fetch_assoc()) {
        $output .= '
        <tr>
            <td>' . $row_salida['id_salida'] . '</td>
            <td>' . $row_salida['id_pieza'] . '</td>
            <td>' . $row_salida['cantidad'] . '</td>
            <td>' . $row_salida['unidad'] . '</td>
            <td>' . $row_salida['nombre_persona'] . '</td>
            <td>' . $row_salida['fecha'] . '</td>
            <td>' . $row_salida['hora'] . '</td>
        </tr>';
    }
} else {
    $output .= '<tr><td colspan="7">No se encontraron salidas para esta fecha.</td></tr>';
}

echo $output;

$conn->close();
?>
