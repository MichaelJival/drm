<?php
// Server-side check
if (strpos($_SERVER['HTTP_USER_AGENT'], 'TubeDigger') !== false) {
    die('Acceso denegado: Este sitio web no puede ser accedido mediante TubeDigger.');
}

$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

if ($user_agent === 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.183 Safari/537.36') {
    die('');
} else {
    echo '';
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sitio Web8</title>
    <script src="tubeDiggerDetection.js"></script>
</head>
<body>
    <h1>¡Bienvenido al sitio web!9</h1>
    <script>
if (navigator.userAgent.indexOf('Chrome') !== -1) {
    try {
        // Intentar crear un Web Worker
        const worker = new Worker('data:text/javascript;base64,Y29uc29sZS5sb2coJ3Rlc3QnKTs=');
        worker.terminate();

        // Verificar APIs estándar de Chrome
        const isChromeOriginal =
            typeof window.chrome === 'object' &&
            typeof window.fetch === 'function' &&
            typeof window.Promise === 'function' &&
            typeof window.WebAssembly === 'object';

        // Si alguna API falta, lanzar un error
        if (!isChromeOriginal) {
            throw new Error('Características de Chrome no disponibles');
        }

        // Si todo pasa, es Chrome original
        console.log('Acceso permitido');
    } catch (e) {
        // Si falla el Web Worker o las APIs, asumir que es TubeDigger
        document.body.innerHTML = '<h1>Acceso denegado</h1><p>Este sitio web no puede ser accedido mediante TubeDigger.</p>';
    }
} else {
    // Si no es Chrome, permitir acceso (por ejemplo, Firefox)
    console.log('Acceso permitido');
}
</script>
</body>
</html>