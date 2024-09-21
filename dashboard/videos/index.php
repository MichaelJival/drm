<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../login.php");
    exit;
}

$h = time();

?>    

<!DOCTYPE html>
<html lang="es">
<head>
    <title>DASHBOARD</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/all.css">
    <link rel="stylesheet" type="text/css" href="https://drm.eweo.com/dashboard/css/styles.css?v=<?php echo $h ?>">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background-color: #F7F9F9;
        }

        .sidebar {
            padding-top: 100px;
            width: 250px;
            background-color: #FFF;
            color: #000;
            border-right: 1px solid #E5E3F3;
        }

        .sidebar a {
            padding: 10px 20px;
            text-decoration: none;
            color: #000;
            display: flex;
            align-items: center;
            text-align: left;
            margin-left: 0;
        }

        .sidebar a:hover {
            background-color: #E5E3F3;
        }

        .sidebar i {
            font-size: 1.5em;
            margin-right: 10px;
        }
        
        .content {
            padding: 20px;
            flex: 1;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background-color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            height: 80px;
        }

        .user-menu {
            position: relative;
        }

        .user-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #5f50e4;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 4px;
            padding: 10px 0;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: #f8f9fa;
        }

        .folders {
            display: flex;
            gap: 15px;
            padding: 20px;
        }

        .folder {
            background-color: #FFF;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .videos {
            padding: 20px;
            background-color: #FFF;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-left: 20px;
            margin-right: 100px;
        }

        .video-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #E5E3F3;
        }

        .video-thumb {
            display: flex;
            align-items: center;
        }

        .video-thumb img {
            width: 60px;
            margin-right: 20px;
        }
        

        .uploading {
        max-width: 600px;    
        background-color:#FFFFFF;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 20px;
        }

        .upload-percentage {
    font-size: 0.9em;
    color: #6c757d;
}






        .menu-upload {

        margin-left: 20px;
        margin-top: 10px;
        display: flex;
        gap: 10px; /* Espacio entre los botones */
        align-items: center; /* Alinea verticalmente los elementos */
        justify-content: flex-start; /* Alinea horizontalmente los botones al inicio */
        }

        .button-upload {
            border: 1px solid #42389F;
            color: #FFFFFF;
            background-color: #5F50E4;
            border-radius: 3px;
            width: 140px;
            height: 45px;
            padding-right: 12px;
        }

        .button-upload:hover {
            transform: scale(0.96);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        }

        .button-no-fill {
            border: 1px solid #5F50E4;
            color: rgb(95, 80, 228);
            background-color: #F9F9F9;
            border-radius: 3px;
            width: 140px;
            height: 45px;
        }

        .button-no-fill:hover {
            transform: scale(0.96);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        }

        .svg-icon-upload {
            margin-right: 0px;
        }

        .btn-video-list {
            border: 1px solid #5F50E4;
            color: #5F50E4;
            background-color: #FFFFFF;
            border-radius: 3px;
            width: 130px;
            height: 35px;
        }

        .btn-video-list:hover {
            transform: scale(0.96);
        }

        .badge-ready {
            font-size: 14px;
            color: rgb(46, 125, 50);
            text-transform: capitalize;
            font-weight: 600;
            background: #5F50E4;
            padding: 4px 6px;
            border-radius: 4px;
            margin-right: 10px;
            padding: 10px 15px 10px 15px;
        }

        .progress-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 4px;
            margin-top: 5px;
        }

        .progress-bar {
            width: 0%;
            height: 10px;
            background-color: #76c7c0;
            border-radius: 4px;
        }

        .processing-badge {
            font-size: 14px;
            color: rgb(255, 193, 7);
            background: rgb(255, 249, 230);
        }


.upload-item {
    margin-bottom: 10px;
}

.file-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.upload-status {
    font-size: 0.9em;
    color: #6c757d;
    margin-bottom: 5px;
}

.progress-bar {
    width: 100%;
    background-color: #F5F5F5;
    border-radius: 4px;
    overflow: hidden;
}

.progress {
    width: 0;
    height: 20px;
    background-color: #5F50E4;
    transition: width 0.3s ease;
}
    </style>
</head>
<body>
<body>
    <div class="sidebar">
    <a href="#"><i class="fa-regular fa-circle-play"></i><span>Videos</span></a>
        <a href="usuarios.php"><i class="fa-regular fa-shield-check"></i><span>Security</span></a>
        <a href="drm.php"><i class="fa-light fa-database"></i><span>Storage</span></a>
        <a href="drm.php"><i class="fa-light fa-display-chart-up"></i><span>Analytics</span></a>
        <a href="drm.php"><i class="fa-regular fa-clapperboard-play"></i><span>Custom Player</span></a>
        <a href="#"><i class="fa-regular fa-gear"></i><span>Configurations</span></a>
    </div>
    </div>

    <div class="main-content">
       <div class="top-bar">
            <div class="top-bar-left">
                <h5>...</h5>
            </div>

            <div class="user-menu">
                <div class="user-icon" onclick="toggleDropdown()">
                    <i class="fas fa-user"></i>
                </div>
                <div class="dropdown-menu" id="userDropdown">
                    <a href="#"><i class="fas fa-user-circle"></i> Perfil</a>
                    <a href="#"><i class="fas fa-cog"></i> Configuración</a>
                    <a href="../campus/cerrar.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="menu-upload mb-5">
                <button class="button-upload d-flex align-items-center justify-content-center" style="gap: 0.5rem;" onclick="document.getElementById('file-input').click();">
                    <svg class="svg-icon-upload" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill: currentColor; width: 1.8em; height: 1.8em;">
                        <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"></path>
                    </svg>
                    Upload
                </button>
                <button class="button-no-fill">Import</button>
                <button class="button-no-fill">Create Folder</button>
                <input type="file" id="file-input" style="display: none;" onchange="uploadVideo(event)">
            </div>



    <div id="uploading" class="uploading" style="display: none;">
    <h4>Uploading</h4>
    <div id="upload-progress">
        <div class="upload-item">
            <p class="file-name"></p>
            <div class="upload-info">
                <span class="upload-status"></span>
                <span class="upload-percentage"></span>
            </div>
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
        </div>
    </div>
