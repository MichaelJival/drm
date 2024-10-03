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
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">




  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/all.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-duotone-solid.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-thin.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-solid.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-regular.css">
  <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.6.0/css/sharp-light.css">





    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background-color: #F7F9F9;
            font-family:  "Poppins", sans-serif;
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
            margin-left: 0px;
            padding-right: 50px;
            }

        /*.video-thumb img {
            width: 200px;
            margin-right: 113px;
        }*/

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


        .btn-video-list {
            border: 1px solid #959CAF;
            color: #818895;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .btn-video-list:hover {
            /*transform: scale(0.96);*/
            border: 1px solid #5F50E4;
            color: #5F50E4;
       
        }


        .btn-video-list-conf {
            border: 1px solid #A9B0C9;
            color: #333;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .btn-video-list-conf:hover {
            /*transform: scale(0.96);*/
            border: 1px solid #5F50E4;
            color: #5F50E4;
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

        .btn-video-list-archived {
            border: 1px solid #CCC;
            color: #CCC;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .btn-video-list-archived:hover {
            transform: scale(0.96);
        }


        .btn-video-list-delete {
            border: 1px solid #959CAF;
            color: #818895;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .btn-video-list-delete:hover {
            /*transform: scale(0.96);*/
            border: 1px solid #E4505F;
            color: #E4505F;
            background-color: #FFFFFF;
            border-radius: 3px;
            padding: 5px 10px 5px 10px;
            font-size: 0.85em;
        }

        .video-item:has(.btn-video-list-delete:hover) {
        border-color: #E4505F;
        }

        .btn-video-embed {
            border: 1px solid #5F50E4;
            color: #FFF;
            font-size: 0.85em;
            font-family: 'Poppins', sans-serif;
            background-color: #5F50E4;
            border-radius: 3px;
            padding: 5px 15px 5px 10px;
            font-size: 0.85em;


        }


        .badge-ready {
            font-size: 14px;
            /*color: rgb(46, 125, 50);*/
            color: #2E7D32;
            text-transform: capitalize;
            font-weight: 600;
            background: #D7FEDD;
            padding: 4px 6px;
            border-radius: 4px;
            margin-right: 10px;
            padding: 10px 15px 10px 15px;
        }


        
        

        .badge-processing {
            font-size: 14px;
            color: rgb(178, 83, 1);
            text-transform: capitalize;
            font-weight: 600;
            background: rgb(255, 240, 217);
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

    .badge-folder {
    /*background-color: #f0f0f0;
    color: #333;
    padding: 3px 6px;
    margin-right: 5px;
    border-radius: 3px;
    font-size: 0.8em;*/
    
            font-size: .65em;
            color: #FFF;
            text-transform: capitalize;
            font-weight: 600;
            background-color: #5F50E4;
            padding: 2px 3px;
            border-radius: 4px;
            margin-right: 10px;
            padding: 5px 10px 5px 10px;
         
        }


        .badge-not-archived {
            font-size: .65em;
            color: #333;
            text-transform: capitalize;
            font-weight: 600;
            background-color: #CCCCCC;
            padding: 2px 3px;
            border-radius: 4px;
            margin-right: 10px;
            padding: 5px 10px 5px 10px;
            
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
           cursor: pointer;

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


    @keyframes processing-animation {
    0% { width: 0%; }
    50% { width: 100%; }
    100% { width: 0%; }
}

.progress-bar-processing {
    animation: processing-animation 2s ease-in-out infinite;
    background-color: #007bff;
}
     


.video-wrapper {
    position: relative;
    cursor: pointer;
    background-color: #CCC;
}

.play-button-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.1); /* Añade un ligero oscurecimiento al video */
    transition: background-color 0.3s ease;
}

.play-button-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.3s ease;
}

.play-icon {
    color: white;
    font-size: 24px;
    margin-left: 4px;
}

.video-wrapper:hover .play-button-overlay {
    background-color: rgba(0, 0, 0, 0.3); /* Oscurece más al hacer hover */
}

