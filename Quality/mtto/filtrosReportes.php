<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="2">
<tr><td align="right"><b>Period of</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right"><b>Account: </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td align="right"><b>Supervisor: </td><td><select id="lsSup" onChange="getEmployees(this.value)" class="txtPag"><!--optSup--></select></td></tr>
<tr><td align="right"><b>Employee: </td><td><span id="lyEmp"><select id="lsEmp" class="txtPag"><option value="0">[ALL]</option><!--optEmp--></select></span></td></tr>
<tr><td align="right"><b>QA Agent: </td><td><select id="lsQa" class="txtPag"><option value="0">[ALL]</option><!--optQa--></select></td></tr>
<tr><td align="right"><b>Maker position:</b> </td><td>
<select id="lsPosicion" class="txtPag">
<option value="Q">QA AGENT</option>
<option value="0">[ALL]</option>
<option value="O">SUPERVISORS</option>
</select>
</td></tr>
<tr><td align="right"><b>Report: </td><td><select id="lsReport" class="txtPag">
<option value="0">SELECT A REPORT TYPE</option>
<option value="1">Details</option>
<option value="2">Total averages</option>
<option value="3">Monitoring report</option>
</select></td></tr>
<tr><td align="right"><b>Monitoring type: </td><td><select id="lsMonit" class="txtPag">
<option value="0">SELECT A TYPE OF EVALUATION</option>
<option value="1">CUSTOMER SERVICE</option>
<option value="2">SALES</option>
<option value="3">NEW SERVICE</option>
</select></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Load report" onClick="loadReportQa()"/></td></tr>
</table>
<br><br><br><br>
<div id="lyData"></div>

