<table class="backTablaMain" cellpadding="2" cellspacing="2">
<tr><td class="showItem" colspan="2">Form to update Account</td></tr>
<tr><td class="itemForm" width="25%">Name: </td>
	<td><input type="text" required="true" id="txtName" value="<!--name_account-->"> </td></tr>
<tr><td class="itemForm" width="25%">Description: </td>
	<td><input type="text" id="txtDesc" value="<!--desc_account-->"></td></tr>
<tr><td class="itemForm" width="25%">Type: </td>
	<td><select id="optType" required="true"><!--optTpAcc--></select></td></tr>
<tr><td class="itemForm" width="25%">Status: </td>
	<td><select id="optStatus" required="true"><!--optS--></select></td></tr>
<tr><td class="itemForm" colspan="2"><input type="button" value="Save" 
	onclick="updateAccount('<!--id_account-->')" class="btn"></td></tr>
</tr>
</table>