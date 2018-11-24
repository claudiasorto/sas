<table class="tblListBack" width="825" align="center">
<tr><td class="showItem" colspan="2"><b>FILTERS TO GENERATE PAYROLL REPORT</b></td></tr>
<tr><td class="itemForm">Payroll period from:&nbsp;</td><td class="txtPag"><input type="text" id="fec_ini" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;&nbsp;to:&nbsp;<input type="text" id="fec_fin" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Employee: </td><td colspan="3"><select id="lsEmp" class="txtPag"><option value="0">[TODOS]</option><!--optEmp--></select>
<tr><td class="itemForm">Employee name: </td><td colspan="3"><input type="text" id="txtNombre" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Badge: </td><td colspan="3"><input type="text" id="txtUsername" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td colspan="4" class="showItem" title="Click to generate the report"><input type="button" class="btn" value="Generate report" onClick="load_rptPayrollxSup()"></td></tr>
</table>
<br />
<div id="datos_rpt"></div>
