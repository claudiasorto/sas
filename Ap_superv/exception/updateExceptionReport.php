<input type="hidden" id="idException" value="<!--exception_id-->" >
<table width="700" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">
<tr><td colspan="2" class="txtForm" align="center">Exception number: <!--exception_id--></td></tr>
<tr><td class="itemForm">Employee: </td><td><!--nombre-->&nbsp;<!--apellido--></td></tr>
<tr><td class="itemForm">Badge: </td><td><!--badge--></td></tr>
<tr><td class="itemForm">Date: </td><td><input type="text" id="fecha" class="txtPag" readonly="1" size="25" value="<!--date-->"><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Initial and Final time: </td><td>
<select id="lsHoraIni" class="txtPag"><!--horasIni--></select>:<select id="lsMinutosIni" class="txtPag"><!--minutosIni--></select>
<select id="lsHoraFin" class="txtPag"><!--horasFin--></select>:<select id="lsMinutosFin" class="txtPag"><!--minutosFin--></select>
</td></tr>
<tr><td class="itemForm">Reason: </td><td><select id="lsReason" class="txtPag"><!--optException--></select></td></tr>
<tr><td class="itemForm">Comments: </td><td><textarea  cols="70" rows="3" class="txtPag" id="txtComment"><!--comment--></textarea></td></tr>
<tr><td align="center" colspan="2"><input type="button" class="btn" value="Save upgrade" onClick="saveUpdateExceptionReport()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" class="btn" value="Cancel" onclick="loadException(<!--exception_id-->)"></td></tr>
</table>
