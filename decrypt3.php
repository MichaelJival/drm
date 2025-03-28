<?php
session_start();
date_default_timezone_set('America/Costa_Rica');

// Definir constantes globales
define('CACHE_DIR', '/home/drm/public_html/cache');
define('ENCRYPTION_KEY', '5aad9b549e86812c95542e0714c1b2b7'); // Asegúrate de usar tu clave real
define('CACHE_EXPIRY', 86400); // 24 horas
define('DEBUG', true); // Cambiar a true para activar el debugging

// Función para logging de debug
function logDebug($message) {
    if (DEBUG) {
        $logFile = '/home/drm/public_html/LOGS.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
    }
}

// Función para limpiar la caché antigua
function cleanupCache($probability = 0.01) {
    if (mt_rand(0, 100) / 100 < $probability) {
        $files = glob(CACHE_DIR . '/*.{gz,br}', GLOB_BRACE);
        $now = time();
        foreach ($files as $file) {
            if ($now - filemtime($file) > CACHE_EXPIRY * 2) {
                unlink($file);
                logDebug("Removed expired cache file: $file");
            }
        }
    }
}

// Función principal de desencriptación
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
        
        // Mejorar la generación de llaves de caché
        $cacheKey = function_exists('hash') ? hash('xxh3', $data['content']) : md5($data['content']);
        $gzipCachePath = CACHE_DIR . "/{$cacheKey}.m3u8.gz"; 
        $brotliCachePath = CACHE_DIR . "/{$cacheKey}.m3u8.br";
        
        logDebug("Cache key: $cacheKey");
        
        // Verificar que el directorio de caché exista
        if (!is_dir(CACHE_DIR)) {
            mkdir(CACHE_DIR, 0755, true);
            logDebug("Created cache directory: " . CACHE_DIR);
        }
        
        // Verificación de caché con control de expiración
        $cacheValid = false;
        $cachePath = '';
        $encoding = '';
        
        if (file_exists($brotliCachePath) && (time() - filemtime($brotliCachePath) < CACHE_EXPIRY)) {
            $cachePath = $brotliCachePath;
            $encoding = 'br';
            $cacheValid = true;
            logDebug("Found valid Brotli cache");
        } elseif (file_exists($gzipCachePath) && (time() - filemtime($gzipCachePath) < CACHE_EXPIRY)) {
            $cachePath = $gzipCachePath;
            $encoding = 'gzip';
            $cacheValid = true;
            logDebug("Found valid Gzip cache");
        }
        
        // Mejor detección de soporte de compresión
        $encodings = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
        $supportsBrotli = strpos($encodings, 'br') !== false;
        $supportsGzip = strpos($encodings, 'gzip') !== false;
        
        logDebug("Client supports: " . ($supportsBrotli ? "Brotli, " : "") . ($supportsGzip ? "Gzip" : ""));
        
        // Si tenemos caché válida, servirla
        if ($cacheValid) {
            header('Content-Type: application/vnd.apple.mpegurl');
            header('Cache-Control: max-age=86400');
            
            if ($supportsBrotli && file_exists($brotliCachePath)) {
                header('Content-Encoding: br');
                $compressedPath = $brotliCachePath;
                logDebug("Serving Brotli cached content");
            } elseif ($supportsGzip && file_exists($gzipCachePath)) {
                header('Content-Encoding: gzip');
                $compressedPath = $gzipCachePath;
                logDebug("Serving Gzip cached content");
                logDebug("....");
            } else {
                // Descomprimir el archivo y enviarlo sin compresión
                $compressedPath = file_exists($gzipCachePath) ? $gzipCachePath : $brotliCachePath;
                if (preg_match('/\.gz$/', $compressedPath)) {
                    echo gzdecode(file_get_contents($compressedPath));
                } else {
                    // Descomprimir brotli requiere función o comando
                    if (function_exists('brotli_uncompress')) {
                        echo brotli_uncompress(file_get_contents($compressedPath));
                    } else {
                        // Fallback usando shell
                        $tempOut = tempnam(sys_get_temp_dir(), 'vid');
                        shell_exec("brotli -d $compressedPath -o $tempOut");
                        readfile($tempOut);
                        unlink($tempOut);
                    }
                }
                logDebug("Serving uncompressed content from cache");
                return;
            }
            
            readfile($compressedPath);
            return;
        }
        
        // Si llegamos aquí, no hay caché válida, desencriptar y crear caché
        logDebug("No valid cache found, decrypting content");
        
        $decryptedContent = openssl_decrypt(
            base64_decode($data['content']), 
            'aes-128-cbc', 
            ENCRYPTION_KEY, 
            0, 
            base64_decode($data['iv'])
        );
        
        if ($decryptedContent === false) {
            throw new Exception("Falló la desencriptación");
        }
        
        logDebug("Content decrypted successfully");
        
        // Crear versión GZIP
        file_put_contents($gzipCachePath, gzencode($decryptedContent, 9));
        logDebug("Gzip cache created: $gzipCachePath");
        
        // Crear versión Brotli
        if (function_exists('brotli_compress')) {
            // Usar la extensión nativa si está disponible
            file_put_contents($brotliCachePath, brotli_compress($decryptedContent, 11));
            logDebug("Brotli cache created with native function");
        } else {
            // Fallback al comando de shell
            $tempFile = CACHE_DIR . "/temp_$cacheKey.txt";
            file_put_contents($tempFile, $decryptedContent);
            shell_exec("brotli -q 11 $tempFile -o '$brotliCachePath'");
            unlink($tempFile);
            logDebug("Brotli cache created with shell command");
        }
        
        // Servir el contenido optimizado
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Cache-Control: max-age=86400');
        
        if ($supportsBrotli) {
            header('Content-Encoding: br');
            readfile($brotliCachePath);
            logDebug("Serving fresh Brotli content");
        } elseif ($supportsGzip) {
            header('Content-Encoding: gzip');
            readfile($gzipCachePath);
            logDebug("Serving fresh Gzip content");
        } else {
            echo $decryptedContent;
            logDebug("Serving fresh uncompressed content");
        }
        
        // Liberar memoria después de operaciones pesadas
        $decryptedContent = null;
        gc_collect_cycles();
        
        // Posibilidad de limpiar caché antigua
        //cleanupCache();
        
    } catch (Exception $e) {
        logDebug("ERROR: " . $e->getMessage());
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// Ejecutar la función principal
decryptVideo();