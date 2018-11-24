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
  
  function restaHoras($horaIni, $horaFin){
	return (date("H:i:s", strtotime("00:00:00") + strtotime($horaFin) - strtotime($horaIni) ));
	}
  
  //Funcion para saber si tiene las AP aprobadas, retorna 1 si la AP ha sido aprobada y 0 si no ha sido aprobada.
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
	case 'mis_ap':
		$sqlText = "select username, firstname, lastname from employees where employee_id=".$_SESSION['usr_id'];
		$dtE = $dbEx->selSql($sqlText);
		$sqlText = "select ap.id_apxemp, tp.id_tpap, tp.name_tpap, date_format(ap.startdate_ap,'%d/%m/%Y') as f1, date_format(ap.storagedate_ap,'%d/%m/%Y') as f2, date_format(ap.enddate_ap,'%d/%m/%Y') as f4, ap.comment_ap, autor_ap, approved_status, approved_emp from apxemp ap inner join type_ap tp on tp.id_tpap=ap.id_tpap where employee_id=".$_SESSION['usr_id']." order by ap.id_apxemp desc";
		$dtAp = $dbEx->selSql($sqlText);
		$rslt .= '<br><table cellpadding="2" cellspacing="2" width="80%" class="tblReport" align="center" bordercolor="#069" border="1">';
		if($dbEx->numrows>0){
			$rslt .= '<tr class="txtPag"><td colspan="7">Personnel Actions
registered for '.$dtE['0']['firstname']."&nbsp;".$dtE['0']['lastname'].'</td></tr>';
			$rslt .= '<tr class="txtPag"><td colspan="7">Total personnel actions:&nbsp;'.$dbEx->numrows.'</td></tr>';
			$rslt .= '<tr class="showItem"><td width="25" align="center">Ap#</td>
			<td align="center">date of the creation</td>
			<td align="center">Effective Date</td>
			<td align="center">Personnel action type</td>
			<td align="center">Comments</td>
			<td align="center">Status</td><td></td></tr>';
			foreach($dtAp as $dtAp){
				$sqlText = "select firstname, lastname from employees where employee_id=".$dtAp['autor_ap'];
				$dtAutor = $dbEx->selSql($sqlText);
				
				$estado = "";
				if($dtAp['approved_status']=='A'){
					$estado = "Approved";	
				}
				else if($dtAp['approved_status']=='R'){
					$estado = "Rejected";
				}
				else if($dtAp['approved_status']=='P'){
					$estado = "In Progress";
				}
				$estadoEmp = "";
				if($dtAp['approved_emp']=='S'){
					$estadoEmp = "Accepted by employee";
				}
				else{
					$estadoEmp = '<input type="button" class="btn" value="I Accept AP" onclick="AceptarAp('.$dtAp['id_apxemp'].')" title="Click to accept AP">';	
				}
				$rslt .= '<tr class="rowCons">
				<td width="5%">'.$dtAp['id_apxemp'].'</td>
				<td width="10%">'.$dtAp['f2'].'</td>
				<td width="10%">'.$dtAp['f1'].'</td>
				<td width="20%">'.$dtAp['name_tpap'].'</td>
				<td width="35%">'.$dtAp['comment_ap'].'</td>
				<td width="10%">'.$estado.'</td>
				<td width="10%">'.$estadoEmp.'</td></tr>';
			}
		}
		else {
			$rslt .= '<tr><td>No personnel actions</td></tr>';	
		}
		$rslt .= '</table><br><br>';
		echo $rslt;
	break;
	
	case 'AceptarAp':
		$sqlText = "update apxemp set approved_emp='S' where id_apxemp=".$_POST['idAp'];
		$dbEx->updSql($sqlText);
		echo "2";
	break;

	case 'payroll':
		$rslt = cargaPag("../mtto/filtrosPayroll.php");
		echo $rslt;
	break;
	
	case 'loadPayroll':
		$filtro = "";
		$filtroPay = "";
		$filtroAp = "";
		$filtroExcep = "";
		if($_POST['fecIni']!=""){
			$fec_ini = $oFec->cvDtoY($_POST['fecIni']);
			$fec_fin = $oFec->cvDtoY($_POST['fecFin']);
			$filtroPay .= " and payroll_date between date '".$fec_ini."' and date '".$fec_fin."'";
			$filtroAp .= " and startdate_ap between date '".$fec_ini."' and date '".$fec_fin."'";
			$filtroExcep .= " and exceptionemp_date between date '".$fec_ini."' and '".$fec_fin."'";
		}
		$sqlText = "select (sum(time_to_sec(payroll_htotal)))/3600 as stotal, (sum(time_to_sec(payroll_daytime)))/3600 as sday, (sum(time_to_sec(payroll_nigth)))/3600 as snigth ".
					"from payroll where employee_id=".$_SESSION['usr_id']." ".$filtroPay;

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

		$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$_SESSION['usr_id']." and id_tpap in(1,7) and hours_ap!='' ".$filtroAp;
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
		$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$_SESSION['usr_id']." and id_tpap in(5) and hours_ap!='' ".$filtroAp;
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$_SESSION['usr_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$_SESSION['usr_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=9 group by ex.employee_id";
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$_SESSION['usr_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=5 group by ex.employee_id";
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$_SESSION['usr_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=6 group by ex.employee_id";
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$_SESSION['usr_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=7 group by ex.employee_id";
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
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$_SESSION['usr_id']." ".$filtroExcep." and exceptionemp_approved='A' and exceptiontp_level=2 and et.exceptiontp_id=8 group by ex.employee_id";
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
		$rslt = '<table cellpadding="3" cellspacing="0" width="80%" class="tblReport" align="center" bordercolor="#069">';
				
		$rslt .='<tr class="txtForm">
		<td width="10%" align="center"><b>Daytime hours</td>
		<td width="10%" align="center"><b>Nocturnal hours</td>
		<td width="10%" align="center"><b>AP hours</td>
		<td width="10%" align="center"><b>Vacation</td>
		<td width="10%" align="center"><b>Exception hours</td>
		<td width="10%" align="center"><b>Additional hours</td>
		<td width="10%" align="center"><b>Paid holiday</td>
		<td width="10%" align="center"><b>Day overtime</td>
		<td width="10%" align="center"><b>Night overtime</td>
		<td width="10%" align="center"><b>Holiday overtime</td>
		<td width="10%" align="center"><b>Total hours</td></tr>';
			
		$rslt .= '<tr><td align="center">'.round($horasDia,2).'</td>
		<td align="center">'.round($horasNocturna,2).'</td>
		<td align="center">'.round($horasAp,2).'</td>
		<td align="center">'.round($horasVacacion,2).'</td>
		<td align="center">'.round($horasException,2).'</td>
		<td align="center">'.round($additionalHours,2).'</td>
		<td align="center">'.round($horasPaidHoliday,2).'</td>
		<td align="center">'.round($horasDayOvertime,2).'</td>
		<td align="center">'.round($horasNightOvertime,2).'</td>
		<td align="center">'.round($horasHolidayOvertime,2).'</td>
		<td align="center">'.round($horasTotal,2).'</td></tr>';	
				
		$rslt .='</table><br><br><br><br><br>';
		
		echo $rslt;
	break;
	
	
	case 'absenteeism':
		$rslt = cargaPag("../mtto/formAbsent.php");
		echo $rslt;
	break;
	
	case 'reportAbsent':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$start = strtotime($fechaIni);
		$end = strtotime($fechaFin);
		
		$rslt = '<table cellpadding="3" cellspacing="0" width="60%" class="tblReport" align="center" bordercolor="#069">';
		$rslt .='<tr bgcolor="#003366"><td align="center"><font color="#FFFFFF"><b>Date</td><td align="center"><font color="#FFFFFF">Status</td></tr>';
		
		for($i = $start; $i <=$end; $i +=86400){
			$estado = "";
			$sqlText = "select * from absenteeism where employee_id=".$_SESSION['usr_id']." and absent_date='".date('Y-m-d',$i)."'";
			$dtAbsent = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				if($dtAbsent['0']['ABSENT_STATUS']=='A'){
					$estado = "UNJUSTIFIED ABSENCE";	
				}
				else if($dtAbsent['0']['ABSENT_STATUS']=='AJ'){
					$estado = "JUSTIFIED ABSENCE";	
				}
				else if($dtAbsent['0']['ABSENT_STATUS']=='O'){
					$estado = "DAY OFF";	
				}
				else if($dtAbsent['0']['ABSENT_STATUS']=='T'){
					$estado = "TARDY";	
				}
				else if($dtAbsent['0']['ABSENT_STATUS']=='P'){
					$estado = "PRESENT";	
				}
			}
			else{
				$estado = "PRESENT";	
			}
			$rslt .='<tr><td align="center">'.date('d/m/Y',$i).'</td><td align="center">'.$estado.'</td></tr>';
		
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'exception':
		$rslt = cargaPag("../mtto/formException.php");
		echo $rslt;
		
	break;
	
	case 'reportException':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		$sqlText = "select e.exceptionemp_id, date_format(exceptionemp_date,'%d/%m/%Y') as f1, exceptionemp_hini, exceptionemp_hfin, exceptionemp_comment, exceptionemp_approved, exceptionemp_authorizer, exceptionemp_creator, exceptiontp_name from exceptionxemp e inner join exceptions_type tp on e.exceptiontp_id=tp.exceptiontp_id where e.employee_id=".$_SESSION['usr_id']." and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."'";
		$dtEx = $dbEx->selSql($sqlText);
		$rslt = '<table width="950" class="tblResult" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
		if($dbEx->numrows>0){
			$rslt .='<tr bgcolor="#FFFFFF"><td><b>Type Exception</td>
			<td><b>Date</td>
			<td><b>Initial and Final Time</td>
			<td><b>Total Time</td>
			<td><b>Observations</td>
			<td><b>Status</td>
			<td><b>Creator</td>
			<td><b>Authorizer</td></tr>';
			foreach($dtEx as $dtE){
				$estado = "";
				if($dtE['exceptionemp_approved']=='A'){
					$estado = '<font color="#009999"><b> Approved</font>';
				}
				else if($dtE['exceptionemp_approved']=='R'){
					$estado = '<font color="#990000"><b> Rejected</font>';
				}
				else if($dtE['exceptionemp_approved']=='P'){
					$estado = '<font color="#FF9900"><b>In progress</font>';	
				}
				//Busca el creador de la exception
				$creador = "";
				if($dtE['exceptionemp_creator']>0){
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['exceptionemp_creator'];
					$dtAutor = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$creador = $dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'];
					}
				}
					
				//Busca el autorizador de la exception
				$autorizador = "";
				if($dtE['exceptionemp_authorizer']>0){
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['exceptionemp_authorizer'];
					$dtAutor = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$autorizador = $dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'];
					}
				}
				$tiempoTotal = restaHoras($dtE['exceptionemp_hini'],$dtE['exceptionemp_hfin']);
				
				$rslt .='<tr><td>'.$dtE['exceptiontp_name'].'</td><td>'.$dtE['f1'].'</td><td>'.$dtE['exceptionemp_hini']." - ".$dtE['exceptionemp_hfin'].'</td><td>'.$tiempoTotal.'</td><td>'.$dtE['exceptionemp_comment'].'</td><td>'.$estado.'</td><td>'.$creador.'</td><td>'.$autorizador.'</td></tr>';
			}
			
		}
		else{
			$rslt .='<tr><td>No Matches</td></tr>';	
		}
		echo $rslt;
	break;
	
	case 'evaluations':
		$rslt = cargaPag("../mtto/formEvaluatios.php");
		echo $rslt;
	break;
	
	case 'reportEvaluations':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$filtro ="";
		if($_POST['maker']==1){
			$filtro .=" and 'QUALITY' = (select name_depart from employees emp inner join plazaxemp pe on pe.employee_id=emp.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where emp.employee_id = qa_agent and status_plxemp='A') ";	
		}
		
		if($_POST['maker']==2){
			$filtro .=" and 'SUPERVISOR' = (select name_role from employees emp inner join plazaxemp pe on pe.employee_id=emp.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where emp.employee_id = qa_agent and status_plxemp='A') ";

		}
		
		$start = strtotime($fechaIni);
		$end = strtotime($fechaFin);
		
		$totalEvas = 0;
		$sumEvas = 0;
		$tbl = '<table width="70%" class="tblResult" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
		$tblEvas = "";
		for($i = $start; $i <=$end; $i +=86400){
			//Busca evas de CS
			$sqlText = "select date_format(monitcsemp_date,'%d/%m/%Y') as f1, monitcsemp_qualification, qa_agent from monitoringcs_emp where employee_id=".$_SESSION['usr_id']." and monitcsemp_date='".date('Y-m-d',$i)."' ".$filtro;
			$dtCs = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtCs as $dtC){
					$totalEvas = $totalEvas + 1;
					$sumEvas = $sumEvas + $dtC['monitcsemp_qualification'];
					
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtC['qa_agent'];
					$dtQaAgent = $dbEx->selSql($sqlText);
					
					$tblEvas .='<tr><td>'.$dtC['f1'].'</td><td>Customer Services</td><td>'.number_format($dtC['monitcsemp_qualification'],2).'%</td><td>'.$dtQaAgent['0']['firstname']." ".$dtQaAgent['0']['lastname'].'</td></tr>';
				}
			}
			
			//Busca Evas de Sales
			$sqlText = "select date_format(monitsales_date,'%d/%m/%Y') as f1, monitsales_qualification, qa_agent from monitoringsales_emp where employee_id=".$_SESSION['usr_id']." and monitsales_date='".date('Y-m-d',$i)."' ".$filtro;
			$dtSales = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtSales as $dtS){
					$totalEvas = $totalEvas + 1;
					$sumEvas = $sumEvas + $dtS['monitsales_qualification'];
					
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtS['qa_agent'];
					$dtQaAgent = $dbEx->selSql($sqlText);
					
					$tblEvas .='<tr><td>'.$dtS['f1'].'</td><td>Sales</td><td>'.number_format($dtS['monitsales_qualification'],2).'%</td><td>'.$dtQaAgent['0']['firstname']." ".$dtQaAgent['0']['lastname'].'</td></tr>';
					
				}
			}
			
			//Busca Evas de New Services
			
			$sqlText = "select date_format(monitnsemp_date,'%d/%m/%Y') as f1, monitnsemp_qualification, qa_agent from monitoringns_emp where employee_id=".$_SESSION['usr_id']." and monitnsemp_date='".date('Y-m-d',$i)."' ".$filtro;
			$dtNS = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtNS as $dtN){
					$totalEvas = $totalEvas + 1;
					$sumEvas = $sumEvas + $dtN['monitnsemp_qualification'];
					
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtN['qa_agent'];
					$dtQaAgent = $dbEx->selSql($sqlText);
					
					$tblEvas .='<tr><td>'.$dtN['f1'].'</td><td>New Services</td><td>'.number_format($dtN['monitnsemp_qualification'],2).'%</td><td>'.$dtQaAgent['0']['firstname']." ".$dtQaAgent['0']['lastname'].'</td></tr>';
					
				}
			}
		}
		$promEvas = "";
		if($totalEvas>0){
			$promEvas = '<tr bgcolor="#FFFFFF"><td colspan="4"><b>Average: '.number_format($sumEvas / $totalEvas,2).'%</td></tr>';
			
		}
		
		if($totalEvas >0){
			$tbl .= '<tr bgcolor="#FFFFFF"><td><b>Date</td><td><b>Monitoring type</td><td><b>Qualification</td><td><b>Evaluator</td>';
		}
		else{
			$tbl .= '<tr><td>No matches</td></tr>';	
		}
		$tbl .= $tblEvas;
		$tbl .= $promEvas;
		$tbl .='</table>';
		echo $tbl;
		
	break;
	
	case 'schedules':
		$rslt = cargaPag("../mtto/formSchedules.php");
		echo $rslt;
	break;
	
	case 'reportSchedules':
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$start = strtotime($fechaIni);
		$end = strtotime($fechaFin);
		
		$rslt ='<div class="scroll">';
		$rslt .='<table width="70%" class="tblResult" bordercolor="#069" align="center" cellpadding="2" cellspacing="2" border="1">';
		//Muestra unicamente los dias
		$rslt .= '<tr bgcolor="#FFFFFF">';
		for($i = $start; $i <=$end; $i +=86400){
			$rslt .='<td><b>'.date('d/m/Y',$i).'</td>';
		}
		$rslt .='</tr>';
		
		for($i = $start; $i<=$end; $i+=86400){
					$sqlText = "select time_format(sch_entry,'%H:%i') as SCH_ENTRY, time_format(sch_break1in,'%H:%i') as SCH_BREAK1IN, ".
						"time_format(sch_break1out,'%H:%i') as SCH_BREAK1OUT, time_format(sch_lunchin,'%H:%i') as SCH_LUNCHIN, time_format(sch_lunchout,'%H:%i') as SCH_LUNCHOUT, ".
						"time_format(sch_break2in,'%H:%i') as SCH_BREAK2IN, time_format(sch_break2out,'%H:%i') as SCH_BREAK2OUT, time_format(sch_departure,'%H:%i') as SCH_DEPARTURE, ".
						"SCH_OFF from schedules where employee_id=".$_SESSION['usr_id']." and sch_date='".date("Y-m-d",$i)."'";
					$dtSch = $dbEx->selSql($sqlText);
					
					if($dbEx->numrows>0){
						if($dtSch['0']['SCH_OFF']=='Y'){
							$hora = "OFF";
						}
						else{
							$hora = '<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">';
							$hora .='<tr><td class="txtPag">Entry </td><td class="txtPag">'.$dtSch['0']['SCH_ENTRY'].'</td></tr>';
							$hora .='<tr><td class="txtPag">Break 1 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'].'</td></tr>';
							$hora .='<tr><td class="txtPag">Lunch </td><td class="txtPag">'.$dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'].'</td></tr>';
							$hora .='<tr><td class="txtPag">Break 2 </td><td class="txtPag">'.$dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'].'</td></tr>';
							$hora .='<tr><td class="txtPag">End of Duty</td><td class="txtPag">'.$dtSch['0']['SCH_DEPARTURE'].'</td></tr></table>';
						}
					}
					else{
						$hora = " - ";
					}
					$rslt .='<td align="center">'.$hora.'</td>';
				}//Termina for
		
		/*
		//Muestra las horas de entrada por dia
		
		$rslt .='<tr><td width="100">Time of entry </td>';
		for($i = $start; $i <=$end; $i +=86400){
			
			$sqlText = "select date_format(sch_entry,'%H:%i') as sch_entry from schedules where employee_id=".$_SESSION['usr_id']." and sch_date='".date('Y-m-d',$i)."'";
			$dtEntry = $dbEx->selSql($sqlText);
			$hEntry = '';
			if($dbEx->numrows>0){
				$hEntry = $dtEntry['0']['sch_entry'];
			}
			$rslt .= '<td>'.$hEntry.'</td>';
		}
		$rslt .='</tr>';
		
		//Muestra las horas del primer break
		
		$rslt .='<tr><td>Break 1 </td>';
		for($i = $start; $i <=$end; $i +=86400){
			$sqlText = "select date_format(sch_break1out,'%H:%i') as break1out, date_format(sch_break1in,'%H:%i') as break1in from schedules where employee_id=".$_SESSION['usr_id']." and sch_date='".date('Y-m-d',$i)."'";
			$dtBreak1 = $dbEx->selSql($sqlText);
			$break1 = '';
			if($dbEx->numrows>0){
				$break1 .=	$dtBreak1['0']['break1out']." - ".$dtBreak1['0']['break1in'];
			}
			$rslt .='<td>'.$break1.'</td>';
		}
		$rslt .='</tr>';
		
		//Muestra las horas de lunch o off si tiene libre
		
		$rslt .='<tr><td>Lunch </td>';
		for($i = $start; $i <=$end; $i +=86400){
			$sqlText = "select date_format(sch_lunchout,'%H:%i') as lunchout, date_format(sch_lunchin,'%H:%i') as lunchin from schedules where employee_id=".$_SESSION['usr_id']." and sch_date='".date('Y-m-d',$i)."'";
			$dtLunch = $dbEx->selSql($sqlText);
			$lunch = '';
			if($dbEx->numrows>0){
				$lunch .= $dtLunch['0']['lunchout']." - ".$dtLunch['0']['lunchin'];
			}
			
			$sqlText = "select sch_off from schedules where employee_id=".$_SESSION['usr_id']." and sch_date='".date('Y-m-d',$i)."' and sch_off='Y'";
			$dtOff = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$lunch .=' OFF ';
			}
			$rslt .='<td>'.$lunch.'</td>'; 
			
		}
		$rslt .='</tr>';
		
		//Muestra las horas de break2
		$rslt .='<tr><td>Break 2 </td>';
		for($i = $start; $i <=$end; $i +=86400){
			$sqlText = "select date_format(sch_break2out,'%H:%i') as break2out, date_format(sch_break2in,'%H:%i') as break2in from schedules where employee_id=".$_SESSION['usr_id']." and sch_date='".date('Y-m-d',$i)."'";
			$dtBreak2 = $dbEx->selSql($sqlText);
			$break2 = '';
			if($dbEx->numrows>0){
				$break2 .= $dtBreak2['0']['break2out']." - ".$dtBreak2['0']['break2in'];
			}
			$rslt .='<td>'.$break2.'</td>';
		}
		$rslt .='</tr>';
		
		//Muestra la hora de salida
		$rslt .='<tr><td>End of duty</td>';
		for($i = $start; $i <=$end; $i +=86400){
			$sqlText = "select date_format(sch_departure,'%H:%i') as salida from schedules where employee_id=".$_SESSION['usr_id']." and sch_date='".date('Y-m-d',$i)."'";	
			$dtSalida = $dbEx->selSql($sqlText);
			$salida = '';
			if($dbEx->numrows>0){
				$salida .= $dtSalida['0']['salida'];	
			}
			$rslt .='<td>'.$salida.'</td>';
			
		}
		*/
		$rslt .='</tr></table></div>';
			
		echo $rslt;
		
	break;
	
	case 'Metrics':
		$rslt = cargaPag("../mtto/filtrosMetrics.php");
		
		echo $rslt;
	break;
	
	case 'loadMetrics':
		$percentHoursCompletion = 0.40;
		$percentQA = 0.40;
		$percentAht = 0.05;
		$percentRefused = 0.05;
		$percentEfficiency = 0.10;
		
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		$filtroMetric = " and metric_date between date '".$fechaIni."' and '".$fechaFin."'";
		
		//Busca a todos los agentes para obtener la posicion del agente
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
				
			}//Termina de guardar vectores de notas
			
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
			
			//Recorrer todos los vectores para encontrar el agente actual
			$rslt = '<table class="backTablaMain" width="800" align="center" cellpadding="2" cellspacing="2">';
			$rslt .='<tr class="backList">
			<td align="center">Global position</td>
			<td align="center">Score</td>
			<td align="center">Hours completion <br>'.($percentHoursCompletion * 100).'%</td>
			<td align="center">Quality <br>'.($percentQA * 100).'%</td>
			<td align="center">AHT <br>'.($percentAht * 100).'%</td>
			<td align="center">Refused <br>'.($percentRefused * 100).'%</td>
			<td align="center">Efficiency <br>'.($percentEfficiency * 100).'%</td></tr>';
			$flag = true;
			for($i=$indice; $i>=0; $i--){
				if($employee[$i] == $_SESSION['usr_id']){
					$flag = false;
					$pos = ($indice - $i);
					$rslt .='<tr class="rowCons">
					<td align="center">'.$pos.'</td>
					<td align="center">'.$notaTotal[$i].'</td>
					<td align="center">'.$notaHoursCompletion[$i].'</td>
					<td align="center">'.$notaQA[$i].'</td>
					<td align="center">'.$notaAht[$i].'</td>
					<td align="center">'.$notaRefused[$i].'</td>
					<td align="center">'.$notaEfficiency[$i].'</td></tr>';
				}
			}
			
			if($flag){
				$rslt = '<table class="backTablaMain" width="800" align="center" cellpadding="2" cellspacing="2"><tr><td>No Matches</td></tr></table>';	
			}
			$rslt .='</table>';
		}//Termina numrows
		else{
			$rslt ='<table class="backTablaMain"><tr><td>No Matches</td></tr></table>';	
		}
		echo $rslt;
		
	break;
	
	case 'hrRequest':
		$rslt = cargaPag("../mtto/filtrosHrRequest.php");
		$sqlText = "select * from type_request where tpreq_status='A'";
		$dtTpReq = $dbEx->selSql($sqlText);
		$optReq = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtTpReq as $dtT){
				$optReq .='<option value="'.$dtT['TPREQ_ID'].'">'.$dtT['TPREQ_NAME'].'</option>';
			}
		}
		$rslt = str_replace("<!--optReq-->",$optReq,$rslt);
		
		$sqlText = "select h.hrreq_id, h.tpreq_id, hrreq_authorizer, hrreq_content, date_format(hrreq_date,'%d/%m/%Y') as fecReq, date_format(hrreq_dayresponse,'%d/%m/%Y') as fecRespuesta, hrreq_response, hrreq_status, tpreq_name from hrrequest h inner join type_request tr on h.tpreq_id=tr.tpreq_id where hrreq_status='O' and employee_id=".$_SESSION['usr_id'];
		$dtReq = $dbEx->selSql($sqlText);
		$tblResult = '<table cellpadding="3" cellspacing="1" width="80%" border="1" class="backTablaMain" align="center" bordercolor="#BFD1DF">';
		$tblResult .='<tr><td colspan="6">Matches: '.$dbEx->numrows.'</td></tr>';
		$tblResult .='<tr class="showItem" >
		<td width="5%">#</td>
		<td width="15%">Category</td>
		<td width="10%">Date</td>
		<td width="20%">Assigned to</td>
		<td width="10%">Date of resolution</td>
		<td width="35%">Reply</td>
		<td width="5%"></td></tr>';
		if($dbEx->numrows>0){
			foreach($dtReq as $dtR){
				$responsable = "";
				if($dtR['hrreq_authorizer']>0){
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtR['hrreq_authorizer'];
					$dtA = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$responsable = $dtA['0']['firstname']." ".$dtA['0']['lastname'];	
					}
				}
				
				$btn = "";
				if($dtR['hrreq_status']=='O'){
					$btn = '<img src="images/edit.png" alt="Edit request" title="Edit request"  width="40" onclick="editRequest('.$dtR['hrreq_id'].')" >';
				}
				
				$tblResult .='<tr class="rowCons">
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['hrreq_id'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['tpreq_name'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['fecReq'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$responsable.'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['fecRespuesta'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['hrreq_response'].'</td>
				<td>'.$btn.'</td>
				</tr>';
			}
		}
		$tblResult .='</table>';
		
		$rslt = str_replace("<!--tblData-->",$tblResult,$rslt);
		echo $rslt;
	break;
	
	case 'newHrRequest':
		$rslt = cargaPag("../mtto/formHrRequest.php");
		$sqlText = "select * from type_request where tpreq_status='A'";
		$dtTpReq = $dbEx->selSql($sqlText);
		
		$optReq = '<option value="0">Select a category</option>';
		if($dbEx->numrows>0){
			foreach($dtTpReq as $dtT){
				$optReq .='<option value="'.$dtT['TPREQ_ID'].'" >'.$dtT['TPREQ_NAME'].'</option>';
			}
		}

		$rslt = str_replace("<!--optReq-->",$optReq,$rslt);
		
		echo $rslt;
	break;
	
	case 'saveRequest':
		$sqlText ="insert into hrrequest set tpreq_id=".$_POST['tpReq'].", employee_id=".$_SESSION['usr_id'].", hrreq_content='".$_POST['descrip']."', hrreq_date=now()";
		$dbEx->insSql($sqlText);
		echo "2";
	break;
	
	case 'getDetallesRequest':
		$rslt = "";
		$sqlText = "select hrreq_content from hrrequest where hrreq_id=".$_POST['idR'];
		$dtDet = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt .= $dtDet['0']['hrreq_content'];
		}
		$tblResult = '<table cellpadding="3" cellspacing="0" width="50%" class="tblReport" align="center" bordercolor="#069">';
		$tblResult .='<tr><td class="showItem"><b>Description</td></tr>';
		$tblResult .='<tr><td>'.$rslt.'</td></tr>';
		$tblResult .='</table>';
		echo $tblResult;
	break;
	
	case 'getRequest':
		$filtro = "";
		if($_POST['status']!='0'){
			$filtro .=" and hrreq_status='".$_POST['status']."'";
		}
		if($_POST['tpReq']>0){
			$filtro .=" and h.tpreq_id=".$_POST['tpReq'];
		}
		
		$sqlText = "select h.hrreq_id, h.tpreq_id, hrreq_authorizer, hrreq_content, date_format(hrreq_date,'%d/%m/%Y') as fecReq, date_format(hrreq_dayresponse,'%d/%m/%Y') as fecRespuesta, hrreq_response, hrreq_status, tpreq_name from hrrequest h inner join type_request tr on h.tpreq_id=tr.tpreq_id where employee_id=".$_SESSION['usr_id']." ".$filtro;
		$dtReq = $dbEx->selSql($sqlText);
		$tblResult = '<table cellpadding="3" cellspacing="1" width="80%" border="1" class="backTablaMain" align="center" bordercolor="#BFD1DF">';
		$tblResult .='<tr><td colspan="7">Matches: '.$dbEx->numrows.'</td></tr>';
		$tblResult .='<tr class="showItem" >
		<td width="5%">#</td>
		<td width="15%">Category</td>
		<td width="10%">Date</td>
		<td width="20%">Assigned to</td>
		<td width="10%">Date of resolution</td>
		<td width="35%">Reply</td>
		<td width="5%"></td></tr>';
		if($dbEx->numrows>0){
			foreach($dtReq as $dtR){
				$responsable = "";
				if($dtR['hrreq_authorizer']>0){
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtR['hrreq_authorizer'];
					$dtA = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$responsable = $dtA['0']['firstname']." ".$dtA['0']['lastname'];	
					}
				}
				
				$btn = "";
				if($dtR['hrreq_status']=='O'){
					$btn = '<img src="images/edit.png" alt="Edit request" title="Edit request"  width="40" onclick="editRequest('.$dtR['hrreq_id'].')" >';
				}
				
				$tblResult .='<tr class="rowCons">
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['hrreq_id'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['tpreq_name'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['fecReq'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$responsable.'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['fecRespuesta'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['hrreq_response'].'</td>
				<td>'.$btn.'</td></tr>';
			}
		}
		$tblResult .='</table>';
		
		echo $tblResult;
	break;
	
	case 'editRequest':
		$rslt = cargaPag("../mtto/formEditRequest.php");
		
		$sqlText = "select * from hrrequest where hrreq_id=".$_POST['idR'];
		$dtReq = $dbEx->selSql($sqlText);
		
		$sqlText = "select * from type_request where tpreq_status='A'";
		$dtCat = $dbEx->selSql($sqlText);
		$optCat = "";
		if($dbEx->numrows>0){
			foreach($dtCat as $dtC){
				$sel = "";
				if($dtC['TPREQ_ID']==$dtReq['0']['TPREQ_ID']){
					$sel = "selected";	
				}
				$optCat .='<option value="'.$dtC['TPREQ_ID'].'" '.$sel.'>'.$dtC['TPREQ_NAME'].'</option>';
			
			}
		}
		
		$rslt = str_replace("<!--IdR-->",$dtReq['0']['HRREQ_ID'],$rslt);
		$rslt = str_replace("<!--content-->",$dtReq['0']['HRREQ_CONTENT'],$rslt);
		$rslt = str_replace("<!--optCat-->",$optCat,$rslt);
		
		echo $rslt;
	break;
	
	case 'saveEditRequest':
		$sqlText = "update hrrequest set tpreq_id=".$_POST['categoria'].", hrreq_content='".$_POST['descrip']."' where hrreq_id=".$_POST['idR'];
		$dbEx->updSql($sqlText);
		echo "2";
		
	break;
	
}
