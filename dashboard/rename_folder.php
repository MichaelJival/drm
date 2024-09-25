<?php
include("/home/drm/public_html/conexion/conexion.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderId = $_POST['folderId'] ?? '';
    $newName = $_POST['newName'] ?? '';

    if (!$folderId || !$newName) {
        echo json_encode(['success' => false, 'message' => 'Missing folder ID or new name']);
        exit;
    }

    $sql = "UPDATE folders SET name = ? WHERE id_folder = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $newName, $folderId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating folder name']);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}