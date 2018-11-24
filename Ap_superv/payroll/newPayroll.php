<form method="POST" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="frUP" action="payroll/up_PayrollBatch.php" >
<table cellpadding="0" cellspacing="0" align="center">
<tr><td height="10"></td></tr>
  <tr><td height="1" bgcolor="#CCCCCC"></td></tr>
  <tr><td height="10"></td></tr>
  <tr><td style="padding-left:5px;"> 
	<table width="700" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">
	<tr><td class="txtForm" colspan="2" align="center">UPLOAD PAYROLL</td></tr>
	<tr><td class="txtForm" width="30%" align="right">Date: </td><td><input type="text" name="fecha" id="fecha" size="25" class="txtPag" readonly="1" /><img src="images/calendar.jpg" 			onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr> 
	<tr><td class="txtForm" align="right">Payroll Format: </td><td><span id="lyPayrollType"><!--optTp--></span></td></tr>
	<tr><td class="txtForm" align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>
	<tr><td colspan="2"><i>The file format must be csv with comma delimiter</i></td></tr>
	<tr><td colspan="2"><i>Workforce format: Badge, Account, Daytime hours (example 8.5), Night hours</i></td></tr>
	<tr><td class="txtForm" align="center" colspan="2"><input type="button" onClick="upFileWait()" id="btnUp" value="Upload data" class="btn" ></td></tr> 
	<tr><td colspan="2" height="1px"></td></tr>
	<tr>
	<td colspan="2" align="center"><div class="loadP" id="lyMsg" style="display:none;">
	<img src="images/PleaseWait.gif" width="400">
	</div></td></tr>
	</table>
</td></tr>
<tr><td height="1"><iframe id="frUP" height="1" name="frUP" src="no.php" style="visibility:hidden;"></iframe></td></tr>
</table>

</form>
