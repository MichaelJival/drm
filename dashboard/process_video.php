<?php
//process_video_optimized.php
/*include("/home/drm/public_html/conexion/conexion.php");

define('ERROR_LOG_FILE', '/home/drm/public_html/dashboard/errores.log');
define('FFMPEG_PATH', '/usr/bin/ffmpeg');
define('SEGMENT_DIR_BASE', '/home/drm/public_html/dashboard/processed_videos/');
define('BASE_IMAGE_PATH', '/home/drm/public_html/portadas/');

function logError($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, ERROR_LOG_FILE);
}

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
        logError("Este script debe ser ejecutado desde la línea de comandos con un ID de video");
        die("Error: Uso: php process_video_optimized.php [id_video]\n");
    }
}

logError("Script iniciado...");

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

    $encryptionKey = bin2hex(openssl_random_pseudo_bytes(16));
    file_put_contents($keyfile, $encryptionKey);
    $iv = bin2hex(openssl_random_pseudo_bytes(16));
    $keyinfoContent = implode("\n", [$baseUrl . "enc.key", $baseUrl . "enc.key", $iv]);
    file_put_contents($keyinfoFile, $keyinfoContent);

    $coverImagePath = BASE_IMAGE_PATH . $videoId . '.jpg';

    // Antes de ejecutar FFmpeg
    logError("Verificando archivos de entrada:");
    logError("Archivo de entrada existe: " . (file_exists($inputFile) ? "Sí" : "No"));
    logError("Directorio de segmentos existe: " . (is_dir($segmentDir) ? "Sí" : "No"));


    $command1 = sprintf(
        "%s -v debug -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($keyinfoFile),
        escapeshellarg($segmentPattern),
        escapeshellarg($baseUrl),
        escapeshellarg($m3u8File)
        );

        logError("Ejecutando comando FFmpeg (HLS): " . $command1);
        //exec($command1 . " 2>&1", $output1, $returnVar1);
        shell_exec($command1 . " 2>&1");
        logError("Salida de FFmpeg (HLS): " . $output1, $returnVar1);

        logError("Código de salida de FFmpeg (HLS): " . $returnVar1);
        sleep(5);

        $command2 = sprintf(
        "%s -v debug -i %s -vf \"select='eq(pict_type\\,I)*gte(n\\,3)',scale=200:-1\" -frames:v 1 %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($coverImagePath)
        );

        logError("Ejecutando comando FFmpeg (Miniatura): " . $command2);
        //exec($command2 . " 2>&1", $output2, $returnVar2);
        $output2 = shell_exec($command2 . " 2>&1");
        //logError("Salida de FFmpeg (Miniatura): " . $output2);
        
        // Después de ejecutar FFmpeg
        logError("Verificando archivos de salida:");
        logError("Archivo m3u8 existe: " . (file_exists($m3u8File) ? "Sí" : "No"));
        logError("Número de segmentos TS: " . count(glob($segmentDir . "*.ts")));

        if ($returnVar !== 0 || !file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
            throw new Exception("Error al procesar el video. Código de salida: $returnVar");
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
    logError("Video procesado exitosamente");
    echo "Video procesado exitosamente\n";
    logError("..................................................................................");
    } catch (Exception $e) {
        logError($e->getMessage());
        die($e->getMessage() . "\n");
    } finally {
        $conexion->close();
    }
    //////////////////////////////////////////////////////////////////////////////////////////////
    */

  
  
