<?php
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

  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=LatenessReport.xls");
  
  $sqlText = "select distinct(e.employee_id) as emp_id, firstname, lastname, username from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep where pe.status_plxemp='A' ".$_POST['filtro']." and (pd.id_role=2 or pd.id_role=3) order by firstname";
  
  $dtEmp = $dbEx->selSql($sqlText);
  
  ?>
  <table border="1" bordercolor="#003366">
  <tr bgcolor="#003366"><td><font color="#FFFFFF">BADGE</font></td>
  <td><font color="#FFFFFF">EMPLOYEE</font></td>
  <td><font color="#FFFFFF">SCHEDULES HOURS</font></td>
  <td><font color="#FFFFFF">HOURS COMPLETED</font></td>
  <td><font color="#FFFFFF">LATE TIME</font></td></tr>
  
  <?php
  foreach($dtEmp as $dtE){

				$horasHorario = "00:00:00";
				$sqlText = "select sec_to_time(sum(time_to_sec(TIMEDIFF(SCH_DEPARTURE, SCH_ENTRY)))) as sumHorario from schedules where sch_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."' and employee_id=".$dtE['emp_id'];
				$dtSch = $dbEx->selSql($sqlText);
				if($dtSch['0']['sumHorario']!=NULL){
					$horasHorario = $dtSch['0']['sumHorario'];
				}
				
				$horasLunch = "00:00:00";
				$sqlText = "select sec_to_time(sum(time_to_sec(TIMEDIFF(SCH_LUNCHIN,SCH_LUNCHOUT)))) as sumLunch from schedules where sch_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."' and employee_id=".$dtE['emp_id'];
				$dtLunch = $dbEx->selSql($sqlText);
				if($dtLunch['0']['sumLunch']!=NULL){
					$horasLunch = $dtLunch['0']['sumLunch'];	
				}
				
				$horasProgramadas = restarHoras($horasLunch,$horasHorario);
				
				$horasPayroll = 0;
				$sqlText = "select sum(payroll_htotal) as sumPayroll from payroll where payroll_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."' and employee_id=".$dtE['emp_id'];
				$dtPayroll = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$horasPayroll = $dtPayroll['0']['sumPayroll'];	
				}
				
				$horasAp = 0;
				$sqlText = "select id_apxemp, hours_ap from apxemp where employee_id=".$dtE['emp_id']." and id_tpap in(1) and hours_ap!='' and startdate_ap between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."' and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					foreach($dtAp as $dtA){
						$horasAp = $horasAp + $dtA['hours_ap'];	
					}
				}
				
				$horasException = 0;
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['emp_id']." and (exceptionemp_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
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
				?>
				
				<tr><td><?php echo $dtE['username']; ?></td>
                <td><?php echo $dtE['firstname']." ".$dtE['lastname']; ?></td>
                <td align="center"><?php echo $horasProgramadas; ?></td>
                <td align="center"><?php echo $horasTrab; ?></td>
                <td align="center"><b><?php echo $font.' '.$horasTarde; ?></b></font></td></tr>
                <?php
			}


?>