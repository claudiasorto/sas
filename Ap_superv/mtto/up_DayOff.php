<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$oFec = new OFECHA;

function suma_fechas($fecha,$ndias)
            
{
            
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
            
              list($dia,$mes,$año)=split("/", $fecha);
            
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
            
              list($dia,$mes,$año)=split("-",$fecha);
        $nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
        $nuevafecha=date("d/m/Y",$nueva);
            
      return ($nuevafecha);  
            
}

$csv = array();
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
				$csv[$row]['lunes'] = $data[1];
				$csv[$row]['martes'] = $data[2];
				$csv[$row]['miercoles'] = $data[3];
				$csv[$row]['jueves'] = $data[4];
				$csv[$row]['viernes'] = $data[5];
				$csv[$row]['sabado'] = $data[6];
				
				$row++;
			}
			fclose($handle);
		}
		for($i=0; $i<$row; $i++){
			
			$sqlText = "select employee_id from employees where username='".$csv[$i]['badge']."'";
			$dtEmp = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$nuevaFecha = "";
				$n = 0;
				if($csv[$i]['lunes']=='OFF'){
					$nuevaFecha[$n] = $_POST['fechaIni'];
					$n  = $n+1;
				}
				if($csv[$i]['martes']=='OFF'){
					$nuevaFecha[$n] = suma_fechas($_POST['fechaIni'],1);
					$n = $n+1;
				}
				if($csv[$i]['miercoles']=='OFF'){
					$nuevaFecha[$n] = suma_fechas($_POST['fechaIni'],2);	
					$n = $n +1 ;
				}
				if($csv[$i]['jueves']=='OFF'){
					$nuevaFecha[$n] = suma_fechas($_POST['fechaIni'],3);	
					$n = $n +1 ;
				}
				if($csv[$i]['viernes']=='OFF'){
					$nuevaFecha[$n] = suma_fechas($_POST['fechaIni'],4);	
					$n = $n +1 ;
				}
				if($csv[$i]['sabado']=='OFF'){
					$nuevaFecha[$n] = suma_fechas($_POST['fechaIni'],5);	
					$n = $n +1 ;
				}
				for($j=0; $j<$n; $j++){
					$fecha = $oFec->cvDtoY($nuevaFecha[$j]);
					$sqlText = "select absent_id from absenteeism where employee_id=".$dtEmp['0']['employee_id']." and absent_date='".$fecha."'";
					$dtAbsent = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update absenteeism set absent_status='O' where absent_id=".$dtAbsent['0']['absent_id'];
						$dbEx->updSql($sqlText);
					}	
					else{
						$sqlText = "insert into absenteeism set employee_id=".$dtEmp['0']['employee_id'].", absent_date='".$fecha."', absent_status='O'";
						$dbEx->insSql($sqlText);	
					}
				}
			}//termina de verificar si existe el empleado
		}
		$rslt = 2;
		if($rslt ==2){
			echo '<script>alert("Pay stub upload successful");location.href="../index.php";</script>';	
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV");location.href="../index.php";</script>';
		die();	
	}
}
