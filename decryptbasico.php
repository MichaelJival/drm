<?php
session_start();
define('CACHE_DIR', '/home/drm/public_html/cache');

/**
 * Función principal para desencriptar y servir videos
 */
function decryptVideo() {
    try {
        // Verificar método de petición
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            throw new Exception("Método no permitido");
        }
        
        // Obtener y validar los datos JSON
        $json = file_get_contents('php://input');
        if (!$json) {
            throw new Exception("No se recibieron datos");
        }
        
        $data = json_decode($json, true);
        if (!$data || !isset($data['content']) || !isset($data['iv'])) {
            throw new Exception("Datos incompletos o inválidos");
        }
       
        // Generar clave de caché
        $cacheKey = hash('crc32c', $data['content']);
        $originalFile = CACHE_DIR . "/$cacheKey.m3u8";
        
        // Si existe el archivo en caché, servirlo directamente
        if (file_exists($originalFile)) {
            header('Content-Type: application/vnd.apple.mpegurl');
            header('Cache-Control: max-age=86400');
            readfile($originalFile);
            return;
        }
        
        // Si no existe, desencriptar y guardar
        $originalContent = openssl_decrypt(
            base64_decode($data['content']), 
            'aes-128-cbc', 
            ENCRYPTION_KEY, 
            0, 
            base64_decode($data['iv'])
        );
        
        if ($originalContent === false) {
            throw new Exception("Falló la desencriptación");
        }
        
        // Modificar la URL de la clave en el contenido
        $videoId = null;
        if (preg_match('/processed_videos\/([a-f0-9]+)\//', $originalContent, $matches)) {
            $videoId = $matches[1];
            // Reemplazar la URL de la clave con nuestro proxy
            $originalContent = preg_replace(
                '/(#EXT-X-KEY:METHOD=AES-128,URI=")([^"]+)(",IV=.+)/',
                '$1api.php?action=key&kid=' . $videoId . '$3',
                $originalContent
            );
        }
        
        // Guardar y servir
        file_put_contents($originalFile, $originalContent);
        
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Cache-Control: max-age=86400');
        echo $originalContent;
        
    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// Ejecutar la función
decryptVideo();