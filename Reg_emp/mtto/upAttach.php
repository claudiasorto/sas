<?php

  require_once("../db_funcs.php");
  $dbEx = new DBX;

 $dir = 'archivos/';

  // Intentamos Subir Archivo
  // (1) Comprobamos que existe el nombre temporal del archivo
  if(isset($_FILES['flDoc']['tmp_name'])) {
    $NUM = time();
    $nombre = $NUM.$_FILES['flDoc']['name'];
    $tipoAr = $_FILES['flDoc']['type'];
	$tamAr = $_FILES['flDoc']['size'];
	if($tamAr >= 10000000){
	  echo '<script>alert("Error: El archivo que intenta cargar supera los 10 MB!");</script>';
	  die();
	}
    if (!copy($_FILES['flDoc']['tmp_name'], $dir.$nombre)){
      echo '<script> alert("Error cargando el archivo, intente nuevamente!");</script>';
	  die();
    } else{
	//Insertar el nuevo archivo
 	$sqlText = "insert into empl_attachments set EMPLOYEE_ID = ".$_POST['idE'].", EMP_ATTACH_NAME = '".$nombre."', ".
			"CREATION_DATE = curdate(), created_by = ".$_SESSION['usr_id'];
	$dbEx->insSql($sqlText);
	  

	echo '<script>alert("Archivo cargado con exito!")</script>';

    }
  }

?>
