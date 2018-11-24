<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=reportScorecard.xls"); 
  
 	$sqlText = "select e.employee_id, firstname, lastname, username from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep where pe.status_plxemp='A' ".$_POST['filtro']." and (pd.id_role=2 or pd.id_role=3) order by firstname";
	$dtEmp = $dbEx->selSql($sqlText);
	
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
	
	
?>
<table border="1" bordercolor="#003366">
<tr><td colspan="6" bgcolor="#003366" align="center"><font color="#FFFFFF">Hours Completion Report in the period: <?php echo $_POST['fecIni']." - ".$_POST['fecFin']; ?></font></td></tr>
<tr><td bgcolor="#003366"><font color="#FFFFFF">Badge</font></td>
<td bgcolor="#003366"><font color="#FFFFFF">Employee</font></td>
<td bgcolor="#003366"><font color="#FFFFFF">Scheduled hours</font></td>
<td bgcolor="#003366"><font color="#FFFFFF">Hours logged</font></td>
<td bgcolor="#003366"><font color="#FFFFFF">Hours completed</font></td>
<td bgcolor="#003366"><font color="#FFFFFF">Percent hours completion</font></td>
</tr>
<?php
foreach($dtEmp as $dtE){
	//Recupera las horas del schedule
	$horasProgramadas = 0;
	$sqlText = "select sum(sch_proghrs) as sumHorario from schedules where employee_id=".$dtE['employee_id']." and sch_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."'";
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
	
	?>	
	<tr><td><?php echo $dtE['username']; ?></td>
	<td><?php echo $dtE['firstname'].' '.$dtE['lastname'];?></td>
	<td><?php echo $horasProgramadasFormato; ?></td>
	<td><?php echo $horasTrab; ?></td>
    <td><?php echo $font.' '.$horasTarde; ?></font></td>
	<td><?php echo number_format($completado,2); ?>%</td></tr>
	<?php		
     }//Termina foreach
	?>