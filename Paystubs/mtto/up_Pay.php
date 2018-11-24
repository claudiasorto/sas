<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$fechaFunc = new OFECHA; 

$csv = array();
//verificamos la fecha seleccionada
if($_POST['lsDelivery']==0){
	echo '<script>alert("You must select a date'.$_POST['lsDelivery'].'");</script>';
	die();
}
else if($_FILES['flDoc']['size']==0){
	echo '<script>alert("You must select a document in format CSV")</script>';
	die();
}
else{
	$ext = strtolower(end(explode('.',$_FILES['flDoc']['name']))); 
	$type = $_FILES['flDoc']['type'];
	$tmpName = $_FILES['flDoc']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$row = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csv[$row]['badge'] = $data[0];
				$csv[$row]['bono'] = $data[1];
				$csv[$row]['aguinaldo'] = $data[2];
				$csv[$row]['severance'] = $data[3];
				$csv[$row]['other_income'] = $data[4];
				$csv[$row]['emi'] = $data[5];
				$csv[$row]['cheff'] = $data[6];
				$csv[$row]['cafeteria'] = $data[7];
				$csv[$row]['equipo'] = $data[8];
				$csv[$row]['optica'] = $data[9];
				$csv[$row]['loans'] = $data[10];
				$csv[$row]['advances'] = $data[11];
				
				$row++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV")</script>';
		die();	
	}
	$sqlText = "select paystub_ini, paystub_fin from paystub where paystub_id=".$_POST['lsDelivery'];
	$fecha = $dbEx->selSql($sqlText);	
	
	for($i=0; $i<$row; $i++){
		$sqlText = "select employee_id from employees where username='".$csv[$i]['badge']."'";
		$dtE = $dbEx->selSql($sqlText);
		//Si el badge ingresado coincide con un empleado sige el proceso, sino lo ignora
		if($dbEx->numrows>0){
			//Calculo de total de horas del agente 
			$horasTotal = 0;
			//Horas de Payroll
			$sqlText = "select sum(payroll_htotal) as pt from payroll where employee_id=".$dtE['0']['employee_id']." and payroll_date between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."'";
			$horasPayroll = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$horasTotal = $horasTotal + $horasPayroll['0']['pt'];
			}
			//Horas AP
			$sqlText = "select sum(hours_ap) as hap from apxemp where employee_id=".$dtE['0']['employee_id']." and id_tpap in(1,7) and hours_ap!='' and (startdate_ap between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."') and approved_status='A'";
			$horasAp = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$horasTotal = $horasTotal + $horasAp['0']['hap'];	
			}
			//Horas exception
			$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['0']['employee_id']." and (exceptionemp_date between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
				$dtEx = $dbEx->selSql($sqlText);
				$horasException = 0;
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
				$horasTotal = $horasTotal + $horasException;
				
			//Horas feriadas
			
			$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['0']['employee_id']." and (exceptionemp_date between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."') and exceptionemp_approved='A' and ex.exceptiontp_id=5 group by ex.employee_id";
			$dtHoliday = $dbEx->selSql($sqlText);
			$horasFeriadas = 0;
			if($dbEx->numrows>0){
					$horas = $dtHoliday['0']['hora']; 
					$min = $dtHoliday['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasFeriadas = $horas.".".$formatMinutos[1];	
					$horasFeriadas = $horasFeriadas * 2;
				}
				$horasTotal = $horasTotal + $horasFeriadas;
			
			//Fin de calculo de horas totales 
			//Calculo de salario
			$salario = 0;
			$sqlText = "select salary from employees where employee_id=".$dtE['0']['employee_id'];
			$dtsalario = $dbEx->selSql($sqlText);
			$salario = $horasTotal*($dtsalario['0']['salary']/176);
			
			//Calculo de horas nocturnas
			$horasNoct = 0;
			$dineroNoct = 0;
			$sqlText = "select sum(payroll_nigth) as pn from payroll where employee_id=".$dtE['0']['employee_id']." and (payroll_date between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."')";
			$dtHorasNoc = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$horasNoct = $dtHorasNoc['0']['pn'];
				$dineroNoct = (($dtsalario['0']['salary']/176)*1.25)*$dtHorasNoc['0']['pn'];	
			}
			//Calculo de horas extras diurnas
			$horasExtrasDia = 0;
			$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['0']['employee_id']." and (exceptionemp_date between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."') and exceptionemp_approved='A' and ex.exceptiontp_id=6 group by ex.employee_id";
			$dtExDiurna = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
					$horas = $dtExDiurna['0']['hora']; 
					$min = $dtExDiurna['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasExtrasDia = $horas.".".$formatMinutos[1];	
			}
			//Calculo de horas extras diurnas feriadas
			$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['0']['employee_id']." and (exceptionemp_date between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."') and exceptionemp_approved='A' and ex.exceptiontp_id=8 group by ex.employee_id";
			$dtExFeriadaDia = $dbEx->selSql($sqlText);
			$horasExtrasFeriadaDia = 0;
			if($dbEx->numrows>0){
					$horas = $dtExFeriadaDia['0']['hora']; 
					$min = $dtExFeriadaDia['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasExtrasFeriadaDia = $horas.".".$formatMinutos[1];	
					$horasExtrasFeriadaDia = $horasExtrasFeriadaDia * 2;
			}
			$horasExtrasDia = $horasExtrasDia + $horasExtrasFeriadaDia;
			$dineroExtraDia = ($dtsalario['0']['salary']/176)*2*$horasExtrasDia;	
			
			
			//Calculo de horas extras nocturnas
			$horasExtrasNoct = 0;
			$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['0']['employee_id']." and (exceptionemp_date between date '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."') and exceptionemp_approved='A' and ex.exceptiontp_id=7 group by ex.employee_id";
			$dtExNoct = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
					$horas = $dtExNoct['0']['hora']; 
					$min = $dtExNoct['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasExtrasNoct = $horas.".".$formatMinutos[1];	
			}
			$dineroExtraNoct = $horasExtrasNoct * ($dtsalario['0']['salary']/176) * 2 * 1.25; 
			//calculo de vacacion 
			$totalVacacion = 0;
			$sqlText = "select id_apxemp from apxemp where employee_id=".$dtE['0']['employee_id']." and id_tpap=5 and startdate_ap between '".$fecha['0']['paystub_ini']."' and '".$fecha['0']['paystub_fin']."' and approved_status='A'";
			$dtVacacion = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$totalVacacion = ($dtsalario['0']['salary']/176)*1.30;	
			}
			//Calculo del total de ingresos
			$totalIngresos = 0;
			$totalIngresos = $csv[$i]['other_income'] + $csv[$i]['bono'] + $csv[$i]['severance'] + $totalVacacion + $csv[$i]['aguinaldo'] + $dineroExtraNoct + $dineroExtraDia + $dineroNoct + $salario;
			
			
			//Calculo de ISSS
			$totalIsss = 0;
			if($totalIngresos <= 342.85){
				$totalIsss = $totalIngresos * 0.03;	
			}
			else{	$totalIsss = 10.29;
			}
			
			//Calculo AFP
			$totalAfp = 0;
			$totalAfp = $totalIngresos * 0.0625;
			
			//Calculo ISR
			$ingSinAfp = $totalIngresos - $totalAfp;
			$totalIsr = 0;
			if($ingSinAfp>0.01 and $ingSinAfp<=243.8){
				$totalIsr = 0;
			}
			else if($ingSinAfp>243.8 and $ingSinAfp<=321.42){
				$totalIsr = (($ingSinAfp - 243.8)*.10)+8.74;	
			}
			else if($ingSinAfp>321.42 and $ingSinAfp<=457.9){
				$totalIsr = (($ingSinAfp-321.42)*.20)+16.35;
			}
			else if($ingSinAfp>457.9 and $ingSinAfp<=1029.33){
				$totalIsr = (($ingSinAfp-457.9)*.20)+30;
			}
			else if($ingSinAfp>1029.33){
				$totalIsr = (($ingSinAfp-1029.33)*.30)+144.28;	
			}
			else{
				$totalIsr = 0;	
			}
			$totalDeducciones = $totalIsss + $totalAfp + $totalIsr;
			//Calculo total descuentos
			$totalDescuentos = $csv[$i]['emi'] + $csv[$i]['cheff'] + $csv[$i]['cafeteria']+ $csv[$i]['equipo'] + $csv[$i]['optica'] + $csv[$i]['loans'] + $csv[$i]['advances'];
			//Total a recibir
			$TotalRecibir = $totalIngresos - $totalDeducciones - $totalDescuentos;
			
			
			$sqlText = "select * from paystubxemp where employee_id=".$dtE['0']['employee_id']." and paystub_id=".$_POST['lsDelivery'];
			$dtPay = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$sqlText = "update paystubxemp set ";
				$sqlText .=" payxemp_nhoras='".$horasTotal."',";
				$sqlText .=" payxemp_salary='".$salario."',";
				$sqlText .=" payxemp_nhorasnoct='".$horasNoct."',";
				$sqlText .=" payxemp_horasnoct='".$dineroNoct."',";
				$sqlText .=" payxemp_notdiurnal='".$horasExtrasDia."',";
				$sqlText .=" payxemp_otdiurnal='".$dineroExtraDia."',";
				$sqlText .=" payxemp_notnoct='".$horasExtrasNoct."',";
				$sqlText .=" payxemp_otnoct='".$dineroExtraNoct."',";
				$sqlText .=" payxemp_bono='".$csv[$i]['bono']."', ";
				$sqlText .=" payxemp_aguinaldo='".$csv[$i]['aguinaldo']."', ";
				$sqlText .=" payxemp_vacation='".$totalVacacion."', ";
				$sqlText .=" payxemp_severance='".$csv[$i]['severance']."', ";
				$sqlText .=" payxemp_otherincome='".$csv[$i]['other_income']."', ";
				$sqlText .=" payxemp_isr='".$totalIsr."',";
				$sqlText .=" payxemp_isss='".$totalIsss."',";
				$sqlText .=" payxemp_afp='".$totalAfp."',";
				$sqlText .=" payxemp_emi='".$csv[$i]['emi']."', ";	
				$sqlText .=" payxemp_cheff='".$csv[$i]['cheff']."', ";
				$sqlText .=" payxemp_cafeteria='".$csv[$i]['cafeteria']."', ";
				$sqlText .=" payxemp_damagedequip='".$csv[$i]['equipo']."', ";
				$sqlText .=" payxemp_optica='".$csv[$i]['optica']."', ";
				$sqlText .=" payxemp_loans='".$csv[$i]['loans']."', ";
				$sqlText .=" payxemp_advances='".$csv[$i]['advances']."',";
				$sqlText .=" payxemp_liquid='".$TotalRecibir."'";
				$sqlText .=" where payxemp_id=".$dtPay['0']['PAYXEMP_ID'];
				
				$dbEx->updSql($sqlText);
			}
			//Si no existe paystub se registra uno nuevo y sus datos
			else{
				$sqlText = "insert into paystubxemp set employee_id=".$dtE['0']['employee_id'].", ";
				$sqlText .=" paystub_id=".$_POST['lsDelivery'].", ";
				$sqlText .=" payxemp_nhoras='".$horasTotal."',";
				$sqlText .=" payxemp_salary='".$salario."',";
				$sqlText .=" payxemp_nhorasnoct='".$horasNoct."',";
				$sqlText .=" payxemp_horasnoct='".$dineroNoct."',";
				$sqlText .=" payxemp_notdiurnal='".$horasExtrasDia."',";
				$sqlText .=" payxemp_otdiurnal='".$dineroExtraDia."',";
				$sqlText .=" payxemp_notnoct='".$horasExtrasNoct."',";
				$sqlText .=" payxemp_otnoct='".$dineroExtraNoct."',";
				$sqlText .=" payxemp_bono='".$csv[$i]['bono']."', ";
				$sqlText .=" payxemp_aguinaldo='".$csv[$i]['aguinaldo']."', ";
				$sqlText .=" payxemp_vacation='".$totalVacacion."', ";
				$sqlText .=" payxemp_severance='".$csv[$i]['severance']."', ";
				$sqlText .=" payxemp_otherincome='".$csv[$i]['other_income']."', ";
				$sqlText .=" payxemp_isr='".$totalIsr."',";
				$sqlText .=" payxemp_isss='".$totalIsss."',";
				$sqlText .=" payxemp_afp='".$totalAfp."',";
				$sqlText .=" payxemp_emi='".$csv[$i]['emi']."', ";	
				$sqlText .=" payxemp_cheff='".$csv[$i]['cheff']."', ";
				$sqlText .=" payxemp_cafeteria='".$csv[$i]['cafeteria']."', ";
				$sqlText .=" payxemp_damagedequip='".$csv[$i]['equipo']."', ";
				$sqlText .=" payxemp_optica='".$csv[$i]['optica']."', ";
				$sqlText .=" payxemp_loans='".$csv[$i]['loans']."', ";
				$sqlText .=" payxemp_advances='".$csv[$i]['advances']."', ";
				$sqlText .=" payxemp_liquid='".$TotalRecibir."'";
				$dbEx->insSql($sqlText);
			}
		}
	}
	$rslt = 2;
	if($rslt ==2){
		echo '<script>alert("Pay stub upload successful");location.href="../index.php";</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again"); return false;		</script>';	
	}	
}

?>