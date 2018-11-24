<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="_self" action="mttoCallDetails/up_callDetails.php">
<table class="tblHead" width="700px" align="center" cellpadding="2" cellspacing="4">
<tr><td width="35%" align="right">Account: </td><td>
<select id="lsCuenta" name="lsCuenta" class="txtPag">
<option value="0">Select an account</option>
<option value="1">TERRACOM</option>
<option value="2">YOURTEL</option>
</select>
<tr><td width="35%" align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>
<tr><td align="center" colspan="2"><input type="button" onClick="upFile()" value="Upload data" class="btn" ></td></tr>
<tr><td colspan="2" align="center">Note: The file you upload must be in CSV<br>Date and time format dd/mm/yyyy hh:mm:ss</td></tr>

</table>

<iframe id="frUP" name="frUP" src="no.php" style="visibility:hidden;"></iframe>
</form>