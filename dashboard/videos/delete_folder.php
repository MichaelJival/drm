<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'success' => false,
        'message' => "PHP Error: [$errno] $errstr in $errfile on line $errline"
    ];
    echo json_encode($error);
    error_log("PHP Error: [$errno] $errstr in $errfile on line $errline");
    die();
}

set_error_handler("handleError");

try {
    // Include your database connection file here
    include('/home/drm/public_html/conexion/conexion.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $folderId = $_POST['folderId'] ?? '';

        if (empty($folderId)) {
            throw new Exception('Folder ID is required');
        }

        // Primero, verifica si la carpeta existe
        $checkSql = "SELECT id FROM folders WHERE id_folder = ?";
        $checkStmt = $conexion->prepare($checkSql);
        if ($checkStmt === false) {
            throw new Exception("Prepare failed: " . $conexion->error);
        }

        $checkStmt->bind_param("i", $folderId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            throw new Exception("Folder not found");
        }

        $checkStmt->close();

        // Si la carpeta existe, procede a eliminarla
        $deleteSql = "DELETE FROM folders WHERE id_folder = ?";
        $deleteStmt = $conexion->prepare($deleteSql);
        if ($deleteStmt === false) {
            throw new Exception("Prepare failed: " . $conexion->error);
        }

        $deleteStmt->bind_param("s", $folderId);

        if (!$deleteStmt->execute()) {
            throw new Exception("Execute failed: " . $deleteStmt->error);
        }

        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log("Caught exception: " . $e->getMessage());
} finally {
    if (isset($deleteStmt)) {
        $deleteStmt->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}