.video-wrapper:hover .play-button-circle {
    background-color: rgba(255, 255, 255, 1);
}

.video-wrapper:hover .play-icon {
    color: #5F50E4;
}


.modal-video-wrapper {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    cursor: pointer;
}

.modal-video-wrapper video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.modal-video-wrapper .play-button-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.3);
    opacity: 1;
    transition: opacity 0.3s ease
}

.modal-video-wrapper .play-button-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.3);
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.3s ease;
}

.modal-video-wrapper .play-icon {
    color: white;
    font-size: 50px;
}

.modal-video-wrapper:hover .play-button-circle {
    background-color: rgba(255, 255, 255, 1);
}

.modal-video-wrapper:hover .play-icon {
    color: #5F50E4;
}






@keyframes tvNoise {
            0% { background-position: 0 0; }
            100% { background-position: 100% 100%; }
        }


        @keyframes colorBars {
            0% { transform: translateX(0); }
            100% { transform: translateX(-16.67%); }
        }
        .tv-effect {
            background-image: 
                linear-gradient(to bottom, transparent, rgba(255,255,255,0.3)),
                repeating-radial-gradient(circle at 50% 50%, black 0, black 2px, transparent 2px, transparent 4px);
            background-size: 100% 100%, 4px 4px;
            animation: tvNoise 0.5s infinite linear;
        }
        .color-bars {
            animation: colorBars 2s linear infinite, static 0.05s linear infinite;
        }
        .color-bar {
            opacity: 0.5;
        }


    @keyframes static {  
    
        0% { transform: translate(0, 0); }
        100% { transform: translate(-5px, -5px); }
  }
  
  .tv-effect {
    display: block;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    overflow: hidden;
  }

  .color-bars {
    position: absolute;
    top: 0;
    left: 0;
    width: 120%;
    height: 100%;
    display: flex;
    animation: static 0.05s linear infinite;
    /*nimation: static 0.1s steps(10) infinite;*/
    z-index: 1;
    mix-blend-mode: multiply;
  }

  .color-bars > div {
    flex: 1;
  }

  .noise {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('https://drm.eweo.com/dashboard/noise.jpg') repeat;
    background-size: cover;
    animation: static 0.05s linear infinite;
    z-index: 2;
    opacity: 0.5;
    mix-blend-mode: multiply;
  }


  @keyframes dots {
    0%, 20% { content: ''; }
    40% { content: '.'; }
    60% { content: '..'; }
    80%, 100% { content: '...'; }
}

.processing-text .dots::after {
    content: '';
    animation: dots 1.5s infinite;
}


.disabledEmbed {
   
    border: 1px solid #ccc;
    color: #333;
    font-size: 0.85em;
    font-family: 'Poppins', sans-serif;
    background-color: #ccc;
    border-radius: 3px;
    padding: 5px 15px 5px 10px;
    font-size: 0.85em;
    pointer-events: none;
    opacity: 0.6; 
    font-weight: 600;  
    
    
}


.hide {
    display: none;
}

</style>
</head>
<body>






<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="videoModalLabel">Video Player</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modalVideoContainer"></div>
      </div>
    </div>
  </div>
</div>













<div class="modal fade" id="renameFolderModal" tabindex="-1" aria-labelledby="renameFolderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="renameFolderModalLabel">Rename Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control" id="newFolderName">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmRenameBtn">Rename</button>
      </div>
    </div>
  </div>
