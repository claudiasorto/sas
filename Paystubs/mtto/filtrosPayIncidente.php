<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="2" cellspacing="2" class="tblInc">
<tr><td>Payment day: </td><td><select id="lsPay"><option value="0">Select a payment date</option><!--optPay--></select></td></tr>
<tr><td>Status Employee: </td>
<td><select id="lsStatus" onchange="loadEmp(this.value)">
<option value="1">ACTIVE</option><option value="0">ALL</option><option value="2">INACTIVE</option></select></td></tr>
<tr><td>Employee: </td><td><span id="lyEmp"><select id="lsEmp"><option value="0">Select a employee</option><!--optEmp--></select></span></td></tr>
<tr><td>Badge: </td><td><input type="text" id="txtBadge"></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="btn" value="Register incidents" onClick="formRegIncidents()"></td></tr>
</table>
<br>
<div id="lyData"></div>
