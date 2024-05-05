<?php
    require_once "./config/app.php";
    require_once "./autoload.php";
    require_once "./app/views/inc/session_start.php";

    //obtiene todos los valores que estan en la url
    if (isset($_GET['views'])) {
        //obtiene por separado los valores de la url
        $url = explode("/",$_GET['views']);
    } else {
        $url = ['login'];
    }
    
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "./app/views/inc/head.php"; ?>
</head>
    <?php 
    use app\controllers\viewsController;
    use app\controllers\loginController;

    $insLogin = new loginController();

    $viewsController = new viewsController();
    $vista = $viewsController->obtenerVistasControlador($url[0]);

    if($vista=="login" || $vista=="404"){
        require_once "./app/views/content/".$vista."-view.php";
    }else{

        //cerrar sesion
        if((!isset($_SESSION['id']) || $_SESSION['id']=="") || (!isset($_SESSION['usuario']) || $_SESSION['usuario']=="")){
            $insLogin->cerrarSesionControlador();
            exit();
        }
        require_once "./app/views/inc/navbar.php";
        require_once $vista;
    }
    
 
    require_once "./app/views/inc/script.php"; 
    ?>
</body>
</html>