<?php
################################################################################
#	Este script permanece libre mientras estas lineas permanezcan intactas
################################################################################
#	Nombre de
#	Archivo		:		CCMIE.class.php
#
#	Autor		:		Fabio Grandas <gffabio@hotmail.com>
#
#	Version		:		1.0.3
#
#
#	URL			:		http://www.gffabio.webcindario.com/
#
#	Documentacion:		http://www.gffabio.webcindario.com/document/CCMIE
#
################################################################################
#
#CCMIEc - consultas en PHP/MySQL
#Copyright (C) 2012  Fabio Grandas
#
#This program is free software: you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation, either version 3 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#
#You should have received a copy of the GNU General Public License
#along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#Este programa es software libre: usted puede redistribuirlo y / o modificar
#Bajo los términos de la Licencia Pública General de GNU según lo publicado por
#La Free Software Foundation, ya sea la versión 3 de la Licencia, o
#(A su elección) cualquier versión posterior.
#
#Este programa se distribuye con la esperanza de que sea útil,
#Pero SIN NINGUNA GARANTÍA, incluso sin la garantía implícita de
#O IDONEIDAD PARA UN PROPÓSITO PARTICULAR. ver el
#GNU General Public License para más detalles.
#
#Deberías haber recibido una copia de la Licencia Pública General de GNU
#Junto con este programa. Si no es así, consulte <http://www.gnu.org/licenses/>.
################################################################################
/**
 *	Clase CCMIEC - Consultas  con PHP/MySQL<br>
 *  <b>Descripcion:</b>C conectar, C consultar, M modificar, I insertar, E eliminar C contruir
 *  CCMIEC es una clase basada en PHP/MySQL, que recibe
 *  una serie de datos y los procesa para así lograr unas mejores consultas.
 *
 *  @package CCMIEC
 *	@author Fabio Grandas A
 *	@license http://opensource.org/licenses/gpl-license.php GNU Public License
 *	@version v.1.0.3
 *  @copyright 2012
 *  @access public
 *	@todos Revision: 04 Febrero 2012
 *  @link http://www.gffabio.webcindario.com/document/CCMIE  www.gffabio.com/CCMIE
 */
/**
 * se requiere este archivo que contiene la clse base
 */

require_once('CCMIE.class.basic.php');
/**
 * C conectar C consultar M modificar I insertar E eliminar C contruir
 */
