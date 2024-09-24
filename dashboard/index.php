<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['id'])) {
    header("location: ../login.php");
    exit;
}

include("/home/drm/public_html/conexion/conexion.php");


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
    <link rel="stylesheet" type="text/css" href="https://drm.eweo.com/dashboard/css/sidebar.css?v=<?php echo $h ?>">
    <link rel="stylesheet" type="text/css" href="https://drm.eweo.com/dashboard/css/topbar.css?v=<?php echo $h ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background-color: #F7F9F9;
            font-family: Arial, sans-serif;
        }

        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
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


        .video-container {
        margin-left: 18px;
        padding-bottom: 20px;
       
        }


        .titulo-videos {
         padding-top:35px;   
         margin-top: 50px; 
         margin-left: 35px;
         margin-bottom: 20px;  
            
        }


        .videos {
            padding: 0px;
            background-color: #FFF;
            border-radius: 8px;
            /*box-shadow:  2px 4px rgba(0,0,0,0.1);*/
            margin-left: 0px;
            margin-right: 100px;
            padding-bottom: 20px;
            padding-top: 10px;
        }

        .video-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border: 1px solid #E5E3F3;
            margin-bottom: 10px;
            padding: 25px 0;
            padding-right: 20px;
            padding-left: 20px;
            margin-left:30px;
        }

        .video-thumb {
            display: flex;
            align-items: center;
            margin-left: 20px;
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
            margin-bottom: 10px;
            margin-left: 18px;
        }

        .menu-upload {
            margin-left: 20px;
            margin-top: 25px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-start;
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

        .btn-video-list-conf {
            border: 1px solid #5F50E4;
            color: #5F50E4;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .btn-video-list-conf:hover {
            transform: scale(0.96);
        }

        .btn-video-list-archive {
            border: 1px solid #5F50E4;
            color: #5F50E4;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .btn-video-list-archive:hover {
            transform: scale(0.96);
        }




        .btn-video-list-delete {
            border: 1px solid #E4505F;
            color: #E4505F;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .btn-video-list-delete:hover {
            transform: scale(0.96);
        }








        .badge-ready {
            font-size: 14px;
            color: rgb(46, 125, 50);
            text-transform: capitalize;
            font-weight: 600;
            background: #D7FEDD;
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
            overflow: hidden;
        }

        .progress-bar {
            width: 0%;
            height: 15px;
            background-color: #5F50E4;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
            transition: width 0.5s ease;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: linear-gradient(
                -45deg,
                rgba(255, 255, 255, 0.2) 25%,
                transparent 25%,
                transparent 50%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0.2) 75%,
                transparent 75%,
                transparent
            );
            background-size: 50px 50px;
            animation: stripes 1s linear infinite;
            z-index: 1;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: skew(-20deg);
            animation: shine 1.5s linear infinite;
        }

        @keyframes stripes {
            0% { background-position: 0 0; }
            100% { background-position: 50px 0; }
        }

        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
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

        .upload-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .upload-status {
            font-size: 18px;
            font-weight: bold;
            color: #6c757d;
        }

        .upload-percentage {
            font-size: 18px;
            font-weight: bold;
            color: #6c757d;
            margin-left: auto;
        }

        .folder-box {
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            margin-left:30px;
            margin-right:20px;
            border-radius: 8px;
            background-color: #FFF;
            cursor: pointer;
            padding: 20px 25px 20px 25px;
            box-shadow:  rgba(62, 73, 84, 0.04) 0px 12px 23px;
                   
        }



        /*.btn-link {
            display: inline-block;
            color: rgba(0, 0, 0, 0.54);
            font-size: 1.2rem;  
            border: none;
            background-color: transparent;
            width: 15px;
            height: 15px; 
            border-radius: 50%; /* Hace el botón circular */
    /*transition: background-color 0.3s ease, color 0.3s ease, font-size 0.3s ease;
        }*/


        /*.btn-link:hover {
        color:rgba(0, 0, 0, 0.54);
        background-color: #F7F9F9;
        font-size: 1.2rem;
        }*/


        .btn-link {
        color: rgba(0, 0, 0, 0.54);
        font-size: 1.2rem;  
        display: inline-block;
        width: 2.5em;
        height: 2.5em;
        background-color: transparent;
        border: 2px solid transparent;
        border-radius: 50%; /* Hace el botón circular */
        transition: background-color 0.3s ease, color 0.3s ease, font-size 0.3s ease;
        }

        .btn-link:hover {
        color: rgba(0, 0, 0, 0.54);
        background-color: #f5f5f5;
        font-size: 1.2rem;
        }


        .folder-name {
        text-decoration: none;
        color: #333;
        font-size: 0.875rem;
        margin-left: 15px;
         }


        .path-folders {
            margin-left:60px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
           color: rgba(0, 0, 0, 0.6); 

        }

            @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .new-folder {
        animation: fadeIn 0.5s ease-in-out;
    }

    .folder-link {
        text-decoration: none;
        color: #333;
        font-size: 0.875rem;
        margin-left: 15px;
    }
     
        
    </style>
