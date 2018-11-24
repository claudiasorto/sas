<table width="700" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">
<tr><td class="txtForm" colspan="2" align="center">System for recording daily hours</td></tr>
<tr><td class="itemForm" width="30%" align="right">Date: </td><td><input type="text" name="fecha" id="fecha" size="25" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" title="Choose the day to which records hours" /></td></tr>
<tr><td class="itemForm">Immediate boss: </td><td colspan="3"><select id="lsSuperv" class="txtPag"><option value="0">[ALL]</option><!--optSuperv--></select></td></tr>
<tr><td class="itemForm">Employee: </td><td colspan="3"><div id="lySuperv"><select id="lsEmp" class="txtPag"><option value="0">[ALL]</option><!--optEmployee--></select></div></td></tr>
<tr><td class="itemForm">Employee name: </td><td colspan="3"><input type="text" id="txtNombre" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Badge: </td><td colspan="3"><input type="text" id="txtUsername" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td class="txtForm" colspan="2" align="center"><input type="button" class="btn" value="Load payroll for this date" onClick="loadPayxEmpAll()" ></td></tr>

</table>
<br><br>
<div id="lyPayDiary"></div>