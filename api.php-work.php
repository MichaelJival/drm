<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

session_start();

/*if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}*/

// Simular autorización (deberías implementar tu propio sistema de autenticación)
//$_SESSION['authorized'] = true;

/*if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Access denied"]);
    exit;
}*/

$action = $_GET['action'] ?? '';
$classId = $_GET['id'] ?? '';
$keyId = $_GET['kid'] ?? '';

switch ($action) {
    case 'getVideoInfo':
        require_once 'getVideoInfo.php';
        getVideoInfo($classId);
        break;
    case 'decrypt':
        require_once 'decrypt.php';
        decryptVideo();
        break;
    case 'key':
        require_once 'keyHandler.php';
        serveEncryptionKey($keyId);
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid action"]);
        break;
}


//Y necesitarías modificar la URL en el manifiesto HLS a algo como:
//`#EXT-X-KEY:METHOD=AES-128,URI="api.php?action=key&kid=5a51912dafd5281aa74d5d32dce303d9",IV=0x57bb17202cd782f38a5c039bfd9ac926`