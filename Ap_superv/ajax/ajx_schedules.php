<?php
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
 
$dbEx = new DBX;
$oFec = new OFECHA;
  function cargaPag($urlToLoad){ //funcion para cargar una pagina
    $pagLoad = '';
    $fp=fopen($urlToLoad,"r") or die("Error al abrir el fichero");
    $pagLoad = fread($fp,30000);
    return $pagLoad;
  }
  function zerofill($entero, $largo){
    // Limpiamos por si se encontraran errores de tipo en las variables
    $entero = (int)$entero;
    $largo = (int)$largo;
     
    $relleno = '';
     
    /**
     * Determinamos la cantidad de caracteres utilizados por $entero
     * Si este valor es mayor o igual que $largo, devolvemos el $entero
     * De lo contrario, rellenamos con ceros a la izquierda del número
     **/
    if (strlen($entero) < $largo){
		$valor = $largo - strlen($entero);
        $relleno = str_repeat('0', $valor);
    }
    return $relleno . $entero;
}
function sumarHoras($h1,$h2)
{
    $dbExec = new DBX;
	$sqlText = "select sec_to_time((time_to_sec('".$h1."') + time_to_sec('".$h2."'))) result from dual";
	$result = $dbExec->selSql($sqlText);
	return $result['0']['result'];
}

