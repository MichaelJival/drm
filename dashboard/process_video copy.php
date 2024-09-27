<?php
// process_video.php
include("/home/drm/public_html/conexion/conexion.php");

// Función para registrar errores
function logError($message) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $message);
}

// Verificar si se proporcionó un ID de video
if ($argc < 2) {
    logError("No se proporcionó un ID de video");
    die("Uso: php process_video.php [id_video]\n");
}

// Obtener el ID del video del argumento de línea de comandos
$videoId = $argv[1];

// Verificar si el ID del video está vacío
if (empty($videoId)) {
    logError("El ID del video está vacío");
    die("Error: ID de video no válido\n");
}

// Obtener información del video
$sql = "SELECT url_video, nombre_video FROM videos WHERE id_video = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $videoId);
$stmt->execute();
$result = $stmt->get_result();
$video = $result->fetch_assoc();
$stmt->close();

if (!$video) {
    logError("No se encontró el video con ID: $videoId");
    die("Video no encontrado\n");
}

$inputFile = $video['url_video'];
$segmentDir = "/home/drm/public_html/dashboard/processed_videos/" . $videoId . "/";
$m3u8File = $segmentDir . 'playlist.m3u8';
$segmentPattern = $segmentDir . 'segment_%03d.ts';
$ffmpegPath = '/usr/bin/ffmpeg';
$keyfile = $segmentDir . 'enc.key';
$keyinfoFile = $segmentDir . 'enc.keyinfo';
$baseUrl = "https://drm.eweo.com/dashboard/processed_videos/" . $videoId . "/";

// Verificar si el archivo de entrada existe
if (!file_exists($inputFile)) {
    logError("El archivo de entrada no existe: $inputFile");
    die("Archivo de video no encontrado\n");
}

// Crear el directorio de segmentos si no existe
if (!is_dir($segmentDir)) {
    if (!mkdir($segmentDir, 0777, true)) {
        logError("No se pudo crear el directorio de segmentos: $segmentDir");
        die("Error al crear el directorio de segmentos\n");
    }
}

// Generar una clave de encriptación aleatoria
$encryptionKey = openssl_random_pseudo_bytes(16);
file_put_contents($keyfile, $encryptionKey);

// Generar un IV aleatorio de 16 bytes
$iv = openssl_random_pseudo_bytes(16);

// Crear el archivo de información de clave
//$keyinfoContent = $keyfile . "\n" . $baseUrl . "enc.key" . "\n" . bin2hex($iv);
$keyinfoContent = $baseUrl . "enc.key\n" . $baseUrl . "enc.key" . "\n" . bin2hex($iv);
file_put_contents($keyinfoFile, $keyinfoContent);

// Comando FFmpeg para segmentar el video
/*$command = sprintf(
    '%s -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s',
    escapeshellcmd($ffmpegPath),
    escapeshellarg($inputFile),
    escapeshellarg($keyinfoFile),
    escapeshellarg($segmentPattern),
    escapeshellarg($baseUrl),
    escapeshellarg($m3u8File)
);*/

//$coverImagePath = $segmentDir . '/portada_' . $videoId . '.jpg';
$baseImagePath = '/home/drm/public_html/portadas/';
$coverImagePath = $baseImagePath . $videoId . '.jpg';

$command = sprintf(
    /*'%s -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s && %s -i %s -vf "select=eq(pict_type\,I),scale=200:-1" -frames:v 1 %s',*/

    /*"%s -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_segment_filename %s -hls_base_url %s %s && %s -i %s -vf \"select='eq(pict_type\,I)*gte(n\,3)',scale=200:-1\" -frames:v 1 %s",*/

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


// Ejecutar el comando FFmpeg
exec($command . " 2>&1", $output, $returnVar);

// Registrar la salida de FFmpeg para depuración
file_put_contents($segmentDir . 'ffmpeg_output.log', implode("\n", $output));

if ($returnVar !== 0) {
    logError("Error procesando el video $videoId. Código de salida: $returnVar");
    logError("Salida de FFmpeg: " . implode("\n", $output));
    die("Error al procesar el video\n");
}

// Verificar si se crearon los archivos
if (!file_exists($m3u8File) || count(glob($segmentDir . "*.ts")) === 0) {
    logError("Los archivos no se crearon correctamente para el video $videoId");
    die("Error: No se generaron los archivos esperados\n");
}

// Actualizar el url del video en la base de datos con el archivo m3u8
$sql = "UPDATE videos SET url_video = ? WHERE id_video = ?";
$stmt = $conexion->prepare($sql);
$processedUrl = $baseUrl . 'playlist.m3u8';
$stmt->bind_param("ss", $processedUrl, $videoId);
if (!$stmt->execute()) {
    logError("Error al actualizar el estado del video $videoId: " . $stmt->error);
    die("Error al actualizar la base de datos\n");
}


// Actualizar el estado del video a READY
$sql = "UPDATE videos SET estado = 'READY', url_processed = ? WHERE id_video = ?";
$stmt = $conexion->prepare($sql);
$processedUrl = $baseUrl . 'playlist.m3u8';
$stmt->bind_param("ss", $processedUrl, $videoId);

if (!$stmt->execute()) {
    logError("Error al actualizar el estado del video $videoId: " . $stmt->error);
    die("Error al actualizar la base de datos\n");
}

$stmt->close();
$conexion->close();

echo "Video procesado exitosamente\n";
?>