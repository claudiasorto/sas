<table class="backTablaMain" width="800px" cellpadding="2" cellspacing="2">
<tr><td class="showItem" colspan="2">Form to update AP Type</td></tr>
<tr><td class="itemForm" width="25%">Name: </td>
<td><input type="text" size="80" class="txtPag" id="txtName" value="<!--name_tpap-->"></td></tr>
<tr><td class="itemForm" width="25%">Has start date?: </td>
<td><select id="lsStartDate" class="txtPag">
	<!--has_start_date-->
</select></td></tr>
<tr><td class="itemForm" width="25%">Has end date?: </td>
<td><select id="lsEndDate" class="txtPag">
	<!--has_end_date-->
</select></td></tr>
<tr><td class="itemForm" width="25%">Has time?: </td>
<td><select id="lsTime" class="txtPag">
	<!--has_time-->
</select></td></tr>
<tr><td class="itemForm">Effective end date
	<td><input type="text" name="EffectiveEnd" id="EffectiveEnd" size="15" class="txtPag" value="<!--end_date-->" /><img src="images/calendar.jpg" onclick="return showCalendar('EffectiveEnd', '%d/%m/%Y');" style="cursor:pointer;" />
</td></tr>	
<tr><td class="itemForm" width="25%">Affect salary?: </td>
<td><select id="lsSalary" class="txtPag">
	<!--affects_salary-->
</select></td></tr>
<tr><td class="itemForm" width="25%">inactivate employee?: </td>
<td><select id="lsInactive" class="txtPag">
	<!--inactive_employee-->
</select></td></tr>
<tr><td class="showItem" colspan="2">Approvers: </td>
<tr><td class="itemForm" width="25%">Area Manager: </td>
<td><select id="lsAreaManager" class="txtPag">
	<!--areaManager-->
</select></td></tr>
<tr><td class="itemForm" width="25%">Workforce: </td>
<td><select id="lsWorkforce" class="txtPag">
	<!--workforce-->
</select></td></tr>
<tr><td class="itemForm" width="25%">Human resources: </td>
<td><select id="lsHR" class="txtPag">
	<!--hr-->
</select></td></tr>
<tr><td class="itemForm" width="25%">General Manager: </td>
<td><select id="lsGeneralManager" class="txtPag">
	<!--generalManager-->
</select></td></tr>

<tr><td colspan="2" align="center">
	<input type="button" class="btn" value="Save" onClick="saveEditAP(<!--id_tpap-->)">
	<input type="button" class="btn" value="Cancel" onClick="cancelAP()">
</td></tr>
</table>
<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>