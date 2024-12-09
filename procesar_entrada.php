<?php
session_start();

include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pieza = $_POST['pieza'];
    $cantidad = $_POST['cantidad'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    if (!empty($id_pieza) && !empty($cantidad) && !empty($fecha) && !empty($hora)) {
        // Iniciar la transacción
        $conn->begin_transaction();

        try {
            // Insertar la entrada en la tabla entradas
            $sql_entrada = "INSERT INTO entradas (id_pieza, cantidad, fecha, hora) VALUES (?, ?, ?, ?)";
            $stmt_entrada = $conn->prepare($sql_entrada);
            $stmt_entrada->bind_param("iiss", $id_pieza, $cantidad, $fecha, $hora);
            $stmt_entrada->execute();

            // Actualizar la cantidad de piezas en la tabla piezas
            $sql_update_piezas = "UPDATE piezas SET cantidad = cantidad + ? WHERE id_pieza = ?";
            $stmt_update_piezas = $conn->prepare($sql_update_piezas);
            $stmt_update_piezas->bind_param("ii", $cantidad, $id_pieza);
            $stmt_update_piezas->execute();

            // Confirmar la transacción
            $conn->commit();

            echo json_encode(["success" => true, "message" => "Entrada registrada y cantidad actualizada correctamente."]);
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            echo json_encode(["success" => false, "message" => "Error al registrar la entrada: " . $e->getMessage()]);
        }

        $stmt_entrada->close();
        $stmt_update_piezas->close();
    } else {
        echo json_encode(["success" => false, "message" => "Por favor, complete todos los campos."]);
    }
}

$conn->close();
?>
