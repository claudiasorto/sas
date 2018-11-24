<?php
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_paystub.xls");
  require_once("../db_funcs.php");
  $dbEx = new DBX;
	$sqlText = "select * from paystubxemp where employee_id=".$_POST['employee_id']." and paystub_id=".$_POST['paystub_id'];
	$dtPay = $dbEx->selSql($sqlText);
	
	$totalIncome = $dtPay['0']['PAYXEMP_OTHERINCOME'] + $dtPay['0']['PAYXEMP_BONO'] + $dtPay['0']['PAYXEMP_VACATION'] + $dtPay['0']['PAYXEMP_AGUINALDO'] + $dtPay['0']['PAYXEMP_OTNOCT'] + $dtPay['0']['PAYXEMP_OTDIURNAL'] + $dtPay['0']['PAYXEMP_HORASNOCT'] + $dtPay['0']['PAYXEMP_SALARY'] + $dtPay['0']['PAYXEMP_ADDITIONALHOURS'] - $dtPay['0']['PAYXEMP_SALARYDISC'] - $dtPay['0']['PAYXEMP_SEVENTH'];
				
	$totalDeductions = $dtPay['0']['PAYXEMP_ISR'] + $dtPay['0']['PAYXEMP_ISSS'] + $dtPay['0']['PAYXEMP_AFP'];

    $sqlText = "select ps.disc_id, ps.disc_label, pa.disc_attributename ".
			"from pay_discount_setup ps, pay_discount_attr pa ".
			"where ps.disc_attributeid = pa.disc_attributeid ".
			"and ifnull(ps.disc_end_date,sysdate() + 1) > sysdate() ".
    		"order by ps.disc_label ";

	$dtDesc = $dbEx->selSql($sqlText);
	$incTotalDescuentos = 0;
	$totalDiscounts = 0;
	
	foreach($dtDesc as $dtD){
                    $sqlIncLabel = "select ifnull(".$dtD['disc_attributename'].",0.0) attribute from paystub_incidents where payxemp_id=".$dtPay['0']['PAYXEMP_ID'];
                    $dtLbInc = $dbEx->selSql($sqlIncLabel);
                    $attrInc = 0;
                    if($dbEx->numrows>0){
						$attrInc = $dtLbInc['0']['attribute'];
						$incTotalDescuentos = $incTotalDescuentos + $attrInc;
					}
					$attrInc = number_format($attrInc,2);

					$sqlLabel = "select format(ifnull(".$dtD['disc_attributename'].",0),2) attribute, '".$dtD['disc_label']."' label, ".
					    " format(((ifnull(attribute1,0)) + ".
						" ifnull((select ".$dtD['disc_attributename']." from paystub_incidents where payxemp_id=".$dtPay['0']['PAYXEMP_ID']."),0)),2) total_attr ".
						"from paystubxemp where employee_id=".$dtPay['0']['EMPLOYEE_ID']." and paystub_id=".$dtPay['0']['PAYSTUB_ID'];

					$dtLabel = $dbEx->selSql($sqlLabel);
					if($dbEx->numrows>0){
					    $totalDiscounts = $totalDiscounts + $dtLabel['0']['attribute'];

						$tblDisc .= '<tr><td>'.$dtLabel['0']['label'].'</td><td>'.$dtLabel['0']['attribute'].'</td>'.
							'<td>'.$attrInc.'</td><td>'.$dtLabel['0']['total_attr'].'</td></tr>';
					}

				}


	$sqlText = "select * from paystub_incidents where payxemp_id=".$dtPay['0']['PAYXEMP_ID'];
			$dtInc = $dbEx->selSql($sqlText);
			$incId = 0;
			$incNhoras = 0;
			$incSalary = 0;
			$incNaddHoras = 0;
			$incAddHoras = 0;
			$incSalaryDisc = 0;
			$incSeventh = 0;
			$incNhnoct = 0;
			$incHnoct = 0;
			$incNotdia = 0;
			$incOtdia = 0;
			$incNotnoct = 0;
			$incOtnoct = 0;
			$incBono = 0;
			$incAguinaldo = 0;
			$incVacacion = 0;
			$incSeverance = 0;
			$incOtherIncome = 0;
			$incIsr  = 0;
			$incIsss = 0;
			$incAfp = 0;
			$incRecibir = 0;
			$incTotalIngresos = 0;
			$incTotalDeducciones = 0;
			
			
			if($dbEx->numrows>0){
				$incId = $dtInc['0']['PAYINC_ID'];
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
				$incIsr  = $dtInc['0']['PAYINC_ISR'];
				$incIsss = $dtInc['0']['PAYINC_ISSS'];
				$incAfp = $dtInc['0']['PAYINC_AFP'];
				$incRecibir = $dtInc['0']['PAYINC_RECEIVED'];
				$incTotalIngresos = $incOtherIncome + $incBono + $incVacacion + $incAguinaldo + $incOtnoct + $incOtdia + $incHnoct + $incSalary + $incAddHoras - $incSalaryDisc - $incSeventh;
				$incTotalDeducciones = $incIsr + $incIsss + $incAfp;
			
			}
