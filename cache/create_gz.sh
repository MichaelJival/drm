#!/bin/bash

cd /home/drm/public_html/cache

for file in *.m3u8; do
    if [ ! -f "$file.br" ]; then
        echo "Creando .br para $file"
        brotli -q 11 -o "$file.br" "$file"
    fi
done

echo "Proceso completado"