/*error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando script...\n";

include("/home/drm/public_html/conexion/conexion.php");
echo "Conexión incluida\n";

define('ERROR_LOG_FILE', '/home/drm/public_html/dashboard/errores.log');
define('FFMPEG_PATH', '/usr/bin/ffmpeg');
define('SEGMENT_DIR_BASE', '/home/drm/public_html/dashboard/processed_videos/');
define('BASE_IMAGE_PATH', '/home/drm/public_html/portadas/');

echo "Constantes definidas\n";

function logError($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, ERROR_LOG_FILE);
    echo $message . "\n"; // Imprimir también en la salida estándar
}

function executeSql($conexion, $query, $types = '', ...$params) {
    echo "Ejecutando SQL: $query\n";
    $stmt = $conexion->prepare($query);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
    }
    echo "SQL ejecutado con éxito\n";
    return $stmt;
}

function checkCliEnvironment($argc) {
    echo "Verificando entorno CLI\n";
    if (PHP_SAPI !== 'cli' || $argc < 2) {
        logError("Este script debe ser ejecutado desde la línea de comandos con un ID de video");
        die("Error: Uso: php process_video_optimized.php [id_video]\n");
    }
    echo "Entorno CLI verificado\n";
}

logError("Script iniciado...");

try {
    checkCliEnvironment($argc);

    $videoId = $argv[1];
    echo "ID de video: $videoId\n";

    $sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";
    $result = executeSql($conexion, $sql, 's', $videoId)->get_result();
    $video = $result->fetch_assoc();
    $result->close();

    if (!$video) {
        throw new Exception("Video no encontrado con ID: $videoId");
    }
    echo "Video encontrado: " . print_r($video, true) . "\n";

    $inputFile = $video['url_video'];
    echo "Archivo de entrada: $inputFile\n";
    if (!file_exists($inputFile)) {
        throw new Exception("Archivo de video no encontrado: $inputFile");
    }
    echo "Archivo de entrada existe\n";

    $segmentDir = SEGMENT_DIR_BASE . $videoId . "/";
    $m3u8File = $segmentDir . 'playlist.m3u8';
    $segmentPattern = $segmentDir . 'segment_%03d.ts';
    
    $keyfile = $segmentDir . 'enc.key';
    $keyinfoFile = $segmentDir . 'enc.keyinfo';
       
    $baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";
    
    echo "Creando directorio: $segmentDir\n";
    if (!is_dir($segmentDir) && !mkdir($segmentDir, 0777, true)) {
        throw new Exception("Error al crear el directorio de segmentos: $segmentDir");
    }
    echo "Directorio creado o ya existente\n";

    echo "Generando clave de encriptación\n";
    $encryptionKey = bin2hex(openssl_random_pseudo_bytes(16));
    file_put_contents($keyfile, $encryptionKey);
    $iv = bin2hex(openssl_random_pseudo_bytes(16));
    $keyinfoContent = implode("\n", [$baseUrl . "enc.key", $baseUrl . "enc.key", $iv]);
    file_put_contents($keyinfoFile, $keyinfoContent);
    echo "Clave de encriptación generada\n";

    $coverImagePath = BASE_IMAGE_PATH . $videoId . '.jpg';

    echo "Verificando archivos de entrada:\n";
    echo "Archivo de entrada existe: " . (file_exists($inputFile) ? "Sí" : "No") . "\n";
    echo "Directorio de segmentos existe: " . (is_dir($segmentDir) ? "Sí" : "No") . "\n";

    $command1 = sprintf(
        "%s -v debug -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($keyinfoFile),
        escapeshellarg($segmentPattern),
        escapeshellarg($baseUrl),
        escapeshellarg($m3u8File)
    );
    
    logError("Comando FFmpeg (HLS): $command1");
    echo "Ejecutando comando FFmpeg (HLS): $command1\n";
    $output1 = shell_exec($command1 . " 2>&1");
    echo "Salida de FFmpeg (HLS): $output1\n";
    
    // ... (resto del código sin cambios)
    
    $command2 = sprintf(
        "%s -v debug -i %s -vf \"select='eq(pict_type\\,I)*gte(n\\,3)',scale=200:-1\" -frames:v 1 %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($coverImagePath)
    );
    
    logError("Comando FFmpeg (Miniatura): $command2");
    echo "Ejecutando comando FFmpeg (Miniatura): $command2\n";
    $output2 = shell_exec($command2 . " 2>&1");
    echo "Salida de FFmpeg (Miniatura): $output2\n";
    

    echo "Verificando archivos de salida:\n";
    echo "Archivo m3u8 existe: " . (file_exists($m3u8File) ? "Sí" : "No") . "\n";
    echo "Número de segmentos TS: " . count(glob($segmentDir . "*.ts")) . "\n";

    if (!file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
        throw new Exception("Error al procesar el video. Archivos de salida no encontrados.");
    }

    echo "Actualizando URL del video en la base de datos\n";
    $processedUrl = $baseUrl . 'playlist.m3u8';
    executeSql($conexion, "UPDATE videos SET url_video = ? WHERE id_video = ?", 'ss', $processedUrl, $videoId);

    echo "Leyendo contenido del archivo m3u8\n";
    $content = file_get_contents($m3u8File);
    if ($content === false) {
        throw new Exception("Error al leer el archivo m3u8 para el video $videoId");
    }

    echo "Encriptando contenido del m3u8\n";
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);
    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode($iv)
    ];
    file_put_contents($segmentDir . $videoId . '.json', json_encode($responseData));

    echo "Actualizando estado del video a READY\n";
    executeSql($conexion, "UPDATE videos SET estado = 'READY' WHERE id_video = ?", 's', $videoId);

    logError("Video procesado exitosamente");
    echo "Video procesado exitosamente\n";
    logError("..................................................................................");
} catch (Exception $e) {
    logError("Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
    die();
} finally {
    echo "Cerrando conexión a la base de datos\n";
    $conexion->close();
}*/


