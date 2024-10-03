<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baseDir = '/home/drm/public_html/dashboard/processed_videos/';
$encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

$id_video = $_GET['id'];
$directory = $baseDir . $id_video;
$playlistFile = $directory . '/playlist.m3u8';

if (!file_exists($playlistFile)) {
    echo json_encode(['error' => 'Archivo playlist.m3u8 no encontrado']);
    exit;
}

$content = file_get_contents($playlistFile);
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
$encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);

$responseData = [
    'encrypted' => base64_encode($encryptedContent),
    'iv' => base64_encode($iv)
];

$jsonContent = json_encode($responseData);
file_put_contents($directory . '/' . $id_video . '.json', $jsonContent);

echo json_encode(['success' => true, 'message' => 'Archivo procesado correctamente']);