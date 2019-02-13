<?php
  $NUM = time();
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_paystub".$NUM.".xls");
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  $sqlText = "select date_format(paystub_ini,'%d/%m/%Y') as f1, date_format(paystub_fin, '%d/%m/%Y') as f2, date_format(paystub_delivery,'%d/%m/%Y') as f3 from paystub where paystub_id=".$_POST['idP'];
		$infoPaystub = $dbEx->selSql($sqlText);
  	$sqlText = "select p.*, e.*, ".
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
  			" from paystubxemp p inner join employees e on p.employee_id=e.employee_id where p.paystub_id=".$_POST['idP']." ".$_POST['filtro'];
	$dtPay = $dbEx->selSql($sqlText);
		
  	$sqlText = "select ps.disc_id, upper(ps.disc_label) disc_label, pa.disc_attributename ".
				"from pay_discount_setup ps, pay_discount_attr pa ".
				"where ps.disc_attributeid = pa.disc_attributeid ".
				"and STR_TO_DATE('".$infoPaystub['0']['f3']."', '%d/%m/%Y') between date(disc_start_date) and ifnull(date(ps.disc_end_date),sysdate() + 1) ".
    			"order by ps.disc_label";

   $dtDisc = $dbEx->selSql($sqlText);
   $tblLabel = '';
   if($dbEx->numrows>0){
		foreach($dtDisc as $dtD){
			$tblLabel .= '<td><font color="#FFFFFF">'.$dtD['disc_label'].'</font></td>';
		}
   }

   //Etiquetas de deducciones activas
   $sqlText = " select legaldisc_name from legal_discount ".
 			"where ifnull(end_date,date(sysdate())) <= date(sysdate()) ".
 			"order by legaldisc_name asc";

 	$dtDLabel = $dbEx->selSql($sqlText);
 	$tblDLabel = '';
 	if($dbEx->numrows>0){
 		foreach ($dtDLabel as $dtDl) {
 			$tblDLabel .= '<td><font color="#FFFFFF">'.$dtDl['legaldisc_name'].'</font></td>';
 		}
 	}

 ?>
 <table cellpadding="0" cellspacing="0" border="1" bordercolor="#003366">
 <tr bgcolor="#003366"><td colspan="7" align="center"><font color="#FFFFFF">PAYROLL FOR THE PERIOD <?php echo $infoPaystub['0']['f1']." - ".$infoPaystub['0']['f2'];?></font></td></tr>
 <tr bgcolor="#003366"><td><font color="#FFFFFF">BADGE</font></td>
 <td><font color="#FFFFFF">EMPLEADO</font></td>
 <td><font color="#FFFFFF">SUPERVISOR</font></td>
 <td><font color="#FFFFFF">HORAS PROGRAMADAS</font></td>
 <td><font color="#FFFFFF">HORAS TRABAJADAS</font></td>
 <td><font color="#FFFFFF">SALARIO</font></td>
 <td><font color="#FFFFFF">FERIADOS</font></td>
 <td><font color="#FFFFFF">$ FERIADOS</font></td>
 <td><font color="#FFFFFF">HORAS ADICIONALES</font></td>
 <td><font color="#FFFFFF">$ HORAS ADICIONALES</font></td>
 <td><font color="#FFFFFF">HORAS NOCTURNAS</font></td>
 <td><font color="#FFFFFF">$ HORAS NOCTURNAS</font></td>
 <td><font color="#FFFFFF">EXTRAS DIURNAS</font></td>
 <td><font color="#FFFFFF">$ EXTRAS DIURNAS</font></td>
 <td><font color="#FFFFFF">EXTRAS NOCTURNAS</font></td>
 <td><font color="#FFFFFF">$ EXTRAS NOCTURNAS</font></td>
 <td><font color="#FFFFFF">AGUINALDO</font></td>
 <td><font color="#FFFFFF">VACACIONES</font></td>
 <td><font color="#FFFFFF">BONOS</font></td>
 <td><font color="#FFFFFF">INDEMNIZACION</font></td>
 <td><font color="#FFFFFF">OTROS INGRESOS</font></td>
 <td><font color="#FFFFFF">TOTAL DE INGRESOS</font></td>
 <?php echo $tblDLabel; ?>
 <td><font color="#FFFFFF">TOTAL DEDUCCIONES</font></td>
 <?php echo $tblLabel; ?>
 <td><font color="#FFFFFF">TOTAL DESCUENTOS</font></td>
 <td><font color="#FFFFFF">DESCUENTOS SALARIALES</font></td>
 <td><font color="#FFFFFF">SEPTIMO</font></td>
 <td><font color="#FFFFFF">PAGO A RECIBIR</font></td>
 <td><font color="#FFFFFF">FECHA INICIAL</font></td>
 <td><font color="#FFFFFF">FECHA FINAL</font></td>
 <td><font color="#FFFFFF">FECHA DE PAGO</font></td>
 <td><font color="#FFFFFF">SALARIO BASE X MES</font></td>
 <td><font color="#FFFFFF">CUENTA DE BANCO</font></td></tr>
 <?php 
 foreach($dtPay as $dtP){
 
        //Calculo de horas programadas
        $sqlText = "select round((((SUM(TIME_TO_SEC(sch_departure))) - (SUM(TIME_TO_SEC(sch_entry)))) - ".
   			"((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) horas_prog ".
			"from schedules ".
			"where employee_id = ".$dtP['EMPLOYEE_ID']." ".
			"and sch_date between STR_TO_DATE('".$infoPaystub['0']['f1']."', '%d/%m/%Y') and STR_TO_DATE('".$infoPaystub['0']['f2']."', '%d/%m/%Y')";

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
		$incReintegros = 0;
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
		  "from paystub_incidents pi where payxemp_id=".$dtP['PAYXEMP_ID'];
		$dtInc = $dbEx->selSql($sqlText);

			if($dbEx->numrows>0){
				$incNhoras = $dtInc['0']['PAYINC_NHORAS'];
				$incSalary = $dtInc['0']['PAYINC_SALARY'];;
				$incNaddHoras = $dtInc['0']['PAYINC_NADDITIONALHOURS'];
				$incAddHoras = $dtInc['0']['PAYINC_ADDITIONALHOURS'];
				$incSalaryDisc = $dtInc['0']['PAYINC_SALARYDISC'];
				$incSeventh = $dtInc['0']['PAYINC_SEVENTH'];
				$incNhnoct = $dtInc['0']['PAYINC_NHORASNOCT'];
				$incHnoct =$dtInc['0']['PAYINC_HORASNOCT'];
				$incNotdia = $dtInc['0']['PAYINC_NOTDIURNAL'];
				$incOtdia = $dtInc['0']['PAYINC_OTDIURNAL'];
				$incNotnoct = $dtInc['0']['PAYINC_NOTNOCT'];
				$incOtnoct = $dtInc['0']['PAYINC_OTNOCT'];
				$incBono = $dtInc['0']['PAYINC_BONO'];
				$incAguinaldo = $dtInc['0']['PAYINC_AGUINALDO'];
				$incVacacion = $dtInc['0']['PAYINC_VACATION'];
				$incSeverance = $dtInc['0']['PAYINC_SEVERANCE'];
				$incOtherIncome = $dtInc['0']['PAYINC_OTHERINCOME'];
				$incRecibir = $dtInc['0']['PAYINC_RECEIVED'];
				$incTotalDescuentos = $dtInc['0']['DESCUENTOS'];
			}
			$incTotalIngresos = $incOtherIncome + $incBono + $incVacacion + $incAguinaldo + $incOtnoct + $incOtdia + $incHnoct + $incSalary + $incAddHoras + $incSeverance;
			
		
		$totalIncome = $dtP['PAYXEMP_OTHERINCOME'] + $dtP['PAYXEMP_BONO'] + $dtP['PAYXEMP_VACATION'] + $dtP['PAYXEMP_AGUINALDO'] + $dtP['PAYXEMP_OTNOCT'] + $dtP['PAYXEMP_OTDIURNAL'] + $dtP['PAYXEMP_HORASNOCT'] + $dtP['PAYXEMP_SALARY'] + $dtP['PAYXEMP_ADDITIONALHOURS'] + $dtP['PAYXEMP_SEVERANCE'] + $dtP['PAYXEMP_HOLIDAY'];

		//Deducciones
		$sqlText = "select round(ifnull(sum(amount),0),2) deducciones from paystub_legaldisc where payxemp_id = ".$dtP['PAYXEMP_ID'];

		$dtDeduc = $dbEx->selSql($sqlText);
		$totalDeductions = $dtDeduc['0']['deducciones'];

		
		$totalDiscounts = $dtP['DESCUENTOS'];

		$sqlText = "select firstname, lastname from employees where employee_id=".$dtP['ID_SUPERVISOR'];

		$dtSup = $dbEx->selSql($sqlText);
		$nombreSup = "";
		if($dbEx->numrows>0){
			$nombreSup = $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];	
		}
		//Valores de deducciones 
		$sqlText = "select round(ifnull((select pld.amount ".
			"from paystub_legaldisc pld ".
            "where pld.legaldisc_id = ld.legaldisc_id ".
            "and pld.payxemp_id = ".$dtP['PAYXEMP_ID']." ),0),2) amount ".
 			"from legal_discount ld ".
 			"where ifnull(end_date,date(sysdate())) <= date(sysdate()) ".
 			"order by legaldisc_name asc";
		$tblDeduc = '';
		$dtDeduc = $dbEx->selSql($sqlText);
		if($dbEx->numrows > 0){
			foreach ($dtDeduc as $dtD) {
				$tblDeduc .='<td>'.$dtD['amount'].'</td>';
			}
		}

		
		//Obtener valores de los attribute
		$tblDisc = '';

		foreach($dtDisc as $dtD){
			$sqlAttr = "select format(((ifnull(".$dtD['disc_attributename'].",0)) + ".
						" ifnull((select ".$dtD['disc_attributename']." from paystub_incidents where payxemp_id=".$dtP['PAYXEMP_ID']."),0)),2) total_attr ".
						" from paystubxemp where payxemp_id=".$dtP['PAYXEMP_ID'];
            $dtAttr = $dbEx->selSql($sqlAttr);
            $attr = 0;
            if($dbEx->numrows>0){
				$attr = $dtAttr['0']['total_attr'];
			}
			$attr = number_format($attr,2);
            $tblDisc .= '<td>'.$attr.'</td>';
		}
		

 ?>
		<tr><td><?php echo $dtP['USERNAME']; ?>
        </td><td><?php echo $dtP['FIRSTNAME'].' '.$dtP['LASTNAME']; ?></td>
        <td><?php echo $nombreSup; ?></td>
        <td><?php echo $horasProgram; ?></td>
        <td><?php echo number_format($dtP['PAYXEMP_NHORAS'] + $incNhoras,2); ?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_SALARY'] + $incSalary,2); ?></td>
        <td><?php echo number_format($dtP['PAYXEMP_NHOLIDAY'] + $incNaddHoras,2)?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_HOLIDAY'] + $incAddHoras,2)?></td>
        <td><?php echo number_format($dtP['PAYXEMP_NADDITIONALHOURS'] + $incNaddHoras,2)?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_ADDITIONALHOURS'] + $incAddHoras,2)?></td>
        <td><?php echo number_format($dtP['PAYXEMP_NHORASNOCT'] + $incNhnoct,2);?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_HORASNOCT'] + $incHnoct,2);?></td>
        <td><?php echo number_format($dtP['PAYXEMP_NOTDIURNAL'] + $incNotdia,2);?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_OTDIURNAL'] + $incOtdia,2);?></td>
        <td><?php echo number_format($dtP['PAYXEMP_NOTNOCT'] + $incNotnoct,2);?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_OTNOCT'] + $incOtnoct,2);?> </td>
        <td>$<?php echo number_format($dtP['PAYXEMP_AGUINALDO'] + $incAguinaldo,2);?> </td>
        <td>$<?php echo number_format($dtP['PAYXEMP_VACATION'] + $incVacacion,2);?> </td>
        <td>$<?php echo number_format($dtP['PAYXEMP_BONO'] + $incBono,2);?> </td>
        <td>$<?php echo number_format($dtP['PAYXEMP_SEVERANCE'] + $incSeverance,2);?> </td>
        <td>$<?php echo number_format($dtP['PAYXEMP_OTHERINCOME'] + $incOtherIncome,2);?> </td>
        <td>$<?php echo number_format($totalIncome + $incTotalIngresos,2); ?></td>
		<?php echo $tblDeduc; ?>        
        <td>$<?php echo number_format($totalDeductions,2); ?></td>
        <?php echo $tblDisc; ?>
        <td>$<?php echo number_format($totalDiscounts + $incTotalDescuentos,2); ?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_SALARYDISC'] + $incSalaryDisc,2)?></td>
        <td>$<?php echo number_format($dtP['PAYXEMP_SEVENTH'] + $incSeventh,2)?></td>        
        <td>$<?php echo number_format($dtP['PAYXEMP_LIQUID']+ $incRecibir,2); ?></td>
        <td><?php echo $infoPaystub['0']['f1'];?> </td>
        <td><?php echo $infoPaystub['0']['f2'];?> </td>
        <td><?php echo $infoPaystub['0']['f3'];?> </td>
        <td>$<?php echo number_format($dtP['SALARY'],2); ?></td>
        <td><?php echo $dtP['ACCOUNT_NUMBER'];?> </td>
        </tr>
        
				
<?php	
     }
 ?>
