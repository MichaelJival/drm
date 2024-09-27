<?php
session_start();

/*if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    exit('Acceso denenado');
}*/

//function respondWithError($statusCode, $message) {
//    header("HTTP/1.0 $statusCode");
//    exit(json_encode(['error' => $message]));
//}

//$allowed_origins = ["https://drm.eweo.com", "https://campustribu.com"];
//if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
//    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
//}
//header("Access-Control-Allow-Methods: POST, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-Token");

//if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//    exit(0);
///}

/*if (!isset($_SESSION['csrf_token']) || 
    !isset($_SERVER['HTTP_X_CSRF_TOKEN']) ||
    $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    respondWithError(403, 'Invalid CSRF token');
}*/

/*$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax && !isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false) {
    respondWithError(403, 'Invalid AJAX request');
}*/



function decryptVideo() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    /*if (!isset($data['content']) || !isset($data['token']) || !isset($data['iv'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required data"]);
        exit;
    }*/

    /*if ($data['token'] !== $_SESSION['video_token'] || time() > $_SESSION['token_expiry']) {
        http_response_code(403);
        echo json_encode(["error" => "Invalid or expired video token"]);
        exit;
    }*/

    $encryptionKey = 'KUPyyqkR12zZdINm5rGecyUT5t8W1QxbhHXHJtlDz0c=';
    $decryptedContent = openssl_decrypt(base64_decode($data['content']), 'aes-256-cbc', $encryptionKey, 0, base64_decode($data['iv']));

    if ($decryptedContent === false) {
        http_response_code(500);
        echo json_encode(["error" => "Decryption failed"]);
        exit;
    }

    header('Content-Type: application/vnd.apple.mpegurl');
    echo $decryptedContent;
}