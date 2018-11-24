<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="_self" action="mttoPhoneMetric/up_formMetric.php">
<table class="tblHead" width="800" align="center" cellpadding="2" cellspacing="4">
<tr><td width="35%" align="right">Date: </td><td class="txtPag" colspan="2"><input type="text" name="fecha" id="fecha" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td width="35%" align="right">Data: </td>
<td><input type="file" name="flData" id="flData" size="25" class="txtPag" ></td></tr>
<tr><td colspan="3">
<table border="1" bordercolor="#003366" align="center">
<tr><td class="txtPag">Badge</td>
<td class="txtPag">Active(HH:MM:SS)</td>
<td class="txtPag">Calls received in decimal formal</td>
<td class="txtPag">Total refused calls</td>
<td class="txtPag">Percentage of Efficiency range 0 -1</td></tr>
</table>
</td></tr>
<!--
<tr><td width="35%" align="right">Total time on calls: </td>
<td><input type="file" name="flTimeAht" id="flTimeAht" size="25" class="txtPag" ></td>
<td class="txtPag">Total time on calls (HH:MM:SS)</td></tr>

<tr><td width="35%" align="right">Total calls received: </td>
<td><input type="file" name="flCalls" id="flCalls" size="25" class="txtPag" ></td>
<td class="txtPag">Total calls received in decimal formal</td></tr>

<tr><td width="35%" align="right">Total refused calls: </td>
<td><input type="file" name="flRefused" id="flRefused" size="25" class="txtPag"></td></tr>

<tr><td width="35%" align="right">Percentage of Efficiency: </td>
<td><input type="file" name="flEfficiency" id="flEfficiency" size="25" class="txtPag" ></td>
<td class="txtPag">Percentage of Efficiency in the range 0 -1 ej(0.10)</td></tr>

<tr><td align="right">Total time in lateness: </td>
<td><input type="file" name="flLateness" id="flLateness" size="25" class="txtPag"></td>
<td class="txtPag">Format (HH:MM:SS)</td></tr>
-->
<tr><td align="center" colspan="2"><input type="button" onClick="upFile()" value="Upload data" class="btn" ></td></tr>
</table>

<iframe id="frUP" name="frUP" src="no.php" style="visibility:hidden;"></iframe>
</form>