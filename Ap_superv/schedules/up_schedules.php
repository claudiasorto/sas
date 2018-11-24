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
	echo '<script>alert("You must select a date");window.parent.loadPageSchedules();</script>';
}
else{
	$fecha = $oFec->cvDtoY($_POST['fecha']);
	$start = strtotime($fecha);
	//Verificar que el dia seleccionado sea lunes
	$nFecha = strtotime(date("Y/m/d",$start));
	$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
	$flag = true;
	if($dia>1){
		echo '<script>alert("The selected date must be a Monday");window.parent.loadPageSchedules();</script>';
		$flag = false;
	}

//Verificar si se selecciono Documento
if($_FILES['flDoc']['size']==0 and $flag){
	echo '<script>alert("You must select a document in format CSV");window.parent.loadPageSchedules();</script>';
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
				$csv[$row]['horarioLunes'] = trim($data[1]);
				$csv[$row]['break1Lunes'] = trim($data[2]);
				$csv[$row]['lunchLunes'] = trim($data[3]);
				$csv[$row]['break2Lunes'] = trim($data[4]);
				$csv[$row]['horarioMartes'] = trim($data[5]);
				$csv[$row]['break1Martes'] = trim($data[6]);
				$csv[$row]['lunchMartes'] = trim($data[7]);
				$csv[$row]['break2Martes'] = trim($data[8]);
				$csv[$row]['horarioMierc'] = trim($data[9]);
				$csv[$row]['break1Mierc'] = trim($data[10]);
				$csv[$row]['lunchMierc'] = trim($data[11]);
				$csv[$row]['break2Mierc'] = trim($data[12]);
				$csv[$row]['horarioJueves'] = trim($data[13]);
				$csv[$row]['break1Jueves'] = trim($data[14]);
				$csv[$row]['lunchJueves'] = trim($data[15]);
				$csv[$row]['break2Jueves'] = trim($data[16]);
				$csv[$row]['horarioViernes'] = trim($data[17]);
				$csv[$row]['break1Viernes'] = trim($data[18]);
				$csv[$row]['lunchViernes'] = trim($data[19]);
				$csv[$row]['break2Viernes'] = trim($data[20]);
				$csv[$row]['horarioSabado'] = trim($data[21]);
				$csv[$row]['break1Sabado'] = trim($data[22]);
				$csv[$row]['lunchSabado'] = trim($data[23]);
				$csv[$row]['break2Sabado'] = trim($data[24]);
				$csv[$row]['horarioDomingo'] = trim($data[25]);
				$csv[$row]['break1Domingo'] = trim($data[26]);
				$csv[$row]['lunchDomingo'] = trim($data[27]);
				$csv[$row]['break2Domingo'] = trim($data[28]);
			
				$row++;

			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV");window.parent.loadPageSchedules();</script>';
		die();	
	}
}

