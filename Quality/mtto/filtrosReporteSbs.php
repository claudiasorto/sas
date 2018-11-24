<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="2">
<tr><td align="right"><b>Period of</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right"><b>Account: </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td align="right"><b>Supervisor: </td><td><select id="lsSup" onChange="getEmployees(this.value)" class="txtPag"><!--optSup--></select></td></tr>
<tr><td align="right"><b>Employee: </td><td><span id="lyEmp"><select id="lsEmp" class="txtPag"><option value="0">[ALL]</option><!--optEmp--></select></span></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Load report" onClick="loadReportSbs()"/></td></tr>
</table>
<br><br>
<div id="lyData"></div>

