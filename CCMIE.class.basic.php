<?php
/**
 ********************************************************************************<br>
 *	Nombre de
 *	Archivo		:		CCMIE.class.basic.php
 *
 *	Autor		:		Fabio Grandas <gffabio@hotmail.com>
 *
 *	Version		:		1.0.6
 *
 *	URL			:		http://www.gffabio.com/
 *
 *	Documentacion:		http://www.gffabio.com/CCMIE
 *
 ********************************************************************************<br>
 *
 *CCMIE - consultas en PHP/MySQL<br>
 *Copyright (C) 2012  Fabio Grandas
 */
include('../config/DB.php');
/**
 *	C conectar C consultar M modificar I insertar E eliminar
 *	Clase CCMIE - Consultas  con PHP/MySQL<br>
 * <b>Descripcion:</b>C conectar, C consultar, M modificar, I insertar, E eliminar
 * CCMIE es una clase basada en PHP/MySQL, que recibe
 * una serie de datos y los procesa para así lograr unas mejores consultas.
 *
 * @package CCMIE
 * @author Fabio Grandas A
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version v1.0.6
 * @copyright 2012
 * @access public
 * @link http://gffabio.com/CCMIE
 * @link http://www.gffabio.com/CCMIE  www.gffabio.com
 */
class CCMIE{
  /**
   * En esta variable se ingresa el servidor de la variable gloval <b>DB_SERVIDOR</b>
   * <code>
   * $host=DB_SERVIDOR='localhost';
   * </code>
   * @var string
   * @access private
   */
	private $host=DB_SERVIDOR;
  /**
   * En esta variable se ingresa el usuario de la variable gloval <b>DB_USUARIO</b>
   * <code>
   * $usuario=DB_USUARIO='usuario';
   * </code>
   * @var string
   * @access private
   */
	private $usuario=DB_USUARIO;
  /**
   * En esta variable se ingresa la contraseña de la variable global <b>DB_CONTRASENA</b>
   * <code>
   * $usuario=DB_CONTRASENA='usuario';
   * </code>
   * @var string
   * @access private
   */
	private $contasena=DB_CONTRASENA;
  /**
   * En esta variable se ingresar la base de datos a utilizar de la variable
   * global <b>DB_BASEDATO</b>
   * <code>
   * $usuario=DB_BASEDATO='usuario';
   * </code>
   * @var string
   * @access private
   */
	private $basedato=DB_BASEDATO;
  /**
   * el tipo de codificacion de caracteres
   * <code>
   * $charset = 'utf8';
   * </code>
   * @var string
   * @access private
   */
	private $charset = CHARACTERSET;
  /**
   * En esta variable se guardara la conexion
   * <code>
   * $conn = mysql_pconnect($this->host,$this->usuario,$this->contasena);
   * </code>
   * @access private
   */
	private $conn;
  /**
   * me dice si mostrar los errores generados
   * por default esta en verdadero
   * @access private
  */
 private $vererrores = true;
  /**
   * se guardara el query utilizado en la consulta
   * <code>
   * $query = 'SELECT campo FROM tabla';
   * </code>
   * @access private
   */
	private $query;
  /**
   * se almacenaran lo errores generados por mysql
   * <code>
   * $mysqlerror[] = mysql_error();
   * </code>
   * @access private
   */
	private $mysqlerror = array();
  /**
   * se almacenaran lo errores generados por php
   * <code>
   * $phperror[] = E_WARNING => 'Advertencia';
   * </code>
   * @access private
   */
	private $phperror = array();
   /**
    * Errores publicos
    * @access public
    */
 public $errores = array();
  /**
   * se guarda el resultado de una consulta
   * <code>
   * $sql = mysql_query($this->query,$this->conn);
   * </code>
   * @access public
   */
	public $sql;
	/**
	 * CCMIE::erroresphp()
  * si se produce un error en php, error_get_last() me lo guarda en
  * $this->$phperror[] y retorna true para detener la ejecucion en curso
  * <code>$this->$phperror[] = $err;</code>
  * @access private
  * @return boolean
	 */
	private function erroresphp(){
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
				E_RECOVERABLE_ERROR  => 'Fatal Error capturable',
				E_DEPRECATED				 => 'avisos sobre código que no funcionará en futuras versiones.',
				E_USER_DEPRECATED    => 'Mensajes de advertencia generados por el usuario. Son como un E_DEPRECATED, excepto que es generado por código de PHP mediante el uso de la función de PHP trigger_error().',
				E_ALL                => 'Todos los errores y advertencias soportados, excepto del nivel E_STRICT antes de PHP 5.4.0.'
				);
				if($numero == 8 || $numero == 32 || $numero > 5000 || $numero = 2048)return false;
				$err  = $numero.':Tipo:'.$tipoerror[$numero]."\n";
				$err .= 'Mensaje:'.$mensaje."\n";
				$err .= 'Archivo:'.$nombrearchivo."\n";
				$err .= 'Linea:'.$numlinea."\n";
				//para probar
				//echo $err;
				$this->phperror[] = $err;
				return true;
		}
		return false;
	}
  /**
   * Constructor de la clase
   * <code>
   * $this->conn=mysql_pconnect($this->host,$this->usuario,$this->contasena)
   * mysql_select_db($this->basedato,$this->conn)
   * mysql_query("SET NAMES '".$this->charset."'");
   *
   * </code>
   */
	function __construct($vererrores=null) {
		$this->conn=mysql_pconnect($this->host,$this->usuario,$this->contasena) or
	  die('Imposible conectar con '. $this->host);
		mysql_select_db($this->basedato,$this->conn) or
  die('La base de datos "'.$this->basedato.'" no existe.');
		//agrego la codificacion utf8 para que me saque bien los acentos latinos como la ñ y tildes
		mysql_query("SET NAMES '".$this->charset."'");
  if($vererrores==1)$this->vererrores = false;
	}
  /**
   * me realiza una consulta y me retorna ciertos valores u objetos segun
   * el parametro <b>$retornar</b>
   * - la consulta <code>consulta($query,$retornar=0,$limite=0)</code>
   * - el parametro <b>$query</b><code>$this->query=$query</code>
   * - el parametro <b>$retornar</b>; si es una consulta sin parametros retorno <b>true</b>
   * <code>
   * 0  return true;
   * 1  return $this->sql; un objeto recordset $this->sql
   * 2  return $this->verFila(); una fila
   * 3  return true; que se realizo la consulta
   * 4  return $this->verResult(); retorno el valor de la fila 0 columna 0
   * 5  return $this->contarColumnas(); retorna el numero de columnas que tiene objeto sql
   * 6  return $this->nomColumna(); el nombre de la primera columna
   * 7  return $this->query; el query
   * 8  return eliminado
   * 9  return eliminado
   * 10 return $this->contarFilas(); retorna el numero de filas del objeto recordset $this->sql1
   * 11 return $this->insert_id(); el ultimo numero ingresado del campo autoincrementar
   * 12 return $this->arrayfila(); retorna el objeto recordset $this->sql en un array
   * $retornar > 12 return true;
   * </code>
   * - el parametro <b>$limite</b><code>
   * Ej:$limite = 5
   * consulta('select * from tabla',7,5) el resultado sera:select * from tabla limit = 5
   * </code>
   * @param string $query contiene el query a ejecutar
   * @param int $retornar me retorna segun caso
   * @param int $limite El numero de filas a consultar
   * @return mixed
   */
	public function consulta($query,$retornar=0,$limite=0) {
		//verifico que el query no este vacio, si la consulta esta vacia retorno falso
		if(empty($query))return false;
		//hago una copia interna del query;
		$this->query = $query;
		//verifico si se envia un numero de limite para la consulta, si es verdadero agrego el limite
		if((int)$limite>0)$this->query .= ' LIMIT '.$limite;
		//rectifico que sea un entero la variable de retornar
		$retornar = (int)$retornar;
		//segun el numero de retornar hago la consulta en un caso
		switch($retornar){
			//caso 3 realizo la consulta sin guardar resultado, esto para consultas que no retornan datos como insert o delete
			case 3 : mysql_query($this->query,$this->conn);break;
			//en este caso se retorna el query, continuo para validar errores
			case 7 : break;
			//caso 0,1,2,4,5,6,8 y mas de 9, consulta y guardo el resultado en $this->sql
			default : $this->sql = mysql_query($this->query,$this->conn);break;
		}
		//compruebo que no se generaron errores
		if(mysql_error()){
			// si hay un error lo capturo y retorno false
			$this->mysqlerror[] = mysql_error();
			return false;
		}
		//la variable retornar no solo indica hacer la consulta sino retornar un valor especifico
		//hacemos el retorno segun el caso
		switch($retornar){
			//retorno la consulta (objeto) sql
			case 1 : return $this->sql;break;
			//retorno una fila
			case 2 : return $this->verFila();break;
			//retorno el valor de la fila 0 columna 0
			case 4 : return $this->verResult();break;
			//retorno el numero de columnas que tiene objeto sql
			case 5 : return $this->contarColumnas();break;
			//retorno el nombre de la primera columna
			case 6 : return $this->nomColumna();break;
			//retornar el query, la uso en la clase extendida o segun nececidad
			case 7 : return $this->query;break;

			//case 8 : return $this->sql;break;
			//case 9 : return $this->verFila($this->sql);break;

			//retorno el numero de filas de la consulta
			case 10: return $this->contarFilas();break;
			//retorno el id del ultimo registro autoincremental
			case 11: return $this->insert_id();break;
			//retorno las filas de la consulta en un arrray
			case 12: return $this->arrayfila();break;
			//si es una consulta, sin parametros retorno true
			default : return true;break;
		}
	}
  /**
   * Esta funcion me devuelve una fila de un objeto recordset en un array
   * <code>
   * $resultado = mysql_fetch_row($sql);
   * $resultado = mysql_fetch_row($this->sql);
   * </code>
   * @param recordset $sql si no se envia este parametro, se toma <b>$this->sql</b>
   * @return array $resultado devuelve la fila en un array
   * @access public
   */
	public function verFila($sql=0) {
		if($sql && $this->contarFilas($sql)>0){
			$resultado = mysql_fetch_row($sql);
		}elseif($this->contarFilas()>0){
			$resultado = mysql_fetch_row($this->sql);
		}else return false;
		if($this->erroresphp()){
			return false;
		}
		return $resultado;
	}
  /**
   * me retorna la consulta
   * <code>return $this->query;</code>
   * @return string <b>($resultado)</b> el query que este almacenado en ese momento
   * @access public
   */
	public function verQuery(){
		return $this->query;
	}
  /**
   * Contar las filas que tiene objeto recordset
   * <code>
   * $resultado = mysql_num_rows($sql);
   * $resultado = mysql_num_rows($this->sql);
   * </code>
   * @param recordset $sql si no se envia este parametro, se toma <b>$this->sql</b>
   * @return int <b>($resultado)</b> el numero de filas del objeto recordset
   * @access public
   */
	public function contarFilas($sql=0){
		if(!empty($sql)){
			$resultado = mysql_num_rows($sql);
		}elseif(!empty($this->sql)){
			$resultado = mysql_num_rows($this->sql);
		}else return false;
		if($this->erroresphp()){
			return false;
		}
		return $resultado;
	}
  /**
   * muestra el nombre de la columna
   * <code>
   * $resultado = mysql_field_name($sql,$num);
   * $resultado = mysql_field_name($this->sql,$num);
   * </code>
   * @param int $num si no se envia este parametro, se toma <b>0</b>
   * @param recordset $sql si no se envia este parametro, se toma <b>$this->sql</b>
   * @return string <b>($resultado)</b> el nombre de la columna
   * @access public
   */
	public function nomColumna($num=0,$sql=0){
		if($sql){
			$resultado = mysql_field_name($sql,$num);
		}elseif($this->sql){
			$resultado = mysql_field_name($this->sql,$num);
		}
		if($this->erroresphp()){
			return false;
		}
		return $resultado;
	}
  /**
   *mostrar el resultado de una fila x en la columna y
   * <code>
   * $resultado = mysql_result($sql,$x,$y);
   * $resultado = mysql_result($this->sql,$x,$y);
   * </code>
   * @param int $x la posicion en la fila, si no se envia este parametro, se toma <b>0</b>
   * @param int $y la posicion en la columna, si no se envia este parametro, se toma <b>0</b>
   * @param recordset $sql si no se envia este parametro, se toma <b>$this->sql</b>
   * @return mixed <b>($resultado)</b>
   * @access public
   */
	public function verResult($x=0,$y=0,$sql=0){
		if($sql && $this->contarFilas($sql)>0){
			$resultado = mysql_result($sql,$x,$y);
		}elseif($this->contarFilas()>0){
			$resultado = mysql_result($this->sql,$x,$y);
		}else return false;
		if($this->erroresphp()){
			return false;
		}
		return $resultado;
	}
  /**
   * Contar las columnas que tiene objeto recordset
   * <code>
   * $resultado = mysql_num_fields($sql);
   * $resultado = mysql_num_fields($this->sql);
   * </code>
   * @param recordset $sql si no se envia este parametro, se toma <b>$this->sql</b>
   * @return int <b>($resultado)</b> el numero de filas del objeto recordset
   * @access public
   */
	public function contarColumnas($sql=0){
		$resultado = ($sql)?mysql_num_fields($sql):mysql_num_fields($this->sql);
		if($this->erroresphp()){
			return false;
		}
		return $resultado;
	}
  /**
   * Esta funcion me filtra caracteres que puedan violar la seguridad de la BD<br>
   * automatizar mas esta funcion......
   * <code>
   * $cadena = trim($cadena);
   * $cadena = htmlentities($cadena, ENT_QUOTES, "UTF-8");
   * if(get_magic_quotes_gpc()) {
   * $cadena = stripslashes($cadena);
   * }
   * return mysql_real_escape_string($cadena);
   * </code>
   * @param strin $cadena la cadena a escapar
   * @return string la cadena escapada
   * @access public
   */
	public function addFiltro($cadena) {//madificada 10 agosto 2012
		$cadena = trim($cadena);
		if(empty($cadena))return '';
		//return $cadena;
  $cadena = strtr($cadena,array('"'=>'&#34;',"'"=>'&#39;'));
		$cadena = htmlentities($cadena, ENT_QUOTES, "UTF-8");
		if(get_magic_quotes_gpc()) {
			$cadena = stripslashes($cadena);
		}
		return mysql_real_escape_string($cadena);
	}
 /**
  * funcion para hacer filtrado de caracteres
  * pendiente actualizar
 */

 public function quitarcaracteres($mensaje){
  $nopermitidos = array("'",'\\','<','>',"\"","#","@","%","&");
  $mensaje = str_replace($nopermitidos,"", $mensaje);
  return $mensaje;
 }
  /**
   * Verificar que la cadena o variable sea un numero sin importar su longitud
   * <code>
   * return (is_numeric($cadena))?$cadena:false;
   * </code>
   *
   * @param string $cadena la cadena a validar
   * @return int el numero encontrado
   * @access public
   */
	public function numero($cadena=0){
		return (is_numeric($cadena))?$cadena:false;
	}
  /**
   *recupera el id del ultimo registro insertado o modificado
   * <code>
   * return $this->consulta('SELECT last_insert_id()',4);
   * </code>
   * @return int el numero encontrado
   * @access public
   */
	public function insert_id(){
		return $this->consulta('SELECT last_insert_id()',4);
	}
	// una consulta en un array
  /**
   * Retornar las filas de un objeto recordset en un array
   * <code>
   * while($fila = $this->verFila($sql)){
   *  $resultado[] = $fila;
   * }
   * while($fila = $this->verFila()){
   *  $resultado[] = $fila;
   * }
   * </code>
   * @param recordset $sql si no se envia este parametro, se toma <b>$this->sql</b>
   * @return array <b>($resultado)</b> las filas en un array
   * @access public
   */
	public function arrayfila($sql=0){
    $resultado = array();
		if($sql && $this->contarFilas($sql)>0){
			while($fila = $this->verFila($sql)){
				$resultado[] = $fila;
			}
		}elseif($this->contarFilas()>0){
			while($fila = $this->verFila()){
				$resultado[] = $fila;
			}
		}else return false;
		if($this->erroresphp()){
			return false;
		}
		return $resultado;
	}
  /**
   * ver los datos que se usan en la conexion<br>
   * <b>funcion para desarrollo</b> por lo que esta en comentario
   *
   * @return string
   * @access public
   */
  public function datosConexion(){
    return /*"Usuario:$this->usuario, Contraseña:$this->contasena, Base de Datos:$this->basedato, Servidor:$this->host."*/;
  }
  /**
   * cerrar la conexion
   * <code>mysql_close();</code>
   * @access public
   */
	public function cerrarConexion(){
		if($this->conn)mysql_close($this->conn);
	}
  /**
   * realiza unas tareas del objeto creado<br>
   * - si hay errores php o mysql los imprime en pantalla en un alert() de javascrip
   * - cierra la conexion
   * - se destruye este objeto.
   * @access public
   */
	public function __destruct(){
		if($this->vererrores && ($this->mysqlerror||$this->phperror)){
			$msg = '<script>alert("';
			if($this->mysqlerror){
				$msg .= "Errores MySql\\n";
				foreach($this->mysqlerror as $ind=>$err)
					$msg .= ($ind+1)."\\n ".$err."\\n-----------------------\\n";
			}
			if($this->phperror){
				$msg .= "Errores PHP\\n";
				foreach($this->phperror as $ind=>$err)
					$msg .= ($ind+1)."\\n ".$err."\\n-----------------------\\n";
			}
			if($this->errores){
				$msg .= "Errores\\n";
				foreach($this->errores as $ind=>$err)
					$msg .= ($ind+1)."\\n ".$err."\\n-----------------------\\n";
			}
			$msg .='");</script>';
   echo $msg;
		}
		$this->cerrarConexion();
 	unset($this);
	}
}
?>
