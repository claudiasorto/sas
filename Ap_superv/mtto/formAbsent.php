<input type="hidden" id="idAccion" value="<!--optAccion-->" />
<table width="700" bgcolor="#FFFFFF" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">
<tr><td align="center" colspan="2" class="showItem"><b>FILTERS TO GENERATE ABSENTEEISM REPORT<b></td></tr>
<tr><td class="itemForm" width="30%"><b>Period of:</b>&nbsp;</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="25" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;The:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="25" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /> </td></tr>
<tr><td class="itemForm"><b>Account: </b></td><td>
<select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td class="itemForm"><b>Immediate boss:</b> </td>
<td><select id="lsSup" class="txtPag"><!--optSup--></select></td></tr>
<tr><td  class="itemForm"><b>Employee: </b></td><td><select id="lsEmp" class="txtPag"><!--optEmp--></select></td></tr>
<tr><td class="itemForm"><b>Employee name: </b></td><td><input type="text" class="txtPag" id="txtNombre" size="35" /></td></tr>
<tr><td class="itemForm"><b>Badge: </b></td><td><input type="text" class="txtPag" id="txtBadge" /></td></tr>
<tr><td class="itemForm"><b>Status:</b> </td><td>
<select multiple name="sel1[]" id="sel1" size="5" class="txtPag">
<option value="P">PRESENT</option>
<option value="T">TARDY</option>
<option value="AJ">JUSTIFIED ABSENCE</option>
<option value="A">UNJUSTIFIED ABSENCE</option>
<option value="O">DAY OFF</option>
</select>
<input type="button" value=">>" onclick="pasar()" align="absmiddle" />
<select multiple name="sel2[]" id="sel2" size="5" class="txtPag">
<option value="-">-</option>
</select>
</td>
</tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Generate Report" onClick="loadReportAbsent()"></td></tr>
</table>
<br><br>
<div id="lyRpt"></div>