<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="frUP" action="payroll/up_nighthours.php">
<table cellpadding="0" cellspacing="0" align="center">
<tr><td height="10"></td></tr>
  <tr><td height="1" bgcolor="#CCCCCC"></td></tr>
  <tr><td height="10"></td></tr>
  <tr><td style="padding-left:5px;">
	<table width="700" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">
	<tr><td class="txtForm" colspan="2" align="center">UPLOAD PAYROLL NIGHT HOURS</td></tr>
	<tr><td class="txtForm" align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>
	<tr><td colspan="2"><i>The format of the file must be in csv without headers</i></td></tr>
    <tr><td colspan="2">
	<table class="backTablaMain" cellpadding="4" cellspacing="4">
 	<tr>
    <td align="center">Badge</td>
    <td align="center">Date in format DD/MM/YYYY</td>
    <td align="center">Total hours</td></tr>
    <tr class="txtPag"><td>CSXXXX</td>
    <td>01/01/2017</td>
    <td>4</td></tr>
    </table>
	</td></tr>
	<tr><td class="txtForm" align="center" colspan="2"><input type="button" onClick="upFile()" id="btnUp" value="Upload data" class="btn" ></td></tr>
 	<tr><td colspan="2" height="1px"></td></tr>
	<tr>
	<td colspan="2" align="center"><div class="loadP" id="lyMsg" style="display:none;">Uploading file ...</div></td></tr>
	</table>
</td></tr>
<tr><td height="1"><iframe id="frUP" height="1" name="frUP" src="no.php" style="visibility:hidden;"></iframe></td></tr>
</table>
</form>

