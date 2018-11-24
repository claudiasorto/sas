<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">
<tr bgcolor="#8FBC8F"><td colspan="2" align="center"><font color="#FFFFFF">Form to configure the applicable discounts in the payroll</font></td></tr>
<tr><td align="right">Discount label: </td>
<td><input type="text" id="txtLabel"></td></tr>
<tr><td align="right">Flexfield: </td>
<td><span id="lyAttr"><select id="lsAttr"><option value="0">Select an attribute</option><!--optAttr--></select></span></td></tr>
<tr><td colspan="2" align="center"><input type="button" onclick="saveDiscountSetup()" value="Save setup"></td></tr>
</table><br><br>
<div id="tblDiscounts"><!--tblDiscounts--></div>
<div id="lyData"></div>
