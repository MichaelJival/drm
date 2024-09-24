<?php
session_start();
include("/home/drm/public_html/conexion/conexion.php");

header('Content-Type: application/json');

function sendJsonResponse($success, $message = '') {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if (!isset($_POST['videoId']) || !isset($_POST['folderId'])) {
    sendJsonResponse(false, 'Missing required data');
}

$videoId = $_POST['videoId'];
$folderId = $_POST['folderId'];

// Validación básica
if (empty($videoId) || empty($folderId)) {
    sendJsonResponse(false, 'Invalid video or folder ID');
}

if ($conexion->connect_error) {
    sendJsonResponse(false, 'Database connection failed: ' . $conexion->connect_error);
}

// Cambia el tipo de dato en la consulta SQL a VARCHAR
$sql = "INSERT INTO archived (id_video, id_folder) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    sendJsonResponse(false, 'Prepare failed: ' . $conexion->error);
}

// Usa 'ss' en lugar de 'ii' para indicar que ambos son strings
$stmt->bind_param('ss', $videoId, $folderId);

if ($stmt->execute()) {
    sendJsonResponse(true, 'Video archived successfully');
} else {
    sendJsonResponse(false, 'Error archiving video: ' . $stmt->error);
}

$stmt->close();
$conexion->close();
?>