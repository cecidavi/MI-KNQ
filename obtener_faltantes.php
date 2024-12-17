<?php
include('conexion.php');

// Recibir parámetros de fecha
$fechaFiltro = isset($_GET['fechaFiltro']) ? $_GET['fechaFiltro'] : 'hoy';
$fechaCustom = isset($_GET['fechaCustom']) ? $_GET['fechaCustom'] : '';

// Obtener la fecha actual
$fecha_actual = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day"));
$hace_dos_dias = date("Y-m-d", strtotime("-2 days"));

if ($fechaFiltro == 'hoy') {
    $fecha_filtrada = $fecha_actual;
} elseif ($fechaFiltro == 'ayer') {
    $fecha_filtrada = $ayer;
} elseif ($fechaFiltro == 'personalizada' && $fechaCustom) {
    $fecha_filtrada = $fechaCustom;
} else {
    $fecha_filtrada = $fecha_actual; // Por defecto, mostrar hoy
}

// Filtrar los faltantes por fecha
$sql_faltantes = "SELECT p.nombre, f.cantidad_faltante, f.descripcion, f.fecha, f.hora
                  FROM faltantes f
                  INNER JOIN piezas p ON f.id_pieza = p.id_pieza
                  WHERE f.fecha = '$fecha_filtrada'
                  ORDER BY f.fecha DESC, f.hora DESC";

$result_faltantes = $conn->query($sql_faltantes);

while ($row_faltante = $result_faltantes->fetch_assoc()) {
    echo "<tr>
            <td>{$row_faltante['nombre']}</td>
            <td>{$row_faltante['cantidad_faltante']}</td>
            <td>{$row_faltante['descripcion']}</td>
            <td>{$row_faltante['fecha']}</td>
            <td>{$row_faltante['hora']}</td>
          </tr>";
}

$conn->close();
?>