</head>
<body>



<!-- Modal para archivar video -->
<div class="modal fade" id="archiveVideoModal" tabindex="-1" aria-labelledby="archiveVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveVideoModalLabel">Archive Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="folderSelect">Select Folder:</label>
                <select id="folderSelect" class="form-select">
                    <?php
                    // Código PHP para obtener los folders de la base de datos
                    $sql = "SELECT id_folder, name FROM folders";
                    $result = $conexion->query($sql);
                    if ($result->num_rows > 0) {
                        while($folder = $result->fetch_assoc()) {
                            echo "<option value='" . $folder['id_folder'] . "'>" . htmlspecialchars($folder['name']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="archiveVideoBtn">Archive</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="deleteVideoModal" tabindex="-1" aria-labelledby="deleteVideoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteVideoModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this video?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>










<!-- Modal para crear carpeta -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createFolderModalLabel">New Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="createFolderForm">
          <div class="mb-3">
            
            <input type="text" class="form-control" id="folderName" placeholder="Folder Name" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="createFolderBtn">Create Folder</button>
      </div>
    </div>
  </div>
</div>




<?php include("/home/drm/public_html/dashboard/sidebar.php"); ?>
    

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
    <svg class="svg-icon-upload" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill: currentColor; width: 1.8em; height: 1.8em;"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"></path>
    </svg>Upload</button>
    <button class="button-no-fill">Import</button>
    <button class="button-no-fill" data-bs-toggle="modal" data-bs-target="#createFolderModal" data-bs-dismiss="modal">Create Folder</button>


    <div class="path-folders">Root Folder </div>

     <input type="file" id="file-input" style="display: none;" onchange="uploadVideo(event)"></div>








    <div id="uploading" class="uploading" style="display: none;">
        <h4>Uploading</h4>
        <div id="upload-progress">
            <div class="upload-item">
                <p class="file-name"></p>
                <div class="upload-info">
                    <span class="upload-status"></span>
                    <span class="upload-percentage"></span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar"></div>
                </div>
            </div>
        </div>
    </div>




<?php
$sql = "SELECT f.id_folder as id, f.name, COALESCE(COUNT(a.id_video), 0) as video_count 
FROM folders f 
LEFT JOIN archived a ON f.id_folder = a.id_folder 
GROUP BY f.id_folder, f.name";
$result = $conexion->query($sql);

$folders = [];
if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {
$folders[] = $row;
}
}
?>

<div id="container-folders" class="container-folders">
<div class="row">
<?php foreach ($folders as $folder): ?>
      <div class="col-md-3 mb-3 folder-box">
        <div class="d-flex align-items-center">
          <i class="fa-solid fa-folder fa-2x" style="color: #f4d471;"></i>
          <div class="flex-grow-1 ms-3">
            <a href="#" class="folder-name" data-folder-id="<?php echo $folder['id']; ?>">
              <?php echo htmlspecialchars($folder['name']); ?>
            </a>
            <div class="video-count small text-muted ms-3">
            <?php echo $folder['video_count']; ?> video<?php echo $folder['video_count'] != 1 ? 's' : ''; ?>
            </div>
          </div>
          <div class="dropdown">
            <button class="btn-link" type="" id="<?php echo $folder['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="<?php echo $folder['id']; ?>">
              <li><a class="dropdown-item" href="#">Rename</a></li>
              <li><a class="dropdown-item delete-folder" href="#" data-folder-id="<?php echo $folder['id']; ?>">Delete</a></li>
              <li><a class="dropdown-item" href="#"></a></li>
            </ul>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>
</div>






<div class="video-container bg-white p-0">
    <h4 class="titulo-videos">Videos</h4>
    <div class="videos">
        <!-- Video items will be appended here -->
    </div>
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
        const chunkSize = 1024 * 1024; // 1 MB
        const totalChunks = Math.ceil(file.size / chunkSize);
        let currentChunk = 0;

        document.getElementById('uploading').style.display = 'block';
        const uploadItem = document.querySelector('#upload-progress .upload-item');
        uploadItem.querySelector('.file-name').textContent = file.name;

        let startTime = Date.now();
        let lastLoaded = 0;
        let targetLoaded = 0;
        let lastPercent = 0;
        let targetPercent = 0;

        function smoothUpdate() {
            const now = Date.now();
            const progress = Math.min((now - startTime) / 5000, 1); // progress in 5 seconds
            const currentLoaded = lastLoaded + (targetLoaded - lastLoaded) * progress;
            const currentPercent = lastPercent + (targetPercent - lastPercent) * progress;

            updateLoadedText(currentLoaded, file.size);
            updatePercentage(currentPercent);

            if (progress < 1) {
                requestAnimationFrame(smoothUpdate);
            }
        }

        function updatePercentage(percent) {
            uploadItem.querySelector('.progress-bar').style.width = percent + '%';
            uploadItem.querySelector('.upload-percentage').textContent = 
                `${percent.toFixed(2)}% `;
        }

        function uploadNextChunk() {
            const start = currentChunk * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('chunk', chunk, file.name);
            formData.append('fileName', file.name);
            formData.append('chunkNumber', currentChunk);
            formData.append('totalChunks', totalChunks);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload.php', true);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const totalLoaded = start + e.loaded;
                    const percentComplete = (totalLoaded / file.size) * 100;
                    
                    lastLoaded = targetLoaded;
                    targetLoaded = totalLoaded;
                    lastPercent = targetPercent;
                    targetPercent = percentComplete;
                    startTime = Date.now();
                    smoothUpdate();
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
                                    if (currentChunk < totalChunks - 1) {
                                        currentChunk++;
                                        uploadNextChunk();
                                    } else {
                                        displayVideoItem(response);
                                        hideUploadingSection();
                                    }
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

                uploadNextChunk();
            }
        }

        function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    // Siempre usa 2 decimales para MB y GB
    const formattedSize = (i === 2 || i === 3) ? 
        (bytes / Math.pow(k, i)).toFixed(2) : 
        (bytes / Math.pow(k, i)).toFixed(dm);
    
    return formattedSize + ' ' + sizes[i];
}

        function hideUploadingSection() {
            document.getElementById('uploading').style.display = 'none';
            const uploadItem = document.querySelector('#upload-progress .upload-item');
            uploadItem.querySelector('.file-name').textContent = '';
            uploadItem.querySelector('.progress-bar').style.width = '0%';
            uploadItem.querySelector('.upload-status').textContent = '';
            uploadItem.querySelector('.upload-percentage').textContent = '';
            
        }


        window.onload = function() {
            fetch('get_videos.php')
                .then(response => response.json())
                .then(videos => {
                    console.log(videos);
                    videos.forEach(video => {
                        displayVideoItem({
                            fileName: video.nombre_video,
                            uploadDate: video.fecha,
                            videoId: video.id_video,
                            status: 'success'
                        });
                    });
                })
                .catch(error => console.error('Error:', error));
        };

        function updateLoadedText(loaded, total) {
            const uploadItem = document.querySelector('#upload-progress .upload-item');
            const loadedText = formatBytes(loaded);
            const totalText = formatBytes(total);
            uploadItem.querySelector('.upload-status').textContent = 
                `Uploaded: ${loadedText} / ${totalText}`;
        }







