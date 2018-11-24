<?php
//Funciones para Quality	
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
	function burbuja($A,$n)
    {
        for($i=1;$i<$n;$i++)
        {
            for($j=0;$j<$n-$i;$j++)
                {
                  if($A[$j]>$A[$j+1])
                        {$k=$A[$j+1]; $A[$j+1]=$A[$j]; $A[$j]=$k;}
                }
        }
      return $A;
    }

  
  switch($_POST['Do']){
	case 'searchScorecard';
		$rslt = cargaPag("../mttoAgentScorecard/filtrosAgentScorecard.php");
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
			$sqlText = "select * from account where id_typeacc=2 and account_status='A' order by name_account ";
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
	
	case 'loadSearchScorecard':
		//Inicializacion y asignacion de variables

		$percentHoursCompletion = 0.40;
		$percentQA = 0.40;
		$percentAht = 0.05;
		$percentRefused = 0.05;
		$percentEfficiency = 0.10;
		
		$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
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
			
				//Busca Efficiency
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
			if($_POST['emp']>0){
				$filtro .=" and e.employee_id=".$_POST['emp'];
			}
			if(strlen($_POST['nombre'])>0){
				$filtro .=" and (e.firstname like '%".$_POST['nombre']."%' or e.lastname like '%".$_POST['nombre']."%' or concat(firstname,' ',lastname) like '%".$_POST['nombre']."%'  ) ";
			}
			if(strlen($_POST['badge'])>0){
				$filtro .=" and e.username like '%".$_POST['badge']."%'";
			}
			$top = $indice;
			if($_POST['top']>0){
				$top = $_POST['top'];
			}
			
			//Recorrer los agentes para ver si cumplen con los filtros 
						
			$rslt = '<table class="backTablaMain" width="800" align="center" cellpadding="2" cellspacing="2">';
			$rslt.='<tr><td align="left"><form target="_blank" action="mttoAgentScorecard/xls_scorecard.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="top" value="'.$top.'">
			<input type="hidden" name="fechaIni" value="'.$fechaIni.'">
			<input type="hidden" name="fechaFin" value="'.$fechaFin.'"</td></tr>';
			
			//Titulos de los rangos de notas entre los cuales se pusieron las notas
			$rslt .= '<tr><td colspan="9"><b>Hours Completion: </b> Completion < 85% = 0 points, Completion >=85% and <90% = 5 points, Completion >=90% and <95% = 7.5 points, Completion >=95% = 10 points</td></tr>';
			
			$rslt .='<tr><td colspan="9"><b>Quality:</b> Score<85% = 0 points, Score >=85% and <90% = 20 points, Score >=90% and  <95% = 30 points, Score >=95% = 40 points</td></tr>';
			
			$rslt .='<tr><td colspan="9"><b>AHT: </b> > 10 mins = 0 points, >8 min and <=10 min = 1 point, > 7 min and <= 8 min = 3 points, >=5 min and <= 7 min = 5 points </td></tr>';
			
			$rslt .='<tr><td colspan="9"><b>Refused: </b> 0% - 2% refused = 5 points, 2% - 5% refused = 3 points </td></tr>';
			
			$rslt .='<tr><td colspan="9"><b>Efficiency: </b> 0% - 80% effic = 0 points, 80% - 85% = 5 points, 85% - 95% = 7 points, >=95% = 10 points</td></tr>';
			

			$rslt .='<tr class="backList">
			<td>Badge</td>
			<td>Employee</td>
			<td align="center">Global position</td>
			<td align="center">Score</td>
			<td align="center">Hours completion <br>'.($percentHoursCompletion * 100).'%</td>
			<td align="center">Quality <br>'.($percentQA * 100).'%</td>
			<td align="center">AHT <br>'.($percentAht * 100).'%</td>
			<td align="center">Refused <br>'.($percentRefused * 100).'%</td>
			<td align="center">Efficiency <br>'.($percentEfficiency * 100).'%</td></tr>';
			
			$indice = $indice-1;
			$contador = 0;
			for($i=$indice; $i>=0; $i--){
				$contador = $contador +1;
				
				$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep where user_status=1 and pe.status_plxemp='A' ".$filtro." and e.employee_id=".$employee[$i];
				$dtEmpFiltros = $dbEx->selSql($sqlText);
				//Verifica si agente cumplio las condiciones
				if($dbEx->numrows>0){
				$pos = ($indice - $i) + 1;
					$rslt .='<tr class="rowCons"><td>'.$dtEmpFiltros['0']['username'].'</td>
					<td>'.$dtEmpFiltros['0']['firstname']." ".$dtEmpFiltros['0']['lastname'].'</td>
					<td align="center">'.$pos.'</td>
					<td align="center">'.$notaTotal[$i].'</td>
					<td align="center">'.$notaHoursCompletion[$i].'</td>
					<td align="center">'.$notaQA[$i].'</td>
					<td align="center">'.$notaAht[$i].'</td>
					<td align="center">'.$notaRefused[$i].'</td>
					<td align="center">'.$notaEfficiency[$i].'</td></tr>';
				
				} //Termina segundo numrows
				if($contador >= $top){
					$i = -1;
				}
			
			} //Termina for
			
		}//Termina numrows
		
		echo $rslt;
	
	break;
	
  }
  
?>