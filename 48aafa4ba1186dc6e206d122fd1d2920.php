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

switch ($action) {
    case '881d88d07cb03b125274dc3704a60fe5':
        require_once 'getVideoInfo.php';
        getVideoInfo($classId);
        break;
    case '76f0f918e8c9e8f0a07abede72711b5d':
        require_once 'decrypt.php';
        decryptVideo();
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid action"]);
        break;
}