<?php
// process_video.php
include("/home/drm/public_html/conexion/conexion.php");

// Función para registrar errores
function logError($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message . "\n", 3, '/home/drm/public_html/dashboard/errores.log');
}

logError("Script iniciado");

// Verificar entorno CLI
if (PHP_SAPI !== 'cli') {
    logError("Este script debe ser ejecutado desde la línea de comandos");
    die("Error: Este script debe ser ejecutado desde la línea de comandos\n");
}

// Verificar si se proporcionó un ID de video
if ($argc < 2) {
    logError("No se proporcionó un ID de video");
    die("Uso: php process_video.php [id_video]\n");
} else {
    $videoId = $argv[1];
    logError("ID de video proporcionado: $videoId");
}

$videoId = $argv[1];

if (empty($videoId)) {
    logError("El ID del video está vacío");
    die("Error: ID de video no válido\n");
} 


$sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";

if (!($stmt = $conexion->prepare($sql))) {
    logError("Error preparando declaración: " . $conexion->error);
    die("Error preparando declaración SQL\n");
} 

$stmt->bind_param("s", $videoId);

if (!$stmt->execute()) {
    logError("Error ejecutando declaración: " . $stmt->error);
    die("Error ejecutando declaración SQL\n");
} 

$result = $stmt->get_result();
$video = $result->fetch_assoc();
$stmt->close();

if (!$video) {
    logError("No se encontró el video con ID: $videoId");
    die("Video no encontrado\n");
} else  {
    logError("Video encontrado: $videoId");
}

$inputFile = $video['url_video'];
$segmentDir = "/home/drm/public_html/dashboard/processed_videos/" . $videoId . "/";
$m3u8File = $segmentDir . 'playlist.m3u8';
$segmentPattern = $segmentDir . 'segment_%03d.ts';
$ffmpegPath = '/usr/bin/ffmpeg';
$keyfile = $segmentDir . 'enc.key';
$keyinfoFile = $segmentDir . 'enc.keyinfo';
$baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";

if (!file_exists($inputFile)) {
    logError("El archivo de entrada no existe: $inputFile");
    die("Archivo de video no encontrado\n");
} else {
    logError("Archivo de video encontrado: $inputFile");
}

if (!is_dir($segmentDir)) {
    if (!mkdir($segmentDir, 0777, true)) {
        logError("No se pudo crear el directorio de segmentos: $segmentDir");
        die("Error al crear el directorio de segmentos\n");
    } else {
        logError("Directorio de segmentos creado: $segmentDir");
    }
}
sleep(3);
logError("Esperando 3 segundos...");
$encryptionKey = openssl_random_pseudo_bytes(16);
file_put_contents($keyfile, $encryptionKey);
$iv = openssl_random_pseudo_bytes(16);

$keyinfoContent = $baseUrl . "enc.key\n" . $baseUrl . "enc.key" . "\n" . bin2hex($iv);
file_put_contents($keyinfoFile, $keyinfoContent);

$baseImagePath = '/home/drm/public_html/portadas/';
$coverImagePath = $baseImagePath . $videoId . '.jpg';

$command = sprintf(
    "%s -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s && %s -i %s -vf \"select='eq(pict_type\,I)*gte(n\,3)',scale=200:-1\" -frames:v 1 %s",
    escapeshellcmd($ffmpegPath),
    escapeshellarg($inputFile),
    escapeshellarg($keyinfoFile),
    escapeshellarg($segmentPattern),
    escapeshellarg($baseUrl),
    escapeshellarg($m3u8File),
    escapeshellcmd($ffmpegPath),
    escapeshellarg($inputFile),
    escapeshellarg($coverImagePath)
);

if (!function_exists('exec')) {
    logError("La función exec está deshabilitada en este servidor");
    die("Error: La función exec está deshabilitada\n");
}

logError("Ejecutando comando FFmpeg");
exec($command . " 2>&1", $output, $returnVar);
logError("Comando FFmpeg ejecutado");

sleep(5);
logError("Esperando 5 segundos...");

file_put_contents($segmentDir . 'ffmpeg_output.log', implode("\n", $output));

if ($returnVar !== 0) {
    logError("Error procesando el video $videoId. Código de salida: $returnVar");
    logError("Salida de FFmpeg: " . implode("\n", $output));
    die("Error al procesar el video\n");
}

if (!file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
    logError("Los archivos no se crearon correctamente para el video $videoId");
    die("Error: No se generaron los archivos esperados\n");
}

