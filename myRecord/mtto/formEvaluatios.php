<table cellpadding="3" cellspacing="0" width="60%" class="tblReport" align="center" bordercolor="#069">
<tr><td align="right"><b>Period of</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.png" width="20" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.png" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" width="20" /></td></tr>
<tr><td align="right"><b>Maker</td>
<td ><select id="lsMaker" class="txtPag"><option value="0">[ALL]</option><option value="1">QA Agent</option><option value="2">Supervisor</option></select></td>
<tr><td align="center" colspan="2"><input type="button" class="btn" value="Search Evaluations" onClick="reportEvaluations()"></td></tr>
</table>
<br /><br />
<div id="lyData"></div>