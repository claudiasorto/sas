<?php

################################################
# Clase para obtener el calculo del salario #
################################################

header("Content-Type: text/html; charset=utf-8");
require_once("db_funcs.php");
require_once("fecha_funcs.php");


class SAL{

	function getMontoDesc($baseAmount,
							$bottonAmount,
							$topAmount,
							$percentage, 
							$overExcess, 
							$fixedFee,
							$maxQuotable){

		$totalDesc = 0;
		$flagCalculo = true;
		$baseAmount = round($baseAmount,2);

		//Monto base sera cambiado al maximo cotizable si supera dicho valor
		if(!is_null($maxQuotable)){
			if($baseAmount > $maxQuotable){
				$baseAmount = $maxQuotable;
			}
		}

		//Hacer calculos si el monto base se encuentra dentro de los limites de monto superior e inferior del impuesto
		if(!is_null($bottonAmount)){
			if( $baseAmount < $bottonAmount ){
				$flagCalculo = false;
			}
		}
		if(!is_null($topAmount)){
			if( $baseAmount > $topAmount ){
				$flagCalculo = false;
			}
		}

		if ($flagCalculo){
			//Evaluar si el descuento se aplica sobre el exceso del monto base
			if(!is_null($overExcess)){
				$totalDesc = ($percentage/100) * ($baseAmount - $overExcess);
			}
			else{
				$totalDesc = ($percentage/100) * $baseAmount;
			}

			//Evaluar si posee una cuota fija extra
			if(!is_null($fixedFee)){
				$totalDesc = $totalDesc + $fixedFee;
				$totalDesc = round($totalDesc,2);
			}
		}

		return $totalDesc;

	}

	function calcularPagoEmpleado($employee_id, 
								$paystub_id, 
								$fechaIni, 
								$fechaFin, 
								$fechaEntrega){

		$dbEx 		= new DBX;
		$result 	= 'Error';
		$horasTotal = 0;

		//Obtener el numero de la boleta de pagos
		$sqlText = "select payxemp_id, payxemp_salarydisc, payxemp_bono, payxemp_aguinaldo, payxemp_severance, payxemp_otherincome ".
					"from paystubxemp ".
					"where employee_id=".$employee_id.
					" and paystub_id=".$paystub_id ;
		
		$dtP = $dbEx->selSql($sqlText);

		//Crear boleta de pago si no existe
		if ($dbEx->numrows==0) {
			$sqlText = "insert into paystubxemp ".
						" set employee_id=".$employee_id.
						" ,paystub_id=".$paystub_id;

			$dbEx->insSql($sqlText);
			$payxemp_id = $dbEx->insertID;
			$salarydisc = 0;
			$bono 		= 0;
			$aguinaldo 	= 0;
			$indeminizacion = 0;
			$otherIncome = 0;

		}	
		else{
			$payxemp_id = $dtP['0']['payxemp_id'];
			$salarydisc = $dtP['0']['payxemp_salarydisc'];
			$bono 		= $dtP['0']['payxemp_bono'];
			$aguinaldo 	= $dtP['0']['payxemp_aguinaldo'];
			$indeminizacion = $dtP['0']['payxemp_severance'];
			$otherIncome = $dtP['0']['payxemp_otherincome'];
		}


		//Horas trabajadas
		$sqlText = "select ifnull(round((sum(TIME_TO_SEC(payroll_daytime)))/3600,2),0) as pt from payroll where employee_id=".$employee_id." and payroll_date between date '".$fechaIni."' and '".$fechaFin."'";
		$horasPayroll = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$horasTotal = $horasTotal + $horasPayroll['0']['pt'];
		}
		//Horas AP
		$sqlText = "select sum(hours_ap) as hap from apxemp where employee_id=".$employee_id." and id_tpap in(1,7) and hours_ap!='' and (startdate_ap between date '".$fechaIni."' and '".$fechaFin."') and approved_status='A'";
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
		//Validar si esto debe ir aca
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_feriada ".
		" from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
		" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=5 group by ex.employee_id";
		$dtHoliday = $dbEx->selSql($sqlText);
		$horasFeriadas = 0;
		if($dbEx->numrows>0){
			$horasFeriadas = $dtHoliday['0']['h_feriada'] * 2;
		}
		$horasTotal = $horasTotal + $horasFeriadas;

