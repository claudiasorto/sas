<table class="tblHead" width="600px" align="center" cellpadding="2" cellspacing="2">
<tr><td>Account: </td><td class="txtPag">
<select id="lsCuenta" name="lsCuenta" class="txtPag">
<option value="0">[ALL]</option>
<option value="1">Terracom</option>
<option value="2">Yourtel</option>
</select>
</td></tr>
<tr><td>Location: </td><td>
<select id="lsUbicacion" name="lsUbicacion" class="txtPag">
<option value="0">[ALL]</option>
<option value="1">Expresstel</option>
<option value="2">Skycom</option>
<tr><td>Period of: </td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Load" onClick="loadReportCall()"></td></tr>
</table>
<br><br>
<div id="lyData"></div>