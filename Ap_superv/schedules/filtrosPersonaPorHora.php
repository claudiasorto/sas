<table cellpadding="2" cellspacing="0" width="700" border="0" class="tblListBack" align="center">
<tr><td class="backTablaForm" colspan="2" align="center">Filters for schedules</td></tr>
<tr><td class="itemForm">Account: </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td class="itemForm">Department: </td><td><select id="lsDepart" class="txtPag"><!--optDepart--></select></td></tr>
<tr><td class="itemForm">Position: </td><td><select id="lsPosicion" class="txtPag"><!--optPosicion--></select></td></tr>
<tr><td class="itemForm">Employee: </td><td><select id="lsEmp" class="txtPag"><!--optEmp--></select></td></tr>
<tr><td class="itemForm">Period of: </td><td class="txtPag">
<input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" /> to the 
<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Timeslots: </td><td>
<select id="lsHora" class="txtPag"><!--optHora--></select>:
<select id="lsMinuto" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Seach" onClick="loadRepPersonasPorHorario()"></td></tr>
</table><br><br>
<div id="lyDetalles"></div><br />
<div id="lyData"></div>