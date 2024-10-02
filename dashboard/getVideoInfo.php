<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
///////////////////////////////////////////////////////////////////////////
function getVideoInfo($classId) {
    // Conexión a la base de datos
    include("/home/drm/public_html/conexion/conexion.php");
    
    
    $get_url = $conexion->prepare("SELECT url_video FROM videos WHERE id_video = ?");
    $get_url->bind_param("s", $classId);
    $get_url->execute();
    $result = $get_url->get_result();

    if ($result->num_rows === 0) {header('HTTP/1.1 404 Not Found');exit('Video not found');}

    $row = $result->fetch_assoc();
    $url = $row['url_video'];
    $videoSrc = $url;

    
    $encryptionKey = '5aad9b549e86812c95542e0714c1b2b7';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));

    $content = file_get_contents($videoSrc);
    $encryptedContent = openssl_encrypt($content, 'aes-128-cbc', $encryptionKey, 0, $iv);

    $_SESSION['video_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_expiry'] = time() + 300;

    $responseData = [
        'encrypted' => base64_encode($encryptedContent),
        'iv' => base64_encode($iv)
        ];

    echo json_encode($responseData);
}