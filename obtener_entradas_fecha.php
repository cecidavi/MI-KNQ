<?php
include('conexion.php');

if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];

    $sql = "SELECT entradas.id_entrada, piezas.nombre AS pieza_nombre, entradas.cantidad, entradas.fecha, entradas.hora
            FROM entradas
            JOIN piezas ON entradas.id_pieza = piezas.id_pieza
            WHERE entradas.fecha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id_entrada']}</td>
                    <td>{$row['pieza_nombre']}</td>
                    <td>{$row['cantidad']}</td>
                    <td>{$row['fecha']}</td>
                    <td>{$row['hora']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No se encontraron entradas para la fecha seleccionada.</td></tr>";
    }

    $stmt->close();
}

$conn->close();
?>
