<?php

  require_once("../db_funcs.php");
  $dbEx = new DBX;

 $dir = 'fotos/';

  // Intentamos Subir Archivo
  // (1) Comprobamos que existe el nombre temporal del archivo
  if(isset($_FILES['flPhoto']['tmp_name'])) {
    $NUM = time();
    $nombre = $NUM.$_FILES['flPhoto']['name'];
    $tipoAr = $_FILES['flPhoto']['type'];
	$tamAr = $_FILES['flPhoto']['size'];
	if($tipoAr <> "image/jpeg" and $tipoAr <> "image/png"){
        echo '<script>alert("Error: La imagen a cargar debe ser en formato jpg o png '.$tipoAr.'");</script>';
	  	die();
	}
	
	if($tamAr >= 2000000){
	  echo '<script>alert("Error: El archivo que intenta cargar supera los 2 MB!");</script>';
	  die();
	}
    if (!copy($_FILES['flPhoto']['tmp_name'], $dir.$nombre)){
      echo '<script> alert("Error cargando el archivo, intente nuevamente!");</script>';
	  die();
    } else{
    
    //comprobamos si ya tiene un documento, lo borramos e ingresamos el nuevo a la base de datos
    $sqlText = "select foto from employees where employee_id = ".$_POST['idE'];
    $dtVer = $dbEx->selSql($sqlText);
 	if(file_exists($dir.$dtVer['0']['foto'])){
	    unlink($dir.$dtVer['0']['foto']);
 	}
 	//Actualizar el registro del empleado
 	$sqlText = "update employees set foto='".$nombre."' where employee_id = ".$_POST['idE'];
  	$dbEx->updSql($sqlText);

	echo '<script>alert("Archivo cargado con exito!")</script>';

    }
  }

?>
