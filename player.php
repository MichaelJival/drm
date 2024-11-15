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
    .video {
            width: 100% !important;
            height: 100% !important;
            position: absolute !important;
        }

     
        @media screen and (min-width: 768px) {
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.5); 
        }

        .video {
            width: 100% !important;
            height: 100% !important;
            position: absolute !important;
        }


        .video-container {
            position: relative;
            width: 100% !important;
            height: 100% !important;
        }
        .video {
            width: 100% !important;
            height: 100% !important;
            position: absolute !important;
        }
        .controls {
            position: absolute;
            bottom: 38px !important;
            left: 18%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            font-size: 12px;
            color: white;
            border-radius: 5px;
            padding: 5px;
            gap: 35px !important;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        .video-container:hover .controls, .show-controls .controls {
            opacity: 1;
            pointer-events: auto;
        }
        .controls button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 18px;
        }
        .seconds {
            font-size: 14px;
        }
        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;        
            z-index: 2;
        }
        .play-button i {
            color: white;
            font-size: 40px;
            margin-left: 5px;
        }

    }

    @media (max-width: 767px) {
    
        .controls,  .play-button, .play-button i, .controls button, .seconds {
            display: none;
            visibility: hidden;
            
        }
    
}
    </style>
</head>
<body>
    <div class="video-container">
        <video class="video" id="video" controls></video>
        <div class="controls">
            <button title="Rebobinar 5 segundos" onclick="skipBack(5)">
                <i class="fa-sharp fa-regular fa-rotate-left"></i>
                <span class="seconds"> 5s</span>
            </button>
            <button title="Adelantar 5 segundos" onclick="skipAhead(5)">
                <i class="fa-sharp fa-regular fa-rotate-right"></i>
                <span class="seconds">5s</span>
            </button>
        </div>
        <div class="play-button" id="playButton">
            <i class="fas fa-play"></i>
        </div>
    </div>

    <script>
        const videoId = "<?php echo $videoId; ?>";
        const videoElement = document.getElementById('video');
        const playButton = document.getElementById('playButton');
        let halfwayNotified = false;

        if ('disablePictureInPicture' in HTMLVideoElement.prototype) {
            videoElement.disablePictureInPicture = true;
        }

        videoElement.addEventListener('click', togglePlayPause);
        playButton.addEventListener('click', togglePlayPause);

        function togglePlayPause() {
            if (videoElement.paused) {
                videoElement.play();
            } else {
                videoElement.pause();
            }
            updatePlayButton();
        }

        function updatePlayButton() {
            if (videoElement.paused) {
                playButton.style.opacity = 1;
            } else {
                playButton.style.opacity = 0;
            }
        }

        videoElement.addEventListener('play', updatePlayButton);
        videoElement.addEventListener('pause', updatePlayButton);

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
                    videoElement.currentTime = 2;
                    updatePlayButton(); // Forzamos la actualización para asegurar que el estado inicial es correcto
                    console.log('Video listo para reproducirse');
                });
            } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                videoElement.src = videoSrc;
                videoElement.addEventListener('loadedmetadata', () => {
                    updatePlayButton();
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