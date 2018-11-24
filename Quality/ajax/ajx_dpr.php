<?php
//Funciones para SDR	
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
  
  function hoursToSecods ($hour) { // $hour must be a string type: "HH:mm:ss"

		$parse = array();
		if (!preg_match ('#^(?<hours>[\d]{2}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#',$hour,$parse)) {
			// Throw error, exception, etc
			throw new RuntimeException ("Hour Format not valid".$hour." ");
		}

		return (int) $parse['hours'] * 3600 + (int) $parse['mins'] * 60 + (int) $parse['secs'];

} 
  
switch($_POST['Do']){
	case 'newDPR':
		$rslt = cargaPag("../dpr/filtros_newDpr.php");
		echo $rslt;
	break;
	
	case 'loadFormDpr':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		
		$sqlText = "select firstname, lastname from employees where employee_id=".$_SESSION['usr_id'];
		$dtSup = $dbEx->selSql($sqlText);
		
		$sqlText = "select employee_id, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		
		//Titulos de todas las secciones del form
		$tblTitulos = '<input type="hidden" id="txtIdSup" value="'.$_SESSION['usr_id'].'">';
		$tblTitulos .='<input type="hidden" id="txtFecha" value="'.$fecha.'">';
		$tblTitulos .= '<table class="tblDpr" width="80%" align="center" cellpadding="2" cellspacing="2" border="1">
<tr class="showItem"><td colspan="8">DAILY PERFORMANCE REVIEW</td></tr>';
		$tblTitulos .='<tr><td colspan="8" align="right"><img src="images/save.ico"  width="50" title="click to save form" style="cursor:pointer" onclick="saveDpr()"></td></tr>';
		$tblTitulos .='<tr><td colspan="2">Supervisor: '.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td><td colspan="6">Date: '.$_POST['fecha'].'</td></tr>';
		
		$tblAht = '<tr class="showItem"><td colspan="8">AHT Offenders</td></tr>';
		$tblAht .= '<tr class="itemCenter"><td>Name</td><td>AHT</td><td colspan="6">Comments</td></tr>';
		
		$tblRefused = '<tr class="showItem"><td colspan="8">Refused Contact Offenders</td></tr>';
		$tblRefused .='<tr class="itemCenter"><td colspan="5">Name</td><td>Calls Handled</td><td>Refused C</td><td>Refused %</td></tr>';
		
		$tblCompletedHours = '<tr class="showItem"><td colspan="8">Completed Hours Offenders</td></tr>';
		$tblCompletedHours .='<tr class="itemCenter"><td>Name</td><td>Staffed H</td><td>WH</td><td>Time difference</td><td>Ratio %</td><td colspan="3">Comments</td></tr>';
		
		$tblQA = '<tr class="showItem"><td colspan="8">QA Offenders</td></tr>';
		$tblQA .='<tr><td class="itemCenter">Name</td><td>Score</td><td>MTD</td><td colspan="5">Comments</td></tr>';
		
		$tblTardiness = '<tr class="showItem"><td colspan="8">Tardiness</td></tr>';
		$tblTardiness .='<tr class="itemCenter"><td colspan="6">Name</td><td colspan="2">Minutes Late</td></tr>';
		
		if($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$tblAht .='<tr><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblRefused .='<tr><td colspan="5">'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblCompletedHours .='<tr><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblQA .='<tr><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblTardiness .='<tr><td colspan="6">'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				
				$aht = "";
				$calls = 0;
				$refused = 0;
				$refusedProm = 0;
				$staffed = "";
				$wh = "";
				$ratioh = "";
				$ratiopercent = 0;
				$scoreQA = "";
				$commentAht = "";
				$commentHours = "";
				$commentQa = "";
				$commentMinutesLate = "";
				
				//Obtiene los datos de cada metrica de la form por agente
				$sqlText = "select * from phone_metrics where employee_id=".$dtE['employee_id']." and metric_date='".$fecha."'";
				$dtMetric = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					//Asigna valor a los comentarios
					$commentAht = $dtMetric['0']['METRIC_AHT_COMMENT'];
					$commentHours = $dtMetric['0']['METRIC_HOURSOFFENDERS_COMMENT'];
					$commentQa = $dtMetric['0']['METRIC_QA_COMMENTS'];
					$commentMinutesLate = $dtMetric['0']['METRIC_TARDINESS'];
					
					//Calcula AHT
					$tiempoLlamadas = hoursToSecods($dtMetric['0']['METRIC_AHT_TOTALTIME']);
					if($dtMetric['0']['METRIC_TOTALCALLS']!='' and $dtMetric['0']['METRIC_TOTALCALLS']>0){
						$calls = $dtMetric['0']['METRIC_TOTALCALLS'];
						$promLlamada = $tiempoLlamadas / $calls;
						$aht = gmdate("H:i:s",$promLlamada);
					}
					//Calcula Refused
					if($calls >0){
						$refused = $dtMetric['0']['METRIC_REFUSED'];
						$refusedProm = $refused/($calls + $refused);
					}
					
					
				}
				
				//Calcula horas completadas
				$staffed = 0;
				$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['employee_id']." and sch_date = '".$fecha."' group by employee_id";
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$staffed = $dtSch['0']['sumHorario'];
				}

				
				//Recupera horas del payroll mas exception y mas AP
				//Obtiene horas de payroll para el periodo
				$sqlText = "select payroll_htotal as stotal from payroll where employee_id=".$dtE['employee_id']." and payroll_date ='".$fecha."'";
				$dtPay = $dbEx->selSql($sqlText);
				$horasPayroll = 0.0;
				$horasAp = 0.0;
				$horasException = 0.0;
				if($dbEx->numrows>0){
					$horasPayroll = $dtPay['0']['stotal'];
				}
				//Obtiene horas de las AP en el periodo dado

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap ='".$fecha."' and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$horasAp = $horasAp + $dtA['hours_ap'];	
					}
				}
				//Obtine horas de las exceptions en el periodo dado
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and exceptionemp_date='".$fecha."' and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
				$dtEx = $dbEx->selSql($sqlText);
				$horasException = "0.0";
				if($dbEx->numrows>0){
					$horas = $dtEx['0']['hora']; 
					$min = $dtEx['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasException = $horas.".".$formatMinutos[1];	
				}
				
				//Obtiene las horas de PAID HOLIDAY
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and exceptionemp_date='".$fecha."' and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
				$dtPh = $dbEx->selSql($sqlText);
				$horasPaidHoliday ="0.0";
				if($dbEx->numrows){
					$horasPh = $dtPh['0']['hora']; 
					$minPh = $dtPh['0']['minutos']; 
					$minutosPh = $minPh%60; 
					$minutosPh = round($minutosPh/60,2);
					$formatMinutosPh = explode(".",$minutosPh);
					$h=0; 
					$h=(int)($minPh/60); 
					$horasPh+=$h;
					$horasPaidHoliday = $horasPh.".".$formatMinutosPh[1];	
				}
				$wh = $horasPayroll + $horasAp + $horasException + $horasPaidHoliday;
				$ratioh = $staffed - $wh;
				$ratiopercent = 0;
				if($staffed>0){
					$ratiopercent = ($wh/$staffed)*100;
				}
				
				//Obtiene notas de QA
				$scoreQA  = 0; 
				$sumaEva = 0;
				$cantidadEva = 0;

				$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp where monitcsemp_date = '".$fecha."' and monitcsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtSumCS = $dbEx->selSql($sqlText);
				if($dtSumCS['0']['sumCS']!=NULL){
					$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
				}
				$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp where monitcsemp_date = '".$fecha."' and monitcsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtCountCS = $dbEx->selSql($sqlText);
				if($dtCountCS['0']['countCS']!=NULL){
					$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
				}
				
				$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp where monitsales_date = '".$fecha."' and monitsales_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtSumSales = $dbEx->selSql($sqlText);
				if($dtSumSales['0']['sumSales']!=NULL){
					$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					
				}
				
				$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp where monitsales_date = '".$fecha."' and monitsales_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtCountSales = $dbEx->selSql($sqlText);
				if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
					$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					
				}
				
				$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp where monitnsemp_date ='".$fecha."' and monitnsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtSumNS = $dbEx->selSql($sqlText);
				if($dtSumNS['0']['sumNS']!=NULL){
					$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
				}
				
				$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp where monitnsemp_date ='".$fecha."' and monitnsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtCountNS = $dbEx->selSql($sqlText);
				if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
					$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
				}
				if($cantidadEva > 0){
					$scoreQA = $sumaEva/$cantidadEva;
				}
				
				//Completar Tablas
				$tblAht .='<td>'.$aht.'</td>
				<td colspan="6"><input type="text" class="txtPag" size="80" id="txtAht[]" name="txtAht[]" value="'.$commentAht.'"></td></tr>';
				
				$tblRefused .='<td>'.$calls.'</td>
				<td>'.$refused.'</td>
				<td>'.number_format(($refusedProm * 100),2).'</td></tr>';
				
				$tblCompletedHours .='<td>'.number_format($staffed,2).'</td>
				<td>'.number_format($wh,2).'</td>
				<td>'.number_format($ratioh,2).'</td>
				<td>'.number_format($ratiopercent,2).'%</td>
				<td colspan="3"><input type="text" class="txtPag" id="txtHours[]" name="txtHours[]" value="'.$commentHours.'" size="50"></td></tr>';
				
				$tblQA .='<td>'.number_format($scoreQA,2).'%</td>
				<td colspan="6"><input type="text" class="txtPag" size="80" id="txtQA[]" name="txtQA[]" value="'.$commentQa.'"></td></tr>';
				
				$tblTardiness .='<td colspan="2"><input type="text" class="txtPag" id="txtMinutosLate[]" name="txtMinutosLate[]" value="'.$commentMinutesLate.'"></td></tr>';
				
				
			}//Termina recorrido por empleado	
		}
		//Unir Tablas
		$tblTitulos .= $tblAht;
		$tblTitulos .= $tblRefused;
		$tblTitulos .= $tblCompletedHours;
		$tblTitulos .= $tblQA;
		$tblTitulos .= $tblTardiness;
		$tblTitulos .='<tr class="showItem"><td colspan="8">Minutes of the Daily Huddle</td></tr>';
		
		$sqlText = "select dailyperf_comments from daily_performance where employee_id=".$_SESSION['usr_id']." and dailyperf_date='".$fecha."'";
		$dtPerf = $dbEx->selSql($sqlText);
		$comment = "";
		if($dbEx->numrows>0){
			$comment = $dtPerf['0']['dailyperf_comments'];
		}
		
		$tblTitulos .='<tr><td colspan="8" align="center"><textarea id="txtComments" class="txtPag" cols="150" rows="6">'.$comment.'</textarea></td></tr>';
		$tblTitulos .='<tr><td colspan="8" align="left"><img src="images/save.ico"  width="50" title="click to save form" style="cursor:pointer" onclick="saveDpr()"></td></tr>';
		$tblTitulos .='</table>';
		
		echo $tblTitulos;
	break;
	
	case 'saveDpr':
		//Recorrer los array para obtener los valores de cada agente
		$commentAht = explode("***************",$_POST['arrayAht']);
		$commentHours = explode("***************",$_POST['arrayHours']);
		$commentQa = explode("***************",$_POST['arrayQA']);
		$commentMinutesLate = explode("***************",$_POST['arrayMinutesLate']);
		$i = 0;
		
		//Guarda los datos generales de la form
		$sqlText = "select dailyperf_id from daily_performance where dailyperf_date='".$_POST['fecha']."' and employee_id=".$_POST['sup'];
		$dtPerf = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update daily_performance set dailyperf_comments='".$_POST['comment']."' where dailyperf_id=".$dtPerf['0']['dailyperf_id'];
			$dbEx->updSql($sqlText);
		}
		else{
			$sqlText = " insert into daily_performance set employee_id=".$_POST['sup'].", dailyperf_date='".$_POST['fecha']."', dailyperf_comments='".$_POST['comment']."'";
			$dbEx->insSql($sqlText);
			$sqlText = "select dailyperf_id from daily_performance where dailyperf_date='".$_POST['fecha']."' and employee_id=".$_POST['sup'];
			$dtPerf = $dbEx->selSql($sqlText);
		}
		
		//Actualizar cada registro de phone metrics con los datos de la form
		$sqlText = "select employee_id from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		foreach($dtEmp as $dtE){
			$sqlText = "select metric_id from phone_metrics where employee_id=".$dtE['employee_id']." and metric_date='".$_POST['fecha']."'";		
			$dtMetric = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$sqlText = "update phone_metrics set supervisor_id=".$_POST['sup'].", metric_aht_comment='".$commentAht[$i]."', metric_hoursoffenders_comment='".$commentHours[$i]."', metric_qa_comments='".$commentQa[$i]."', metric_tardiness='".$commentMinutesLate[$i]."' where metric_id=".$dtMetric['0']['metric_id'];
				$dbEx->updSql($sqlText);
			}
			else{
				$sqlText = "insert into phone_metrics set employee_id=".$dtE['employee_id'].", supervisor_id=".$_POST['sup'].", metric_date='".$_POST['fecha']."', metric_aht_comment='".$commentAht[$i]."', metric_hoursoffenders_comment='".$commentHours[$i]."', metric_qa_comments='".$commentQa[$i]."', metric_tardiness='".$commentMinutesLate[$i]."' ";
				$dbEx->insSql($sqlText);
			}
			$i++;
		}
		echo $dtPerf['0']['dailyperf_id'];
		
	break;
	
	case 'loadDpr':
		$tblResult = '<table class="tblDpr" width="80%" align="center" cellpadding="2" cellspacing="2" border="1">';
		
		$sqlText = "select *, date_format(dailyperf_date,'%d/%m/%Y') as f1 from daily_performance where dailyperf_id=".$_POST['id'];
		$dtPerf = $dbEx->selSql($sqlText);
		
		$sqlText = "select firstname, lastname from employees where employee_id=".$dtPerf['0']['EMPLOYEE_ID'];
		$dtSup = $dbEx->selSql($sqlText);
		
		$sqlText = "select m.employee_id, username, firstname, lastname, metric_id, metric_aht_totaltime, metric_totalcalls, metric_efficiency, metric_refused, metric_tardiness, metric_aht_comment, metric_hoursoffenders_comment, metric_qa_comments from phone_metrics m inner join employees e on m.employee_id=e.employee_id where m.supervisor_id=".$dtPerf['0']['EMPLOYEE_ID']." and metric_date='".$dtPerf['0']['DAILYPERF_DATE']."' order by firstname";
		$dtMetric = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			
			$tblTitulos = '<tr class="showItem"><td colspan="8">DAILY PERFORMANCE REVIEW</td></tr>';
			$tblTitulos .='<tr><td colspan="2">Supervisor: '.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td><td colspan="6">Date: '.$dtPerf['f1'].'</td></tr>';
		
			$tblAht = '<tr class="showItem"><td colspan="8">AHT Offenders</td></tr>';
			$tblAht .= '<tr class="itemCenter"><td>Name</td><td>AHT</td><td colspan="6">Comments</td></tr>';
		
			$tblRefused = '<tr class="showItem"><td colspan="8">Refused Contact Offenders</td></tr>';
			$tblRefused .='<tr class="itemCenter"><td colspan="5">Name</td><td>Calls Handled</td><td>Refused C</td><td>Refused %</td></tr>';
		
			$tblCompletedHours = '<tr class="showItem"><td colspan="8">Completed Hours Offenders</td></tr>';
			$tblCompletedHours .='<tr class="itemCenter"><td>Name</td><td>Staffed H</td><td>WH</td><td>Time difference</td><td>Ratio %</td><td colspan="3">Comments</td></tr>';
		
			$tblQA = '<tr class="showItem"><td colspan="8">QA Offenders</td></tr>';
			$tblQA .='<tr><td class="itemCenter">Name</td><td>Score</td><td colspan="6">Comments</td></tr>';
		
			$tblTardiness = '<tr class="showItem"><td colspan="8">Tardiness</td></tr>';
			$tblTardiness .='<tr class="itemCenter"><td colspan="6">Name</td><td colspan="2">Minutes Late</td></tr>';
			
			foreach($dtMetric as $dtE){
				$tblAht .='<tr><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblRefused .='<tr><td colspan="5">'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblCompletedHours .='<tr><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblQA .='<tr><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				$tblTardiness .='<tr><td colspan="6">'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				
				$aht = "";
				$calls = 0;
				$refused = 0;
				$refusedProm = 0;
				$staffed = "";
				$wh = "";
				$ratioh = "";
				$ratiopercent = 0;
				$scoreQA = "";
				
				//Calcula AHT
				$tiempoLlamadas = hoursToSecods($dtE['metric_aht_totaltime']);
				if($dtE['metric_totalcalls']!='' and $dtE['metric_totalcalls']>0){
					$calls = $dtE['metric_totalcalls'];
					$promLlamada = $tiempoLlamadas / $calls;
					$aht = gmdate("H:i:s",$promLlamada);
				}
				//Calcula Refused
				if($calls >0){
					$refused = $dtE['metric_refused'];
					$refusedProm = $refused/($calls + $refused);
				}
				
				//Calcula horas completadas
				$staffed = 0;
				$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['employee_id']." and sch_date ='".$dtPerf['0']['DAILYPERF_DATE']."'";
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$staffed = $dtSch['0']['sumHorario'];
				}
				
				//Recupera horas del payroll mas exception y mas AP
				//Obtiene horas de payroll para el periodo
				$sqlText = "select payroll_htotal as stotal from payroll where employee_id=".$dtE['employee_id']." and payroll_date ='".$dtPerf['0']['DAILYPERF_DATE']."'";
				$dtPay = $dbEx->selSql($sqlText);
				$horasPayroll = 0.0;
				$horasAp = 0.0;
				$horasException = 0.0;
				if($dbEx->numrows>0){
					$horasPayroll = $dtPay['0']['stotal'];
				}
				//Obtiene horas de las AP en el periodo dado

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap ='".$dtPerf['0']['DAILYPERF_DATE']."' and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$horasAp = $horasAp + $dtA['hours_ap'];	
					}
				}
				//Obtine horas de las exceptions en el periodo dado
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and exceptionemp_date='".$dtPerf['0']['DAILYPERF_DATE']."' and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
				$dtEx = $dbEx->selSql($sqlText);
				$horasException = "0.0";
				if($dbEx->numrows>0){
					$horas = $dtEx['0']['hora']; 
					$min = $dtEx['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasException = $horas.".".$formatMinutos[1];	
				}
				
				//Obtiene las horas de PAID HOLIDAY
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and exceptionemp_date='".$dtPerf['0']['DAILYPERF_DATE']."' and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
				$dtPh = $dbEx->selSql($sqlText);
				$horasPaidHoliday ="0.0";
				if($dbEx->numrows){
					$horasPh = $dtPh['0']['hora']; 
					$minPh = $dtPh['0']['minutos']; 
					$minutosPh = $minPh%60; 
					$minutosPh = round($minutosPh/60,2);
					$formatMinutosPh = explode(".",$minutosPh);
					$h=0; 
					$h=(int)($minPh/60); 
					$horasPh+=$h;
					$horasPaidHoliday = $horasPh.".".$formatMinutosPh[1];	
				}
				$wh = $horasPayroll + $horasAp + $horasException + $horasPaidHoliday;
				$ratioh = $staffed - $wh;

				$ratiopercent = 0;
				if($staffed>0){
					$ratiopercent = ($wh/$staffed)*100;
				}
				
				//Obtiene notas de QA
				$scoreQA  = 0; 
				$sumaEva = 0;
				$cantidadEva = 0;

				$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp where monitcsemp_date = '".$dtPerf['0']['DAILYPERF_DATE']."' and monitcsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtSumCS = $dbEx->selSql($sqlText);
				if($dtSumCS['0']['sumCS']!=NULL){
					$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
				}
				$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp where monitcsemp_date = '".$dtPerf['0']['DAILYPERF_DATE']."' and monitcsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtCountCS = $dbEx->selSql($sqlText);
				if($dtCountCS['0']['countCS']!=NULL){
					$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
				}
				
				$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp where monitsales_date = '".$dtPerf['0']['DAILYPERF_DATE']."' and monitsales_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtSumSales = $dbEx->selSql($sqlText);
				if($dtSumSales['0']['sumSales']!=NULL){
					$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					
				}
				
				$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp where monitsales_date = '".$dtPerf['0']['DAILYPERF_DATE']."' and monitsales_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtCountSales = $dbEx->selSql($sqlText);
				if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
					$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					
				}
				
				$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp where monitnsemp_date ='".$dtPerf['0']['DAILYPERF_DATE']."' and monitnsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtSumNS = $dbEx->selSql($sqlText);
				if($dtSumNS['0']['sumNS']!=NULL){
					$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
				}
				
				$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp where monitnsemp_date ='".$dtPerf['0']['DAILYPERF_DATE']."' and monitnsemp_maker='Q' and employee_id=".$dtE['employee_id'];
				$dtCountNS = $dbEx->selSql($sqlText);
				if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
					$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
				}
				if($cantidadEva > 0){
					$scoreQA = $sumaEva/$cantidadEva;
				}
				
				//Completar Tablas
				$tblAht .='<td>'.$aht.'</td>
				<td colspan="6">'.$dtE['metric_aht_comment'].'</td></tr>';
				
				$tblRefused .='<td>'.$calls.'</td>
				<td>'.$refused.'</td>
				<td>'.number_format(($refusedProm * 100),2).'</td></tr>';
				
				$tblCompletedHours .='<td>'.number_format($staffed,2).'</td>
				<td>'.number_format($wh,2).'</td>
				<td>'.number_format($ratioh,2).'</td>
				<td>'.number_format($ratiopercent,2).'%</td>
				<td colspan="3">'.$dtE['metric_hoursoffenders_comment'].'</td></tr>';
				
				$tblQA .='<td>'.number_format($scoreQA,2).'%</td>
				<td colspan="6">'.$dtE['metric_qa_comments'].'</td></tr>';
				
				$tblTardiness .='<td colspan="2">'.$dtE['metric_tardiness'].'</td></tr>';
				
			}
			$tblTitulos .= $tblAht;
			$tblTitulos .= $tblRefused;
			$tblTitulos .= $tblCompletedHours;
			$tblTitulos .= $tblQA;
			$tblTitulos .= $tblTardiness;
			$tblTitulos .='<tr class="showItem"><td colspan="8">Minutes of the Daily Huddle</td></tr>';
			$tblTitulos .='<tr><td colspan="8" align="center"><textarea id="txtComments" class="txtPag" cols="150" rows="6" disable="disabled">'.$dtPerf['0']['DAILYPERF_COMMENTS'].'</textarea></td></tr>';
			$tblResult .=$tblTitulos;
			
		}
		else{
			$tblResult .='<tr><td>No Matches</td></tr>';
		}
		$tblResult .='</table>';
		echo $tblResult;
		
	break;
	
	case 'DPRSupervisor':
		$rslt = cargaPag("../dpr/filtrosDprSup.php");
		$sqlText = "select e.employee_id, firstname, lastname, pd.id_role from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join places pl on pd.id_place=pl.id_place where status_plxemp='A' and  (name_role='SUPERVISOR' or name_role='GERENTE DE AREA') and name_place!='CLIENT' and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtSup as $dtS){
				$sel = "";
				if($_SESSION['usr_id']==$dtS['employee_id']){ $sel = "selected";}	
				$optSup .='<option value="'.$dtS['employee_id'].'" '.$sel.'>'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
		}
		
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		echo $rslt;
	break;
	
	case 'loadDprSup':
		$filtro = " where 1 ";
		if(strlen($_POST['fechaIni'])>0 and strlen($_POST['fechaFin'])>0){
			$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
			$filtro .= " and dailyperf_date between date '".$fechaIni."' and '".$fechaFin."'";
		}
		if($_POST['sup']>0){
			$filtro .= " and d.employee_id=".$_POST['sup'];
		}
		
		$sqlText = " select d.employee_id, firstname, lastname, dailyperf_id, date_format(dailyperf_date,'%d/%m/%Y') as f1, dailyperf_comments from daily_performance d inner join employees e on d.employee_id=e.employee_id ".$filtro;
		$dtPerf = $dbEx->selSql($sqlText);
		
		$tblResult = '<table class="tblDpr" width="80%" align="center" cellpadding="2" cellspacing="2" border="0">';
		if($dbEx->numrows>0){
			$tblResult .='<tr bgcolor="#D3DCFA" align="center"><td width="30%"><b>Supervisor</td><td width="10%"><b>Date</td><td width="60%"><b>Comments</td></tr>';
			foreach($dtPerf as $dtP){
				$tblResult .='<tr onclick="loadDrpDate('.$dtP['dailyperf_id'].')" title="click to display the completed form" style="cursor:pointer">
				<td>'.$dtP['firstname']." ".$dtP['lastname'].'</td>
				<td>'.$dtP['f1'].'</td>
				<td>'.$dtP['dailyperf_comments'].'</td></tr>';
			}
			
		}
		else{
			$tblResult .='<tr><td>No Matches</td></tr>';
		}
		$tblResult .='</table>';
		
		echo $tblResult;

	break;
	
	case 'weeklyPerformance':
		$rslt = cargaPag("../dpr/filtrosWeekly.php");
		$sqlText = "select e.employee_id, firstname, lastname, pd.id_role from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join places pl on pd.id_place=pl.id_place where status_plxemp='A' and  (name_role='SUPERVISOR' or name_role='GERENTE DE AREA') and name_place!='CLIENT' and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtSup as $dtS){
				$sel = "";
				if($_SESSION['usr_id']==$dtS['employee_id']){ $sel = "selected";}	
				$optSup .='<option value="'.$dtS['employee_id'].'" '.$sel.'>'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
		}
		
		/*
		$sqlText = "select id_account, name_account from account where account_status='A' and id_typeacc=2 order by name_account";
		$dtAccount = $dbEx->selSql($sqlText);
		$optCuenta = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtAccount as $dtA){
				$optCuenta .='<option value="'.$dtA['id_account'].'">'.$dtA['name_account'].'</option>';	
			}
		}
		$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
		*/		
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);

		
		echo $rslt;
	break;
	
	case 'loadWeeklyPerformance':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$filtro = " where dailyperf_date between date '".$fechaIni."' and '".$fechaFin."' ";
		if($_POST['sup']>0){
			$filtro .=" and p.employee_id=".$_POST['sup'];
		}
		//Obtiene los supervisores segun filtro para mostrar rendimientos por supervisor
		$sqlText = "select distinct(p.employee_id) as emp_id, firstname, lastname from employees e inner join daily_performance p on p.employee_id=e.employee_id ".$filtro;
		$dtEmp = $dbEx->selSql($sqlText);
		$tblResult = '<table class="tblDpr" width="80%" align="center" cellpadding="2" cellspacing="2" border="1">';
		$tblResult .='<tr><td colspan="5" class="showItem">WEEKLY PERFORMANCE REVIEW FOR THE PERIDO '.$_POST['fechaIni']." - ".$_POST['fechaFin'];
		$tblTitulos = "";
		//Generara una tabla por cada sup
		if($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$tblTitulos .='<tr>
				<td colspan="5">
				<div id="lydown'.$dtE['emp_id'].'">
				<img src="images/sign-down.png" style="cursor:pointer" title="click to display details" width="25" onclick="getDetPerformance('.$dtE['emp_id'].')"></div>
				<div id="lyup'.$dtE['emp_id'].'" style="display:none">
				<img src="images/flecha_derecha.png" style="cursor:pointer" title="click for removing details" width="25" onclick="quitDetails('.$dtE['emp_id'].')"> 
				</div>
				 Supervisor: '.$dtE['firstname'].' '.$dtE['lastname'].' </td></tr>';
				$tblTitulos .='<tr><td colspan="5"><div id="lyDet'.$dtE['emp_id'].'"></div></td></tr>';
				/*
				
				$tblAht = '<tr class="showItem"><td colspan="5">AHT Offenders</td></tr>';
				$tblAht .= '<tr class="itemCenter"><td colspan="4">Name</td><td>AHT</td></tr>';
		
				$tblRefused = '<tr class="showItem"><td colspan="5">Refused Contact Offenders</td></tr>';
				$tblRefused .='<tr class="itemCenter"><td colspan="2">Name</td><td>Calls Handled</td><td>Refused C</td><td>Refused %</td></tr>';
		
				$tblCompletedHours = '<tr class="showItem"><td colspan="5">Completed Hours Offenders</td></tr>';
				$tblCompletedHours .='<tr class="itemCenter"><td>Name</td><td>Staffed H</td><td>WH</td><td>Time difference</td><td>Ratio %</td></tr>';
		
				$tblQA = '<tr class="showItem"><td colspan="5">QA Offenders</td></tr>';
				$tblQA .='<tr><td class="itemCenter" colspan="4">Name</td><td>Score</td></tr>';
		
				$tblTardiness = '<tr class="showItem"><td colspan="5">Tardiness</td></tr>';
				$tblTardiness .='<tr class="itemCenter"><td colspan="3">Name</td><td colspan="2">Minutes Late</td></tr>';
				
				$sqlText = "select distinct(m.employee_id) as employee_id, username, firstname, lastname from phone_metrics m inner join employees e on m.employee_id=e.employee_id where m.supervisor_id=".$dtE['emp_id']." and metric_date between date '".$fechaIni."' and '".$fechaFin."' order by firstname";
				$dtMetric = $dbEx->selSql($sqlText);
				foreach($dtMetric as $dtM){
					$tblAht .='<tr><td colspan="4">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblRefused .='<tr><td colspan="2">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblCompletedHours .='<tr><td>'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblQA .='<tr><td colspan="4">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblTardiness .='<tr><td colspan="3">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					
					$aht = "";
					$calls = 0;
					$refused = 0;
					$refusedProm = 0;
					$staffed = "";
					$wh = "";
					$ratioh = "";
					$ratiopercent = 0;
					$scoreQA = "";
					
					//Calcula AHT
					$sqlText = "select sum(metric_totalcalls) as totalcalls from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$dtE['emp_id']." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtCalls = $dbEx->selSql($sqlText);
					if($dtCalls['0']['totalcalls']!=NULL and $dtCalls['0']['totalcalls']>0){
						$calls = $dtCalls['0']['totalcalls'];
					}
					
					$sqlText = "select sec_to_time(sum(time_to_sec(metric_aht_totaltime))) as tiempo from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$dtE['emp_id']." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtAht = $dbEx->selSql($sqlText);
					$sumaTiempo = '';
					if($dbEx->numrows>0 and $dtAht['0']['tiempo']!=NULL){
						$sumaTiempo = $dtAht['0']['tiempo'];
					}
					$tiempoDecimal = hoursToSecods($sumaTiempo);
					if($calls>0){
						$promLlamada = $tiempoDecimal/$calls;
						$aht = gmdate("H:i:s",$promLlamada);
					}
					
					//Calcula Refused
					$sqlText = "select sum(metric_refused) as sumRefused from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$dtE['emp_id']." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtRefused = $dbEx->selSql($sqlText);
					if($dtRefused['0']['sumRefused']!=NULL and $dtRefused['0']['sumRefused']>0 ){
						$refused = $dtRefused['0']['sumRefused'];
						$refusedProm = $refused / ($calls + $refused);	
					}
					
					//Calcula horas programadas
					$sqlText = "select sum(HOUR(TIMEDIFF(sch_departure, sch_entry))) as hora, sum(MINUTE(TIMEDIFF(sch_departure, sch_entry))) as minutos from schedules sch where employee_id=".$dtM['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtSh = $dbEx->selSql($sqlText);
					$programadas = "0.0";
					if($dbEx->numrows>0){
						$horas = $dtSh['0']['hora']; 
						$min = $dtSh['0']['minutos']; 
						$minutos = $min%60; 
						$minutos = round($minutos/60,2);
						$formatMinutos = explode(".",$minutos);
						$h=0; 
						$h=(int)($min/60); 
						$horas+=$h;
						$programadas = $horas.".".$formatMinutos[1];
					}
					
					$sqlText = "select sum(HOUR(TIMEDIFF(sch_lunchout,sch_lunchin))) as hora, sum(MINUTE(TIMEDIFF(sch_lunchout,sch_lunchin))) as minutos from schedules sch where employee_id=".$dtM['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtLunch = $dbEx->selSql($sqlText);
					$horasLunch = "0.0";
					if($dbEx->numrows>0){
						$horas = $dtLunch['0']['hora'];
						$min = $dtLunch['0']['minutos'];
						$minutos = $min%60; 
						$minutos = round($minutos/60,2);
						$formatMinutos = explode(".",$minutos);
						$h=0; 
						$h=(int)($min/60); 
						$horas+=$h;
						$horasLunch = $horas.".".$formatMinutos[1];
					}
					$staffed = $programadas - $horasLunch;
					
					//Recupera horas del payroll mas exception y mas AP
					//Obtiene horas de payroll para el periodo
					$sqlText = "select payroll_htotal as stotal from payroll where employee_id=".$dtM['employee_id']." and payroll_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtPay = $dbEx->selSql($sqlText);
					$horasPayroll = 0.0;
					$horasAp = 0.0;
					$horasException = 0.0;
					if($dbEx->numrows>0){
						$horasPayroll = $dtPay['0']['stotal'];
					}
					//Obtiene horas de las AP en el periodo dado

					$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtM['employee_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap between date '".$fechaIni."' and '".$fechaFin."' and approved_status='A'";
					$dtAp = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						foreach($dtAp as $dtA){
							$horasAp = $horasAp + $dtA['hours_ap'];	
						}
					}
					//Obtine horas de las exceptions en el periodo dado
					$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtM['employee_id']." and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."' and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
					$dtEx = $dbEx->selSql($sqlText);
					$horasException = "0.0";
					if($dbEx->numrows>0){
						$horas = $dtEx['0']['hora']; 
						$min = $dtEx['0']['minutos']; 
						$minutos = $min%60; 
						$minutos = round($minutos/60,2);
						$formatMinutos = explode(".",$minutos);
						$h=0; 
						$h=(int)($min/60); 
						$horas+=$h;
						$horasException = $horas.".".$formatMinutos[1];	
					}
				
					//Obtiene las horas de PAID HOLIDAY
					$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtM['employee_id']." and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."' and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
					$dtPh = $dbEx->selSql($sqlText);
					$horasPaidHoliday ="0.0";
					if($dbEx->numrows){
						$horasPh = $dtPh['0']['hora']; 
						$minPh = $dtPh['0']['minutos']; 
						$minutosPh = $minPh%60; 
						$minutosPh = round($minutosPh/60,2);
						$formatMinutosPh = explode(".",$minutosPh);
						$h=0; 
						$h=(int)($minPh/60); 
						$horasPh+=$h;
						$horasPaidHoliday = $horasPh.".".$formatMinutosPh[1];	
					}
					$wh = $horasPayroll + $horasAp + $horasException + $horasPaidHoliday;
					$ratioh = $staffed - $wh;
				
					$ratiopercent = 0;
					if($staffed>0){
						$ratiopercent = ($wh/$staffed)*100;
					}
					
					//Obtiene notas de QA
					$scoreQA  = 0; 
					$sumaEva = 0;
					$cantidadEva = 0;

					$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitcsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtSumCS = $dbEx->selSql($sqlText);
					if($dtSumCS['0']['sumCS']!=NULL){
						$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
					}
					$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitcsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtCountCS = $dbEx->selSql($sqlText);
					if($dtCountCS['0']['countCS']!=NULL){
						$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
					}
				
					$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' and monitsales_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtSumSales = $dbEx->selSql($sqlText);
					if($dtSumSales['0']['sumSales']!=NULL){
						$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					}
				
					$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' and monitsales_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtCountSales = $dbEx->selSql($sqlText);
					if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
						$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					}
				
					$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp where monitnsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitnsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtSumNS = $dbEx->selSql($sqlText);
					if($dtSumNS['0']['sumNS']!=NULL){
						$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
					}
				
					$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp where monitnsemp_date  between date '".$fechaIni."' and '".$fechaFin."' and monitnsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtCountNS = $dbEx->selSql($sqlText);
					if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
						$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
					}
					if($cantidadEva > 0){
						$scoreQA = $sumaEva/$cantidadEva;
					}
					
					//Suma de tardiness
					
					$sqlText = "select sec_to_time(sum(time_to_sec(metric_tardiness))) as tiempo_tarde from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$dtE['emp_id']." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";;
					$dtTardiness = $dbEx->selSql($sqlText);
					$tardiness = '00:00:00';
					if($dtTardiness[0]['tiempo_tarde']!=NULL){
						$tardiness = $dtTardiness[0]['tiempo_tarde'];
					}
				
				//Completar Tablas
				$tblAht .='<td>'.$aht.'</td></tr>';
				
				$tblRefused .='<td>'.$calls.'</td>
				<td>'.$refused.'</td>
				<td>'.number_format(($refusedProm * 100),2).'</td></tr>';
				
				$tblCompletedHours .='<td>'.number_format($staffed,2).'</td>
				<td>'.number_format($wh,2).'</td>
				<td>'.number_format($ratioh,2).'</td>
				<td>'.number_format($ratiopercent,2).'%</td></tr>';
				
				$tblQA .='<td>'.number_format($scoreQA,2).'%</td></tr>';
				
				$tblTardiness .='<td colspan="2">'.$tardiness.'</td></tr>';
				

				}//Termina foreach por empleado para la tabla de cada agente
				*/
				
			}
			/*
			$tblTitulos .= $tblAht;
			$tblTitulos .= $tblRefused;
			$tblTitulos .= $tblCompletedHours;
			$tblTitulos .= $tblQA;
			$tblTitulos .= $tblTardiness;
			*/
			
			$tblResult .=$tblTitulos;
			
		}
		else{
			$tblResult .='<tr><td>No Matches</td></tr>';	
		}
		$tblResult .='</table>';
		echo $tblResult;
	break;
	
	case 'getDetPerformance':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		$sup = $_POST['idS'];
		$tblResult = '<table class="tblDpr" width="80%" align="center" cellpadding="2" cellspacing="2" border="1">';
		$tblAht = '<tr class="showItem"><td colspan="5">AHT Offenders</td></tr>';
		$tblAht .= '<tr class="itemCenter"><td colspan="4">Name</td><td>AHT</td></tr>';
		
		$tblRefused = '<tr class="showItem"><td colspan="5">Refused Contact Offenders</td></tr>';
		$tblRefused .='<tr class="itemCenter"><td colspan="2">Name</td><td>Calls Handled</td><td>Refused C</td><td>Refused %</td></tr>';
		
		$tblCompletedHours = '<tr class="showItem"><td colspan="5">Completed Hours Offenders</td></tr>';
		$tblCompletedHours .='<tr class="itemCenter"><td>Name</td><td>Staffed H</td><td>WH</td><td>Time difference</td><td>Ratio %</td></tr>';
		
		$tblQA = '<tr class="showItem"><td colspan="5">QA Offenders</td></tr>';
		$tblQA .='<tr><td class="itemCenter" colspan="4">Name</td><td>Score</td></tr>';
		
		$tblTardiness = '<tr class="showItem"><td colspan="5">Tardiness</td></tr>';
		$tblTardiness .='<tr class="itemCenter"><td colspan="3">Name</td><td colspan="2">Minutes Late</td></tr>';
		
		$sqlText = "select distinct(m.employee_id) as employee_id, username, firstname, lastname from phone_metrics m inner join employees e on m.employee_id=e.employee_id where m.supervisor_id=".$sup." and metric_date between date '".$fechaIni."' and '".$fechaFin."' order by firstname";
				$dtMetric = $dbEx->selSql($sqlText);
				foreach($dtMetric as $dtM){
					$tblAht .='<tr><td colspan="4">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblRefused .='<tr><td colspan="2">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblCompletedHours .='<tr><td>'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblQA .='<tr><td colspan="4">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					$tblTardiness .='<tr><td colspan="3">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>';
					
					$aht = "";
					$calls = 0;
					$refused = 0;
					$refusedProm = 0;
					$staffed = "";
					$wh = "";
					$ratioh = "";
					$ratiopercent = 0;
					$scoreQA = "";
					
					//Calcula AHT
					$sqlText = "select sum(metric_totalcalls) as totalcalls from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$sup." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtCalls = $dbEx->selSql($sqlText);
					if($dtCalls['0']['totalcalls']!=NULL and $dtCalls['0']['totalcalls']>0){
						$calls = $dtCalls['0']['totalcalls'];
					}
					
					$sqlText = "select sec_to_time(sum(time_to_sec(metric_aht_totaltime))) as tiempo from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$sup." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtAht = $dbEx->selSql($sqlText);
					$sumaTiempo = '';
					if($dbEx->numrows>0 and $dtAht['0']['tiempo']!=NULL){
						$sumaTiempo = $dtAht['0']['tiempo'];
					}
					$tiempoDecimal = hoursToSecods($sumaTiempo);
					if($calls>0){
						$promLlamada = $tiempoDecimal/$calls;
						$aht = gmdate("H:i:s",$promLlamada);
					}
					
					//Calcula Refused
					$sqlText = "select sum(metric_refused) as sumRefused from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$sup." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtRefused = $dbEx->selSql($sqlText);
					if($dtRefused['0']['sumRefused']!=NULL and $dtRefused['0']['sumRefused']>0 ){
						$refused = $dtRefused['0']['sumRefused'];
						$refusedProm = $refused / ($calls + $refused);	
					}
					
					//Calcula horas programadas
					$staffed = 0;
					$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtM['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtSch = $dbEx->selSql($sqlText);
					if($dtSch['0']['sumHorario']!=NULL){
						$staffed = $dtSch['0']['sumHorario'];
					}
					
					//Recupera horas del payroll mas exception y mas AP
					//Obtiene horas de payroll para el periodo
					$sqlText = "select sum(payroll_htotal) as stotal from payroll where employee_id=".$dtM['employee_id']." and payroll_date between date '".$fechaIni."' and '".$fechaFin."'";
					$dtPay = $dbEx->selSql($sqlText);
					$horasPayroll = 0.0;
					$horasAp = 0.0;
					$horasException = 0.0;
					if($dbEx->numrows>0){
						$horasPayroll = $dtPay['0']['stotal'];
					}
					//Obtiene horas de las AP en el periodo dado

					$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtM['employee_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap between date '".$fechaIni."' and '".$fechaFin."' and approved_status='A'";
					$dtAp = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						foreach($dtAp as $dtA){
							$horasAp = $horasAp + $dtA['hours_ap'];	
						}
					}
					//Obtine horas de las exceptions en el periodo dado
					$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtM['employee_id']." and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."' and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
					$dtEx = $dbEx->selSql($sqlText);
					$horasException = "0.0";
					if($dbEx->numrows>0){
						$horas = $dtEx['0']['hora']; 
						$min = $dtEx['0']['minutos']; 
						$minutos = $min%60; 
						$minutos = round($minutos/60,2);
						$formatMinutos = explode(".",$minutos);
						$h=0; 
						$h=(int)($min/60); 
						$horas+=$h;
						$horasException = $horas.".".$formatMinutos[1];	
					}
				
					//Obtiene las horas de PAID HOLIDAY
					$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtM['employee_id']." and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."' and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
					$dtPh = $dbEx->selSql($sqlText);
					$horasPaidHoliday ="0.0";
					if($dbEx->numrows){
						$horasPh = $dtPh['0']['hora']; 
						$minPh = $dtPh['0']['minutos']; 
						$minutosPh = $minPh%60; 
						$minutosPh = round($minutosPh/60,2);
						$formatMinutosPh = explode(".",$minutosPh);
						$h=0; 
						$h=(int)($minPh/60); 
						$horasPh+=$h;
						$horasPaidHoliday = $horasPh.".".$formatMinutosPh[1];	
					}
					$wh = $horasPayroll + $horasAp + $horasException + $horasPaidHoliday;
					$ratioh = $staffed - $wh;
				
					$ratiopercent = 0;
					if($staffed>0){
						$ratiopercent = ($wh/$staffed)*100;
					}
					
					//Obtiene notas de QA
					$scoreQA  = 0; 
					$sumaEva = 0;
					$cantidadEva = 0;

					$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitcsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtSumCS = $dbEx->selSql($sqlText);
					if($dtSumCS['0']['sumCS']!=NULL){
						$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
					}
					$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitcsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtCountCS = $dbEx->selSql($sqlText);
					if($dtCountCS['0']['countCS']!=NULL){
						$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
					}
				
					$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' and monitsales_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtSumSales = $dbEx->selSql($sqlText);
					if($dtSumSales['0']['sumSales']!=NULL){
						$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					}
				
					$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' and monitsales_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtCountSales = $dbEx->selSql($sqlText);
					if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
						$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					}
				
					$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp where monitnsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitnsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtSumNS = $dbEx->selSql($sqlText);
					if($dtSumNS['0']['sumNS']!=NULL){
						$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
					}
				
					$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp where monitnsemp_date  between date '".$fechaIni."' and '".$fechaFin."' and monitnsemp_maker='Q' and employee_id=".$dtM['employee_id'];
					$dtCountNS = $dbEx->selSql($sqlText);
					if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
						$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
					}
					if($cantidadEva > 0){
						$scoreQA = $sumaEva/$cantidadEva;
					}
					
					//Suma de tardiness
					
					$sqlText = "select sec_to_time(sum(time_to_sec(metric_tardiness))) as tiempo_tarde from phone_metrics where employee_id=".$dtM['employee_id']." and supervisor_id=".$sup." and metric_date between date '".$fechaIni."' and '".$fechaFin."'";;
					$dtTardiness = $dbEx->selSql($sqlText);
					$tardiness = '00:00:00';
					if($dtTardiness[0]['tiempo_tarde']!=NULL){
						$tardiness = $dtTardiness[0]['tiempo_tarde'];
					
					}
				
				//Completar Tablas
				$tblAht .='<td>'.$aht.'</td></tr>';
				
				$tblRefused .='<td>'.$calls.'</td>
				<td>'.$refused.'</td>
				<td>'.number_format(($refusedProm * 100),2).'</td></tr>';
				
				$tblCompletedHours .='<td>'.number_format($staffed,2).'</td>
				<td>'.number_format($wh,2).'</td>
				<td>'.number_format($ratioh,2).'</td>
				<td>'.number_format($ratiopercent,2).'%</td></tr>';
				
				$tblQA .='<td>'.number_format($scoreQA,2).'%</td></tr>';
				
				$tblTardiness .='<td colspan="2">'.$tardiness.'</td></tr>';
				
			}
			$tblResult .= $tblAht;
			$tblResult .= $tblRefused;
			$tblResult .= $tblCompletedHours;
			$tblResult .= $tblQA;
			$tblResult .= $tblTardiness;
			$tblResult .='</table>';
		echo $tblResult;
	break;

}



?>