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
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.3.5"></script>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.5); 
            overflow: hidden;
        }

        .video-container {
            position: relative;
            width: 100% !important;
            height: 100% !important;
            background-color: #000;
        }

        .video {
            width: 100% !important;
            height: 100% !important;
            position: absolute !important;
            object-fit: contain;
        }

        /* Estilos para controles en desktop */
        @media screen and (min-width: 768px) {
            .controls {
                position: absolute;
                bottom: 38px !important;
                left: 18%;
                transform: translateX(-50%);
                display: flex;
                gap: 35px !important;
                font-size: 12px;
                color: white;
                border-radius: 5px;
                padding: 5px;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: 10;
            }
            
            .video-container:hover .controls, 
            .show-controls .controls {
                opacity: 1;
                pointer-events: auto;
            }
            
            .controls button {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                font-size: 18px;
                padding: 5px 10px;
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
                width: 80px;
                height: 80px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;        
                z-index: 5;
                transition: opacity 0.3s ease;
            }
            
            .play-button i {
                color: white;
                font-size: 30px;
                margin-left: 5px;
            }
        }

        /* Ocultar controles personalizados en mobile */
        @media (max-width: 767px) {
            .controls, .play-button {
                display: none;
            }
            
            /* En mobile dejamos los controles nativos visibles */
            video::-webkit-media-controls {
                display: flex !important;
            }
        }

        /* Ajustes específicos para Safari */
        @supports (-webkit-touch-callout: none) {
            .video {
                position: relative !important;
            }
            
            /* Asegurarnos de que los controles nativos sean visibles en Safari mobile */
            @media (max-width: 767px) {
                video::-webkit-media-controls {
                    display: flex !important;
                }
            }
        }
    </style>
</head>
<body>
    <div class="video-container">
        <video 
            class="video" 
            id="video" 
            controls 
            autoplay 
            playsinline 
            webkit-playsinline
            allowfullscreen>
        </video>
        
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
        
        // Detectar si es Safari
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        
        // Deshabilitar Picture-in-Picture si es posible
        if ('disablePictureInPicture' in HTMLVideoElement.prototype) {
            videoElement.disablePictureInPicture = true;
        }

        videoElement.addEventListener('click', togglePlayPause);
        playButton.addEventListener('click', togglePlayPause);

        function togglePlayPause() {
            if (videoElement.paused) {
                // Para Safari, a veces necesita un enfoque explícito
                videoElement.focus();
                
                // Usamos Promise con catch para manejar errores en Safari
                const playPromise = videoElement.play();
                
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.error('Error al reproducir:', error);
                        // Intentar reproducir automáticamente después de interacción del usuario
                        document.addEventListener('click', function playOnce() {
                            videoElement.play();
                            document.removeEventListener('click', playOnce);
                        }, { once: true });
                    });
                }
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
            .catch(error => {
                console.error('Error al obtener la fuente del video:', error);
                // Mostrar mensaje de error en la interfaz
                document.querySelector('.video-container').innerHTML += 
                    '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:white;background:rgba(0,0,0,0.7);padding:20px;border-radius:10px;text-align:center;">' +
                    'Error al cargar el video.<br>Por favor recargue la página.</div>';
            });
        });

        function initializePlayer(videoSrc) {
            // Estrategia específica para Safari
            if (isSafari && videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                console.log('Usando reproductor nativo de Safari para HLS');
                videoElement.src = videoSrc;
                
                videoElement.addEventListener('loadedmetadata', () => {
                    videoElement.currentTime = 2;
                    updatePlayButton();
                    console.log('Video listo para reproducirse en Safari');
                });
                
                // Safari puede necesitar este enfoque para autoplay
                videoElement.addEventListener('canplay', function canPlayHandler() {
                    const playPromise = videoElement.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(() => {
                            // Si falla autoplay, mostrar el botón de play más visible
                            playButton.style.opacity = 1;
                        });
                    }
                    videoElement.removeEventListener('canplay', canPlayHandler);
                });
            } 
            // Para otros navegadores usamos HLS.js
            else if (Hls.isSupported()) {
                const hls = new Hls({
                    // Configuraciones adicionales para mejor rendimiento
                    maxBufferLength: 30,
                    maxMaxBufferLength: 60,
                    enableWorker: true
                });
                
                hls.loadSource(videoSrc);
                hls.attachMedia(videoElement);
                
                hls.on(Hls.Events.MANIFEST_PARSED, () => {
                    videoElement.currentTime = 2;
                    videoElement.play().catch(() => {
                        console.log('Autoplay bloqueado, mostrando botón de play');
                        playButton.style.opacity = 1;
                    });
                    updatePlayButton();
                    console.log('Video listo para reproducirse con HLS.js');
                });
                
                // Manejar errores de HLS
                hls.on(Hls.Events.ERROR, function(event, data) {
                    if (data.fatal) {
                        console.error('Error fatal de HLS:', data.type, data.details);
                        hls.destroy();
                        
                        // Intentar reproducir con método nativo como respaldo
                        videoElement.src = videoSrc;
                        videoElement.play().catch(e => console.error('Error en reproducción de respaldo:', e));
                    }
                });
            } 
            // Respaldo para navegadores que no soportan ni HLS.js ni HLS nativo
            else {
                console.error('HLS no es soportado en este navegador y no hay reproductor de respaldo');
                document.querySelector('.video-container').innerHTML += 
                    '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:white;background:rgba(0,0,0,0.7);padding:20px;border-radius:10px;text-align:center;">' +
                    'Su navegador no soporta la reproducción de este video.<br>Intente con Chrome o Firefox.</div>';
                return;
            }

            // Eventos comunes para todos los navegadores
            videoElement.addEventListener('timeupdate', function() {
                const currentTime = videoElement.currentTime;
                const duration = videoElement.duration;

                if (!isNaN(duration) && !halfwayNotified && currentTime >= duration / 2) {
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
            
            // Manejar eventos de error general del video
            videoElement.addEventListener('error', function(e) {
                console.error('Error en el elemento de video:', videoElement.error);
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