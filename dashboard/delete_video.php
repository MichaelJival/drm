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





/*session_start();
include("/home/drm/public_html/conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoId = $_POST['videoId'];
    
    error_log("Attempting to delete video with ID: " . $videoId);

    // Obtener el nombre del video
    $sql = "SELECT nombre_video FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $videoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $nombre_video = $row['nombre_video'];

    // Eliminar de la tabla 'videos'
    $sql = "DELETE FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $videoId);

    if ($stmt->execute()) {
        // Eliminar de la tabla 'archived'
        $sql = "DELETE FROM archived WHERE id_video = ?";
        $stmt_archived = $conexion->prepare($sql);
        $stmt_archived->bind_param("s", $videoId);
        $stmt_archived->execute();
        $stmt_archived->close();

        // Eliminar archivos del sistema
        $processedDir = "/home/drm/public_html/dashboard/processed_videos/" . $videoId;
        if (is_dir($processedDir)) {
            array_map('unlink', glob("$processedDir/*.*"));
            rmdir($processedDir);
        }

        $originalFile = "/home/drm/videos/" . $nombre_video . "";
        array_map('unlink', glob($originalFile));

        $portadaFile = "/home/drm/public_html/portadas/" . $videoId . ".jpg";
        if (file_exists($portadaFile)) {
            unlink($portadaFile);
        }

        error_log("Video, associated files, cover image, and archived entry deleted successfully");
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

    // Obtener el nombre del video
    $sql = "SELECT nombre_video FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $videoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $nombre_video = $row['nombre_video'];

    // Eliminar de la tabla 'videos'
    $sql = "DELETE FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $videoId);

    if ($stmt->execute()) {
        // Eliminar de la tabla 'archived'
        $sql = "DELETE FROM archived WHERE id_video = ?";
        $stmt_archived = $conexion->prepare($sql);
        $stmt_archived->bind_param("s", $videoId);
        $stmt_archived->execute();
        $stmt_archived->close();

        // Eliminar archivos del sistema usando 'rm -rf'
        $processedDir = escapeshellarg("/home/drm/public_html/dashboard/processed_videos/" . $videoId);
        $originalFile = escapeshellarg("/home/drm/videos/" . $nombre_video);
        $portadaFile = escapeshellarg("/home/drm/public_html/portadas/" . $videoId . ".jpg");

        // Usar 'rm -rf' para eliminar los archivos y carpetas
        shell_exec("rm -rf $processedDir");
        shell_exec("rm -f $originalFile");
        shell_exec("rm -f $portadaFile");

        error_log("Video, associated files, cover image, and archived entry deleted successfully");
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
