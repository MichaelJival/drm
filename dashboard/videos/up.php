<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subida de Archivos en Chunks</title>
</head>
<body>
    <h1>Subida de Archivos en Chunks</h1>
    <input type="file" id="fileInput">
    <button onclick="uploadFile()">Subir Archivo</button>
    <div id="progress"></div>

    <script>
    function uploadFile() {
        const file = document.getElementById('fileInput').files[0];
        const chunkSize = 1024 * 1024; // 1 MB
        const totalChunks = Math.ceil(file.size / chunkSize);
        let currentChunk = 0;

        function uploadNextChunk() {
            const start = currentChunk * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('chunk', chunk, file.name);
            formData.append('fileName', file.name);
            formData.append('chunkNumber', currentChunk);
            formData.append('totalChunks', totalChunks);

            fetch('upto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const progress = Math.round(((currentChunk + 1) / totalChunks) * 100);
                document.getElementById('progress').innerText = `Progreso: ${progress}%`;

                if (currentChunk < totalChunks - 1) {
                    currentChunk++;
                    uploadNextChunk();
                } else {
                    document.getElementById('progress').innerText = 'Subida completa';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('progress').innerText = 'Error en la subida';
            });
        }

        uploadNextChunk();
    }
    </script>
</body>
</html>