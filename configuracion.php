<?php
/**
 * defino Variables globales<br>
 * para la proxima version estas seran tipo privado
 * @author Fabio Grandas A
 * @version 1.0.2
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2012
 * Revision: 04 Febrero 2012
 * @link http://ccmie.engcm.com ww.engcm.com
 * @link http://www.gffabio.webcindario.com/document/CCMIE  www.gffabio.webcindario.com
 */
/**
 * Defino la zona horaria
 */
date_default_timezone_set("America/Bogota");
/**
 * codificacion de caracteres para base de datos
 * @link http://dev.mysql.com/doc/refman/5.0/es/charset.html http://dev.mysql.com/doc/refman/5.0/es/charset.html
 */
define('CODIFICACIONCARACTER','utf8');
/**
 * nombre del servidor
 */
define('DB_SERVIDOR','localhost');
/**
 * nombre de usuario de la base de datos
 */
define('DB_USUARIO','root');
/**
 * contraseña de usuario de la base de datos
 */
define('DB_CONTRASENA','12345');
/**
 * nombre de la base de datos
 */
define('DB_BASEDATO','prueba');
#--------------------------------------
?>