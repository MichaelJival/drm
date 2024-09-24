<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
// Asegúrate de incluir tu archivo de conexión a la base de datos aquí
//conexion
include('/home/drm/public_html/conexion/conexion.php');
error_log("Received request: " . print_r($_POST, true));
//error_log("Response: " . json_encode($response));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $folderName = $_POST['folderName'];
    
    // Validar el nombre de la carpeta
    if (empty($folderName)) {
        echo json_encode(['success' => false, 'message' => 'Folder name is required']);
        exit;
    }

    // Insertar la nueva carpeta en la base de datos
    $sql = "INSERT INTO folders (name) VALUES (?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $folderName);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conexion->error]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}*/


/*error_reporting(E_ALL);
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
        $folderName = $_POST['folderName'] ?? '';

        if (empty($folderName)) {
            throw new Exception('Folder name is required');
        }


        function generateVideoId() {
        $data = uniqid('', true) . microtime(true) . mt_rand() . bin2hex(random_bytes(10));
        return substr(hash('sha256', $data), 0, 32);
        }
        $folderId = generateVideoId();

        $sql = "INSERT INTO folders (name, id_folder) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conexion->error);
        }

        $stmt->bind_param("ss", $folderName, $folderId);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        echo json_encode(['success' => true, 'folderId' => $folderId]);
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log("Caught exception: " . $e->getMessage());
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}*/


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
        $folderName = $_POST['folderName'] ?? '';

        if (empty($folderName)) {
            throw new Exception('Folder name is required');
        }

        function generateVideoId() {
            $data = uniqid('', true) . microtime(true) . mt_rand() . bin2hex(random_bytes(10));
            return substr(hash('sha256', $data), 0, 32);
        }
        $folderId = generateVideoId();

        $sql = "INSERT INTO folders (name, id_folder) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conexion->error);
        }

        $stmt->bind_param("ss", $folderName, $folderId);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        echo json_encode(['success' => true, 'folderId' => $folderId]);
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    error_log("Caught exception: " . $e->getMessage());
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}



