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

    $uploadFileDir = '/home/drm/videos/';

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
                         sleep(5);       
                        // Iniciar el procesamiento con FFmpeg en segundo plano
                        $command = "php process_video.php $videoId > /dev/null 2>&1 &";
                        shell_exec($command);

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

define("LOG_FILE", "/home/drm/public_html/dashboard/LOGS.log");

function logMessage($message) {
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "[$timestamp] [UPLOAD] $message\n";
    error_log($logEntry, 3, LOG_FILE);
}
//logMessage("Iniciando script de carga");

// conexion
include("/home/drm/public_html/conexion/conexion.php");
//logMessage("Conexión incluida");

function exception_error_handler($severity, $message, $file, $line) {
    logMessage("Error capturado: $message in $file on line $line");
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");

if (!isset($_SESSION['id'])) {
    logMessage("Sesión no iniciada. Redirigiendo a login.php");
    header("location: ../login.php");
    exit;
}

function generateVideoId() {
    $data = uniqid('', true) . microtime(true) . mt_rand() . bin2hex(random_bytes(10));
    $hash = substr(hash('sha256', $data), 0, 32);
    logMessage("VideoId generado: $hash");
    return $hash;
}

function generateEncryptionKey($videoId) {
    logMessage("Generando clave de encriptación para videoId: $videoId");
    $segmentDir = '/home/drm/public_html/dashboard/processed_videos/' . $videoId . '/';
    $keyfile = $segmentDir . 'enc.key';
    $keyinfoFile = $segmentDir . 'enc.keyinfo';
    $baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";

    if (!is_dir($segmentDir)) {
        logMessage("Creando directorio: $segmentDir");
        if (!mkdir($segmentDir, 0777, true)) {
            logMessage("Error al crear el directorio: $segmentDir");
            throw new Exception("No se pudo crear el directorio de segmentos");
        }
    }

    $encryptionKey = bin2hex(openssl_random_pseudo_bytes(16));
    logMessage("Clave de encriptación generada");

    if (file_put_contents($keyfile, $encryptionKey) === false) {
        logMessage("Error al escribir el archivo de clave: $keyfile");
        throw new Exception("No se pudo escribir el archivo de clave");
    }
    logMessage("Archivo de clave creado: $keyfile");

    $iv = bin2hex(openssl_random_pseudo_bytes(16));
    $keyinfoContent = implode("\n", [$baseUrl . "enc.key", $baseUrl . "enc.key", $iv]);
    
    if (file_put_contents($keyinfoFile, $keyinfoContent) === false) {
        logMessage("Error al escribir el archivo keyinfo: $keyinfoFile");
        throw new Exception("No se pudo escribir el archivo keyinfo");
    }
    logMessage("Archivo keyinfo creado: $keyinfoFile");

    return [
        'key' => $encryptionKey,
        'iv' => $iv,
        'keyfile' => $keyfile,
        'keyinfoFile' => $keyinfoFile
    ];
}

$response = array();

//logMessage("Método de solicitud: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';
    $chunkNumber = isset($_POST['chunkNumber']) ? intval($_POST['chunkNumber']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? intval($_POST['totalChunks']) : 0;

    //logMessage("Datos recibidos - Nombre de archivo: $fileName, Chunk: $chunkNumber/$totalChunks");

    $uploadFileDir = '/home/drm/videos/';

    if (!is_dir($uploadFileDir)) {
        logMessage("Creando directorio de carga: $uploadFileDir");
        if (!mkdir($uploadFileDir, 0777, true)) {
            logMessage("Error al crear el directorio de carga");
            $response = array('status' => 'error', 'message' => 'No se pudo crear el directorio de carga');
            echo json_encode($response);
            exit;
        }
    }

    $tempFilePath = $uploadFileDir . $fileName . '.part';
    //logMessage("Ruta del archivo temporal: $tempFilePath");

    if (isset($_FILES['chunk']) && $_FILES['chunk']['error'] === UPLOAD_ERR_OK) {
        //logMessage("Chunk recibido correctamente");
        $chunkData = file_get_contents($_FILES['chunk']['tmp_name']);
        
        $fileHandle = fopen($tempFilePath, ($chunkNumber === 0) ? 'wb' : 'ab');
        if ($fileHandle === false) {
            //logMessage("Error al abrir el archivo temporal: $tempFilePath");
            $response = array('status' => 'error', 'message' => 'No se pudo abrir el archivo temporal');
        } else {
            if (fwrite($fileHandle, $chunkData) === false) {
                //logMessage("Error al escribir el chunk en el archivo temporal");
                $response = array('status' => 'error', 'message' => 'Error al escribir el chunk');
            } else {
                fclose($fileHandle);
                //logMessage("Chunk escrito correctamente");

                if ($chunkNumber === $totalChunks - 1) {
                    logMessage("Último chunk recibido, procesando archivo completo");
                    $finalFilePath = $uploadFileDir . $fileName;
                    if (!rename($tempFilePath, $finalFilePath)) {
                        logMessage("Error al renombrar el archivo temporal a final: $tempFilePath -> $finalFilePath");
                        $response = array('status' => 'error', 'message' => 'Error al finalizar el archivo');
                    } else {
                        logMessage("Archivo renombrado correctamente: $finalFilePath");

                        $videoId = generateVideoId();
                        $urlVideo = $finalFilePath;
                        $fechaSubida = date("Y-m-d H:i:s");

                        try {
                            logMessage("Generando clave de encriptación");
                            $encryptionData = generateEncryptionKey($videoId);
                            logMessage("Clave de encriptación generada exitosamente");

                            $sql = "INSERT INTO videos (id_video, nombre_video, url_video, fecha, estado) VALUES (?, ?, ?, ?, 'PROCESSING')";
                            logMessage("Preparando consulta SQL: $sql");
                            $stmt = $conexion->prepare($sql);
                            if ($stmt === false) {
                                logMessage("Error en la preparación de la consulta: " . $conexion->error);
                                throw new Exception("Error en la preparación de la consulta");
                            }
                            $stmt->bind_param("ssss", $videoId, $fileName, $urlVideo, $fechaSubida);

                            logMessage("Ejecutando consulta SQL");
                            if ($stmt->execute()) {
                                logMessage("Datos insertados correctamente en la base de datos");
                                $response = array(
                                    'status' => 'success',
                                    'fileName' => $fileName,
                                    'fileSize' => filesize($finalFilePath),
                                    'uploadDate' => $fechaSubida,
                                    'videoId' => $videoId,
                                    'estado' => 'PROCESSING'
                                );
                                logMessage("Esperando 5 segundos antes de iniciar el procesamiento");
                                sleep(3);       
                                logMessage("Iniciando procesamiento de video en segundo plano");
                                $command = "php process_video.php $videoId > /dev/null 2>&1 &";
                                shell_exec($command);
                                logMessage("Procesamiento de video iniciado");
                            } else {
                                logMessage("Error al insertar datos en la base de datos: " . $stmt->error);
                                $response = array(
                                    'status' => 'error',
                                    'message' => 'Error al insertar datos en la base de datos: ' . $stmt->error
                                );
                            }

                            $stmt->close();
                        } catch (Exception $e) {
                            logMessage("Excepción capturada: " . $e->getMessage());
                            $response = array(
                                'status' => 'error',
                                'message' => 'Error durante el procesamiento: ' . $e->getMessage()
                            );
                        }
                    }
                } else {
                    //logMessage("Chunk procesado correctamente");
                    $response = array('status' => 'success', 'message' => 'Chunk recibido');
                }
            }
        }
    } else {
        logMessage("Error al recibir el chunk: " . print_r($_FILES, true));
        $response = array('status' => 'error', 'message' => 'No se recibió el chunk o hubo un error en la subida');
    }
} else {
    logMessage("Método de solicitud inválido");
    $response = array('status' => 'error', 'message' => 'Método de solicitud inválido');
}

logMessage("Respuesta final: " . json_encode($response));
echo json_encode($response);
