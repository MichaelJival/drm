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
    case 'getVideoInfo':
        require_once 'getVideoInfo.php';
        getVideoInfo($classId);
        break;
    case 'decrypt':
        require_once 'decrypt4.php';
        decryptVideo();
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid action"]);
        break;
}