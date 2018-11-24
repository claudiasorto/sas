<?php
//Funciones para AHT	
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
  		if (strlen($entero) < $largo){
			$valor = $largo - strlen($entero);
        	$relleno = str_repeat('0', $valor);
    	}
    	return $relleno . $entero;
	}
	

	function sumarHoras($h1,$h2)
	{
		$h2h = date('H', strtotime($h2));
		$h2m = date('i', strtotime($h2));
		$h2s = date('s', strtotime($h2));
		$hora2 =$h2h." hour ". $h2m ." min ".$h2s ." second";

		$horas_sumadas= $h1." + ". $hora2;
		$text=date('H:i:s', strtotime($horas_sumadas)) ;
		return $text;

	}
	
	function restarHoras ($hora1,$hora2){ 
     
    $temp1 = explode(":",$hora1); 
    $temp_h1 = (int)$temp1[0]; 
    $temp_m1 = (int)$temp1[1]; 
    $temp_s1 = (int)$temp1[2]; 
    $temp2 = explode(":",$hora2); 
    $temp_h2 = (int)$temp2[0]; 
    $temp_m2 = (int)$temp2[1]; 
    $temp_s2 = (int)$temp2[2]; 
     
    // si $hora2 es mayor que la $hora1, invierto 
    if( $temp_h1 < $temp_h2 ){ 
        $temp  = $hora1; 
        $hora1 = $hora2; 
        $hora2 = $temp; 
    } 
    /* si $hora2 es igual $hora1 y los minutos de 
       $hora2 son mayor que los de $hora1, invierto*/ 
    elseif( $temp_h1 == $temp_h2 && $temp_m1 < $temp_m2){ 
        $temp  = $hora1; 
        $hora1 = $hora2; 
        $hora2 = $temp; 
    } 
    /* horas y minutos iguales, si los segundos de  
       $hora2 son mayores que los de $hora1,invierto*/ 
    elseif( $temp_h1 == $temp_h2 && $temp_m1 == $temp_m2 && $temp_s1 < $temp_s2){ 
        $temp  = $hora1; 
        $hora1 = $hora2; 
        $hora2 = $temp; 
    }     
     
    $hora1=explode(":",$hora1); 
    $hora2=explode(":",$hora2); 
    $temp_horas = 0; 
    $temp_minutos = 0;         
     
    //resto segundos 
    $segundos; 
    if( (int)$hora1[2] < (int)$hora2[2] ){ 
        $temp_minutos = -1;         
        $segundos = ( (int)$hora1[2] + 60 ) - (int)$hora2[2]; 
    } 
    else     
        $segundos = (int)$hora1[2] - (int)$hora2[2]; 
         
    //resto minutos 
    $minutos; 
    if( (int)$hora1[1] < (int)$hora2[1] ){ 
        $temp_horas = -1;         
        $minutos = ( (int)$hora1[1] + 60 ) - (int)$hora2[1] + $temp_minutos; 
    }     
    else 
        $minutos =  (int)$hora1[1] - (int)$hora2[1] + $temp_minutos; 
         
    //resto horas     
    $horas = (int)$hora1[0]  - (int)$hora2[0] + $temp_horas; 
         
    if($horas<10) 
        $horas= '0'.$horas; 
     
    if($minutos<10) 
        $minutos= '0'.$minutos; 
     
    if($segundos<10) 
        $segundos= '0'.$segundos; 
         
    $rst_hrs = $horas.':'.$minutos.':'.$segundos;     

    return ($rst_hrs);     
     
    }
	function comparaHoras($hora1,$hora2){
		$temp1 = explode(":",$hora1); 
    	$temp_h1 = (int)$temp1[0]; 
   	 	$temp_m1 = (int)$temp1[1]; 
    	$temp_s1 = (int)$temp1[2]; 
   	 	$temp2 = explode(":",$hora2); 
    	$temp_h2 = (int)$temp2[0]; 
    	$temp_m2 = (int)$temp2[1]; 
    	$temp_s2 = (int)$temp2[2]; 
		$result = 1;
     
    	// si $hora2 es mayor que la $hora1, invierto 
   		if( $temp_h1 < $temp_h2 ){ 
        	$temp  = $hora1; 
        	$hora1 = $hora2; 
        	$hora2 = $temp; 
    	} 
    	/* si $hora2 es igual $hora1 y los minutos de 
       $hora2 son mayor que los de $hora1, invierto*/ 
    	elseif( $temp_h1 == $temp_h2 && $temp_m1 < $temp_m2){ 
        	$temp  = $hora1; 
        	$hora1 = $hora2; 
        	$hora2 = $temp; 
    	} 
    	/* horas y minutos iguales, si los segundos de  
       $hora2 son mayores que los de $hora1,invierto*/ 
    	elseif( $temp_h1 == $temp_h2 && $temp_m1 == $temp_m2 && $temp_s1 < $temp_s2){ 
       	 	$result = 2;
   		} 
		return $result;
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
	case 'uploadPhoneMetrics':
		$rslt = cargaPag("../mttoPhoneMetric/formUpMetrics.php");
		
		echo $rslt;
	break;
	
	case 'latenessReport':
		$rslt = cargaPag("../mttoPhoneMetric/filtrosLateness.php");
		
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

	case 'loadLateness':
		$filtro = " ";
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
		if(strlen($_POST['fechaIni'])>0){
			$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
			//$filtro .=" and lateness_date between date '".$fechaIni."' and '".$fechaFin."' ";
		}
		if($_POST['emp']>0){
			$filtro .=" and l.employee_id=".$_POST['emp'];
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (e.firstname like '%".$_POST['nombre']."%' or e.lastname like '%".$_POST['nombre']."%' or concat(firstname,' ',lastname) like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and username like '%".$_POST['badge']."%'";
		}
		
		
		$sqlText = "select distinct(e.employee_id) as emp_id, firstname, lastname, username from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep where pe.status_plxemp='A' ".$filtro." and (pd.id_role=2 or pd.id_role=3) order by firstname";
		
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="backTablaMain"  bordercolor="#069" align="center"  width="800">'; 
		
		if($dbEx->numrows>0){
			$rslt .='<tr><td colspan="4">Matches: '.$dbEx->numrows.'</td>
			<td><form target="_blank" action="mttoPhoneMetric/xls_reportLateness.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="fechaIni" value="'.$fechaIni.'">
			<input type="hidden" name="fechaFin" value="'.$fechaFin.'"></form></td></tr>';
			
			
			$rslt .='<tr class="backTablaForm"><td width="15%">BADGE</td><td width="40%">EMPLOYEE</td><td width="15%">SCHEDULES HOURS</td><td width="15%">HOURS COMPLETED</td><td width="15%">LATE TIME</td></tr>';
			foreach($dtEmp as $dtE){
				$horasProgramadas = 0;
				$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['emp_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$horasProgramadas = $dtSch['0']['sumHorario'];
				}
				
				$horasPayroll = 0;
				$sqlText = "select sum(payroll_htotal) as sumPayroll from payroll where payroll_date between date '".$fechaIni."' and '".$fechaFin."' and employee_id=".$dtE['emp_id'];
				$dtPayroll = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$horasPayroll = $dtPayroll['0']['sumPayroll'];	
				}
				
				$horasAp = 0;
				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['emp_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap between date '".$fechaIni."' and '".$fechaFin."' and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$horasAp = $horasAp + $dtA['hours_ap'];	
					}
				}
				
				$horasException = 0;
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['emp_id']." and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
				$dtEx = $dbEx->selSql($sqlText);
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
				
				$horasTrabajadas = $horasPayroll + $horasAp + $horasException;
				//Desglosar horas trabajadas para obtener formato HH:mm:ss
				$formatHorasTrabRedondeado = explode(".",number_format($horasTrabajadas,2));
				$hora = $formatHorasTrabRedondeado[0];
				$min = explode(".",($formatHorasTrabRedondeado[1]*60)/100);
				
				$segundos = ($min[1]/10) * 60;
				$horasTrab = zerofill($hora,2).":".zerofill($min[0],2).":".zerofill($segundos,2);
				
				$horasTarde = restarHoras($horasTrab,$horasProgramadas);
				if(comparaHoras($horasTrab,$horasProgramadas)==1){
					$font = '<font color="#003333">';
				}
				else if(comparaHoras($horasProgramadas,$horasTrab)==2 or ($horasProgramadas=='00:00:00')){
					$font = '<font color="#6D0114">';	
				}
				
				//onclick="detalleLateness('.$dtE['emp_id'].')" title="click for details"><td align="center"
				$rslt .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td><td align="center">'.$horasProgramadas.'</td><td align="center">'.$horasTrab.'</td><td align="center"><b>'.$font.' '.$horasTarde.'</b></font></td></tr>';
			}
		}
		else{
			$rslt .='<tr><td>No Matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
		
	break;
	
	case 'detalleLateness':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		$sqlText = "select firstname, lastname from employees where employee_id=".$_POST['idE'];
		$dtEmp = $dbEx->selSql($sqlText);
		
		$sqlText = "select date_format(lateness_date,'%d/%m/%Y') as fecha, lateness_totaltime from lateness l where l.employee_id=".$_POST['idE']." and lateness_date between date '".$fechaIni."' and '".$fechaFin."' ";
		$dtTarde = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="backTablaMain"  bordercolor="#069" align="center"  width="500">';
		
		if($dbEx->numrows>0){
			$rslt .='<tr bgcolor="#003366"><td colspan="2" align="center">
			<font color="#FFFFFF"><b>Tardiness in the period '.$_POST['fechaIni'].' - '.$_POST['fechaFin'].'</b></font></td></tr>';
			$rslt .='<tr bgcolor="#FFFFFF"><td colspan="2" align="center"><b>Agent: '.$dtEmp['0']['firstname'].' '.$dtEmp['0']['lastname'].'</b></td></tr>';
			$rslt .='<tr><td align="center">Date</td><td align="center">Total hours</td></tr>';
			foreach($dtTarde as $dtT){
				$rslt .='<tr><td align="center">'.$dtT['fecha'].'</td><td align="center">'.$dtT['lateness_totaltime'].'</td></tr>';
			}
		}
		echo $rslt;
	break;
	
	case 'filtrosReportAHT':
		$rslt = cargaPag("../mttoAHT/filtrosReportAHT.php");
		$sqlText = "select * from account where id_typeacc=2 and account_status='A' order by name_account";
		$dtCuenta = $dbEx->selSql($sqlText);
		$optC = '<option value="0">[ALL]</option>';
		foreach($dtCuenta as $dtC){
			$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where name_role='SUPERVISOR' and e.user_status=1 and pe.status_plxemp='A'";
		
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = '<option value="0">[ALL]</option>';
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = '<option value="0">[ALL]</option>';
		foreach($dtEmp as $dtE){
			$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
		}
		
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		echo $rslt;
	break;
	
	case 'loadAveragesCall':
		$filtro = " ";
		$filtroCall =" ";
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];	
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and e.employee_id=".$_POST['emp'];
		}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			$filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";
		}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			$filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";	
		}
		if(strlen($_POST['fecIni'])>0 and strlen($_POST['fecFin']>0)){
			$fechaIni = $oFec->cvDtoY($_POST['fecIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fecFin']);
			$filtroCall .=" and metric_date between date '".$fechaIni."' and '".$fechaFin."'";	
		}
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A'".$filtro." order by firstname";
		
		$dtEmp = $dbEx->selSql($sqlText);
		$tblResult = '<table class="tblRepQA" width="80%" align="center" cellpadding="2" cellspacing="2">';
		if($dbEx->numrows>0){
			$tblResult .='<tr><td colspan="5" align="center"><b>Times average calls per agent in the period: '.$_POST['fecIni'].'-'.$_POST['fecFin'].'<b></td></tr>';
			$tblResult .='<tr class="showItem"><td width="10%">BADGE</td><td width="45%">AGENT</td><td align="center">TOTAL CALLS</td><td align="center">TOTAL TIME</td><td align="center">AVERAGE TIME</td></tr>';
			foreach($dtEmp as $dtE){
				$sumaTime = '00:00:00';
				$tiempoDecimal = 0;
				$sumaCall = 0;
				$promLlamada = 0;
				$horaPromLlamada = '00:00:00';
				
				//Suma el tiempo total en llamadas

				$sqlText = "select sec_to_time(sum(time_to_sec(metric_aht_totaltime))) as tiempo from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroCall;
				
				$dtTime = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtTime['0']['tiempo']!=NULL){
					$sumaTime = $dtTime['0']['tiempo'];
				}
				$tiempoDecimal = hoursToSecods($sumaTime);
				 
	
				//Total de llamadas 			
				$sqlText = "select sum(metric_totalcalls) as totalcalls from phone_metrics where employee_id=".$dtE['employee_id']."".$filtroCall;
				$sCall = $dbEx->selSql($sqlText);
				if($sCall['0']['totalcalls']!=NULL){
					$sumaCall = $sCall['0']['totalcalls'];	
				}
				if($sumaCall!='' and $sumaCall>0){
					$promLlamada = $tiempoDecimal / $sumaCall;
					$horaPromLlamada = gmdate("H:i:s",$promLlamada);
				}
			
				$tblResult .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td><td align="center">'.$sumaCall.'</td><td align="center">'.$sumaTime.'</td><td align="center">'.$horaPromLlamada.'</td></tr>'; 
			}
		}
		else{
			$tblResult .='<tr><td colspan="4">No matches</td></tr>';	
		}
		$tblResult .='</table>';
		echo $tblResult;
	break;
	
	case 'filtrosPhoneMetrics':
		$rslt = cargaPag("../mttoReportScorecard/filtrosConsolidado.php");
		$sqlText = "select * from account where id_typeacc=2 and account_status='A' order by name_account";
		$dtCuenta = $dbEx->selSql($sqlText);
		$optC = '<option value="0">[ALL]</option>';
		foreach($dtCuenta as $dtC){
			$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where name_role='SUPERVISOR' and e.user_status=1 and pe.status_plxemp='A'";
		
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = '<option value="0">[ALL]</option>';
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = '<option value="0">[ALL]</option>';
		foreach($dtEmp as $dtE){
			$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
		}
		
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		
		echo $rslt;
	break;

	//Genera consolidado de los datos de AHT, Refused Call, Eficiency, Quality score
	case 'loadPhoneMetrics':
		$filtro = " ";
		$filtroMetric = "";
		
		$filtroCS = "";
		$filtroSales = "";
		$filtroNS = "";
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];	
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and e.employee_id=".$_POST['emp'];
		}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			$filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";
		}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			$filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";	
		}
		if(strlen($_POST['fecIni'])>0 and strlen($_POST['fecFin']>0)){
			$fechaIni = $oFec->cvDtoY($_POST['fecIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fecFin']);
			
			$filtroMetric .=" and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
			
			$filtroCS .=" where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitcsemp_maker='Q'";
			$filtroSales .=" where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' and monitsales_maker='Q'";
			$filtroNS .=" where monitnsemp_date between date '".$fechaIni."' and '".$fechaFin."' and monitnsemp_maker='Q'";

		}
		
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A'".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="tblRepQA" width="70%" align="center" cellpadding="2" cellspacing="2">';
		if($dbEx->numrows>0){
			$rslt .='<tr><td colspan="7" align="right"><form target="_blank" action="mttoPhoneMetric/xls_reportScorecard.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="filtroMetric" value="'.$filtroMetric.'">
			<input type="hidden" name="filtroCS" value="'.$filtroCS.'">
			<input type="hidden" name="filtroSales" value="'.$filtroSales.'">
			<input type="hidden" name="filtroNS" value="'.$filtroNS.'">
			<input type="hidden" name="fechaIni" value="'.$fechaIni.'">
			<input type="hidden" name="fechaFin" value="'.$fechaFin.'">
			</td></tr>';
			$rslt .= '<tr class="showItem">
			<td>Badge</td>
			<td>Agent</td>
			<td>Total Calls</td>
			<td>AHT</td>
			<td>Refused calls</td>
			<td>Efficiency</td>
			<td>Quality score</td>
			<td># Q.A</td>
			<td>Hours Completion</td></tr>';
			foreach($dtEmp as $dtE){
				
				//Obtiene el promedio de tiempo en llamadas
				$sumaTime = '00:00:00';
				$tiempoDecimal = 0;
				$sumaCall = 0;
				$promLlamada = 0;
				$horaPromLlamada = '00:00:00';
				
				//Suma el tiempo total en llamadas

				$sqlText = "select sec_to_time(sum(time_to_sec(metric_aht_totaltime))) as tiempo from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				
				$dtTime = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtTime['0']['tiempo']!=NULL){
					$sumaTime = $dtTime['0']['tiempo'];
				}
				$tiempoDecimal = hoursToSecods($sumaTime);
							
				$sqlText = "select sum(metric_totalcalls) as totalcalls from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				$sCall = $dbEx->selSql($sqlText);
				if($sCall['0']['totalcalls']!=NULL){
					$sumaCall = $sCall['0']['totalcalls'];	
				}
				if($sumaCall!='' and $sumaCall>0){
					$promLlamada = $tiempoDecimal / $sumaCall;
					$horaPromLlamada = gmdate("H:i:s",$promLlamada);
				}

				
				//Obtiene el promedio de refused call
				
				$sqlText = "select sum(metric_refused) as sumRefused from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
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
				$sqlText = "select sum(metric_efficiency) as sumEfficiency from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
				$dtEficiencia = $dbEx->selSql($sqlText);
				
				$sqlText = "select count(1) as countEfficiency from phone_metrics where employee_id=".$dtE['employee_id']." ".$filtroMetric;
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
				

				$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp ".$filtroCS." and employee_id=".$dtE['employee_id'];
				$dtSumCS = $dbEx->selSql($sqlText);
				if($dtSumCS['0']['sumCS']!=NULL){
					$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
				}
				$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp ".$filtroCS." and employee_id=".$dtE['employee_id'];
				$dtCountCS = $dbEx->selSql($sqlText);
				if($dtCountCS['0']['countCS']!=NULL){
					$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
				}
				
				$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp ".$filtroSales." and employee_id=".$dtE['employee_id'];
				$dtSumSales = $dbEx->selSql($sqlText);
				if($dtSumSales['0']['sumSales']!=NULL){
					$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					
				}
				
				$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp ".$filtroSales." and employee_id=".$dtE['employee_id'];
				$dtCountSales = $dbEx->selSql($sqlText);
				if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
					$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					
				}
				
				$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp ".$filtroNS." and employee_id=".$dtE['employee_id'];
				$dtSumNS = $dbEx->selSql($sqlText);
				if($dtSumNS['0']['sumNS']!=NULL){
					$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
				}
				
				$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp ".$filtroNS." and employee_id=".$dtE['employee_id'];
				$dtCountNS = $dbEx->selSql($sqlText);
				if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
					$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
				}
				if($cantidadEva > 0){
					$promEva = $sumaEva/$cantidadEva;
				}
				
				//Lateness
				
				//Recupera las horas del schedule
				$horasProgramadas = 0;
				$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$horasProgramadas = $dtSch['0']['sumHorario'];
				}
				
				
				//Recupera horas del payroll mas exception y mas AP
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
					$completado = ($horasPayroll/$horasProgramadas)*100;
				}
				
				
				$rslt .='<tr class="rowCons"><td>'.$dtE['username'].'</td>
				<td>'.$dtE['firstname']." ".$dtE['lastname'].'</td>
				<td align="center">'.$sumaCall.'</td>
				<td align="center">'.$horaPromLlamada.'</td>
				<td align="center">'.number_format($promRefused,2).'%</td>
				<td align="center">'.number_format($promEficiencia,2).'%</td>
				<td align="center">'.number_format($promEva,2).'%</td>
				<td align="center">'.$cantidadEva.'</td>
				<td align="center">'.number_format($completado,2).'%</td></tr>';
				
				
			}//Termina de evaluar por empleado
		}
		else{
			$rslt .='<tr><td colspan="5">No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
		
	break;
	
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
		if($_POST['emp']>0){
			$filtro .=" and e.employee_id=".$_POST['emp'];
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (e.firstname like '%".$_POST['nombre']."%' or e.lastname like '%".$_POST['nombre']."%' or concat(firstname,' ',lastname) like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and username like '%".$_POST['badge']."%'";
		}
		
		$sqlText = "select e.employee_id, firstname, lastname, username from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep where pe.status_plxemp='A' ".$filtro." and (pd.id_role=2 or pd.id_role=3) order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="backTablaMain"  bordercolor="#069" align="center"  width="800">';
		if($dbEx->numrows>0){
			$rslt .='<tr><td colspan="5">Matches: '.$dbEx->numrows.'</td>
			<td><form target="_blank" action="mttoPhoneMetric/xls_reportHoursCompletion.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="fechaIni" value="'.$fechaIni.'">
			<input type="hidden" name="fechaFin" value="'.$fechaFin.'">
			<input type="hidden" name="fecIni" value="'.$_POST['fechaIni'].'">
			<input type="hidden" name="fecFin" value="'.$_POST['fechaFin'].'">
			</form></td>
			</tr>';
			$rslt .='<tr class="backTablaForm">
			<td width="10%">Badge</td>
			<td width="30%">Employee</td>
			<td width="15%">Scheduled hours</td>
			<td width="15%">Hours logged</td>
			<td width="15%">Hours completed</td>
			<td width="15%">Percent hours completion</td></tr>';
			
			foreach($dtEmp as $dtE){
				//Recupera las horas del schedule
				$horasProgramadas = 0;
				$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['employee_id']." and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$horasProgramadas = $dtSch['0']['sumHorario'];
				}
				$HorasProgConvertir = explode(".",number_format($horasProgramadas,2));
				$hora = $HorasProgConvertir[0];
				$min = explode(".",($HorasProgConvertir[1]*60)/100);
				$segundos = ($min[1]/10)*60;
				
				$horasProgramadasFormato = zerofill($hora,2).":".zerofill($min[0],2).":".zerofill($segundos,2);
				
				//Recupera horas del payroll mas exception y mas AP
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
				
				//Horas trabajadas en formato hh:mm:ss
				$formatHorasTrabRedondeado = explode(".",number_format($horasPayroll,2));
				$hora = $formatHorasTrabRedondeado[0];
				$min = explode(".",($formatHorasTrabRedondeado[1]*60)/100);
				
				$segundos = ($min[1]/10) * 60;
				$horasTrab = zerofill($hora,2).":".zerofill($min[0],2).":".zerofill($segundos,2);
				
				//Horas tarde en formato hh:mm:ss
				$horasTarde = restarHoras($horasTrab,$horasProgramadasFormato);
				$font = '<font color="#000000">';
				//Si las horas trabajadas es mayor o igual a las horas programadas poner en verde, sino poner en rojo
				if($horasPayroll>=$horasProgramadas){
					$font = '<font color="#006633">';	
				}
				else if($horasPayroll<$horasProgramadas){
					$font = '<font color="#6D0114">';	
				}

				
				$completado = 0;
				
				if($horasProgramadas>0){
					$completado = ($horasPayroll/$horasProgramadas)*100;
				}
				
			$rslt .='<tr class="rowCons"><td>'.$dtE['username'].'</td>
			<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>
			<td align="center">'.$horasProgramadasFormato.'</td>
			<td align="center">'.$horasTrab.'</td>
			<td align="center"><b>'.$font.' '.$horasTarde.'</b></font></td>
			<td align="center">'.number_format($completado,2).'%</td></tr>';
			}//Termina foreach
		}
		else{
			$rslt .='<tr><td>No Matches</td></tr>';	
		}
		
		echo $rslt;
	break;
}
?>