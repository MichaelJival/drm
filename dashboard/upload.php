<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

ini_set('max_execution_time', 900); // 15 minutos
ini_set('memory_limit', '1024M');

// conexion
include("/home/drm/public_html/conexion/conexion.php");

function exception_error_handler($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");

if (!isset($_SESSION['id'])) {
    header("location: ../login.php");
    exit;
}

function generateVideoId() {
    $data = uniqid('', true) . microtime(true) . mt_rand() . bin2hex(random_bytes(10));
    $hash = substr(hash('sha256', $data), 0, 32);
    return $hash;
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';
    $chunkNumber = isset($_POST['chunkNumber']) ? intval($_POST['chunkNumber']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? intval($_POST['totalChunks']) : 0;

    $uploadFileDir = 'uploaded_files/';
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0777, true);
    }

    $tempFilePath = $uploadFileDir . $fileName . '.part';

    if (isset($_FILES['chunk']) && $_FILES['chunk']['error'] === UPLOAD_ERR_OK) {
        $chunkData = file_get_contents($_FILES['chunk']['tmp_name']);
        
        $fileHandle = fopen($tempFilePath, ($chunkNumber === 0) ? 'wb' : 'ab');
        if ($fileHandle === false) {
            $response = array('status' => 'error', 'message' => 'No se pudo abrir el archivo temporal');
        } else {
            if (fwrite($fileHandle, $chunkData) === false) {
                $response = array('status' => 'error', 'message' => 'Error al escribir el chunk');
            } else {
                fclose($fileHandle);

                if ($chunkNumber === $totalChunks - 1) {
                    // Último chunk, procesar el archivo completo
                    $finalFilePath = $uploadFileDir . $fileName;
                    rename($tempFilePath, $finalFilePath);

                    $videoId = generateVideoId();
                    $urlVideo = $finalFilePath;
                    $fechaSubida = date("Y-m-d H:i:s");

                    $sql = "INSERT INTO videos (id_video, nombre_video, url_video, fecha) VALUES (?, ?, ?, ?)";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ssss", $videoId, $fileName, $urlVideo, $fechaSubida);

                    if ($stmt->execute()) {
                        $response = array(
                            'status' => 'success',
                            'fileName' => $fileName,
                            'fileSize' => filesize($finalFilePath),
                            'uploadDate' => $fechaSubida,
                            'videoId' => $videoId
                        );
                    } else {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Error al insertar datos en la base de datos: ' . $conexion->error
                        );
                    }

                    $stmt->close();
                } else {
                    $response = array('status' => 'success', 'message' => 'Chunk recibido');
                }
            }
        }
    } else {
        $response = array('status' => 'error', 'message' => 'No se recibió el chunk o hubo un error en la subida');
    }
} else {
    $response = array('status' => 'error', 'message' => 'Método de solicitud inválido');
}

echo json_encode($response);
?>*/


/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

ini_set('max_execution_time', 900); // 15 minutos
ini_set('memory_limit', '1024M');

// conexión
include("/home/drm/public_html/conexion/conexion.php");

function exception_error_handler($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");

if (!isset($_SESSION['id'])) {
    header("location: ../login.php");
    exit;
}

function generateVideoId() {
    $data = uniqid('', true) . microtime(true) . mt_rand() . bin2hex(random_bytes(10));
    $hash = substr(hash('sha256', $data), 0, 32);
    return $hash;
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';
    $chunkNumber = isset($_POST['chunkNumber']) ? intval($_POST['chunkNumber']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? intval($_POST['totalChunks']) : 0;

    $uploadFileDir = 'uploaded_files/';
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0777, true);
    }

    $tempFilePath = $uploadFileDir . $fileName . '.part';

    if (isset($_FILES['chunk']) && $_FILES['chunk']['error'] === UPLOAD_ERR_OK) {
        $chunkData = file_get_contents($_FILES['chunk']['tmp_name']);
        
        $fileHandle = fopen($tempFilePath, ($chunkNumber === 0) ? 'wb' : 'ab');
        if (!$fileHandle) {
            $response = array('status' => 'error', 'message' => 'No se pudo abrir el archivo temporal');
        } else {
            if (fwrite($fileHandle, $chunkData) === false) {
                $response = array('status' => 'error', 'message' => 'Error al escribir el chunk');
            } else {
                fclose($fileHandle);

                if ($chunkNumber === $totalChunks - 1) {
                    // Último chunk, procesar el archivo completo
                    $finalFilePath = $uploadFileDir . $fileName;
                    rename($tempFilePath, $finalFilePath);

                    $videoId = generateVideoId();
                    $urlVideo = $finalFilePath;
                    $fechaSubida = date("Y-m-d H:i:s");

                    $sql = "INSERT INTO videos (id_video, nombre_video, url_video, fecha, status) VALUES (?, ?, ?, ?, 'PROCESSING')";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ssss", $videoId, $fileName, $urlVideo, $fechaSubida);

                    if ($stmt->execute()) {
                        $response = array(
                            'status' => 'success',
                            'fileName' => $fileName,
                            'fileSize' => filesize($finalFilePath),
                            'uploadDate' => $fechaSubida,
                            'videoId' => $videoId
                        );

                        // Inicia el procesamiento del video con FFmpeg en segundo plano
                        $cmd = sprintf(
                            'php %s %s %s %s %s > /dev/null 2>/dev/null &',
                            escapeshellarg('/home/drm/public_html/dashboard/process_video.php'),
                            escapeshellarg($videoId),
                            escapeshellarg($fileName),
                            escapeshellarg($finalFilePath),
                            escapeshellarg($conexion->insert_id)
                        );
                        exec($cmd);
                    } else {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Error al insertar datos en la base de datos: ' . $conexion->error
                        );
                    }

                    $stmt->close();
                } else {
                    $response = array('status' => 'success', 'message' => 'Chunk recibido');
                }
            }
        }
    } else {
        $response = array('status' => 'error', 'message' => 'No se recibió el chunk o hubo un error en la subida');
    }
} else {
    $response = array('status' => 'error', 'message' => 'Método de solicitud inválido');
}

