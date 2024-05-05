<?php
    namespace app\models;
    use \PDO;

    if(file_exists(__DIR__."/../../config/server.php")){
        require_once __DIR__."/../../config/server.php";
    }

    class mainModel {
        private $server = DB_SERVER;
        private $db= DB_NAME;
        private $user = DB_USER;
        private $pass= DB_PASS;

        //funcion conectar a bd
        protected function conectar(){
            $conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db, $this->user, $this->pass);
            //utilizar caracters utf 8 ñ por ejemplo
            $conexion->exec("SET CHARACTER SET utf8");
            return $conexion;
        }
        //funcion ejecitar consultas
        protected function ejecutarConsulta($consulta){
            $sql = $this->conectar()->prepare($consulta);
            $sql->execute();
            return $sql;
        }

        //filtro para evitar injeccion sql limpiar cadenas
        public function limpiarCadena($cadena){
            $palabras=["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," SELECT ","DELETE FROM","INSERT INTO","DROP TABLE","DROP DATABASE","TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","<",">","==","=",";","::"];

            //eliminar los espacios en blanco
            $cadena=trim($cadena);
            $cadena=stripcslashes($cadena);

            foreach ($palabras as $palabra){
                $cadena = str_ireplace($palabra, "" ,$cadena);
            }
            $cadena=trim($cadena);
            $cadena=stripcslashes($cadena);

            return $cadena;
        }

        protected function verificarDatos($filtro, $cadena){
            if(preg_match("/^".$filtro."$/",$cadena)){
                return false;
            }else{
                return true;
            }
        }

        protected function guardarDatos($tabla, $datos){
            $query = "INSERT INTO $tabla (";

            $c=0;
            foreach($datos as $clave){
                if($c>=1){
                    $query.=","; 
                }
                $query.=$clave["campo_nombre"];
                $c++;
                }

            $query.=") VALUES(";

            $c=0;
            foreach($datos as $clave){
                if($c>=1){
                    $query.=","; 
                }
                $query.=$clave["campo_marcador"];
                $c++;
            }

            $query.=")";
            $sql = $this->conectar()->prepare($query);

            foreach($datos as $clave){
                $sql->bindParam($clave["campo_marcador"],$clave["campo_valor"]);
            }

            $sql->execute();
            return $sql;
        }

        public function seleccionarDatos($tipo,$tabla,$campos,$id){
            $tipo = $this->limpiarCadena($tipo);
            $tabla = $this->limpiarCadena($tabla);
            $campos = $this->limpiarCadena($campos);
            $id = $this->limpiarCadena($id);

            //seleccionar un usuario mediante su id
            if($tipo == "unico"){
                $sql = $this->conectar()->prepare("SELECT * FROM $tabla WHERE $campos =:ID");
                $sql->bindParam(":ID",$id);
            }elseif($tipo=="normal"){
                $sql = $this->conectar()->prepare("SELECT $campos FROM $tabla");
            }
            $sql->execute();

            return $sql;
        }

        protected function actualizarDatos($tabla, $datos, $condicion){
			$query="UPDATE $tabla SET ";

			$C=0;
			foreach ($datos as $clave){
				if($C>=1){ $query.=","; }
				$query.=$clave["campo_nombre"]."=".$clave["campo_marcador"];
				$C++;
			}

			$query.=" WHERE ".$condicion["condicion_campo"]."=".$condicion["condicion_marcador"];

			$sql=$this->conectar()->prepare($query);

			foreach ($datos as $clave){
				$sql->bindParam($clave["campo_marcador"],$clave["campo_valor"]);
			}

			$sql->bindParam($condicion["condicion_marcador"],$condicion["condicion_valor"]);

			$sql->execute();

			return $sql;
        }

        protected function eliminarRegistros($tabla,$campo,$id){
            
            $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
            $sql->bindParam(":id",$id);
            $sql->execute();

            return $sql;
        }

        protected function paginadorTablas($pagina, $numeroPaginas, $url, $botones){
            $tabla='
                <nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">
            ';

            if ($pagina<=1) {
                $tabla.='
                    <a class="pagination-previous is-disabled" disabled >Anterior</a>
                    <ul class="pagination-list">
                ';
            } else {
                //se le resta 1 a la pagina actual para que vaya a la anterior
                $tabla.='
                    <a class="pagination-previous" href="'.$url.($pagina-1).'/">Anterior</a>
                    <ul class="pagination-list">
                        <li><a class="pagination-link" href="'.$url.'1/"></a></li>
                        <li><span class="pagination-ellipsis">&hellip;</span></li>
                ';
            }

            $ci=0;

            for($i=$pagina; $i<=$numeroPaginas; $i++){
                //limite de botones
                if($ci>=$botones){
                    break;
                }

                if($pagina==$i){
                    $tabla.='<li><a class="pagination-link is-current" href="'.$url.$i.'/">'.$i.'</a></li>';
                }else{
                    $tabla.='<li><a class="pagination-link" href="'.$url.$i.'/">'.$i.'</a></li>';
                }
                $ci++;

            }

            if ($pagina==$numeroPaginas) {
                $tabla.='
                </ul>
                <a class="pagination-next is-disabled" disabled >Siguiente</a>
                ';
            } else {
                $tabla.='
                    <li><span class="pagination-ellipsis">&hellip;</span></li>
                    <li><a class="pagination-link" href="'.$url.$numeroPaginas.'/">'.$numeroPaginas.'</a></li>
                </ul>
                <a class="pagination-next" href="'.$url.($pagina+1).'/">Siguiente</a>
                ';
            }    
            $tabla.='</nav>'; 
            return $tabla;
        }
    }

