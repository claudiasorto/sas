<table class="backTablaMain" width="800px" cellpadding="2" cellspacing="2">
<tr><td class="showItem" colspan="2">Form to create new AP Type</td></tr>
<tr><td class="itemForm" width="25%">Name: </td>
<td><input type="text" size="80" class="txtPag" id="txtName"></td></tr>
<tr><td class="itemForm" width="25%">Has start date?: </td>
<td><select id="lsStartDate" class="txtPag">
	<option value="0">Select an option</option>
	<option value="Y">Yes</option>
	<option value="N">No</option>
</select></td></tr>
<tr><td class="itemForm" width="25%">Has end date?: </td>
<td><select id="lsEndDate" class="txtPag">
	<option value="0">Select an option</option>
	<option value="Y">Yes</option>
	<option value="N">No</option>
</select></td></tr>
<tr><td class="itemForm" width="25%">Has time?: </td>
<td><select id="lsTime" class="txtPag">
	<option value="0">Select an option</option>
	<option value="Y">Yes</option>
	<option value="N">No</option>
</select></td></tr>
<tr><td class="itemForm" width="25%">Affect salary?: </td>
<td><select id="lsSalary" class="txtPag">
	<option value="igual">=</option>
	<option value="mas">+</option>
	<option value="menos">-</option>
</select></td></tr>
<tr><td class="itemForm" width="25%">inactivate employee?: </td>
<td><select id="lsInactive" class="txtPag">
	<option value="N">No</option>
	<option value="Y">Yes</option>
</select></td></tr>
<tr><td class="showItem" colspan="2">Approvers: </td>
<tr><td class="itemForm" width="25%">Area Manager: </td>
<td><select id="lsAreaManager" class="txtPag">
	<option value="N">No</option>
	<option value="Y">Yes</option>
</select></td></tr>
<tr><td class="itemForm" width="25%">Workforce: </td>
<td><select id="lsWorkforce" class="txtPag">
	<option value="N">No</option>
	<option value="Y">Yes</option>
</select></td></tr>
<tr><td class="itemForm" width="25%">Human resources: </td>
<td><select id="lsHR" class="txtPag">
	<option value="N">No</option>
	<option value="Y">Yes</option>
</select></td></tr>
<tr><td class="itemForm" width="25%">General Manager: </td>
<td><select id="lsGeneralManager" class="txtPag">
	<option value="N">No</option>
	<option value="Y">Yes</option>
</select></td></tr>
<tr><td colspan="2" align="center">
	<input type="button" class="btn" value="Save" onClick="saveAP()">
	<input type="button" class="btn" value="Cancel" onClick="cancelAP()">
</td></tr>
</table>