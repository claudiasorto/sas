<?php

  require_once("../db_funcs.php");
  require_once("../fecha_funcs.php");
  $dbEx = new DBX;
  $oFec = new OFECHA;
  
 $dir = 'archivosNS/';
  
if($_POST['acc']==1){ //Ingreso de nuevo archivo
  // Intentamos Subir Archivo
  // (1) Comprobamos que existe el nombre temporal del archivo
  if(isset($_FILES['flDoc']['tmp_name'])) {
    $NUM = time();
    $nombre = $NUM.$_FILES['flDoc']['name'];
    $tipoAr = $_FILES['flDoc']['type'];
	$tamAr = $_FILES['flDoc']['size'];
	if($tamAr >= 10000000){
	  echo '<script>alert("Error: The file to upload must not exceed 10 MB!");</script>'; 
	  die();
	}
    if (!copy($_FILES['flDoc']['tmp_name'], $dir.$nombre)){
      echo '<script> alert("Error uploading the file, try again later!");</script>';
	  die();
    } else{    
	  //comprobamos si ya tiene un documento, lo borramos e ingresamos el nuevo a la base de datos
	  $sqlText = "select monitnsemp_attach from monitoringns_emp where id_monitnsemp=".$_POST['idM'];
	  $dtVer = $dbEx->selSql($sqlText);
	  if(file_exists($dir.$dtVer['0']['monitnsemp_attach'])){
	    unlink($dir.$dtVer['0']['monitnsemp_attach']);
	  }
	  //ingresamos en la base de datos
	  $sqlText = "update monitoringns_emp set monitnsemp_attach='".$nombre."' where id_monitnsemp=".$_POST['idM'];
	  $dbEx->updSql($sqlText);
	
	  echo '<script> alert("File uploaded successfully!");window.parent.loadPage("../filtros_monitlog.php");</script>';	  	
	
    }		
  }
}
?>