		//Fin de calculo de horas totales 

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
	        		//Horas programadas se multiplican por 2 para sacar el valor de hora por mes, ya que la sumatoria de horas es de una quincena y el salio es base a mes
	        		//Validar que pasa con el cargue de horas programadas cuando es vacacion
	                $horasProgram = $dtPr['0']['horas_prog'] * 2;
			}
		}
		
		
		$horasExtrasDia = 0;
		if($horasTotal > $horasProgram){
			$horasExtrasDia = $horasTotal - ($horasProgram/2);
		}

		$salario = ($horasTotal-$horasExtrasDia) *($salarioEmp/$horasProgram);

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
			$dineroNoct = (($salarioEmp/$horasProgram)*0.25)*$dtHorasNoc['0']['pn'];
		}
		//Calculo de horas extras diurnas
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_extra ".
		" from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
		" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=6 group by ex.employee_id";
		$dtExDiurna = $dbEx->selSql($sqlText);

		if($dbEx->numrows>0){
			$horasExtrasDia = $horasExtrasDia + $dtExDiurna['0']['h_extra'];
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
		$dineroExtraDia = ($salarioEmp/$horasProgram)*$horasExtrasDia * 2;


		//Calculo de horas extras nocturnas
		$horasExtrasNoct = 0;
		$sqlText = "select ifnull(round(((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) /3600,2),0) as h_extra_fn ".
		" from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$employee_id.
		" and (exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."') and exceptionemp_approved='A' and ex.exceptiontp_id=7 group by ex.employee_id";
		$dtExNoct = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$horasExtrasNoct = $dtExNoct['0']['h_extra_fn'];
		}
		$dineroExtraNoct = $horasExtrasNoct * ($salarioEmp/$horasProgram) * 2.5;
		//calculo de vacacion 
		$totalVacacion = 0;
		$sqlText = "select id_apxemp from apxemp where employee_id=".$employee_id." and id_tpap=5 and ".
			" (startdate_ap between date '".$fechaFin."' and '".$fechaEntrega."') and approved_status='A'";
		$dtVacacion = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$totalVacacion = ($dtsalario['0']['salary']/2)*1.30;	
		}

		//Calculo del total de ingresos
		$totalIngresos = 0;
		$totalIngresos =  $totalVacacion  + 
							$dineroExtraNoct + 
							$dineroExtraDia + 
							$dineroNoct + 
							$salario + 
							$dineroHorasAdicionales +
							$bono + 
							$aguinaldo +
							$indeminizacion + 
							$otherIncome;

		//Identificando tipo de plaza
		$sqlText = "select jt.job_type_name ".
					"from plazaxemp pe inner join job_type jt on pe.job_type_id = jt.job_type_id ".
					"where start_date <= '".$fechaIni."' ".
	    			"and ifnull(end_date,date(sysdate())) >= '".$fechaFin."' ".
					"and pe.employee_id = ".$employee_id;

		$dtPlaza = $dbEx->selSql($sqlText);
		$tpPlaza = "FIJO";
		if($dbEx->numrows>0){
			$tpPlaza = $dtPlaza['0']['job_type_name'];
		}
		//Calculo de impuestos de ley si plaza es fija
		$sqlText = "select ld.legaldisc_id, ".
						"ld.legaldisc_name, ".
						"ld.taxable_remunation, ".
					    "ld.percentage, ".
					    "ld.botton_amount, ".
					    "ld.top_amount, ".
					    "ld.over_excess, ".
					    "ld.fixed_fee, ".
					    "ld.pension_flag, ".
					    "ld.max_quotable ".
					"from legal_discount ld inner join employees e ".
						"on ld.geography_code = e.geography_code ".
					"where ifnull(end_date,date(sysdate())) <= date(sysdate()) ".
					    "and e.employee_id = ".$employee_id." ".
					"order by ld.taxable_remunation asc";

		$dtDescLey = $dbEx->selSql($sqlText);
		$DescGravados = 0;
		$DescPostGravamen = 0;

		//Eliminar los datos de la tabla de descuentos legales 
		$sqlText = "delete from paystub_legaldisc where payxemp_id = ".$payxemp_id;
		$dbEx->updSql($sqlText);

		foreach ($dtDescLey as $dtDL) {
			$montoDesc = 0;
			if($dtDL['taxable_remunation'] == 'N'){
				$montoDesc = $this->getMontoDesc(
								$totalIngresos,
								$dtDL['botton_amount'],
								$dtDL['top_amount'],
								$dtDL['percentage'],
								$dtDL['over_excess'],
								$dtDL['fixed_fee'],
								$dtDL['max_quotable']);

				if($montoDesc > 0){
					$sqlText = "insert into paystub_legaldisc(".
										"payxemp_id, ".
										"legaldisc_id, ".
										"amount) ".
								"values(".$payxemp_id.", ".
								$dtDL['legaldisc_id'].", ".
								$montoDesc.")";

					$dbEx->insSql($sqlText);
					$DescGravados = $DescGravados + $montoDesc;
				}
			}elseif($dtDL['taxable_remunation'] == 'Y'){
				$montoDesc = $this->getMontoDesc(
								$totalIngresos - $DescGravados,
								$dtDL['botton_amount'],
								$dtDL['top_amount'],
								$dtDL['percentage'],
								$dtDL['over_excess'],
								$dtDL['fixed_fee'],
								$dtDL['max_quotable']);

				if($montoDesc > 0){

					$sqlText = "insert into paystub_legaldisc(".
										"payxemp_id, ".
										"legaldisc_id, ".
										"amount) ".
								"values(".$payxemp_id.", ".
								$dtDL['legaldisc_id'].", ".
								$montoDesc.")";

					$dbEx->insSql($sqlText);
					$DescPostGravamen = $DescPostGravamen + $montoDesc;
				}

			}
		}

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

		$totalRecibir = $totalIngresos - $septimo - $salarydisc - $DescGravados - $DescPostGravamen;

		$sqlText = "update paystubxemp set ";
		$sqlText .=" payxemp_nhoras= '".$horasTotal."',";
		$sqlText .=" payxemp_salary='".$salario."',";
		$sqlText .=" payxemp_nadditionalhours='".$horasAdicionales."', ";
		$sqlText .=" payxemp_additionalhours='".$dineroHorasAdicionales."',";
		$sqlText .=" payxemp_seventh='".$septimo."',";
		$sqlText .=" payxemp_nhorasnoct='".$horasNoct."',";
		$sqlText .=" payxemp_horasnoct='".$dineroNoct."',";
		$sqlText .=" payxemp_notdiurnal='".$horasExtrasDia."',";
		$sqlText .=" payxemp_otdiurnal='".$dineroExtraDia."',";
		$sqlText .=" payxemp_notnoct='".$horasExtrasNoct."',";
		$sqlText .=" payxemp_otnoct='".$dineroExtraNoct."',";
		$sqlText .=" payxemp_vacation='".$totalVacacion."', ";
		$sqlText .=" payxemp_salarydisc ='".$salarydisc."', ";
		$sqlText .=" payxemp_bono = '".$bono."', ";
		$sqlText .=" payxemp_aguinaldo = '".$aguinaldo."', ";
		$sqlText .=" payxemp_severance = '".$indeminizacion."', ";
		$sqlText .=" payxemp_otherincome = '".$otherIncome."', ";
		$sqlText .=" payxemp_liquid='".$totalRecibir."' ";
		$sqlText .=" where payxemp_id=".$payxemp_id;

		$dbEx->updSql($sqlText);

		if($dbEx->affectedRows > 0){
			$result = 'Exito';
		}
		else{$result = 'Error';}

		return $result;

	}

	//Funcion para mostrar la boleta de pago del empleado
	function getBoletaPago($paystub_id,
						$employee_id){


		$dbEx = new DBX;
		$rslt = "";

		$sqlText = "select p.payxemp_id, ".
					"e.employee_id, ".
					"ps.paystub_id, ".
					"round(payxemp_nhoras,2) as nhoras, ".
					"round(payxemp_salary,2) as salary, ".
					"round(payxemp_nadditionalhours,2) as naddHours, ".
					"round(payxemp_additionalhours,2) as addHours, ".
					"round(payxemp_salarydisc,2) as desc_h, ".
					"round(payxemp_seventh,2) as seventh, ".
					"round(payxemp_nhorasnoct,2) as nhours_noc, ".
					"round(payxemp_horasnoct,2) as hours_noct, ".
					"round(payxemp_notdiurnal,2) as nhoursext_di, ".
					"round(payxemp_otdiurnal,2) as hoursext_di, ".
					"round(payxemp_notnoct,2) as nhoursext_noct, ".
					"round(payxemp_otnoct,2) as hoursext_noct, ".
					"round(payxemp_bono,2) as bono, ".
					"round(payxemp_aguinaldo,2) as aguinaldo, ".
					"round(payxemp_vacation,2) as vacation, ".
					"round(payxemp_severance,2) as indemnizacion, ".
					"round(payxemp_otherincome,2) as other_income, ".
					"round(payxemp_liquid,2) as liquid, ".
					"paystub_ini, ".
					"paystub_fin, ".
					"date_format(paystub_ini,'%d/%m/%Y') as inicio_per, ".
					"date_format(paystub_fin,'%d/%m/%Y') as fin_per, ".
					"date_format(paystub_delivery,'%d/%m/%Y') as fec_delivery, ".
					"round(payxemp_liquid,2) as liquid, ".
					"payxemp_status, ".
					"username, ".
					"firstname, ".
					"lastname,  ".
					"((ifnull(p.attribute1,0)) + (ifnull(p.attribute11,0)) + ".
					"(ifnull(p.attribute2,0)) + (ifnull(p.attribute12,0)) + ".
    				"(ifnull(p.attribute3,0)) + (ifnull(p.attribute13,0)) + ".
    				"(ifnull(p.attribute4,0)) + (ifnull(p.attribute14,0)) + ".
   					"(ifnull(p.attribute5,0)) + (ifnull(p.attribute15,0)) + ".
    				"(ifnull(p.attribute6,0)) + (ifnull(p.attribute16,0)) + ".
		    		"(ifnull(p.attribute7,0)) + (ifnull(p.attribute17,0)) + ".
    				"(ifnull(p.attribute8,0)) + (ifnull(p.attribute18,0)) + ".
    				"(ifnull(p.attribute9,0)) + (ifnull(p.attribute19,0)) + ".
	    			"(ifnull(p.attribute10,0)) + (ifnull(p.attribute20,0))) DESCUENTOS ".
					"from paystubxemp p inner join employees e on e.employee_id = p.employee_id ".
					"inner join paystub ps on ps.paystub_id=p.paystub_id ".
					"where p.paystub_id=".$paystub_id." and e.employee_id=".$employee_id;

		$dtEmp = $dbEx->selSql($sqlText);

		$rslt .= '<table width="800" align="center" cellpadding="4" cellspacing="4" class="tablaVerde">';

		if($dbEx->numrows>0){

			//Horas programadas
			$sqlText = "select round((((SUM(TIME_TO_SEC(sch_departure))) - (SUM(TIME_TO_SEC(sch_entry)))) - ".
   			"((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) horas_prog ".
			"from schedules ".
			"where employee_id = ".$employee_id." ".
			"and sch_date between date '".$dtEmp['0']['paystub_ini']."' and '".$dtEmp['0']['paystub_fin']."'";

			$dtPr = $dbEx->selSql($sqlText);
			$horasProgram = 88;
			if($dbEx->numrows>0){
  				if ($dtPr['0']['horas_prog'] <> ""){
     				$horasProgram = $dtPr['0']['horas_prog'];
				}
			}

			$incNhoras = 0;
			$incSalary = 0;
			$incNaddHoras = 0;
			$incAddHoras = 0;
			$incSalaryDisc = 0;
			$incSeventh = 0;
			$incNhnoct = 0;
			$incHnoct =0;
			$incNotdia = 0;
			$incOtdia = 0;
			$incNotnoct = 0;
			$incOtnoct = 0;
			$incBono = 0;
			$incAguinaldo =0;
			$incVacacion =0;
			$incSeverance = 0;
			$incOtherIncome = 0;
			$incIsr  =0;
			$incIsss = 0;
			$incAfp = 0;
			$incRecibir = 0;
			$incTotalDescuentos = 0;

			$sqlText = "select pi.*, ".
			    "((ifnull(attribute1,0)) + (ifnull(attribute11,0)) + ".
					"(ifnull(attribute2,0)) + (ifnull(attribute12,0)) + ".
    				"(ifnull(attribute3,0)) + (ifnull(attribute13,0) ) + ".
    				"(ifnull(attribute4,0)) + (ifnull(attribute14,0)) + ".
   					"(ifnull(attribute5,0)) + (ifnull(attribute15,0)) + ".
    				"(ifnull(attribute6,0)) + (ifnull(attribute16,0)) + ".
		    		"(ifnull(attribute7,0)) + (ifnull(attribute17,0)) + ".
    				"(ifnull(attribute8,0)) + (ifnull(attribute18,0)) + ".
    				"(ifnull(attribute9,0)) + (ifnull(attribute19,0)) + ".
	    			"(ifnull(attribute10,0)) + (ifnull(attribute20,0))) DESCUENTOS ".
				" from paystub_incidents pi where payxemp_id=".$dtEmp['0']['payxemp_id'];

			$dtInc = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$incNhoras 		= $dtInc['0']['PAYINC_NHORAS'];
				$incSalary 		= $dtInc['0']['PAYINC_SALARY'];
				$incNaddHoras 	= $dtInc['0']['PAYINC_NADDITIONALHOURS'];
				$incAddHoras 	= $dtInc['0']['PAYINC_ADDITIONALHOURS'];
				$incSalaryDisc 	= $dtInc['0']['PAYINC_SALARYDISC'];
				$incSeventh 	= $dtInc['0']['PAYINC_SEVENTH'];
				$incNhnoct 		= $dtInc['0']['PAYINC_NHORASNOCT'];
				$incHnoct 		= $dtInc['0']['PAYINC_HORASNOCT'];
				$incNotdia 		= $dtInc['0']['PAYINC_NOTDIURNAL'];
				$incOtdia 		= $dtInc['0']['PAYINC_OTDIURNAL'];
				$incNotnoct 	= $dtInc['0']['PAYINC_NOTNOCT'];
				$incOtnoct 		= $dtInc['0']['PAYINC_OTNOCT'];
				$incBono 		= $dtInc['0']['PAYINC_BONO'];
				$incAguinaldo 	= $dtInc['0']['PAYINC_AGUINALDO'];
				$incVacacion 	= $dtInc['0']['PAYINC_VACATION'];
				$incSeverance 	= $dtInc['0']['PAYINC_SEVERANCE'];
				$incOtherIncome = $dtInc['0']['PAYINC_OTHERINCOME'];
				$incRecibir 	= $dtInc['0']['PAYINC_RECEIVED'];
				$incTotalDescuentos = $dtInc['0']['DESCUENTOS'];
			}


			$totalIncome = $dtEmp['0']['other_income'] + 
							$dtEmp['0']['bono']  + 
							$dtEmp['0']['vacation'] + 
							$dtEmp['0']['aguinaldo'] +
							$dtEmp['0']['indemnizacion'] + 
							$dtEmp['0']['hoursext_noct'] + 
							$dtEmp['0']['hoursext_di'] + 
							$dtEmp['0']['hours_noct'] + 
							$dtEmp['0']['salary'] + 
							$dtEmp['0']['addHours'] +
							$incOtherIncome + 
							$incBono  + 
							$incVacacion + 
							$incAguinaldo + 
							$incOtnoct + 
							$incOtdia + 
							$incHnoct + 
							$incSalary + 
							$incAddHoras;

			$sqlText = "select round(ifnull(sum(amount),0),2) deducciones from paystub_legaldisc where payxemp_id = ".$dtEmp['0']['payxemp_id'];

			$dtDeduc = $dbEx->selSql($sqlText);
			$totalDeductions = $dtDeduc['0']['deducciones'];

			//Identificar deducciones configuradas
			$sqlText = "select ld.legaldisc_name, round(pld.amount,2) amount ".
						"from legal_discount ld inner join paystub_legaldisc pld ".
						"on ld.legaldisc_id = pld.legaldisc_id ".
						"where pld.payxemp_id = ".$dtEmp['0']['payxemp_id'];
			$dtDeduc = $dbEx->selSql($sqlText);


			$totalDiscounts = $dtEmp['0']['DESCUENTOS'] + 
							$dtEmp['0']['desc_h'] +
							$dtEmp['0']['seventh'] + 
							$incSalaryDisc +
							$incSeventh;


			//Verifica el estado del pago
			$estado = "";
			if($dtEmp['0']['payxemp_status']=='A'){
				$estado = '<u><font color="#006666"> Aceptado por agente</font></u>';
			}
			else if($dtEmp['0']['payxemp_status']=='P'){
				$estado = '<u><font color="#BC6D03"> Pendiente de aceptaci&oacute;n por agente</font></u>';
			}
			else if($dtEmp['0']['payxemp_status']=='R'){
				$estado = '<u><font color="#8A0000">Rechazado por agente</u></font>';
			}


   			$rslt .= '<tr><td>BADGE: '.$dtEmp['0']['username'].'</td></tr>';
			$rslt .='<tr><td>He recibido de Skycom Call Center el monto de: $'.number_format(($dtEmp['0']['liquid'] + $incRecibir),2).'</td></tr>';
			$rslt .='<tr><td>En concepto de salario para el per&iacute;odo del: '.$dtEmp['0']['inicio_per'].' al '.$dtEmp['0']['fin_per'].'</td></tr>';
			$rslt .='<tr><td>Estado de la boleta de pago: '.$estado.'</td>';
			
			//Imprimir paystub
            $rslt .='<td align="center">'.$btn.
            		'<a href="report/impPaystub.php?eid='.$dtEmp['0']['employee_id'].
					'&pid='.$dtEmp['0']['paystub_id'].'". target="_blank"><img src="images/print.png" border="0" width="50" style="cursor:pointer;" title="Print" align="absmiddle" /></a></td></tr>';

			
			$rslt .='<tr><td><table border="1" cellpadding="4" cellspacing="4" class="tablaVerde" width="400">';
			$rslt .= '<tr><td></td><td class="showItem">Horas</td>'.
					'<td class="showItem">Ingresos</td>';
			$rslt .='<tr><td>Horas programadas</td>'.
            		'<td>'.number_format($horasProgram,2).'</td><td></td></tr>';
			$rslt .='<tr><td>Salario base</td>'.
					'<td>'.number_format(($dtEmp['0']['nhoras'] + $incNhoras),2).'</td>'.
					'<td align="right">$'.number_format(($dtEmp['0']['salary'] + $incSalary),2).'</td></tr>';
			$rslt .='<tr><td>Horas adicionales</td>'.
					'<td>'.number_format(($dtEmp['0']['naddHours'] + $incNaddHoras),2).'</td>'.
					'<td align="right">$'.number_format(($dtEmp['0']['addHours'] + $incAddHoras),2).'</td></tr>';
			$rslt .='<tr><td>Horas nocturnas</td>'.
					'<td>'.number_format(($dtEmp['0']['nhours_noc'] + $incNhnoct),2).'</td>'.
					'<td align="right">$'.number_format(($dtEmp['0']['hours_noct'] + $incHnoct),2).'</td></tr>';
			$rslt .='<tr><td>Horas extras diurnas</td>'.
					'<td>'.number_format(($dtEmp['0']['nhoursext_di'] + $incNotdia),2).'</td>'.
					'<td align="right">$'.number_format(($dtEmp['0']['hoursext_di']+ $incOtdia),2).'</td></tr>';
			$rslt .='<tr><td>Horas extras nocturnas</td>'.
					'<td>'.number_format(($dtEmp['0']['nhoursext_noct'] + $incNotnoct),2).'</td>'.
					'<td align="right">$'.number_format(($dtEmp['0']['hoursext_noct'] + $incOtnoct),2).'</td></tr>';
			$rslt .='<tr><td>Bonificaci&oacute;n</td>'.
					'<td></td><td align="right">$'.number_format(($dtEmp['0']['bono'] + $incBono),2).'</td></tr>';
			$rslt .='<tr><td>Vacaci&oacute;n</td>'.
					'<td></td><td align="right">$'.number_format(($dtEmp['0']['vacation'] + $incVacacion),2).'</td>'.
					'</tr>';
			$rslt .='<tr><td>Aguinaldo</td>'.
					'<td></td><td align="right">$'.number_format(($dtEmp['0']['aguinaldo'] + $incAguinaldo),2).'</td></tr>';

			$rslt .='<tr><td>Otros ingresos</td>'.
					'<td></td><td align="right">$'.number_format(($dtEmp['0']['other_income'] + $incOtherIncome),2).'</td></tr>';
            
            $rslt .='<tr><td>Indemnizaci&oacute;n</td><td></td>'.
					'<td align="right">$'.number_format(($dtEmp['0']['indemnizacion'] + $incSeverance),2).'</td></tr>';
			$rslt .= '<tr><td>TOTAL DE INGRESOS</td><td></td>'.
					'<td align="right">$'.number_format($totalIncome,2).'</td></tr>'; 
			$rslt .='</table></td>';


			$rslt .='<td valign="top"><table cellpadding="4" cellspacing="4" class="tablaVerde" width="400" border="1">';
			$rslt .='<tr><td class="showItem" colspan="2">Deducciones y Descuentos</td></tr>';

				//Recorrer deducciones
				foreach ($dtDeduc as $dtD) {
					$rslt .= '<tr><td>'.$dtD['legaldisc_name'].'</td><td align="right">$'.$dtD['amount'].'</td></tr>';
				}

			$rslt .= '<tr><td>TOTAL DEDUCCIONES</td>'.
					'<td align="right">$'.$totalDeductions.'</td></tr>';
			$rslt .= '<tr><td colspan="2"><br></td></tr>';


				//Obtener descuentos de acuerdo a configuracion
				$sqlText = "select ps.disc_id, ps.disc_label, pa.disc_attributename ".
					"from pay_discount_setup ps, pay_discount_attr pa ".
					"where ps.disc_attributeid = pa.disc_attributeid ".
					"and STR_TO_DATE('".$dtEmp['0']['fec_delivery']."', '%d/%m/%Y') between date(disc_start_date) and ifnull(date(ps.disc_end_date),sysdate() + 1) ".
	    			"order by ps.disc_label ";

            	$dtDesc = $dbEx->selSql($sqlText);
            	$totalDescuentos = 0;

            	if($dbEx->numrows>0){
					foreach($dtDesc as $dtD){

						$sqlLabel = "select format(ifnull(".$dtD['disc_attributename'].",0),2) attribute, '".
							$dtD['disc_label']."' label, ".
					    	" format(((ifnull(attribute1,0)) + ".
							" ifnull((select ".$dtD['disc_attributename'].
								" from paystub_incidents ".
								" where payxemp_id=".$dtEmp['0']['payxemp_id']."),0)),2) total_attr ".
							" from paystubxemp where payxemp_id=".$dtEmp['0']['payxemp_id'];

						$dtLabel = $dbEx->selSql($sqlLabel);
						if($dbEx->numrows>0){
						    $rslt .= '<tr><td>'.$dtLabel['0']['label'].'</td>'.
									'<td align="right">$'.$dtLabel['0']['total_attr'].'</td></tr>';

	         				$totalDescuentos = $totalDescuentos + $dtLabel['0']['total_attr'];
	     				}

					}
				}
				$rslt .= '<tr><td>TOTAL DE DESCUENTOS</td>'.
									'<td align="right">$'.number_format($totalDescuentos,2).'</td></tr>';

				$rslt .= '<tr><td colspan="2"><br></td></tr>';
				$rslt .='<tr><td>Descuentos salariales</td>'.
					'<td align="right"><font color="#990000">$'.number_format(($dtEmp['0']['desc_h'] + $incSalaryDisc),2).'</font></td>';
            
				$rslt .='<tr><td>Descuento d&iacute;a s&eacute;ptimo</td>'.
						'<td align="right"><font color="#990000">$'.number_format(($dtEmp['0']['seventh'] + $incSeventh),2).'</font></td>';				

			$rslt .='</table></td></tr>';
			$rslt .='<tr class="showItem">'.
					'<td>PAGO A RECIBIR '.
					'$'.number_format(($dtEmp['0']['liquid'] + $incRecibir),2).'</td>'.
					'<td></td></tr>';
			$rslt .='<tr><td>Fecha de entrega: '.$dtEmp['0']['fec_delivery'].'</td></tr>';

			//Mostrar botones si el empleado de la boleta es el usuario actual
			if($dtEmp['0']['employee_id'] == $_SESSION['usr_id'] ){
				//Mostrar boton para aceptar paystub
				$rslt .= '<tr><td><br></td></tr>';
				$btn = "<tr>";
				if($dtEmp['0']['payxemp_status']=='P' or $dtEmp['0']['payxemp_status']=='R'){
					$btn = '<td align="right"><img src="images/botonAceptar.png" alt="Aceptar pago" style="cursor: pointer" title="Click to accept payment" onclick="acceptPaystub('.$dtEmp['0']['payxemp_id'].')" width="160"></td>';
				}

				$rslt .=$btn.'<td><img src="images/LupaDinero.jpg" alt="check payment" style="cursor:pointer" title="check payment" width="100" onclick="chequearPaystub('.$dtEmp['0']['payxemp_id'].','.$dtEmp['0']['employee_id'].')"></td></tr>';
				$rslt .= '<tr><td colspan="2"><div id="lyIncidencias"></div></td></tr></table>';
			}

			//Buscar si hay tickets de incidencias para mostrarlos en pantalla
			$sqlText = "select payticket_id, payticket_comments, date_format(payticket_date,'%d/%m/%Y') as fecReg, payticket_status, payticket_authorizer, date_format(payticket_dateauthor,'%d/%m/%Y') as fecAutor from paystub_tickets where payxemp_id=".$dtEmp['0']['payxemp_id'];
			$dtTicket = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$estado = "";
				if($dtTicket['0']['payticket_status']=='P'){
					$estado = '<font color="#CC6600"><b>In Progress</b></font>';
				}
				else if($dtTicket['0']['payticket_status']=='A'){
					$estado = '<font color="#003333"><b>Approved</b></font>';
				}
				else if($dtTicket['0']['payticket_status']=='R'){
					$estado = '<font color="#800000"><b>Rejected</b></font>';
				}
				$autor = '';
				if($dtTicket['0']['payticket_authorizer']>0){
					$sqlText ="select firstname, lastname from employees where employee_id=".$dtTicket['0']['payticket_authorizer'];
					$dtAutor = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$autor = $dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'];
					}
				}
				$rslt .='<tr><td colspan="5"><table class="backTablaMain" width="500" align="center" cellpadding="1" cellspacing="1">
				<tr><td class="backList">Ticket incidence of payment number: '.$dtTicket['0']['payticket_id'].'</td></tr>
				<tr><td>Record date: '.$dtTicket['0']['fecReg'].'</td></tr>
				<tr><td>Comments: '.$dtTicket['0']['payticket_comments'].'</td></tr>
				<tr><td>Status: '.$estado.'</td></tr>
				<tr><td>Authorizer: '.$autor.'</td></tr>
				</table></td></tr>';

			}


		}
		else{
			$rslt .='<tr><td colspan="5">Boleta de pago no disponible</td></tr>';
		}
		$rslt .='</table>';

		return $rslt;

	}

	function getSalarioxHora($employee_id,
			$fechaIni,
			$fechaFin){

		$dbEx = new DBX;

		$salario = 0;
		$salarioEmp = 0;
		$sqlText = "select salary from employees where employee_id=".$employee_id;
		$dtsalario = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$salarioEmp = $dtsalario['0']['salary'];
		}

		//El salario se dividira entre el total de horas programadas, si no tiene se pone por defecto 176
		$sqlText = "select round((((SUM(TIME_TO_SEC(sch_departure))) - (SUM(TIME_TO_SEC(sch_entry)))) - ".
			"((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) horas_prog ".
			"from schedules ".
			"where employee_id = ".$employee_id." ".
			"and sch_date between date '".$fechaIni."' and '".$fechaFin."'";
		
		$dtPr = $dbEx->selSql($sqlText);
		$horasProgram = 176;
		if($dbEx->numrows>0){
	        if ($dtPr['0']['horas_prog'] <> ""){
	        		//Horas programadas se multiplican por 2 para sacar el valor de hora por mes, ya que la sumatoria de horas es de una quincena y el salio es base a mes
	        		//Validar que pasa con el cargue de horas programadas cuando es vacacion
	                $horasProgram = $dtPr['0']['horas_prog'] * 2;
			}
		}
		
		$salario = ($salarioEmp/$horasProgram);
		return $salario;
		
	}

}
?>