/*error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando script...\n";

include("/home/drm/public_html/conexion/conexion.php");
echo "Conexión incluida\n";

//define('ERROR_LOG_FILE', '/home/drm/public_html/dashboard/errores.log');
define('FFMPEG_PATH', '/usr/bin/ffmpeg');
define('SEGMENT_DIR_BASE', '/home/drm/public_html/dashboard/processed_videos/');
define('BASE_IMAGE_PATH', '/home/drm/public_html/portadas/');

echo "Constantes definidas\n";

/*function logError($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, ERROR_LOG_FILE);
    echo $message . "\n";
}*/

/*define("LOG_FILE", "/home/drm/public_html/dashboard/errores.txt"); // Define tu archivo de logs
function logMessage($message, $level = "INFO") {
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "[$timestamp] [$level] $message\n";
    error_log($logEntry, 3, LOG_FILE);
}


function executeSql($conexion, $query, $types = '', ...$params) {
    echo "Ejecutando SQL: $query\n";
    $stmt = $conexion->prepare($query);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
    }
    echo "SQL ejecutado con éxito\n";
    return $stmt;
}

function checkCliEnvironment($argc) {
    echo "Verificando entorno CLI\n";
    if (PHP_SAPI !== 'cli' || $argc < 2) {
        logError("Este script debe ser ejecutado desde la línea de comandos con un ID de video");
        die("Error: Uso: php process_video_optimized.php [id_video]\n");
    }
    echo "Entorno CLI verificado\n";
}

function executeFFmpegCommand($command, $maxAttempts = 3) {
    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        logError("Intento $attempt de $maxAttempts - Ejecutando comando FFmpeg: $command");
        $output = shell_exec($command . " 2>&1");
        if ($output !== null && strpos($output, 'error') === false) {
            return $output;
        }
        logError("Intento $attempt fallido. Salida: " . $output);
        if ($attempt < $maxAttempts) {
            sleep(5);
        }
    }
    throw new Exception("FFmpeg falló después de $maxAttempts intentos");
}

//logError("Script iniciado...");
logMessage("Script Iniciado", "INFO");

try {
    checkCliEnvironment($argc);

    $videoId = $argv[1];
    echo "ID de video: $videoId\n";

    $sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";
    $result = executeSql($conexion, $sql, 's', $videoId)->get_result();
    $video = $result->fetch_assoc();
    $result->close();

    if (!$video) {
        throw new Exception("Video no encontrado con ID: $videoId");
    }
    echo "Video encontrado: " . print_r($video, true) . "\n";

    $inputFile = $video['url_video'];
    echo "Archivo de entrada: $inputFile\n";
    if (!file_exists($inputFile)) {
        throw new Exception("Archivo de video no encontrado: $inputFile");
    }
    echo "Archivo de entrada existe\n";

    $segmentDir = SEGMENT_DIR_BASE . $videoId . "/";
    $m3u8File = $segmentDir . 'playlist.m3u8';
    $segmentPattern = $segmentDir . 'segment_%03d.ts';
    
    $keyfile = $segmentDir . 'enc.key';
    $keyinfoFile = $segmentDir . 'enc.keyinfo';
       
    $baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";
    
    echo "Creando directorio: $segmentDir\n";
    if (!is_dir($segmentDir) && !mkdir($segmentDir, 0777, true)) {
        throw new Exception("Error al crear el directorio de segmentos: $segmentDir");
    }
    echo "Directorio creado o ya existente\n";

    echo "Generando clave de encriptación\n";
    $encryptionKey = bin2hex(openssl_random_pseudo_bytes(16));
    file_put_contents($keyfile, $encryptionKey);
    $iv = bin2hex(openssl_random_pseudo_bytes(16));
    $keyinfoContent = implode("\n", [$baseUrl . "enc.key", $baseUrl . "enc.key", $iv]);
    file_put_contents($keyinfoFile, $keyinfoContent);
    echo "Clave de encriptación generada\n";

    $coverImagePath = BASE_IMAGE_PATH . $videoId . '.jpg';

    echo "Verificando archivos de entrada:\n";
    echo "Archivo de entrada existe: " . (file_exists($inputFile) ? "Sí" : "No") . "\n";
    echo "Directorio de segmentos existe: " . (is_dir($segmentDir) ? "Sí" : "No") . "\n";

    $command1 = sprintf(
        "%s -v debug -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($keyinfoFile),
        escapeshellarg($segmentPattern),
        escapeshellarg($baseUrl),
        escapeshellarg($m3u8File)
    );
    
    logError("Comando FFmpeg (HLS): $command1");
    echo "Ejecutando comando FFmpeg (HLS): $command1\n";
    $output1 = executeFFmpegCommand($command1);
    echo "Salida de FFmpeg (HLS): $output1\n";

    echo "Esperando 5 segundos...\n";
    sleep(5);

    $command2 = sprintf(
        "%s -v debug -i %s -vf \"select='eq(pict_type\\,I)*gte(n\\,3)',scale=200:-1\" -frames:v 1 %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($coverImagePath)
    );
    
    logError("Comando FFmpeg (Miniatura): $command2");
    echo "Ejecutando comando FFmpeg (Miniatura): $command2\n";
    $output2 = executeFFmpegCommand($command2);
    echo "Salida de FFmpeg (Miniatura): $output2\n";

    echo "Verificando archivos de salida:\n";
    echo "Archivo m3u8 existe: " . (file_exists($m3u8File) ? "Sí" : "No") . "\n";
    echo "Número de segmentos TS: " . count(glob($segmentDir . "*.ts")) . "\n";

    if (!file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
        throw new Exception("Error al procesar el video. Archivos de salida no encontrados.");
    }

    echo "Actualizando URL del video en la base de datos\n";
    $processedUrl = $baseUrl . 'playlist.m3u8';
    executeSql($conexion, "UPDATE videos SET url_video = ? WHERE id_video = ?", 'ss', $processedUrl, $videoId);

    echo "Leyendo contenido del archivo m3u8\n";
    $content = file_get_contents($m3u8File);
    if ($content === false) {
        throw new Exception("Error al leer el archivo m3u8 para el video $videoId");
    }

    echo "Encriptando contenido del m3u8\n";
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);
    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode($iv)
    ];
    file_put_contents($segmentDir . $videoId . '.json', json_encode($responseData));


    echo "Actualizando estado del video a READY\n";
    executeSql($conexion, "UPDATE videos SET estado = 'READY' WHERE id_video = ?", 's', $videoId);

    logError("Video procesado exitosamente");
    echo "Video procesado exitosamente\n";
    logError("..................................................................................");
} catch (Exception $e) {
    logError("Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
    die();
} finally {
    echo "Cerrando conexión a la base de datos\n";
    $conexion->close();
}*/


