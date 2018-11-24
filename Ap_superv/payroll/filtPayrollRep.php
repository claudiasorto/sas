<table class="tblListBack" width="825" align="center">
<tr><td class="showItem" colspan="2"><b>FILTERS TO GENERATE PAYROLL REPORT</b></td></tr>
<tr><td class="itemForm">Account: </td><td><select id="lsCuenta" onChange="getDepartFiltros2(this.value)" class="txtPag"><option value="0">[ALL]</option><!--optCuenta--></select></td></tr>
<tr><td class="itemForm">Department:&nbsp;</td><td colspan="3"><span id="lyDepart"><!--optDepart--></span></td></tr>
<tr><td class="itemForm">Posici&oacute;n:&nbsp;</td><td colspan="3"><span id="lyPlaza"><!--optPlaza--></span></td></tr>
<tr><td class="itemForm">Immediate boss</td><td colspan="3"><div id="lySuperv"><select id="lsSuperv" class="txtPag"><option value="0">[ALL]</option><!--optSuperv--></select></div></td></tr>
<tr><td class="itemForm">Payroll period from:&nbsp;</td><td class="txtPag"><input type="text" id="fec_ini" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;&nbsp;to:&nbsp;<input type="text" id="fec_fin" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Employee name: </td><td colspan="3"><input type="text" id="txtNombre" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Badge: </td><td colspan="3"><input type="text" id="txtUsername" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td colspan="4" class="showItem" title="Click to generate the report"><input type="button" class="btn" value="Generate report" onClick="load_rptPayroll()"></td></tr>
</table>
<br />
<div id="datos_rpt"></div>
