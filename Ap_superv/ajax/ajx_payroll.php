<?php
		//Funciones para planilla	
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
 
  function restaFechas($dFecIni, $dFecFin)
	{
    $dFecIni = str_replace("-","",$dFecIni);
    $dFecIni = str_replace("/","",$dFecIni);
    $dFecFin = str_replace("-","",$dFecFin);
    $dFecFin = str_replace("/","",$dFecFin);

    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);
    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);

    $date1 = mktime(0,0,0,$aFecIni[2], $aFecIni[1], $aFecIni[3]);
    $date2 = mktime(0,0,0,$aFecFin[2], $aFecFin[1], $aFecFin[3]);

    return round(($date2 - $date1) / (60 * 60 * 24));
}
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
	
	 function sumarHoras($h1,$h2)
	{
		/*$h2h = date('H', strtotime($h2));
		$h2m = date('i', strtotime($h2));
		$h2s = date('s', strtotime($h2));
		$hora2 =$h2h." hour ". $h2m ." min ".$h2s ." second";

		$horas_sumadas= $h1." + ". $hora2;
		$text=date('H:i:s', strtotime($horas_sumadas)) ;
		return $text;   */
		$dbExec = new DBX;
		$sqlText = "select sec_to_time((time_to_sec('".$h1."') + time_to_sec('".$h2."'))) result from dual";
		$result = $dbExec->selSql($sqlText);
		return $result['0']['result'];

	}
	function restarHoras($h1,$h2)
	{
  		$dbExec = new DBX;
		$sqlText = "select time_format(sec_to_time(if((time_to_sec('".$h1."') - time_to_sec('".$h2."'))<0,0, ".
			" (time_to_sec('".$h1."') - time_to_sec('".$h2."')))),'%H:%i:%s') result from dual";
		$result = $dbExec->selSql($sqlText);
		return $result['0']['result'];

	}
	
	//Funcion para saber si tiene las AP aprobadas, retorna 1 si la AP ha sido aprobada y 0 si no ha sido aprobada.
	function verificarAprobAp($IdAp){
		$result = 0;
		//datos de la AP
		$sqlText = "SELECT ID_APXEMP, ID_TPAP, EMPLOYEE_ID, ID_CENTER, STARTDATE_AP, ENDDATE_AP, HOURS_AP, ".
				"STORAGEDATE_AP, ID_TPDISCIPLINARY, TYPESANCTION_AP, TYPEINCAP_AP, APPROVED_EMP, ".
    			"AUTOR_AP, IFNULL(AUTOR_WORK,0) AUTOR_WORK, APPROVED_WORK, IFNULL(AUTOR_AREA,0) AUTOR_AREA, " .
    			"APPROVED_AREA,IFNULL(AUTOR_HR,0) AUTOR_HR, APPROVED_HR, IFNULL(AUTOR_GENERALMAN,0) ".
    			"AUTOR_GENERALMAN, APPROVED_GENERAL, COMMENT_AP, STATUS_AP, REJECTED_COMMENTS, APPROVED_STATUS, ".
				"ID_PLACEXDEP_NEW, ID_PLACEXDEP_OLD, SUPERVISOR_NEW, SUPERVISOR_OLD ".
				"FROM apxemp where ID_APXEMP = ".$IdAp;
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
	

switch($_POST['Do']){
	
	//Muestra formulario para cargar planilla como adjunto
	case 'newPayroll':
		$rslt = cargaPag("../payroll/newPayroll.php");
		$sqlText = "select payroll_tp_id, payroll_tp_name from payroll_type where ifnull(payroll_tp_effective_end_date,sysdate()) >= sysdate()";
		$dtTp = $dbEx->selSql($sqlText);
		$optTp = '<select id="lsPayrollType" name="lsPayrollType" class="txtPag">';
		$optTp .= '<option value="0">[Select one value]</option>';
		if ($dbEx->numrows>0){
			foreach($dtTp as $dtT){
				$optTp .='<option value="'.$dtT['payroll_tp_id'].'">'.$dtT['payroll_tp_name'].'</option>';	
			}	
		}
		else{
			$optTp .= '<option value="-1"> There are no payroll formats entered</option>';
		}
		$optTp .= '</select>';
		$rslt = str_replace("<!--optTp-->",$optTp,$rslt);
		
		echo $rslt;
	break; 
	//
	case 'newRegHora':
		$rslt = cargaPag("../payroll/newRegHora.php");
		echo $rslt;
	break;

	case 'loadPayxEmp':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$sqlText = "select employee_id, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$tblPay = '<table width="825" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
		
		if($dbEx->numrows >0){
			$tblPay .='<tr class="txtForm"><td colspan="7" align="center">HOURS RECORDED FOR EACH AGENT, PLEASE ENTER THE TOTAL NOCTURNAL HOURS</td></tr>';				
			$tblPay .='<tr class="txtForm"><td width="5%">N&deg;</td><td width="10%">BADGE</td><td width="35%">EMPLOYEE</td><td width="12%">DAYTIME HOURS</td><td width="12%">NOCTURNAL HOURS</td><td width="12%">AP HOURS</td><td width="12%">TOTAL HOURS</td></tr>';
			$n = 1;
			foreach($dtEmp as $dtE){
				//Obtiene horas de las AP en el periodo dado
				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1,7) and hours_ap!='' and startdate_ap='".$fecha."'";
				$dtAp = $dbEx->selSql($sqlText);
				$horasAp = 0;
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$flag = verificarAprobAp($dtA['id_apxemp']);
						if($flag==1){
							$horasAp = $horasAp + $dtA['hours_ap'];
						}	
					}
				}	

				//Busca si hay payroll para el empleado
				$sqlText = "select payroll_id, payroll_date, payroll_htotal, payroll_daytime, round((time_to_sec(payroll_nigth)/3600),2) payroll_nigth, ".
				    "sec_to_time((time_to_sec(payroll_htotal)) + (ifnull(".$horasAp.",0)*3600) ) total_hours ".
					"from payroll where employee_id=".$dtE['employee_id']." and payroll_date='".$fecha."'";
				$dtP = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$htotal = $dtP['0']['total_hours'];
					$htotalFormat = date('H:i:s', strtotime($htotal));
					$tblPay .= '<tr><td>'.$n.'</td><td>'.$dtE['username'].'</td>
					<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>
					<td>'.$dtP['0']['payroll_daytime'].'</td>';
					$tblPay .='<td><select id="selHora" name="selHora[]">';
					for($i=0.00; $i<=4.00; $i=$i+0.50){
					    $sel = "";
						if($i==$dtP['0']['payroll_nigth']){$sel="selected";}
							$tblPay .= '<option value="'.$i.'" '.$sel.'>'.$i.'</option>';
					}
					$tblPay .='</select></td><td>'.$horasAp.'</td><td>'.$htotal.'</td></tr>';

				}	
				else{
					$tblPay .='<tr><td>'.$n.'</td><td>'.$dtE['username'].'</td>
					<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>0</td>';
					$tblPay .='<td><select id="selHora" name="selHora[]" disabled="disabled"><option value=0>0</option></select></td><td>'.$horasAp.'</td></tr>';
				}
				$n = $n+1;
			}
			$tblPay .='<tr><td align="center" colspan="7"><input type="button" class="btn" value="Save" onClick="savePayroll()"></td></tr>';
			$tblPay .='<tr><td colspan="5"><input type="hidden" value="'.$_POST['fecha'].'" id="fecha"></td></tr>';
		}
		else{
			$tblPay .='<tr><td colspan="7">There is no record of hours for this day</td></tr>';
		}
		$tblPay .= '</table>';
		echo $tblPay;
	break;	 

	case 'savePayroll':
		$horas = $_POST['hora'];
		$hora = explode(" ",$horas);
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$sqlText = "select employee_id, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$n = 0;		
		foreach($dtEmp as $dtE){
			$totalHoras = 0;
			$horasDia = 0;
			$sqlText = "select payroll_id, date_format(payroll_date,'d/m/Y') as f1, payroll_htotal, ".
			" payroll_daytime, payroll_nigth ".
			" from payroll where employee_id=".$dtE['employee_id']." and payroll_date='".$fecha."'";
			$dtP = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$totalHoras = $dtP['0']['payroll_htotal'];
				$horasDia = $dtP['0']['payroll_htotal'];
			}
			
			$sqlText = "SELECT exceptionemp_id, SEC_TO_TIME((TIME_TO_SEC(exceptionemp_hfin) -  TIME_TO_SEC(exceptionemp_hini))) as diffexc from exceptionxemp where employee_id=".$dtE['employee_id']." and exceptionemp_date='".$fecha."'";
			$dtEx = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$totalHoras = $totalHoras + $dtEx['0']['diffexc'];
			}
			
			if($totalHoras <> "00:00:00"){
				$noche = $hora[$n];
				if ($noche > 0) {
					$sqlText = "select sec_to_time(".$noche." * 3600) as horas_noche from dual";
					$dtN = $dbEx->selSql($sqlText);
					$horasNoche = $dtN['0']['horas_noche'];

					if (restarHoras($totalHoras,$horasNoche) <> "00:00:00"){
						$dia = restarHoras($horasDia,$horasNoche);
						$sqlText = "update payroll set payroll_daytime='".$dia."', payroll_nigth='".$horasNoche."' where payroll_id=".$dtP['0']['payroll_id'];
						$dbEx->updSql($sqlText);
					}
					else{
					    //actualiza el registro poniendo en las horas nocturnas todas las diurnas, teniendo en cuenta que solo se actualiza en multiplos de 30 minutos
						$sqlText = "update payroll set ".
									"payroll_nigth = sec_to_time((time_to_sec(payroll_htotal)) - mod((time_to_sec(payroll_htotal)),1800)), ".
									"payroll_daytime= sec_to_time(mod((time_to_sec(payroll_htotal)),1800)) ".
									"where payroll_id=".$dtP['0']['payroll_id'];

						$dbEx->updSql($sqlText);
					}
					
				}
			}
			else if($totalHoras == "00:00:00" and $hora[$n] > 0){
				$sqlText = "insert into payroll set employee_id=".$dtE['employee_id'].", payroll_date='".$fecha."', payroll_htotal=sec_to_time(".$hora[$n]."*3600), ".
				" payroll_daytime='00:00:00', payroll_nigth=sec_to_time(".$hora[$n]."*3600)";
				$dbEx->insSql($sqlText);	
			}	
			$n = $n+1;	
		}
		$rslt = 2;
		echo $rslt;
	break;
	case 'rptPayRoll':
		$rslt = cargaPag("../payroll/filtPayrollRepxSup.php");
		$sqlText = "select employee_id, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if ($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$optE .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']."&nbsp;".$dtE['lastname'].'</option>';	
			}	
		}
		else{
			$optE .= '<option value="-1"> You do not have employees supervised</option>';
		}
		$rslt = str_replace("<!--optEmp-->",$optE,$rslt);
		echo $rslt;
	break;
	
	case 'load_rptPayrollxSup':
		$filtro = " where e.user_status=1 and e.id_supervisor=".$_SESSION['usr_id'];
		$filtroPay = "";
		$filtroAp = "";
		$filtroExcep = "";
		$jointable = "";
		if($_POST['fecIni']!=""){
			$fec_ini = $oFec->cvDtoY($_POST['fecIni']);
			$fec_fin = $oFec->cvDtoY($_POST['fecFin']);
			$filtroPay .= " and payroll_date between date '".$fec_ini."' and date '".$fec_fin."'";
			$filtroAp .= " and startdate_ap between date '".$fec_ini."' and date '".$fec_fin."'";
			$filtroExcep .= " and exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."'";
			}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			 $filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";
			}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			 $filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";
			}
		if($_POST['emp']>0){
			$filtro .= " and e.employee_id=".$_POST['emp'];	
		}
		$sqlText = "select distinct(e.employee_id), e.username, e.firstname, e.lastname from employees e ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$tblPay = '<table width="900" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
		if($dbEx->numrows>0){
			$tblPay .='<tr><td colspan="11">Matches: '.$dbEx->numrows.'</td>';
			$tblPay .='<td colspan="2" align="rigth"><form target="_blank" action="payroll/xls_rptpayrollSup.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$filtro.'"><input type="hidden" name="filtroPay" value="'.$filtroPay.'"><input type="hidden" name="filtroExcep" value="'.$filtroExcep.'"><input type="hidden" name="filtroAp" value="'.$filtroAp.'"></td></tr>';
			$tblPay .='<tr><td colspan="14" align="center" class="txtForm">REPORT OF PAYROLL TO '.$_POST['fecIni'].' THE '.$_POST['fecFin'].'</td></tr>';
			$tblPay .='<tr class="txtForm"><td width="5%">N&deg;</td><td width="5%">BADGE</td><td width="30%">EMPLOYEE</td>'.
					'<td width="8%">DAYTIME HOURS</td><td width="8%">NOCTURNAL HOURS</td><td width="8%">AP HOURS</td><td width="8%">VACATIONS</td>'.
					'<td width="8%">EXCEPTION HOURS</td><td width="8%">ADDITIONAL HOURS</td><td width="8%">PAID HOLIDAY</td>'.
					'<td width="8%">DAY OVERTIME</td><td width="8%">NIGHT OVERTIME</td><td width="8%">HOLIDAY OVERTIME</td>'.
					'<td width="8%">TOTAL HOURS</td></tr>';
			$n =1;
			foreach($dtEmp as $dtE){
				//Obtiene horas de payroll para el periodo
				$sqlText = "select (sum(time_to_sec(payroll_htotal)))/3600 as stotal, (sum(time_to_sec(payroll_daytime)))/3600 as sday, (sum(time_to_sec(payroll_nigth)))/3600 as snigth ".
					"from payroll where employee_id=".$dtE['employee_id']." ".$filtroPay;
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

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1,7) and hours_ap!='' ".$filtroAp;
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
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
				
				//$horasTotal = $horasTotal + $horasAp + $horasException + $horasPaidHoliday + $horasDayOvertime + $horasNightOvertime + $horasHolidayOvertime;
				
				$tblPay .= '<tr><td>'.$n.'</td><td>'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>'.
					'<td>'.round($horasDia,2).'</td><td>'.round($horasNocturna,2).'</td><td>'.$horasAp.'</td><td>'.$horasVacacion.'</td><td>'.round($horasException,2).'</td>'.
					'<td>'.round($additionalHours,2).'</td><td>'.round($horasPaidHoliday,2).'</td><td>'.round($horasDayOvertime,2).'</td>'.
					'<td>'.round($horasNightOvertime,2).'</td><td>'.round($horasHolidayOvertime,2).'</td><td>'.round($horasTotal,2).'</td></tr>';
				$n = $n+1;
			}
		}
		else{
			$tblPay .='<tr><td colspan="6">No Matches</td></tr>';	
		}
		$tblPay .='</table>';
		echo $tblPay;
	
	break;
	
	case 'reportPayroll':
		$rslt = cargaPag("../payroll/filtPayrollRep.php");

		$sqlText = "select * from account order by name_account";
		$dtC = $dbEx->selSql($sqlText);
		$optC = "";
		foreach($dtC as $dtC){
				$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}
		$sqlText ="select * from depart_exc where status_depart = 1 order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		
		$optD = '<select id="lsDpto" class="txtPag" onchange="getPoscFiltros(this.value)">';
		$optD .= '<option value="0">[ALL]</option>';
			foreach($dtD as $dtD){
				$optD .='<option value="'.$dtD['ID_DEPART'].'">'.$dtD['NAME_DEPART'].'</option>';	
			}
		$optD .='</select>';
		
		$sqlText = "select * from places order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP ='<select id="lsPosc" class="txtPag">[ALL]</option>';
		$optP .= '<option value="0">[ALL]</option>';
		foreach($dtP as $dtP){
			$optP .='<option value="'.$dtP['ID_PLACE'].'">'.$dtP['NAME_PLACE'].'</option>';	
		}
		$optP .='</select>';
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--optDepart-->",$optD,$rslt);
		$rslt = str_replace("<!--optPlaza-->",$optP,$rslt);
		
		$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join places pl on pd.id_place=pl.id_place where nivel_place=2 and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = "";
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname'].'&nbsp;'.$dtS['lastname'].'</option>';	
		}
		$rslt = str_replace("<!--optSuperv-->",$optSup,$rslt);
		
		echo $rslt;
	break;
	
	case 'load_rptPayroll':
		$filtro = " where pe.status_plxemp='A' and e.user_status=1";
		$filtroPay = "";
		$filtroAp = "";
		$filtroExcep = "";
		$jointable = "";
		if($_POST['cuenta']>0){
			$filtro .= " and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depart']>0){
			$filtro .= " and pd.id_depart=".$_POST['depart'];
			}
		if($_POST['posicion']>0){
			$filtro .= " and pd.id_place=".$_POST['posicion'];
			}
		if($_POST['superv']>0){
			$filtro .= " and e.id_supervisor=".$_POST['superv'];
			}
		if($_POST['fecIni']!=""){
			$fec_ini = $oFec->cvDtoY($_POST['fecIni']);
			$fec_fin = $oFec->cvDtoY($_POST['fecFin']);
			$filtroPay .= " and payroll_date between date '".$fec_ini."' and date '".$fec_fin."'";
			$filtroAp .= " and startdate_ap between date '".$fec_ini."' and date '".$fec_fin."'";
			$filtroExcep .= " and exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."'";
			}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			 $filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";
			}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			 $filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";
			}
			
		//Si es gerente de area solo le permite a roles de agente y supervisores
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" and (name_role='AGENTE' or name_role='SUPERVISOR') ";
		}	
			
		$sqlText = "select distinct(e.employee_id), e.username, e.firstname, e.lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep inner join user_roles u on u.id_role=pd.id_role ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$tblPay = '<table width="925" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
		if($dbEx->numrows>0){
			$tblPay .='<tr><td colspan="11">Matches: '.$dbEx->numrows.'</td>';
			$tblPay .='<td colspan="2" align="rigth"><form target="_blank" action="payroll/xls_rptpayroll.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$filtro.'"><input type="hidden" name="filtroPay" value="'.$filtroPay.'"><input type="hidden" name="filtroExcep" value="'.$filtroExcep.'"><input type="hidden" name="filtroAp" value="'.$filtroAp.'"></td></tr>';
			$tblPay .='<tr><td colspan="14" align="center" class="txtForm">REPORT OF PAYROLL TO '.$_POST['fecIni'].' THE '.$_POST['fecFin'].'</td></tr>';
			
			$tblPay .='<tr class="txtForm"><td width="5%">N&deg;</td><td width="5%">BADGE</td><td width="30%">EMPLOYEE</td>'.
				'<td width="8%">DAYTIME HOURS</td><td width="8%">NOCTURNAL HOURS</td><td width="8%">AP HOURS</td><td width="8%">VACATIONS</td>'.
				'<td width="8%">EXCEPTION HOURS</td><td width="8%">ADDITIONAL HOURS</td><td width="8%">PAID HOLIDAY</td>'.
				'<td width="8%">DAY OVERTIME</td><td width="8%">NIGHT OVERTIME</td><td width="8%">HOLIDAY OVERTIME</td>'.
				'<td width="8%">TOTAL HOURS</td></tr>';
			$n =1;
			foreach($dtEmp as $dtE){
				//Obtiene horas de payroll para el periodo
				$sqlText = "select (sum(time_to_sec(payroll_htotal)))/3600 as stotal, (sum(time_to_sec(payroll_daytime)))/3600 as sday, (sum(time_to_sec(payroll_nigth)))/3600 as snigth ".
					"from payroll where employee_id=".$dtE['employee_id']." ".$filtroPay;
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

				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1,7) and hours_ap!='' ".$filtroAp;
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['employee_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
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

				//$horasTotal = $horasTotal + $horasAp + $horasException + $horasPaidHoliday + $horasDayOvertime + $horasNightOvertime + $horasHolidayOvertime;
				
				
				$tblPay .= '<tr><td>'.$n.'</td><td>'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>'.
					'<td>'.round($horasDia,2).'</td><td>'.round($horasNocturna,2).'</td><td>'.round($horasAp,2).'</td><td>'.round($horasVacacion,2).'</td>'.
					'<td>'.round($horasException,2).'</td><td>'.round($additionalHours,2).'</td><td>'.round($horasPaidHoliday,2).'</td>'.
					'<td>'.round($horasDayOvertime,2).'</td><td>'.round($horasNightOvertime,2).'</td><td>'.round($horasHolidayOvertime,2).'</td>'.
					'<td>'.round($horasTotal,2).'</td></tr>';
				$n = $n+1;
			}
		}
		else{
			$tblPay .='<tr><td colspan="6">No Matches</td></tr>';	
		}
		$tblPay .='</table>';
		echo $tblPay;
	break;
	
	case 'newRegHoraAll':
		$rslt = cargaPag("../payroll/newRegHoraAll.php");
		$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join places pl on pd.id_place=pl.id_place where nivel_place=2 and user_status=1 and pe.status_plxemp='A' order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = "";
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname'].'&nbsp;'.$dtS['lastname'].'</option>';	
		}
		$filtro = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" ";
		}
		$sqlText = "select e.employee_id, username, lastname, firstname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id where user_status=1 and status_plxemp='A' ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		foreach($dtEmp as $dtE){
			$optEmp .= '<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
		}
		$rslt = str_replace("<!--optSuperv-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmployee-->",$optEmp,$rslt);
		
		echo $rslt;
	break;
	
	case 'loadPayxEmpAll':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$filtro = "where user_status=1";
		if($_POST['superv']>0){
			$filtro .= " and id_supervisor=".$_POST['superv'];
			}
		if($_POST['employee']>0){
			$filtro .= " and e.employee_id =".$_POST['employee'];
		}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			 $filtro .= " and (firstname like '%".strtoupper($_POST['nombre'])."%' or lastname like '%".strtoupper($_POST['nombre'])."%')";
			}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			 $filtro .= " and (username like '%".strtoupper($_POST['badge'])."%')";
			}
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" ";
		}
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id ".$filtro." and status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$tblPay = '<table width="825" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
		
		if($dbEx->numrows >0){
			$tblPay .='<tr class="txtForm"><td colspan="7" align="center">HOURS RECORDED FOR EACH AGENT, PLEASE ENTER THE TOTAL NOCTURNAL HOOURS</td></tr>';
			$tblPay .='<tr class="txtForm"><td width="5%">N&deg;</td><td width="10%">BADGE</td><td width="35%">EMPLOYEE</td><td width="12%">DAYTIME HOURS</td><td width="12%">NOCTURNAL HOURS</td><td width="12%">AP HOURS</td><td width="12%">TOTAL HOURS</td></tr>';
			$n = 1;
				
			foreach($dtEmp as $dtE){
				//Obtiene horas de las AP en el periodo dado
				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['employee_id']." and id_tpap in(1,7) and hours_ap!='' and startdate_ap='".$fecha."'";
				$dtAp = $dbEx->selSql($sqlText);
				$horasAp = 0;
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$flag = verificarAprobAp($dtA['id_apxemp']);
						if($flag==1){
							$horasAp = $horasAp + $dtA['hours_ap'];
						}	
					}
				}	
					
				//Busca si hay payroll para el empleado
				$sqlText = "select payroll_id, payroll_date, payroll_htotal, payroll_daytime, round((time_to_sec(payroll_nigth)/3600),2) payroll_nigth, ".
				    "sec_to_time((time_to_sec(payroll_htotal)) + (ifnull(".$horasAp.",0)*3600) ) total_hours ".
					"from payroll where employee_id=".$dtE['employee_id']." and payroll_date='".$fecha."'";
				$dtP = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$htotal = $dtP['0']['total_hours'];
					$tblPay .= '<tr><td>'.$n.'</td><td>'.$dtE['username'].'</td>
					<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>
					<td>'.$dtP['0']['payroll_daytime'].'</td>';
					$tblPay .='<td><select id="selHora" name="selHora[]">';
					for($i=0.00; $i<=4.00; $i=$i+0.50){
					    $sel = "";
						if($i==$dtP['0']['payroll_nigth']){$sel="selected";}
							$tblPay .= '<option value="'.$i.'" '.$sel.'>'.$i.'</option>';
					}
					$tblPay .='</select></td><td>'.$horasAp.'</td><td>'.$htotal.'</td></tr>';

				}	
				else{
					$tblPay .='<tr><td>'.$n.'</td><td>'.$dtE['username'].'</td>
					<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>0</td>';
					$tblPay .='<td><select id="selHora" name="selHora[]" disabled="disabled"><option value=0>0</option></select></td><td>'.$horasAp.'</td></tr>';
				}
				$n = $n+1;
			}
			$tblPay .='<tr><td align="center" colspan="7"><input type="button" class="btn" value="Save" onClick="savePayrollAll()"></td></tr>';
			$tblPay .='<tr><td colspan="5"><input type="hidden" value="'.$_POST['fecha'].'" id="fecha"></td></tr>';
			$tblPay .='<tr><td colspan="5"><input type="hidden" value="'.$_POST['superv'].'" id="txtSuperv"></td></tr>';
			$tblPay .='<tr><td colspan="5"><input type="hidden" value="'.$_POST['employee'].'" id="txtEmployee"></td></tr>';
			$tblPay .='<tr><td colspan="5"><input type="hidden" value="'.$_POST['nombre'].'" id="txtNombre"></td></tr>';
			$tblPay .='<tr><td colspan="5"><input type="hidden" value="'.$_POST['badge'].'" id="txtBadge"></td></tr>';
		}
		else{
			$tblPay .='<tr><td colspan="7">There is no record of hours for this day</td></tr>';
		}
		$tblPay .= '</table>';
		echo $tblPay;
		
	break;
	
	case 'savePayrollAll':
		
		$filtro = "where user_status=1";
		if($_POST['superv']>0){
			$filtro .= " and id_supervisor=".$_POST['superv'];
			}
		if($_POST['employee']>0){
			$filtro .= " and employee_id =".$_POST['employee'];
		}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			 $filtro .= " and (firstname like '%".strtoupper($_POST['nombre'])."%' or lastname like '%".strtoupper($_POST['nombre'])."%')";
			}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			 $filtro .= " and (username like '%".strtoupper($_POST['badge'])."%')";
			}

		$horas = $_POST['hora'];
		$hora = explode(" ",$horas);
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$sqlText = "select employee_id, username, firstname, lastname from employees ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$n = 0;		
		foreach($dtEmp as $dtE){
			$sqlText = "select payroll_id, date_format(payroll_date,'d/m/Y') as f1, payroll_htotal, payroll_daytime, payroll_nigth from payroll where employee_id=".$dtE['employee_id']." and payroll_date='".$fecha."'";
			$dtP = $dbEx->selSql($sqlText);
   			if($dbEx->numrows>0){
				$totalHoras = $dtP['0']['payroll_htotal'];
				$horasDia = $dtP['0']['payroll_htotal'];
			}

			$sqlText = "SELECT exceptionemp_id, SEC_TO_TIME((TIME_TO_SEC(exceptionemp_hfin) -  TIME_TO_SEC(exceptionemp_hini))) as diffexc from exceptionxemp where employee_id=".$dtE['employee_id']." and exceptionemp_date='".$fecha."'";
			$dtEx = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$totalHoras = $totalHoras + $dtEx['0']['diffexc'];
			}

			if($totalHoras <> "00:00:00"){
				$noche = $hora[$n];
				if ($noche > 0) {
					$sqlText = "select sec_to_time(".$noche." * 3600) as horas_noche from dual";
					$dtN = $dbEx->selSql($sqlText);
					$horasNoche = $dtN['0']['horas_noche'];

					if (restarHoras($totalHoras,$horasNoche) <> "00:00:00"){
						$dia = restarHoras($horasDia,$horasNoche);
						$sqlText = "update payroll set payroll_daytime='".$dia."', payroll_nigth='".$horasNoche."' where payroll_id=".$dtP['0']['payroll_id'];
						$dbEx->updSql($sqlText);
					}
					else{
					    $sqlText = "update payroll set ".
									"payroll_nigth = sec_to_time((time_to_sec(payroll_htotal)) - mod((time_to_sec(payroll_htotal)),1800)), ".
									"payroll_daytime= sec_to_time(mod((time_to_sec(payroll_htotal)),1800)) ".
									"where payroll_id=".$dtP['0']['payroll_id'];
						$dbEx->updSql($sqlText);
					}

				}
			}
			else if($totalHoras == "00:00:00" and $hora[$n] > 0){
				$sqlText = "insert into payroll set employee_id=".$dtE['employee_id'].", payroll_date='".$fecha."', payroll_htotal=sec_to_time(".$hora[$n]."*3600), ".
				" payroll_daytime='00:00:00', payroll_nigth=sec_to_time(".$hora[$n]."*3600)";
				$dbEx->insSql($sqlText);
			}
			$n = $n+1;	
		}
		$rslt = 2;
		echo $rslt;

	break;
	
	case 'deletePayroll':
		$rslt = cargaPag("../payroll/deletePayroll.php");
		echo $rslt;
	break;
	
	case 'resetPayroll':
		$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		$sqlText = "update payroll set payroll_htotal='00:00:00', payroll_daytime='00:00:00', payroll_nigth='00:00:00' ".
		"where payroll_date between date '".$fec_ini."' and '".$fec_fin."' ";
		$dbEx->updSql($sqlText);
		echo "2";
	break;
	
	case 'cleanPayrollBatch':
	    $rslt = cargaPag("../payroll/cleanPayrollBatch.php");
		echo $rslt;
	break;
	
	case 'cleanDbPayrollBatch':
        $fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		$rslt = 1;
		//Actualizar los registros del batch
		$sqlText = "delete from payroll_batch ".
				"where payroll_request_id in ".
				"(select payroll_request_id from payroll_request where request_date between date '".$fec_ini."' and '".$fec_fin."')";
			
		$dbEx->updSql($sqlText);
		
		$rows = $dbEx->affectedRows;

		if ( $rows > 0) {
		    //si los batches pudieron ser actializados, se actualiza el request
			$sqlText = "delete from payroll_request ".
				"where request_date between date '".$fec_ini."' and '".$fec_fin."'";

            $dbEx->updSql($sqlText);
            $rows = $dbEx->affectedRows;
            
            if ($rows > 0) {
				$rslt = 2;
   			}
		}
		// Resultado 1 significa que no habian datos que actualizar
		// Resultado 2 actualizo los datos
		echo $rslt;
	break;
	
	case 'uploadNightHours':
		$rslt = cargaPag("../payroll/formUploadNightHours.php");
		echo $rslt;
	break;
}
?>
