<?php
session_start();
include('conexion.php');

$response = ['success' => false, 'message' => ''];

// Verifica que los datos necesarios estén presentes
if (isset($_POST['empleado'], $_POST['fecha_entrega']) && isset($_FILES['foto_poliza'])) {
    $id_empleado = $_POST['empleado'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $foto_poliza = $_FILES['foto_poliza'];

    // Ruta donde se guardarán las fotos
    $target_dir = "uploads/polizas/";  // Esta ruta debe existir en tu servidor o ser creada
    $target_file = $target_dir . basename($foto_poliza["name"]);  // Nombre completo del archivo (incluyendo la ruta)
    $uploadOk = 1;  // Bandera para verificar si todo está bien
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));  // Tipo de archivo

    // Verificar si el archivo es una imagen real
    $check = getimagesize($foto_poliza["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $response['message'] = "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    // Verificar si el archivo ya existe
    if (file_exists($target_file)) {
        $response['message'] = "El archivo ya existe.";
        $uploadOk = 0;
    }

    // Verificar el tamaño del archivo (por ejemplo, máximo 5MB)
    if ($foto_poliza["size"] > 5000000) { // 5MB
        $response['message'] = "El archivo es demasiado grande.";
        $uploadOk = 0;
    }

    // Solo permitir ciertos tipos de archivo (JPG, PNG, JPEG)
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
        $response['message'] = "Solo se permiten archivos JPG, JPEG y PNG.";
        $uploadOk = 0;
    }

    // Si todo está bien, se sube el archivo
    if ($uploadOk == 1) {
        if (move_uploaded_file($foto_poliza["tmp_name"], $target_file)) {
            // Inserta los datos en la base de datos
            $sql = "INSERT INTO entregas_pilas (id_empleado, fecha_entrega, foto_poliza) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id_empleado, $fecha_entrega, $target_file);  // Bind parámetros

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
