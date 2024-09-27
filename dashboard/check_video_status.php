<?php
// conexión
include("/home/drm/public_html/conexion/conexion.php");

$videoId = $_GET['videoId'];
$sql = "SELECT estado FROM videos WHERE id_video = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $videoId);
$stmt->execute();
$result = $stmt->get_result();
$video = $result->fetch_assoc();
$stmt->close();

echo json_encode(['status' => $video['estado']]);
?>