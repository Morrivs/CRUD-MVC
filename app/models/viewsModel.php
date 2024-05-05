<?php
namespace app\models;

class viewsModel{
    protected function obtenerVistasModelo($vista){
        //palabras que se van a permitir en la url
        $listaBlanca=["dashboard","userNew","userList","userSearch","userUpdate","userPhoto","logOut"];

        //revisa si el nombre de la vista aparece en la lista blanca
        if(in_array($vista, $listaBlanca)){
            if(is_file("./app/views/content/".$vista."-view.php")){
                $contenido="./app/views/content/".$vista."-view.php";
            }else{
                $contenido="404";
            }
        }elseif($vista=="login" || $vista=="index"){
            $contenido="login";
        }else{
            $contenido="404";
        }
        return $contenido;
    }
}