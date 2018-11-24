<?php

################################################
# Clase para obtener el calculo del salario #
################################################

header("Content-Type: text/html; charset=utf-8");
require_once("db_funcs.php");
require_once("fecha_funcs.php");


class SAL{

	function getSalary($employee_id, $fechaIni, $fechaFin, $fechaEntrega,
					$otherIncome, $bono, $descuentos, $aguinaldo){

        $dbEx = new DBX;
		$oFec = new OFECHA;

		$horasTotal = 0;
		//Horas de Payroll

		$sqlText = "select ifnull(round((sum(TIME_TO_SEC(payroll_daytime)))/3600,2),0) as pt from payroll ".
			"where employee_id=".$employee_id." and payroll_date between date '".$fechaIni."' and '".$fechaFin."'";
		$horasPayroll = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$horasTotal = $horasTotal + $horasPayroll['0']['pt'];
		}

		//Horas AP
		$sqlText = "select sum(hours_ap) as hap from apxemp where employee_id=".$employee_id." and id_tpap in(1,7) and hours_ap!='' ".
		"and (startdate_ap between date '".$fechaIni."' and '".$fechaFin."') and approved_status='A'";
		$horasAp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$horasTotal = $horasTotal + $horasAp['0']['hap'];
		}
		
		//Horas exception
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_excep ".
	 		"from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
	 		" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
		$dtEx = $dbEx->selSql($sqlText);
		$horasException = 0;
		if($dbEx->numrows>0){
			$horasException = $dtEx['0']['h_excep'];
		}
		$horasTotal = $horasTotal + $horasException;

		//Horas feriadas

		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_feriada ".
			" from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
			" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=5 group by ex.employee_id";
		$dtHoliday = $dbEx->selSql($sqlText);
		$horasFeriadas = 0;
		if($dbEx->numrows>0){
			$horasFeriadas = $dtHoliday['0']['h_feriada'];
		}
		$horasTotal = $horasTotal + $horasFeriadas;
		
		//Fin de calculo de horas totales
		//Calculo de salario se divide entre 2 por la quincena
		$salario = 0;
		$salarioEmp = 0;
		$sqlText = "select salary from employees where employee_id=".$employee_id;
		$dtsalario = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$salarioEmp = $dtsalario['0']['salary'];
		}
		
		//El salario se dividira entre el total de horas programadas, si no tiene se pone por defecto 176
		$sqlText = "select round((((SUM(TIME_TO_SEC(sch_departure))) - (SUM(TIME_TO_SEC(sch_entry)))) - ".
			//"((SUM(TIME_TO_SEC(sch_break1in))) - (SUM(TIME_TO_SEC(sch_break1out)))) - ".
			//"((SUM(TIME_TO_SEC(sch_break2in))) - (SUM(TIME_TO_SEC(sch_break2out)))) - ".
   			"((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) horas_prog ".
			"from schedules ".
			"where employee_id = ".$employee_id." ".
			"and sch_date between date '".$fechaIni."' and '".$fechaFin."'";

		$dtPr = $dbEx->selSql($sqlText);
		$horasProgram = 176;
		if($dbEx->numrows>0){
  			if ($dtPr['0']['horas_prog'] <> ""){
     			$horasProgram = $dtPr['0']['horas_prog'] * 2;
			}
		}

		$salario = $horasTotal*($salarioEmp/$horasProgram);
		
		//Calculo de horas adicionales
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_adit ".
			" from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
			" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=9 group by ex.employee_id";
		$dtAddH = $dbEx->selSql($sqlText);
		$horasAdicionales = 0;
		if($dbEx->numrows>0){
			$horasAdicionales = $dtAddH['0']['h_adit'];
		}
		$dineroHorasAdicionales = $horasAdicionales*($salarioEmp/$horasProgram);
		
		//Calculo de horas nocturnas
		$horasNoct = 0;
		$dineroNoct = 0;
		$sqlText = "select ifnull(round((sum(TIME_TO_SEC(payroll_nigth)))/3600,2),0) as pn from payroll where employee_id=".$employee_id.
			" and (payroll_date between date '".$fechaIni."' and '".$fechaFin."')";
		$dtHorasNoc = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$horasNoct = $dtHorasNoc['0']['pn'];
			$dineroNoct = (($salarioEmp/$horasProgram)*1.25)*$dtHorasNoc['0']['pn'];
		}
		//Calculo de horas extras diurnas
		$horasExtrasDia = 0;
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_extra ".
			" from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
			" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=6 group by ex.employee_id";
		$dtExDiurna = $dbEx->selSql($sqlText);
		$horasExtrasDia = 0;
		if($dbEx->numrows>0){
			$horasExtrasDia = $dtExDiurna['0']['h_extra'];
		}
		//Calculo de horas extras diurnas feriadas
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_extra_f ".
			"from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
			" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=8 group by ex.employee_id";
		$dtExFeriadaDia = $dbEx->selSql($sqlText);
		$horasExtrasFeriadaDia = 0;
		if($dbEx->numrows>0){
			$horasExtrasFeriadaDia = $dtExFeriadaDia['0']['h_extra_f'];
			$horasExtrasFeriadaDia = $horasExtrasFeriadaDia * 2;
		}
		$horasExtrasDia = $horasExtrasDia + $horasExtrasFeriadaDia;
		$dineroExtraDia = ($salarioEmp/$horasProgram)*2*$horasExtrasDia;

		//Calculo de horas extras nocturnas
		$horasExtrasNoct = 0;
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_extra_fn ".
			" from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
			" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=7 group by ex.employee_id";
		$dtExNoct = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$horasExtrasNoct = $dtExNoct['0']['h_extra_fn'];
		}
		$dineroExtraNoct = $horasExtrasNoct * ($salarioEmp/$horasProgram) * 2 * 1.25;
		//calculo de vacacion
		$totalVacacion = 0;
		$sqlText = "select id_apxemp from apxemp where employee_id=".$employee_id." and id_tpap=5 and ".
			" (startdate_ap between date '".$fechaFin."' and '".$fechaEntrega."') and approved_status='A'";
		$dtVacacion = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$totalVacacion = ($dtsalario['0']['salary']/2)*1.30;
		}
		//$septimo = 0;

		//Calculo de ausentismos

		$start = strtotime($fechaIni);
		$end = strtotime($fechaFin);
		$septimo = 0;
		$nuevaSemana = 0;
		for($i = $start; $i <=$end; $i +=86400){
			$sqlText = "select absent_id, absent_status from absenteeism where employee_id=".$employee_id." and absent_date='".date('Y-m-d',$i)."' and absent_status='A'";
			$dtAbsent = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$sqlText = "select id_apxemp from apxemp where employee_id=".$employee_id." and startdate_ap='".date('Y-m-d',$i)."' and approved_status='A' and typesanction_ap!=3";
				$dtApAbsent = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){

				}
				else{
					if($nuevaSemana == 0){
						$septimo = $septimo + 17.85;
						$nuevaSemana = 1;
       				}
				}
			}
			$nFecha = strtotime(date("Y/m/d",$i));
			$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);

			if($dia=="0"){
				$nuevaSemana = 0;
			}
		}	//Termina ausentismo
		
		//Calculo del total de ingresos
		$totalIngresos = 0;
		$totalIngresos =  $totalVacacion  + $dineroExtraNoct + $dineroExtraDia + $dineroNoct + $salario + $dineroHorasAdicionales - $septimo + $otherIncome + $bono - $descuentos + $aguinaldo;

		//Verifica el tipo de plaza del empleado, si es fija o temporal realiza los descuentos de Isss, AFP e ISR, si es Servicios Profesionales solo calcula el 10%
		$sqlText = "select tp_hiring from employees where employee_id=".$employee_id;
		$dtPlaza = $dbEx->selSql($sqlText);
		$tpPlaza = "F";
		if($dbEx->numrows>0){
			$tpPlaza = $dtPlaza['0']['tp_hiring'];
		}

		if($tpPlaza=="F" or $tpPlaza=="T"){
			//Calculo de ISSS
			$totalIsss = 0;
			if($totalIngresos <= 342.85){
				$totalIsss = $totalIngresos * 0.03;
			}
			else{
				$totalIsss = 10.29;
			}

			//Calculo AFP
			$totalAfp = 0;
			$totalAfp = $totalIngresos * 0.0725;

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
		}

		else if($tpPlaza =="P"){
			//Calculo de ISR
			$totalIsr = $totalIngresos * 0.10;
			$totalIsss = 0;
			$totalAfp = 0;
		}

		$totalDeducciones = $totalIsss + $totalAfp + $totalIsr;
		//Calculo total descuentos
		$totalDescuentos = 0;
		//Total a recibir
		$TotalRecibir = $totalIngresos - $totalDeducciones - $totalDescuentos;

		$this->horasTotal= $horasTotal;
		$this->salario = $salario;
		$this->horasAdicionales = $horasAdicionales;
		$this->dineroHorasAdicionales = $dineroHorasAdicionales;
		$this->septimo = $septimo;
		$this->horasNoct = $horasNoct;
		$this->dineroNoct = $dineroNoct;
		$this->horasExtrasDia = $horasExtrasDia;
		$this->dineroExtraDia = $dineroExtraDia;
		$this->horasExtrasNoct = $horasExtrasNoct;
		$this->dineroExtraNoct = $dineroExtraNoct;
		$this->totalVacacion = $totalVacacion;
		$this->totalIsr = $totalIsr;
		$this->totalIsss = $totalIsss;
		$this->totalAfp = $totalAfp;
		$this->TotalRecibir = $TotalRecibir;
		$this->totalIngresos = $totalIngresos;
		$this->totalDeducciones = $totalDeducciones;
		          
		return $TotalRecibir;

	}


}
?>
