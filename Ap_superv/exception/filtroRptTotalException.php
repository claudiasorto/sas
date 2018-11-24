<table width="825" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">
<tr><td colspan="2" align="center" class="txtForm">Filters to generate exception report</td></tr>
<tr><td width="25%" class="itemForm">Period to: </td><td>
<input type="text" id="fechaIni" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fechaIni', '%d/%m/%Y');" style="cursor:pointer;" /> The: 
<input type="text" id="fechaFin" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fechaFin', '%d/%m/%Y');" style="cursor:pointer;" />
</td></tr>
<tr><td class="itemForm">Type of exception: </td><td><select id="lsException" class="txtPag"><option value="0">[ALL]</option><!--optTpExc--></select></td></tr>
<tr><td class="itemForm">Account: </td><td><select id="lsCuenta" onChange="getDepartFiltros2(this.value)" class="txtPag"><option value="0">[ALL]</option><!--optCuenta--></select></td></tr>
<tr><td class="itemForm">Department:&nbsp;</td><td colspan="3"><span id="lyDepart"><!--optDepart--></span></td></tr>
<tr><td class="itemForm">Position:&nbsp;</td><td colspan="3"><span id="lyPlaza"><!--optPlaza--></span></td></tr>
<tr><td class="itemForm">Immediate boss</td><td colspan="3"><div id="lySuperv"><select id="lsSuperv" class="txtPag"><option value="0">[ALL]</option><!--optSuperv--></select></div></td></tr>
<tr><td class="itemForm">Employee: </td><td><select id="lsEmp" class="txtPag"><option value="0">[ALL]</option><!--optEmp--></select></td></tr>
<tr><td class="itemForm">Employee name: </td><td><input type="text" id="txtEmp" size="70" class="txtPag"></td></tr>
<tr><td class="itemForm">Badge: </td><td><input type="text" id="txtBadge" size="25" class="txtPag"></td></tr>
<tr><td class="itemForm">Status: </td><td><select id="lsStatus" class="txtPag">
<option value="0">[ALL]</option>
<option value="P">In Progress</option>
<option value="A">Approved</option>
<option value="R">Rejected</option>
</select></td></tr>
<tr><td class="itemForm">Format Report: </td><td><select id="lsTpReport" class="txtPag">
<option value="1">DETAILS</option>
<option value="2">TOTAL</option></select></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Generate report" onClick="loadRptTotalException()"></td></tr>
</table>
<br><br>
<div id="data"></div>