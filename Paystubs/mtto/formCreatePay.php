<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="frUP" action="mtto/up_Pay2.php">
<table width="800" align="center" cellpadding="2" cellspacing="2" class="tablaVerde">
<tr bgcolor="#8FBC8F"><td colspan="2" align="center"><font color="#FFFFFF">UPLOAD PAY STUBS</font></td></tr>
<tr><td align="right">Delivery date payslip</td>
<td colspan="2">
<span id="selDate"><select id="lsDelivery" name="lsDelivery" onChange="showUpdBtn(this.value)">
<option value="0">Select a date</option><!--payroll_date--></select></span>
<input type="button" onClick="newRegPaystub()" value="Create Paystub"/>
<span id="lyBtnUpd">
</span>
</td></tr>
<tr><td colspan="3" ></td></tr>
<tr><td align="right">Salary discounts: </td><td><input type="file" name="flDescuento" id="flDescuento" size="25" class="txtPag"/></td></tr>
<tr><td align="right">Bono: </td><td><input type="file" name="flBono" id="flBono" size="25" class="txtPag" /></td></tr>
<tr><td align="right">Aguinaldo: </td><td><input type="file" name="flAguinaldo" id="flAguinaldo" size="25" class="txtPag" /></td></tr>
<tr><td align="right">Severance: </td><td><input type="file" name="flSeverance" id="flSeverance" size="25" class="txtPag" /></td></tr>
<tr><td align="right">Other Income: </td><td><input type="file" name="flOtherIncome" id="flOtherIncome" size="25" class="txtPag" /></td></tr>
<!--<tr><td align="right">Seventh day discount: </td><td><input type="file" name="flSeven" id="flSeven" size="25" class="txtPag"/></td></tr>  -->
<!--tblDiscount-->

<tr><td align="center" colspan="2"><input type="button" onClick="upFile()" value="Upload data" ></td></tr>
</table><br />
<iframe id="frUP" name="frUP" src="no.php" height="2px" style="visibility:hidden;"></iframe>
</form>
<!--Form para crear nuevo paystub-->
<div id="lyCreate" style="display:none">
<table width="600" class="tablaVerde" align="center" cellpadding="4" cellspacing="4"
  style="font-size: 11px;
  font-family: Tahoma;
  font-weight: 800;
  color: #666;">
<tr class="thVerde"><td colspan="5" align="center">FORM TO CREATE NEW PAYSTUB</td></tr>

<tr><td align="right">Delivery date:</td>
  <td><input type="text" name="fechaEntrega" id="fechaEntrega" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaEntrega', '%d/%m/%Y');" style="cursor:pointer;" align="center" /></td></tr>
<tr><td align="right">Period start date:</td>
  <td><input type="text" name="fechaIni" id="fechaIni" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaIni', '%d/%m/%Y');" style="cursor:pointer;" align="center" /></td></tr>
<tr><td align="right">Period end date:</td><td><input type="text" name="fechaFin" id="fechaFin" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fechaFin', '%d/%m/%Y');" style="cursor:pointer;" align="center"/></td></tr>
<tr><td colspan="2" align="center"><input type="button" value="Save new paystub" id="btnSavePaystub" onClick="saveNewPaystub()"/>
</td></tr>
</table>
</div>

<!--Form para actualizar paystub-->
<div id="lyUpdate" style="display:none">
<table width="600" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4"
  style="font-size: 11px;
  font-family: Tahoma;
  font-weight: 800;
  color: #666;">
<tr><td>
FORM TO UPDATE PAYSTUB<br>
Delivery date: <input type="text" name="fechaEntregaUpd" id="fechaEntregaUpd" size="15" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onclick="return showCalendar('fechaEntregaUpd', '%d/%m/%Y');" style="cursor:pointer;" /><br>
Period start date: <input type="text" name="fechaIniUpd" id="fechaIniUpd" size="15" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onclick="return showCalendar('fechaIniUpd', '%d/%m/%Y');" style="cursor:pointer;" /><br />
Period end date: <input type="text" name="fechaFinUpd" id="fechaFinUpd" size="15" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onclick="return showCalendar('fechaFinUpd', '%d/%m/%Y');" style="cursor:pointer;" /><br>
<input type="button" value="Save" id="btnUpdPaystub" onClick="saveUpdatePaystub()"/>
</td></tr>
</table>
</div>

<!--Mensaje de espera de carga -->
<div class="loadP" id="lyMsg" style="display:none;">
<table align="center">
<tr><td align="center">
<img src="images/PleaseWait.gif" width="400">
</td></tr>
</table>
</div>
