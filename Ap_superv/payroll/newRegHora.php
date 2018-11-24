<table width="700" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">
<tr><td class="txtForm" colspan="2" align="center">System for recording daily hours</td></tr>
<tr><td class="txtForm" width="30%" align="right">Date: </td><td><input type="text" name="fecha" id="fecha" size="25" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" title="Choose the day to which records hours" /></td></tr>
<tr><td class="txtForm" colspan="2" align="center"><input type="button" class="btn" value="Load payroll for this date" onClick="loadPayxEmp()" ></td></tr>
</table>
<br><br>
<div id="lyPayDiary"></div>