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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Estilos adicionales de Font Awesome -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/all.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-duotone-solid.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-thin.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-solid.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-regular.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-light.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .video-container {
            position: relative;
            width: 100% !important;
            height: 100% !important;
            background-color: black;
        }
        .video {
            width: 100% !important;
            height: 100% !important;
            margin-top: 0px !important;
            position: absolute !important;
        }
        .controls {
            position: absolute;
            bottom: 38px !important;
            left: 15%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            font-size: 12px;
            color: white;
            /*background-color: rgba(0, 0, 0, 0.5);*/
            border-radius: 5px;
            padding: 5px;
        }
        .controls button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="video-container">
        <video class="video" id="video" controls></video>
        <div class="controls">
            <button title="Rebobinar 5 segundos" onclick="skipBack(10)"><i class="fa-sharp fa-regular fa-rotate-left"></i></button>
            <button title="Adelantar 5 segundos" onclick="skipAhead(10)"><i class="fa-sharp fa-regular fa-rotate-right"></i></button></button>
        </div>
    </div>

    <script>
        const videoId = "<?php echo $videoId; ?>";
        const videoElement = document.getElementById('video');
        let halfwayNotified = false;

        document.addEventListener('DOMContentLoaded', function() {
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
            .catch(error => console.error('Error al obtener la fuente del video:', error));
        });

        function initializePlayer(videoSrc) {
            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(videoSrc);
                hls.attachMedia(videoElement);
                hls.on(Hls.Events.MANIFEST_PARSED, () => {
                    videoElement.currentTime = 2; // Establece el tiempo inicial en el segundo 2
                    console.log('Video listo para reproducirse');
                });
            } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                videoElement.src = videoSrc;
                videoElement.addEventListener('loadedmetadata', () => {
                    console.log('Video listo para reproducirse');
                });
            } else {
                console.error('HLS no es soportado en este navegador');
            }

            videoElement.addEventListener('timeupdate', function() {
                const currentTime = videoElement.currentTime;
                const duration = videoElement.duration;

                if (!halfwayNotified && currentTime >= duration / 2) {
                    halfwayNotified = true;
                    window.parent.postMessage({
                        type: 'videoHalfway',
                        videoId: videoId
                    }, '*');
                }
            });

            videoElement.addEventListener('ended', function() {
                window.parent.postMessage({
                    type: 'videoEnded',
                    videoId: videoId
                }, '*');
            });
        }

        function skipAhead(seconds) {
            const newTime = videoElement.currentTime + seconds;
            if (newTime <= videoElement.duration) {
                videoElement.currentTime = newTime;
            } else {
                videoElement.currentTime = videoElement.duration;
            }
        }

        function skipBack(seconds) {
            const newTime = videoElement.currentTime - seconds;
            if (newTime >= 0) {
                videoElement.currentTime = newTime;
            } else {
                videoElement.currentTime = 0;
            }
        }
    </script>
</body>
</html>