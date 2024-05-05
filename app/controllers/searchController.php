<?php

	namespace app\controllers;
	use app\models\mainModel;

	class searchController extends mainModel{

        //controlador modulos de busqueda
        public function modulosbusquedaControlador($modulo){
            $listaModulos = ['userSearch'];

            if(in_array($modulo,$listaModulos)){
                return false;
            }else{
                return true;
            }
        }

        //controlador para iniciar las busquedas
        public function iniciarBuscadorControlador(){

            # Almacenando datos#
		    $url=$this->limpiarCadena($_POST['modulo_url']);
		    $texto=$this->limpiarCadena($_POST['txt_buscador']);

            //validar si el modulo existe
            if($this->modulosbusquedaControlador($url)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No pudimos procesar la peticion en este momento",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            if($texto==""){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"introduce un termino de busqueda",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            //verificar la integridad de los datos
            if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}",$texto)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El termino de busqueda no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            $_SESSION[$url]=$texto;
            $alerta=[
                "tipo"=>"redireccionar",
                "url"=>\APP_URL.$url."/"
            ];
            return json_encode($alerta);
        }

        //controlador elimninar busqueda
        public function eliminarBuscadorControlador(){
            # Almacenando datos#
		    $url=$this->limpiarCadena($_POST['modulo_url']);

            if($this->modulosbusquedaControlador($url)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No pudimos procesar la peticion en este momento",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            unset($_SESSION[$url]);
            $alerta=[
                "tipo"=>"redireccionar",
                "url"=>\APP_URL.$url."/"
            ];
            return json_encode($alerta);
        }
    }