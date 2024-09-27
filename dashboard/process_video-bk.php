<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// conexión
include("/home/drm/public_html/conexion/conexion.php");

// Entradas de argumentos
$videoId = $argv[1];
$fileName = $argv[2];
$filePath = $argv[3];
$idDB = $argv[4];

// FFMpeg configuration
$ffmpegPath = '/usr/bin/ffmpeg';
$playlistDir = "/home/campustribu/cipher/" . $fileName . "/";
$segmentDir = "/home/campustribu/public_html/admin/drm/" . $fileName . "/";
$keyfile = '/home/campustribu/cipher/';
$baseUrl = "https://campustribu.com/admin/drm/{$fileName}/";

$originalName = preg_replace("/[^a-zA-Z0-9]/", "", pathinfo($fileName, PATHINFO_FILENAME));
$m3u8File = $playlistDir . $originalName . '.m3u8';
$segmentPattern = $segmentDir . $originalName . '_%03d.ts';

// Ensure directories exist
if (!is_dir($playlistDir)) {
    mkdir($playlistDir, 0755, true);
}
chmod($playlistDir, 0755);

if (!is_dir($segmentDir)) {
    mkdir($segmentDir, 0755, true);
}
chmod($segmentDir, 0755);

// FFmpeg command to segment the video
$command = sprintf(
    '%s -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_base_url %s -hls_segment_filename %s %s 2>&1',
    escapeshellcmd($ffmpegPath),
    escapeshellarg($filePath),
    escapeshellarg($keyfile . 'key.txt'),
    escapeshellarg($baseUrl),
    escapeshellarg($segmentPattern),
    escapeshellarg($m3u8File)
);

exec($command, $output, $returnVar);

if ($returnVar !== 0) {
    $errorDetails = implode("\n", $output);
    error_log("FFmpeg command failed: $errorDetails");
    $conexion->query("UPDATE videos SET status='ERROR' WHERE id_video='$videoId'");
    exit;
}

$videoUrl = $m3u8File;
// Update the database entry to mark it as processed
$stmt = $conexion->prepare("UPDATE videos SET url_video=?, status='READY' WHERE id_video=?");
$stmt->bind_param('ss', $videoUrl, $videoId);

if ($stmt->execute()) {
    echo "Video processed successfully.";
} else {
    echo "Failed to update the database: " . $stmt->error;
}

$stmt->close();
?>*/



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
    $outputDir = 'processed_videos/' . $videoId . '/ ';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    $m3u8File = $outputDir . 'playlist.m3u8';
    $segmentPattern = $outputDir . 'segment_%03d.ts';

    // Comando FFmpeg
    //$ffmpegPath = '/usr/bin/ffmpeg'; // Ajusta esta ruta según tu sistema
    //$command = "$ffmpegPath -i $inputFile -c:v libx264 -c:a aac -hls_time 10 -hls_list_size 0 -f hls $m3u8File";
    /*$command = "$ffmpegPath -i $inputFile -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file /home/drm/public_html/key/key.txt -hls_playlist_type vod -hls_list_size 0 -f hls $m3u8File";*/
    //$command = "$ffmpegPath -i $inputFile -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file /home/drm/public_html/key/key.txt -hls_playlist_type vod -hls_list_size 0 -f hls $m3u8File";*/
 
    

    
    $ffmpegPath = '/usr/bin/ffmpeg';
    $PathLists = "/home/drm/cipher/" . $videoId . "/";
    $keyfile = '/home/drm/cipher/';
    $segmentDir = "/home/drm/public_html/dashboard/segments/" . $videoId . "/";
    $baseUrl = "https://drm.eweo.com/dashboard/segments/";


    // FFmpeg command to segment the video
    $command = sprintf(
    '%s -i %s -c:v libx264 -c:a aac -hls_time 10 -hls_key_info_file %s -hls_playlist_type vod -hls_base_url %s -hls_segment_filename %s %s 2>&1',
    escapeshellcmd($ffmpegPath),
    escapeshellarg($inputFile),
    escapeshellarg($keyfile . 'key.txt'),
    escapeshellarg($baseUrl),
    escapeshellarg($segmentPattern),
    escapeshellarg($m3u8File)
);


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