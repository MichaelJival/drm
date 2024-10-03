<?php
/*include("/home/drm/public_html/conexion/conexion.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoId = $_POST['videoId'];
    $newFileName = $_POST['newFileName'];

    if ($videoId && $newFileName) {
        $sql = "UPDATE videos SET nombre_video = ? WHERE id_video = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $newFileName, $videoId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unable to update video name.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}

$conexion->close();
?>*/


include("/home/drm/public_html/conexion/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoId = $_POST['videoId'];
    $newFileName = $_POST['newFileName'];

    if ($videoId && $newFileName) {
        $sql = "SELECT nombre_video FROM videos WHERE id_video = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $videoId);
        $stmt->execute();
        $stmt->bind_result($currentFileName);
        $stmt->fetch();
        $stmt->close();

        if ($currentFileName) {
            $oldFilePath = "/home/drm/videos/$currentFileName";
            $newFilePath = "/home/drm/videos/$newFileName";

            // Cambiamos primero el nombre en el sistema de archivos
            if (file_exists($oldFilePath)) {
                if (rename($oldFilePath, $newFilePath)) {
                    // Después actualizamos el nombre en la base de datos
                    $sql = "UPDATE videos SET nombre_video = ? WHERE id_video = ?";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param("ss", $newFileName, $videoId);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true]);
                    } else {
                        // Si ocurre un error al actualizar la base de datos, revertimos el cambio en el sistema de archivos
                        rename($newFilePath, $oldFilePath);
                        echo json_encode(['success' => false, 'message' => 'Unable to update video name in database.']);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Unable to rename file.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Original file does not exist.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Current file name retrieval failed.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
}

$conexion->close();
