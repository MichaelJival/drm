<?php

/*session_start();
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
?>*/





session_start();
include("/home/drm/public_html/conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoId = $_POST['videoId'];
    
    error_log("Attempting to delete video with ID: " . $videoId);

    $sql = "SELECT nombre_video FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $videoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $nombre_video = $row['nombre_video'];

    $sql = "DELETE FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $videoId);

    if ($stmt->execute()) {
        $processedDir = "/home/drm/public_html/dashboard/processed_videos/" . $videoId;
        if (is_dir($processedDir)) {
            array_map('unlink', glob("$processedDir/*.*"));
            rmdir($processedDir);
        }

        $originalFile = "/home/drm/public_html/dashboard/uploaded_files/" . $nombre_video . "";
        array_map('unlink', glob($originalFile));

        $portadaFile = "/home/drm/public_html/portadas/" . $videoId . ".jpg";
        if (file_exists($portadaFile)) {
            unlink($portadaFile);
        }

        error_log("Video, associated files and cover image deleted successfully");
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
