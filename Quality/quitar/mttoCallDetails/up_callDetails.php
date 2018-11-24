<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$oFec = new OFECHA; 

function zerofill($entero, $largo){
    	// Limpiamos por si se encontraran errores de tipo en las variables
    	$entero = (int)$entero;
    	$largo = (int)$largo;
     
    	$relleno = '';
  		if (strlen($entero) < $largo){
			$valor = $largo - strlen($entero);
        	$relleno = str_repeat('0', $valor);
    	}
    	return $relleno . $entero;
}

function cvDtoY($fec){ //obtenemos la fecha en dd/mm/yyyy y devolvemos yyyy-mm-dd
	  $part = explode("/",$fec);
	  $fechaC = $part[2].'-'.$part[1].'-'.$part[0];
	  return $fechaC;	  
}
if($_POST['lsCuenta']<=0){
	echo '<script>alert("You must select an account");location.href="../index.php";</script>';
	die();	
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
				$csv[$row]['name'] = $data[0];
				$csv[$row]['sequence'] = $data[1];
				$csv[$row]['agent_no'] = $data[2];
				$csv[$row]['team_no'] = $data[3];
				$csv[$row]['login_date'] = $data[4];
				$csv[$row]['logoff_date'] = $data[5];
				$csv[$row]['duration'] = $data[6];
				$csv[$row]['wk'] = $data[7];
				
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

//Si la cuenta seleccionada es de terracom registra las horas normales
if($_POST['lsCuenta']==1){
	for($i=0; $i<$row; $i++){
		$formatFecLogin = explode(" ",$csv[$i]['login_date']);
		$fechaLogin = $formatFecLogin[0];
		$formatHoraLogin = explode(":",$formatFecLogin[1]);
		$horaLogin = zerofill($formatHoraLogin[0],2).':'.zerofill($formatHoraLogin[1],2).':'.zerofill($formatHoraLogin[2],2);

		$formatFecLogoff = explode(" ",$csv[$i]['logoff_date']);
		$fechaLogoff = $formatFecLogoff[0];
		$formatHoraLogoff = explode(":",$formatFecLogoff[1]);
		$horaLogoff = zerofill($formatHoraLogoff[0],2).':'.zerofill($formatHoraLogoff[1],2).':'.zerofill($formatHoraLogoff[2],2);

		$fecLogin = cvDtoY($fechaLogin);
		$fecLogoff = cvDtoY($fechaLogoff);
		$sqlText = "select calldet_id from time_calldetails where calldet_sequence=".$csv[$i]['sequence'];
		$dtCall = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update time_calldetails set calldet_name='".$csv[$i]['name']."', calldet_sequence=".$csv[$i]['sequence'].", calldet_agentno='".$csv[$i]['agent_no']."', calldet_logindate='".$fecLogin." ".$horaLogin."', calldet_logoffdate='".$fecLogoff." ".$horaLogoff."', calldet_duration=".$csv[$i]['duration'].", id_account=".$_POST['lsCuenta']." where calldet_id=".$dtCall['0']['calldet_id'];
			$dbEx->updSql($sqlText);	
		}
		else{
			$sqlText = "insert into time_calldetails set calldet_name='".$csv[$i]['name']."',calldet_sequence=".$csv[$i]['sequence'].", calldet_agentno='".$csv[$i]['agent_no']."', calldet_logindate='".$fecLogin." ".$horaLogin."', calldet_logoffdate='".$fecLogoff." ".$horaLogoff."', calldet_duration=".$csv[$i]['duration'].", id_account=".$_POST['lsCuenta'];
	
			$dbEx->insSql($sqlText);
		}
		$rslt = 2;
	
	}
}

//Si la cuenta seleccionada es de yourtel resta una hora a la ingresada

else if($_POST['lsCuenta']==2){
	for($i=0; $i<$row; $i++){
		$formatFecLogin = explode(" ",$csv[$i]['login_date']);
		$fechaLogin = $formatFecLogin[0];
		$formatHoraLogin = explode(":",$formatFecLogin[1]);
		$horaLogin = zerofill($formatHoraLogin[0],2).':'.zerofill($formatHoraLogin[1],2).':'.zerofill($formatHoraLogin[2],2);
		$fecLogin = cvDtoY($fechaLogin);
		$fechaLoginCompleta =  $fecLogin." ".$horaLogin;
		$fechaLoginCompleta = date('Y-m-d H:i:s', strtotime('+ 1 hours',strtotime($fechaLoginCompleta)));

		$formatFecLogoff = explode(" ",$csv[$i]['logoff_date']);
		$fechaLogoff = $formatFecLogoff[0];
		$formatHoraLogoff = explode(":",$formatFecLogoff[1]);
		$horaLogoff = zerofill($formatHoraLogoff[0],2).':'.zerofill($formatHoraLogoff[1],2).':'.zerofill($formatHoraLogoff[2],2);
		$fecLogoff = cvDtoY($fechaLogoff);
		$fechaLogoffCompleta = $fecLogoff." ".$horaLogoff;
		$fechaLogoffCompleta = date('Y-m-d H:i:s', strtotime('+ 1 hours', strtotime($fechaLogoffCompleta)));
		
		$sqlText = "select calldet_id from time_calldetails where calldet_sequence=".$csv[$i]['sequence'];
		$dtCall = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update time_calldetails set calldet_name='".$csv[$i]['name']."', calldet_sequence=".$csv[$i]['sequence'].", calldet_agentno='".$csv[$i]['agent_no']."', calldet_logindate='".$fechaLoginCompleta."', calldet_logoffdate='".$fechaLogoffCompleta."', calldet_duration=".$csv[$i]['duration'].", id_account=".$_POST['lsCuenta']." where calldet_id=".$dtCall['0']['calldet_id'];
			$dbEx->updSql($sqlText);	
		}
		else{
			$sqlText = "insert into time_calldetails set calldet_name='".$csv[$i]['name']."',calldet_sequence=".$csv[$i]['sequence'].", calldet_agentno='".$csv[$i]['agent_no']."', calldet_logindate='".$fechaLoginCompleta."', calldet_logoffdate='".$fechaLogoffCompleta."', calldet_duration=".$csv[$i]['duration'].", id_account=".$_POST['lsCuenta'];
	
			$dbEx->insSql($sqlText);
		}
		$rslt = 2;
	
	}
}
	if($rslt ==2){
		echo '<script>alert("Call details upload successful");location.href="../index.php";</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again");location.href="../index.php";</script>';	
	} 
//echo '<script>alert("'.$sqlText.'");</cript>';

?>
