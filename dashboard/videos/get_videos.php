<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// conexion
include("/home/drm/public_html/conexion/conexion.php");

if (!isset($_SESSION['id'])) {
    header("location: ../login.php");
    exit;
}

$sql = "SELECT id_video, nombre_video, url_video, fecha FROM videos ORDER BY fecha DESC";
$result = $conexion->query($sql);

$videos = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }
}

echo json_encode($videos);
?>