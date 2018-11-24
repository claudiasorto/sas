<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="2" cellspacing="2" class="tblInc">
<tr><td colspan="2" align="center" class="backList">Payment trouble tickets</td></tr>
<tr><td align="right"><b>Paystub</b></td><td><select id="lsPaystub"><!--optPaystub--></select></td></tr>
<tr><td align="right"><b>Status</b></td><td>
<select id="lsStatus">
<option value="P">Pending</option>
<option value="0">[ALL]</option>
<option value="A">Approved</option>
<option value="R">Rejected</option>
</select>
</td></tr>
<tr><td align="right">Employee </td><td><input type="text" id="txtNombre" size="30"></td></tr>
<tr><td align="right">Badge </td><td><input type="text" id="txtBadge"></td></tr>
<tr><td colspan="2" align="center"><input type="button" value="Search" onClick="loadPaystubTicket()"></td></tr>
</table>
<br>
<div id="lyData"></div>