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
$h = date('YmdHis');
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
        /* Añadir el estilo aquí */
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
            padding: 10px 20px; /* Añadir padding izquierdo para mantener alineación */
            text-decoration: none;
            color: #000;
            display: flex; /* Flex para alinear ícono y texto */
            align-items: center; /* Alinear verticalmente */
            text-align: left; /* Alineación del texto a la izquierda */
            margin-left: 0; /* Quitar margen izquierdo */
        }

        .sidebar a:hover {
            background-color: #E5E3F3;
        }

        .sidebar i {
            font-size: 1.5em; /* Tamaño de los iconos */
            margin-right: 10px; /* Espacio entre el ícono y el texto */
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
            margin-left : 20px;
            margin-right : 100px;


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

        .menu-upload {
            margin-left : 20px;
            margin-top : 10px;
        }

    .button-upload {
    border: 1px solid #42389F;
    color: #FFFFFF;
    background-color: #5F50E4;
    border-radius: 3px;
    width: 140px;
    height: 45px;
    padding-right:12px;
    }
    .button-upload:hover {
    border: 1px solid #42389F;
    color: #FFFFFF;
    background-color: #5F50E4;
    border-radius: 3px;
    width: 140px;
    height: 45px;
    padding-right:12px;
    transform: scale(0.96); /* Hacer que se vea un poco más pequeño, dando un efecto de "presionado" */
    box-shadow:  0 1px 1px rgba(0, 0, 0, 0.2); /* Sombra interior para el efecto "hundido" */

    }  
    

    .button-upload:active {
    /* Los mismos estilos aplicados cuando el botón se presiona */
    border: 1px solid #42389F;
    color: #FFFFFF;
    background-color: #5F50E4;
    border-radius: 3px;
    width: 140px;
    height: 45px;
}


     

    .button-no-fill {
    border: 1px solid #5F50E4 ;
    color: rgb(95, 80, 228);
    background-color: #F9F9F9;
    border-radius: 3px;
    width: 140px;
    height: 45px;
    }

    .button-no-fill:hover {
    border: 1px solid #5F50E4 ;
    color: rgb(95, 80, 228);
    background-color: #F9F9F9;
    border-radius: 3px;
    width: 140px;
    height: 45px;
    transform: scale(0.96); /* Hacer que se vea un poco más pequeño, dando un efecto de "presionado" */
    box-shadow:  0 1px 1px rgba(0, 0, 0, 0.2); /* Sombra interior para el efecto "hundido" */
    }

    .menu-upload {
    display: flex;
    gap: 10px; /* Espacio entre los botones */
    align-items: center; /* Alinea verticalmente los elementos */
    justify-content: flex-start; /* Alinea horizontalmente los botones al inicio */
}

    .svg-icon-upload {
        margin-right:0px;

    }



    </style>
</head>
<body>
    <div class="sidebar">
        <a href="panel.php"><i class="fa-regular fa-circle-play"></i><span>Videos</span></a>
        <a href="usuarios.php"><i class="fa-regular fa-shield-check"></i><span>Security</span></a>
        <a href="drm.php"><i class="fa-light fa-database"></i><span>Storage</span></a>
        <a href="drm.php"><i class="fa-light fa-display-chart-up"></i><span>Analytics</span></a>
        <a href="drm.php"><i class="fa-regular fa-clapperboard-play"></i><span>Custom Player</span></a>
        <a href="#"><i class="fa-regular fa-gear"></i><span>Configurations</span></a>
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

        <div class="menu-upload">
        <button class="button-upload d-flex align-items-center justify-content-center" style="gap: 0.5rem;">
  <svg class ="svg-icon-upload" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="fill: currentColor; width: 1.8em; height: 1.8em;">
    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"></path>
  </svg>
  Upload
</button>


 <button class="button-no-fill">Import</button>
                <button class="button-no-fill">Create Folder</button>


</div>



               
          



            <!-- Folder Section -->
            <div class="folders">
                <div class="folder">
                    <i class="fa-regular fa-folder"></i>
                    <p>ENTRADAS<br><small>12 Videos & 0 Folders</small></p>
                </div>
                <div class="folder">
                    <i class="fa-regular fa-folder"></i>
                    <p>BACKTESTING<br><small>15 Videos & 0 Folders</small></p>
                </div>
                <div class="folder">
                    <i class="fa-regular fa-folder"></i>
                    <p>PSICOTRADING<br><small>12 Videos & 0 Folders</small></p>
                </div>
                <div class="folder">
                    <i class="fa-regular fa-folder"></i>
                    <p>TRIBU BINARIAS EVOLUTION<br><small>28 Videos & 0 Folders</small></p>
                </div>
                <div class="folder">
                    <i class="fa-regular fa-folder"></i>
                    <p>TRIBU FX EVOLUTION<br><small>63 Videos & 1 Folders</small></p>
                </div>
            </div>

            <!-- Videos Section -->
            <div class="videos">

               <!-- grid boostrap-->
                <h4>Videos</h4>
                <div class="video-item">
                    <div class="video-thumb">
                        <img src="https://via.placeholder.com/60" alt="thumbnail">
                        <div>
                            <p>mundo.mp4</p>
                            <small>19-Sep-2024, 23:25</small>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-success">Ready</span>
                        <button class="btn btn-outline-primary btn-sm mx-1">Copy ID</button>
                        <button class="btn btn-outline-info btn-sm mx-1">Edit</button>
                        <button class="btn btn-outline-secondary btn-sm mx-1">Embed</button>
                    </div>
                </div>
                <!-- Repite "video-item" para los demás videos -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("userDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        window.onclick = function(event) {
            if (!event.target.matches('.user-icon')) {
                var dropdown = document.getElementById("userDropdown");
                if (dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
            }
        }
        </script>
</body>
</html>