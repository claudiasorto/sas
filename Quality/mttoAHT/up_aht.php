<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$oFec = new OFECHA; 

$csvTime = array();
$csvCalls = array();
if(strlen($_POST['fecha'])<=0){
	echo '<script>alert("You must a date");location.href="../index.php";</script>';
	die();
}
else{
	$fecha = $oFec->cvDtoY($_POST['fecha']);	
}

if($_FILES['flTime']['size']==0){
	echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
	die();
}
else{
	$ext = strtolower(end(explode('.',$_FILES['flTime']['name']))); 
	$type = $_FILES['flTime']['type'];
	$tmpName = $_FILES['flTime']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$rowTime = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvTime[$rowTime]['badge'] = $data[0];
				$csvTime[$rowTime]['valor'] = $data[1];
				$rowTime++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
		die();	
	}
}//Termina de guardar objetos del CSV
	
	
if($_FILES['flCalls']['size']==0){
	echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
	die();
}
else{
	$ext = strtolower(end(explode('.',$_FILES['flCalls']['name']))); 
	$type = $_FILES['flCalls']['type'];
	$tmpName = $_FILES['flCalls']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$rowCalls = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvCalls[$rowCalls]['badge'] = $data[0];
				$csvCalls[$rowCalls]['valor'] = $data[1];
				$rowCalls++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
		die();	
	}
}//Termina de guardar objetos del CSV

for($i=0; $i<$rowTime; $i++){
	//verifica que el badge ingresado corresponda a un agente
	$sqlText = "select employee_id from employees where username='".trim($csvTime[$i]['badge'])."'";
	$dtEmp = $dbEx->selSql($sqlText);
	if($dbEx->numrows>0){
		$sqlText = "select * from aht where employee_id=".$dtEmp['0']['employee_id']." and aht_date='".$fecha."'";
		$dtAht = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update aht set aht_totaltime='".$csvTime[$i]['valor']."' where aht_id=".$dtAht['0']['AHT_ID'];
			$dbEx->updSql($sqlText);
		}	
		else{
			$sqlText = "insert into aht set employee_id=".$dtEmp['0']['employee_id'].", aht_date='".$fecha."', aht_totaltime='".$csvTime[$i]['valor']."'";
			$dbEx->insSql($sqlText);	
		}
	}
}
for($i=0; $i<$rowCalls; $i++){
	$sqlText = "select employee_id from employees where username='".trim($csvCalls[$i]['badge'])."'";
	$dtEmp = $dbEx->selSql($sqlText);
	if($dbEx->numrows>0){
		$sqlText = "select * from aht where employee_id=".$dtEmp['0']['employee_id']." and aht_date='".$fecha."'";
		$dtAht = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update aht set aht_totalcalls='".$csvCalls[$i]['valor']."' where aht_id=".$dtAht['0']['AHT_ID'];
			$dbEx->updSql($sqlText);
		}
		else{	
			$sqlText = "insert into aht set employee_id=".$dtEmp['0']['employee_id'].", aht_date='".$fecha."', aht_totalcalls='".$csvCalls[$i]['valor']."'";	
			$dbEx->insSql($sqlText);
		}
			
	}
}
$rslt = 2;
if($rslt ==2){
		echo '<script>alert("AHT upload successful");location.href="../index.php";</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again");location.href="../index.php";</script>';	
} 
	
?>

