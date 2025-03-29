<?php
session_start();
date_default_timezone_set('America/Costa_Rica');

// Definir constantes básicas
define('CACHE_DIR', '/home/drm/public_html/cache');
define('ENCRYPTION_KEY', '5aad9b549e86812c95542e0714c1b2b7');
define('LOG_FILE', '/home/drm/public_html/LOGS.log');

/**
 * Función para registrar mensajes de log
 */
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [VIDEO] $message\n";
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
}

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
        
        // Obtener datos JSON
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
        $cacheFile = CACHE_DIR . "/$cacheKey.m3u8";
        
        // Verificar si existe en caché
        if (file_exists($cacheFile)) {
            logMessage("Sirviendo desde caché: $cacheFile");
            header('Content-Type: application/vnd.apple.mpegurl');
            readfile($cacheFile);
            return;
        }
        
        // Crear directorio de caché si no existe
        if (!is_dir(CACHE_DIR)) {
            mkdir(CACHE_DIR, 0755, true);
        }
        
        // Desencriptar contenido
        $content = openssl_decrypt(
            base64_decode($data['content']), 
            'aes-128-cbc', 
            ENCRYPTION_KEY, 
            0, 
            base64_decode($data['iv'])
        );
        
        if ($content === false) {
            throw new Exception("Falló la desencriptación");
        }
        
        // Guardar en caché
        file_put_contents($cacheFile, $content);
        logMessage("Contenido guardado en caché: $cacheFile");
        
        // Servir el contenido
        header('Content-Type: application/vnd.apple.mpegurl');
        echo $content;
        
    } catch (Exception $e) {
        logMessage("Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// Ejecutar la función principal
decryptVideo();