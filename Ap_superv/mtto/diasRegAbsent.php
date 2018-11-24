<table width="725" class="tblResult" align="center" bordercolor="#069" align="center" cellpadding="2" cellspacing="0">
<tr class="txtForm"><th align="center">REGISTRATION ABSENTEEISM</th></tr>
<tr><td  align="center">Date: <input type="text" id="fecha" name="fecha" value="<!--fechaActual-->" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="center">Supervisor: 
<select id="lsSup" class="txtPag"><!--optSup--></select></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Load" onClick="loadFormAbsentDay()"></td></tr>
</table><br><br>
<div id="lyComment" style="display:none"></div>
<br><br>
<div id="lyData"></div>