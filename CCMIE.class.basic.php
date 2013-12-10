<?php
 #####################################################################################################
 #	Este script permanece libre mientras estas lineas permanezcan intactas
 #####################################################################################################
 #	Nombre de 				
 #	Archivo		:		CCMIE.class.basic.php
 #
 #	Autor		:		Fabio Grandas <gffabio@hotmail.com>
 #
 #	Version		:		1.0.4
 #
 #	Descripcion	:		C conectar, C consultar, M modificar, I insertar, E eliminar
 #						CCMIE es una clase basada en PHP/MySQL, que recibe
 #						una serie de datos y los procesa para así lograr unas mejores consultas.
 #
 #	URL			:		http://www.gffabio.webcindario.com/
 #
 #	Documentacion:		http://www.gffabio.webcindario.com/document/CCMIE
 #
 #####################################################################################################
 #
 #     CCMIE - consultas en PHP/MySQL
 #     Copyright (C) 2011  Fabio Grandas
 #
 #     This program is free software: you can redistribute it and/or modify
 #     it under the terms of the GNU General Public License as published by
 #     the Free Software Foundation, either version 3 of the License, or
 #     (at your option) any later version.
 #
 #     This program is distributed in the hope that it will be useful,
 #     but WITHOUT ANY WARRANTY; without even the implied warranty of
 #     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 #     GNU General Public License for more details.
 #
 #     You should have received a copy of the GNU General Public License
 #     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # 	Este programa es software libre: usted puede redistribuirlo y / o modificar
 # 	Bajo los términos de la Licencia Pública General de GNU según lo publicado por
 # 	La Free Software Foundation, ya sea la versión 3 de la Licencia, o
 # 	(A su elección) cualquier versión posterior.
 #
 # 	Este programa se distribuye con la esperanza de que sea útil,
 # 	Pero SIN NINGUNA GARANTÍA, incluso sin la garantía implícita de
 # 	O IDONEIDAD PARA UN PROPÓSITO PARTICULAR. ver el
 # 	GNU General Public License para más detalles.
 #
 # 	Deberías haber recibido una copia de la Licencia Pública General de GNU
 # 	Junto con este programa. Si no es así, consulte <http://www.gnu.org/licenses/>.
 #####################################################################################################
 #	Clase CCMIE - Consultas  con PHP/MySQL
 #
 #
 #
 #  @package CCMIE
 #	@author Fabio Grandas A
 #	@license http://opensource.org/licenses/gpl-license.php GNU Public License
 #	@version v1.0.4
 #  @copyright 2011
 #  @access public
 #	Revision: 10 diciembre 2011 

