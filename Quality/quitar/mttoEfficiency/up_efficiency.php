<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$oFec = new OFECHA; 

$csv = array();
if(strlen($_POST['fecha'])<=0){
	echo '<script>alert("You must a date");location.href="../index.php";</script>';
	die();
}
else{
	$fecha = $oFec->cvDtoY($_POST['fecha']);	
}

if($_FILES['flDoc']['size']==0){
	echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
	die();
}
else{
	$ext = strtolower(end(explode('.',$_FILES['flDoc']['name']))); 
	$type = $_FILES['flDoc']['type'];
	$tmpName = $_FILES['flDoc']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$row = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csv[$row]['badge'] = $data[0];
				$csv[$row]['valor'] = $data[1];
				$row++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
		die();	
	}
}//Termina de guardar objetos del CSV


for($i=0; $i<$row; $i++){
	//verifica que el badge ingresado corresponda a un agente
	$sqlText = "select employee_id from employees where username='".trim($csv[$i]['badge'])."'";
	$dtEmp = $dbEx->selSql($sqlText);
	if($dbEx->numrows>0){
		$sqlText = "select * from efficiency where employee_id=".$dtEmp['0']['employee_id']." and efficiency_date='".$fecha."'";
		$dtAht = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update efficiency set efficiency_percent='".$csv[$i]['valor']."' where efficiency_id=".$dtAht['0']['EFFICIENCY_ID'];
			$dbEx->updSql($sqlText);
		}	
		else{
			$sqlText = "insert into efficiency set employee_id=".$dtEmp['0']['employee_id'].", efficiency_date='".$fecha."', efficiency_percent='".$csv[$i]['valor']."'";
			$dbEx->insSql($sqlText);	
		}
	}
}
$rslt = 2;
if($rslt ==2){
		echo '<script>alert("Efficiency upload successful");location.href="../index.php";</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again");location.href="../index.php";</script>';	
} 