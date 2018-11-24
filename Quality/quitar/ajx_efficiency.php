<?php
//Funciones para call details	
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
 
$dbEx = new DBX;
$oFec = new OFECHA;
  function cargaPag($urlToLoad){ //funcion para cargar una pagina
    $pagLoad = '';
    $fp=fopen($urlToLoad,"r") or die("Error al abrir el fichero");
    $pagLoad = fread($fp,30000);
    return $pagLoad;
  }
switch($_POST['Do']){
	case 'formUpEfficiency':
		$rslt = cargaPag("../mttoEfficiency/formUpEfficiency.php");
		echo $rslt;
	break;
}
  
?>