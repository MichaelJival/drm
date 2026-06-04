<?php
// Sirve el playlist HLS descifrado como URL real (.m3u8) para compatibilidad
// con HLS nativo de Safari / iOS / macOS. Reemplaza el blob URL del player.
date_default_timezone_set('America/Costa_Rica');

define('ENCRYPTION_KEY', '5aad9b549e86812c95542e0714c1b2b7');
define('VIDEOS_DIR', '/home/drm/public_html/dashboard/processed_videos');

// Validar referer (mismo criterio que api.php, tolerante a vacio para media nativa)
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if ($referer !== '' && !preg_match('#^https://drm\.eweo\.com/#', $referer)) {
    http_response_code(403);
    exit('Forbidden');
}

// Sanitizar id (los folders son hex de 32 chars)
$id = preg_replace('/[^a-f0-9]/', '', strtolower($_GET['id'] ?? ''));
if ($id === '') { http_response_code(400); exit('Bad request'); }

$jsonFile = VIDEOS_DIR . "/$id/$id.json";
if (!is_file($jsonFile)) { http_response_code(404); exit('Not found'); }

$data = json_decode(file_get_contents($jsonFile), true);
if (!$data || !isset($data['encrypted'], $data['iv'])) {
    http_response_code(500); exit('Invalid video data');
}

$m3u8 = openssl_decrypt(
    base64_decode($data['encrypted']),
    'aes-128-cbc',
    ENCRYPTION_KEY,
    0,
    base64_decode($data['iv'])
);

if ($m3u8 === false) { http_response_code(500); exit('Decrypt failed'); }

header('Content-Type: application/vnd.apple.mpegurl');
header('Cache-Control: max-age=86400');
echo $m3u8;