</div>


            <div class="videos mt-3">
                <h4>Videos</h4>
                <div class="video-item" id="video-item-template" style="display: none;">
                    <div class="video-thumb">
                        <img src="https://via.placeholder.com/60" alt="thumbnail">
                        <div>
                            <p class="file-name"></p>
                            <small class="upload-date"></small>
                            <div class="progress-container">
                                <div class="progress-bar"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="badge processing-badge">Processing</span>
                        <button class="btn-video-list mx-1">Configuration</button>
                    </div>
                </div>
                <!-- Video items will be appended here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById("userDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        window.onclick = function(event) {
            if (!event.target.matches('.user-icon')) {
                const dropdown = document.getElementById("userDropdown");
                if (dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
            }
        }

        function uploadVideo(event) {
    const file = event.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('file', file);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);

        // Mostrar la sección de uploading
        document.getElementById('uploading').style.display = 'block';
        
        // Crear elemento de progreso
        const uploadItem = document.querySelector('#upload-progress .upload-item');
        uploadItem.querySelector('.file-name').textContent = file.name;
        
        xhr.upload.onprogress = function(e) {
    if (e.lengthComputable) {
        const percentComplete = (e.loaded / e.total) * 100;
        const uploadItem = document.querySelector('#upload-progress .upload-item');
        uploadItem.querySelector('.progress').style.width = percentComplete + '%';
        uploadItem.querySelector('.upload-status').textContent = 
            `Uploaded: ${formatBytes(e.loaded)} / ${formatBytes(e.total)}`;
        uploadItem.querySelector('.upload-percentage').textContent = 
            `${Math.round(percentComplete)}% / 100%`;
    }
};

        xhr.onerror = function() {
            console.error("Error en la solicitud XHR");
            hideUploadingSection();
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        displayVideoItem(response);
                        hideUploadingSection();
                    } else {
                        console.error("Error en la respuesta del servidor:", response.message);
                        hideUploadingSection();
                    }
                } catch (e) {
                    console.error("Error al parsear la respuesta JSON:", e);
                    hideUploadingSection();
                }
            } else {
                console.error("Error en la respuesta del servidor. Estado:", xhr.status);
                hideUploadingSection();
            }
        };

        xhr.send(formData);
    }
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function hideUploadingSection() {
    document.getElementById('uploading').style.display = 'none';
    // Limpiar el contenido del elemento de progreso
    const uploadItem = document.querySelector('#upload-progress .upload-item');
    uploadItem.querySelector('.file-name').textContent = '';
    uploadItem.querySelector('.progress').style.width = '0%';
    uploadItem.querySelector('.upload-status').textContent = '';
}

        function updateProgressBar(fileName, progress) {
            const videoItems = document.querySelectorAll('.video-item');
            videoItems.forEach(item => {
                const name = item.querySelector('.file-name').textContent;
                if (name === fileName) {
                    const progressBar = item.querySelector('.progress-bar');
                    progressBar.style.width = progress + '%';
                    if (progress === 100) {
                        const badge = item.querySelector('.badge');
                        badge.textContent = 'Processing';
                        badge.classList.remove('uploading-badge');
                        badge.classList.add('processing-badge');
                    }
                }
            });
        }

        function displayVideoItem(file) {
    const template = document.getElementById('video-item-template');
    const clone = template.cloneNode(true);
    clone.removeAttribute('id');
    clone.style.display = 'flex';
    clone.querySelector('.file-name').textContent = file.fileName;
    clone.querySelector('.upload-date').textContent = file.uploadDate;

    const badge = clone.querySelector('.badge');
    badge.textContent = 'Ready';
    badge.classList.add('badge-ready');

    document.querySelector('.videos').insertBefore(clone, document.querySelector('.videos').firstChild);
}

        // Cargar videos existentes al cargar la página
        window.onload = function() {
            fetch('get_videos.php')
                .then(response => response.json())
                .then(videos => {
                    videos.forEach(video => {
                        displayVideoItem({
                            fileName: video.nombre_video,
                            uploadDate: video.fecha_subida,
                            status: 'success'
                        });
                    });
                })
                .catch(error => console.error('Error:', error));
        };
    </script>
</body>
</html>