
<?php

// Obtener la IP del cliente
/*$ip = $_SERVER['REMOTE_ADDR'];

/*echo "IP del cliente: {$ip}";*/


// Tamaño del IV para AES-128 (16 bytes)
/*$iv_length = openssl_cipher_iv_length('AES-256-CBC');

// Generar un IV aleatorio
$iv = openssl_random_pseudo_bytes($iv_length);

// Convertir a formato hexadecimal para su representación
$iv_hex = bin2hex($iv);

echo  $iv_hex;*/


/*$iv = openssl_random_pseudo_bytes(16);
$iv_hex = bin2hex($iv);
echo $iv_hex;*/
/*$iv = openssl_random_pseudo_bytes(16);
$iv = bin2hex($iv);
echo $iv;*/



// URL original
/*$url = "https://drm.dominio.com/dashboard/processed_videos/3a9137b9a7403c613c61b73fca2d59ea/playlist.m3u8";

// Eliminar "playlist.m3u8" y dejar solo el nombre base con la extensión ".jpg"
$nueva_url = preg_replace('/\/playlist\.m3u8$/', '.jpg', $url);

// Mostrar la nueva URL
echo $nueva_url;*/

function getIPUsuario() {
    $ip = '';
    
    // Comprobamos si el usuario está detrás de un proxy utilizando varias cabeceras.
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR']) && !empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Si hay múltiples IPs en 'HTTP_X_FORWARDED_FOR', tomamos la primera (la 'verdadera').
    if (strpos($ip, ',') !== false) {
        $ipArray = explode(',', $ip);
        $ip = trim($ipArray[0]);
    }

    return $ip;
}

$ip = getIPUsuario();
echo "La dirección IP del usuario es: " . $ip;




function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getIPInfo($ip) {
    $url = "https://ipapi.co/{$ip}/json/";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$userIP = getIP();
$ipInfo = getIPInfo($ip);

$isp = $ipInfo['org'] ?? 'No disponible';
$asn = $ipInfo['asn'] ?? 'No disponible';
$org = $ipInfo['org'] ?? 'No disponible';

echo "IP: " . $userIP . "<br>";
echo "ISP: " . $isp . "<br>";
echo "ASN: " . $asn . "<br>";
echo "Organización: " . $org . "<br>";
