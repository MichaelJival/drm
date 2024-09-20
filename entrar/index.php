<?php
//////////////////////////////////////////////////
include ("../ufw.php");
//////////////////////////////////////////////////
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("../conexion/conexion.php");
// Iniciar sesión
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consulta SQL para verificar las credenciales
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$password'";
    $result = $conexion->query($sql);

    if ($result->num_rows == 1) {
        // Usuario autenticado, guardar información de sesión
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['nombre'] = $row['nombre']; // Guardar el nombre en la variable de sesión
        $_SESSION['csrf_token'] = "1234567890";  
        
        $user_authenticated = true;
       if($user_authenticated){$_SESSION['authorized']=true;}else{$_SESSION['authorized'] = false;}  

        // Redirigir a la página de inicio o a cualquier otra página del inicio de sesión
        header("location: ../dashboard.php");}
}
$conexion->close();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="">

    <!-- Title Page -->
    <title>Entrar -1449</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font special for pages -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Estilos CSS -->
    <link href="css/estilos.css?v=<?php echo $h ?>" rel="stylesheet">   

    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    
<div class="d-flex justify-content-center">
        <div class="pt-5">
            <div class="container">
                <div class="card-login shadow">
                    <div class="card-body mr-5 ml-5 pb-5">
                        <h2 class="title text-center mb-4"> Admin</h2>
                        <hr>
                        <form method="POST" action="">
                            <div class="form-group">
                                <div class="input-wrapper">
                                   <label for="usuario" class="d-flex align-items-center">Email:</label>
                                    <input class="form-control inputs" type="text" name="usuario" value="michaeljival@gmail.com" id="usuario" placeholder="Tu Email">
                                </div>
                               
                            </div>
                            <div class="form-group">
                                <div class="input-wrapper">
                                      <label for="password" class="d-flex align-items-center">Contraseña:</label>
                                    <input class="form-control inputs" type="password" name="password" id="password" placeholder="Tu Contraseña">
                                </div>
                              
                            </div>
                            <div class="text-center mt-4">
                                <button class="btn btn-primary btn-block" type="submit">ENTRAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    
</body>

</html>