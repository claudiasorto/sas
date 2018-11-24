<table width="700" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">
<tr><td colspan="2" class="txtForm" align="center">FORM TO REGISTER EXCEPTIONES</td></tr>
<tr><td width="15%">Employee: </td><td><select id="lsEmp" class="txtPag"><!--optEmp--></select></td></tr>
<tr><td>Date: </td><td><input type="text" id="fecha" class="txtPag" readonly="1" size="25" ><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td>Initial time:</td><td><select id="lsHoraIni" class="txtPag"><!--optHora--></select>:
<select id="lsMinutosIni" class="txtPag"><!--optMinutos--></select>
Final time: <select id="lsHoraFin" class="txtPag"><!--optHora--></select>:
<select id="lsMinutosFin" class="txtPag"><!--optMinutos--></select></td></tr>
<tr><td>Reason: </td><td><select id="lsRazon" class="txtPag"><!--optExcep--></select></td></tr>
<tr><td>Ticket number: </td><td><input type="text" id="txtTicket" class="txtPag" size="25" /></td></tr>
<tr><td>Comments: </td><td><textarea id="txtComment" class="txtPag" rows="3" cols="90"></textarea></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Save Exception" onClick="saveException()"></td></tr>
</table>
