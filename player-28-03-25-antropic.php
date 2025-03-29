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
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.3.5"></script> <!-- Versión específica de HLS.js -->
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
    /* Mismos estilos que antes */
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

        /* Nuevo: Panel de diagnóstico para errores */
        #errorPanel {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            max-width: 80%;
            z-index: 100;
            display: none;
            font-size: 12px;
        }
    }

    @media (max-width: 767px) {
        .controls, .play-button, .play-button i, .controls button, .seconds {
            display: none;
            visibility: hidden;
        }
    }
    </style>
</head>
<body>
    <div class="video-container">
        <video class="video" id="video" controls autoplay allowfullscreen ></video>
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
        <div id="errorPanel"></div>
    </div>

    <script>
        const videoId = "<?php echo $videoId; ?>";
        const videoElement = document.getElementById('video');
        const playButton = document.getElementById('playButton');
        const errorPanel = document.getElementById('errorPanel');
        let halfwayNotified = false;
        let hlsInstance = null;

        // Función para mostrar errores en el panel
        function showError(message) {
            console.error(message);
            errorPanel.innerHTML = message;
            errorPanel.style.display = 'block';
        }

        if ('disablePictureInPicture' in HTMLVideoElement.prototype) {
            videoElement.disablePictureInPicture = true;
        }

        videoElement.addEventListener('click', togglePlayPause);
        playButton.addEventListener('click', togglePlayPause);

        function togglePlayPause() {
            if (videoElement.paused) {
                videoElement.play().catch(error => {
                    showError(`Error al reproducir: ${error.message}`);
                });
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
        
        // Capturar errores generales del reproductor de video
        videoElement.addEventListener('error', function(e) {
            const error = videoElement.error;
            let errorMsg = "Error desconocido";
            
            if (error) {
                switch (error.code) {
                    case MediaError.MEDIA_ERR_ABORTED:
                        errorMsg = "La reproducción fue abortada por el usuario";
                        break;
                    case MediaError.MEDIA_ERR_NETWORK:
                        errorMsg = "Error de red al cargar el video";
                        break;
                    case MediaError.MEDIA_ERR_DECODE:
                        errorMsg = "Error al decodificar el video";
                        break;
                    case MediaError.MEDIA_ERR_SRC_NOT_SUPPORTED:
                        errorMsg = "Formato de video no soportado";
                        break;
                    default:
                        errorMsg = `Error desconocido: ${error.code}`;
                }
            }
            
            showError(`Error en el reproductor: ${errorMsg}`);
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (!videoId) {
                showError('ID de video no definido');
                return;
            }

            const jsonUrl = `https://drm.eweo.com/dashboard/processed_videos/${videoId}/${videoId}.json`;
            console.log(`Cargando información de video desde: ${jsonUrl}`);

            fetch(jsonUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error al cargar JSON: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(`JSON cargado, desencriptando contenido para video: ${videoId}`);
                
                if (!data.encrypted || !data.iv) {
                    throw new Error('Datos JSON incompletos (faltan encrypted o iv)');
                }
                
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
                    throw new Error(`Error en la respuesta del servidor: ${response.status} ${response.statusText}`);
                }
                return response.text();
            })
            .then(decryptedContent => {
                console.log(`Contenido desencriptado recibido (${decryptedContent.length} bytes)`);
                
                // Verificar si el contenido parece un HLS válido
                if (!decryptedContent.includes('#EXTM3U')) {
                    console.warn('Advertencia: El contenido no parece ser un manifiesto HLS válido');
                    console.log('Primeros 200 caracteres:', decryptedContent.substring(0, 200));
                }
                
                const videoSrc = URL.createObjectURL(new Blob([decryptedContent], { type: 'application/vnd.apple.mpegurl' }));
                console.log(`Blob URL creado: ${videoSrc}`);
                initializePlayer(videoSrc);
            })
            .catch(error => {
                showError(`Error al preparar el video: ${error.message}`);
                console.error('Error detallado:', error);
            });
        });

        function initializePlayer(videoSrc) {
            console.log(`Inicializando reproductor para video ${videoId}`);
            console.log(`Soporte nativo HLS: ${videoElement.canPlayType('application/vnd.apple.mpegurl') ? 'Sí' : 'No'}`);
            console.log(`HLS.js soportado: ${Hls.isSupported() ? 'Sí' : 'No'}`);
            
            try {
                if (Hls.isSupported()) {
                    if (hlsInstance) {
                        hlsInstance.destroy();
                    }
                    
                    // Configuración mejorada de HLS.js
                    hlsInstance = new Hls({
                        debug: true,
                        enableWorker: true,
                        lowLatencyMode: false,
                        backBufferLength: 90,
                        // Más tolerante a errores
                        maxBufferLength: 30,
                        maxMaxBufferLength: 600,
                        maxBufferSize: 60 * 1000 * 1000,
                        maxBufferHole: 0.5
                    });
                    
                    // Registrar eventos para diagnóstico
                    hlsInstance.on(Hls.Events.MEDIA_ATTACHED, () => {
                        console.log('HLS: Media attached');
                    });
                    
                    hlsInstance.on(Hls.Events.MANIFEST_PARSED, (event, data) => {
                        console.log(`HLS: Manifiesto parseado, ${data.levels.length} niveles de calidad`);
                        videoElement.currentTime = 2;
                        updatePlayButton();
                        console.log('Video listo para reproducirse');
                    });
                    
                    hlsInstance.on(Hls.Events.ERROR, (event, data) => {
                        console.warn(`HLS Error: ${data.type} - ${data.details}`);
                        
                        if (data.fatal) {
                            switch(data.type) {
                                case Hls.ErrorTypes.NETWORK_ERROR:
                                    console.error('Error fatal de red, intentando recuperar...');
                                    hlsInstance.startLoad();
                                    break;
                                case Hls.ErrorTypes.MEDIA_ERROR:
                                    console.error('Error fatal de media, intentando recuperar...');
                                    hlsInstance.recoverMediaError();
                                    break;
                                default:
                                    showError(`Error fatal de HLS: ${data.details}`);
                                    break;
                            }
                        }
                    });
                    
                    hlsInstance.loadSource(videoSrc);
                    hlsInstance.attachMedia(videoElement);
                    
                    // Intentar reproducir cuando esté listo
                    videoElement.addEventListener('canplay', function() {
                        videoElement.play().catch(e => {
                            console.warn('No se pudo iniciar la reproducción automáticamente:', e);
                        });
                    });
                } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                    // Reproducción nativa HLS (Safari/iOS)
                    videoElement.src = videoSrc;
                    videoElement.addEventListener('loadedmetadata', () => {
                        updatePlayButton();
                        console.log('Video listo para reproducirse (nativo)');
                    });
                } else {
                    showError('HLS no es soportado en este navegador');
                }
            } catch (error) {
                showError(`Error al inicializar el reproductor: ${error.message}`);
                console.error('Error detallado:', error);
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