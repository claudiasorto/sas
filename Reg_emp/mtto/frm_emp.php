
<table class="tblListBack" width="700" align="center">
<tr><td class="showItem" colspan="4">Formulario de Registro de Empleados</td></tr>
<tr><td class="itemForm">Badge de Empleado:&nbsp;</td><td colspan="3"><input type="text" class="txtPag" id="txtCod" size="25" style="text-transform:uppercase" value="<!--badge-->" ></td></tr>
<tr><td class="itemForm">Nombres:&nbsp;</td><td><input type="text" id="txtNombre" class="txtPag"  size="40" style="text-transform:uppercase"></td>
<td class="itemForm">Apellidos:&nbsp;</td><td><input type="text" id="txtApellido" class="txtPag"  size="40" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Status Empleado:&nbsp;</td><td colspan="3"><select id="lsStatus" class="txtPag"><!--optStatus--></select></td></tr>
<tr><td class="itemForm">Cuenta:&nbsp;</td><td colspan="3"><select id="lsCuenta" class="txtPag" onChange="getDepart(this.value)"><!--optCuenta--></select></td></tr>
<tr><td class="itemForm">Departamento:&nbsp;</td><td colspan="3"><span id="lyDepart"><select id="lsDepart" class="txtPag" disabled="disabled"><option value="0">Seleccione un Departamento:&nbsp;</option></select></span></td></tr>
<tr><td class="itemForm">Posici&oacute;n:&nbsp;</td><td colspan="3"><span id="lyPosc"><select id="lsPosc" class="txtPag" disabled="disabled"><option value="0">Seleccione una Posici&oacute;n:&nbsp;</option></select></span></td></tr>
<tr><td class="itemForm">Jefe Inmediato:&nbsp;</td><td colspan="3"><div id="lySuperv"></div></td></tr>
<tr><td class="itemForm">Fecha de Ingreso a la Empresa:&nbsp;</td><td colspan="3"><input type="text" id="fec_admis" class="txtPag" readonly="1" size="25" value="<!--fec_actual-->"><img src="images/calendar.jpg" onclick="return showCalendar('fec_admis', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Tipo de Plaza:&nbsp;</td><td colspan="3"><select id="lsTipoPlaza" class="txtPag"><!--optJobType--></select></td></tr>
<tr><td class="itemForm">Duraci&oacute;n de per&iacute;odo de prueba:&nbsp;</td><td colspan="3" class="txtPag"><input type="text" id="txtPrueba" class="txtPag" size="10" onkeydown="return onlyNumber(this,event)" />Meses</td></tr>
<tr><td class="itemForm">Salario:&nbsp;</td><td colspan="3"><input type="text" id="txtSalario" size="25"  class="txtPag" /></td></tr>
<tr><td class="itemForm">Bono:&nbsp;</td><td colspan="3"><input type="text" id="txtBono" size="25" class="txtPag" /></td></tr>
<tr><td class="itemForm">Pais:&nbsp;</td>
	<td colspan="3"><select id="lsCountry" class="txtPag"><option value="0">Seleccione un pais</option><!--optCountry--></select></td></tr>
<tr><td class="itemForm">N&uacute;mero de Cuenta:&nbsp;</td><td colspan="3"><input type="text" id="txtNumCuenta" size="25" class="txtPag" /></td></tr>
<tr><td class="itemForm">DUI:&nbsp;</td><td colspan="3"><input type="text" id="txtDui" size="25" class="txtPag"/></td></tr>
<tr><td class="itemForm">NIT:&nbsp;</td><td colspan="3"><input type="text" id="txtNit" size="25" class="txtPag"/></td></tr>
<tr><td class="itemForm">ISSS:&nbsp;</td><td colspan="3"><input type="text" id="txtIsss" size="25" class="txtPag"/></td></tr>
<tr><td class="itemForm">AFP Crecer:&nbsp;</td><td colspan="3"><input type="text" id="txtAFPcrecer" size="25" class="txtPag"/></td></tr>
<tr><td class="itemForm">AFP Confia:&nbsp;</td><td colspan="3"><input type="text" id="txtAFPconfia" size="25" class="txtPag"/></td></tr>
<tr><td class="itemForm">Carnet de Minoridad:&nbsp;</td><td colspan="3"><input type="text" id="txtCarnetMin" class="txtPag" size="25" /></td></tr>
<tr><td class="itemForm">IPSFA:&nbsp;</td><td colspan="3"><input type="text" id="txtIpsfa" class="txtPag" size="25" /></td></tr>
<tr><td class="itemForm">Fecha de Nacimiento:&nbsp;</td><td colspan="3"><input type="text" id="fec_nac" class="txtPag" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fec_nac', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Direcci&oacute;n:&nbsp;</td><td colspan="3"><textarea id="txtDireccion" class="txtPag" cols="80" rows="3" style="text-transform:uppercase"></textarea></td></tr>
<tr><td class="itemForm">Correo:&nbsp;</td><td colspan="3"><input type="text" size="40" id="txtEmail" /></td></tr> 
<tr><td class="itemForm">Recibir notificaciones?:&nbsp;</td>
	<td colspan="3">
	<select id="lsNotificacion" class="txtPag">
		<option value="N">N</option>
		<option value="Y">Y</option>
	</select>
	</td>
</tr> 
<tr><td class="itemForm">Celular:&nbsp;</td><td colspan="3"><input type="text" size="25" id="txtCel" /></td></tr>
<tr><td class="itemForm">Telefono fijo:&nbsp;</td><td colspan="3"><input type="text" size="25" id="txtTel" /></td></tr>
<tr><td class="itemForm">Profesi&oacute;n:&nbsp;</td><td colspan="3"><input type="text" id="txtProfesion" size="75" class="txtPag" style="text-transform:uppercase" /></td></tr>
<tr><td class="itemForm">Headset:&nbsp;</td><td colspan="3"><input type="text" id="txtHeadset" size="25"></td></tr>
<tr><td class="itemForm">N&deg; de Locker:&nbsp;</td><td colspan="3"><input type="text" id="txtLocker" size="25"></td></tr>
<tr><td class="itemForm">Agent ID</td><td colspan="3"><input type="text" id="txtAgentId" size="25"></td></tr>
<tr><td class="itemForm">Comentarios:&nbsp;</td><td colspan="3"><textarea id="txtComent" class="txtPag" cols="80" rows="3"></textarea></td></tr>
<tr><td colspan="4" class="showItem" title="Click para guardar"><input type="button" class="btn" onclick="save_emp()" value="Guardar nuevo empleado" /></td></tr>
</table>
