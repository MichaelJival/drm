<?php
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
$client_ip = getPublicIP();
// Definir el rango de IPs permitidas
$allowed_ip_start = '186.15.193.0';
$allowed_ip_end = '186.15.193.255';
// Verificar si la IP del cliente está en el rango permitido
if (!isIPInRange($client_ip, $allowed_ip_start, $allowed_ip_end)) {
    header("HTTP/1.1 403 Forbidden");
    echo "<div style='text-align: center; background-color: #000; color: white; padding: 20px; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;font-size: 18px; font-family: sans-serif; '>
    <h1>No tienes Autorizacion <br>para acceder a esta página.</h1></div>";
    exit;
}
// Si llegamos aquí, la IP está permitida
