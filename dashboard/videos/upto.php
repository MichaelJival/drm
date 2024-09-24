<?php
// Configuración
$uploadDir = 'uploads_files/';
$chunkSize = 1024 * 1024; // 1 MB

// Asegúrate de que el directorio de subidas existe
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Función para registrar errores
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, 'upload_errors.log');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener información del chunk
    $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';
    $chunkNumber = isset($_POST['chunkNumber']) ? intval($_POST['chunkNumber']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? intval($_POST['totalChunks']) : 0;

    // Validar el nombre del archivo
    $fileName = preg_replace("/[^a-zA-Z0-9.-]/", "_", $fileName);

    $targetPath = $uploadDir . $fileName;

    // Manejar la subida del chunk
    if (isset($_FILES['chunk']) && $_FILES['chunk']['error'] === UPLOAD_ERR_OK) {
        $chunkData = file_get_contents($_FILES['chunk']['tmp_name']);
        
        // Abrir o crear el archivo
        $fileHandle = fopen($targetPath, ($chunkNumber === 0) ? 'wb' : 'ab');

        if ($fileHandle === false) {
            logError("No se pudo abrir el archivo: $targetPath");
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al abrir el archivo']);
            exit;
        }

        // Escribir el chunk
        if (fwrite($fileHandle, $chunkData) === false) {
            logError("No se pudo escribir en el archivo: $targetPath");
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al escribir el chunk']);
            fclose($fileHandle);
            exit;
        }

        fclose($fileHandle);

        // Verificar si es el último chunk
        if ($chunkNumber === $totalChunks - 1) {
            // Aquí puedes agregar validaciones adicionales o procesamiento post-subida
            echo json_encode(['success' => true, 'message' => 'Archivo subido completamente']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Chunk recibido']);
        }
    } else {
        logError("Error en la subida del chunk para el archivo: $fileName");
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error en la subida del chunk']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>