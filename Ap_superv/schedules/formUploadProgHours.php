<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="frUP" action="schedules/up_proghours.php">
<table cellpadding="0" cellspacing="0" align="center">
<tr><td height="10"></td></tr>
  <tr><td height="1" bgcolor="#CCCCCC"></td></tr>
  <tr><td height="10"></td></tr>
  <tr><td style="padding-left:5px;">

	<table cellpadding="4" cellspacing="4" width="700" border="0" class="backTablaMain" align="center">
	<tr><td class="txtForm" colspan="2" align="center">UPLOAD PROGRAMMED HOURS</td></tr>
	
    <tr><td class="txtForm" width="30%" align="right">Date: </td>
    <td><input type="text" name="fecha" id="fecha" size="15" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha', '%d/%m/%Y');"  style="cursor:pointer;" /></td></tr>
    <tr><td class="txtForm" align="right">Cantidad de dias a cargar</td>
    <td>
        <select id="lsDias" name="lsDias" class="txtPag">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        </select> 
    </td></tr>
    <tr><td class="txtForm" align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>  
    <tr><td colspan="2"><i>the file format must be csv in the following order: Badge, hours</i> </td></tr>

<!--
    <tr><td class="txtForm" align="right">Starting date of the week: </td>
	<td><input type="text" name="fecha" id="fecha" size="15" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha', '%d/%m/%Y');"  style="cursor:pointer;" /></td></tr>
	<tr><td class="txtForm" align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>
	<tr><td colspan="2" class="txtPag"><b>Note:</b> The format of the file to load must be in CSV in the following order
	<table border="1" align="center">
    <tr class="showItem"><td>BADGE</td>
    <td  align="center">Hour for Monday</td>
    <td align="center">Tuesday</td>
    <td align="center">Wednesday</td>
    <td align="center">Thursday</td>
    <td align="center">Friday</td>
    <td align="center">Saturday</td>
    <td align="center">Sunday</td></tr>

     <tr class="txtPag"><td>CSXXXX</td>
    <td align="center">9</td>
    <td align="center">8</td>
    <td align="center">10</td>
    <td align="center">4.5</td>
    <td align="center">9</td>
    <td align="center">0</td>
    <td align="center">0</td>
    </tr>
    </table><br>
    <li>Total Hours in decimal format</li>
    <li>If Day off, total hours is 0</li>
    <li>Remove the headers of days, is only referential</li>
    </td></tr>
-->
	<tr><td class="txtForm" align="center" colspan="2"><input type="button" onClick="upFileWait()" value="Upload data" class="btn" id="btnUp"></td></tr>
	<tr><td colspan="2" height="1px"></td></tr>
    <tr><td colspan="2" align="center"><div class="loadP" id="lyMsg" style="display:none;">
    <img src="images/PleaseWait.gif" width="400">
    </div></td></tr>
	</table>
</td></tr>
<tr><td height="1"><iframe id="frUP" height="1" name="frUP" src="no.php" style="visibility:hidden;"></iframe></td></tr>
</table>
</form>