?>
<table cellpadding="2" cellspacing="2" >
<tr><td colspan="3" align="center">Boleta de pago del empleado: <?php echo $_POST['nombre'];?></td></tr>
<tr><td colspan="3" align="center">En el periodo <?php echo $_POST['periodo'];?> </td></tr>
<tr><td colspan="4">
<table width="100%" align="center"  border="1" cellpadding="5" cellspacing="0">
<tr><td>Info</td><td>Valor</td><td>Incidente</td><td>Valor con variaciones</td></tr>
<tr><td>Horas totales</td><td><?php echo number_format($dtPay['0']['PAYXEMP_NHORAS'],2); ?></td>
<td><?php echo $incNhoras;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_NHORAS'] + $incNhoras,2);?></td></tr>
<tr><td>Salario base</td><td><?php echo number_format($dtPay['0']['PAYXEMP_SALARY'],2);?></td>
<td><?php echo $incSalary;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_SALARY'] + $incSalary,2);?></td></tr>
<tr><td># Horas adicionales</td><td><?php echo number_format($dtPay['0']['PAYXEMP_NADDITIONALHOURS'],2);?></td>
<td><?php echo $incNaddHoras;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_NADDITIONALHOURS'] + $incNaddHoras,2);?></td></tr>
<tr><td>$ Horas adicionales</td><td><?php echo number_format($dtPay['0']['PAYXEMP_ADDITIONALHOURS'],2);?></td>
<td><?php echo $incAddHoras;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_ADDITIONALHOURS'] + $incAddHoras,2);?></td></tr>
<tr><td>$ Descuentos salariales</td><td><?php echo number_format($dtPay['0']['PAYXEMP_SALARYDISC'],2);?></td>
<td><?php echo $incSalaryDisc;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_SALARYDISC'] + $incSalaryDisc,2);?></td></tr>
<tr><td>$ S&eacute;ptimo</td><td><?php echo number_format($dtPay['0']['PAYXEMP_SEVENTH'],2);?></td>
<td><?php echo $incSeventh;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_SEVENTH'] + $incSeventh,2);?></td></tr>
<tr><td># Horas nocturnas</td><td><?php echo number_format($dtPay['0']['PAYXEMP_NHORASNOCT'],2);?></td>
<td><?php echo $incNhnoct;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_NHORASNOCT'] + $incNhnoct,2);?></td></tr>
<tr><td>$ Horas nocturnas</td><td><?php echo number_format($dtPay['0']['PAYXEMP_HORASNOCT'],2);?></td>
<td><?php echo $incHnoct;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_HORASNOCT'] + $incHnoct,2);?></td></tr>
<tr><td># Extras diurnas</td><td><?php echo number_format($dtPay['0']['PAYXEMP_NOTDIURNAL'],2);?></td>
<td><?php echo $incNotdia;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_NOTDIURNAL'] + $incNotdia,2);?></td></tr>
<tr><td>$ Extras diurnas</td><td><?php echo number_format($dtPay['0']['PAYXEMP_OTDIURNAL'],2);?></td>
<td><?php echo $incOtdia;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_OTDIURNAL'] + $incOtdia,2);?></td></tr>
<tr><td># Extras nocturnas</td><td><?php echo number_format($dtPay['0']['PAYXEMP_NOTNOCT'],2);?></td>
<td><?php echo $incNotnoct;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_NOTNOCT'] + $incNotnoct,2);?></td></tr>
<tr><td>$ Extras nocturnas</td><td><?php echo number_format($dtPay['0']['PAYXEMP_OTNOCT'],2);?></td>
<td><?php echo $incOtnoct;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_OTNOCT'] + $incOtnoct,2);?></td></tr>
<tr><td>Bono</td><td><?php echo number_format($dtPay['0']['PAYXEMP_BONO'],2);?></td>
<td><?php echo $incBono;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_BONO'] + $incBono,2);?></td></tr>
<tr><td>Aguinaldo</td><td><?php echo number_format($dtPay['0']['PAYXEMP_AGUINALDO'],2);?></td>
<td><?php echo $incAguinaldo;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_AGUINALDO'] + $incAguinaldo,2);?></td></tr>
<tr><td>Vacaci&oacute;n</td><td><?php echo number_format($dtPay['0']['PAYXEMP_VACATION'],2);?></td>
<td><?php echo $incVacacion;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_VACATION'] + $incVacacion,2);?></td></tr>
<tr><td>Otros ingresos</td><td><?php echo number_format($dtPay['0']['PAYXEMP_OTHERINCOME'],2);?></td>
<td><?php echo $incOtherIncome;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_OTHERINCOME'] + $incOtherIncome,2);?></td></tr>
<tr><td>Total de ingresos</td><td><?php echo number_format($totalIncome,2);?></td>
<td><?php echo $incTotalIngresos;?></td><td><?php echo number_format($totalIncome + $incTotalIngresos,2);?></td></tr>
<tr><td>ISR</td><td><?php echo number_format($dtPay['0']['PAYXEMP_ISR'],2);?></td>
<td><?php echo $incIsr;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_ISR'] + $incIsr,2);?></td></tr>
<tr><td>ISSS</td><td><?php echo number_format($dtPay['0']['PAYXEMP_ISSS'],2);?></td>
<td><?php echo $incIsss;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_ISSS'] + $incIsss,2);?></td></tr>
<tr><td>AFP</td><td><?php echo number_format($dtPay['0']['PAYXEMP_AFP'],2);?></td>
<td><?php echo $incAfp;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_AFP'] + $incAfp,2);?></td></tr>
<tr><td>Total deducciones</td><td><?php echo number_format($totalDeductions,2);?></td>
<td><?php echo $incTotalDeducciones;?></td><td><?php echo number_format($totalDeductions + $incTotalDeducciones,2);?></td></tr>
<?php echo $tblDisc; ?>
<tr><td>Total descuentos</td><td><?php echo number_format($totalDiscounts,2);?></td>
<td><?php echo number_format($incTotalDescuentos,2);?></td><td><?php echo number_format(($incTotalDescuentos + $totalDiscounts),2);?></td></tr>
<tr><td>Indemnizaci&oacute;n</td><td><?php echo number_format($dtPay['0']['PAYXEMP_SEVERANCE'],2);?></td>
<td><?php echo $incSeverance;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_SEVERANCE'] + $incSeverance,2);?></td></tr>
<tr><td>Pago a recibir</td><td><?php echo number_format($dtPay['0']['PAYXEMP_LIQUID'],2);?></td>
 <td><?php echo $incRecibir;?></td><td><?php echo number_format($dtPay['0']['PAYXEMP_LIQUID'] + $incRecibir,2);?></td></tr>
</table>
</td></tr>
</table>
