<table width="825" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">
<tr><td colspan="2" align="center" class="txtForm">Filters to generate exception report</td></tr>
<tr><td width="25%">Period to: </td><td>
<input type="text" id="fechaIni" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fechaIni', '%d/%m/%Y');" style="cursor:pointer;" /> The: 
<input type="text" id="fechaFin" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fechaFin', '%d/%m/%Y');" style="cursor:pointer;" />
</td></tr>
<tr><td>Type of exception: </td><td><select id="lsException" class="txtPag"><option value="0">[ALL]</option><!--optTpExc--></select></td></tr>
<tr><td>Employee: </td><td><select id="lsEmp" class="txtPag"><option value="0">[ALL]</option><!--optEmp--></select></td></tr>
<tr><td>Employee name: </td><td><input type="text" id="txtEmp" size="70" class="txtPag"></td></tr>
<tr><td>Badge: </td><td><input type="text" id="txtBadge" size="25" class="txtPag"></td></tr>
<tr><td>Format Report: </td><td><select id="lsTpReport" class="txtPag">
<option value="1">DETAILS</option>
<option value="2">TOTAL</option></select></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Generate report" onClick="loadRptException()"></td></tr>
</table>
<br><br>
<div id="data"></div>