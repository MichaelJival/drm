Entendido. Aquí tienes el código completo de upload.php que incluye la subida del archivo, el procesamiento con FFmpeg, y la actualización del estado del video:
phpCopy<?php
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

                    $sql = "INSERT INTO videos (id_video, nombre_video, url_video, fecha, estado) VALUES (?, ?, ?, ?, 'PROCESANDO')";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ssss", $videoId, $fileName, $urlVideo, $fechaSubida);

                    if ($stmt->execute()) {
                        $response = array(
                            'status' => 'success',
                            'fileName' => $fileName,
                            'fileSize' => filesize($finalFilePath),
                            'uploadDate' => $fechaSubida,
                            'videoId' => $videoId,
                            'estado' => 'PROCESANDO'
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
?>
Este código hace lo siguiente:

Sube el archivo por chunks.
Cuando se completa la subida, inserta la información del video en la base de datos con el estado "PROCESANDO".
Inicia el procesamiento del video en segundo plano llamando a un script process_video.php.

Ahora, necesitas crear el archivo process_video.php que se encargará del procesamiento con FFmpeg:
phpCopy<?php
// process_video.php
include("/home/drm/public_html/conexion/conexion.php");

$videoId = $argv[1];

// Obtener información del video
$sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $videoId);
$stmt->execute();
$result = $stmt->get_result();
$video = $result->fetch_assoc();
$stmt->close();

if ($video) {
    $inputFile = $video['url_video'];
    $outputDir = 'processed_videos/' . $videoId . '/';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    $m3u8File = $outputDir . 'playlist.m3u8';
    $segmentPattern = $outputDir . 'segment_%03d.ts';

    // Comando FFmpeg
    $ffmpegPath = '/usr/bin/ffmpeg'; // Ajusta esta ruta según tu sistema
    $command = "$ffmpegPath -i $inputFile -c:v libx264 -c:a aac -hls_time 10 -hls_list_size 0 -f hls $m3u8File";

    exec($command, $output, $returnVar);

    if ($returnVar === 0) {
        // Actualizar el estado del video a READY
        $sql = "UPDATE videos SET estado = 'READY', url_processed = ? WHERE id_video = ?";
        $stmt = $conexion->prepare($sql);
        $processedUrl = $m3u8File;
        $stmt->bind_param("ss", $processedUrl, $videoId);
        $stmt->execute();
        $stmt->close();
    } else {
        // Manejar el error
        error_log("Error procesando el video $videoId: " . implode("\n", $output));
    }
} else {
    error_log("No se encontró el video con ID: $videoId");
}

$conexion->close();
?>
Este script process_video.php se encarga de:

Procesar el video con FFmpeg.
Actualizar el estado del video a "READY" una vez completado el procesamiento.

En el frontend, deberás implementar una función que verifique periódicamente el estado del video y actualice la interfaz de usuario en consecuencia. Puedes usar AJAX para hacer estas verificaciones. Cuando el estado cambie a "READY", actualiza la etiqueta y detén la animación de la barra de progreso.Add to Conversation