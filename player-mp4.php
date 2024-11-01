<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$video = $_GET['video'] ?? 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <meta name="csrf-token*" content="<?php echo $_SESSION['csrf_token']; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>

    .video {
        width: 100% !important;
        height: 100% !important;
        /*background-color: green !important;*/
        margin-top:0px !important;
        position : absolute !important;
    
    }


    
    </style>
</head>
<body>

<video class="video player" id="video" controls>
<source src="https://drm.eweo.com/videos/<?php echo $video; ?>?<?php echo time(); ?>" type="video/mp4">
    Your browser does not support the video tag.
</video>

</body>
</html>