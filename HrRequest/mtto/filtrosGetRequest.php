<table cellpadding="3" cellspacing="0" width="60%" class="tblResult" align="center" bordercolor="#069">
<tr><td class="itemForm">Status: </td>
<td><select id="lsStatus" class="txtPag"><option value="O">Open</option><option value="0">ALL</option><option value="C">Closed</option></select></td></tr>
<tr><td class="itemForm">Type of request: </td>
<td><select id="lsTpReq" class="txtPag"><!--optTpRequest--></select></td></tr>
<tr><td class="itemForm">Period: </td>
<td><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" width="20" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" width="20" /></td></tr>
<tr><td class="itemForm">Employee: </td><td><input type="text" class="txtPag" size="40" id="txtNombre" /></td></tr>
<tr><td class="itemForm">Badge: </td><td><input type="text" class="txtPag" id="txtBadge" /></td></tr>
<tr><td colspan="2" align="center">
<input type="button" class="btn" value="Search HR Request" onclick="getRequest()" />
</td></tr>
</table>
<br />
<div id="lyFormDoc"></div>
<br />
<div id="lyClose"></div>
<br>
<div id="lyForm"></div>