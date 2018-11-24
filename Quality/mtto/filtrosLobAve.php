<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="2">
<tr><td align="right"><b>Period of</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right"><b>Account: </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td align="right"><b>Supervisor: </td><td><select id="lsSup" class="txtPag"><!--optSup--></select></td></tr>
<tr><td align="right"><b>Status of employee: </b></td>
<td><select id="lsStatus" class="txtPag">
<option value="1">Active</option>
<option value="-1">[ALL]</option>
<option value="0">Inactive</option>
</select></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Load LOB Average" onClick="loadLobAverage()"/></td></tr>
</table>
<br><br><br><br>
<div id="lyData"></div>