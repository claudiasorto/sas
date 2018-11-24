<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="_self" action="mtto/up_DayOff.php">
<table width="725" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">
<tr><td colspan="2" align="center" class="txtForm">UPLOAD DAY OFF</td></tr>
<tr><td align="right">Initial date: <input type="text" id="fechaIni" name="fechaIni" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fechaIni', '%d/%m/%Y');" style="cursor:pointer;" /></td>
<td>End date: <input type="text" id="fechaFin" name="fechaFin" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fechaFin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag"/></td></tr>
<tr><td align="center" colspan="2"><i>Note: The document must have the following format: Badge, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday. The freeday will be marked with the word in CAPS OFF.</i></td></tr>
<tr><td colspan="2" align="center"><input type="button" onClick="upFile()" value="Upload data" class="btn" ></td></tr>
</table>
<iframe id="frUP" name="frUP" src="no.php" height="2px" style="visibility:hidden;"></iframe>
</form>
