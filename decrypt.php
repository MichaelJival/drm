<?php
session_start();
date_default_timezone_set('America/Costa_Rica');

// Definir constantes globales
define('CACHE_DIR', '/home/drm/public_html/cache');
define('ENCRYPTION_KEY', '5aad9b549e86812c95542e0714c1b2b7');
define('CACHE_EXPIRY', 86400); // 24 horas
define('LOG_FILE', '/home/drm/public_html/LOGS.log');
define('ENABLE_LOGGING', true);

// Generar un ID único para esta ejecución del script
$executionId = uniqid();

/**
 * Función para registrar mensajes de log con formato detallado
 */
function logMessage($message, $type = 'INFO') {
    global $executionId;
    
    if (!ENABLE_LOGGING) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [VIDEO] [DECRYPT] [$executionId] $message\n";
    file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);
}

/**
 * Función para prevenir ejecuciones duplicadas en la misma solicitud
 */
function preventDuplicateExecution($cacheKey) {
    static $processedKeys = [];
    
    // Si ya procesamos esta clave en esta ejecución, detener
    if (isset($processedKeys[$cacheKey])) {
        return false; // Ya se procesó esta solicitud
    }
    
    // Marcar esta clave como procesada
    $processedKeys[$cacheKey] = true;
    return true; // Primera vez que se procesa
}

/**
 * Función para medir el tamaño de un contenido y calcular la reducción
 */
function calculateCompression($originalSize, $compressedSize) {
    $reduction = (($originalSize - $compressedSize) / $originalSize) * 100;
    
    return [
        'original' => $originalSize,
        'compressed' => $compressedSize,
        'reduction' => $reduction
    ];
}

/**
 * Función para limpiar la caché antigua
 */
function cleanupCache($probability = 0.01) {
    if (mt_rand(0, 100) / 100 < $probability) {
        $files = glob(CACHE_DIR . '/*.{gz,m3u8}', GLOB_BRACE);
        $now = time();
        $count = 0;
        
        foreach ($files as $file) {
            if ($now - filemtime($file) > CACHE_EXPIRY * 2) {
                unlink($file);
                $count++;
            }
        }
        
        if ($count > 0) {
            logMessage("Se eliminaron $count archivos de caché antiguos");
        }
    }
}

/**
 * Función principal para desencriptar y servir videos
 */