document.getElementById('createFolderBtn').addEventListener('click', function() {
    const folderName = document.getElementById('folderName').value;
    if (folderName) {
        // Send AJAX request to create the folder
        fetch('create_folder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'folderName=' + encodeURIComponent(folderName)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);  // For debugging
            if (data.success) {
                // Update the folder list
                updateFolderList();
                // Hide the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createFolderModal'));
                if (modal) {
                    modal.hide();
                }
                document.getElementById('folderName').value = '';
            } else {
                alert('Error creating folder: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the folder: ' + error.message);
        });
    } else {
        alert('Please enter a folder name');
    }
});

function updateFolderList() {
    fetch('get_folders.php')
    .then(response => response.text())
    .then(html => {
        document.getElementById('container-folders').innerHTML = html;
    })
    .catch(error => {
        console.error('Error updating folder list:', error);
    });
}








// Función para eliminar una carpeta
function deleteFolder(folderId) {
    if (confirm('Are you sure you want to delete this folder?')) {
        fetch('delete_folder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'folderId=' + encodeURIComponent(folderId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateFolderList();
            } else {
                alert('Error deleting folder: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the folder: ' + error.message);
        });
    }
}

// Event delegation para los botones de eliminar
document.getElementById('container-folders').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('delete-folder')) {
        e.preventDefault();
        const folderId = e.target.getAttribute('data-folder-id');
        deleteFolder(folderId);
    }
});












