<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Paystub</title>
<style type="text/css">
 BODY{
  font-family:Tahoma;
  font-size:12px;
 }
</style>
</head>
<body topmargin="0" leftmargin="0" onload="window.print() ">
<table width="800" bordercolor="#999999" align="center" cellpadding="1" cellspacing="3" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">
<tr><td align="right" colspan="5"><img src="../images/LogoSkycom.png" alt="exptel" width="200"/></td></tr>
<tr><td colspan="3" align="center"><b>Paystub of employee: <?php echo $_GET['badge']." ".$_GET['nombre'];?></b></td></tr>
<tr><td colspan="3" align="center"><b>In the period: <?php echo $_GET['fec_ini']." - ".$_GET['fec_fin'];?></b></td></tr>
<td width="25%">
<table width="200" align="center"  border="1" cellpadding="0" cellspacing="0">
<tr><td align="center"><b>Data</b></td></tr>
<tr><td>Total hours</td></tr>
<tr><td>Salary</td></tr>
<tr><td># Additional hours</td></tr>
<tr><td>$ Additional hours</td></tr>
<tr><td>$ Salary discounts</td></tr>
<tr><td>$ Seventh day</td></tr>
<tr><td># Night hours</td></tr>
<tr><td>$ Nigth hours</td></tr>
<tr><td># Day overtime</td></tr>
<tr><td>$ Day overtime</td></tr>
<tr><td># Night overtime</td></tr>
<tr><td>$ Night overtime</td></tr>
<tr><td>Bonus</td></tr>
<tr><td>Aguinaldo</td></tr>
<tr><td>Vacation</td></tr>
<tr><td>Severance</td></tr>
<tr><td>Other Income</td></tr>
<tr><td>Total income</td></tr>
<tr><td>ISR</td></tr>
<tr><td>ISSS</td></tr>
<tr><td>AFP</td></tr>
<tr><td>Total deductions</td></tr>
<tr><td>EMI</td></tr>
<tr><td>Cheff factory</td></tr>
<tr><td>Cafeteria</td></tr>
<tr><td>Damaged Equipment</td></tr>
<tr><td>Other Discounts</td></tr>
<tr><td>Loans</td></tr>
<tr><td>Advances</td></tr>
<tr><td>Refunds</td></tr>
<tr><td>Total discounts</td></tr>
<tr><td>Payment to received</td></tr>
</table></td>
<td width="25%"><table width="200" align="center" border="1" cellpadding="0" cellspacing="0">
<tr><td align="center"><b>Paystub</b></td></tr>
<tr><td><?php echo $_GET['payxemp_nhoras']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_salary']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_nadditionalhours']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_additionalhours']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_salarydisc']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_seventh']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_nhorasnoct']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_horasnoct']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_notdiurnal']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_otdiurnal']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_notnoct']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_otnoct']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_bono']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_aguinaldo']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_vacation']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_severance']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_otherincome']; ?></td></tr>
<tr><td><?php echo $_GET['totalIngresos']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_isr']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_isss']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_afp']; ?></td></tr>
<tr><td><?php echo $_GET['totalDeducciones']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_emi']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_cheff']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_cafeteria']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_damagedequip']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_otherdesc']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_loans']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_advances']; ?></td></tr>
<tr><td><?php echo $_GET['reintegros']; ?></td></tr>
<tr><td><?php echo $_GET['totalDescuentos']; ?></td></tr>
<tr><td><?php echo $_GET['payxemp_liquid']; ?></td></tr>
</table></td>
<td width="50%">
<table width="400" align="center" border="1" cellpadding="0" cellspacing="0">
<tr><td align="center"><b>incidences</b></td><td colspan="2" align="center"><b>Paystub with Variations</b></td></tr>
<tr><td><?php echo $_GET['inc_nhoras']; ?></td><td><?php echo $_GET['total_nhoras']; ?></td></tr>
<tr><td><?php echo $_GET['inc_salary']; ?></td><td><?php echo $_GET['total_salary']; ?></td></tr>
<tr><td><?php echo $_GET['inc_naddhoras']; ?></td><td><?php echo $_GET['total_naddhoras']; ?></td></tr>
<tr><td><?php echo $_GET['inc_addhoras']; ?></td><td><?php echo $_GET['total_addhoras']; ?></td></tr>
<tr><td><?php echo $_GET['inc_salarydisc']; ?></td><td><?php echo $_GET['total_salarydisc']; ?></td></tr>
<tr><td><?php echo $_GET['inc_seventh']; ?></td><td><?php echo $_GET['total_seventh']; ?></td></tr>
<tr><td><?php echo $_GET['inc_nhnoct']; ?></td><td><?php echo $_GET['total_nhorasnoct']; ?></td></tr>
<tr><td><?php echo $_GET['inc_hnoct']; ?></td><td><?php echo $_GET['total_horasnoct']; ?></td></tr>
<tr><td><?php echo $_GET['inc_notdia']; ?></td><td><?php echo $_GET['total_notdiurnal']; ?></td></tr>
<tr><td><?php echo $_GET['inc_otdia']; ?></td><td><?php echo $_GET['total_otdiurnal']; ?></td></tr>
<tr><td><?php echo $_GET['inc_notnoct']; ?></td><td><?php echo $_GET['total_notnoct']; ?></td></tr>
<tr><td><?php echo $_GET['inc_otnoct']; ?></td><td><?php echo $_GET['total_otnoct']; ?></td></tr>
<tr><td><?php echo $_GET['inc_bono']; ?></td><td><?php echo $_GET['total_bono']; ?></td></tr>
<tr><td><?php echo $_GET['inc_aguinaldo']; ?></td><td><?php echo $_GET['total_aguinaldo']; ?></td></tr>
<tr><td><?php echo $_GET['inc_vacacion']; ?></td><td><?php echo $_GET['total_vacation']; ?></td></tr>
<tr><td><?php echo $_GET['inc_severance']; ?></td><td><?php echo $_GET['total_severance']; ?></td></tr>
<tr><td><?php echo $_GET['inc_otherincome']; ?></td><td><?php echo $_GET['total_otherincome']; ?></td></tr>
<tr><td><?php echo $_GET['inc_totalingresos']; ?></td><td><?php echo $_GET['total_totalingresos']; ?></td></tr>
<tr><td><?php echo $_GET['inc_isr']; ?></td><td><?php echo $_GET['total_isr']; ?></td></tr>
<tr><td><?php echo $_GET['inc_isss']; ?></td><td><?php echo $_GET['total_isss']; ?></td></tr>
<tr><td><?php echo $_GET['inc_afp']; ?></td><td><?php echo $_GET['total_afp']; ?></td></tr>
<tr><td><?php echo $_GET['inc_totaldeducciones']; ?></td><td><?php echo $_GET['total_totaldeducciones']; ?></td></tr>
<tr><td><?php echo $_GET['inc_emi']; ?></td><td><?php echo $_GET['total_emi']; ?></td></tr>
<tr><td><?php echo $_GET['inc_cheff']; ?></td><td><?php echo $_GET['total_cheff']; ?></td></tr>
<tr><td><?php echo $_GET['inc_cafe']; ?></td><td><?php echo $_GET['total_cafeteria']; ?></td></tr>
<tr><td><?php echo $_GET['inc_equipo']; ?></td><td><?php echo $_GET['total_equipo']; ?></td></tr>
<tr><td><?php echo $_GET['inc_otherdesc']; ?></td><td><?php echo $_GET['total_otherdesc']; ?></td></tr>
<tr><td><?php echo $_GET['inc_loans']; ?></td><td><?php echo $_GET['total_loans']; ?></td></tr>
<tr><td><?php echo $_GET['inc_adelantos']; ?></td><td><?php echo $_GET['total_advances']; ?></td></tr>
<tr><td><?php echo $_GET['inc_reintegros']; ?></td><td><?php echo $_GET['total_reintegros']; ?></td></tr>
<tr><td><?php echo $_GET['inc_totalDescuentos']; ?></td><td><?php echo $_GET['total_totaldescuentos']; ?></td></tr>
<tr><td><?php echo $_GET['inc_recibir']; ?></td><td><?php echo $_GET['total_recibir']; ?></td></tr>
</table>
</td></tr>

</table>

