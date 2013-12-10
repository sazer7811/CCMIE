<?php
 #####################################################################################################
 #	Este script permanece libre mientras estas lineas permanezcan intactas
 #####################################################################################################
 #	Nombre de 				
 #	Archivo		:		CCMIE.class.php
 #
 #	Autor		:		Fabio Grandas <gffabio@hotmail.com>
 #
 #	Version		:		1.0.2
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
#   @package CCMIE
#	@author Fabio Grandas A
#	@license http://opensource.org/licenses/gpl-license.php GNU Public License
#	@version v1.0.2
#   @copyright 2011
#   @access public
#

require_once('CCMIE.class.basic.php');
#C conectar C consultar M modificar I insertar E eliminar
class CCMIEC extends CCMIE{  
	####################################################################################################################
	#DECLARACION DE VARIABLES																############################
	####################################################################################################################
	
	#variables privadas usadas en funciones 
	private $campolike = array();
	private $were=0;
	private $ordeby;
	private $limit;
	private $tabla;
	private $tablas = array();
	private $campos = array();
	private $campo;
	private $campovalor = array();
	private $valores = array();
	private $valor;
	private $condicion = array();
	private $inerjoin = array();
	private $campovalorinser = array();
	#variables publicas
	
	#constructor de la clase   
	public function __construct() {		
        parent::__construct();
	} 
	####################################################################################################################
	#FUNCIONES PRIVADAS																		############################
	####################################################################################################################
	
	#funcion privada que me crea una consulta de actualizacion de datos
	private function actualizar(){
		if(empty($this->campovalor))return 'No se han agregado campos ni valores';
		if(empty($this->tabla))return 'No se ha agregado ninguna tabla';
		if(empty($this->condicion)) return 'no se ha agregado una condicion';
        $this->query = 'UPDATE '; 
        $this->query.= $this->tabla;
        $this->query.= ' SET ';
        foreach($this->campovalor as $campo){
			$this->query.=$campo.', ';
        }
		unset($this->campovalor);
        $this->query = substr($this->query,0,strlen($this->query)-2); 
		$this->query.= ' WHERE ';
		foreach($this->condicion as $condi){
			$this->query.= $condi.' AND ';	
		}
		unset($this->condicion);
        $this->query = substr($this->query,0,strlen($this->query)-4); 
		return $this->query;
	}
	#funcion que me crea la consulta de insercion	
	private function insertar($tipo=0){
		if(empty($this->tabla))return 'No se ha agregado ninguna tabla';
		$this->query = 'INSERT INTO '.$this->tabla;
		if($tipo==1){	
			if(empty($this->campovalorinser))return 'No se han agregado campos o valores';
				$campo='';$valor='';
				foreach($this->campovalorinser as $c=>$v){
					$campo.=$c.',';$valor.="'".$v."',";
				}
				unset($this->campovalorinser);
				$this->query.= ' ('.substr($campo,0,strlen($campo)-1).')';
				$this->query.= ' VALUES (';
				$this->query.= substr($valor,0,strlen($valor)-1);
				$this->query.= ')';			
		}else{
			if(empty($this->valores))return 'No se han agregado campos';
				$this->query.= ' VALUES (';
				foreach($this->valores as $valor){
					$this->query.="'".$valor."',";
				}
				unset($this->valores);
				$this->query = substr($this->query,0,strlen($this->query)-1); 
				$this->query.= ')';
		}
		return $this->query;
	}
	#funcion privada que contruye el query de consulta 
	private function consultar(){
		#si no se agrga una tabla de consulta retorno mensage
		if(empty($this->tablas) && empty($this->inerjoin))return 'No se ha agregado ninguna tabla';
        $this->query = 'SELECT ';
		if($this->campos){
		foreach($this->campos as $campo){
			$this->query.= $campo.', ';
		}
		unset($this->campos);
		$this->query = substr($this->query,0,strlen($this->query)-2);
		}else {
			$this->query.='*';
		}
		$this->query.= ' FROM ';
		if($this->tablas){
			foreach($this->tablas as $table){
				$this->query.= $table.', ';
			}
			unset($this->tablas);
			$this->query = substr($this->query,0,strlen($this->query)-2);
		}
		if($this->inerjoin){
			$this->query .= $this->inerjoin;
		}
		#si se agrego esta condicion, agrego al query una condicion like	
		if($this->campolike){
			$this->query.= ($this->were==0)?' WHERE ':' AND ';
			foreach($this->campolike as $dato)$this->query.=$dato.' AND ';
			$this->query = substr($this->query,0,strlen($this->query)-4);
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
			$this->query = substr($this->query,0,strlen($this->query)-4);
		}
		#si se agrega un orden lo adiciona al query
		if($this->ordeby){
			$this->query.= ' ORDER BY '.$this->ordeby;
		}
		#si hay un limite lo agrego al query
		if($this->limit){
			$this->query.=' LIMIT '.$this->limit;
		}
		return $this->query;
	}
	#funcion para crear la consulta de eliminar
	private function eliminar(){
		if($this->tabla)return 'No se ha agregado ninguna tabla';
		if(empty($this->condicion)) return 'no se ha agregado una condicion';
		$this->query = 'DELETE FROM ';
		$this->query .= $this->tabla;
		$this->query.= ' WHERE ';
		foreach($this->condicion as $condi){
			$this->query.= $condi.' AND ';	
		}
		unset($this->condicion);
        $this->query = substr($this->query,0,strlen($this->query)-4); 
		return $this->query;		
	}
	#funcion para vaciar una tabla
	private function truncate(){
		if($this->tabla){
			return 'TRUNCATE TABLE '.$this->tabla;
		}
		return false;
	}
	####################################################################################################################
	#FUNCIONES PUBLICAS																		############################
	####################################################################################################################
	