/*error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando script...\n";

include("/home/drm/public_html/conexion/conexion.php");
echo "Conexión incluida\n";

define('ERROR_LOG_FILE', '/home/drm/public_html/dashboard/errores.log');
define('FFMPEG_PATH', '/usr/bin/ffmpeg');
define('SEGMENT_DIR_BASE', '/home/drm/public_html/dashboard/processed_videos/');
define('BASE_IMAGE_PATH', '/home/drm/public_html/portadas/');

echo "Constantes definidas\n";

function logError($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, ERROR_LOG_FILE);
    echo $message . "\n";
}

function executeSql($conexion, $query, $types = '', ...$params) {
    echo "Ejecutando SQL: $query\n";
    $stmt = $conexion->prepare($query);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
    }
    echo "SQL ejecutado con éxito\n";
    return $stmt;
}

function checkCliEnvironment($argc) {
    echo "Verificando entorno CLI\n";
    if (PHP_SAPI !== 'cli' || $argc < 2) {
        logError("Este script debe ser ejecutado desde la línea de comandos con un ID de video");
        die("Error: Uso: php process_video_optimized.php [id_video]\n");
    }
    echo "Entorno CLI verificado\n";
}

// Función para esperar a que un archivo exista
function waitForFile($filePath, $timeout = 10) {
    $start = time();
    while (!file_exists($filePath)) {
        if (time() - $start > $timeout) {
            throw new Exception("Timeout esperando el archivo: $filePath");
        }
        usleep(100000); // Espera 0.1 segundos
    }
}

logError("Script iniciado...");

try {
    checkCliEnvironment($argc);

    $videoId = $argv[1];
    echo "ID de video: $videoId\n";

    $sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";
    $result = executeSql($conexion, $sql, 's', $videoId)->get_result();
    $video = $result->fetch_assoc();
    $result->close();

    if (!$video) {
        throw new Exception("Video no encontrado con ID: $videoId");
    }
    echo "Video encontrado: " . print_r($video, true) . "\n";

    $inputFile = $video['url_video'];
    echo "Archivo de entrada: $inputFile\n";
    if (!file_exists($inputFile)) {
        throw new Exception("Archivo de video no encontrado: $inputFile");
    }
    echo "Archivo de entrada existe\n";

    $segmentDir = SEGMENT_DIR_BASE . $videoId . "/";
    $m3u8File = $segmentDir . 'playlist.m3u8';
    $segmentPattern = $segmentDir . 'segment_%03d.ts';
    
    $keyfile = $segmentDir . 'enc.key';
    $keyinfoFile = $segmentDir . 'enc.keyinfo';
       
    $baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";
    
    echo "Creando directorio: $segmentDir\n";
    if (!is_dir($segmentDir) && !mkdir($segmentDir, 0777, true)) {
        throw new Exception("Error al crear el directorio de segmentos: $segmentDir");
    }
    echo "Directorio creado o ya existente\n";

    echo "Generando clave de encriptación\n";
    $encryptionKey = bin2hex(openssl_random_pseudo_bytes(16));
    
    // Usar flock para asegurar la escritura completa del archivo
    $keyFileHandle = fopen($keyfile, 'w');
    if (flock($keyFileHandle, LOCK_EX)) {
        fwrite($keyFileHandle, $encryptionKey);
        flock($keyFileHandle, LOCK_UN);
    } else {
        throw new Exception("No se pudo obtener el bloqueo para escribir en $keyfile");
    }
    fclose($keyFileHandle);
    
    $iv = bin2hex(openssl_random_pseudo_bytes(16));
    $keyinfoContent = implode("\n", [$baseUrl . "enc.key", $baseUrl . "enc.key", $iv]);
    
    // Usar flock para asegurar la escritura completa del archivo
    $keyinfoFileHandle = fopen($keyinfoFile, 'w');
    if (flock($keyinfoFileHandle, LOCK_EX)) {
        fwrite($keyinfoFileHandle, $keyinfoContent);
        flock($keyinfoFileHandle, LOCK_UN);
    } else {
        throw new Exception("No se pudo obtener el bloqueo para escribir en $keyinfoFile");
    }
    fclose($keyinfoFileHandle);
    
    echo "Clave de encriptación generada\n";

    // Esperar a que los archivos existan
    waitForFile($keyfile);
    waitForFile($keyinfoFile);

    $coverImagePath = BASE_IMAGE_PATH . $videoId . '.jpg';

    echo "Verificando archivos de entrada:\n";
    echo "Archivo de entrada existe: " . (file_exists($inputFile) ? "Sí" : "No") . "\n";
    echo "Directorio de segmentos existe: " . (is_dir($segmentDir) ? "Sí" : "No") . "\n";

    $command1 = sprintf(
        "%s -v debug -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($keyinfoFile),
        escapeshellarg($segmentPattern),
        escapeshellarg($baseUrl),
        escapeshellarg($m3u8File)
    );
    
    logError("Comando FFmpeg (HLS): $command1");
    echo "Ejecutando comando FFmpeg (HLS): $command1\n";
    $output1 = shell_exec($command1 . " 2>&1");
    echo "Salida de FFmpeg (HLS): $output1\n";
    
    $command2 = sprintf(
        "%s -v debug -i %s -vf \"select='eq(pict_type\\,I)*gte(n\\,3)',scale=200:-1\" -frames:v 1 %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($coverImagePath)
    );
    
    logError("Comando FFmpeg (Miniatura): $command2");
    echo "Ejecutando comando FFmpeg (Miniatura): $command2\n";
    $output2 = shell_exec($command2 . " 2>&1");
    echo "Salida de FFmpeg (Miniatura): $output2\n";

    echo "Verificando archivos de salida:\n";
    echo "Archivo m3u8 existe: " . (file_exists($m3u8File) ? "Sí" : "No") . "\n";
    echo "Número de segmentos TS: " . count(glob($segmentDir . "*.ts")) . "\n";

    if (!file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
        throw new Exception("Error al procesar el video. Archivos de salida no encontrados.");
    }

    echo "Actualizando URL del video en la base de datos\n";
    $processedUrl = $baseUrl . 'playlist.m3u8';
    executeSql($conexion, "UPDATE videos SET url_video = ? WHERE id_video = ?", 'ss', $processedUrl, $videoId);

    echo "Leyendo contenido del archivo m3u8\n";
    $content = file_get_contents($m3u8File);
    if ($content === false) {
        throw new Exception("Error al leer el archivo m3u8 para el video $videoId");
    }

    echo "Encriptando contenido del m3u8\n";
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);
    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode($iv)
    ];
    file_put_contents($segmentDir . $videoId . '.json', json_encode($responseData));

    echo "Actualizando estado del video a READY\n";
    executeSql($conexion, "UPDATE videos SET estado = 'READY' WHERE id_video = ?", 's', $videoId);

    logError("Video procesado exitosamente");
    echo "Video procesado exitosamente\n";
    logError("..................................................................................");
} catch (Exception $e) {
    logError("Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
    die();
} finally {
    echo "Cerrando conexión a la base de datos\n";
    $conexion->close();
}*/




