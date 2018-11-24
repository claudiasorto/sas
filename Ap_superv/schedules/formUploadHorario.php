<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="frUP" action="schedules/up_schedules.php">
<table cellpadding="0" cellspacing="0" align="center">
<tr><td height="10"></td></tr>
  <tr><td height="1" bgcolor="#CCCCCC"></td></tr>
  <tr><td height="10"></td></tr>
  <tr><td style="padding-left:5px;">

	<table cellpadding="1" cellspacing="0" width="700" border="0" class="tblListBack" align="center">
	<tr><td class="backTablaForm"  colspan="2" align="center">UPLOAD SCHEDULES</td></tr>
	<tr><td class="txtForm" align="right">Starting date of the week: </td>
	<td><input type="text" name="fecha" id="fecha" size="15" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha', '%d/%m/%Y');"  style="cursor:pointer;" /></td></tr>
	<tr><td class="txtForm" align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>
	<tr><td colspan="2" class="txtPag"><b>Note:</b> The format of the file to load must be in CSV in the following order (hours in format HH:MM):
	<table border="1">
    <tr class="showItem"><td></td>
    <td colspan="4" align="center">Monday</td>
    <td colspan="4" align="center">Tuesday</td>
    <td colspan="4" align="center">Wednesday</td>
    <td colspan="4" align="center">Thursday</td>
    <td colspan="4" align="center">Friday</td>
    <td colspan="4" align="center">Saturday</td>
    <td colspan="4" align="center">Sunday</td></tr>
	<tr class="txtPag">
    <td>BADGE</td>
 	<td>Entry - End of duty</td>
 	<td>Break 1 out</td>
 	<td>Lunch out</td>
 	<td>Break 2 out</td>
    <td>Entry - End of duty</td>
 	<td>Break 1 out</td>
 	<td>Lunch out</td>
 	<td>Break 2 out</td>
    <td colspan="24">...</td>
    </tr>
    <tr class="txtPag"><td>CSXXXX</td>
    <td>08:00-17:00</td>
    <td>10:00</td>
    <td>12:00</td>
    <td>16:00</td>
    <td>OFF</td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="24">...</td>
    </tr>
    </table><br>
    <li>Hours in format hh:mm</li>
    <li>Day off "OFF" in the first cell</li>
    <li>Remove the headers of days, is only referential</li>

	<!--
    No schedules will be saved in the following cases:<br />
	<li>Agents that are not active</li>
	Schedules that do not meet the following conditions:
	<li> End of Duty time cannot be smaller than the Start time</li>
	<li> 'Break 1' must be before 'Lunch'</li>
	<li> 'Break 2' must be after 'Lunch' </li>-->
    </td></tr>
	<tr><td class="txtForm" align="center" colspan="2"><input type="button" onClick="upFile()" value="Upload data" class="btn" ></td></tr>
	<tr><td colspan="2" height="1px"></td></tr>
	</table>
</td></tr>
<tr><td height="1"><iframe id="frUP" height="1" name="frUP" src="no.php" style="visibility:hidden;"></iframe></td></tr>
</table>
</form>