for($i=0; $i<$row; $i++ ){
	$sqlText = "select employee_id from employees where username='".$csv[$i]['badge']."' and user_status=1";

	$dtE = $dbEx->selSql($sqlText);
	if($dbEx->numrows>0){

		$dayOffLunes = false;
		$dayOffMartes = false;
		$dayOffMiercoles = false;
		$dayOffJueves = false;
		$dayOffViernes = false;
		$dayOffSabado = false;
		$dayOffDomingo = false;

		//Horario para lunes
		$horarioLunes = explode("-",$csv[$i]['horarioLunes']);
		if($horarioLunes[0]=='OFF'){
			$dayOffLunes = true;
		}
		else{
			$entradaLunes = " , sch_entry=NULL ";
			$salidaLunes = " , sch_departure = NULL ";
			
			$break1SalidaLunes = " , sch_break1out=NULL ";
			$break1EntradaLunes = " , sch_break1in=NULL ";
			$lunchSalidaLunes = " , sch_lunchout=NULL ";
			$lunchEntradaLunes = " , sch_lunchin=NULL ";
			$break2SalidaLunes = " , sch_break2out=NULL ";
			$break2EntradaLunes = " , sch_break2in=NULL ";
			
			if(strlen($csv[$i]['horarioLunes']>0)){
				$entradaLunes = " , sch_entry= '".$horarioLunes[0].":00"."'";
				$salidaLunes = " , sch_departure = '".$horarioLunes[1].":00"."' ";
			}
			
			if(strlen($csv[$i]['break1Lunes'])>0){
				$break1SalidaLunes = " , sch_break1out= '".$csv[$i]['break1Lunes'].":00"."' ";
				$break1EntradaLunes = " , sch_break1in= '".date("H:i:s",(strtotime($csv[$i]['break1Lunes'].":00 + 15 Minutes")))."' ";
			}
			if(strlen($csv[$i]['lunchLunes'])>0){
				$lunchSalidaLunes = " , sch_lunchout='".$csv[$i]['lunchLunes'].":00"."' ";
				$lunchEntradaLunes = " , sch_lunchin='".date("H:i:s", strtotime($csv[$i]['lunchLunes'].":00 + 1 Hours"))."' ";
			}
			if(strlen($csv[$i]['break2Lunes'])>0){
				$break2SalidaLunes = " , sch_break2out='".$csv[$i]['break2Lunes'].":00"."' ";
				$break2EntradaLunes = " , sch_break2in='".date("H:i:s",(strtotime($csv[$i]['break2Lunes'].":00 + 15 Minutes")))."' ";
			}
		}	
		
		//Horario para martes
		$horarioMartes = explode("-",$csv[$i]['horarioMartes']);
		if($horarioMartes[0]=="OFF"){
			$dayOffMartes = true;
		}
		else{
			
			$entradaMartes = " , sch_entry=NULL ";
			$salidaMartes = " , sch_departure = NULL ";
			
			$break1SalidaMartes = " , sch_break1out=NULL ";
			$break1EntradaMartes = " , sch_break1in=NULL ";
			$lunchSalidaMartes = " , sch_lunchout=NULL ";
			$lunchEntradaMartes = " , sch_lunchin=NULL ";
			$break2SalidaMartes = " , sch_break2out=NULL ";
			$break2EntradaMartes = " , sch_break2in=NULL ";
			
			if(strlen($csv[$i]['horarioMartes']>0)){
				$entradaMartes = " , sch_entry= '".$horarioMartes[0].":00"."'";
				$salidaMartes = " , sch_departure = '".$horarioMartes[1].":00"."' ";
			}
			
			if(strlen($csv[$i]['break1Martes'])>0){
				$break1SalidaMartes = " , sch_break1out= '".$csv[$i]['break1Martes'].":00"."' ";
				$break1EntradaMartes = " , sch_break1in= '".date("H:i:s",(strtotime($csv[$i]['break1Martes'].":00 + 15 Minutes")))."' ";
			}
			if(strlen($csv[$i]['lunchMartes'])>0){
				$lunchSalidaMartes = " , sch_lunchout='".$csv[$i]['lunchMartes'].":00"."' ";
				$lunchEntradaMartes = " , sch_lunchin='".date("H:i:s", strtotime($csv[$i]['lunchMartes'].":00 + 1 Hours"))."' ";
			}
			if(strlen($csv[$i]['break2Martes'])>0){
				$break2SalidaMartes = " , sch_break2out='".$csv[$i]['break2Martes'].":00"."' ";
				$break2EntradaMartes = " , sch_break2in='".date("H:i:s",(strtotime($csv[$i]['break2Martes'].":00 + 15 Minutes")))."' ";
			}
		}
		
		//Horario de miercoles
		$horarioMiercoles = explode("-",$csv[$i]['horarioMierc']);
		if($horarioMiercoles[0]=="OFF"){
			$dayOffMiercoles = true;
		}
		else{
			$entradaMiercoles = " , sch_entry=NULL ";
			$salidaMiercoles = " , sch_departure = NULL ";

			$break1SalidaMiercoles = " , sch_break1out=NULL ";
			$break1EntradaMiercoles = " , sch_break1in=NULL ";
			$lunchSalidaMiercoles = " , sch_lunchout=NULL ";
			$lunchEntradaMiercoles = " , sch_lunchin=NULL ";
			$break2SalidaMiercoles = " , sch_break2out=NULL ";
			$break2EntradaMiercoles = " , sch_break2in=NULL ";
			
			if(strlen($csv[$i]['horarioMierc']>0)){
				$entradaMiercoles = " , sch_entry= '".$horarioMiercoles[0].":00"."'";
				$salidaMiercoles = " , sch_departure = '".$horarioMiercoles[1].":00"."' ";
			}
			
			if(strlen($csv[$i]['break1Mierc'])>0){
				$break1SalidaMiercoles = " , sch_break1out= '".$csv[$i]['break1Mierc'].":00"."' ";
				$break1EntradaMiercoles = " , sch_break1in= '".date("H:i:s",(strtotime($csv[$i]['break1Mierc'].":00 + 15 Minutes")))."' ";
			}
			if(strlen($csv[$i]['lunchMierc'])>0){
				$lunchSalidaMiercoles = " , sch_lunchout='".$csv[$i]['lunchMierc'].":00"."' ";
				$lunchEntradaMiercoles = " , sch_lunchin='".date("H:i:s",strtotime($csv[$i]['lunchMierc'].":00 + 1 Hours"))."' ";
			}
			if(strlen($csv[$i]['break2Mierc'])>0){
				$break2SalidaMiercoles = " , sch_break2out='".$csv[$i]['break2Mierc'].":00"."' ";
				$break2EntradaMiercoles = " , sch_break2in='".date("H:i:s",(strtotime($csv[$i]['break2Mierc'].":00 + 15 Minutes")))."' ";
			}
		}
		
		//Horario de Jueves
		$horarioJueves = explode("-",$csv[$i]['horarioJueves']);
		if($horarioJueves[0]=='OFF'){
			$dayOffJueves = true;
		}
		else{
			$entradaJueves = " , sch_entry=NULL ";
			$salidaJueves = " , sch_departure = NULL ";
			
			$break1SalidaJueves = " , sch_break1out=NULL ";
			$break1EntradaJueves = " , sch_break1in=NULL ";
			$lunchSalidaJueves = " , sch_lunchout=NULL ";
			$lunchEntradaJueves = " , sch_lunchin=NULL ";
			$break2SalidaJueves = " , sch_break2out=NULL ";
			$break2EntradaJueves = " , sch_break2in=NULL ";
			
			if(strlen($csv[$i]['horarioJueves']>0)){
				$entradaJueves = " , sch_entry= '".$horarioJueves[0].":00"."'";
				$salidaJueves = " , sch_departure = '".$horarioJueves[1].":00"."' ";
			}
			
			if(strlen($csv[$i]['break1Jueves'])>0){
				$break1SalidaJueves = " , sch_break1out= '".$csv[$i]['break1Jueves'].":00"."' ";
				$break1EntradaJueves = " , sch_break1in= '".date("H:i:s",(strtotime($csv[$i]['break1Jueves'].":00 + 15 Minutes")))."' ";
			}
			if(strlen($csv[$i]['lunchJueves'])>0){
				$lunchSalidaJueves = " , sch_lunchout='".$csv[$i]['lunchJueves'].":00"."' ";
				$lunchEntradaJueves = " , sch_lunchin='".date("H:i:s",strtotime($csv[$i]['lunchJueves'].":00 + 1 Hours"))."' ";
			}
			if(strlen($csv[$i]['break2Jueves'])>0){
				$break2SalidaJueves = " , sch_break2out='".$csv[$i]['break2Jueves'].":00"."' ";
				$break2EntradaJueves = " , sch_break2in='".date("H:i:s",(strtotime($csv[$i]['break2Jueves'].":00 + 15 Minutes")))."' ";
			}
		}
		
		//Horario de Viernes
		$horarioViernes = explode("-",$csv[$i]['horarioViernes']);
		if($horarioViernes[0]=='OFF'){
			$dayOffViernes = true;
		}
		else{
			$entradaViernes = " , sch_entry=NULL ";
			$salidaViernes = " , sch_departure = NULL ";
			
			$break1SalidaViernes = " , sch_break1out=NULL ";
			$break1EntradaViernes = " , sch_break1in=NULL ";
			$lunchSalidaViernes = " , sch_lunchout=NULL ";
			$lunchEntradaViernes = " , sch_lunchin=NULL ";
			$break2SalidaViernes = " , sch_break2out=NULL ";
			$break2EntradaViernes = " , sch_break2in=NULL ";
			
			if(strlen($csv[$i]['horarioViernes']>0)){
				$entradaViernes = " , sch_entry= '".$horarioViernes[0].":00"."'";
				$salidaViernes = " , sch_departure = '".$horarioViernes[1].":00"."' ";
			}
			
			if(strlen($csv[$i]['break1Viernes'])>0){
				$break1SalidaViernes = " , sch_break1out= '".$csv[$i]['break1Viernes'].":00"."' ";
				$break1EntradaViernes = " , sch_break1in= '".date("H:i:s",(strtotime($csv[$i]['break1Viernes'].":00 + 15 Minutes")))."' ";
			}
			if(strlen($csv[$i]['lunchViernes'])>0){
				$lunchSalidaViernes = " , sch_lunchout='".$csv[$i]['lunchViernes'].":00"."' ";
				$lunchEntradaViernes = " , sch_lunchin='".date("H:i:s",strtotime($csv[$i]['lunchViernes'].":00 + 1 Hours"))."' ";
			}
			if(strlen($csv[$i]['break2Viernes'])>0){
				$break2SalidaViernes = " , sch_break2out='".$csv[$i]['break2Viernes'].":00"."' ";
				$break2EntradaViernes = " , sch_break2in='".date("H:i:s",(strtotime($csv[$i]['break2Viernes'].":00 + 15 Minutes")))."' ";
			}
		}
		
		//Horario de Sabado
		$horarioSabado = explode("-",$csv[$i]['horarioSabado']);
		if($horarioSabado[0]=='OFF'){
			$dayOffSabado = true;
		}
		else{
			$entradaSabado = " , sch_entry=NULL ";
			$salidaSabado = " , sch_departure = NULL ";
			
			$break1SalidaSabado = " , sch_break1out=NULL ";
			$break1EntradaSabado = " , sch_break1in=NULL ";
			$lunchSalidaSabado = " , sch_lunchout=NULL ";
			$lunchEntradaSabado = " , sch_lunchin=NULL ";
			$break2SalidaSabado = " , sch_break2out=NULL ";
			$break2EntradaSabado = " , sch_break2in=NULL ";
			
			if(strlen($csv[$i]['horarioSabado']>0)){
				$entradaSabado = " , sch_entry= '".$horarioSabado[0].":00"."'";
				$salidaSabado = " , sch_departure = '".$horarioSabado[1].":00"."' ";
			}
			
			if(strlen($csv[$i]['break1Sabado'])>0){
				$break1SalidaSabado = " , sch_break1out= '".$csv[$i]['break1Sabado'].":00"."' ";
				$break1EntradaSabado = " , sch_break1in= '".date("H:i:s",(strtotime($csv[$i]['break1Sabado'].":00 + 15 Minutes")))."' ";
			}
			if(strlen($csv[$i]['lunchSabado'])>0){
				$lunchSalidaSabado = " , sch_lunchout='".$csv[$i]['lunchSabado'].":00"."' ";
				$lunchEntradaSabado = " , sch_lunchin='".date("H:i:s",strtotime($csv[$i]['lunchSabado'].":00 + 1 Hours"))."' ";
			}
			if(strlen($csv[$i]['break2Sabado'])>0){
				$break2SalidaSabado = " , sch_break2out='".$csv[$i]['break2Sabado'].":00"."' ";
				$break2EntradaSabado = " , sch_break2in='".date("H:i:s",(strtotime($csv[$i]['break2Sabado'].":00 + 15 Minutes")))."' ";
			}
		}
		
		//Horario de Domingo
		$horarioDomingo = explode("-",$csv[$i]['horarioDomingo']);
		if($horarioDomingo[0]=='OFF'){
			$dayOffDomingo = true;
		}
		else{
			$entradaDomingo = " , sch_entry=NULL ";
			$salidaDomingo = " , sch_departure = NULL ";
			
			$break1SalidaDomingo = " , sch_break1out=NULL ";
			$break1EntradaDomingo = " , sch_break1in=NULL ";
			$lunchSalidaDomingo = " , sch_lunchout=NULL ";
			$lunchEntradaDomingo = " , sch_lunchin=NULL ";
			$break2SalidaDomingo = " , sch_break2out=NULL ";
			$break2EntradaDomingo = " , sch_break2in=NULL ";
			
			if(strlen($csv[$i]['horarioDomingo']>0)){
				$entradaDomingo = " , sch_entry= '".$horarioDomingo[0].":00"."'";
				$salidaDomingo = " , sch_departure = '".$horarioDomingo[1].":00"."' ";
			}
			
			if(strlen($csv[$i]['break1Domingo'])>0){
				$break1SalidaDomingo = " , sch_break1out= '".$csv[$i]['break1Domingo'].":00"."' ";
				$break1EntradaDomingo = " , sch_break1in= '".date("H:i:s",(strtotime($csv[$i]['break1Domingo'].":00 + 15 Minutes")))."' ";
			}
			if(strlen($csv[$i]['lunchDomingo'])>0){
				$lunchSalidaDomingo = " , sch_lunchout='".$csv[$i]['lunchDomingo'].":00"."' ";
				$lunchEntradaDomingo = " , sch_lunchin='".date("H:i:s",strtotime($csv[$i]['lunchDomingo'].":00 + 1 Hours"))."' ";
			}
			if(strlen($csv[$i]['break2Domingo'])>0){
				$break2SalidaDomingo = " , sch_break2out='".$csv[$i]['break2Domingo'].":00"."' ";
				$break2EntradaDomingo = " , sch_break2in='".date("H:i:s",(strtotime($csv[$i]['break2Domingo'].":00 + 15 Minutes")))."' ";
			}
		}
		
		//Ingresa horario para lunes
		//$fecha = $start;
		$fecha = $start;
		$sqlText = "select SCH_ID from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".date("Y-m-d",$fecha)."'";
		$dtSch = $dbEx->selSql($sqlText);
			
		if($dbEx->numrows>0){
			if($dayOffLunes){
				$sqlText = "update schedules set sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
			else{
			    $sqlText = "update schedules set sch_off='N' ".$entradaLunes." ".$break1SalidaLunes." ".$break1EntradaLunes." ".$lunchSalidaLunes." ".$lunchEntradaLunes." ".
				 $break2SalidaLunes." ".$break2EntradaLunes." ".$salidaLunes." where sch_id=".$dtSch['0']['SCH_ID'];

				$dbEx->updSql($sqlText);
			}
		}
		else{
			if($dayOffLunes){
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' ";
				$dbEx->insSql($sqlText);
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."' ".$entradaLunes." ".$break1SalidaLunes." ".$break1EntradaLunes." ".$lunchSalidaLunes." ".$lunchEntradaLunes." ".$break2SalidaLunes." ".$break2EntradaLunes." ".$salidaLunes.", sch_off='N'";
				$dbEx->insSql($sqlText);	
			}
		}
		
		//Ingresa horario para Martes
		//$fecha = $start + 86400;
		$fecha = strtotime($fecha . ' +1 day');
		$sqlText = "select SCH_ID from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".date("Y-m-d",$fecha)."'";
		$dtSch = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			if($dayOffMartes){
				$sqlText = "update schedules set sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
			else{
				$sqlText = "update schedules set sch_off='N' ".$entradaMartes." ".$break1SalidaMartes." ".$break1EntradaMartes." ".$lunchSalidaMartes." ".$lunchEntradaMartes." ".$break2SalidaMartes." ".$break2EntradaMartes." ".$salidaMartes." where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
		}
		else{
			if($dayOffMartes){
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' ";
				$dbEx->insSql($sqlText);
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."' ".$entradaMartes."' ".$break1SalidaMartes." ".$break1EntradaMartes." ".$lunchSalidaMartes." ".$lunchEntradaMartes." ".$break2SalidaMartes." ".$break2EntradaMartes." ".$salidaMartes.", sch_off='N'";
				$dbEx->insSql($sqlText);	
			}
		}
		
		//Ingresa horario para Miercoles
		//$fecha = $fecha + 86400;
		$fecha = strtotime($fecha . ' +1 day');
		$sqlText = "select SCH_ID from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".date("Y-m-d",$fecha)."'";
		$dtSch = $dbEx->selSql($sqlText);
			
		if($dbEx->numrows>0){
			if($dayOffMiercoles){
				$sqlText = "update schedules set sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
			else{
				$sqlText = "update schedules set sch_off='N' ".$entradaMiercoles." ".$break1SalidaMiercoles." ".$break1EntradaMiercoles." ".$lunchSalidaMiercoles." ".$lunchEntradaMiercoles." ".$break2SalidaMiercoles." ".$break2EntradaMiercoles." ".$salidaMiercoles." where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
		}
		else{
			if($dayOffMiercoles){
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' ";
				$dbEx->insSql($sqlText);
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."' ".$entradaMiercoles." ".$break1SalidaMiercoles." ".$break1EntradaMiercoles." ".$lunchSalidaMiercoles." ".$lunchEntradaMiercoles." ".$break2SalidaMiercoles." ".$break2EntradaMiercoles." ".$salidaMiercoles.", sch_off='N'";
				$dbEx->insSql($sqlText);	
			}
		}
		
		//Ingresa horario para Jueves
		//$fecha = $fecha + 86400;
		$fecha = strtotime($fecha . ' +1 day');
		$sqlText = "select SCH_ID from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".date("Y-m-d",$fecha)."'";
		$dtSch = $dbEx->selSql($sqlText);
			
		if($dbEx->numrows>0){
			if($dayOffJueves){
				$sqlText = "update schedules set sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
			else{
				$sqlText = "update schedules set sch_off='N' ".$entradaJueves." ".$break1SalidaJueves." ".$break1EntradaJueves." ".$lunchSalidaJueves." ".$lunchEntradaJueves." ".$break2SalidaJueves." ".$break2EntradaJueves." ".$salidaJueves." where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
		}
		else{
			if($dayOffJueves){
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' ";
				$dbEx->insSql($sqlText);
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."' ".$entradaJueves." ".$break1SalidaJueves." ".$break1EntradaJueves." ".$lunchSalidaJueves." ".$lunchEntradaJueves." ".$break2SalidaJueves." ".$break2EntradaJueves." ".$salidaJueves.", sch_off='N'";
				$dbEx->insSql($sqlText);	
			}
		}
		
		
		//Ingresa horario para Viernes
		//$fecha = $fecha + 86400;
		$fecha = strtotime($fecha . ' +1 day');
		$sqlText = "select SCH_ID from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".date("Y-m-d",$fecha)."'";
		$dtSch = $dbEx->selSql($sqlText);
			
		if($dbEx->numrows>0){
			if($dayOffViernes){
				$sqlText = "update schedules set sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
			else{
				$sqlText = "update schedules set sch_off='N' ".$entradaViernes." ".$break1SalidaViernes." ".$break1EntradaViernes." ".$lunchSalidaViernes." ".$lunchEntradaViernes." ".$break2SalidaViernes." ".$break2EntradaViernes." ".$salidaViernes."  where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
		}
		else{
			if($dayOffViernes){
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' ";
				$dbEx->insSql($sqlText);
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."' ".$entradaViernes." ".$break1SalidaViernes." ".$break1EntradaViernes." ".$lunchSalidaViernes." ".$lunchEntradaViernes." ".$break2SalidaViernes." ".$break2EntradaViernes." ".$salidaViernes.", sch_off='N'";
				$dbEx->insSql($sqlText);	
			}
		}
		
		//Ingresa horario para Sabado
		//$fecha = $fecha + 86400;
		$fecha = strtotime($fecha . ' +1 day');
		$sqlText = "select SCH_ID from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".date("Y-m-d",$fecha)."'";
		$dtSch = $dbEx->selSql($sqlText);
			
		if($dbEx->numrows>0){
			if($dayOffSabado){
				$sqlText = "update schedules set sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
			else{
				$sqlText = "update schedules set sch_off='N' ".$entradaSabado." ".$break1SalidaSabado." ".$break1EntradaSabado." ".$lunchSalidaSabado." ".$lunchEntradaSabado." ".$break2SalidaSabado." ".$break2EntradaSabado." ".$salidaSabado."  where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
		}
		else{
			if($dayOffSabado){
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' ";
				$dbEx->insSql($sqlText);
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."' ".$entradaSabado." ".$break1SalidaSabado." ".$break1EntradaSabado." ".$lunchSalidaSabado." ".$lunchEntradaSabado." ".$break2SalidaSabado." ".$break2EntradaSabado." ".$salidaSabado.", sch_off='N'";
				$dbEx->insSql($sqlText);	
			}
		}
		
		
		//Ingresa horario para Domingo
		//$fecha = $fecha + 86400;
		$fecha = strtotime($fecha . ' +1 day');
		$sqlText = "select SCH_ID from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".date("Y-m-d",$fecha)."'";
		$dtSch = $dbEx->selSql($sqlText);
			
		if($dbEx->numrows>0){
			if($dayOffDomingo){
				$sqlText = "update schedules set sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
			else{
				$sqlText = "update schedules set sch_off='N' ".$entradaDomingo." ".$break1SalidaDomingo." ".$break1EntradaDomingo." ".$lunchSalidaDomingo." ".$lunchEntradaDomingo." ".$break2SalidaDomingo." ".$break2EntradaDomingo." ".$salidaDomingo."  where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			}
		}
		else{
			if($dayOffDomingo){
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."', sch_entry=NULL, sch_break1out=NULL, sch_break1in=NULL, sch_lunchout=NULL, sch_lunchin=NULL, sch_break2out=NULL, sch_break2in=NULL, sch_departure=NULL, sch_off='Y' ";
				$dbEx->insSql($sqlText);
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".date("Y-m-d",$fecha)."' ".$entradaDomingo." ".$break1SalidaDomingo." ".$break1EntradaDomingo." ".$lunchSalidaDomingo." ".$lunchEntradaDomingo." ".$break2SalidaDomingo." ".$break2EntradaDomingo." ".$salidaDomingo.", sch_off='N'";
				$dbEx->insSql($sqlText);	
			}
		}
		
		/*
		//Variable para indicar si se hace el registro, true=insert, false=pasa a siguiente
		$flag = true;
		
		//Valida los datos de hora ingresados
		
		if(strtotime($entrada)>= strtotime($salida)){
			$flag = false;
		}
		
		if(strtotime($break1Salida)>= strtotime($break1Entrada)){
			$flag = false;	
		}
		else if(strtotime($entrada)>= strtotime($break1Salida)){
			$flag = false;
		}
		if((strtotime($lunchSalida)<= strtotime($break1Entrada)) and (strtotime($lunchSalida)>= strtotime($break1Salida))){
			$flag = false;
		}
		if(strtotime($lunchSalida)>=strtotime($lunchEntrada)){
			$flag = false;
		}
		if(strtotime($break2Salida)>=strtotime($break2Entrada)){
			$flag = false;
		}
		if((strtotime($break2Salida)<= strtotime($lunchEntrada)) and (strtotime($break2Salida)>=strtotime($lunchSalida))){
			$flag = false;
		}
		if((strtotime($break2Salida)<= strtotime($break1Entrada)) and (strtotime($break2Salida)>=strtotime($break1Salida))){
			$flag = false;
		}
		
		if($flag){
		
			//Verificar si no existe horario para ese dia y sino lo cambiamos
			$sqlText = "select * from schedules where employee_id=".$dtE['0']['employee_id']." and sch_date='".$fecha."'";
			$dtSch = $dbEx->selSql($sqlText);
			
			if($dbEx->numrows>0){
				$sqlText = "update schedules set sch_entry='".$entrada."', sch_break1out='".$break1Salida."', sch_break1in='".$break1Entrada."', sch_lunchout='".$lunchSalida."', sch_lunchin='".$lunchEntrada."', sch_break2out='".$break2Salida."', sch_break2in='".$break2Entrada."', sch_departure='".$salida."', sch_off='N' where sch_id=".$dtSch['0']['SCH_ID'];
				$dbEx->updSql($sqlText);
			
			}
			else{
				$sqlText = "insert into schedules set employee_id=".$dtE['0']['employee_id'].", sch_date='".$fecha."', sch_entry='".$entrada."', sch_break1out='".$break1Salida."', sch_break1in='".$break1Entrada."', sch_lunchout='".$lunchSalida."', sch_lunchin='".$lunchEntrada."', sch_break2out='".$break2Salida."', sch_break2in='".$break2Entrada."', sch_departure='".$salida."', sch_off='N'";
				$dbEx->insSql($sqlText);	
			}	
		}
		*/
		
	}//Termina numrows
	$rslt = 2;	
}//termina for

}//Termina else
	
	if($rslt ==2){
		echo '<script>alert("Upload successfully");window.parent.loadPageSchedules()</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again");window.parent.loadPageSchedules();</script>';	
	}



?>
