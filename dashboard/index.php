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
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-thin.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-solid.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-regular.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/sharp-light.css">
    <link rel="stylesheet" type="text/css" href="https://drm.eweo.com/dashboard/css/styles.css?v=<?php echo $h ?>"> 
    
        
</head>
<body>
    <div class="sidebar">
        <a href="videos"><i class="fa-regular fa-circle-play"></i><span class="">Videos</span></a>
        <a href="usuarios.php"><i class="fa-regular fa-shield-check"></i><span class="">Security</span></a>
        <a href="drm.php"><i class="fa-light fa-database"></i><span class="">Storage</span></a>
        <a href="drm.php"><i class="fa-light fa-display-chart-up"></i><span class="">Analytics</span></a>
        <a href="drm.php"><i class="fa-regular fa-clapperboard-play"></i><span class="">Custom Player</span></a>
        <a href="#"><i class="fa-regular fa-gear"></i><span class="">Configurations</span></a>
    </div>
    
    <div class="main-content">
        <div class="top-bar">
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
            <!-- Aquí va el contenido principal del dashboard -->
            <h1>Bienvenido al Dashboard</h1>
            <!-- Puedes añadir más contenido aquí -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("userDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        // Cerrar el dropdown si se hace clic fuera de él
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