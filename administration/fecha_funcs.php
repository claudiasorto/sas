<?php

################################################
# CLASE Y LIBRERIA PARA ACCESO A BASE DE DATOS #
#  02/2008 Miguel Romero mikeromero21@gmail.com  #
################################################
	
class OFECHA{
	var $fechaHoy;

	function OFECHA(){ // Constructor
	  $this->fechaHoy = date("Y-m-d"); 		
	}	
		
	function cvDtoY($fec){ //obtenemos la fecha en dd/mm/yyyy y devolvemos yyyy-mm-dd
	  $part = explode("/",$fec);
	  $fechaC = $part[2].'-'.$part[1].'-'.$part[0];
	  return $fechaC;	  
	}
	
	function fecToLetras($fec){ //recibimos la fecha en formato yyyy-mm-dd y la convertimos a letras

	  $part = explode('-',$fec);
	  switch($part[1]){
	    case 1:
		  $mm = "Enero";
		  break;
		case 2:
		  $mm = "Febrero";
		  break;
		case 3:
		  $mm = "Marzo";
		  break;
		case 4:
		  $mm = "Abril";
		  break;
		case 5:
		  $mm = "Mayo";
		  break;
		case 6:
		  $mm = "Junio";
		  break;
		case 7:
		  $mm = "Julio";
		  break;
		case 8:
		  $mm = "Agosto";
		  break;
		case 9:
		  $mm = "Septiembre";
		  break;
		case 10:
		  $mm = "Octubre";
		  break;
		case 11:
		  $mm = "Noviembre";
		  break;
		case 12:
		  $mm = "Diciembre";
		  break;
	  }
	  
	  return $part['2'].' de '.$mm.' de '. $part[0];
	  //return $fec;
	}
	
	function cvFecha($fec){  //recibimos la fecha en formato yyyy-mm-dd y la convertimos  dd/mm/yyyy
		$part = explode('-',$fec);
		return 	$part['2'].'/'.$part['1'].'/'.$part['0'];
	}
	
}
?>