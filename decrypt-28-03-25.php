<?php
session_start();
date_default_timezone_set('America/Costa_Rica');

function decryptVideo() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $cacheKey = hash('crc32c', $data['content']);
    $gzipCachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8.gz"; 
    
    // Si existe la versión comprimida en gzip, servirla
    if (file_exists($gzipCachePath)) {
        ob_start();
        
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Content-Encoding: gzip');
        
        readfile($gzipCachePath);
        return;
    }
    
    // Si es primera vez, desencriptar y crear la versión comprimida en gzip
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $decryptedContent = openssl_decrypt(base64_decode($data['content']), 'aes-128-cbc', $encryptionKey, 0, base64_decode($data['iv']));
    
    if($decryptedContent === false){
        http_response_code(500);
        echo json_encode(["error" => "Decryption failed"]);
        exit;
    }
    
    // Crear la versión comprimida en gzip
    file_put_contents($gzipCachePath, gzencode($decryptedContent, 9));
    
    // Servir el contenido por primera vez
    header('Content-Type: application/vnd.apple.mpegurl');
    echo $decryptedContent;   
}