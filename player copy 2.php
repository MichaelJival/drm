<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
//echo '<br><br><pre>' . print_r($_SESSION, TRUE) . '</pre>';
/////////////////////////////////////////////////////////
/*$videoId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($videoId <= 0) {
    die("ID de video inválido");

    echo "<h1>$videoId</h1>";
}*/
$videoId = $_GET['id'] ?? 0;

/*if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    exit('
    <html>
        <head>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: black;
                    color: white;
                    font-family: Arial, sans-serif;
                }
                .message {
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="message">
                <h1>Acceso Denegado</h1>
                <p>No tienes permiso para acceder a esta página.</p>
            </div>
        </body>
    </html>
    ');
}*/
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

<link rel="stylesheet" href="https://cdn.plyr.io/3.6.8/plyr.css" />
<script src="https://cdn.plyr.io/3.6.8/plyr.js"></script>


    <style>
 #video {
    background-color: #000000;
  }
  #videoContainer {
        background-color: #000000;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #loadingMessage {
        color: white;
        font-size: 24px;
        text-align: center;
        font-family: 'Poppins', sans-serif;
    }
    .dots::after {
        content: '';
        animation: dots 1.5s steps(5, end) infinite;
    }
    @keyframes dots {
        0%, 20% { content: ''; }
        40% { content: '.'; }
        60% { content: '..'; }
        80%, 100% { content: '...'; }
    }

    </style>
</head>
<body>
<div id="videoContainer" style="width: 100%; height: 700px; position: relative;">
    <div id="loadingMessage">Cargando video<span class="dots"></span></div>
    <video id="video" controls autoplay style="width: 100%; height: 100%; display: none;"></video>
    
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoId = "<?php echo $videoId; ?>"; // Asegúrate de que este valor sea correcto
    const video = document.getElementById('video');
    //const videoToken = '<?php echo "";/*$_SESSION['video_token'];*/ ?>';
    //const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!videoId) {
        console.error('ID de clase no definido');
        return;
    }

    fetch(`api.php?action=getVideoInfo&id=${videoId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener la información del video');
            }
            return response.json();
        })
        .then(data => {
            return fetch('api.php?action=decrypt', {
                method: 'POST',
                body: JSON.stringify({
                    content: data.a9d51eaf10a6d6d19d51fe4c40bc507b,
                    //token: data.token,
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
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                loadingMessage.style.display = 'none';
                video.style.display = 'block';
                //video.play();
                video.play().catch(e => console.error('Error al reproducir:', e));
            });
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
            video.addEventListener('canplay', () => {
                loadingMessage.style.display = 'none';
                video.style.display = 'block';
                //video.play();
                video.play().catch(e => console.error('Error al reproducir:', e));
            });
        } else {
            console.error('HLS no es soportado en este navegador');
        }
    }
});
</script>
</body>
</html>