<table class="backTablaMain" bordercolor="#069" width="700" align="center">
<tr><td class="txtForm" colspan="2" align="center"><b>RESET PAYROLL</b></td></tr>
<tr><td class="itemForm">Payroll period from:&nbsp;</td><td class="txtPag"><input type="text" id="fec_ini" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;&nbsp;to:&nbsp;<input type="text" id="fec_fin" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td colspan="4" class="txtForm" align="center"><input type="button" class="btn" value="Reset payroll" onClick="resetPayroll()"></td></tr>
</table>
