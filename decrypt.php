<?php
session_start();
/*define("LOG_FILE", "/home/drm/public_html/LOGS.log");
function logMessage($message) {
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "[$timestamp] [VIDEO] [DECRYPT] $message\n";
    error_log($logEntry, 3, LOG_FILE);
}*/
function decryptVideo() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
     /////////////////////////////////////////////////////////
     //$cacheKey = crc32($data['content']);
     $cacheKey = hash('crc32c', $data['content']);
     //logMessage("Cache key: " . $cacheKey);
     
     $cachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8";
     
     if (file_exists($cachePath)) {
         //logMessage("Using cached version: " . $cachePath);
         header('Content-Type: application/vnd.apple.mpegurl');
         readfile($cachePath);
         return;
     }
    ///////////////////////////////////////////////////////
    //logMessage("Cache not found, decrypting new content");
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $decryptedContent = openssl_decrypt(base64_decode($data['content']), 'aes-128-cbc', $encryptionKey, 0, base64_decode($data['iv']));
    if($decryptedContent === false){
        http_response_code(500);
        echo json_encode(["error" => "Decryption failed"]);
        exit;
    }
    // Guardar en cache
    file_put_contents($cachePath, $decryptedContent);
    header('Content-Type: application/vnd.apple.mpegurl');
    echo $decryptedContent;   
}