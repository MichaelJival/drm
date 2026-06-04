<?php

function serveEncryptionKey($keyId) {
    // Validación básica
    /*if (empty($keyId) || !preg_match('/^[a-f0-9]{32}$/', $keyId)) {
        http_response_code(400);
        exit('Invalid key ID');
    }*/

    if (empty($keyId)) {
        http_response_code(400);
        exit('Invalid key ID');
    }
    
    // Verificar autenticación (ajusta según tu sistema de autenticación)
    // if (!isAuthenticated()) {
    //     http_response_code(403);
    //     exit('Unauthorized');
    // }
    
    // Log de la solicitud
    error_log("Key request for video ID: $keyId from IP: " . $_SERVER['REMOTE_ADDR']);
    
    // Construir la ruta real al archivo de clave
    $keyPath = "dashboard/processed_videos/$keyId/enc.key";
    
    // Verificar que el archivo existe
    if (!file_exists($keyPath)) {
        http_response_code(404);
        exit('Key not found');
    }
    
    // Agregar encabezados para prevenir caché
    header('Content-Type: application/octet-stream');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    
    // Leer y enviar el archivo de clave
    readfile($keyPath);
    exit;
}