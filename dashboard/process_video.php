<?php
//process_video_optimized.php
include("/home/drm/public_html/conexion/conexion.php");


//define('ERROR_LOG_FILE', '/home/drm/public_html/dashboard/LOGS.log');
define('FFMPEG_PATH', '/usr/bin/ffmpeg');
define('SEGMENT_DIR_BASE', '/home/drm/public_html/dashboard/processed_videos/');
define('BASE_IMAGE_PATH', '/home/drm/public_html/portadas/');
define("LOG_FILE", "/home/drm/public_html/dashboard/LOGS.log");

function logMessage($message) {
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "[$timestamp] [PROCESS] $message\n";
    error_log($logEntry, 3, LOG_FILE);
}
logMessage("Script cargando...");
sleep(30);

function executeSql($conexion, $query, $types = '', ...$params) {
    $stmt = $conexion->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("SQL error: " . $stmt->error);
    }
    return $stmt;
}

function checkCliEnvironment($argc) {
    if (PHP_SAPI !== 'cli' || $argc < 2) {
        logMessage("Este script debe ser ejecutado desde la línea de comandos con un ID de video");
        die("Error: Uso: php process_video_optimized.php [id_video]\n");
    }
}

 logMessage("Script iniciado...");

try {
    checkCliEnvironment($argc);

    $videoId = $argv[1];
    $sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";
    $result = executeSql($conexion, $sql, 's', $videoId)->get_result();
    $video = $result->fetch_assoc();
    $result->close();

    if (!$video) {
        throw new Exception("Video no encontrado con ID: $videoId");
    }

    $inputFile = $video['url_video'];
    if (!file_exists($inputFile)) {
        throw new Exception("Archivo de video no encontrado: $inputFile");
    }

    $segmentDir = SEGMENT_DIR_BASE . $videoId . "/";
    $m3u8File = $segmentDir . 'playlist.m3u8';
    $segmentPattern = $segmentDir . 'segment_%03d.ts';
    
    $keyfile = $segmentDir . 'enc.key';
    $keyinfoFile = $segmentDir . 'enc.keyinfo';
       
    
    $baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";
    
    if (!is_dir($segmentDir) && !mkdir($segmentDir, 0777, true)) {
        throw new Exception("Error al crear el directorio de segmentos: $segmentDir");
    }

    $coverImagePath = BASE_IMAGE_PATH . $videoId . '.jpg';

    // Antes de ejecutar FFmpeg
    logMessage("Verificando archivos de entrada:");
    logMessage("Archivo de entrada existe: " . (file_exists($inputFile) ? "Sí" : "No"));
    logMessage("Directorio de segmentos existe: " . (is_dir($segmentDir) ? "Sí" : "No"));


    $command1 = sprintf(
        "%s -v debug -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($keyinfoFile),
        escapeshellarg($segmentPattern),
        escapeshellarg($baseUrl),
        escapeshellarg($m3u8File)
        );

        logMessage("Ejecutando comando FFmpeg (HLS): " . $command1);
        exec($command1 . " 2>&1", $output1, $returnVar1);
        //shell_exec($command1 . " 2>&1");
        logMessage("Salida de FFmpeg (HLS): " . $output1, $returnVar1);

        //logMessage("Código de salida de FFmpeg (HLS): " . $returnVar1);
        //sleep(5);

        $command2 = sprintf(
        "%s -v debug -i %s -vf \"select='eq(pict_type\\,I)*gte(n\\,3)',scale=200:-1\" -frames:v 1 %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($coverImagePath)
        );

        logMessage("Ejecutando comando FFmpeg (Miniatura): " . $command2);
        exec($command2 . " 2>&1", $output2, $returnVar2);
        $output2 = exec($command2 . " 2>&1");
        //logMessage("Salida de FFmpeg (Miniatura): " . $output2);
        
        // Después de ejecutar FFmpeg
        logMessage("Verificando archivos de salida:");
        logMessage("Archivo m3u8 existe: " . (file_exists($m3u8File) ? "Sí" : "No"));
        logMessage("Número de segmentos TS: " . count(glob($segmentDir . "*.ts")));

        /*if ($returnVar1 !== 0 || !file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
            throw new Exception("Error al procesar el video. Código de salida: $returnVar1");
        }*/
        try {
            // Código que puede lanzar excepciones
            
            if (!file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
                throw new Exception("Error al procesar el video. Archivos de salida no encontrados.");
            }
        
        } catch (Exception $e) {
            executeSql($conexion, "UPDATE videos SET estado = 'ERROR' WHERE id_video = ?", 's', $videoId);
            logMessage($e->getMessage());
            die($e->getMessage() . "\n");
        }
           
        


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    $processedUrl = $baseUrl . 'playlist.m3u8';
    executeSql($conexion, "UPDATE videos SET url_video = ? WHERE id_video = ?", 'ss', $processedUrl, $videoId);
    $content = file_get_contents($m3u8File);
    if ($content === false) {
        throw new Exception("Error al leer el archivo m3u8 para el video $videoId");
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);
    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode($iv)
    ];
    file_put_contents($segmentDir . $videoId . '.json', json_encode($responseData));
    //////////////////////////////////////////////////////////////////////////////////////
    executeSql($conexion, "UPDATE videos SET estado = 'READY' WHERE id_video = ?", 's', $videoId);
    logMessage("Video procesado exitosamente");
    logMessage("..................................................................................");
    } catch (Exception $e) {
        logMessage($e->getMessage());
        die($e->getMessage() . "\n");
    } finally {
        $conexion->close();
    }
    //////////////////////////////////////////////////////////////////////////////////////////////
    

  
  
