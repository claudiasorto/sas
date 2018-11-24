<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=scorecard.xls"); 
  
  function hoursToSecods ($hour) { // $hour must be a string type: "HH:mm:ss"

		$parse = array();
		if (!preg_match ('#^(?<hours>[\d]{2}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#',$hour,$parse)) {
			// Throw error, exception, etc
			throw new RuntimeException ("Hour Format not valid".$hour." ");
		}

		return (int) $parse['hours'] * 3600 + (int) $parse['mins'] * 60 + (int) $parse['secs'];

	}
  
  
  //Inicializacion y asignacion de variables

	$percentHoursCompletion = 0.40;
	$percentQA = 0.40;
	$percentAht = 0.05;
	$percentRefused = 0.05;
	$percentEfficiency = 0.10;
		
	$fechaIni =$_POST['fechaIni'];
	$fechaFin = $_POST['fechaFin'];
	$filtroMetric = " and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
	
	//Busca a todos los agentes 
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where pe.status_plxemp = 'A' and user_status=1 and name_role = 'AGENTE' order by firstname";
		
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$indice = 0;
			foreach($dtEmp as $dtE){
				//indice para guardar en vector las notas obtenidas por empleado
				
				$sumaLlamadas = 0;
				$sumaTiempo = '00:00:00';
				$promLlamada = 0;
				$horaPromLlamada = '00:00:00';
				
				$employee[$indice] = $dtE['employee_id'];
				$notaTotal[$indice] = 0;
				$notaHoursCompletion[$indice] = 0;
				$notaQA[$indice] = 0;
				$notaAht[$indice] = 0;
				$notaRefused[$indice] = 0;
				$notaEfficiency[$indice] = 0;
				
				//Busca AHT
				$sqlText = "select sum(metric_totalcalls) as totalcalls from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				$dtTotalLlamadas = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$sumaLlamadas = $dtTotalLlamadas['0']['totalcalls'];
				}
				$sqlText = "select sec_to_time(sum(time_to_sec(metric_aht_totaltime))) as tiempo from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				
				$dtTime = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtTime['0']['tiempo']!=NULL){
					$sumaTiempo = $dtTime['0']['tiempo'];
				}
				$tiempoDecimal = hoursToSecods($sumaTiempo);
				if($sumaLlamadas>0 and $sumaLlamadas!=''){
					$promLlamada = $tiempoDecimal / $sumaLlamadas;
					$horaPromLlamada = gmdate("H:i:s",$promLlamada);
				}
				//*******Aqui evaluar rangos de promedio por hora
				$formatHoraAht = explode(":",$horaPromLlamada);
				if($formatHoraAht[1]>=5 and $formatHoraAht[1]<=7 and $formatHoraAht[0]==0){
					$notaAht[$indice] = 5;
				}
				else if($formatHoraAht[1]>7 and $formatHoraAht[1]<=8 and $formatHoraAht[0]==0){
					$notaAht[$indice] = 3;
				}
				else if($formatHoraAht[1]>8 and $formatHoraAht[1]<=10 and $formatHoraAht[0]==0){
					$notaAht[$indice] = 1;
				}
				

				//Busca Refused calls
				$sqlText = "select sum(metric_refused) as sumRefused from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				$dtRefused = $dbEx->selSql($sqlText);

				$sumaRefused = 0;
				$promRefused = 0;
				if($dtRefused['0']['sumRefused']!=NULL){
					$sumaRefused = $dtRefused['0']['sumRefused'];
				}
				if($sumaRefused >0){
					$promRefused = $sumaRefused/($sumaLlamadas + $sumaRefused);
				}
				//*****Aqui evaluar rangos de refused call
				if($promRefused>0.02 and $promRefused<=0.05){
					$notaRefused[$indice] = 3;
				}
				else if($promRefused>0 and $promRefused<=0.02){
					$notaRefused[$indice] = 5;
				}
			
				//Busca Eficciency
				$sumaEficiencia = 0;
				$countRegistrosEficiencia = 0;
				$promEficiencia = 0;
				$sqlText = "select sum(metric_efficiency) as sumEfficiency from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				$dtEficiencia = $dbEx->selSql($sqlText);
				
				$sqlText = "select count(1) as countEfficiency from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				$dtCountEficiencia = $dbEx->selSql($sqlText);
				
				if($dtEficiencia['0']['sumEfficiency']!=NULL){
					$sumaEficiencia = $dtEficiencia['0']['sumEfficiency'];
				}
				if($dtCountEficiencia['0']['countEfficiency']!=NULL and $dbEx->numrows>0){
					$countRegistrosEficiencia = $dtCountEficiencia['0']['countEfficiency'];
				}
				
				if($countRegistrosEficiencia>0 and $sumaEficiencia>=0){
					$promEficiencia = $sumaEficiencia/$countRegistrosEficiencia;
				}
				//*****Aqui evaluar rangos de eficiencia
				if($promEficiencia >=0.80 and $promEficiencia<0.85){
					$notaEfficiency[$indice] = 5;	
				}
				else if($promEficiencia>=0.85 and $promEficiencia<0.95){
					$notaEfficiency[$indice] = 7;
				}
				else if($promEficiencia>=0.95){
					$notaEfficiency[$indice] = 10;
				}
				
			
				//Busca scores de QA
				$promEva = 0; 
				$sumaEva = 0;
				$cantidadEva = 0;
				
				$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' and employee_id=".$dtE['employee_id'];
				$dtSumCS = $dbEx->selSql($sqlText);
				if($dtSumCS['0']['sumCS']!=NULL){
					$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
				}
				$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' and employee_id=".$dtE['employee_id'];
				$dtCountCS = $dbEx->selSql($sqlText);
				if($dtCountCS['0']['countCS']!=NULL){
					$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
				}
				
				$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' and employee_id=".$dtE['employee_id'];
				$dtSumSales = $dbEx->selSql($sqlText);
				if($dtSumSales['0']['sumSales']!=NULL){
					$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					
				}
				
				$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' and employee_id=".$dtE['employee_id'];
				$dtCountSales = $dbEx->selSql($sqlText);
				if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
					$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					
				}
				
				$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp where monitnsemp_date between date '".$fechaIni."' and '".$fechaFin."' and employee_id=".$dtE['employee_id'];
				$dtSumNS = $dbEx->selSql($sqlText);
				if($dtSumNS['0']['sumNS']!=NULL){
					$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
				}
				
				$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp where monitnsemp_date between date '".$fechaIni."' and '".$fechaFin."' and employee_id=".$dtE['employee_id'];
				$dtCountNS = $dbEx->selSql($sqlText);
				if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
					$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
				}
				if($cantidadEva > 0){
					$promEva = $sumaEva/$cantidadEva;
				}
				//*********Aqui evaluar promedio de Scores QA
				if($promEva>=85 and $promEva<90){
					$notaQA[$indice] = 20;	
				}
				else if($promEva>=90 and $promEva<95){
					$notaQA[$indice] = 30;
				}
				else if($promEva>=95){
					$notaQA[$indice] = 40;
				}
				
			
				//Busca el hours completion
				
				//Recupera las horas del schedule
				$horasProgramadas = 0;
				$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$horasProgramadas = $dtSch['0']['sumHorario'];
				}
				
				
				$sqlText = "select sum(HOUR(TIMEDIFF(sch_departure, sch_entry))) as hora, sum(MINUTE(TIMEDIFF(sch_departure, sch_entry))) as minutos from schedules sch where employee_id=".$dtE['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."' group by employee_id";
				$dtSh = $dbEx->selSql($sqlText);
				$horasTotales = "0.0";
				if($dbEx->numrows>0){
					$horas = $dtSh['0']['hora']; 
					$min = $dtSh['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasTotales = $horas.".".$formatMinutos[1];
					
				}
				
				$sqlText = "select sum(HOUR(TIMEDIFF(sch_lunchout,sch_lunchin))) as hora, sum(MINUTE(TIMEDIFF(sch_lunchout,sch_lunchin))) as minutos from schedules sch where employee_id=".$dtE['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."' group by employee_id";
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
				
				$horasProgramadas = 0;
				if($horasTotales>0){
					$horasProgramadas = $horasTotales - $horasLunch;
				}
				
				//Recupera horas del payroll mas exception, mas AP
				//Obtiene horas de payroll para el periodo
				$sqlText = "select sum(payroll_htotal) as stotal, sum(payroll_daytime) as sday, sum(payroll_nigth) as snigth from payroll where employee_id=".$dtE['employee_id']." and payroll_date between date '".$fechaIni."' and '".$fechaFin."'";	
				$dtPay = $dbEx->selSql($sqlText);
				$horasPayroll = 0.0;
				$horasDia = 0.0;
				$horasNocturna = 0.0;
				$horasAp = 0.0;
				$horasException = 0.0;
				if($dbEx->numrows>0){
					$horasPayroll = $dtPay['0']['stotal'];
					$horasDia = $dtPay['0']['sday'];
					$horasNocturna = $dtPay['0']['snigth'];
				}
				//Obtiene horas de las AP en el periodo dado

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap between date '".$fechaIni."' and '".$fechaFin."' and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$horasAp = $horasAp + $dtA['hours_ap'];	
					}
				}
				//Obtine horas de las exceptions en el periodo dado
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
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
				$horasPayroll = $horasPayroll + $horasAp + $horasException + $horasPaidHoliday;
				$completado = 0;
				if($horasProgramadas>0){
					$completado = ($horasPayroll/$horasProgramadas);
				}
				//***Calculo de nota por hours completion
				
				if($completado >=0.85 and $completado<0.90){
					$notaHoursCompletion[$indice] = 5;
				}
				else if($completado >=0.90 and $completado<0.95){
					$notaHoursCompletion[$indice] = 7.5;	
				}
				else if($completado >=0.95){
					$notaHoursCompletion[$indice] = 10;
				}
				
			
				
				$notaTotal[$indice] = $notaHoursCompletion[$indice] + $notaQA[$indice] + $notaAht[$indice] + $notaRefused[$indice]  + $notaEfficiency[$indice];
				$indice = $indice + 1;
				
			}
			//Por medio del algoritmo de burbuja ordena las notas de menor a mayor
			

			for($i=1; $i<$indice; $i++){
				for($j=0; $j<$indice-$i; $j++){
					if($notaTotal[$j]>$notaTotal[$j+1]){
						$k = $employee[$j+1]; $employee[$j+1]=$employee[$j]; $employee[$j]=$k;
						$k = $notaHoursCompletion[$j+1]; $notaHoursCompletion[$j+1]=$notaHoursCompletion[$j]; $notaHoursCompletion[$j]=$k;
						$k = $notaQA[$j+1]; $notaQA[$j+1]=$notaQA[$j]; $notaQA[$j]=$k;
						$k = $notaAht[$j+1]; $notaAht[$j+1]=$notaAht[$j]; $notaAht[$j]=$k;
						$k = $notaRefused[$j+1]; $notaRefused[$j+1]=$notaRefused[$j]; $notaRefused[$j]=$k;
						$k = $notaEfficiency[$j+1]; $notaEfficiency[$j+1]=$notaEfficiency[$j]; $notaEfficiency[$j]=$k;
						$k = $notaTotal[$j+1]; $notaTotal[$j+1]=$notaTotal[$j]; $notaTotal[$j]=$k;
					}
				}	
			}

			$top = $indice;
			if($_POST['top']>0){
				$top = $_POST['top'];
			}
			
			//Recorrer los agentes para ver si cumplen con los filtros 
						
			$rslt = '<table cellpadding="2" cellspacing="2" border="1" bordercolor="#003366">';
			$rslt .='<tr bgcolor="#003366" style="font-size:11px">
			<td><font color="#FFFFFF" face="Tahoma">Badge</font></td>
			<td><font color="#FFFFFF" face="Tahoma">Employee</font></td>
			<td align="center"><font color="#FFFFFF" face="Tahoma">Global position</font></td>
			<td align="center"><font color="#FFFFFF" face="Tahoma">Score</font></td>
			<td align="center"><font color="#FFFFFF" face="Tahoma">Hours completion <br>'.($percentHoursCompletion * 100).'%</font></td>
			<td align="center"><font color="#FFFFFF" face="Tahoma">Quality <br>'.($percentQA * 100).'%</font></td>
			<td align="center"><font color="#FFFFFF" face="Tahoma">AHT <br>'.($percentAht * 100).'%</font></td>
			<td align="center"><font color="#FFFFFF" face="Tahoma">Refused <br>'.($percentRefused * 100).'%</font></td>
			<td align="center"><font color="#FFFFFF" face="Tahoma">Efficiency <br>'.($percentEfficiency * 100).'%</font></td></tr>';
			
			$indice = $indice-1;
			$contador = 0;
			for($i=$indice; $i>=0; $i--){
				$contador = $contador +1;
				
				$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep where user_status=1 and pe.status_plxemp='A' ".$_POST['filtro']." and e.employee_id=".$employee[$i];
				$dtEmpFiltros = $dbEx->selSql($sqlText);
				//Verifica si agente cumplio las condiciones
				if($dbEx->numrows>0){
				$pos = ($indice - $i) + 1;
					$rslt .='<tr style="font-family:Tahoma; font-size:11px;"><td><font color="#003366" face="Tahoma">'.$dtEmpFiltros['0']['username'].'</td>
					<td><font color="#003366" >'.$dtEmpFiltros['0']['firstname']." ".$dtEmpFiltros['0']['lastname'].'</td>
					<td align="center" ><font color="#003366" >'.$pos.'</td>
					<td align="center"><font color="#003366" >'.$notaTotal[$i].'</td>
					<td align="center"><font color="#003366" >'.$notaHoursCompletion[$i].'</td>
					<td align="center"><font color="#003366" >'.$notaQA[$i].'</td>
					<td align="center"><font color="#003366" >'.$notaAht[$i].'</td>
					<td align="center"><font color="#003366" >'.$notaRefused[$i].'</td>
					<td align="center"><font color="#003366" >'.$notaEfficiency[$i].'</td></tr>';
				
				} //Termina segundo numrows
				if($contador >= $top){
					$i = -1;
				}
			
			} //Termina for
			
		}//Termina numrows

	echo $rslt;
?>