class CCMIEC extends CCMIE{
  /**
   * para guardar campos like
   * <code>$campolike[] = "campo like '%valor%'";</code>
   * @var array
   * @access private
   */
	private $campolike = array();
  /**
   * para saber si ya se guardo un WHERE e ingresar un AND
   * <code>$where = 1; ya hay where</code>
   * @var int
   * @access private
   */
	private $were=0;
  /**
   * para guardar el numero limite de filas a consultar
   * <code>$limit = " LIMIT inicio,cantidad";</code>
   * @var int
   * @access private
   */
	private $limit;
  /**
   * para guardar en nombre de la tablas
   * <code>$tablas = "tabla_1,tabla_2,tabla_n";</code>
   * @var string
   * @access private
   */
	private $tablas;
  /**
   * para guardar en nombre de los campos
   * <code>$campos = "campo_1,campo_2,campo_n";</code>
   * @var string
   * @access private
   */
	private $campos;
  /**
   * para guardar en nombre de un campo = valor
   * <code>$campovalor[] = "campo = valor";</code>
   * @var array
   * @access private
   */
	private $campovalor = array();
  /**
   * para guardar los valores a guardar en una consulta
   * <code>$valores[] = "valor_1,valor_2,valor_n";</code>
   * @var array
   * @access private
   */
	private $valores = array();
  /**
   * para guardar una condicion campo = valor
   * <code>$condicion[] = "campo = valor";</code>
   * @var array
   * @access private
   */
	private $condicion = array();
  /**
   * para guardar una union inner join campo = valor
   * <code>$inerjoin = " INNER JOIN tabla2 ON campo2 = campo1 ";</code>
   * @var array
   * @access private
   */
	private $inerjoin = array();
  /**
   * para guardar el campo = valor a insertar
   * <code>$campovalorinser[campo] = "valor";</code>
   * @var array
   * @access private
   */
	private $campovalorinser = array();
 /**
  * Valores ingresados
  * @var string
  * @access public
 */
 public $ingresados = '';
 private $ingresadostem = '';
  /**
   * Constructor de la clase
   * <code>
   * parent::__construct();
   * </code>
   * @access public
   */
	public function __construct() {
    parent::__construct();
	}
  /**
   * Funcion privada que me crea una query de consulta de actualizacion de datos
   * <code>
   * 'UPDATE tabla SET campo = valor WHERE campo = valor';
   * </code>
   * @return string
   * @access private
   */
	private function actualizar(){
		if(empty($this->campovalor))return 'No se han agregado campos ni valores';
		if(empty($this->tablas))return 'No se ha agregado ninguna tabla';
		$this->query = 'UPDATE ';
		$this->query.= $this->tablas;
		$this->query.= ' SET ';
		foreach($this->campovalor as $campo){
			$this->query.=$campo.', ';
		}
  $this->ingresados = substr($this->ingresadostem,0,-2);
  unset($this->ingresadostem);
		unset($this->campovalor);
  $this->query = substr($this->query,0,-2);
		$this->query.= ' WHERE ';
		foreach($this->condicion as $condi){
			$this->query.= $condi.' AND ';
		}
		unset($this->condicion);
  $this->query = substr($this->query,0,-4);
		return $this->query;
	}
  /**
   * Funcion que me crea la consulta de insercion<br>
   * <code>
   * 'INSERT INTO tabla VALUES(valor_1,valor_2,valor_n)';
   * 'INSERT INTO tabla campo_1,campo_2,campo_n VALUES(valor_1,valor_2,valor_n)';
   * </code>
   * @param int $tipo
   * @return string
   * @access private
   */
	private function insertar($tipo){
		if(empty($this->tablas))return 'No se ha agregado ninguna tabla';
		$this->query = 'INSERT INTO '.$this->tablas;
		if($tipo==1){
			if(empty($this->campovalorinser))return 'No se han agregado campos o valores';
				$campo='';$valor='';
				foreach($this->campovalorinser as $c=>$v){
					$campo.=$c.',';$valor.="'".$v."', ";
     $this->ingresadostem .= $v.', ';
				}
				unset($this->campovalorinser);
				$this->query.= ' ('.substr($campo,0,-1).')';
				$this->query.= ' VALUES (';
				$this->query.= substr($valor,0,-2);
				$this->query.= ')';
		}else{
			if(empty($this->valores))return 'No se han agregado campos';
				$this->query.= ' VALUES (';
				foreach($this->valores as $valor){
					$this->query.="'".$valor."', ";
     $this->ingresadostem .= $v.', ';
				}
				unset($this->valores);
				$this->query = substr($this->query,0,-2);
				$this->query.= ')';
		}
  $this->ingresados = substr($this->ingresadostem,0,-2);
  unset($this->ingresadostem);
		return $this->query;
	}
  /**
   * Funcion privada que contruye el query de consulta
   * <code>
   * 'SELECT campo_1,campo_2,campo_n FROM tabla WHERE campo = valor';
   * </code>
   * @return string
   * @access private
   */
	private function consultar(){
		#si no se agrga una tabla de consulta retorno mensage
		if(empty($this->tablas))return 'No se ha agregado ninguna tabla';
        $this->query = 'SELECT ';
		if($this->campos){
			$this->query.= $this->campos;
			unset($this->campos);
		}else {
			$this->query.='*';
		}
		$this->query.= ' FROM ';
		$this->query.= $this->tablas;
		unset($this->tablas);
		if($this->inerjoin){
			$this->query .= $this->inerjoin;
			unset($this->inerjoin);
		}
		#si se agrego esta condicion, agrego al query una condicion like
		if($this->campolike){
			$this->query.= ($this->were==0)?' WHERE ':' AND ';
			foreach($this->campolike as $dato)$this->query.=$dato.' AND ';
			$this->query = substr($this->query,0,-4);
			$this->were=1;
			unset($this->campolike);
		}
		#si hay una condicion la agrego al query
		if($this->condicion){
			#si ya se agrego una condicion cambio el where por un and
			$this->query.= ($this->were==0)?' WHERE ':' AND ';
			#agregando condiciones
			foreach($this->condicion as $condi)$this->query.=$condi.' AND ';
			unset($this->condicion);
			#quito los ultimos 4 carateres de la cadena query
			$this->query = substr($this->query,0,-4);
		}
		#si se agrega un orden lo adiciona al query
		if($this->ordeby){
			$this->query.= $this->ordeby;
		}
		#si hay un limite lo agrego al query
		if($this->limit){
			$this->query.= $this->limit;
		}
		return $this->query;
	}
  /**
   * Funcion para crear la consulta de eliminar
   * <code>
   * 'DELETE FROM tabla WHERE campo = valor';
   * </code>
   * @return string
   * @access private
   */
	private function eliminar(){
		if($this->tablas)return 'No se ha agregado ninguna tabla';
		$this->query = 'DELETE FROM ';
		$this->query .= $this->tablas;
		if($this->condicion){
			$this->query.= ' WHERE ';
			foreach($this->condicion as $condi){
				$this->query.= $condi.' AND ';
			}
			unset($this->condicion);
			$this->query = substr($this->query,0,-4);
		}
		return $this->query;
	}
  /**
   * Funcion para vaciar una tabla
   * <code>
   * 'TRUNCATE TABLE tabla';
   * </code>
   * @return boolean
   * @access private
   */
	private function truncate(){
		if($this->tablas){
			return 'TRUNCATE TABLE '.$this->tablas;
		}
		return false;
	}
  /**
   * Agregar el monbre del campo y el valor, campo = valor
   * <code>
   * $this->campovalor[] = $this->addFiltro($campo)." = '".$this->addFiltro($valor)."'";
   * </code>
   * @param string $campo
   * @param string $valor
   * @access public
   */
	public function addCamValMod($campo,$valor){
		 if($campo){
			 $this->campovalor[] = $this->addFiltro($campo)." = '".$this->addFiltro($valor)."'";
    $this->ingresadostem .= $this->addFiltro($valor).', ';
		 }
 }
  /**
   * Agregar campos, SELECT campo1,campo2,campo3 ó INSERT campo1,campo2,campo3
   * <code>
   * $this->campos = $this->addFiltro($campos);
   * </code>
   * @param string $campos
   * @access public
   */
	public function addCampos($campos){
		 if($campos){
			 $this->campos = $this->addFiltro($campos);
		 }
 }
  /**
   * Agregar el nombre de la tabla, from tabla1,tabla2,tabla3
   * <code>
   * $this->tablas = $this->addFiltro($tablas);
   * </code>
   * @param string $tablas
   * @access public
   */
	public function addTabla($tablas) {
		if($tablas){
				$this->tablas = $this->addFiltro($tablas);
		}
	}
  /**
   * por  compativilidad vercion anterior
   * <code>
   * $this->tablas = $this->addFiltro($tablas);
   * </code>
   * @param type $tablas
   * @access public
   */
	public function addTablas($tablas) {
		if($tablas){
				$this->tablas = $this->addFiltro($tablas);
		}
	}
  /**
   * Agregar condicion where campo = valor
   * <code>
   * $this->condicion[]= $this->addFiltro($campo)." = '".$this->addFiltro($valor)."' ";
   * </code>
   * @param string $campo
   * @param string $valor
   * @access public
   */
	public function addCondicion($campo,$valor){
		if($campo){
			$this->condicion[]= $this->addFiltro($campo)." = '".$this->addFiltro($valor)."' ";
		}
	}
  /**
   * Agregar campos y valores como para una insercion con campos especificos,
   * INSERT tabla (campo1,campo2...) VALUES(valores1,valores2...) o de la forma
   * INSERT tabla VALUES(valores[0],valores[1]...) cuando son todos los campos de la tabla.
   * <code>
   * $this->campovalorinser[$this->addFiltro($campo)] = $this->addFiltro($valor);
   * $this->valores[] = $this->addFiltro($valor);
   * </code>
   * @param string $valor
   * @param string $campo
   * @access public
   */
	public function addCamValInsert($valor,$campo=''){
		if($campo){
		$this->campovalorinser[$this->addFiltro($campo)] = $this->addFiltro($valor);
		}else{
		$this->valores[] = $this->addFiltro($valor);
		}
	}
  /**
   * Agregar una condicion like, campo like valor
   * <code>
   * $this->campolike[] = $this->addFiltro($campo)." LIKE '%".$this->addFiltro($valor)."%' ";
   * </code>
   * @param string $valor
   * @param string $campo
   * @access public
   */
	public function addLike($campo,$valor){
		if($campo && $valor){
			$this->campolike[] = $this->addFiltro($campo)." LIKE '%".$this->addFiltro($valor)."%' ";
		}
	}
  /**
   * Agregar una condicion de ordenar, ORDEN BY campo DESC por defaul
   * <code>
   * $campo =  ' ORDER BY '.$this->addFiltro($campo);
   * $this->ordeby = ($desc==1)?$campo.' ASC':$campo.' DESC';
   * </code>
   * @param int $desc
   * @param string $campo
   * @access public
   */
	public function addOrdenar($campo,$desc=0){
		if($campo){#solo se agrega uno
			$campo =  ' ORDER BY '.$this->addFiltro($campo);
			$this->ordeby = ($desc==1)?$campo.' ASC':$campo.' DESC';
		}
	}
  /**
   * Agregar un limite a la consulta
   * <code>
   * $this->limit = ' LIMIT '.(int)$inicio.','.(int)$cantidad;
   * </code>
   * @param int $cantidad
   * @param int $inicio
   * @access public
   */
	public function addLimite($cantidad,$inicio=0){
		$this->limit = ' LIMIT '.(int)$inicio.','.(int)$cantidad;
	}
  /**
   * Agrega combinaciones INER JOIN
   * <code>
   * $this->inerjoin .= ' INNER JOIN '.$this->addFiltro($tabla2).' ON '.$this->addFiltro($campo2).' = '.$this->addFiltro($campo1);
   * </code>
   * @param string $tabla2
   * @param string $campo2
   * @param string $campo1
   * @access public
   */
	public function addInerJoin($tabla2,$campo2,$campo1){
	 	if($campo1 && $campo2 && $tabla2){#ej:tabla1 iner join tabla2 on campo2=campo1
			$this->inerjoin .= ' INNER JOIN '.$this->addFiltro($tabla2).' ON '.$this->addFiltro($campo2).' = '.$this->addFiltro($campo1);
		}
	}
  /**
   * Esta funcion ejecuta una consulta de:C ó I ó M ó E ó T<br>
   * Consultar, Insertar, Modificar, Eliminar, Truncate
   * <code>
   * 'I' $this->consulta($this->insertar($tipo),$retornar);
   * 'C' $this->consulta($this->consultar(),$retornar);
   * 'M' $this->consulta($this->actualizar(),$retornar);
   * 'E' $this->consulta($this->eliminar(),$retornar);
   * 'T' $this->consulta($this->truncate(),$retornar);
   * </code>
   * @param string $CIMET
   * @param int $retornar
   * @param int $tipo
   * @return mixed
   * @access public
   */
	public function ejecutar($CIMET,$retornar=0,$tipo=0){
		$CIME = strtoupper(substr($CIMET,0,1));
		switch($CIME){#realiza una consulta segun el parametro indicado de una letra
			case 'I':
			return $this->consulta($this->insertar($tipo),$retornar);
			break;
			case 'C':#consulta normal
			return $this->consulta($this->consultar(),$retornar);
			break;
			case 'M':#actualizar
			return $this->consulta($this->actualizar(),$retornar);
			break;
			case 'E':#eliminar
			return $this->consulta($this->eliminar(),$retornar);
			break;
			case 'T':#vaciar tabla, si tien autoincremento lo deja en 0
			return $this->consulta($this->truncate(),$retornar);
			break;
			default:
			echo 'consulta invalida';
			break;
		}
	}
  /**
   * Imprimir los datos de la consulta en una tabla
   * @param int $borde
   * @param int $padding
   * @param int $spacing
   * @param int $ths
   * @param string $clase
   * @param int $campos
   * @access public
   */
	public function impTabla($borde=0,$padding=0,$spacing=0,$ths=0,$clase=0,$campos=0){
		if($this->sql){#si la consulta tiene datos creo la tabla
			$j = ($campos)?$campos:$this->contarColumnas();
			$tabla = '<table';
			if($borde)$tabla .= ' border='.$borde;
			if($padding)$tabla .= ' cellpadding='.$padding;
			if($spacing)$tabla .= ' cellspacing='.$spacing;
			if($clase)$tabla .= ' class='.$clase;
			$tabla .= '>';
			if($ths == 1){
				$tabla .= '<tr>';#imprimo los nombres de las columnas
				for ($i=0; $i < $j; $i++){
					$tabla .= '<th>'.$this->nomColumna($i).'</th>';
				}
				$tabla .= '</tr>';
			}elseif($ths == 2){
				$tabla .= '<tr>';#imprimo los nombres de las columnas
				for ($i=0; $i < $j; $i++){
					$tabla .= '<td>'.$this->nomColumna($i).'</td>';
				}
				$tabla .= '</tr>';
			}
			while($dt = $this->verFila()){
				$tabla .= '<tr>';
				for($i=0;$i<$j;$i++){
					$tabla .= '<td>'.$dt[$i].'</td>';
				}
				$tabla .= '</tr>';
			}
			$tabla .= '</table>';
			echo $tabla;
		}
	}
  /**
   * Destructor de la clase
   * <code>
   * parent::__destruct();
   * </code>
   * @access public
   */
	public function __destruct(){
		parent::__destruct();
	}
}
?>