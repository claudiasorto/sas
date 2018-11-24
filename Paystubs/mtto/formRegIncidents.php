<table width="800" align="center" bordercolor="#8FBC8F" >
<tr><td colspan="3" align="center"><b>Boleta de pago del empleado: <!--badge--> <!--nomb_emp--></b></td></tr>
<tr><td colspan="3" align="center"><b>En el periodo del: <!--fec_ini--> al <!--fec_fin--></b></td></tr>
<tr>
<tr><td colspan="2" align="right">
<a href="report/impIncidences.php?badge=<!--badge-->&nombre=<!--nomb_emp-->&fec_ini=<!--fec_ini--> &fec_fin=<!--fec_fin-->
&payxemp_nhoras=<!--payxemp_nhoras-->&payxemp_salary=<!--payxemp_salary-->&payxemp_nadditionalhours=<!--payxemp_nadditionalhours-->
&payxemp_additionalhours=<!--payxemp_additionalhours-->&payxemp_salarydisc=<!--payxemp_salarydisc-->&payxemp_seventh=<!--payxemp_seventh-->&payxemp_nhorasnoct=<!--payxemp_nhorasnoct-->&payxemp_horasnoct=<!--payxemp_horasnoct-->&payxemp_notdiurnal=<!--payxemp_notdiurnal-->&payxemp_otdiurnal=<!--payxemp_otdiurnal-->&payxemp_notnoct=<!--payxemp_notnoct-->&payxemp_otnoct=<!--payxemp_otnoct-->&payxemp_bono=<!--payxemp_bono-->&payxemp_aguinaldo=<!--payxemp_aguinaldo-->&payxemp_vacation=<!--payxemp_vacation-->&payxemp_severance=<!--payxemp_severance-->&payxemp_otherincome=<!--payxemp_otherincome-->&totalIngresos=<!--totalIngresos-->&payxemp_isr=<!--payxemp_isr-->&payxemp_isss=<!--payxemp_isss-->&payxemp_afp=<!--payxemp_afp-->&totalDeducciones=<!--totalDeducciones-->&payxemp_emi=<!--payxemp_emi-->&payxemp_cheff=<!--payxemp_cheff-->&payxemp_cafeteria=
<!--payxemp_cafeteria-->&payxemp_damagedequip=<!--payxemp_damagedequip-->&payxemp_otherdesc=<!--payxemp_otherdesc-->&payxemp_loans=<!--payxemp_loans-->&payxemp_advances=<!--payxemp_advances-->&totalDescuentos=<!--totalDescuentos-->&payxemp_liquid=<!--payxemp_liquid-->&inc_nhoras=<!--inc_nhoras-->&total_nhoras=<!--total_nhoras-->&inc_salary=<!--inc_salary-->&total_salary=<!--total_salary-->&inc_naddhoras=<!--inc_naddhoras-->&total_naddhoras=<!--total_naddhoras-->&inc_addhoras=<!--inc_addhoras-->&total_addhoras=<!--total_addhoras-->&inc_salarydisc=<!--inc_salarydisc-->&total_salarydisc=<!--total_salarydisc-->&inc_seventh=<!--inc_seventh-->&total_seventh=<!--total_seventh-->&inc_nhnoct=<!--inc_nhnoct-->&total_nhorasnoct=<!--total_nhorasnoct-->&inc_hnoct=<!--inc_hnoct-->&total_horasnoct=<!--total_horasnoct-->&inc_notdia=<!--inc_notdia-->&total_notdiurnal=<!--total_notdiurnal-->&inc_otdia=<!--inc_otdia-->&total_otdiurnal=<!--total_otdiurnal-->&inc_notnoct=<!--inc_notnoct-->&total_notnoct=<!--total_notnoct-->&inc_otnoct=<!--inc_otnoct-->&total_otnoct=<!--total_otnoct-->&inc_bono=<!--inc_bono-->&total_bono=<!--total_bono-->&inc_aguinaldo=<!--inc_aguinaldo-->&total_aguinaldo=<!--total_aguinaldo-->&inc_vacacion=<!--inc_vacacion-->&total_vacation=<!--total_vacation-->&inc_severance=<!--inc_severance-->&total_severance=<!--total_severance-->&inc_otherincome=<!--inc_otherincome-->&total_otherincome=<!--total_otherincome-->&inc_totalingresos=<!--inc_totalingresos-->&total_totalingresos=<!--total_totalingresos-->&inc_isr=<!--inc_isr-->&total_isr=<!--total_isr-->&inc_isss=<!--inc_isss-->&total_isss=<!--total_isss-->&inc_afp=<!--inc_afp-->&total_afp=<!--total_afp-->&inc_totaldeducciones=<!--inc_totaldeducciones-->&inc_emi=<!--inc_emi-->&total_emi=<!--total_emi-->&inc_cheff=<!--inc_cheff-->&total_cheff=<!--total_cheff-->&inc_cafe=<!--inc_cafe-->&total_cafeteria=<!--total_cafeteria-->&inc_equipo=<!--inc_equipo-->&total_equipo=<!--total_equipo-->&inc_otherdesc=<!--inc_otherdesc-->&total_otherdesc=<!--total_otherdesc-->&inc_loans=<!--inc_loans-->&total_loans=<!--total_loans-->&inc_adelantos=<!--inc_adelantos-->&total_advances=<!--total_advances-->&inc_totalDescuentos=<!--inc_totalDescuentos-->&inc_recibir=<!--inc_recibir-->&total_recibir=<!--total_recibir-->&total_totaldeducciones=<!--total_totaldeducciones-->&total_totaldescuentos=<!--total_totaldescuentos-->&reintegros=<!--payxemp_refunds-->&inc_reintegros=<!--inc_reintegros-->&total_reintegros=<!--total_reintegros-->" target="_blank"><img src="images/print.png" border="0" width="50" style="cursor:pointer;" title="Print" align="absmiddle" /></a>
</td>
<td><form target="_blank" action="report/xls_rptIncidences.php" method="post">
<input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Export to excel" />
<input type="hidden" name="payxemp_id" value="<!--payxemp_id-->" />
<input type="hidden" name="employee_id" value="<!--employee_id-->" />
<input type="hidden" name="paystub_id" value="<!--paystub_id-->">
<input type="hidden" name="nombre" value="<!--badge--> <!--nomb_emp-->">
<input type="hidden" name="periodo" value="<!--fec_ini--> - <!--fec_fin-->">
</form></td></tr>
<tr><td colspan="4">
<div id="lyVariaciones" style="display:block">
<table width="100%" align="center"  border="1" cellpadding="5" cellspacing="0">
<tr><td>Info</td><td>Valor</td><td>Incidente</td><td>Valor con variaciones</td></tr>
<tr><td>Horas totales</td><td><!--payxemp_nhoras--></td>
<td><input type="text" id="txtnhoras" value="<!--inc_nhoras-->"/></td><td><!--total_nhoras--></td></tr>
<tr><td>Salario base</td><td><!--payxemp_salary--></td>
<td><input type="text" id="txtsalary" disabled="disabled" value="<!--inc_salary-->"/></td><td><!--total_salary--></td></tr>
<tr><td># Horas adicionales</td><td><!--payxemp_nadditionalhours--></td>
<td><input type="text" id="txtnadditionalhours" value="<!--inc_naddhoras-->"/></td><td><!--total_naddhoras--></td></tr>
<tr><td>$ Horas adicionales</td><td><!--payxemp_additionalhours--></td>
<td><input type="text" id="txtadditionalhours" disabled="disabled" value="<!--inc_addhoras-->"/></td><td><!--total_addhoras--></td></tr>
<tr><td># Horas nocturnas</td><td><!--payxemp_nhorasnoct--></td>
<td><input type="text" id="txtnhorasnoct" value="<!--inc_nhnoct-->"/></td><td><!--total_nhorasnoct--></td></tr>
<tr><td>$ Horas nocturnas</td><td><!--payxemp_horasnoct--></td>
<td><input type="text" id="txthorasnoct" disabled="disabled" value="<!--inc_hnoct-->"/></td><td><!--total_horasnoct--></td></tr>
<tr><td># Extras diurnas</td><td><!--payxemp_notdiurnal--></td>
<td><input type="text" id="txtnotdiurnal" value="<!--inc_notdia-->"/></td><td><!--total_notdiurnal--></td></tr>
<tr><td>$ Extras diurnas</td><td><!--payxemp_otdiurnal--></td>
<td><input type="text" id="txtotdiurnal" disabled="disabled" value="<!--inc_otdia-->"/></td><td><!--total_otdiurnal--></td></tr>
<tr><td># Extras nocturnas</td><td><!--payxemp_notnoct--></td>
<td><input type="text" id="txtnotnoct" value="<!--inc_notnoct-->"/></td><td><!--total_notnoct--></td></tr>
<tr><td>$ Extras nocturnas</td><td><!--payxemp_otnoct--></td>
<td><input type="text" id="txtotnoct" disabled="disabled" value="<!--inc_otnoct-->"/></td><td><!--total_otnoct--></td></tr>
<tr><td>Bono</td><td><!--payxemp_bono--></td>
<td><input type="text" id="txtbono" value="<!--inc_bono-->"/></td><td><!--total_bono--></td></tr>
<tr><td>Aguinaldo</td><td><!--payxemp_aguinaldo--></td>
<td><input type="text" id="txtaguinaldo" value="<!--inc_aguinaldo-->"/></td><td><!--total_aguinaldo--></td></tr>
<tr><td>Vacaci&oacute;n</td><td><!--payxemp_vacation--></td>
<td><input type="text" id="txtvacation" value="<!--inc_vacacion-->"/></td><td><!--total_vacation--></td></tr>
<tr><td>Otros ingresos</td><td><!--payxemp_otherincome--></td>
<td><input type="text" id="txtotherincome" value="<!--inc_otherincome-->"/></td><td><!--total_otherincome--></td></tr>
<tr><td>Indemnizaci&oacute;n</td><td><!--payxemp_severance--></td>
<td><input type="text" id="txtseverance" value="<!--inc_severance-->"/></td><td><!--total_severance--></td></tr>
<tr><td>Total de ingresos</td><td><!--totalIngresos--></td>
<td><input type="text" id="txtTotalIncome" disabled="disabled" value="<!--inc_totalingresos-->"/></td><td><!--total_totalingresos--></td></tr>
<!--DescuentosLey-->
<tr><td>Total deducciones</td><td><!--totalDeducciones--></td>
<td></td><td><!--totalDeducciones--></td></tr>
<!--dataDescuentos-->
<tr><td>$ Descuentos salariales</td><td><!--payxemp_salarydisc--></td>
<td><input type="text" id="txtsalarydisc" value="<!--inc_salarydisc-->"/></td><td><!--total_salarydisc--></td></tr>
<tr><td>$ S&eacute;ptimo</td><td><!--payxemp_seventh--></td>
<td><input type="text" id="txtseventh" value="<!--inc_seventh-->"/></td><td><!--total_seventh--></td></tr>
<tr><td>Total descuentos</td><td><!--totalDescuentos--></td>
<td><input type="text" id="txtTotalDescuentos" disabled="disabled" value="<!--inc_totalDescuentos-->"/></td><td><!--total_totaldescuentos--></td></tr>
<tr><td>Pago a recibir</td><td><!--payxemp_liquid--></td>
<td><input type="text" id="txtliquid" disabled="disabled" value="<!--inc_recibir-->"/></td><td><!--total_recibir--></td></tr>
</table>
</div>
</td></tr>
<tr><td colspan="3" align="center"><input type="button" value="Calculate incidences" onclick="CalcIncidences()"/></td></tr>
<tr><td colspan="3" align="center">
<div id="lySave" style="display:none">
<input type="button" value="Save incidence" onclick="SaveIncidence()"/></div></td></tr>
<tr><td><input type="hidden" id="txtPayxemp_ID" value="<!--payxemp_id-->"/></td></tr>
<tr><td><input type="hidden" id="txtPayInc_ID" value="<!--inc_id-->"/></td></tr>
</table>
