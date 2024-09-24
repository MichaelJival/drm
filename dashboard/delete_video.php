<?php

session_start();
include("/home/drm/public_html/conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoId = $_POST['videoId'];
    
    error_log("Attempting to delete video with ID: " . $videoId);

    // Logic to delete the video from the database
    $sql = "DELETE FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $videoId);

    if ($stmt->execute()) {
        error_log("Video deleted successfully");
        echo json_encode(['success' => true]);
    } else {
        error_log("Error deleting video: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error deleting video: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    error_log("Invalid request method");
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>