$sql = "UPDATE videos SET url_video = ? WHERE id_video = ?";
$stmt = $conexion->prepare($sql);
$processedUrl = $baseUrl . 'playlist.m3u8';
$stmt->bind_param("ss", $processedUrl, $videoId);
logError(" ID: $m3u8File");
if (!$stmt->execute()) {
    logError("Error al actualizar el estado del video $videoId: " . $stmt->error);
    die("Error al actualizar la base de datos\n");
}

/*sleep(8);
logError("Esperando 8 segundos...");
logError("Encryptando el archivo m3u8 con...");

// Procesar el bloque que estaba comentado
$encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
$content = file_get_contents($m3u8File);
if ($content !== false) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);

    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode($iv)
    ];

    $jsonContent = json_encode($responseData);
    file_put_contents($segmentDir . $videoId . '.json', $jsonContent);
} else {
    logError("Error al leer el archivo m3u8 para el video $videoId");
    die("Error al leer el archivo m3u8\n");
}*/




/*$maxAttempts = 10;
$attempts = 0;
$encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';

while ($attempts < $maxAttempts) {
    if (file_exists($m3u8File)) {
        $content = file_get_contents($m3u8File);
        if ($content !== false) {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
            $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);

            $responseData = [
                'encrypted' => base64_encode($encryptedContent),
                'iv' => base64_encode($iv)
            ];

            $jsonContent = json_encode($responseData);
            file_put_contents($segmentDir . $videoId . '.json', $jsonContent);
            logError("Archivo m3u8 encriptado exitosamente");
            break;
        } else {
            logError("Error al leer el archivo m3u8 para el video $videoId");
            die("Error al leer el archivo m3u8\n");
        }
    } else {
        $attempts++;
        sleep(1);
    }
}

if ($attempts >= $maxAttempts) {
    logError("El archivo m3u8 no se creó después de $maxAttempts intentos");
    die("Error: El archivo m3u8 no se creó\n");
}
*/


$sql = "UPDATE videos SET estado = 'READY' WHERE id_video = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $videoId);

if (!$stmt->execute()) {
    logError("Error al actualizar el estado del video $videoId: " . $stmt->error);
    die("Error al actualizar la base de datos\n");
}

$stmt->close();
$conexion->close();

logError("Video procesado exitosamente");
echo "Video procesado exitosamente\n";
logError(".........................................................................................");
?>

<?php
/* process_video_optimized.php
include("/home/drm/public_html/conexion/conexion.php");

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

    $encryptionKey = openssl_random_pseudo_bytes(16);
    file_put_contents($keyfile, $encryptionKey);
    $iv = openssl_random_pseudo_bytes(16);
    $keyinfoContent = implode("\n", [$baseUrl . "enc.key", $baseUrl . "enc.key", bin2hex($iv)]);
    file_put_contents($keyinfoFile, $keyinfoContent);

    $coverImagePath = BASE_IMAGE_PATH . $videoId . '.jpg';

    $command = sprintf(
        "%s -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s && %s -i %s -vf \"select='eq(pict_type\\,I)*gte(n\\,3)',scale=200:-1\" -frames:v 1 %s",
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($keyinfoFile),
        escapeshellarg($segmentPattern),
        escapeshellarg($baseUrl),
        escapeshellarg($m3u8File),
        escapeshellcmd(FFMPEG_PATH),
        escapeshellarg($inputFile),
        escapeshellarg($coverImagePath)
    );

    if (!function_exists('exec')) {
        throw new Exception("La función exec está deshabilitada en este servidor");
    }

    logError("Ejecutando comando FFmpeg");
    exec($command . " 2>&1", $output, $returnVar);
    logError("Comando FFmpeg ejecutado");

    file_put_contents($segmentDir . 'ffmpeg_output.log', implode("\n", $output));

    if ($returnVar !== 0 || !file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
        throw new Exception("Error al procesar el video. Código de salida: $returnVar");
    }

    $processedUrl = $baseUrl . 'playlist.m3u8';
    executeSql($conexion, "UPDATE videos SET url_video = ? WHERE id_video = ?", 'ss', $processedUrl, $videoId);

    $content = file_get_contents($m3u8File);
    if ($content === false) {
        throw new Exception("Error al leer el archivo m3u8 para el video $videoId");
    }

    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);

    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode($iv)
    ];

    file_put_contents($segmentDir . $videoId . '.json', json_encode($responseData));

    executeSql($conexion, "UPDATE videos SET estado = 'READY' WHERE id_video = ?", 's', $videoId);

    logError("Video procesado exitosamente");
    echo "Video procesado exitosamente\n";
    logError(".........................................................................................");

} catch (Exception $e) {
    logError($e->getMessage());
    die($e->getMessage() . "\n");
} finally {
    $conexion->close();
}
*/