let videoIdToDelete; // Variable para almacenar el ID del video a eliminar

document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('btn-video-list-delete')) {
        e.preventDefault();
        videoIdToDelete = e.target.getAttribute('data-video-id');
        console.log('Delete button clicked, video ID:', videoIdToDelete);
        // Muestra el modal
        const modal = new bootstrap.Modal(document.getElementById('deleteVideoModal'));
        modal.show();
    }
});

// Confirmar eliminación del video
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    fetch('delete_video.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'videoId=' + encodeURIComponent(videoIdToDelete)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            //reload
            location.reload();

            // Actualizar la lista de videos o hacer cualquier otra acción necesaria
            //updateVideoList(); // Función que tendrás que implementar
        } else {
            alert('Error deleting video: ' + (data.message || 'Unknown error'));
        }
        // Cerrar el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteVideoModal'));
        if (modal) {
            modal.hide();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the video: ' + error.message);
    });
});



let videoIdToArchive; // Variable para almacenar el ID del video a archivar

// Event listener para el botón "Archive"
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'archiveBtn') {
        e.preventDefault();
        videoIdToArchive = e.target.getAttribute('data-video-id'); // Obtener el ID del video
        const modal = new bootstrap.Modal(document.getElementById('archiveVideoModal'));
        modal.show(); // Mostrar el modal de archivar
    }
});

// Este evento se activará al hacer clic en el botón "Archive" en el modal
document.getElementById('archiveVideoBtn').addEventListener('click', function() {
    const folderId = document.getElementById('folderSelect').value;

    fetch('archive_video.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'videoId=' + encodeURIComponent(videoIdToArchive) + '&folderId=' + encodeURIComponent(folderId)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Video archived successfully');
            location.reload();
        } else {
            alert('Error archiving video: ' + (data.message || 'Unknown error'));
        }
        // Cerrar el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('archiveVideoModal'));
        if (modal) {
            modal.hide();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while archiving the video: ' + error.message);
    });
});













