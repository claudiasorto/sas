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
//Un solo archivo para subir todas las metricas
if($_FILES['flData']['size']==0){
	echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
	die();
}
else{
	$ext = strtolower(end(explode('.',$_FILES['flData']['name']))); 
	$type = $_FILES['flData']['type'];
	$tmpName = $_FILES['flData']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$rowData = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csv[$rowData]['badge'] = $data[0];
				$csv[$rowData]['timeCalls'] = $data[1];
				$csv[$rowData]['totalCalls'] = $data[2];
				$csv[$rowData]['refused'] = $data[3];
				$csv[$rowData]['effic'] = $data[4];
				
				$rowData++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of total time of calls in CSV format");location.href="../index.php";</script>';
		die();	
	}	
}
//Ingreso de los datos en la base
for($i=0; $i<$rowData;$i++){
	$sqlText = "select employee_id, id_supervisor from employees where username='".trim($csv[$i]['badge'])."' and user_status=1";
	$dtEmp = $dbEx->selSql($sqlText);
	if($dbEx->numrows>0){
		$sqlText = "select * from phone_metrics where metric_date='".$fecha."' and employee_id=".$dtEmp['0']['employee_id'];
		$dtMetric = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update phone_metrics set metric_aht_totaltime='".$csv[$i]['timeCalls']."', metric_totalcalls=".$csv[$i]['totalCalls'].", metric_efficiency='".$csv[$i]['effic']."', metric_refused=".$csv[$i]['refused'].", supervisor_id=".$dtEmp['0']['id_supervisor']." where metric_id=".$dtMetric['0']['METRIC_ID'];
			$dbEx->updSql($sqlText);
		}
		else{
			$sqlText = "insert into phone_metrics set employee_id=".$dtEmp['0']['employee_id'].", metric_date='".$fecha."', metric_aht_totaltime='".$csv[$i]['timeCalls']."', metric_totalcalls=".$csv[$i]['totalCalls'].", metric_efficiency='".$csv[$i]['effic']."', metric_refused=".$csv[$i]['refused'].", supervisor_id=".$dtEmp['0']['id_supervisor'];
			$dbEx->insSql($sqlText);
		}
		
	}
}

/*
$csvTime = array();
$csvCalls = array();
if($_FILES['flTime']['size']>0){
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
		echo '<script>alert("You must select a document of total time of calls in CSV format");location.href="../index.php";</script>';
		die();	
	}
	//Ingresa el tiempo total en llamadas del AHT
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
	
}//TErmina de guardar el CSV de Tiempo en llamadas
	
	
if($_FILES['flCalls']['size']>0){
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
		echo '<script>alert("You must select a document of total calls in CSV format");location.href="../index.php";</script>';
		die();	
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
	
	
}//Termina de guardar objetos del CSV para total de llamadas

//CSV refused calls
$csvRefused = array();
if($_FILES['flRefused']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flRefused']['name']))); 
	$type = $_FILES['flRefused']['type'];
	$tmpName = $_FILES['flRefused']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$rowRefused = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvRefused[$rowRefused]['badge'] = $data[0];
				$csvRefused[$rowRefused]['refused'] = $data[1];
				$rowRefused++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of total refused calls in CSV format");location.href="../index.php";</script>';
		die();	
	}
	for($i=0; $i<$rowRefused; $i++){
	//verifica que el badge ingresado corresponda a un agente
		$sqlText = "select employee_id from employees where username='".trim($csvRefused[$i]['badge'])."'";
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "select * from refused_calls where employee_id=".$dtEmp['0']['employee_id']." and refused_date='".$fecha."'";
			$dtRef = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$sqlText = "update refused_calls set refused_totalrefused=".$csvRefused[$i]['refused']."  where refused_id=".$dtRef['0']['REFUSED_ID'];
				$dbEx->updSql($sqlText);
			}	
			else{
				$sqlText = "insert into refused_calls set employee_id=".$dtEmp['0']['employee_id'].", refused_date='".$fecha."', refused_totalrefused=".$csvRefused[$i]['refused'];
				$dbEx->insSql($sqlText);	
			}
		}
	}
	
}//Termina de guardar objetos del CSV

$csvEfficiency = array();
if($_FILES['flEfficiency']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flEfficiency']['name']))); 
	$type = $_FILES['flEfficiency']['type'];
	$tmpName = $_FILES['flEfficiency']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$rowEfficiency = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvEfficiency[$rowEfficiency]['badge'] = $data[0];
				$csvEfficiency[$rowEfficiency]['valor'] = $data[1];
				$rowEfficiency++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of percentage efficiency in CSV format");location.href="../index.php";</script>';
		die();	
	}
	for($i=0; $i<$rowEfficiency; $i++){
		//verifica que el badge ingresado corresponda a un agente
		$sqlText = "select employee_id from employees where username='".trim($csvEfficiency[$i]['badge'])."'";
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "select * from efficiency where employee_id=".$dtEmp['0']['employee_id']." and efficiency_date='".$fecha."'";
			$dtEff = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$sqlText = "update efficiency set efficiency_percent='".$csvEfficiency[$i]['valor']."' where efficiency_id=".$dtEff['0']['EFFICIENCY_ID'];
				$dbEx->updSql($sqlText);
			}	
			else{
				$sqlText = "insert into efficiency set employee_id=".$dtEmp['0']['employee_id'].", efficiency_date='".$fecha."', efficiency_percent='".$csvEfficiency[$i]['valor']."'";
				$dbEx->insSql($sqlText);	
			}
		}
	}
	
	
}//Termina de guardar objetos del CSV Efficiency


//Archivo de Lateness
$csvLateness = array();
if($_FILES['flLateness']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flLateness']['name']))); 
	$type = $_FILES['flLateness']['type'];
	$tmpName = $_FILES['flLateness']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$rowLateness = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvLateness[$rowLateness]['badge'] = $data[0];
				$csvLateness[$rowLateness]['valor'] = $data[1];
				$rowLateness++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of total time of lateness in CSV format ");location.href="../index.php";</script>';
		die();	
	}
	for($i=0; $i<$rowLateness; $i++){
		//verifica que el badge ingresado corresponda a un agente
		$sqlText = "select employee_id from employees where username='".trim($csvLateness[$i]['badge'])."'";
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "select * from lateness where employee_id=".$dtEmp['0']['employee_id']." and lateness_date='".$fecha."'";
			$dtLet = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$sqlText = "update lateness set lateness_totaltime='".$csvLateness[$i]['valor']."' where lateness_id=".$dtLet['0']['LATENESS_ID'];
				$dbEx->updSql($sqlText);
			}	
			else{
				$sqlText = "insert into lateness set employee_id=".$dtEmp['0']['employee_id'].", lateness_date='".$fecha."', lateness_totaltime='".$csvLateness[$i]['valor']."'";
				$dbEx->insSql($sqlText);	
			}
		}
	}
	
	
}//Termina de guardar objetos del CSV Lateness
*/

$rslt = 2;
if($rslt ==2){
		echo '<script>alert("Metrics upload successful");location.href="../index.php";</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again");location.href="../index.php";</script>';	
} 
	
?>