<?php

// Rango de direcciones IP permitidas
$allowed_ip_ranges = [
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

// Manejar el acceso
if (!$access_allowed) {
    http_response_code(403);
    die("Acceso prohibido. Su dirección IP ({$client_ip}) no está autorizada.");
}

// Si llegamos aquí, el acceso está permitido
//echo "Bienvenido. Su dirección IP ({$client_ip}) tiene acceso autorizado.";