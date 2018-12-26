<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$oFec = new OFECHA;

$csv = array();
$rslt = 0;
//Verificamos que se ha seleccionado fecha
if(strlen($_POST['fecha'])<=0){
	echo '<script>alert("You must select a date");window.parent.loadPageProgHours();</script>';
}
else{
	$fecha = $oFec->cvDtoY($_POST['fecha']);
	$start = $fecha;
	//Verificar que el dia seleccionado sea lunes
	$nFecha = strtotime(date("Y/m/d",strtotime($fecha)));
	/*$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
	if($dia>1){
		echo '<script>alert("The selected date must be a Monday");window.parent.loadPageProgHours();</script>';
	}*/
	
	//Verificar si se selecciono Documento
if($_FILES['flDoc']['size']==0){
	echo '<script>alert("You must select a document in format CSV");window.parent.loadPageProgHours();</script>';
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
				//obtiene valores del CSV
				$csv[$row]['badge'] = trim($data[0]);
				$csv[$row][0] = trim($data[1]);
				$csv[$row][1] = trim($data[2]);
				$csv[$row][2] = trim($data[3]);
				$csv[$row][3] = trim($data[4]);
				$csv[$row][4] = trim($data[5]);
				$csv[$row][5] = trim($data[6]);
				$csv[$row][6] = trim($data[7]);
				$row++;
				
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV");window.parent.loadPageProgHours();</script>';
		die();	
	}
	
}//Termina else de doc
//Cantidad de dias a cargar
$cantDias = $_POST['lsDias'];

	//Insertar la solicitud
	$sqlText = "insert into schedule_request(created_by) values ('".$_SESSION['usr_id']."')";
	$dbEx->insSql($sqlText);
	$reqId = $dbEx->insertID;
	
	for($i=0; $i<$row; $i++ ){
		$sqlText = "select employee_id from employees where username='".$csv[$i]['badge']."' and user_status=1";
		$dtE = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			for($j=0; $j<= ($cantDias - 1 ); $j++){ //la variable J permite cargar varios dias a la vez
				//$fecha = $start + (86400*$j);
				$fecha = strtotime($start . ' +'.$j.' day');
				$sqlText = "select sch_id from schedules where sch_date='".date("Y-m-d",$fecha)."' and employee_id=".$dtE['0']['employee_id'];


				$dtSch = $dbEx->selSql($sqlText);
				//Las horas programadas se pondra como hora de entrada a las 7am sin lunch
				if($dbEx->numrows>0){
					//$sqlText = "update schedules set sch_proghrs='".$csv[$i][$j]."' where sch_id=".$dtSch['0']['sch_id'];
					//Para carga de horas cero
					/*if (floatval($csv[$i][$j]) == 0){
					    $sqlText = "update schedules set sch_entry = null, sch_departure = null, sch_lunchin = null, sch_lunchout = null, ".
						" sch_break1in = null, sch_break1out = null, sch_break2in = null, sch_break2out = null ".
						" where sch_id=".$dtSch['0']['sch_id']." and sch_request_id <> ".$reqId;
						$dbEx->updSql($sqlText);
					}
					else if(floatval($csv[$i][$j]) > 0){ */
						//$sqlText = "update schedules set sch_proghrs='".$csv[$i][$j]."' where sch_id=".$dtSch['0']['sch_id'];
						$sqlText = "update schedules sc set sch_entry = '00:00:00', ".
						"sch_departure = sec_to_time(time_to_sec('00:00:00') + ".floatval($csv[$i][$j])."*3600 ".
						" + if(sch_request_id = ".$reqId.",time_to_sec(sch_departure),0)) , ".
						" sch_lunchin = null, sch_lunchout = null, ".
						" sch_break1in = null, sch_break1out = null, sch_break2in = null, sch_break2out = null , sch_request_id = ".$reqId." ".
						" where sch_id=".$dtSch['0']['sch_id'];
						
						$dbEx->updSql($sqlText);
	 				//}
				}
				else{
					//Para carga de horas cero
				    if (floatval($csv[$i][$j]) == 0){
						$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', ".
						" sch_entry = null, sch_departure = null, sch_request_id = ".$reqId;
					
						$dbEx->insSql($sqlText);
	 				}
	 				else if(floatval($csv[$i][$j]) > 0){
	 				    $sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', ".
						" sch_entry = '00:00:00', sch_departure = sec_to_time(time_to_sec('00:00:00') + ".floatval($csv[$i][$j])." *3600) , sch_request_id = ".$reqId;

						$dbEx->insSql($sqlText);
	  				}
				}	
			}		
		}
		$rslt = 2;	
	}//termina for
	
}//Termina else de fecha
if($rslt ==2){
	echo '<script>alert("Upload successfully");window.parent.loadPageProgHours()</script>';	
	}
else{
	echo '<script>alert("Execution problem, check the uploaded file and try again");window.parent.loadPageProgHours();</script>';	
}
