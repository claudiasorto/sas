<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
    $sqlText = "select date_format(sysdate(),'%d%m%y%h%i%s') timestamp from dual";
    $dtExt = $dbEx->selSql($sqlText);
    
    
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_payroll".$dtExt['0']['timestamp'].".xls");
  
  $sqlText = "select distinct(e.employee_id), e.username, ".
  					"e.firstname, ".
  					"e.lastname ".
					"from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id ".
					"inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".
					"inner join user_roles u on u.id_role=pd.id_role ".$_POST['filtro']." order by firstname";

 	$dt = $dbEx->selSql($sqlText);
 	$fec_ini = $_POST['fec_ini'];
 	$fec_fin = $_POST['fec_fin'];
  
 ?>
 <table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
 <?php 
 if($dbEx->numrows>0){
 ?>
 <tr>
 	<td bgcolor="#003366"><font color="#FFFFFF">N&deg;</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">BADGE</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">EMPLOYEE</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">DAYTIME HOURS</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">NOCTURNAL HOURS</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">AP HOURS</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">VACATIONS</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">EXCEPTION HOURS</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">ADDITIONAL HOURS</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">HOLIDAY</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">DAY OVERTIME</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">NIGTH OVERTIME</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">HOLIDAY OVERTIME</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">TOTAL WORKED HOURS</font></td>
 	<td bgcolor="#003366"><font color="#FFFFFF">TOTAL PROGRAMMED HOURS</font></td></tr>
 <?php 
 	$n = 1;
 	foreach($dt as $dtE){

 		$sqlText = "select (h_total + feriadas) stotal, ".
					"(h_dia + feriadas) sday, ".
					"(h_noct + h_noct_feriada) snigth, ".
					"h_feriada_trab ".
					"from (select ( ".
					"(select ifnull(round((sum(TIME_TO_SEC(payroll_htotal)))/3600,2),0) ".
					"from payroll ".
				    "where employee_id=".$dtE['employee_id']." ".
				    "and (payroll_date between date '".$fec_ini."' and '".$fec_fin."') ".
				    "and payroll_date not in ".
						"(select holiday ".
				        "from holidays ".
				        "where holiday between date '".$fec_ini."' and '".$fec_fin."'))) h_total, ".
				    "(select ifnull(round((sum(TIME_TO_SEC(payroll_htotal)))/3600,2),0) ".
					"from payroll ".
				    "where employee_id= ".$dtE['employee_id']." ".
				    "and (payroll_date between date '".$fec_ini."' and '".$fec_fin."') ".
				    "and payroll_date in ".
						"(select holiday ".
				        "from holidays ".
				        "where holiday between date '".$fec_ini."' and '".$fec_fin."')) h_feriada_trab, ".
					"(select ifnull(round((sum(TIME_TO_SEC(payroll_daytime)))/3600,2),0) ".
					"from payroll ".
				    "where employee_id= ".$dtE['employee_id']." ".
				    "and (payroll_date between date '".$fec_ini."' and '".$fec_fin."') ".
				    "and payroll_date not in ".
						"(select holiday ".
				        "from holidays ".
				        "where holiday between date '".$fec_ini."' and '".$fec_fin."')) h_dia, ".
					"(select ifnull(round((sum(TIME_TO_SEC(payroll_nigth)))/3600,2),0) ".
					"from payroll ".
				    "where employee_id= ".$dtE['employee_id']." ".   
				    "and (payroll_date between date '".$fec_ini."' and '".$fec_fin."') ".
				    "and payroll_date) h_noct, ".
					"(select ifnull(round((sum(TIME_TO_SEC(payroll_nigth)))/3600,2),0) ".
					"from payroll ".
				    "where employee_id= ".$dtE['employee_id']." ".
				    "and (payroll_date between date '".$fec_ini."' and '".$fec_fin."') ".
				    "and payroll_date in ".
						"(select holiday ".  
				        "from holidays ".
				        "where holiday between date '".$fec_ini."' and '".$fec_fin."')) h_noct_feriada, ".
					"((select ifnull(round((((SUM(TIME_TO_SEC(sch_departure))) - ".
						"(SUM(TIME_TO_SEC(sch_entry)))) - ".
				        "((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - ".
				        "(SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2),0) holiday ".
				        "from schedules ".
				        "where employee_id = ".$dtE['employee_id']." ".
				        "and sch_date in ".
				        "(select holiday ".   
							"from holidays ".    
				            "where holiday between date '".$fec_ini."' and '".$fec_fin."')) ".
					") feriadas ".
				    "from dual) a";

				$dtPay = $dbEx->selSql($sqlText);
				$horasTotal = 0.0;
				$horasDia = 0.0;
				$horasNocturna = 0.0;
				$horasAp = 0.0;
				$horasVacacion = 0.0;
				$horasException = 0.0;
				$horasAdicionales = 0.0;
				$horasPaidHoliday = 0.0;
				$horasDayOvertime = 0.0;
				$horasNightOvertime = 0.0;
				$horasHolidayOvertime = 0.0;
				$totalProgramadas = 0.0;

				if($dbEx->numrows>0){
					$horasTotal = $dtPay['0']['stotal'];
					$horasDia = $dtPay['0']['sday'];
					$horasNocturna = $dtPay['0']['snigth'];
				}
				//Obtiene horas de las AP en el periodo dado

				$sqlText = "select sum(hours_ap) as hap ".
					"from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap ".
					"where tp.affects_salary = '+' ".
					"and has_time = 'Y' and has_start_date = 'Y' ".
					"and (startdate_ap between date '".$fec_ini."' and '".$fec_fin."') ".
					"and employee_id= ".$dtE['employee_id']." ".
					"and approved_status='A' ";

				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$horasAp = $horasAp + $dtA['hap'];
				}
				
				//Obtiene horas de vacaciones
				$sqlText = "select startdate_ap, enddate_ap ".
					 "from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap ".
					 "where tp.affects_salary = '+' ".
					 "and has_time = 'N' and has_start_date = 'Y' and has_end_date = 'Y' ".
					 "and (startdate_ap between date '".$fec_ini."' and '".$fec_fin."') ".
					 "and employee_id=".$dtE['employee_id']." ".
					 "and approved_status='A'";

				$dtVacacion = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach ($dtVacacion as $dtV) {
						$sqlText = "select round((((SUM(TIME_TO_SEC(sch_departure))) - ".
							"(SUM(TIME_TO_SEC(sch_entry)))) - ".
							"((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - ".
							"(SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) horas ".
							"from schedules ".
							"where employee_id = ".$dtE['employee_id']." ".
							"and sch_date between date '".$dtV['startdate_ap']."' and '".$dtV['enddate_ap']."'";

						$dtHV = $dbEx->selSql($sqlText);

						$horasVacacion = $horasVacacion + $dtHV['0']['horas'];	

					}
					
				}
							
				//Obtine horas de las exceptions en el periodo dado
				$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - ".
					"(SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_excep ".
		 			"from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id ".
		 			"where ex.employee_id=".$dtE['employee_id']." ".
		 			"and (exceptionemp_date between date '".$fec_ini."' ".
		 			"and '".$fec_fin."') and exceptionemp_approved='A' ".
		 			"and exceptiontp_level=1 group by ex.employee_id";

				$dtEx = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$horasException = $dtEx['0']['h_excep'];
					
				}
				//Obtiene las horas de adicionales
				$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) ".
					" - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_adit ".
					"from exceptionxemp ex inner join exceptions_type et on ".
					"et.exceptiontp_id=ex.exceptiontp_id ".
					"where ex.employee_id=".$dtE['employee_id']." ".
					"and (exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."') ".
					"and exceptionemp_approved='A' and ex.exceptiontp_id=9 group by ex.employee_id";
		
				$dtAh = $dbEx->selSql($sqlText);
				if($dbEx->numrows){
					$horasAdicionales = $dtAh['0']['h_adit'];
				}
				
				//Obtiene las horas feriadas
				$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) ".
					" - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_excep ".
					"from exceptionxemp ex inner join exceptions_type et on ".
					"et.exceptiontp_id=ex.exceptiontp_id ".
					"where ex.employee_id=".$dtE['employee_id']." ".
					"and (exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."') ".
					"and exceptionemp_approved='A' and ex.exceptiontp_id=5 group by ex.employee_id";
		
				$dtEx = $dbEx->selSql($sqlText);
				$horasPaidHoliday = $dtPay['0']['h_feriada_trab'];
				if($dbEx->numrows){
					$horasPaidHoliday = $horasPaidHoliday + $dtEx['0']['h_excep'];
				}

				//Obtiene las horas extras
				$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) ".
					" - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_excep ".
					"from exceptionxemp ex inner join exceptions_type et on ".
					"et.exceptiontp_id=ex.exceptiontp_id ".
					"where ex.employee_id=".$dtE['employee_id']." ".
					"and (exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."') ".
					"and exceptionemp_approved='A' and ex.exceptiontp_id=6 group by ex.employee_id";
		
				$dtEx = $dbEx->selSql($sqlText);
				if($dbEx->numrows){
					$horasDayOvertime = $dtEx['0']['h_excep'];
				}

				//Obtiene las horas nocturnas
				$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) ".
					" - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_excep ".
					"from exceptionxemp ex inner join exceptions_type et on ".
					"et.exceptiontp_id=ex.exceptiontp_id ".
					"where ex.employee_id=".$dtE['employee_id']." ".
					"and (exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."') ".
					"and exceptionemp_approved='A' and ex.exceptiontp_id=7 group by ex.employee_id";
		
				$dtEx = $dbEx->selSql($sqlText);
				if($dbEx->numrows){
					$horasNightOvertime = $dtEx['0']['h_excep'];
				}

				//Obtiene horas extas feriadas 
				$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) ".
					" - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_excep ".
					"from exceptionxemp ex inner join exceptions_type et on ".
					"et.exceptiontp_id=ex.exceptiontp_id ".
					"where ex.employee_id=".$dtE['employee_id']." ".
					"and (exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."') ".
					"and exceptionemp_approved='A' and ex.exceptiontp_id=8 group by ex.employee_id";
		
				$dtEx = $dbEx->selSql($sqlText);
				if($dbEx->numrows){
					$horasHolidayOvertime = $dtEx['0']['h_excep'];
				}				

				
				//Suma la planilla con las demas horas
				$horasTotal = $horasTotal 
						+ $horasAp 
						+ $horasVacacion 
						+ $horasAdicionales
						+ $horasException 
						+ $horasPaidHoliday
						+ $horasDayOvertime
						+ $horasNightOvertime
						+ $horasHolidayOvertime;

				//Horas Programadas
				$totalProgramadas = 0;

				$sqlText = "select round((((SUM(ifnull(TIME_TO_SEC(sch_departure),0))) - (SUM(ifnull(TIME_TO_SEC(sch_entry),0)))) -  ".
                            "((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) sumhoras  ".
    						" from schedules ".
    						" where employee_id = ".$dtE['employee_id'].
							" and sch_date between date '".$fec_ini."' and '".$fec_fin."'";

				$dtProgramadas = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtProgramadas['0']['sumhoras']!=NULL){
					$totalProgramadas = $dtProgramadas['0']['sumhoras'];
				}
 
 ?>
			<tr>
				<td><?php echo $n; ?></td>
				<td><?php echo $dtE['username']; ?></td>
				<td><?php echo $dtE['firstname'].' '.$dtE['lastname']; ?></td>
				<td><?php echo round($horasDia,2); ?></td>
				<td><?php echo round($horasNocturna,2); ?></td>
				<td><?php echo round($horasAp,2); ?></td>
				<td><?php echo round($horasVacacion,2); ?></td>
				<td><?php echo round($horasException,2); ?></td>
				<td><?php echo round($horasAdicionales,2); ?></td>
				<td><?php echo round($horasPaidHoliday,2); ?></td>
				<td><?php echo round($horasDayOvertime,2); ?></td>
				<td><?php echo round($horasNightOvertime,2); ?></td>
				<td><?php echo round($horasHolidayOvertime,2); ?></td>
				<td><?php echo round($horasTotal,2); ?></td>
				<td><?php echo round($totalProgramadas,2); ?></td></tr>
		
 <?php
 		$n = $n+1;
 		}
		
 }
 ?>