error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando script...\n";

include("/home/drm/public_html/conexion/conexion.php");
echo "Conexión incluida\n";

define('FFMPEG_PATH', '/usr/bin/ffmpeg');
define('SEGMENT_DIR_BASE', '/home/drm/public_html/dashboard/processed_videos/');
define('BASE_IMAGE_PATH', '/home/drm/public_html/portadas/');

echo "Constantes definidas\n";

define("LOG_FILE", "/home/drm/public_html/dashboard/LOGS.log");

function logMessage($message) {
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "[$timestamp] [PROCESS] $message\n";
    error_log($logEntry, 3, LOG_FILE);
}

function executeSql($conexion, $query, $types = '', ...$params) {
    echo "Ejecutando SQL: $query\n";
    $stmt = $conexion->prepare($query);
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conexion->error);
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Error en la ejecución de la consulta: " . $stmt->error);
    }
    echo "SQL ejecutado con éxito\n";
    return $stmt;
}

function checkCliEnvironment($argc) {
    echo "Verificando entorno CLI\n";
    if (PHP_SAPI !== 'cli' || $argc < 2) {
        logMessage("Este script debe ser ejecutado desde la línea de comandos con un ID de video", "ERROR");
        die("Error: Uso: php process_video.php [id_video]\n");
    }
    echo "Entorno CLI verificado\n";
}

