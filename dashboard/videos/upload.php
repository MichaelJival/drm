<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

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
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedfileExtensions = array('mp4');
        
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = 'uploaded_files/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $videoId = generateVideoId();
                $nombreVideo = $fileName;
                $urlVideo = $dest_path;
                $fechaSubida = date("Y-m-d H:i:s");

                $sql = "INSERT INTO videos (id_video, nombre_video, url_video, fecha) VALUES (?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssss", $videoId, $nombreVideo, $urlVideo, $fechaSubida);
                
                if ($stmt->execute()) {
                    $response = array(
                        'status' => 'success',
                        'fileName' => $fileName,
                        'fileSize' => $fileSize,
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
                $response = array(
                    'status' => 'error',
                    'message' => 'There was some error moving the file to upload directory.'
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Upload failed. Allowed file types: ' . implode(', ', $allowedfileExtensions)
            );
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'No file uploaded or there was an upload error'
        );
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );
}

echo json_encode($response);
?>