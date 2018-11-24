<table width="725" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">
<tr><td colspan="2" align="center" class="txtForm">REGISTRATION ABSENTEEISM</td></tr>
<tr><td align="right">Date: </td><td><input type="text" id="fecha" name="fecha" class="txtPag" readonly="1" size="35"><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right">Employee: </td><td><select id="lsEmp" class="txtPag"><option value="0">Select a agent</option><!--optEmp--></select></td></tr>
<tr><td colspan="2" align="center"><input type="button" value="Load form" class="btn" onClick="consultAbsent()"></td></tr>
</table>
<br>
<div id="lyForm"></div>
