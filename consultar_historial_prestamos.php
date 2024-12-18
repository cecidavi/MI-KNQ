<?php
// Conectar a la base de datos
include('conexion.php');

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Preparar la consulta SQL
$sql = "SELECT p.id_prestamo, p.nombre_persona, p.fecha_prestamo, p.fecha_devolucion, h.nombre AS herramienta 
        FROM prestamos_herramientas p 
        INNER JOIN herramientas h ON p.id_herramienta = h.id_herramienta";

$stmt = $mysqli->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if ($stmt === false) {
    die("Prepare failed: " . $mysqli->error);
}

// Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();

// Recorrer los resultados y mostrarlos
while ($row = $result->fetch_assoc()) {
    echo "ID Préstamo: " . $row["id_prestamo"] . " - Nombre: " . htmlspecialchars($row["nombre_persona"]) . " - Fecha Préstamo: " . $row["fecha_prestamo"] . " - Fecha Devolución: " . $row["fecha_devolucion"] . " - Herramienta: " . htmlspecialchars($row["herramienta"]) . "<br>";
}

// Cerrar la consulta y la conexión
$stmt->close();
$mysqli->close();
?>
