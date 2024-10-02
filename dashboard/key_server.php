<?php
$encrypted_key_path = '/home/drm/public_html/dashboard/processed_videos/ce1a13fb3b2a0258b0ee76ee07719a34/keyinfo.enc';
$decryption_key = 'a57d862f77befcb77e7192e728eb9440a1a724488d840e69bcfe0fe89487d352';

$encrypted_data = file_get_contents($encrypted_key_path);
$iv = substr($encrypted_data, 0, 16);
$encrypted_content = substr($encrypted_data, 16);
$decrypted_key = openssl_decrypt($encrypted_content, 'AES-256-CBC', $decryption_key, OPENSSL_RAW_DATA, $iv);

header('Content-Type: application/octet-stream');
echo $decrypted_key;