/********************************************************************************* */

//document.addEventListener('DOMContentLoaded', function() {
    const containerFolders = document.getElementById('container-folders');
    if (containerFolders) {
        containerFolders.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('folder-name')) {
                e.preventDefault();
                const folderId = e.target.getAttribute('data-folder-id');
                if (folderId) {
                    loadVideosForFolder(folderId);
                } else {
                    console.error('Folder ID not found');
                }
            }
        });
    } else {
        console.error('Container folders not found');
    }

//});


    function loadVideosForFolder(folderId) {
    const videoContainer = document.querySelector('.videos');
    videoContainer.innerHTML = '<p>Loading videos...</p>'; // Indicador de carga

    fetch('get_folder_videos.php?folderId=' + encodeURIComponent(folderId))
        .then(response => response.json())
        .then(videos => {
            videoContainer.innerHTML = ''; // Limpiar el indicador de carga
            if (videos.length === 0) {
                videoContainer.innerHTML = '<p>No videos found in this folder.</p>';
            } else {
                videos.forEach(video => {
                    displayVideoItem({
                        fileName: video.nombre_video,
                        uploadDate: video.fecha,
                        videoId: video.id_video,
                        status: 'success'
                    });
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            videoContainer.innerHTML = '<p>Error loading videos. Please try again.</p>';
        });
}







/*function displayVideoItem(file) {
  
    // Mueve la declaración del template dentro de la función para asegurar que 
    // siempre exista en el DOM cuando esta función sea llamada.
    //const template = document.getElementById('video-item-template');
    const template = document.querySelector('#video-item-template');
    console.log("Template element:", template);
    if (!template) {
        console.error('Video item template not found');
        return;
    }
    const clone = template.cloneNode(true);
    clone.removeAttribute('id');
    clone.style.display = 'flex';
    clone.querySelector('.file-name').textContent = file.fileName;
    clone.querySelector('.upload-date').textContent = file.uploadDate;
    clone.querySelector('.btn-video-list-delete').setAttribute('data-video-id', file.videoId);
    clone.querySelector('.btn-video-list-archive').setAttribute('data-video-id', file.videoId);
    clone.querySelector('.btn-video-list-conf').setAttribute('data-video-id', file.videoId);

    const badge = clone.querySelector('.badge');
    badge.textContent = 'Ready';
    badge.classList.add('badge-ready');
    badge.classList.remove('processing-badge');
    
    const videosContainer = document.querySelector('.videos');
    if (videosContainer) {
        videosContainer.appendChild(clone);
    } else {
        console.error('Videos container not found');
    }
}*/



function displayVideoItem(file) {
    const videoItem = document.createElement('div');
    videoItem.className = 'video-item';
    videoItem.innerHTML = `
        <div class="video-thumb">
            <img src="https://via.placeholder.com/60" alt="thumbnail">
            <div>
                <p class="file-name">${file.fileName}</p>
                <small class="upload-date">${file.uploadDate}</small>
                <div class="progress-container">
                    <div class="progress-bar"></div>
                </div>
            </div>
        </div>
        <div>
            <span class="badge badge-ready">Ready</span>
            <button id="archiveBtn" class="btn-video-list-archive mx-1" data-video-id="${file.videoId}">Archive</button>
            <button class="btn-video-list-conf mx-1" data-video-id="${file.videoId}">Configuration</button>
            <button class="btn-video-list-delete mx-1" data-video-id="${file.videoId}">Delete</button>
        </div>
    `;
    
    const videosContainer = document.querySelector('.videos');
    if (videosContainer) {
        videosContainer.appendChild(videoItem);
    } else {
        console.error('Videos container not found');
    }
}



</script>
</body>
</html>