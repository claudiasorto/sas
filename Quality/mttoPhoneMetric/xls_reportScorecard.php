<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=reportScorecard.xls"); 
  
  	function hoursToSecods ($hour) { // $hour must be a string type: "HH:mm:ss"

		$parse = array();
		if (!preg_match ('#^(?<hours>[\d]{2}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#',$hour,$parse)) {
			// Throw error, exception, etc
			throw new RuntimeException ("Hour Format not valid".$hour." ");
		}

		return (int) $parse['hours'] * 3600 + (int) $parse['mins'] * 60 + (int) $parse['secs'];

	}  
  
  $sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A'".$_POST['filtro']." order by firstname";
	$dtEmp = $dbEx->selSql($sqlText);
	?>
	<table border="1" bordercolor="#003366">
    <?php
		if($dbEx->numrows>0){
			?>
            <tr style="background-color:#069; color:#FFF; font:Tahoma; font-size:11px">
            <td>Badge</td>
			<td>Agent</td>
			<td>Total Calls</td>
			<td>AHT</td>
			<td>Refused calls</td>
			<td>Efficiency</td>
			<td>Quality score</td>
			<td># Q.A</td>
			<td>Hours Completion</td></tr>
            <?php
			foreach($dtEmp as $dtE){
				
				//Obtiene el promedio de tiempo en llamadas
				$sumaTime = '00:00:00';
				$tiempoDecimal = 0;
				$sumaCall = 0;
				$promLlamada = 0;
				$horaPromLlamada = '00:00:00';
				
				//Suma el tiempo total en llamadas

				$sqlText = "select sec_to_time(sum(time_to_sec(metric_aht_totaltime))) as tiempo from phone_metrics where employee_id=".$dtE['employee_id']." ".$_POST['filtroMetric'];
				
				$dtTime = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtTime['0']['tiempo']!=NULL){
					$sumaTime = $dtTime['0']['tiempo'];
				}
				$tiempoDecimal = hoursToSecods($sumaTime);
							
				$sqlText = "select sum(metric_totalcalls) as totalcalls from phone_metrics where employee_id=".$dtE['employee_id']." ".$_POST['filtroMetric'];
				$sCall = $dbEx->selSql($sqlText);
				if($sCall['0']['totalcalls']!=NULL){
					$sumaCall = $sCall['0']['totalcalls'];	
				}
				if($sumaCall!='' and $sumaCall>0){
					$promLlamada = $tiempoDecimal / $sumaCall;
					$horaPromLlamada = gmdate("H:i:s",$promLlamada);
				}
				//Obtiene el promedio de refused call
				
				$sqlText = "select sum(metric_refused) as sumRefused from phone_metrics where employee_id=".$dtE['employee_id']." ".$_POST['filtroMetric'];
				$dtRefused = $dbEx->selSql($sqlText);

				$sumaRefused = 0;
				$promRefused = 0;
				if($dtRefused['0']['sumRefused']!=NULL){
					$sumaRefused = $dtRefused['0']['sumRefused'];
				}
				if($sumaRefused >0){
					$promRefused = $sumaRefused/($sumaCall + $sumaRefused);
				}
				
				//Obtiene el promedio de eficiencia
				$sqlText = "select sum(metric_efficiency) as sumEfficiency from phone_metrics where employee_id=".$dtE['employee_id']." ".$_POST['filtroMetric'];
				$dtEficiencia = $dbEx->selSql($sqlText);
				
				$sqlText = "select count(1) as countEfficiency from phone_metrics where employee_id=".$dtE['employee_id']." ".$_POST['filtroMetric'];
				$dtCountEficiencia = $dbEx->selSql($sqlText);
				
				$sumaEficiencia = 0;
				$countRegistrosEficiencia = 0;
				$promEficiencia = 0;
				if($dtEficiencia['0']['sumEfficiency']!=NULL){
					$sumaEficiencia = $dtEficiencia['0']['sumEfficiency'];
				}
				if($dtCountEficiencia['0']['countEfficiency']!=NULL and $dbEx->numrows>0){
					$countRegistrosEficiencia = $dtCountEficiencia['0']['countEfficiency'];
				}
				
				if($countRegistrosEficiencia>0 and $sumaEficiencia>=0){
					$promEficiencia = $sumaEficiencia/$countRegistrosEficiencia;
				}
				
				$promEficiencia = $promEficiencia * 100;
				$promRefused = $promRefused * 100;
				
				//Obtiene la puntuacion de calidad para el periodo
				$promEva = 0; 
				$sumaEva = 0;
				$cantidadEva = 0;
				

				$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp ".$_POST['filtroCS']." and employee_id=".$dtE['employee_id'];
				$dtSumCS = $dbEx->selSql($sqlText);
				if($dtSumCS['0']['sumCS']!=NULL){
					$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
				}
				$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp ".$_POST['filtroCS']." and employee_id=".$dtE['employee_id'];
				$dtCountCS = $dbEx->selSql($sqlText);
				if($dtCountCS['0']['countCS']!=NULL){
					$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
				}
				
				$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp ".$_POST['filtroSales']." and employee_id=".$dtE['employee_id'];
				$dtSumSales = $dbEx->selSql($sqlText);
				if($dtSumSales['0']['sumSales']!=NULL){
					$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					
				}
				
				$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp ".$_POST['filtroSales']." and employee_id=".$dtE['employee_id'];
				$dtCountSales = $dbEx->selSql($sqlText);
				if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
					$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					
				}
				
				$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp ".$_POST['filtroNS']." and employee_id=".$dtE['employee_id'];
				$dtSumNS = $dbEx->selSql($sqlText);
				if($dtSumNS['0']['sumNS']!=NULL){
					$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
				}
				
				$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp ".$_POST['filtroNS']." and employee_id=".$dtE['employee_id'];
				$dtCountNS = $dbEx->selSql($sqlText);
				if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
					$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
				}
				if($cantidadEva > 0){
					$promEva = $sumaEva/$cantidadEva;
				}
				
				//Obtiene Lateness
				//Recupera las horas del schedule
				$horasProgramadas = 0;
				$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['employee_id']." and sch_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."'";
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$horasProgramadas = $dtSch['0']['sumHorario'];
				}
				
				//Recupera horas del payroll mas exception y mas AP
				//Obtiene horas de payroll para el periodo
				$sqlText = "select sum(payroll_htotal) as stotal, sum(payroll_daytime) as sday, sum(payroll_nigth) as snigth from payroll where employee_id=".$dtE['employee_id']." and payroll_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."'";	
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

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."' and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$horasAp = $horasAp + $dtA['hours_ap'];	
					}
				}
				//Obtine horas de las exceptions en el periodo dado
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and (exceptionemp_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." and (exceptionemp_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."') and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
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
					$completado = ($horasPayroll/$horasProgramadas)*100;
				}
				
				
				?>

				<tr style="font:Tahoma; font-size:11px; color:#003">
                <td><?php echo $dtE['username'];?></td>
                <td><?php echo $dtE['firstname']." ".$dtE['lastname'];?></td>
                <td align="center"><?php echo $sumaCall; ?></td>
                <td align="center"><?php echo $horaPromLlamada; ?></td>
                <td align="center"><?php echo number_format($promRefused,2);?>%</td>
                <td align="center"><?php echo number_format($promEficiencia,2);?>%</td>
                <td align="center"><?php echo number_format($promEva,2);?>%</td>
                <td align="center"><?php echo $cantidadEva; ?></td>
                <td align="center"><?php echo number_format($completado,2); ?>%</td>
                </tr>
				<?php
				
			}//Termina de evaluar por empleado
			
		}
	
  
?>