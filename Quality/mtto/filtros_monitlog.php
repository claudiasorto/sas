<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="2">
<tr><td width="25%" align="right"><b>Evaluation type: </td><td><select id="lsTpEval">
<option value="0">SELECT A TYPE OF EVALUATION</option>
<option value="1">CUSTOMER SERVICE</option>
<option value="2">SALES</option>
<option value="3">NEW SERVICE</option>
<option value="4">CHAT</option>
</select></td></tr>
<tr><td align="right"><b>Period of:&nbsp;</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /> </td></tr>
<tr><td align="right"><b>Employee: </td><td><select id="lsEmp" class="txtPag"><!--optEmp--></select></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Generate report" onClick="load_Monitlog()"></td></tr></table>
<br/>
<div id="lyAutorizar"></div>
<br />
<div id="lyData"></div>