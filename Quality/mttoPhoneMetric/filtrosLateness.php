<table class="tblHead" width="800" align="center" cellpadding="2" cellspacing="2">
<tr><td class="backTablaForm" colspan="2">Lateness Report</td>
<tr><td class="itemForm">Account </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td class="itemForm">Department </td><td><select id="lsDepart" class="txtPag"><!--optDepart--></select></td></tr>
<tr><td class="itemForm">Position </td><td><select id="lsPos" class="txtPag"><!--optPosicion--></select></td></tr>
<tr><td class="itemForm">Immediate boss </td><td><select id="lsJefe" class="txtPag"><!--optJefe--></select></td></tr>
<tr><td class="itemForm">Period of </td>
<td><input type="text" name="fechaIni" id="fechaIni" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaIni', '%d/%m/%Y');" style="cursor:pointer;" />
&nbsp;to the 
<input type="text" name="fechaFin" id="fechaFin" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaFin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Employee </td><td><select id="lsEmp" class="txtPag"><!--optEmp--></select></td></tr>
<tr><td class="itemForm">Employee name </td><td><input type="text" class="txtPag" size="25" id="txtNombre"></td></tr>
<tr><td class="itemForm">Badge </td><td><input type="text" class="txtPag" id="txtBadge"></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Search" onClick="loadLateness()"/></td></tr>
</table>
<br><br>
<div id="lyDetalles"></div>
<br /><br />
<div id="lyData"></div>