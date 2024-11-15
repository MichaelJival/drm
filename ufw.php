<?php

// Rango de direcciones IP permitidas
/*$allowed_ip_ranges = [
    ['186.15.0.0', '186.15.255.255'],
    // Puedes agregar más rangos aquí
];

// Obtener la IP del cliente
$client_ip = filter_var($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

if (!$client_ip) {
    die("No se pudo determinar la dirección IP del cliente.");
}

// Función para verificar si una IP está dentro de un rango
function ip_in_range($ip, $range) {
    $min = ip2long($range[0]);
    $max = ip2long($range[1]);
    $ip = ip2long($ip);
    return ($ip >= $min && $ip <= $max);
}

// Verificar si la IP del cliente está dentro de alguno de los rangos permitidos
$access_allowed = array_reduce($allowed_ip_ranges, function($carry, $range) use ($client_ip) {
    return $carry || ip_in_range($client_ip, $range);
}, false);

if (!$access_allowed) {
    http_response_code(403);
    echo '
    <html>
        <head>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    background-color: black;
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: white;
                    font-family: Arial, sans-serif;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div>
                <h1>Acceso prohibido</h1>
                <p>No tienes <b>autorización</b> para ver esta pagina.</p>
            </div>
        </body>
    </html>';
    die();
}

// Si llegamos aquí, el acceso está permitido
//echo "Bienvenido. Su dirección IP ({$client_ip}) tiene acceso autorizado.";
*/

// Función para obtener la IP pública del cliente
function getPublicIP() {
    $headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            return $_SERVER[$header];
        }
    }
    return '';
}
// Función para verificar si una IP está en un rango
function isIPInRange($ip, $start, $end) {
    return (ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end));
}
// Obtener la IP del cliente
$client_ip = $_SERVER['REMOTE_ADDR'];
// Definir el rango de IPs permitidas
$allowed_ip_start = '172.69.167.0';
$allowed_ip_end = '172.69.167.255';
// Verificar si la IP del cliente está en el rango permitido
if (!isIPInRange($client_ip, $allowed_ip_start, $allowed_ip_end)) {
    header("HTTP/1.1 403 Forbidden");
    echo "<div style='text-align: center; background-color: #000; color: white; padding: 20px; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;font-size: 18px; font-family: sans-serif; '>
    <h1>No tienes Autorizacion <br>para acceder a esta página.</h1></div>";
    exit;
}

/*if($client_ip === "2803:6000:e006:164:e483:d2b:1370:38a4") {
    header("HTTP/1.1 403 Forbidden");
    echo "<div style='text-align: center; background-color: #000; color: white; padding: 20px; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;font-size: 18px; font-family: sans-serif; '>
    <h1>No tienes Autorizacion <br>para acceder a esta página.</h1><br> ip: " . $client_ip . "</div>";
    exit;
}*/
// Si llegamos aquí, la IP está permitida



/*$ip = $_SERVER['REMOTE_ADDR'];

/*var_dump($ip); // Para ver 

