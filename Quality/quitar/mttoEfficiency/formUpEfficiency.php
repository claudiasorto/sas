<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="_self" action="mttoEfficiency/up_efficiency.php">
<table class="tblHead" width="700px" align="center" cellpadding="2" cellspacing="4">
<tr><td width="35%" align="right">Date: </td><td class="txtPag"><input type="text" name="fecha" id="fecha" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td width="35%" align="right">File percentage of Efficiency: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>
<tr><td colspan="2" align="center">Note: The file you upload must be in CSV<br>In the format: Badge, percentage of Efficiency in the range 0 -1 ej(0.10)</td></tr>

<tr><td align="center" colspan="2"><input type="button" onClick="upFile()" value="Upload data" class="btn" ></td></tr>
</table>

<iframe id="frUP" name="frUP" src="no.php" style="visibility:hidden;"></iframe>
</form>