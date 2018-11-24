<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">
<tr><th bgcolor="#336699" colspan="2" align="center"><font color="#FFFFFF">FILTERS TO GET AVERAGES FOR CALLS</font></th></tr>
<tr><td width="20%"><b>Account: </td><td><select id="lsCuenta" class="txtPag"><!--optCuenta--></select></td></tr>
<tr><td><b>Supervisor: </td><td><select id="lsSup" class="txtPag"><!--optSup--></select></td></tr>
<tr><td><b>Agent: </td><td><select id="lsEmp" class="txtPag"><!--optEmp--></select></td></tr>
<tr><td><b>Name: </td><td><input type="text" id="txtNombre" size="30" class="txtPag"/></td></tr>
<tr><td><b>Badge: </td><td><input type="text" id="txtBadge" class="txtPag"/></td></tr>
<tr><td><b>Period of</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;to the:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Search" onClick="loadAveragesCall()"/></td></tr>
</table>
<br><br>
<div id="lyData"></div>