require_once('configuracion.php');
#en caso de problemas en el servidor habilitar la linea 94 y comentariar la linea 65
ini_set ('display_errors',0);
//#error_reporting(-1);
//#error_reporting(E_ERROR|E_WARNING);
#C conectar C consultar M modificar I insertar E eliminar
class CCMIE{  
	####################################################################################################################
	#DECLARACION DE VARIABLES																############################
	####################################################################################################################
	#variables privadas usadas en la conexion 
	private $host=DB_SERVIDOR; 
	private $usuario=DB_USUARIO; 
	private $contasena=DB_CONTRASENA; 
	private $basedato=DB_BASEDATO;
	private $charset = CODIFICACIONCARACTER;
	#variables usadas en funciones   
	#variable de conexion
	private $conn;
	#variables privadas
	private $query;
	private $error2 = array();#errores php
	#variables publicas
	public $sql;
	public $sql1;
	public $sql2;
	public $error = array();#errores mysql
	####################################################################################################################
	#FUNCIONES PRIVADAS																		############################
	####################################################################################################################
	private function gestorErrores(){
		//return false;
			$error = error_get_last();
			$numero=$error['type'];$mensaje=$error['message'];$nombrearchivo=$error['file'];$numlinea=$error['line'];
		if($numero>0){	
			$tipoerror = array (
				E_ERROR              => 'Error',
				E_WARNING            => 'Advertencia',
				E_PARSE              => 'Error de analisis',
				E_NOTICE             => 'Aviso',
				E_CORE_ERROR         => 'Nucleo de error',
				E_CORE_WARNING       => 'Nucleo de advertencia',
				E_COMPILE_ERROR      => 'Error de compilacion',
				E_COMPILE_WARNING    => 'Compilar  Advertencia',
				E_USER_ERROR         => 'Error de usuario',
				E_USER_WARNING       => 'Advertencia del usuario',
				E_USER_NOTICE        => 'Aviso de usuario',
				E_STRICT             => 'Aviso de tiempo de ejecucion',
				E_RECOVERABLE_ERROR  => 'Fatal Error capturable'
				);
				if($numero == 8 || $numero == 32)return false;			
				$err  = $numero.':Tipo:'.$tipoerror[$numero]."\n";
				$err .= 'Mensaje:'.$mensaje."\n";
				$err .= 'Archivo:'.$nombrearchivo."\n";
				$err .= 'Linea:'.$numlinea."\n";
				#para probar 
				//echo $err;
				$this->error2[] = $err;
				return true;
		}
		return false;
	}
	####################################################################################################################
	#FUNCIONES PUBLICAS																		############################
	####################################################################################################################
	#constructor de la clase   
	function __construct() {  
		$this->conn=mysql_pconnect($this->host,$this->usuario,$this->contasena) or 
	   	$this->error[] = 'Imposible conectar con '. $this->host."\n".mysql_error();
		if($this->error)exit;
		mysql_select_db($this->basedato,$this->conn) or $this->error[] = $this->basedato." no existe.\n".mysql_error();
		if($this->error)exit;
	} 	
	#funcion que hace una consulta de un query
	public function consulta($query,$retornar=0,$limite=0) {
		#verifico que el query no este vacio, si la consulta no esta vacia continuo		
		if(!empty($query)){
			#hago una copia interna del query; 			
			$this->query = $query;
			#verifico si se ennvia un numero de limite para la consulta, si es verdadero agrego el limite
			if($this->numero($limite)>0)$this->query .= ' LIMIT '.$limite;
			#agrego la codificacion utf8 para que me saque bien los acentos latinos como la ñ y tildes
			mysql_query("SET NAMES '".$this->charset."'");
			#rectifico que sea un entero la variable de retornar
			$retornar = (int)$retornar;
			#segun el numero de retornar hago la consulta en un caso
			switch($retornar){
				#caso 3 realizo la consulta sin guardar resultado, esto para consultas que no retornan datos como insert o delete				
				case 3 : mysql_query($this->query,$this->conn);break;
				#caso 5 y 6 para consultas anidadas
				case 5 :
				case 6 :$this->sql1 = mysql_query($this->query,$this->conn);break;
				#en este caso se retorna el query, continuo para validar errores
				case 7 : break;
				#caso 8 y 9 para consultas anidadas
				case 8 :
				case 9 :$this->sql2 = mysql_query($this->query,$this->conn);break;
				#caso 0,1,2,4 y mas de 9, consulta y guardo el resultado en $this->sql
				default : $this->sql = mysql_query($this->query,$this->conn);break;					
			}
			#compruevo que no hallan errores
			if(mysql_error()){
				# si hay un error lo capturo y retorno false
				$this->error[] = mysql_error();
				return false;
			}
			#la variable retornar no solo indica hacer la consulta sini retornar un valor especifico
			#hacemos el retorno segun el caso 
			switch($retornar){
				#retorno la consulta (objeto) sql
				case 1 : return $this->sql;break;
				#retorno una fila
				case 2 : return $this->verFila();break;
				#retorno el valor de la fila 0 columna 0
				case 4 : return $this->verResult();break;
				#retorno la consulta (objeto) sql1
				case 5 : return $this->sql1;break;
				#retorno una fila del objeto sql1
				case 6 : return $this->verFila($this->sql1);break;
				#retornar el query, la uso en la clase extendida o segun nececidad
				case 7 : return $this->query;break;
				#retorno la consulta (objeto) sql2
				case 8 : return $this->sql2;break;
				#retorno una fila del objeto sql2
				case 9 : return $this->verFila($this->sql2);break;
				#retorno el numero de filas de la consulta
				case 10: return $this->contarFilas();
				#retorno el id del ultimo registro autoincremental
				case 11: return $this->insert_id();
				#retorno las filas de la consulta en un arrray
				case 12: return $this->arrayfila();
				#si es una consulta, sin parametros retorno true
				default : return true;break;				
			}
		}return false;
	}	
	#esta funcion me devuelve un resultado de una consulta de una fila
	public function verFila($sql=0) {
		if($sql && $this->contarFilas($sql)>0){
			$resultado = mysql_fetch_row($sql);
		}elseif($this->contarFilas()>0){
			$resultado = mysql_fetch_row($this->sql);
		}else return false;
		if($this->gestorErrores()){
			return false;			
		}
		return $resultado;
	}
	#me imprime la consulta 
	public function verQuery(){
		return $this->query;
	}
	#contar las filas que tiene la consulta
	public function contarFilas($sql=0){
		if(!empty($sql)){
			$resultado = mysql_num_rows($sql);
		}elseif(!empty($this->sql)){
			$resultado = mysql_num_rows($this->sql);
		}else return false;
		if($this->gestorErrores()){
			return false;
		}
		return $resultado;
	} 
	#mostra el nombre de la columna
	public function nomColumna($num=0,$sql=0){
		if(!empty($sql)){
			$resultado = mysql_field_name($sql,$num);
		}elseif(!empty($this->sql)){
			$resultado = mysql_field_name($this->sql,$num);
		}
		if($this->gestorErrores()){
			return false;
		}
		return $resultado;		
	} 
	#mostra el resultado de una fila x en la columna y
	public function verResult($x=0,$y=0,$sql=0){
		if($sql && $this->contarFilas($sql)>0){
			$resultado = mysql_result($sql,$x,$y);
		}elseif($this->contarFilas()>0){
			$resultado = mysql_result($this->sql,$x,$y);
		}else return false;
		if($this->gestorErrores()){
			return false;
		}
		return $resultado;
	} 
	#contar las columnas quw tiene la consulta
	public function contarColumnas($sql=0){
		$resultado = ($sql)?mysql_num_fields($sql):mysql_num_fields($this->sql);
		if($this->gestorErrores()){
			return false;
		}
		return $resultado;
	}
	#recupera el id del ultimo registro insertado o modificado
//	function (){
//	}
	#esta funcion me filtra caracteres que puedan violar la seguridad de la BD
	#automatizar mas esta funcion......
	public function addFiltro($cadena) {
		$cadena = trim($cadena);
		if(empty($cadena))return '';
		$cadena = htmlentities($cadena, ENT_QUOTES, "UTF-8");
		if(get_magic_quotes_gpc()) {
			$cadena = stripslashes($cadena);
		}
		return mysql_real_escape_string($cadena);
	}
	#verificar que la cadena o variable sea un numero sin importar su longitud
	public function numero($cadena=0){
		return (is_numeric($cadena))?$cadena:false;
	}
	#recuperar id de registro autoincremental
	public function insert_id(){
		return $this->consulta('SELECT last_insert_id()',4);
	}
	#retornar las filas de una consulta en un array
	public function arrayfila($sql){
		if($sql && $this->contarFilas($sql)>0){
			while($fila = $this->verFila($sql)){
				$resultado[] = $fila;
			}
		}elseif($this->contarFilas()>0){
			while($fila = $this->verFila()){
				$resultado[] = $fila;
			}
		}else return false;
		if($this->gestorErrores()){
			return false;
		}
		return $resultado;
	}
	#ver los datos que se usan en la conexion
	#funcion para desarrollo
	//public function datosConexion(){
	//	return "Usuario:$this->usuario, Contraseña:$this->contasena, Base de Datos:$this->basedato, Servidor:$this->host.";
	//}
	#cerrara conexion
	public function cerrarConexion(){
		mysql_close();
	}
	#al terminar de ejecutarse la pagina donde se crea un objeto de esta clase se destruye este objeto.
	public function __destruct(){
		if($this->error||$this->error2){
			echo'<script>alert("';
			if($this->error){
				echo "Errores MySql\\n";
				foreach($this->error as $ind=>$err)
					echo ($ind+1),"\\n ",$err,"\\n-----------------------\\n";
			}
			if($this->error2){
				echo "Errores PHP\\n";
				foreach($this->error2 as $ind=>$err)
					echo ($ind+1),"\\n ",$err,"\\n-----------------------\\n";
			}
			echo'");</script>';
		}
		mysql_free_result($this->sql);
   		unset($this);
		$this->cerrarConexion();
	}  
}  
?>
