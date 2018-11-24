<table width="800" align="center" cellpadding="4" cellspacing="4" class="tablaVerde">
<tr class="thVerde"><td colspan="2" align="center">Formulario para configurar descuentos de ley</td></tr>
<tr><td align="right">Pa&iacute;s: </td>
	<td><span id="lyCountry"><select id="lsCountry"><option value="0">Seleccione un pa&iacute;s</option><!--optCountry--></select></span></td></tr>
<tr><td align="right">Nombre del descuento: </td>
	<td><input type="text" id="txtName"></td></tr>
<tr><td align="right">C&aacute;lculo sobre: </td>
	<td><select id="lsFlagCalculo">
		<option value="NA">Seleccione una opci&oacute;n</option>
		<option value="N">Remuneraciones no gravadas</option>
		<option value="Y">Remuneraciones gravadas</option>
	</select></td></tr>
<tr><td align="right">% de Descuento: </td>
	<td><input type="text" id="txtPerc"></td></tr>
<tr><td align="right">Monto desde: </td>
	<td><input type="text" id="txtBottonAmount"></td></tr>
<tr><td align="right">Monto hasta: </td>
	<td><input type="text" id="txtTopAmount"></td></tr>
<tr><td align="right">Sobre exceso de: </td>
	<td><input type="text" id="txtOverExcess"></td></tr>
<tr><td align="right">Cuota fija: </td>
	<td><input type="text" id="txtFixedFee"></td></tr>	
<tr><td align="right">Descuento de pensi&oacute;n?: </td>
	<td><select id="lsFlagPension">
		<option value="N">No</option>
		<option value="Y">Si</option>
	</select></td></tr>
<tr><td align="right">Maximo cotizable: </td>
	<td><input type="text" id="txtMaxQuotable"></td></tr>	
<tr><td align="right">Fecha: </td>
	<td><input type="text" name="feIni" id="fecIni" size="15" class="txtPag" readonly="1" value="<!--sysdate-->" /><img src="images/calendar.jpg" align="center" onclick="return showCalendar('fecIni', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td colspan="2" align="center"><input type="button" onclick="saveLegalDisc()" value="Guardar"></td></tr>
</table><br><br>
<div id="tblDiscounts"><!--tblDisc--></div>
<div id="lyData"></div>
