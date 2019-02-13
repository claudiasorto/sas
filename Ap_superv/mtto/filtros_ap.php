<table cellpadding="2" cellspacing="0" width="700" border="0" class="tblListBack" align="center">
<tr><td class="backTablaForm" colspan="2">Filtros para ver acciones de personal</td></tr>
<tr><td class="txtPag">Tipo de acción de personal: </td><td><select id="lsAp" class="txtPag"><option value="0">[TODOS]</option><!--optAp--></select></td></tr>
<tr><td class="txtPag">Cuenta: </td><td><!--optCuenta--></td></tr>
<tr><td class="txtPag">Departamento: &nbsp;</td><td><span id="lyDepart"><!--optDepto--></span></td></tr>
<tr><td class="txtPag">Empleado: </td><td><span id="lyempdep"><select id="lsAg" class="txtPag"><option value="0">[TODOS]</option><!--optAg--></select></span></td></tr>
<tr><td class="txtPag">Nombre de Empleado: </td><td><input type="text" id="txtEmp" class="txtPag" size="70" /></td></tr>
<tr><td class="txtPag">Badge: </td><td><input type="text" id="txtBadge" class="txtPag" size="25" /></td></tr>
<tr><td class="txtPag">Período Del:&nbsp;</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;Al:&nbsp;<input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag"  readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /> </td></tr>
<tr><td class="txtPag">Estado de la acci&oacute;n de personal:</td>
<td><select id="lsEstado" class="txtPag">
<option value="0">[TODOS]</option>
<option value="1">Aprobadas</option>
<option value="2">Perdientes de Aprobaci&oacute;n</option>
<option value="3">Rechazadas</option>
</select></td>
<tr><td class="txtPag">N&uacute;mero de AP:&nbsp;</td>
	<td><input type="text" id="txtNumAp" class="txtPag" size="70" /></td>
</tr>
<tr><td></td></tr>
<tr><td colspan="2" align="center"><input type="button" class="ui-corner-all btn" value="Generar Reporte" onClick="loadrpt()" ></td></tr></table>
<br>
<div id='datosrpt'></div>
<br>
<br>