function decryptVideo() {
    $startTime = microtime(true);
    
    try {
        // Verificar método de petición
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            logMessage("Método no permitido: " . $_SERVER['REQUEST_METHOD'], 'ERROR');
            throw new Exception("Método no permitido");
        }
        
        // Obtener y validar los datos JSON
        $json = file_get_contents('php://input');
        if (!$json) {
            logMessage("No se recibieron datos en la petición", 'ERROR');
            throw new Exception("No se recibieron datos");
        }
        
        $data = json_decode($json, true);
        if (!$data || !isset($data['content']) || !isset($data['iv'])) {
            logMessage("Datos JSON incompletos o inválidos", 'ERROR');
            throw new Exception("Datos incompletos o inválidos");
        }

        // Añade este código temporal en api.php o decrypt.php
        $data = json_decode(file_get_contents('php://input'), true);
        file_put_contents("/home/drm/public_html/debug_json.txt", 
        "Video ID: " . $_GET['id'] . "\n" .
        "Content primeros 300 chars: " . substr($data['content'], 0, 300) . "\n" .
        "IV: " . $data['iv'] . "\n\n", 
        FILE_APPEND);
        
        // Generar clave de caché más corta para mejor manejo de archivos
        $cacheKey = hash('crc32c', $data['content']);
        
        
        // Prevenir procesamiento duplicado
        if (!preventDuplicateExecution($cacheKey)) {
            logMessage("Solicitud duplicada detectada para: $cacheKey - omitiendo", 'INFO');
            return;
        }
        
        $originalFile = CACHE_DIR . "/$cacheKey.m3u8";
        $gzipFile = CACHE_DIR . "/$cacheKey.gz"; 
        
        // Crear archivo de bloqueo para evitar procesamiento simultáneo
        $lockFile = CACHE_DIR . "/$cacheKey.lock";
        $lockAcquired = false;
        
        // Obtener bloqueo exclusivo con tiempo de espera
        $lockHandle = fopen($lockFile, 'c+');
        if ($lockHandle) {
            $waitUntil = time() + 5; // Esperar máximo 5 segundos
            while (time() < $waitUntil) {
                if (flock($lockHandle, LOCK_EX | LOCK_NB)) {
                    $lockAcquired = true;
                    break;
                }
                usleep(200000); // Esperar 200ms antes de reintentar
            }
        }
        
        if (!$lockAcquired) {
            logMessage("No se pudo adquirir bloqueo para: $cacheKey - otra instancia está procesando", 'WARN');
            // Continuar sin bloqueo, pero evitará algunas condiciones de carrera
        }
        
        logMessage("Iniciando carga del video: $originalFile");
        
        // Verificar que el directorio de caché exista
        if (!is_dir(CACHE_DIR)) {
            if (!mkdir(CACHE_DIR, 0755, true)) {
                logMessage("Error al crear directorio de caché: " . CACHE_DIR, 'ERROR');
                throw new Exception("No se pudo crear el directorio de caché");
            }
            logMessage("Directorio de caché creado: " . CACHE_DIR);
        }
        
        // Detectar compresión soportada
        $encodings = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
        $supportsGzip = strpos($encodings, 'gzip') !== false;
        
        if ($supportsGzip) {
            logMessage("Usando compresión Gzip");
        } else {
            logMessage("Cliente no soporta compresión");
        }
        
        // Variables para manejo de contenido
        $originalContent = null;
        $contentSize = 0;
        $compressedFile = null;
        $compressedSize = 0;
        $fromCache = false;
        
        // Comprobar si tenemos el archivo original ya desencriptado
        if (file_exists($originalFile)) {
            logMessage("Archivo original encontrado en caché");
            $originalContent = file_get_contents($originalFile);
            $contentSize = strlen($originalContent);
            $fromCache = true;
        } else {
            // Desencriptar contenido
            logMessage("No se encontró caché, desencriptando contenido");
            
            $originalContent = openssl_decrypt(
                base64_decode($data['content']), 
                'aes-128-cbc', 
                ENCRYPTION_KEY, 
                0, 
                base64_decode($data['iv'])
            );
            
            if ($originalContent === false) {
                logMessage("Falló la desencriptación del contenido", 'ERROR');
                throw new Exception("Falló la desencriptación");
            }
            
            $contentSize = strlen($originalContent);
            logMessage("Contenido desencriptado correctamente: $contentSize bytes");
            
            // Guardar el contenido original para referencia
            if (file_put_contents($originalFile, $originalContent) === false) {
                logMessage("Error al guardar archivo original: $originalFile", 'ERROR');
            } else {
                logMessage("Archivo original guardado: $originalFile");
            }
        }
        
        // Verificar/crear compresión si no existe
        $gzipExists = file_exists($gzipFile);
        
        // Crear versión Gzip si no existe
        if (!$gzipExists) {
            $gzipContent = gzencode($originalContent, 9);
            if (file_put_contents($gzipFile, $gzipContent) === false) {
                logMessage("Error al guardar versión Gzip: $gzipFile", 'ERROR');
            } else {
                logMessage("Versión Gzip guardada en caché: $gzipFile");
                $gzipExists = true;
            }
        }
        
        // Determinar qué archivo servir
        if ($supportsGzip && $gzipExists) {
            $compressedFile = $gzipFile;
            header('Content-Encoding: gzip');
        } else {
            $compressedFile = null;
        }
        
        // Configurar headers comunes
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Cache-Control: max-age=86400');
        header('X-Cache-Key: ' . $cacheKey); // Para depuración
        
        // Servir el contenido
        if ($compressedFile !== null && file_exists($compressedFile)) {
            $compressedSize = filesize($compressedFile);
            logMessage("Sirviendo archivo comprimido: $compressedFile ($compressedSize bytes)");
            readfile($compressedFile);
        } else {
            logMessage("Sirviendo contenido sin compresión");
            echo $originalContent;
        }
        
        // Calcular estadísticas
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($compressedFile !== null && file_exists($compressedFile)) {
            $stats = calculateCompression($contentSize, $compressedSize);
            logMessage("Video servido en: {$executionTime}ms - Original: {$stats['original']} bytes - Comprimido: {$stats['compressed']} bytes");
            logMessage("Reduccion: " . number_format($stats['reduction'], 8) . "%");
        } else {
            logMessage("Video servido en: {$executionTime}ms - Sin compresión: $contentSize bytes");
        }
        
        // Liberar bloqueo si lo obtuvimos
        if ($lockAcquired && isset($lockHandle)) {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
            @unlink($lockFile);
        }
        
        // Limpieza de memoria
        $originalContent = null;
        gc_collect_cycles();
        
        // Posible limpieza de caché
        cleanupCache(0.005); // Reducir probabilidad a 0.5%
        
    } catch (Exception $e) {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        logMessage("Error ({$executionTime}ms): " . $e->getMessage(), 'ERROR');
        
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(["error" => $e->getMessage()]);
    }
}



// Ejecutar la función principal
decryptVideo();