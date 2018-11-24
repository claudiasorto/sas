<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">
<tr bgcolor="#8FBC8F"><td  align="center"><font color="#FFFFFF">
HORAS TRABAJADAS VRS HORAS PROGRAMADAS
</font> </td></tr>
<tr><td align="center">Seleccione un per&iacute;odo de evaluaci&oacute;n desde:
<input type="text" name="fechaIni" id="fechaIni" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaIni', '%d/%m/%Y');" style="cursor:pointer;" />
Hasta: <input type="text" name="fechaFin" id="fechaFin" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaFin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>	
<tr><td align="center"><input type="button" class="btn" value="Generar reporte" onClick="rptWorkSchHours()"></td></tr>
</table>
<br>
<div id="lyData"></div>
