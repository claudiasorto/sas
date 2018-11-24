<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="2">
<tr><td align="right"><b>Period of</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right"><b>Account: </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td align="right"><b>Supervisor: </td><td><select id="lsSup" onChange="getMultipleEmployees(this.value)" class="txtPag"><!--optSup--></select></td></tr>
<tr><td align="right"><b>Employee: </td><td><span id="lyEmp">
<select NAME="sel1[]" ID="sel1" SIZE="5" multiple="multiple" class="txtPag" style="width: 250px">
<!--optEmp-->
</select>
</span>
</td>
<td>
<input TYPE="BUTTON" VALUE="->" ONCLICK="addIt();" class="btn"></input>
<br />
<input TYPE="BUTTON" VALUE="<-" ONCLICK="delIt();" class="btn"></input>
</td>
<td>
<select NAME="sel2[]" ID="sel2" SIZE="5" class="txtPag" multiple="multiple" style="width: 250px">
</select>
<!--
<select id="lsEmp" class="txtPag"><option value="0">[ALL]</option></select></span></td></tr>-->
<tr><td align="right"><b>QA Agent: </td><td><select id="lsQa" class="txtPag"><option value="0">[ALL]</option><!--optQa--></select></td></tr>
<tr><td align="right"><b>Status of employee: </b></td>
<td><select id="lsStatus" class="txtPag">
<option value="-1">[ALL]</option>
<option value="1">Active</option>
<option value="0">Inactive</option>
</select></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Load weekly report" onClick="loadWeeklyReport()"/></td></tr>
</table>
<br><br><br><br>
<div id="lyData"></div>
