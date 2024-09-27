<?php
include("/home/drm/public_html/conexion/conexion.php");
//$sql = "SELECT id_video, nombre_video, url_video, fecha FROM videos ORDER BY fecha DESC";
/*$sql = "SELECT v.id_video, v.nombre_video, v.fecha, f.name AS folder_name 
        FROM videos v 
        LEFT JOIN archived a ON v.id_video = a.id_video
        LEFT JOIN folders f ON a.id_folder = f.id_folder
        ORDER BY v.fecha DESC";*/

       $sql = "SELECT v.id_video, v.nombre_video, v.fecha, v.estado, 
        GROUP_CONCAT(DISTINCT f.name SEPARATOR ', ') AS folder_names
        FROM videos v 
        LEFT JOIN archived a ON v.id_video = a.id_video
        LEFT JOIN folders f ON a.id_folder = f.id_folder
        GROUP BY v.id_video, v.nombre_video, v.fecha
        ORDER BY v.fecha DESC";
$result = $conexion->query($sql);
$videos = array();
if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {
$videos[] = $row;
}
}
echo json_encode($videos);
?>