echo json_encode($response);*/





ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

ini_set('max_execution_time', 900); // 15 minutos
ini_set('memory_limit', '1024M');

// conexion
include("/home/drm/public_html/conexion/conexion.php");

function exception_error_handler($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");

if (!isset($_SESSION['id'])) {
    header("location: ../login.php");
    exit;
}

function generateVideoId() {
    $data = uniqid('', true) . microtime(true) . mt_rand() . bin2hex(random_bytes(10));
    $hash = substr(hash('sha256', $data), 0, 32);
    return $hash;
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';
    $chunkNumber = isset($_POST['chunkNumber']) ? intval($_POST['chunkNumber']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? intval($_POST['totalChunks']) : 0;

    $uploadFileDir = '/home/drm/public_html/dashboard/uploaded_files/';
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0777, true);
    }

    $tempFilePath = $uploadFileDir . $fileName . '.part';

    if (isset($_FILES['chunk']) && $_FILES['chunk']['error'] === UPLOAD_ERR_OK) {
        $chunkData = file_get_contents($_FILES['chunk']['tmp_name']);
        
        $fileHandle = fopen($tempFilePath, ($chunkNumber === 0) ? 'wb' : 'ab');
        if ($fileHandle === false) {
            $response = array('status' => 'error', 'message' => 'No se pudo abrir el archivo temporal');
        } else {
            if (fwrite($fileHandle, $chunkData) === false) {
                $response = array('status' => 'error', 'message' => 'Error al escribir el chunk');
            } else {
                fclose($fileHandle);

                if ($chunkNumber === $totalChunks - 1) {
                    // Último chunk, procesar el archivo completo
                    $finalFilePath = $uploadFileDir . $fileName;
                    rename($tempFilePath, $finalFilePath);

                    $videoId = generateVideoId();
                    $urlVideo = $finalFilePath;
                    $fechaSubida = date("Y-m-d H:i:s");

                    $sql = "INSERT INTO videos (id_video, nombre_video, url_video, fecha, estado) VALUES (?, ?, ?, ?, 'PROCESSING')";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ssss", $videoId, $fileName, $urlVideo, $fechaSubida);

                    if ($stmt->execute()) {
                        $response = array(
                            'status' => 'success',
                            'fileName' => $fileName,
                            'fileSize' => filesize($finalFilePath),
                            'uploadDate' => $fechaSubida,
                            'videoId' => $videoId,
                            'estado' => 'PROCESSING'
                        );

                        // Iniciar el procesamiento con FFmpeg en segundo plano
                        $command = "php process_video.php $videoId > /dev/null 2>&1 &";
                        exec($command);

                    } else {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Error al insertar datos en la base de datos: ' . $conexion->error
                        );
                    }

                    $stmt->close();
                } else {
                    $response = array('status' => 'success', 'message' => 'Chunk recibido');
                }
            }
        }
    } else {
        $response = array('status' => 'error', 'message' => 'No se recibió el chunk o hubo un error en la subida');
    }
} else {
    $response = array('status' => 'error', 'message' => 'Método de solicitud inválido');
}

echo json_encode($response);