function executeFFmpegCommand($command, $maxAttempts = 3) {
    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        logMessage("Intento $attempt de $maxAttempts - Ejecutando comando FFmpeg: $command", "INFO");
        $output = shell_exec($command . " 2>&1");
        if ($output !== null && strpos($output, 'error') === false) {
            return $output;
        }
        logMessage("Intento $attempt fallido. Salida: " . $output, "WARNING");
        if ($attempt < $maxAttempts) {
            sleep(5);
        }
    }
    throw new Exception("FFmpeg falló después de $maxAttempts intentos");
}


logMessage("Iniciando script de carga");

try {
    checkCliEnvironment($argc);

    $videoId = $argv[1];
    echo "ID de video: $videoId\n";

    $sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";
    $result = executeSql($conexion, $sql, 's', $videoId)->get_result();
    $video = $result->fetch_assoc();
    $result->close();

    if (!$video) {
        throw new Exception("Video no encontrado con ID: $videoId");
    }
    echo "Video encontrado: " . print_r($video, true) . "\n";

    $inputFile = $video['url_video'];
    echo "Archivo de entrada: $inputFile\n";
    if (!file_exists($inputFile)) {
        throw new Exception("Archivo de video no encontrado: $inputFile");
    }
    echo "Archivo de entrada existe\n";

    $segmentDir = SEGMENT_DIR_BASE . $videoId . "/";
    $m3u8File = $segmentDir . 'playlist.m3u8';
    $segmentPattern = $segmentDir . 'segment_%03d.ts';
    
    $keyfile = $segmentDir . 'enc.key';
    $keyinfoFile = $segmentDir . 'enc.keyinfo';
       
    $baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";
    
    echo "Verificando directorio: $segmentDir\n";
    if (!is_dir($segmentDir)) {
        throw new Exception("El directorio de segmentos no existe: $segmentDir");
    }
    echo "Directorio verificado\n";

    echo "Verificando archivos de clave de encriptación\n";
    if (!file_exists($keyfile) || !file_exists($keyinfoFile)) {
        throw new Exception("Archivos de clave de encriptación no encontrados");
    }
    echo "Archivos de clave de encriptación verificados\n";

    $coverImagePath = BASE_IMAGE_PATH . $videoId . '.jpg';

    echo "Verificando archivos de entrada:\n";
    echo "Archivo de entrada existe: " . (file_exists($inputFile) ? "Sí" : "No") . "\n";
    echo "Directorio de segmentos existe: " . (is_dir($segmentDir) ? "Sí" : "No") . "\n";

    $command1 = sprintf(
        "%s -v debug -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($keyinfoFile),
        escapeshellarg($segmentPattern),
        escapeshellarg($baseUrl),
        escapeshellarg($m3u8File)
    );
    
    logMessage("Comando FFmpeg (HLS): $command1", "INFO");
    echo "Ejecutando comando FFmpeg (HLS): $command1\n";
    $output1 = executeFFmpegCommand($command1);
    echo "Salida de FFmpeg (HLS): $output1\n";

      
    logMessage("Esperando 5 segundos");
    sleep(5);

    $command2 = sprintf(
        "%s -v debug -i %s -vf \"select='eq(pict_type\\,I)*gte(n\\,3)',scale=200:-1\" -frames:v 1 %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($coverImagePath)
    );
    
    logMessage("Comando FFmpeg (Miniatura): $command2", "INFO");
    echo "Ejecutando comando FFmpeg (Miniatura): $command2\n";
    $output2 = executeFFmpegCommand($command2);
    echo "Salida de FFmpeg (Miniatura): $output2\n";

    echo "Verificando archivos de salida:\n";
    echo "Archivo m3u8 existe: " . (file_exists($m3u8File) ? "Sí" : "No") . "\n";
    echo "Número de segmentos TS: " . count(glob($segmentDir . "*.ts")) . "\n";

    if (!file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
        throw new Exception("Error al procesar el video. Archivos de salida no encontrados.");
    }

    echo "Actualizando URL del video en la base de datos\n";
    $processedUrl = $baseUrl . 'playlist.m3u8';
    executeSql($conexion, "UPDATE videos SET url_video = ? WHERE id_video = ?", 'ss', $processedUrl, $videoId);

    echo "Leyendo contenido del archivo m3u8\n";
    $content = file_get_contents($m3u8File);
    if ($content === false) {
        throw new Exception("Error al leer el archivo m3u8 para el video $videoId");
    }

    echo "Encriptando contenido del m3u8\n";
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $iv = $video['encryption_iv'];
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, hex2bin($iv));
    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode(hex2bin($iv))
    ];
    file_put_contents($segmentDir . $videoId . '.json', json_encode($responseData));

    echo "Actualizando estado del video a READY\n";
    executeSql($conexion, "UPDATE videos SET estado = 'READY' WHERE id_video = ?", 's', $videoId);

    logMessage("Video procesado exitosamente", "INFO");
    echo "Video procesado exitosamente\n";
    logMessage("..................................................................................", "INFO");
} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage(), "ERROR");
    echo "Error: " . $e->getMessage() . "\n";
    die();
} finally {
    echo "Cerrando conexión a la base de datos\n";
    $conexion->close();
}
