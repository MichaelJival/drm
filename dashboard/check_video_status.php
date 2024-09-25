<?php
// conexión
include("/home/drm/public_html/conexion/conexion.php");

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['videoId'])) {
    $videoId = $_GET['videoId'];

    $sql = "SELECT status FROM videos WHERE id_video = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $videoId);

    if ($stmt->execute()) {
        $stmt->bind_result($status);
        if ($stmt->fetch()) {
            $response = array('status' => $status);
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Video no encontrado.'
            );
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error al consultar la base de datos: ' . $conexion->error
        );
    }

    $stmt->close();
} else {
    $response = array('status' => 'error', 'message' => 'Solicitud inválida');
}

echo json_encode($response);
?>