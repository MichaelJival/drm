<?php
function encryptFile($filePath, $key, $outputFile) {
    // Leer el contenido del archivo
    $data = file_get_contents($filePath);

    // Generar un IV (16 bytes para AES-256-CBC)
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));

    // Encriptar el archivo
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

    // Guardar el IV y el contenido cifrado en un archivo de salida
    file_put_contents($outputFile, $iv . $encrypted);
    
    echo "Archivo encriptado y guardado en: " . $outputFile . "\n";
}

$inputFile = '/home/drm/public_html/dashboard/processed_videos/ce1a13fb3b2a0258b0ee76ee07719a34/enc.keyinfo';
$outputFile = '/home/drm/public_html/dashboard/processed_videos/ce1a13fb3b2a0258b0ee76ee07719a34/keyinfo.enc';
$key = 'a57d862f77befcb77e7192e728eb9440a1a724488d840e69bcfe0fe89487d352'; // Asegúrate de que la clave tiene exactamente 32 bytes para AES-256

encryptFile($inputFile, $key, $outputFile);
?>
