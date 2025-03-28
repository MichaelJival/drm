<?php
session_start();
date_default_timezone_set('America/Costa_Rica');

function decryptVideo() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $cacheKey = hash('crc32c', $data['content']);
    $gzipCachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8.gz"; 
    $brotliCachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8.br";
    
    // Si existen las versiones comprimidas, servirlas
    if (file_exists($brotliCachePath) || file_exists($gzipCachePath)) {
        ob_start();
        $useBrotli = isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'br') !== false;
        
        header('Content-Type: application/vnd.apple.mpegurl');
        if($useBrotli) {
            header('Content-Encoding: br');
            $compressedPath = $brotliCachePath;
        } else {
            header('Content-Encoding: gzip');
            $compressedPath = $gzipCachePath;
        }
        
        readfile($compressedPath);
        return;
    }
    
    // Si es primera vez, desencriptar y crear ambas versiones
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $decryptedContent = openssl_decrypt(base64_decode($data['content']), 'aes-128-cbc', $encryptionKey, 0, base64_decode($data['iv']));
    
    if($decryptedContent === false){
        http_response_code(500);
        echo json_encode(["error" => "Decryption failed"]);
        exit;
    }
    
    // Crear ambas versiones comprimidas
    file_put_contents($gzipCachePath, gzencode($decryptedContent, 9));
    file_put_contents("temp.txt", $decryptedContent);
    shell_exec("brotli -q 11 temp.txt -o '$brotliCachePath'");
    unlink("temp.txt");
    
    // Servir el contenido por primera vez
    header('Content-Type: application/vnd.apple.mpegurl');
    echo $decryptedContent;   
}
