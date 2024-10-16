<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$videoId = $_GET['id'] ?? 0;
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

    <video class="video player" id="video" controls ></video>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoId = "<?php echo $videoId; ?>";
    const videoElement = document.getElementById('video');

    if (!videoId) {
        console.error('ID de clase no definido');
        return;
    }

    fetch(`https://drm.eweo.com/dashboard/processed_videos/${videoId}/${videoId}.json`)
    .then(response => response.json())
    .then(data => {
        return fetch('api.php?action=decrypt', {
            method: 'POST',
            body: JSON.stringify({
                content: data.encrypted,
                iv: data.iv
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        });
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al descifrar el contenido');
        }
        return response.text();
    })
    .then(decryptedContent => {
        const videoSrc = URL.createObjectURL(new Blob([decryptedContent], { type: 'application/vnd.apple.mpegurl' }));
        initializePlayer(videoSrc);
    })
    .catch(error => console.error('Error fetching video source:', error));

    function initializePlayer(videoSrc) {
        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(videoSrc);
            hls.attachMedia(videoElement);
            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                videoElement.play().catch(e => console.error('Error al reproducir:', e));
            });
        } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
            videoElement.src = videoSrc;
            videoElement.addEventListener('loadedmetadata', () => {
                videoElement.play().catch(e => console.error('Error al reproducir:', e));
            });
        } else {
            console.error('HLS no es soportado en este navegador');
        }
    }
});
</script>
</body>
</html>