switch($_POST['Do']){
	case 'newHorario':
		$rslt = cargaPag("../schedules/formNuevoHorario.php");
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and user_status=1 and pe.status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = '<option value="0"></option>';
		if($dbEx->numrows>0){
			$optEmp = "";
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';
			}
		}
		
		$optHora = "";
		for($i=0;$i<=23; $i++){
			$n = zerofill($i, 2);
			$optHora .='<option value="'.$n.'">'.$n.'</option>';
		}
		$optMinutos = "";
		for($i=0; $i<=59;$i++){
			$n = zerofill($i, 2);
			$optMinutos .='<option value="'.$n.'">'.$n.'</option>';	
		}
		$rslt = str_replace("<!--optEmployees-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optHora-->",$optHora,$rslt);
		$rslt = str_replace("<!--optMinutos-->",$optMinutos,$rslt);
		echo $rslt;
	break;
	
	case 'saveHorario':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$arrayAgentes = $_POST['arrayAgentes'];
		$agentes = explode(" ",$arrayAgentes);
		$n = count($agentes);
		$entrada = $_POST['entradaHora'].":".$_POST['entradaMinutos'].":00";
		$break1Entrada = $_POST['break1EntradaHora'].":".$_POST['break1EntradaMinutos'].":00";
		$break1Salida = $_POST['break1SalidaHora'].":".$_POST['break1SalidaMinutos'].":00";
		$lunchEntrada = $_POST['lunchEntradaHora'].":".$_POST['lunchEntradaMinutos'].":00";
		$lunchSalida = $_POST['lunchSalidaHora'].":".$_POST['lunchSalidaMinutos'].":00";
		$break2Entrada = $_POST['break2EntradaHora'].":".$_POST['break2EntradaMinutos'].":00";
		$break2Salida = $_POST['break2SalidaHora'].":".$_POST['break2SalidaMinutos'].":00";
		$salida = $_POST['salidaHora'].":".$_POST['salidaMinutos'].":00";
		
		//Valida las horas ingresadas si Day Off no esta seleccionado
	if($_POST['offCheck']!=1){
		
		if(strtotime($entrada)>= strtotime($salida)){
			echo "2";
			break;
		}
		/*
		if(strtotime($break1Entrada)>= strtotime($break1Salida)){
			echo "3";
			break;	
		}
		else if(strtotime($entrada)>= strtotime($break1Entrada)){
			echo "4";
			break;
		}
		if((strtotime($lunchEntrada)<= strtotime($break1Salida)) and (strtotime($lunchEntrada)>= strtotime($break1Entrada))){
			echo "5";
			break;
		}
		if(strtotime($lunchEntrada)>=strtotime($lunchSalida)){
			echo "6";
			break;
		}
		if(strtotime($break2Entrada)>=strtotime($break2Salida)){
			echo "7";
			break;
		}
		if((strtotime($break2Entrada)<= strtotime($lunchSalida)) and (strtotime($break2Entrada)>=strtotime($lunchEntrada))){
			echo "8";
			break;
		}
		if((strtotime($break2Entrada)<= strtotime($break1Salida)) and (strtotime($break2Entrada)>=strtotime($break1Entrada))){
			echo "9";
			break;
		}
		*/
	}
		
		//Si es dia libre todos los horarios se ponen a 0 y dia libre a Yes
		//Verifica para la primera fecha
		if($_POST['offCheck']==1){
			for($i = 0; $i<$n; $i++){
				$sqlText = "select * from schedules where sch_date='".$fecha."' and employee_id=".$agentes[$i];
				$dtHorario = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$sqlText = "update schedules set sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtHorario['0']['SCH_ID'];	
					$dbEx->updSql($sqlText);
				}
				else{
					$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha."', sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y'";	
					$dbEx->insSql($sqlText);
				}
			}
		}
		//Si no es dia libre se escriben todas las horas 
		else{
			for($i = 0; $i<$n; $i++){
				$sqlText = "select * from schedules where sch_date='".$fecha."' and employee_id=".$agentes[$i];
				$dtHorario = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$sqlText = "update schedules set sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N' where sch_id=".$dtHorario['0']['SCH_ID'];
					$dbEx->updSql($sqlText);
				}
				else{
					$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha."', sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N'";	
					$dbEx->insSql($sqlText);
				}
			}
		}
		//Verifica los datos para la segunda fecha
		if(strlen($_POST['fecha2'])>0){
			$fecha2 = $oFec->cvDtoY($_POST['fecha2']);
			if($_POST['offCheck']==1){
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha2."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtHorario['0']['SCH_ID'];	
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha2."', sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
			else{
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha2."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N' where sch_id=".$dtHorario['0']['SCH_ID'];
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha2."', sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
		}
		
		//verfica datos para la tercera fecha
		if(strlen($_POST['fecha3'])>0){
			$fecha3 = $oFec->cvDtoY($_POST['fecha3']);
			if($_POST['offCheck']==1){
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha3."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtHorario['0']['SCH_ID'];	
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha3."', sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
			else{
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha3."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N' where sch_id=".$dtHorario['0']['SCH_ID'];
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha3."', sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
		}
		
		//verifica datos para la cuarta fecha
		if(strlen($_POST['fecha4'])>0){
			$fecha4 = $oFec->cvDtoY($_POST['fecha4']);
			if($_POST['offCheck']==1){
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha4."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y' where sch_id=".$dtHorario['0']['SCH_ID'];	
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha4."', sch_entry=NULL, sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
			else{
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha4."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N' where sch_id=".$dtHorario['0']['SCH_ID'];
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha4."', sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
		}

		//verifica datos para quinta fecha
		if(strlen($_POST['fecha5'])>0){
			$fecha5 = $oFec->cvDtoY($_POST['fecha5']);
			if($_POST['offCheck']==1){
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha5."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry='', sch_break1in='', sch_break1out='', sch_lunchin='', sch_lunchout='', sch_break2in='', sch_break2out='', sch_departure='', sch_off='Y' where sch_id=".$dtHorario['0']['SCH_ID'];	
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha5."', sch_entry='', sch_break1in=NULL, sch_break1out=NULL, sch_lunchin=NULL, sch_lunchout=NULL, sch_break2in=NULL, sch_break2out=NULL, sch_departure=NULL, sch_off='Y'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
			else{
				for($i = 0; $i<$n; $i++){
					$sqlText = "select * from schedules where sch_date='".$fecha5."' and employee_id=".$agentes[$i];
					$dtHorario = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "update schedules set sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N' where sch_id=".$dtHorario['0']['SCH_ID'];
						$dbEx->updSql($sqlText);
					}
					else{
						$sqlText = "insert into schedules set employee_id=".$agentes[$i].", sch_date='".$fecha5."', sch_entry='".$entrada."', sch_break1out='".$break1Entrada."', sch_break1in='".$break1Salida."', sch_lunchout='".$lunchEntrada."', sch_lunchin='".$lunchSalida."', sch_break2out='".$break2Entrada."', sch_break2in='".$break2Salida."', sch_departure='".$salida."', sch_off='N'";	
						$dbEx->insSql($sqlText);
					}
				}
			}
		}
		
		
		echo "1";
		
		
	break;
	
	case 'getDetalleHorario':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$nFecha = strtotime(date("Y/m/d",(strtotime($fecha))));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		$restar = 0;
		if($dia==0){
			$restar = 6;
			
		}
		else if($dia == 1){
			$restar = 0;	
		}
		else if($dia == 2){
			$restar = 1;	
		}
		else if($dia == 3){
			$restar = 2;	
		}
		else if($dia == 4){
			$restar = 3;	
		}
		else if($dia == 5){
			$restar = 4;	
		}
		else if($dia == 6){
			$restar = 5;	
		}
		$sumar = 0;
		$lunesSql = date("Y-m-d", strtotime("$fecha - $restar day"));
		$lunes = date("d/m/Y", strtotime("$fecha - $restar day"));
		
		$martesSql = date("Y-m-d", strtotime("$lunesSql + 1 day"));
		$martes = date("d/m/Y", strtotime("$lunesSql + 1 day"));
		
		$miercolesSql = date("Y-m-d",strtotime("$lunesSql + 2 day"));
		$miercoles = date("d/m/Y", strtotime("$lunesSql+ 2 day"));
		
		$juevesSql = date("Y-m-d",strtotime("$lunesSql + 3 day"));
		$jueves = date("d/m/Y", strtotime("$lunesSql + 3 day"));
		
		$viernesSql = date("Y-m-d",strtotime("$lunesSql + 4 day"));
		$viernes = date("d/m/Y", strtotime("$lunesSql + 4 day"));
		
		$sabadoSql = date("Y-m-d",strtotime("$lunesSql + 5 day"));
		$sabado = date("d/m/Y", strtotime("$lunesSql + 5 day"));
		
		$domingoSql = date("Y-m-d",strtotime("$lunesSql + 6 day")); 
		$domingo = date("d/m/Y", strtotime("$lunesSql + 6 day"));
		
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where pe.status_plxemp='A' and user_status=1 and (name_role='AGENTE' or name_role='SUPERVISOR') order by firstname";
		
		$dtEmp = $dbEx->selSql($sqlText);
		$tblResult = '<table width="900" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">';
		$tblResult .='<tr bgcolor="#FFFFFF"><font color="#003366">
			<td>Badge</td>
			<td>Employee</td>
			<td>Lunes '.$lunes.'</td>
			<td>Martes '.$martes.'</td>
			<td>Miercoles '.$miercoles.'</td>
			<td>Jueves '.$jueves.'</td>
			<td>Viernes '.$viernes.'</td>
			<td>Sabado '.$sabado.'</td>
			<td>Domingo '.$domingo.'</td></tr>';
		foreach($dtEmp as $dtE){
			
			//Verifica para cada agente si tiene horario para lunes
			$sqlText = "select * from schedules where employee_id=".$dtE['employee_id']." and sch_date='".$lunesSql."'";
			$dtSch = $dbEx->selSql($sqlText);
			$horaLunes = " - ";
			if($dbEx->numrows>0){
				if($dtSch['0']['SCH_OFF']=='Y'){
					$horaLunes = "OFF";
				}
				else{
					$horaLunes = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
					$horaLunes .='<tr><td class="txtForm">Time of entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
					$horaLunes .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';	
					$horaLunes .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
					$horaLunes .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
					$horaLunes .='<tr><td class="txtForm">End of Duty </td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE']."</td></tr></table>";
				}
			}
			
			//Verifica para cada agente si tiene horario para martes
			$sqlText = "select * from schedules where employee_id=".$dtE['employee_id']." and sch_date='".$martesSql."'";
			$dtSch = $dbEx->selSql($sqlText);
			$horaMartes = " - ";
			if($dbEx->numrows>0){
				if($dtSch['0']['SCH_OFF']=='Y'){
					$horaMartes = "OFF";
				}
				else{
					$horaMartes = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
					$horaMartes .='<tr><td class="txtForm">Time of entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
					$horaMartes .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';	
					$horaMartes .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
					$horaMartes .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
					$horaMartes .='<tr><td class="txtForm">End of Duty </td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE']."</td></tr></table>";
				}
			}
			
			//Verifica para cada agente si tiene horario para miercoles
			$sqlText = "select * from schedules where employee_id=".$dtE['employee_id']." and sch_date='".$miercolesSql."'";
			$dtSch = $dbEx->selSql($sqlText);
			$horaMiercoles = " - ";
			if($dbEx->numrows>0){
				if($dtSch['0']['SCH_OFF']=='Y'){
					$horaMiercoles = "OFF";
				}
				else{
					$horaMiercoles = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
					$horaMiercoles .='<tr><td class="txtForm">Time of entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
					$horaMiercoles .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';	
					$horaMiercoles .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
					$horaMiercoles .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
					$horaMiercoles .='<tr><td class="txtForm">End of Duty </td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE']."</td></tr></table>";
				}
			}
			
			//Verifica para cada agente si tiene horario para jueves
			$sqlText = "select * from schedules where employee_id=".$dtE['employee_id']." and sch_date='".$juevesSql."'";
			$dtSch = $dbEx->selSql($sqlText);
			$horaJueves = " - ";
			if($dbEx->numrows>0){
				if($dtSch['0']['SCH_OFF']=='Y'){
					$horaJueves = "OFF";
				}
				else{
					$horaJueves = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
					$horaJueves .='<tr><td class="txtForm">Time of entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
					$horaJueves .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';	
					$horaJueves .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
					$horaJueves .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
					$horaJueves .='<tr><td class="txtForm">End of Duty </td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE']."</td></tr></table>";
				}
			}
			
			//Verifica para cada agente si tiene horario para viernes
			$sqlText = "select * from schedules where employee_id=".$dtE['employee_id']." and sch_date='".$viernesSql."'";
			$dtSch = $dbEx->selSql($sqlText);
			$horaViernes = " - ";
			if($dbEx->numrows>0){
				if($dtSch['0']['SCH_OFF']=='Y'){
					$horaViernes = "OFF";
				}
				else{
					$horaViernes = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
					$horaViernes .='<tr><td class="txtForm">Time of entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
					$horaViernes .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';	
					$horaViernes .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
					$horaViernes .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
					$horaViernes .='<tr><td class="txtForm">End of Duty </td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE']."</td></tr></table>";
				}
			}
			
			//Verifica para cada agente si tiene horario para sabado
			$sqlText = "select * from schedules where employee_id=".$dtE['employee_id']." and sch_date='".$sabadoSql."'";
			$dtSch = $dbEx->selSql($sqlText);
			$horaSabado = " - ";
			if($dbEx->numrows>0){
				if($dtSch['0']['SCH_OFF']=='Y'){
					$horaSabado = "OFF";
				}
				else{
					$horaSabado = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
					$horaSabado .='<tr><td class="txtForm">Time of entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
					$horaSabado .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';	
					$horaSabado .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
					$horaSabado .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
					$horaSabado .='<tr><td class="txtForm">End of Duty </td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE']."</td></tr></table>";
				}
			}
			
			//Verifica para cada agente si tiene horario para sabado
			$sqlText = "select * from schedules where employee_id=".$dtE['employee_id']." and sch_date='".$domingoSql."'";
			$dtSch = $dbEx->selSql($sqlText);
			$horaDomingo = " - ";
			if($dbEx->numrows>0){
				if($dtSch['0']['SCH_OFF']=='Y'){
					$horaDomingo = "OFF";
				}
				else{
					$horaDomingo = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
					$horaDomingo .='<tr><td class="txtForm">Time of entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
					$horaDomingo .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';	
					$horaDomingo .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
					$horaDomingo .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
					$horaDomingo .='<tr><td class="txtForm">End of Duty </td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE']."</td></tr></table>";
				}
			}
			
			//Datos de tabla para todos los dias
			$tblResult .='<tr class="rowCons"><td>'.$dtE['username'].'</td>
			<td>'.$dtE['firstname']." ".$dtE['lastname'].'</td>
			<td>'.$horaLunes.'</td>
			<td>'.$horaMartes.'</td>
			<td>'.$horaMiercoles.'</td>
			<td>'.$horaJueves.'</td>
			<td>'.$horaViernes.'</td>
			<td>'.$horaSabado.'</td>
			<td>'.$horaDomingo.'</td></tr>';
		}//Termina foreach
		$tblResult .='</table>';
		echo $tblResult;
	break;
		
	case 'reportHorario':
		if($_POST['opcion']==1){
			$rslt = cargaPag("../schedules/filtrosRepHorario.php");
		}
		else if($_POST['opcion']==2){
			$rslt = cargaPag("../schedules/filtrosPersonaPorHora.php");
		}
			//Obtiene los agentes para filtros
			$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where pe.status_plxemp = 'A' and user_status=1 and (name_role = 'AGENTE' or name_role='SUPERVISOR') order by firstname";
			
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				$optEmp .='<option value="0">[ALL]</option>';
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
				}
			}
			else{
				$optEmp .='<option value="-1">No Employees</option>';	
			}
			
			//Obtiene las cuentas
			$sqlText = "select * from account where id_typeacc=2 and account_status='A' order by name_account ";
			$dtCuenta = $dbEx->selSql($sqlText);
			$optCuenta = "";
			if($dbEx->numrows>0){
				$optCuenta .='<option value="0">[ALL]</option>';
				foreach($dtCuenta as $dtC){
					$optCuenta .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
				}
			}
			else{
				$optCuenta .='<option value="-1">No Accounts</option>';	
			}
			
			//Obtiene los departamentos
			$sqlText ="select distinct(d.id_depart) as id_dep, name_depart from depart_exc d inner join placexdep pd on pd.id_depart=d.id_depart inner join account a on a.id_account=pd.id_account where status_depart = 1 and a.id_typeacc=2 and account_status='A' order by name_depart";
			$dtD = $dbEx->selSql($sqlText);
			
			$optD = '<option value="0">[ALL]</option>';
			foreach($dtD as $dtD){
				$optD .='<option value="'.$dtD['id_dep'].'">'.$dtD['name_depart'].'</option>';	
			}
			
			//Obtiene las plazas
			$sqlText = "select distinct(p.id_place) as id_place, name_place from places p inner join placexdep pd on p.id_place=pd.id_place inner join account a on a.id_account=pd.id_account where a.id_typeacc=2 and account_status='A' order by name_place";
			$dtP = $dbEx->selSql($sqlText);
			$optP ='<option value="0">[ALL]</option>';
			foreach($dtP as $dtP){
				$optP .='<option value="'.$dtP['id_place'].'">'.$dtP['name_place'].'</option>';	
			}
			
			$optHora = "";
			for($i=0;$i<=23; $i++){
				$n = zerofill($i, 2);
				$optHora .='<option value="'.$n.'">'.$n.'</option>';
			}
			$optMinutos = "";
			for($i=0; $i<=59;$i++){
				$n = zerofill($i, 2);
				$optMinutos .='<option value="'.$n.'">'.$n.'</option>';	
			}
			
			//Obtiene los jefes con rol de Supervisor y Gerente de area
			$filtro = "";
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join places p on pd.id_place=p.id_place where pe.status_plxemp='A' and user_status=1 and (name_role='SUPERVISOR' or name_role='GERENTE DE AREA') and name_place!='CLIENT' order by firstname ";
			$dtJefe = $dbEx->selSql($sqlText);
			$optJefe = '<option value="0">[ALL]</option>';
			if($dbEx->numrows>0){
				foreach($dtJefe as $dtJ){
					$optJefe .='<option value="'.$dtJ['employee_id'].'">'.$dtJ['firstname'].' '.$dtJ['lastname'].'</option>';
				}
			}
			
			$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
			$rslt = str_replace("<!--optDepart-->",$optD,$rslt);
			$rslt = str_replace("<!--optPosicion-->",$optP,$rslt);
			$rslt = str_replace("<!--optJefe-->",$optJefe,$rslt);
			$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
			$rslt = str_replace("<!--optHora-->",$optHora,$rslt);
			$rslt = str_replace("<!--optMinutos-->",$optMinutos,$rslt);
			
			echo $rslt;
	break;
	
	//Funcion para mostrar los horarios en un periodo
	case 'loadRepHorario':
		$filtro = " where 1 ";
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depto']>0){
			$filtro .=" and pd.id_depart=".$_POST['depto'];	
		}
		if($_POST['posicion']>0){
			$filtro .=" and pd.id_place=".$_POST['posicion'];
		}
		if($_POST['employee']>0){
			$filtro .=" and e.employee_id=".$_POST['employee'];
		}
		if(strlen($_POST['fechaIni'])>0){
			$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
			$filtro .= " and sch_date between date '".$fechaIni."' and '".$fechaFin."' ";
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (e.firstname like '%".$_POST['nombre']."%' or e.lastname like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and e.username like '%".$_POST['badge']."%' ";
		}
		if($_POST['jefe']>0){
			$filtro .=" and e.id_supervisor=".$_POST['jefe'];
		}
		$sqlText = "select distinct(e.employee_id) emp_id, username, firstname, lastname from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and pe.status_plxemp='A' order by firstname ";
		$dtEmp = $dbEx->selSql($sqlText);
		
		$tblResult = "";
		$tblResult .='<div class="scroll">';
		$tblResult .='<table class="backTablaMain"  bordercolor="#069" align="center" cellpadding="4" cellspacing="4">';
		if($dbEx->numrows>0){
			$start = strtotime($fechaIni);
			$end = strtotime($fechaFin);
			$tblResult .='<tr><td>
			<form target="_blank" action="schedules/xls_schedules.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="fechaIni" value="'.$fechaIni.'">
			<input type="hidden" name="fechaFin" value="'.$fechaFin.'">
			</td></tr>';
			$tblResult .='<thead class="scroll"><tr><td align="center"><b>BADGE</td><td align="center"><b>EMPLOYEE</td>';
			//Primer for solo para mostrar las fechas
			$i = 0;
			while ( strtotime($fechaIni . ' +'.$i.' day') <= $end){
				$tblResult .='<td align="center"><b>'.date("d/m/Y",strtotime($fechaIni . ' +'.$i.' day')).'</td>';
				$i++;
			}
			$tblResult .='</tr></thead>';
			
			//Por empleado busca los horarios del periodo seleccionado
			foreach($dtEmp as $dtE){
				$tblResult .= '<tr class="rowCons"><td align="center">'.$dtE['username'].'</td><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
				$i = 0;
				while ( strtotime($fechaIni . ' +'.$i.' day') <= $end){
					$sqlText = "select time_format(sch_entry,'%H:%i') as SCH_ENTRY, time_format(sch_break1in,'%H:%i') as SCH_BREAK1IN, ".
						"time_format(sch_break1out,'%H:%i') as SCH_BREAK1OUT, time_format(sch_lunchin,'%H:%i') as SCH_LUNCHIN, time_format(sch_lunchout,'%H:%i') as SCH_LUNCHOUT, ".
						"time_format(sch_break2in,'%H:%i') as SCH_BREAK2IN, time_format(sch_break2out,'%H:%i') as SCH_BREAK2OUT, time_format(sch_departure,'%H:%i') as SCH_DEPARTURE, ".
						"SCH_OFF from schedules where employee_id=".$dtE['emp_id']." and sch_date='".date("Y-m-d",strtotime($fechaIni . ' +'.$i.' day'))."'";
						$i++;
					$dtSch = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						if($dtSch['0']['SCH_OFF']=='Y'){
							$hora = "OFF";
						}
						else{
							$hora = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';	
							$hora .='<tr><td class="txtForm">Entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
							$hora .='<tr><td class="txtForm">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';
							$hora .='<tr><td class="txtForm">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
							$hora .='<tr><td class="txtForm">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
							$hora .='<tr><td class="txtForm">End of Duty</td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE'].'</td></tr></table>';
						}
					}
					else{
						$hora = " - ";
					}
					$tblResult .='<td align="center">'.$hora.'</td>';
				}//Termina for
				$tblResult .='</tr>';
			}//Termina foreach de Empleado
		}//Termina de verificar si devolvio agentes
		else{
			$tblResult .='<tr><td>No Matches</td></tr>';
		}
		$tblResult .='</table>';
		$tblResult .='</div>';
		echo $tblResult;
		
	break;	
	
	case 'loadRepPersonasPorHorario':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		$filtroHora = $_POST['hora'].":".$_POST['minutos'].":00";
		$filtro = " where status_plxemp='A' ";
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depto']>0){
			$filtro .=" and pd.id_depart=".$_POST['depto'];	
		}
		if($_POST['posicion']>0){
			$filtro .=" and pd.id_place=".$_POST['posicion'];
		}
		if($_POST['employee']>0){
			$filtro .=" and e.employee_id=".$_POST['employee'];
		}

		$tblResult = "";
		$tblResult .='<div class="scroll">';
		$tblResult .='<table class="backTablaMain"  bordercolor="#069" border="1" style="border-style:groove" align="center" cellpadding="2" cellspacing="2">';
		
			$start = strtotime($fechaIni);
			$end = strtotime($fechaFin);
			$tblResult .='<thead class="scroll"><tr><td></td>';
			//Primer for solo para mostrar las fechas
			$i = 0;
			while ( strtotime($fechaIni . ' +'.$i.' day') <= $end){
				$tblResult .='<td align="center"><b>'.date("d/m/Y",strtotime($fechaIni . ' +'.$i.' day')).'</td>';
				$i++;
			}
			$tblResult .='</tr></thead>';
			
			//Obtiene el total de agentes programados por dia
			$tblResult .='<tr><td><b>TOTAL AGENTS</b></td>';

			$i = 0;
			while ( strtotime($fechaIni . ' +'.$i.' day') <= $end){
				$sqlText = "select count(1) as TotalDia from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and sch_date='".date("Y-m-d",strtotime($fechaIni . ' +'.$i.' day'))."' and sch_off!='Y'";
				$i++;
				$dtTotal = $dbEx->selSql($sqlText);
				$tblResult .='<td align="center"><b>'.$dtTotal['0']['TotalDia'].'</b></td>';
			}
			$tblResult .='</tr>';
			
			$hora = "00:00:00";
			
			for($k=0 ; $k< 48; $k++){
				$tblResult .='<tr><td>'.$hora.'</td>';
				
				$j = 0;
				while ( strtotime($fechaIni . ' +'.$j.' day') <= $end){	
					
					//Transforma la hora para ser pasada al javascript en formato float y luego nuevamente pasarse a formato hora
					$horaFormat = explode(":",$hora);
					$horaFloat = $horaFormat[0].".".$horaFormat[1];
					$totalDispo = 0;
					$tblResult .='<td class="rowCons" onClick="loadDetPersonas('.$j.','.floatval($horaFloat).')">';
					//Total de agentes que se encuentran programados en esa hora
					$sqlText = "select count(1) as cTotal from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and '".$hora."' between sch_entry and sch_departure and sch_date='".date("Y-m-d",strtotime($fechaIni . ' +'.$j.' day'))."'";
					$dtTotal = $dbEx->selSql($sqlText);
					
					//Total de agentes en break a la hora 
					$sqlText = "select count(1) as cBreak from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and (('".$hora."' between sch_break1out and sch_break1in) or ('".$hora."' between sch_break2out and sch_break2in)) and sch_date='".date("Y-m-d",strtotime($fechaIni . ' +'.$j.' day'))."'";
					$dtBreak = $dbEx->selSql($sqlText);
					
					//Total de agentes en lunch
					$sqlText = "select count(1) as cLunch from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and '".$hora."' between sch_lunchout and sch_lunchin and sch_date='".date("Y-m-d",strtotime($fechaIni . ' +'.$j.' day'))."'";
					$dtLunch = $dbEx->selSql($sqlText);
					
					$totalDispo = $dtTotal['0']['cTotal'] - $dtBreak['0']['cBreak'] - $dtLunch['0']['cLunch'];
					
					//Verificar si agentes estan offline
					$sqlText ="select * from types_schedules where tpsch_status='A'";
					$dtTpSch = $dbEx->selSql($sqlText);
					foreach($dtTpSch as $dtTp){
						$sqlText = "select count(1) as cActiv from schedulesactiv_emp sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and '".$hora."' between schact_start and schact_end and schact_date='".date("Y-m-d",strtotime($fechaIni . ' +'.$j.' day'))."' and tpsch_id=".$dtTp['TPSCH_ID'];	
						$dtActiv = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){
							$totalDispo = $totalDispo - $dtActiv['0']['cActiv'];
						}
					}					

					
					$tblResult .='
					Available: '.$totalDispo.'<br>
					Lunch: '.$dtLunch['0']['cLunch'].'<br>
					Break: '.$dtBreak['0']['cBreak'].'<br>
					Total: '.$dtTotal['0']['cTotal'].'</td>';
					$j++;
				}
				$hora = sumarHoras($hora,'00:30:00');

				$tblResult .='</tr>';
			}
		$tblResult .='</table>';
		echo $tblResult;
	break;
	
	case 'loadDetPersonas':
		$fecha = date("Y-m-d",$_POST['fecha']);
		$horaFormat = explode(".",$_POST['hora']);
		$totalActivos = 0;
		$totalBreak = 0;
		$totalLunch = 0;
		$tblActivos = "";
		$tblBreak = "";
		$tblLunch = "";
	
		if(count($horaFormat)==1){
			$horaFormat[1]=0;
		}
		$hora = zerofill($horaFormat[0],2).":".zerofill(($horaFormat[1]*10),2).":00";
		
		$filtro = " where status_plxemp='A' ";
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depto']>0){
			$filtro .=" and pd.id_depart=".$_POST['depto'];	
		}
		if($_POST['posicion']>0){
			$filtro .=" and pd.id_place=".$_POST['posicion'];
		}
		if($_POST['employee']>0){
			$filtro .=" and e.employee_id=".$_POST['employee'];
		}
		
		$tblResult .='<table class="backTablaMain"  bordercolor="#069" style="border-style:groove" align="center" cellpadding="2" cellspacing="2">';
		$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="2" align="center"><b><font color="#003366">'.date("d/m/Y",$_POST['fecha']).' '.$hora.'</b></td></tr>';
		
		//Busqueda de agentes disponibles	
		
		$sqlText = "select e.employee_id, username, lastname, firstname 
		from schedules sh inner join employees e on sh.employee_id=e.employee_id 
		inner join plazaxemp pe on pe.employee_id=e.employee_id 
		inner join placexdep pd on pd.id_placexdep=pe.id_placexdep 
		".$filtro." and '".$hora."' between sch_entry and sch_departure and sch_date='".$fecha."' 
		and (sch_break1out is NULL or '".$hora."' not between sch_break1out and sch_break1in) 
		and (sch_break2out is NULL or '".$hora."' not between sch_break2out and sch_break2in) 
		and (sch_lunchout is NULL or '".$hora."' not between sch_lunchout and sch_lunchin) order by firstname";
		$dtDispo = $dbEx->selSql($sqlText);
		
		$totalActivos = $dbEx->numrows;
		
		if($dbEx->numrows>0){
			foreach($dtDispo as $dtD){
				
				//Verifica que no haya actividades para ese dia y hora
				$sqlText = "select count(1) as c from schedulesactiv_emp where schact_date='".$fecha."' and ('".$hora."' between schact_start and schact_end) and employee_id = ".$dtD['employee_id'];
				$dtAct = $dbEx->selSql($sqlText);
				if($dtAct['0']['c']!=NULL and $dtAct['0']['c']>0){
						$totalActivos = $totalActivos - 1;
				}
				else{
					$tblActivos .='<tr><td>'.$dtD['username'].'</td><td>'.$dtD['firstname'].' '.$dtD['lastname'].'</td></tr>';
				}
			}
		}
						
		//Busqueda de agentes en break
		$sqlText = "select username, lastname, firstname 
		from schedules sh inner join employees e on sh.employee_id=e.employee_id
		 inner join plazaxemp pe on pe.employee_id=e.employee_id 
		 inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and (('".$hora."' between sch_break1out and sch_break1in) or ('".$hora."' between sch_break2out and sch_break2in)) and sch_date='".$fecha."'";
		$dtBreak = $dbEx->selSql($sqlText);
		$tblBreak .='<tr bgcolor="#FFFFFF"><td colspan="2"><b>Break: '.$dbEx->numrows.'</td></tr>';
		if($dbEx->numrows>0){
			foreach($dtBreak as $dtB){
				$tblBreak .='<tr><td>'.$dtB['username'].'</td><td>'.$dtB['firstname'].' '.$dtB['lastname'].'</td></tr>';
			}
		}
					
		//Busqueda de agentes en lunch
		$sqlText = "select username, lastname, firstname from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and '".$hora."' between sch_lunchout and sch_lunchin and sch_date='".$fecha."'";
		$dtLunch = $dbEx->selSql($sqlText);
		$tblLunch .='<tr bgcolor="#FFFFFF"><td colspan="2"><b>Lunch: '.$dbEx->numrows.'</td></tr>';
		if($dbEx->numrows>0){
			foreach($dtLunch as $dtL){
				$tblLunch .='<tr><td>'.$dtL['username'].'</td><td>'.$dtL['firstname'].' '.$dtL['lastname'].'</td></tr>';
			}
		}
		
		$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="2"><b>Available: '.$totalActivos.'</td></tr>';
		$tblResult .= $tblActivos;
		$tblResult .= $tblBreak;
		$tblResult .= $tblLunch;
		
		
		//Busqueda de agentes fuera de linea
		$sqlText = "select *  from types_schedules where tpsch_status='A'";
		$dtTpSch = $dbEx->selSql($sqlText);
		foreach($dtTpSch as $dtTp){
			$sqlText ="select username, lastname, firstname from schedulesactiv_emp sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and '".$hora."' between schact_start and schact_end and schact_date='".$fecha."' and tpsch_id=".$dtTp['TPSCH_ID'];
			$dtActiv = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="2"><b>'.$dtTp['TPSCH_NAME'].': '.$dbEx->numrows.'</td></tr>';
				foreach($dtActiv as $dtA){
					$tblResult .='<tr><td>'.$dtA['username'].'</td><td>'.$dtA['firstname'].' '.$dtA['lastname'].'</td></tr>';
				}
			}
		}
		
		
		
		
		echo $tblResult;
		
	break;
	
	case 'HorarioEspecial':
		$rslt = cargaPag("../schedules/formHorarioEspecial.php");
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and user_status=1 and pe.status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = '<option value="0"></option>';
		if($dbEx->numrows>0){
			$optEmp = "";
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';
			}
		}
		
		$optHora = "";
		for($i=0;$i<=23; $i++){
			$n = zerofill($i, 2);
			$optHora .='<option value="'.$n.'">'.$n.'</option>';
		}
		$optMinutos = "";
		for($i=0; $i<=59;$i++){
			$n = zerofill($i, 2);
			$optMinutos .='<option value="'.$n.'">'.$n.'</option>';	
		}
		
		$sqlText = "select * from types_schedules where tpsch_status='A'";
		$dtSch = $dbEx->selSql($sqlText);
		$optSch = "";
		if($dbEx->numrows>0){
			$optSch .='<option value="0">select a type of activity</option>'; 
			foreach($dtSch as $dtS){
				$optSch .='<option value="'.$dtS['TPSCH_ID'].'">'.$dtS['TPSCH_NAME'].'</option>';
			}
		}
		else{
			$optSch .='<option value="-1">Not exist types of activities</option>';
		}
		
		$rslt = str_replace("<!--optEmployees-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optHora-->",$optHora,$rslt);
		$rslt = str_replace("<!--optMinutos-->",$optMinutos,$rslt);
		$rslt = str_replace("<!--optTpHorarios-->",$optSch,$rslt);
		echo $rslt;
	break;
	
	case 'saveHorarioEspecial':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$horaIni = $_POST['salidaHora'].":".$_POST['salidaMinutos'].":00";
		$horaFin = $_POST['entradaHora'].":".$_POST['entradaMinutos'].":00";
		$arrayAgentes = $_POST['arrayAgents'];
		$agentes = explode(" ",$arrayAgentes);
		$n = count($agentes);
		
		//Cantidad y lista de personas que no esta esa hora dentro de su horario
		$cantidadFueraDeHora = 0;
		$personasFueraDeHora = 0;
		
		//Lista las personas y actividades q ya tiene programadas el agente para esa hora
		$personasFueraLinea = '<table>';
		/*
		$flagHorario = 0;
		$personasHorario = "";
		$flagFueraLinea = 0;
		*/
		
		//Verifica q la hora final no sea mayor a la hora inicial
		if(strtotime($horaIni)>= strtotime($horaFin)){
			echo "-1";
			break;
		}
		else{
			for($i = 0; $i<$n; $i++){
				$sqlText = "select firstname, lastname from employees where employee_id=".$agentes[$i];
				$dtEmp = $dbEx->selSql($sqlText);
				
				//Verifica que el agente tenga horario asignado para ese dia y durante esas horas 
				$sqlText ="select sch_id from schedules where sch_date='".$fecha."' and sch_entry <='".$horaIni."' and sch_departure >='".$horaFin."' and employee_id=".$agentes[$i];
				$dtSch = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					
					//Verifica que no posea actividad para la misma hora, sino la sustituye
					$sqlText = "select schact_id, tpsch_name from schedulesactiv_emp a inner join types_schedules t on a.tpsch_id=t.tpsch_id where employee_id=".$agentes[$i]." and schact_date='".$fecha."' and ((schact_start between '".$horaIni."' and '".$horaFin."') or (schact_end between '".$horaIni."' and '".$horaFin."'))";
					$dtActivExiste = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flagFueraLinea =1;
						$personasFueraLinea .= '<tr><td>'.$dtEmp['0']['firstname']." ".$dtEmp['0']['lastname'].'</td>
						<td>'.$dtActivExiste['0']['tpsch_name'].'</td></tr>';
					}
					else{
						//Ingresa la actividad fuera de linea
						$sqlText = "insert into schedulesactiv_emp set tpsch_id=".$_POST['tpAct'].", employee_id=".$agentes[$i].", schact_date='".$fecha."', schact_start='".$horaIni."', schact_end='".$horaFin."' ";	
						$dbEx->insSql($sqlText);
					}
				}
				else{
					
					$personasHorario .= $dtEmp['0']['firstname']." ".$dtEmp['0']['lastname'].", ";	
				}
			}
		}
		
		/*$validaciones = '<input type="hidden" id="txtFlagHorario" value="'.$flagHorario.'">';
		$validaciones .='<input type="hidden" id="txtPersonasHorario" value="'.$personasHorario.'">';
		$validaciones .='<input type="hidden" id="txtFlagFueraLinea" value="'.$flagFueraLinea.'">';
		$validaciones .='<input type="hidden" id="txtPersonasFueraLinea" value="'.$personasFueraLinea.'">';
		*/
		echo strtotime($fecha);
	break;
	
	case 'HorarioEspecialDia':
		$fecha = date("Y-m-d",$_POST['fecha']);
		
		$sqlText = "select * from types_schedules where tpsch_status='A' order by tpsch_name";
		$dtTp = $dbEx->selSql($sqlText);
		
		$tblResult = '<table class="backTablaMain"  bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
		$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="4" align="center"><font color="#003366"><b>Activities for the day '.date("d/m/Y",$_POST['fecha']).'</b></font></td></tr>';
		foreach($dtTp as $dtT){
			$sqlText = "select e.employee_id, username, firstname, lastname, date_format(schact_date,'%d/%m/%Y') as f1, date_format(schact_start,'%H:%i') as t1, date_format(schact_end,'%H:%i') as t2 from employees e inner join schedulesactiv_emp sch on e.employee_id=sch.employee_id where schact_date='".$fecha."' and tpsch_id=".$dtT['TPSCH_ID']." order by firstname";
			$dtActiv = $dbEx->selSql($sqlText);
			$tblResult .='<tr><td class="txtForm" colspan="4">'.$dtT['TPSCH_NAME'].': '.$dbEx->numrows.'</td></tr>';
			if($dbEx->numrows>0){
				$tblResult .='<tr class="txtForm"><td>Badge</td><td>Employee</td><td>Initial hour</td><td>Final hour</td></tr>';
				foreach($dtActiv as $dtA){
					$tblResult .='<tr><td>'.$dtA['username'].'</td><td>'.$dtA['firstname']." ".$dtA['lastname'].'</td><td>'.$dtA['t1'].'</td><td>'.$dtA['t2'].'</td></tr>';	
				}
			}
		
		}
		$tblResult .='</table>';
		echo $tblResult;
	break;
	
	case 'reportActivities':
		
		$rslt = cargaPag("../schedules/filtrosRepActividades.php");
			//Obtiene los agentes para filtros
			$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where pe.status_plxemp = 'A' and user_status=1 and (name_role = 'AGENTE' or name_role='SUPERVISOR') order by firstname";
			
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				$optEmp .='<option value="0">[ALL]</option>';
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
				}
			}
			else{
				$optEmp .='<option value="-1">No Employees</option>';	
			}
			
			//Obtiene las cuentas
			$sqlText = "select * from account where id_typeacc=2 and account_status='A' order by name_account ";
			$dtCuenta = $dbEx->selSql($sqlText);
			$optCuenta = "";
			if($dbEx->numrows>0){
				$optCuenta .='<option value="0">[ALL]</option>';
				foreach($dtCuenta as $dtC){
					$optCuenta .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
				}
			}
			else{
				$optCuenta .='<option value="-1">No Accounts</option>';	
			}
			
			//Obtiene los departamentos
			$sqlText ="select distinct(d.id_depart) as id_dep, name_depart from depart_exc d inner join placexdep pd on pd.id_depart=d.id_depart inner join account a on a.id_account=pd.id_account where status_depart = 1 and a.id_typeacc=2 and account_status='A' order by name_depart";
			$dtD = $dbEx->selSql($sqlText);
			
			$optD = '<option value="0">[ALL]</option>';
			foreach($dtD as $dtD){
				$optD .='<option value="'.$dtD['id_dep'].'">'.$dtD['name_depart'].'</option>';	
			}
			
			//Obtiene las plazas
			$sqlText = "select distinct(p.id_place) as id_place, name_place from places p inner join placexdep pd on p.id_place=pd.id_place inner join account a on a.id_account=pd.id_account where a.id_typeacc=2 and account_status='A' order by name_place";
			$dtP = $dbEx->selSql($sqlText);
			$optP ='<option value="0">[ALL]</option>';
			foreach($dtP as $dtP){
				$optP .='<option value="'.$dtP['id_place'].'">'.$dtP['name_place'].'</option>';	
			}
			
			$optHora = "";
			for($i=0;$i<=23; $i++){
				$n = zerofill($i, 2);
				$optHora .='<option value="'.$n.'">'.$n.'</option>';
			}
			$optMinutos = "";
			for($i=0; $i<=59;$i++){
				$n = zerofill($i, 2);
				$optMinutos .='<option value="'.$n.'">'.$n.'</option>';	
			}
			
			$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
			$rslt = str_replace("<!--optDepart-->",$optD,$rslt);
			$rslt = str_replace("<!--optPosicion-->",$optP,$rslt);
			$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
			$rslt = str_replace("<!--optHora-->",$optHora,$rslt);
			$rslt = str_replace("<!--optMinutos-->",$optMinutos,$rslt);
			
			echo $rslt;
	break;

	case 'loadRepActivities':
		$filtro = " where 1 ";
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depto']>0){
			$filtro .=" and pd.id_depart=".$_POST['depto'];	
		}
		if($_POST['posicion']>0){
			$filtro .=" and pd.id_place=".$_POST['posicion'];
		}
		if($_POST['employee']>0){
			$filtro .=" and e.employee_id=".$_POST['employee'];
		}
		if(strlen($_POST['fechaIni'])>0){
			$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
			$filtro .= " and schact_date between date '".$fechaIni."' and '".$fechaFin."' ";
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (e.firstname like '%".$_POST['nombre']."%' or e.lastname like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and e.username like '%".$_POST['badge']."%' ";
		}
		$sqlText = "select * from types_schedules where tpsch_status='A'";
		$dtTp = $dbEx->selSql($sqlText);
		
		$tblResult = '<table class="backTablaMain"  bordercolor="#069" align="center" cellpadding="2" cellspacing="2" width="700">';
		$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="5" align="center"><font color="#003366"><b>Activities for the period '.$_POST['fechaIni'].' - '.$_POST['fechaFin'].'</b></font></td></tr>';
		$tblResult .='<tr><td>
			<form target="_blank" action="schedules/xls_repActividades.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="fechaIni" value="'.$_POST['fechaIni'].'">
			<input type="hidden" name="fechaFin" value="'.$_POST['fechaFin'].'">
			</td></tr>';
		foreach($dtTp as $dtT){
			
			$sqlText = "select e.employee_id, username, firstname, lastname, date_format(schact_date,'%d/%m/%Y') as f1, date_format(schact_start,'%H:%i') as t1, date_format(schact_end,'%H:%i') as t2 from schedulesactiv_emp sch inner join employees e on sch.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and tpsch_id=".$dtT['TPSCH_ID']." and pe.status_plxemp='A' order by schact_date, firstname";
			$dtActiv = $dbEx->selSql($sqlText);
			$tblResult .='<tr><td class="txtForm" colspan="5">'.$dtT['TPSCH_NAME'].': '.$dbEx->numrows.'</td></tr>';
			if($dbEx->numrows>0){
				$tblResult .='<tr class="txtForm"><td>Badge</td><td>Employee</td><td>Date</td><td>Initial hour</td><td>Final hour</td></tr>';
				foreach($dtActiv as $dtA){
					$tblResult .='<tr><td>'.$dtA['username'].'</td><td>'.$dtA['firstname']." ".$dtA['lastname'].'</td><td>'.$dtA['f1'].'</td><td>'.$dtA['t1'].'</td><td>'.$dtA['t2'].'</td></tr>';	
				}
			}
		}
		$tblResult .='</table>';
		echo $tblResult;
	break;
	
	case 'reportProgrammedHours':
		//Obtiene los agentes para filtros
			$rslt = cargaPag("../schedules/filtrosRepProgHours.php");
		
			$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where pe.status_plxemp = 'A' and user_status=1 and (name_role = 'AGENTE' or name_role='SUPERVISOR') order by firstname";
			
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				$optEmp .='<option value="0">[ALL]</option>';
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
				}
			}
			else{
				$optEmp .='<option value="-1">No Employees</option>';	
			}
			
			//Obtiene las cuentas
			$sqlText = "select * from account where id_typeacc=2 and account_status='A' order by name_account ";
			$dtCuenta = $dbEx->selSql($sqlText);
			$optCuenta = "";
			if($dbEx->numrows>0){
				$optCuenta .='<option value="0">[ALL]</option>';
				foreach($dtCuenta as $dtC){
					$optCuenta .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
				}
			}
			else{
				$optCuenta .='<option value="-1">No Accounts</option>';	
			}
			
			//Obtiene los departamentos
			$sqlText ="select distinct(d.id_depart) as id_dep, name_depart from depart_exc d inner join placexdep pd on pd.id_depart=d.id_depart inner join account a on a.id_account=pd.id_account where status_depart = 1 and a.id_typeacc=2 and account_status='A' order by name_depart";
			$dtD = $dbEx->selSql($sqlText);
			
			$optD = '<option value="0">[ALL]</option>';
			foreach($dtD as $dtD){
				$optD .='<option value="'.$dtD['id_dep'].'">'.$dtD['name_depart'].'</option>';	
			}
			
			//Obtiene las plazas
			$sqlText = "select distinct(p.id_place) as id_place, name_place from places p inner join placexdep pd on p.id_place=pd.id_place inner join account a on a.id_account=pd.id_account where a.id_typeacc=2 and account_status='A' order by name_place";
			$dtP = $dbEx->selSql($sqlText);
			$optP ='<option value="0">[ALL]</option>';
			foreach($dtP as $dtP){
				$optP .='<option value="'.$dtP['id_place'].'">'.$dtP['name_place'].'</option>';	
			}
			
			$optHora = "";
			for($i=0;$i<=23; $i++){
				$n = zerofill($i, 2);
				$optHora .='<option value="'.$n.'">'.$n.'</option>';
			}
			$optMinutos = "";
			for($i=0; $i<=59;$i++){
				$n = zerofill($i, 2);
				$optMinutos .='<option value="'.$n.'">'.$n.'</option>';	
			}
			
			//Obtiene los jefes con rol de Supervisor y Gerente de area
			$filtro = "";
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join places p on pd.id_place=p.id_place where pe.status_plxemp='A' and user_status=1 and (name_role='SUPERVISOR' or name_role='GERENTE DE AREA') and name_place!='CLIENT' order by firstname ";
			$dtJefe = $dbEx->selSql($sqlText);
			$optJefe = '<option value="0">[ALL]</option>';
			if($dbEx->numrows>0){
				foreach($dtJefe as $dtJ){
					$optJefe .='<option value="'.$dtJ['employee_id'].'">'.$dtJ['firstname'].' '.$dtJ['lastname'].'</option>';
				}
			}
			
			$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
			$rslt = str_replace("<!--optDepart-->",$optD,$rslt);
			$rslt = str_replace("<!--optPosicion-->",$optP,$rslt);
			$rslt = str_replace("<!--optJefe-->",$optJefe,$rslt);
			$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
			$rslt = str_replace("<!--optHora-->",$optHora,$rslt);
			$rslt = str_replace("<!--optMinutos-->",$optMinutos,$rslt);
			
		echo $rslt;
	break;
	
	case 'loadRepProgHours':
		$filtro = " where 1 ";
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depto']>0){
			$filtro .=" and pd.id_depart=".$_POST['depto'];	
		}
		if($_POST['posicion']>0){
			$filtro .=" and pd.id_place=".$_POST['posicion'];
		}
		if($_POST['employee']>0){
			$filtro .=" and e.employee_id=".$_POST['employee'];
		}
		if(strlen($_POST['fechaIni'])>0){
			$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
			$filtro .= " and sch_date between date '".$fechaIni."' and '".$fechaFin."' ";
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (e.firstname like '%".$_POST['nombre']."%' or e.lastname like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and e.username like '%".$_POST['badge']."%' ";
		}
		if($_POST['jefe']>0){
			$filtro .=" and e.id_supervisor=".$_POST['jefe'];
		}
		$sqlText = "select distinct(e.employee_id) employee_id, username, firstname, lastname from schedules sh inner join employees e ".
			" on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on ".
			" pd.id_placexdep=pe.id_placexdep ".$filtro." and pe.status_plxemp='A' order by firstname ";
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt = '<table cellpadding="2" cellspacing="0" width="600" border="1" class="tblListBack" align="center" bordercolor="#AABCD2">';
		
		if($dbEx->numrows>0){
			$start = strtotime($fechaIni);
			$end = strtotime($fechaFin);
			$rslt .='<tr><td>
			<form target="_blank" action="schedules/xls_repProgrammedHours.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="fechaIni" value="'.$fechaIni.'">
			<input type="hidden" name="fechaFin" value="'.$fechaFin.'">
			</td></tr>';
			$rslt .='<tr class="txtForm"><td align="center" width="5%">Badge</td>
			<td width="20%">Employee</td>';
			$i = 0;
			while ( strtotime($fechaIni . ' +'.$i.' day') <= $end){
				$rslt .='<td align="center"><b>'.date("d/m/Y",strtotime($fechaIni . ' +'.$i.' day')).'</td>';
				$i++;
			}
			
			$rslt .='<td align="center">Total Hrs</td></tr>';
			
			
			foreach($dtEmp as $dtE){

				$rslt .='<tr class="rowCons"><td align="center">'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				//Obtener las horas programadas dia a dia
				$i = 0;
				while ( strtotime($fechaIni . ' +'.$i.' day') <= $end){
					$progh = 0;
					$sqlText = "select round((( ifnull(TIME_TO_SEC(sch_departure),0) - ifnull(TIME_TO_SEC(sch_entry),0)) -  ".
						" ( ifnull(TIME_TO_SEC(sch_lunchin),0) - ifnull(TIME_TO_SEC(sch_lunchout),0) ))/3600,2) sch_proghrs  ".
						" from schedules where employee_id=".$dtE['employee_id']." and sch_date='".date("Y-m-d",strtotime($fechaIni . ' +'.$i.' day'))."'";
					$i++;
					$dtProgH = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0 and $dtProgH['0']['sch_proghrs']>0){
						$progh = $dtProgH['0']['sch_proghrs'];	
					}
					$rslt .='<td align="center">'.$progh.'</td>';
				}
				//Obtener la sumatoria de horas
				$totalProgramadas = 0;
				//$sqlText = "select sum(sch_proghrs) as sumhoras from schedules where employee_id=".$dtE['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
                $sqlText = "select round((((SUM(ifnull(TIME_TO_SEC(sch_departure),0))) - (SUM(ifnull(TIME_TO_SEC(sch_entry),0)))) -  ".
							"((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) sumhoras  ".
    						" from schedules ".
    						" where employee_id = ".$dtE['employee_id'].
							" and sch_date between date '".$fechaIni."' and '".$fechaFin."'";


				$dtSumHorario = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtSumHorario['0']['sumhoras']!=NULL){
					$totalProgramadas = $dtSumHorario['0']['sumhoras'];
				}
				$rslt .='<td>'.$totalProgramadas.'</td></tr>';
				
			}
			
			
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'uploadHorario':
		$rslt = cargaPag("../schedules/formUploadHorario.php");
		echo $rslt;	
	break;
	
	case 'uploadProgHours':
		$rslt = cargaPag("../schedules/formUploadProgHours.php");
		echo $rslt;	
	break;
	
}