</div>

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
        
   <?php include("/home/drm/public_html/dashboard/topbar.php"); ?>

    <div class="content">
    <div class="menu-upload mb-5">
    <button class="button-upload d-flex align-items-center justify-content-center" style="gap: 0.5rem;" onclick="document.getElementById('file-input').click();">
    <svg class="svg-icon-upload" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill: currentColor; width: 1.8em; height: 1.8em;"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"></path>
    </svg>Upload</button>
    <button class="button-no-fill">Import</button>
    <button class="button-no-fill" data-bs-toggle="modal" data-bs-target="#createFolderModal" data-bs-dismiss="modal">Create Folder</button>


    <div onclick="location.reload()" class="path-folders">Root Folder </div>

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
$sql = "SELECT f.id AS id, f.id_folder, f.name, f.fecha, COALESCE(COUNT(a.id_video), 0) as video_count 
FROM folders f 
LEFT JOIN archived a ON f.id_folder = a.id_folder 
GROUP BY f.id_folder, f.name, f.fecha ORDER BY f.id ASC";
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
            <a href="#" class="folder-name" data-folder-id="<?php echo $folder['id_folder']; ?>">
              <?php echo htmlspecialchars($folder['name']); ?>
            </a>

            <div class="video-count small text-muted ms-3">
    <?php echo $folder['video_count']; ?> video<?php echo $folder['video_count'] != 1 ? 's' : ''; ?>
</div>
          </div>

          <div class="dropdown">
            <button class="btn-link" type="" id="<?php echo $folder['id_folder']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu" aria-labelledby="<?php echo $folder['id_folder']; ?>">
            <li><a class="dropdown-item rename-folder" href="#" data-folder-id="<?php echo $folder['id_folder']; ?>">Rename</a></li>
              <li><a class="dropdown-item delete-folder" href="#" data-folder-id="<?php echo $folder['id_folder']; ?>">Delete</a></li>
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
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>

    

document.addEventListener('click', function(e) {
    const videoWrapper = e.target.closest('.video-wrapper');
    if (videoWrapper) {
        const videoId = videoWrapper.getAttribute('data-video-id');
        if (videoId) {
            openVideoModal(videoId);
        }
    }
});


