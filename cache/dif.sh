cd /home/drm/public_html/cache

echo "Verificando archivos..."
for file in *.m3u8; do
    echo -n "$file: "
    missing=""
    
    if [ ! -f "$file.gz" ]; then
        missing="gz "
    fi
    
    if [ ! -f "$file.br" ]; then
        missing+="br"
    fi
    
    if [ -z "$missing" ]; then
        echo "OK (tiene ambas versiones)"
    else
        echo "Falta: $missing"
    fi
done