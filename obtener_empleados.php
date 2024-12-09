<?php
include('conexion.php');

// Verificar que se haya recibido el id del área
if (isset($_GET['area'])) {
    $area = $_GET['area'];

    // Obtener empleados del área seleccionada
    $sql_empleados = "SELECT * FROM empleados WHERE id_departamento = $area";
    $result_empleados = $conn->query($sql_empleados);

    // Crear las opciones para el select de trabajadores
    while ($row_empleado = $result_empleados->fetch_assoc()) {
        echo "<option value='".$row_empleado['id_empleado']."'>".$row_empleado['nombre']." ".$row_empleado['apellido_paterno']." ".$row_empleado['apellido_materno']."</option>";
    }
}
?>
