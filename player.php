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
</head>
<body>
<video id="video" controls style="width: 100%; height: auto;"></video>
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

    fetch(`48aafa4ba1186dc6e206d122fd1d2920.php?action=881d88d07cb03b125274dc3704a60fe5&id=${videoId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error');
            }
            return response.json();
        })
        .then(data => {
            return fetch('48aafa4ba1186dc6e206d122fd1d2920.php?action=76f0f918e8c9e8f0a07abede72711b5d', {
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
                throw new Error('Error');
            }
            return response.text();
        })
        .then(da72e290f290479b4abc904ebe181438 => {
            const videoSrc = URL.createObjectURL(new Blob([da72e290f290479b4abc904ebe181438], { type: 'application/vnd.apple.mpegurl' }));
        initializePlayer(videoSrc);
    })
    .catch(error => console.error('Error', error));

    function initializePlayer(videoSrc) {
        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(videoSrc);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = videoSrc;
            video.addEventListener('loadedmetadata', () => video.play());
        } else {
            console.error('HLS no es soportado en este navegador');
        }
    }
});
</script>
</body>
</html>