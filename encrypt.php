<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$baseDir = '/home/drm/public_html/dashboard/processed_videos/';
$encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
$batchSize = 5;

$directories = glob($baseDir . '*', GLOB_ONLYDIR);
$totalDirectories = count($directories);
$processed = 0;

for ($i = 0; $i < $totalDirectories; $i += $batchSize) {
    $batch = array_slice($directories, $i, $batchSize);
    
    foreach ($batch as $directory) {
        $id_video = basename($directory);
        $playlistFile = $directory . '/playlist.m3u8';
        
        if (file_exists($playlistFile)) {
            $content = file_get_contents($playlistFile);
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
            $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);
            
            $responseData = [
                'encrypted' => base64_encode($encryptedContent),
                'iv' => base64_encode($iv)
            ];
            
            $jsonContent = json_encode($responseData);
            file_put_contents($directory . '/' . $id_video . '.json', $jsonContent);
            
            $processed++;
            echo "Procesados: $processed de $totalDirectories\n";
        }
    }
    
    sleep(2);
}

echo "Proceso completado. Total procesados: $processed\n";