function openVideoModal(videoId) {
    const modalVideoContainer = document.getElementById('modalVideoContainer');
    modalVideoContainer.innerHTML = `
        <div class="modal-video-wrapper" data-video-id="${videoId}">
            <video controls id="modal-video-${videoId}" style="width: 100%; height: auto;">
                <source src="https://drm.eweo.com/dashboard/processed_videos/${videoId}/playlist.m3u8" type="application/x-mpegURL">
                Your browser does not support the video tag.
            </video>
            <div class="play-button-overlay">
                <div class="play-button-circle">
                    <i class="fa-solid fa-play play-icon"></i>
                </div>
            </div>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('videoModal'));
    modal.show();

    const videoElement = document.getElementById(`modal-video-${videoId}`);
    const playButtonOverlay = modalVideoContainer.querySelector('.play-button-overlay');

    // Inicializar el video en el modal
    initializeVideo(`modal-video-${videoId}`);

    // Agregar evento de clic al overlay para reproducir/pausar el video
    playButtonOverlay.addEventListener('click', function() {
        if (videoElement.paused) {
            videoElement.play();
        } else {
            videoElement.pause();
        }
    });

    // Ocultar el overlay cuando el video comience a reproducirse
    videoElement.addEventListener('play', function() {
    playButtonOverlay.style.display = 'none';
    });   

    // Mostrar el overlay cuando el video se pause
    videoElement.addEventListener('pause', function() {
        playButtonOverlay.style.display = 'flex';
    });

    // Detener el video cuando se cierre el modal
    document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
    videoElement.pause();
    videoElement.currentTime = 0;
    playButtonOverlay.style.display = 'flex';
    });
}












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
                                    response.status = 'PROCESSING'; // Cambia el estado inicial a PROCESSING
                                    displayVideoItem(response,true);
                                    hideUploadingSection();
                                    checkVideoStatus(response.videoId);
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
                            folderNames: video.folder_names,
                            status: video.estado // Usa el estado real del video
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
    .then(response => response.json())
    .then(data => {
        const containerFolders = document.getElementById('container-folders');
        const folderSelect = document.getElementById('folderSelect');

        // Actualizar el contenedor de folders
        containerFolders.innerHTML = data.containerHtml;

        // Actualizar el select de folder
        folderSelect.innerHTML = '';
        data.folders.forEach(folder => {
            const option = document.createElement('option');
            option.value = folder.id_folder;
            option.textContent = folder.name;
            folderSelect.appendChild(option);
        });
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
            //alert('Video archived successfully');
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
                        folderNames: video.folder_names,
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


function displayVideoItem(file, isNewUpload = false) {
    const videoItem = document.createElement('div');
    videoItem.className = 'video-item';
    videoItem.setAttribute('data-video-id', file.videoId);
    const videoId = file.videoId;
    
    // Añade estos estilos al head del documento o en tu archivo CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes tvNoise {
            0% { background-position: 0 0; }
            100% { background-position: 100% 100%; }
        }
        @keyframes colorBars {
            0% { transform: translateX(0); }
            100% { transform: translateX(-16.67%); }
        }
        .tv-effect {
            background-image: 
                linear-gradient(to bottom, transparent, rgba(255,255,255,0.3)),
                repeating-radial-gradient(circle at 50% 50%, black 0, black 2px, transparent 2px, transparent 4px);
            background-size: 100% 100%, 4px 4px;
            animation: tvNoise 0.5s infinite linear;
        }
        .color-bars {
            animation: colorBars 2s infinite linear;
        }
    `;
    document.head.appendChild(style);

    videoItem.innerHTML = `
        <div style="display: flex; align-items: flex-start;">
            <div class="video-thumb" style="position: relative; width: 280px; height: 160px; flex-shrink: 0; overflow: hidden;">
                <div id="video-container-${videoId}" class="video-wrapper" data-video-id="${videoId}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                    <img src="https://drm.eweo.com/portadas/${videoId}.jpg?v=<?php echo time(); ?>" alt="${file.fileName}" class="video-thumbnail" style="width: 100%; height: 100%; object-fit: cover; ${file.status === 'PROCESSING' ? 'display: none;' : ''}">
                    
                    <div class="tv-effect" style="display: ${file.status === 'PROCESSING' ? 'block' : 'none'}; width: 100%; height: 100%; position: absolute; top: 0; left: 0; overflow: hidden;">
                        <div class="color-bars" style="position: absolute; top: 0; left: 0; width: 120%; height: 100%; display: flex;">
                             <div style="background: white;"></div>

                                <div style="background: yellow;"></div>
                                <div style="background: cyan;"></div>
                                <div style="background: green;"></div>
                                <div style="background: magenta;"></div>
                                <div style="background: red;"></div>
                                <div style="background: pink;"></div>
                                <div style="background: lime;"></div>
                                <div style="background: orange;"></div>
                                <div style="background: purple;"></div>
                                <div style="background: pink;"></div>
                                <div style="background: gold;"></div>

                           
                            </div>
                             <div class="noise"></div>
                             <div class="processing-text" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 18px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                             PROCESSING<span class="dots"></span>
                             </div>

                    </div>
                    <div class="play-button-overlay" style="display: ${file.status === 'PROCESSING' ? 'none' : 'block'}; position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                        <div class="play-button-circle">
                            <i class="fa-solid fa-play play-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-left: 10px;">
                <p class="ms-5 mt-5 file-name editable" style="margin: 0;" contenteditable="true">${file.fileName}</p>
                <small class="ms-5 upload-date" style="display: block; margin-bottom: 5px;">${file.uploadDate}</small>
                <p style="margin: 0;"> ${file.folderNames ? file.folderNames.split(', ').map(folder => 
                    `<span class="ms-5 badge badge-folder">${folder}</span>`
                ).join(' ') : '<span class="ms-5 badge badge-not-archived">Not Archived</span>'}</p>
                <div class="progress-container" style="display: ${file.status === 'READY' ? 'none' : 'none'}; margin-top: 5px;">
                    <!--<div class="progress-bar ${file.status === 'PROCESSING' ? 'progress-bar-processing' : ''}"></div>-->
                </div>
            </div>
        </div>
        <div style="margin-top: 10px;">
            <span class="badge badge-${file.status.toLowerCase()}">${file.status}</span>
            
            <button  title="${videoId}" id="Enbed_${videoId}" class="btn-video-embed mx-1 ${file.status === 'PROCESSING' ? 'disabledEmbed' : ''}" data-video-id="${videoId}" ${file.status === 'PROCESSING' ? 'title="Video Not Ready"' : ''}> <> Embed </button>

            <button title="${videoId}" id="copyID_${videoId}" class="btn-video-list mx-1" data-video-id="${videoId}">Copy ID</button>
            <button id="archiveBtn" class="btn-video-list mx-1" data-video-id="${videoId}">Archive</button>
            <button class="btn-video-list mx-1" data-video-id="${videoId}">Configuration</button>
            <button class="btn-video-list-delete mx-1" data-video-id="${videoId}">Delete</button>
        </div>
    `;
    
    const videosContainer = document.querySelector('.videos');
    if (videosContainer) {
        if (isNewUpload) {
            videosContainer.insertBefore(videoItem, videosContainer.firstChild);
        } else {
            videosContainer.appendChild(videoItem);
        }
        
        // Add click event listener for the Copy ID button
        const copyButton = videoItem.querySelector(`#copyID_${videoId}`);
        copyButton.addEventListener('click', function() {
            navigator.clipboard.writeText(videoId).then(() => {
                // Provide visual feedback that the ID was copied
                const originalText = copyButton.textContent;
                copyButton.textContent = 'Copied!';
                setTimeout(() => {
                    copyButton.textContent = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        });

        // Add click event listener for the Embed button
        const embedButton = videoItem.querySelector(`#Enbed_${videoId}`);
        embedButton.addEventListener('click', function() {
            const embedUrl = `https://drm.eweo.com/player.php?id=${videoId}`;
            navigator.clipboard.writeText(embedUrl).then(() => {
                // Provide visual feedback that the URL was copied
                const originalText = embedButton.textContent;
                embedButton.textContent = 'Embed URL Copied!';
                setTimeout(() => {
                    embedButton.textContent = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        });
        
    } else {
        console.error('Videos container not found');
    }
}





let folderIdToRename;

document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('rename-folder')) {
        e.preventDefault();
        folderIdToRename = e.target.getAttribute('data-folder-id');
        const currentFolderName = e.target.closest('.folder-box').querySelector('.folder-name').textContent.trim();
        const modal = new bootstrap.Modal(document.getElementById('renameFolderModal'));
        document.getElementById('newFolderName').value = currentFolderName;
        modal.show();
        setTimeout(() => {
            const input = document.getElementById('newFolderName');
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }, 500);
    }
});

document.getElementById('confirmRenameBtn').addEventListener('click', function() {
    const newName = document.getElementById('newFolderName').value.trim();
    if (newName) {
        fetch('rename_folder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'folderId=' + encodeURIComponent(folderIdToRename) + '&newName=' + encodeURIComponent(newName)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateFolderList();
                const modal = bootstrap.Modal.getInstance(document.getElementById('renameFolderModal'));
                modal.hide();
            } else {
                alert('Error renaming folder: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while renaming the folder: ' + error.message);
        });
    } else {
        alert('Please enter a folder name');
    }
});



function checkVideoStatus(videoId) {
    fetch('check_video_status.php?videoId=' + encodeURIComponent(videoId))
        .then(response => response.json())
        .then(data => {
            const videoItem = document.querySelector(`.video-item[data-video-id="${videoId}"]`);
            if (videoItem) {
                let statusBadge = videoItem.querySelector('.badge-processing, .badge-ready');
                if (!statusBadge) {
                    statusBadge = document.createElement('span');
                    statusBadge.className = 'badge';
                    videoItem.querySelector('div:last-child').prepend(statusBadge);
                }
                

                // Actualizar la visibilidad de la portada, el efecto de TV y el botón de reproducción
                const thumbnail = videoItem.querySelector('.video-thumbnail');
                const tvEffect = videoItem.querySelector('.tv-effect');
                const playButton = videoItem.querySelector('.play-button-overlay');
                if (thumbnail && tvEffect && playButton) {
                    if (data.status === 'READY') {
                        thumbnail.style.display = 'block';
                        tvEffect.style.display = 'none';
                        playButton.style.display = 'flex';
                    } else {
                        thumbnail.style.display = 'none';
                        tvEffect.style.display = 'block';
                        playButton.style.display = 'none';
                    }
                }


                const embedButton = videoItem.querySelector(`#Enbed_${videoId}`);
                if (embedButton) {
                    if (data.status === 'READY') {
                        embedButton.classList.remove('disabledEmbed');
                        embedButton.removeAttribute('title');
                    } else {
                        embedButton.classList.add('disabledEmbed');
                        embedButton.setAttribute('title', 'Video Not Ready');
                    }
                }


                const oldStatus = statusBadge.textContent;
                statusBadge.className = `badge badge-${data.status.toLowerCase()}`;
                statusBadge.textContent = data.status;
                
                // Actualizar la barra de progreso
                const progressContainer = videoItem.querySelector('.progress-container');
                if (progressContainer) {
                    if (data.status === 'PROCESSING') {
                        progressContainer.style.display = 'block';
                        const progressBar = progressContainer.querySelector('.progress-bar');
                        if (progressBar) {
                            progressBar.classList.add('progress-bar-processing');
                        }
                    } else if (data.status === 'READY') {
                        progressContainer.style.display = 'none';
                    }
                }
                
                if (data.status === 'READY' && oldStatus !== 'READY') {
                    // El estado ha cambiado a READY por primera vez
                    const videoContainer = videoItem.querySelector(`#video-container-${videoId}`);
                    if (videoContainer) {
                        // Actualizar la imagen con la nueva portada (si es necesario)
                        const thumbnailImg = videoContainer.querySelector('img');
                        if (thumbnailImg) {
                            // Añadir un parámetro de versión para forzar la recarga de la imagen
                            const timestamp = new Date().getTime();
                            thumbnailImg.src = `https://drm.eweo.com/portadas/${videoId}.jpg?v=${Date.now()}`;
                        }
                    }
                    
                    const oldBadges = videoItem.querySelectorAll('.badge-processing');
                    oldBadges.forEach(badge => badge.remove());
                }
            }
            
            if (data.status !== 'READY') {
                setTimeout(() => checkVideoStatus(videoId), 5000);
            }
        })
        .catch(error => console.error('Error:', error));
}



function initializeVideo(videoId) {
    const video = document.getElementById(videoId);
    if (Hls.isSupported()) {
        const hls = new Hls();
        hls.loadSource(video.querySelector('source').src);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            console.log('HLS manifest loaded');
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = video.querySelector('source').src;
        video.addEventListener('loadedmetadata', function() {
            console.log('Video metadata loaded');
        });
    }
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
}



document.querySelector('.videos').addEventListener('blur', function(e) {
    if (e.target.classList.contains('editable')) {
        const newFileName = e.target.textContent.trim();
        const videoId = e.target.closest('.video-item').getAttribute('data-video-id');

        if (newFileName) {
            // Actualiza el nombre en la tabla de videos
            fetch('update_video_name.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `videoId=${encodeURIComponent(videoId)}&newFileName=${encodeURIComponent(newFileName)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar el nombre en el HTML correspondiente
                    e.target.textContent = newFileName; // En file name
                    // Aquí también podrás hacer algo para actualizar '/home/drm/videos/file-name' si es necesario.
                    console.log("Nombre actualizado en la base de datos y visualmente.");
                } else {
                    alert('Error updating video name: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the video name: ' + error.message);
            });
        }
    }
}, true);



</script>
</body>
</html>