<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
    $sqlText = "select date_format(sysdate(),'%d%m%y%h%i%s') timestamp from dual";
    $dtExt = $dbEx->selSql($sqlText);
    
    
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_payroll".$dtExt['0']['timestamp'].".xls");


  //Verifica si la AP esta aprobada o no
  function verificarAprobAp($IdAp){
		$result = 0;
		//datos de la AP
		$sqlText = "select * from apxemp where id_apxemp = ".$IdAp;
        $dbEx = new DBX;
		$dtAp = $dbEx->selSql($sqlText);
		//Verificamos si la AP ya ha sido aprobada, sino verificamos
		if($dtAp['0']['APPROVED_STATUS']=='A'){
			$result = 1;	
		}
		else{
		//datos del empleado que tiene la AP
		$sqlText = "select name_role, ur.id_role, name_depart, nivel_place from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join depart_exc d on d.id_depart=pd.id_depart inner join places pl on pl.id_place=pd.id_place where e.employee_id=".$dtAp['0']['EMPLOYEE_ID'];
		$dtEmp = $dbEx->selSql($sqlText);
		
		//datos del creador de la AP
		$sqlText = "select name_role, ur.id_role, name_depart, d.id_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join depart_exc d on pd.id_depart=d.id_depart where e.employee_id=".$dtAp['0']['AUTOR_AP'];
		$dtAutor = $dbEx->selSql($sqlText);
		$departAutor = 0;
		if($dbEx->numrows>0){
			$departAutor = $dtAutor['0']['name_depart'];	
		}
		
		if($dtAp['0']['ID_TPAP']!=15){
			if($dtEmp['0']['name_role']=='AGENTE' or $dtEmp['0']['name_role']=='SUPERVISOR'){
				if($departAutor!='CHAT'){
					if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7){
						//Verifica si la Ap ya esta aprobada xq es tipo con goce ó sin goce ó incapacidad no necesita la aprobacion de gerencia
						if($dtAp['0']['AUTOR_WORK']!=0 and $dtAp['0']['APPROVED_WORK']=='S' and $dtAp['0']['AUTOR_AREA']!=0 and $dtAp['0']['APPROVED_AREA']=='S' and $dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;
						}
					}
					//AP de agente y supervisores verbales y escritas no necesitan aprobacion de Workforce y gerencia
					else if($dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
						if($dtAp['0']['AUTOR_AREA']!=0 and $dtAp['0']['APPROVED_AREA']=='S' and $dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}	
					}
					//Resto de las AP necesita autorizacion por todos
					else{
						if($dtAp['0']['AUTOR_WORK']!=0 and $dtAp['0']['APPROVED_WORK']=='S' and $dtAp['0']['AUTOR_AREA']!=0 and $dtAp['0']['APPROVED_AREA']=='S' and $dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}	
					}
				}
				
				//Si el creador fue de CHAT solo se autoriza por HR y Gerencia
				else{
					if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7 or $dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
						if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}
					}
					else{
						if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}	
					} 
				}
		}//Termina verificacion de agentes y supervisores
			if($dtEmp['0']['id_role']>3 and $dtEmp['0']['id_role']<7){
				if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7 or $dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;
					}
				}
				//aprobacion de HR y gerencia
				else{
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
					}
				}
			}
			//Si el empleado es de HR
			if($dtEmp['0']['id_role']==7){
				if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7 or $dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
					}	
				}
				else{
					if($dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
					}	
				}
			}
			if($dtEmp['0']['id_role']==8){
				if($dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
				}		
			}
		}
		//Ap de ingreso solo son verificadas por HR
		else{
			if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
				$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
				$dbEx->updSql($sqlText);
				$result = 1;	
			}	
		}
		}
		return $result;
	}
  
  $sqlText = "select distinct(e.employee_id), e.username, e.firstname, e.lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep inner join user_roles u on u.id_role=pd.id_role ".$_POST['filtro']." order by firstname";
  

		$dt = $dbEx->selSql($sqlText);
  
 ?>
 <table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
 <?php 
 if($dbEx->numrows>0){
 ?>
 <tr class="txtForm"><td>N&deg;</td><td>BADGE</td><td>EMPLOYEE</td><td>DAYTIME HOURS</td><td>NOCTURNAL HOURS</td><td>AP HOURS</td><td>VACATIONS</td><td>EXCEPTION HOURS</td><td width="10%">ADDITIONAL HOURS</td><td>PAID HOLIDAY</td><td>DAY OVERTIME</td><td>NIGTH OVERTIME</td><td>HOLIDAY OVERTIME</td><td>TOTAL HOURS</td></tr>
 <?php 
 	$n = 1;
 	foreach($dt as $dtE){
    $sqlText = "select (sum(time_to_sec(payroll_htotal)))/3600 as stotal, (sum(time_to_sec(payroll_daytime)))/3600 as sday, ".
					"(sum(time_to_sec(payroll_nigth)))/3600 as snigth ".
					"from payroll where employee_id=".$dtE['employee_id']." ".$_POST['filtroPay'];

				$dtPay = $dbEx->selSql($sqlText);
				$horasTotal = 0.0;
				$horasDia = 0.0;
				$horasNocturna = 0.0;
				$horasAp = 0.0;
				$horasVacacion = 0.0;
				$horasException = 0.0;
				if($dbEx->numrows>0){
					$horasTotal = $dtPay['0']['stotal'];
					$horasDia = $dtPay['0']['sday'];
					$horasNocturna = $dtPay['0']['snigth'];
				}
				//Obtiene horas de las AP en el periodo dado

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1,7) and hours_ap!='' ".$_POST['filtroAP'];
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$flag = verificarAprobAp($dtA['id_apxemp']);
						if($flag==1){
							$horasAp = $horasAp + $dtA['hours_ap'];
						}	
					}
				}
				
				//Obtiene horas de vacaciones
				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(5) and hours_ap!='' ".$filtroAp;
				$dtVac = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtVac as $dtV){
						$flag = verificarAprobAp($dtV['id_apxemp']);
						if($flag==1){
							$horasVacacion = $horasVacacion + $dtV['hours_ap'];
						}
					}
				}

				//Obtine horas de las exceptions en el periodo dado
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$_POST['filtroExcep']." and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
				$dtEx = $dbEx->selSql($sqlText);
				$horasExceptions = "00:00";
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
				
				//Obtiene las horas de adicionales
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=9 group by ex.employee_id";
				$dtAh = $dbEx->selSql($sqlText);
				$additionalHours ="0.0";
				if($dbEx->numrows){
					$horasAh = $dtAh['0']['hora']; 
					$minAh = $dtAh['0']['minutos']; 
					$minutosAh = $minAh%60; 
					$minutosAh = round($minutosAh/60,2);
					$formatMinutosAh = explode(".",$minutosAh);
					$h=0; 
					$h=(int)($minAh/60); 
					$horasAh+=$h;
					$additionalHours = $horasAh.".".$formatMinutosAh[1];	
				}
				
				//Obtiene las horas de PAID HOLIDAY
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
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
				//Obtine las horas de Day overtime
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=6 group by ex.employee_id";
				$dtDo = $dbEx->selSql($sqlText);
				$horasDayOvertime ="0.0";
				if($dbEx->numrows){
					$horasDo = $dtDo['0']['hora']; 
					$minDo = $dtDo['0']['minutos']; 
					$minutosDo = $minDo%60; 
					$minutosDo = round($minutosDo/60,2);
					$formatMinutosDo = explode(".",$minutosDo);
					$h=0; 
					$h=(int)($minDo/60); 
					$horasDo+=$h;
					$horasDayOvertime = $horasDo.".".$formatMinutosDo[1];	
				}
				//Obtine las horas de Night overtime
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=7 group by ex.employee_id";
				$dtNo = $dbEx->selSql($sqlText);
				$horasNightOvertime ="0.0";
				if($dbEx->numrows){
					$horasNo = $dtNo['0']['hora']; 
					$minNo = $dtNo['0']['minutos']; 
					$minutosNo = $minNo%60; 
					$minutosNo = round($minutosNo/60,2);
					$formatMinutosNo = explode(".",$minutosNo);
					$h=0; 
					$h=(int)($minNo/60); 
					$horasNo+=$h;
					$horasNightOvertime = $horasNo.".".$formatMinutosNo[1];	
				}
				//Obtine las horas de Holiday overtime
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=8 group by ex.employee_id";
				$dtHo = $dbEx->selSql($sqlText);
				$horasHolidayOvertime ="0.0";
				if($dbEx->numrows){
					$horasHo = $dtHo['0']['hora']; 
					$minHo = $dtHo['0']['minutos']; 
					$minutosHo = $minHo%60; 
					$minutosHo = round($minutosHo/60,2);
					$formatMinutosHo = explode(".",$minutosHo);
					$h=0; 
					$h=(int)($minHo/60); 
					$horasHo+=$h;
					$horasHolidayOvertime = $horasHo.".".$formatMinutosHo[1];	
				}
				
				//Suma la planilla con las demas horas
				$horasTotal = $horasTotal + $horasAp + $horasVacacion + $horasException + $horasPaidHoliday;
				//$horasTotal = $horasTotal + $horasAp + $horasVacacion + $horasException + $horasPaidHoliday + $horasDayOvertime + $horasNightOvertime + $horasHolidayOvertime;
 
 ?>
			<tr><td><?php echo $n; ?></td><td><?php echo $dtE['username']; ?></td><td><?php echo $dtE['firstname'].' '.$dtE['lastname']; ?></td><td><?php echo round($horasDia,2); ?></td><td><?php echo round($horasNocturna,2); ?></td><td><?php echo $horasAp; ?></td><td><?php echo $horasVacacion; ?></td><td><?php echo round($horasException,2); ?></td><td><?php echo round($additionalHours,2); ?></td><td><?php echo round($horasPaidHoliday,2); ?></td><td><?php echo round($horasDayOvertime,2); ?></td><td><?php echo round($horasNightOvertime,2); ?></td><td><?php echo round($horasHolidayOvertime,2); ?></td><td><?php echo round($horasTotal,2); ?></td></tr>
		
 <?php
 		$n = $n+1;
 		}
		
 }
 ?>
