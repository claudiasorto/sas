<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$oFec = new OFECHA;

$csv = array();
$rslt = 0;
//Verificar si se selecciono Documento
if($_FILES['flDoc']['size']==0){
	echo '<script>alert("You must select a document in format CSV");window.parent.loadPageNightHours();</script>';
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
				$csv[$row]['fecha'] = trim($data[1]);
				$csv[$row]['hora'] = trim($data[2]);
				$row++;
				
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV");window.parent.loadPageNightHours();</script>';
		die();	
	}
	$count = 0;
	$countRows = 0;
	for($i=0; $i<$row; $i++ ){
	    if(strlen($csv[$i]['badge']) > 0){
            $countRows = $countRows + 1;
		}
	
		$sqlText = "select employee_id from employees where username='".$csv[$i]['badge']."' and user_status=1";
		$dtE = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$horaNoc = "";
			if (floatval($csv[$i]['hora']) > 0 ){
				$horaNoc = floatval($csv[$i]['hora']);
			}
		    if($horaNoc <> ""){
		    	//Validar que exista payroll para la fecha sino no carga horas
            	$sqlText = "select payroll_id, payroll_date, payroll_htotal, payroll_daytime, payroll_nigth ".
					" from payroll where employee_id=".$dtE['0']['employee_id']." and payroll_date=str_to_date('".$csv[$i]['fecha']."','%d/%m/%Y')";

				$dtP = $dbEx->selSql($sqlText);
   				if($dbEx->numrows>0){
					$payrollID = $dtP['0']['payroll_id'];

					//Actualizar el registro de horas, las nocturnas no pueden ser mayores a las totales
					$sqlText = "update payroll ".
					"set payroll_daytime = (sec_to_time(if(time_to_sec(payroll_htotal) < (".$horaNoc."*3600), 0, (time_to_sec(payroll_htotal) - (".$horaNoc."* 3600))))), ".
					"payroll_nigth = (sec_to_time(if( time_to_sec(payroll_htotal) < (".$horaNoc."*3600) ,time_to_sec(payroll_htotal), (".$horaNoc."* 3600)))) ".
				    "where payroll_id= ".$dtP['0']['payroll_id'];
				    $dbEx->updSql($sqlText);
                    $updt = $dbEx->affectedRows;
            		if ($updt > 0) {
                        $count = $count + 1;
   					}
				}
			}
		}
	}//termina for
	
	if ($count == 0){
        $rslt = 0;
	}
	else if ($count < $countRows){
        $rslt = 1;
	}
	else if ($count == $countRows){
        $rslt = 2;
	}
	
}//Termina else de documento
if($rslt == 0){
	echo '<script>alert("Unable to upload any record, check the document loaded");window.parent.loadPageNightHours()</script>';
	}
else if($rslt ==1){
	echo '<script>alert("Partial data load, check the document loaded");window.parent.loadPageNightHours()</script>';
	}
else if($rslt ==2){
	echo '<script>alert("Upload successfully");window.parent.loadPageNightHours()</script>';
	}
else{
	echo '<script>alert("Execution problem, check the uploaded file and try again");window.parent.loadPageNightHours();</script>';
}
