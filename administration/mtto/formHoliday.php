<table class="backTablaMain" width="800px" cellpadding="2" cellspacing="2">
<tr><td class="showItem" colspan="2">Holidays</td></tr>
<tr><td class="itemForm" width="25%">Holiday name: </td>
	<td><input type="text" class="txtPag" name="txtName" id="txtName" size="25"></td></tr>
<tr><td class="itemForm" width="25%">Country: </td>
	<td><select id="lsGeo" class="txtPag"><!--optG--></select> </td></tr>
<tr><td class="itemForm" width="25%">Date: </td>
	<td><input type="text" name="txtDate" id="txtDate" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('txtDate', '%d/%m/%Y');" style="cursor:pointer;" />
<tr><td colspan="2" align="center">
	<input type="button" class="btn" value="Save" onClick="saveHoliday()" title="click to save" style="cursor:pointer;">
	<input type="button" class="btn" value="Search" onClick="searchHoliday()" title="click to search" style="cursor:pointer;">
</td></tr>
<tr><td colspan="2"><div id="lyData"></div></td></tr>
</table>
