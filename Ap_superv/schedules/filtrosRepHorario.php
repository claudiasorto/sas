<table cellpadding="2" cellspacing="0" width="700" border="0" class="tblListBack" align="center">
<tr><td class="backTablaForm" colspan="2" align="center">Filters for schedules</td></tr>
<tr><td class="itemForm">Account: </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td class="itemForm">Department: </td><td><select id="lsDepart" class="txtPag"><!--optDepart--></select></td></tr>
<tr><td class="itemForm">Position: </td><td><select id="lsPosicion" class="txtPag"><!--optPosicion--></select></td></tr>
<tr><td class="itemForm">Immediate boss: </td><td><select id="lsJefe" class="txtPag"><!--optJefe--></select></td></tr>
<tr><td class="itemForm">Employee: </td><td><select id="lsEmp" class="txtPag"><!--optEmp--></select></td></tr>
<tr><td class="itemForm">Employee name: </td><td><input type="text" class="txtPag" size="50" id="txtNombre" /></td></tr>
<tr><td class="itemForm">Badge: </td><td><input type="text" class="txtPag" id="txtBadge"/></td></tr>
<tr><td class="itemForm">Period of: </td><td class="txtPag">
<input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp; to the 
<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Seach" onClick="loadRepHorario()"></td></tr>
</table><br><br>
<div id="lyData"></div>
