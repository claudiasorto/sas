<?php
//Funciones para Agent Scorecard	
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
switch($_POST['Do']){
 case 'hoursCompletionReport':
 	$rslt = cargaPag("../mttoHourScorecard/filtrosRepScorecard.php");
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
			$sqlText = "select * from account where id_typeacc=2 order by name_account ";
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
	
 	echo $rslt;
	break;
	
	case 'loadRepScorecard':
		$filtro = " ";
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];	
		}
		if($_POST['depart']>0){
			$filtro .=" and pd.id_depart=".$_POST['depart'];
		}
		if($_POST['posicion']>0){
			$filtro .=" and pd.id_place=".$_POST['posicion'];
		}
		if($_POST['jefe']>0){
			$filtro .=" and e.id_supervisor=".$_POST['jefe'];	
		}
		if($_POST['status']>=0){
			$filtro .=" and e.user_status=".$_POST['status'];
		}
		if(strlen($_POST['emp'])>0){
			$filtro .=" and e.employee_id=".$_POST['emp'];
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (e.firstname like '%".$_POST['nombre']."%' or e.lastname like '%".$_POST['nombre']."%' or concat(firstname,' ',lastname) like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and username like '%".$_POST['badge']."%'";
		}
		
		$sqlText = "select e.employee_id, firstname, lastname, username from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep where pe.status_plxemp='A' ".$filtro;
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="backTablaMain"  bordercolor="#069" align="center"  width="800">';
		if($dbEx->numrows>0){
			$rslt .='<tr class="backTablaForm">
			<td width="10%">Badge</td>
			<td width="45%">Employee</td>
			<td width="15%">Scheduled time</td>
			<td width="15%">Time logged</td>
			<td width="15%">hours completion</td></tr>';
			
			foreach($dtEmp as $dtE){
				//Recupera las horas del schedule
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
				
				//Recupera horas del payroll mas exception y mas AP
				//Obtiene horas de payroll para el periodo
				$sqlText = "select sum(payroll_htotal) as stotal, sum(payroll_daytime) as sday, sum(payroll_nigth) as snigth from payroll where employee_id=".$dtE['employee_id']." and payroll_date between date '".$fechaIni."' and '".$fechaFin."'";	
				$dtPay = $dbEx->selSql($sqlText);
				$horasTotal = 0.0;
				$horasDia = 0.0;
				$horasNocturna = 0.0;
				$horasAp = 0.0;
				$horasException = 0.0;
				if($dbEx->numrows>0){
					$horasTotal = $dtPay['0']['stotal'];
					$horasDia = $dtPay['0']['sday'];
					$horasNocturna = $dtPay['0']['snigth'];
				}
				//Obtiene horas de las AP en el periodo dado

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1,7) and hours_ap!='' and startdate_ap between date '".$fechaIni."' and '".$fechaFin."' and approved_status='A'";
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
				$horasTotal = $horasTotal + $horasAp + $horasException + $horasPaidHoliday;
				
				
			}//Termina foreach
		}
		else{
			$rslt .='<tr><td>No Matches</td></tr>';	
		}
		
		
		
	break;
 
}
?>