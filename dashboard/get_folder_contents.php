<?php
include("/home/drm/public_html/conexion/conexion.php");

$folderId = $_GET['folderId'];

$response = ['folders' => [], 'videos' => []];

// Obtener subfolderes
$sqlFolders = "SELECT id_folder, name FROM folders WHERE parent_id = ?";
$stmtFolders = $conexion->prepare($sqlFolders);
$stmtFolders->bind_param("i", $folderId);
$stmtFolders->execute();
$resultFolders = $stmtFolders->get_result();

while ($folder = $resultFolders->fetch_assoc()) {
    $response['folders'][] = $folder;
}

// Obtener videos del folder
$sqlVideos = "SELECT v.id_video, v.nombre_video, v.fecha 
              FROM videos v 
              JOIN archived a ON v.id_video = a.id_video 
              WHERE a.id_folder = ?";
$stmtVideos = $conexion->prepare($sqlVideos);
$stmtVideos->bind_param("i", $folderId);
$stmtVideos->execute();
$resultVideos = $stmtVideos->get_result();

while ($video = $resultVideos->fetch_assoc()) {
    $response['videos'][] = $video;
}

echo json_encode($response);