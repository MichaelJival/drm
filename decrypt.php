<?php
session_start();
//////zona horaria costa rica ////
date_default_timezone_set('America/Costa_Rica');
define("LOG_FILE", "/home/drm/public_html/LOGS.log");
function logMessage($message) {
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "[$timestamp] [VIDEO] [DECRYPT] $message\n";
    error_log($logEntry, 3, LOG_FILE);
}
function decryptVideo() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
     /////////////////////////////////////////////////////////
     /*$cacheKey = hash('crc32c', $data['content']);
     $cachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8";
     
     if (file_exists($cachePath)) {
         $startTime = microtime(true);
         logMessage("Iniciando carga del video: " . $cachePath);
         
         // Headers de tipo y caché
         header('Content-Type: application/vnd.apple.mpegurl');
         header('Content-Encoding: gzip');
         
         // Headers de caché
         $etag = md5_file($cachePath);
         header("ETag: \"$etag\"");
         header('Cache-Control: public, max-age=3600');
         header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
         
         // Lectura y envío optimizado
         $chunkSize = 8192;
         $handle = fopen($cachePath, 'rb');
         $compressor = gzencode(stream_get_contents($handle), 9);
         
         echo $compressor;
         logMessage("Video servido con gzip");
         logMessage("Video servido con gzip - Tamaño: " . filesize($cachePath) . " bytes - Comprimido: " . strlen($compressor) . " bytes");
         fclose($handle);
     
         $endTime = microtime(true);
         $loadTime = round(($endTime - $startTime) * 1000, 2); // Convertir a milisegundos
         logMessage("Video servido en: {$loadTime}ms - Tamaño: " . filesize($cachePath) . " bytes - Comprimido: " . strlen($compressor) . " bytes");
         
         return;
     }*/
     $cacheKey = hash('crc32c', $data['content']);
     $cachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8";
     $gzipCachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8.gz"; 
     $brotliCachePath = "/home/drm/public_html/cache/{$cacheKey}.m3u8.br";
     
     if (file_exists($cachePath)) {
         if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
             $etag = hash_file('crc32c', $cachePath);
             if ($_SERVER['HTTP_IF_NONE_MATCH'] === '"' . $etag . '"') {
                 logMessage("Cliente usando caché del navegador - 304 Not Modified");
                 header('HTTP/1.1 304 Not Modified');
                 exit;
             }
         }
     
         $startTime = microtime(true);
         logMessage("Iniciando carga del video: " . $cachePath);
         
         ob_start();
         
         $useBrotli = isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'br') !== false;
         
         header('Content-Type: application/vnd.apple.mpegurl');
         if($useBrotli) {
             header('Content-Encoding: br');
             $compressedPath = $brotliCachePath;
             logMessage("Usando compresión Brotli");
         } else {
             header('Content-Encoding: gzip');
             $compressedPath = $gzipCachePath;
             logMessage("Usando compresión Gzip");
         }
         
         $etag = hash_file('crc32c', $cachePath);
         header("ETag: \"$etag\"");
         header('Cache-Control: public, max-age=2592000');
         header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');
     
         if($useBrotli && !file_exists($brotliCachePath)) {
             logMessage("Creando nueva versión Brotli");
             $command = "brotli -q 11 \"{$cachePath}\" -o \"{$brotliCachePath}\"";
             $output = shell_exec($command);
             logMessage("Versión Brotli creada: " . $brotliCachePath);
         } elseif(!$useBrotli && !file_exists($gzipCachePath)) {
             logMessage("Creando nueva versión Gzip");
             $content = file_get_contents($cachePath);
             $compressed = gzencode($content, 9);
             file_put_contents($gzipCachePath, $compressed);
             logMessage("Versión Gzip creada: " . $gzipCachePath);
         } else {
             logMessage("Usando versión comprimida existente - {$compressedPath}");
         }
     
         if(function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules())) {
             header('X-Sendfile: ' . $compressedPath);
             logMessage("Usando X-Sendfile para enviar archivo");
         } else {
             $chunkSize = 65536; // Aumentado a 64KB
             readfile($compressedPath);
             logMessage("Archivo enviado mediante readfile()");
         }
         
     
         $endTime = microtime(true);
         $loadTime = round(($endTime - $startTime) * 1000, 2);
         logMessage("Video servido en: {$loadTime}ms - Original: " . filesize($cachePath) . " bytes - Comprimido: " . filesize($compressedPath) . " bytes ");
         //// logMesagge % de reduccion  ///
         $originalSize = filesize($cachePath);
         $compressedSize = filesize($compressedPath);
         $reduction = ($originalSize - $compressedSize) / $originalSize * 100;
         logMessage("Reduccion: {$reduction}%");
         
         return;
     }
     












    ///////////////////////////////////////////////////////
    logMessage("Cache not found, decrypting new content");
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