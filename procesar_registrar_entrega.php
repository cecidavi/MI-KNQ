<?php
session_start();
include('conexion.php');

$response = ['success' => false, 'message' => ''];

if (isset($_POST['empleado'], $_POST['fecha_entrega']) && isset($_FILES['foto_poliza'])) {
    $id_empleado = $_POST['empleado'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $foto_poliza = $_FILES['foto_poliza'];

    // Subir la foto de la póliza
    $target_dir = "uploads/polizas/";
    $target_file = $target_dir . basename($foto_poliza["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificación de imagen
    $check = getimagesize($foto_poliza["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $response['message'] = "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    // Verificación de existencia del archivo
    if (file_exists($target_file)) {
        $response['message'] = "El archivo ya existe.";
        $uploadOk = 0;
    }

    // Verificación del tamaño del archivo
    if ($foto_poliza["size"] > 5000000) { // 5MB max
        $response['message'] = "El archivo es demasiado grande.";
        $uploadOk = 0;
    }

    // Verificación del formato de archivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
        $response['message'] = "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        $uploadOk = 0;
    }

    // Proceder solo si la foto es válida
    if ($uploadOk == 1) {
        // Subir el archivo
        if (move_uploaded_file($foto_poliza["tmp_name"], $target_file)) {
            // Depuración: Verificar valores
            var_dump($id_empleado, $fecha_entrega, $target_file); // Verifica los valores

            // Insertar en la base de datos
            $sql = "INSERT INTO entregas_pilas (id_empleado, fecha_entrega, foto_poliza) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id_empleado, $fecha_entrega, $target_file);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Entrega registrada exitosamente.";
            } else {
                $response['message'] = "Error al registrar la entrega: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $response['message'] = "Error al subir la foto de la póliza.";
        }
    }
} else {
    $response['message'] = "Todos los campos son requeridos.";
}

$conn->close();
echo json_encode($response);
?>