	#agregar el monbre del campo y el valor, campo = valor
	public function addCamValMod($campo,$valor){
		 if($campo){
			 $this->campovalor[] = $this->addFiltro($campo)." = '".$this->addFiltro($valor)."'";
		 }
    }
	#agregar campos, SELECT campo[0],campo[1],campo[2] o INSERT campo[0],campo[1],campo[2]
	public function addCampos($campo){
		 if($campo){
			 $this->campos[] = $this->addFiltro($campo);
		 }
    }
	#agregar el nombre de la tabla, from tabla
    public function addTabla($tabla) {
		if($tabla){
        $this->tabla = $this->addFiltro($tabla);
		}
    }
	#agregar el nombre de las tablas, from tabla1,tabla2,tabla3
    public function addTablas($tablas) {
		if($tablas){
        $this->tablas[] = $this->addFiltro($tablas);
		}
    }
	#agregar condicion where campo = valor
	public function addCondicion($campo,$valor){
		if($campo){
			$this->condicion[]= $this->addFiltro($campo)." = '".$this->addFiltro($valor)."' ";
		}
	}
	#agregar campos y valores como para una insercion con campos especificos, INSERT tabla (campo1,campo2...) VALUES(valores[0],valores[1]...) o de la forma
	#INSERT tabla VALUES(valores[0],valores[1]...) cuando son todos los campos de la tabla.
	public function addCamValInsert($valor,$campo=''){		
		if($campo){
		$this->campovalorinser[$this->addFiltro($campo)] = $this->addFiltro($valor);
		}else{
		$this->valores[] = $this->addFiltro($valor);
		}
	}
	#agregar una condicion like, campo like valor
	public function addLike($campo,$valor){
		if($campo && $valor){
			$this->campolike[] = $this->addFiltro($campo)." LIKE '%".$this->addFiltro($valor)."%' ";
		}
	}
	#agregar una condicion de ordenar, ORDEN BY campo DESC por defaul
	public function addOrdenar($campo,$desc=0){
		if($campo){#solo se agrega uno
			$campo = $this->addFiltro($campo);
			$this->ordeby = ($desc==1)?$campo.' ASC':$campo.' DESC';
		}
	}
	public function addLimite($cantidad,$inicio=0){
		$this->limit = (int)$inicio.','.(int)$cantidad;
	} 
	#agrega combinaciones INER JOIN
	public function addInerJoin($tabla2,$campo2,$campo1){
	 	if($campo1 && $campo2 && $tabla2){#ej:tabla1 iner join tabla2 on campo2=campo1
			$this->inerjoin .= ' INNER JOIN '.$this->addFiltro($tabla2).' ON '.$this->addFiltro($campo2).' = '.$this->addFiltro($campo1);
		}
	}
	#esta funcion ejecuta una consulta de:C ó I ó M ó E ó T
	#Consultar, Insertar, Modificar, Eliminar, Truncate
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
	#imprimir los datos de la consulta en una tabla
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
	#al terminar de ejecutarse la pagina donde se crea un objeto de esta clase se destruye este objeto.
	public function __destruct(){
		parent::__destruct();
	}   
}  
?>