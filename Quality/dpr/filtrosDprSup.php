<table class="tblHead" width="800" align="center" cellpadding="2" cellspacing="2">
<tr><td class="itemForm">Period of: </td><td><input type="text" name="fechaIni" id="fechaIni" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaIni', '%d/%m/%Y');" style="cursor:pointer;" />
to the <input type="text" name="fechaFin" id="fechaFin" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaFin', '%d/%m/%Y');" style="cursor:pointer;" />
</td></tr>
<tr><td class="itemForm">Inmediate boss: </td><td><select id="lsSup" class="txtPag"><!--optSup--></select></td></tr>
<tr><td class="itemCenter" colspan="2"><input type="button" class="btn" value="Search" onClick="loadDprSup()"></td></tr>
</table>
<br>
<div id="lyData"></div>