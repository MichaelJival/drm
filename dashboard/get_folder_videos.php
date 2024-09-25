<?php
session_start();
include("/home/drm/public_html/conexion/conexion.php");
header('Content-Type: application/json');
if (!isset($_GET['folderId'])) {
    echo json_encode(['error' => 'No folder ID provided']);
    exit;
}
$folderId = $_GET['folderId'];


/*$sql = "SELECT v.id_video, v.nombre_video, v.fecha 
        FROM videos v 
        INNER JOIN archived a ON v.id_video = a.id_video 
        WHERE a.id_folder = ?";*/


/*$sql = "SELECT v.id_video, v.nombre_video, v.fecha, f.name AS folder_name 
FROM videos v 
LEFT JOIN archived a ON v.id_video = a.id_video
LEFT JOIN folders f ON a.id_folder = f.id_folder
ORDER BY v.fecha DESC";*/

/*$sql = "SELECT v.id_video, v.nombre_video, v.fecha, 
        GROUP_CONCAT(DISTINCT f.name SEPARATOR ', ') AS folder_names
        FROM videos v 
        LEFT JOIN archived a ON v.id_video = a.id_video 
        LEFT JOIN folders f ON a.id_folder = f.id_folder
        GROUP BY v.id_video, v.nombre_video, v.fecha
        ORDER BY v.fecha DESC";*/

$sql = "SELECT v.id_video, v.nombre_video, v.fecha,
        GROUP_CONCAT(DISTINCT f.name SEPARATOR ', ') AS folder_names
 FROM videos v
 LEFT JOIN archived a ON v.id_video = a.id_video
 LEFT JOIN folders f ON a.id_folder = f.id_folder
 WHERE a.id_folder = ?
 GROUP BY v.id_video, v.nombre_video, v.fecha
 ORDER BY v.fecha DESC";        


$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $folderId);
$stmt->execute();
$result = $stmt->get_result();
$videos = [];
while ($row = $result->fetch_assoc()) {
$videos[] = $row;
}
echo json_encode($videos);
$stmt->close();
$conexion->close();
?>