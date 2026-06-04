<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración
$baseDir = '/home/drm/public_html/dashboard/processed_videos/';
$encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';

// Obtener el ID del video desde la URL
$id_video = isset($_GET['id']) ? $_GET['id'] : null;

if (empty($id_video)) {
    die('Error: Se requiere un ID de video');
}

// Validar que el ID contiene solo caracteres permitidos (por seguridad)
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id_video)) {
    die('Error: ID de video no válido');
}

// Comprobar si el directorio existe
$directory = $baseDir . $id_video;
if (!file_exists($directory) || !is_dir($directory)) {
    die('Error: Video no encontrado');
}

// Comprobar si existe el archivo playlist
$playlistFile = $directory . '/playlist.m3u8';
if (!file_exists($playlistFile)) {
    die('Error: Playlist no encontrada');
}

// Leer y encriptar el contenido
$content = file_get_contents($playlistFile);
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
$encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);

// Crear respuesta
$responseData = [
    'id' => $id_video,
    'encrypted' => base64_encode($encryptedContent),
    'iv' => base64_encode($iv)
];

// Convertir a JSON
$jsonContent = json_encode($responseData);

// Configurar cabeceras para forzar la descarga
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $id_video . '.json"');
header('Content-Length: ' . strlen($jsonContent));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Enviar el contenido como descarga
echo $jsonContent;
exit;