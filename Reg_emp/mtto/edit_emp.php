<table class="tblListBack" width="700" align="center">
<td class="showItem" colspan="2" align="center"><b>ACTUALIZAR REGISTRO DE EMPLEADO</b></td>
</tr>
<tr><td class="itemForm" width="200">Badge:&nbsp;</td><td class="txtResalt"><input type="text" id="txtBadge" value="<!--username-->" class="txtPag" size="15" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Nombre:&nbsp;</td><td class="txtResalt"><input type="text" id="txtNombre" value="<!--nombre-->" class="txtPag" size="25" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Apellido:&nbsp;</td><td class="txtResalt"><input type="text" id="txtApellido" value="<!--apellido-->" class="txtPag" size="25" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Estado: </td><td class="txtResalt"><!--optEstado--></td></tr>
<tr><td class="itemForm">Cuenta:&nbsp;</td><td class="txtResalt"><!--optcuenta--></td></tr>
<tr><td class="itemForm">Departamento:&nbsp;</td><td class="txtResalt"><span id="lyDepart"><!--optDepto--></span></td></tr>
<tr><td class="itemForm">Posici&oacute;n:&nbsp;</td><td class="txtResalt"><span id="lyPosc"><!--optPosicion--></span></td></tr>
<tr><td class="itemForm">Supervisor:&nbsp;</td><td class="txtResalt"><div id="lySuperv"><!--optSup--></div></td></tr>
<tr><td class="itemForm">Tipo de Plaza:&nbsp;</td><td class="txtResalt"><!--optTipoPlaza--></td></tr>
<tr><td class="itemForm">Fecha de Ingreso a la Empresa:&nbsp;</td><td class="txtResalt"><input type="text" id="fec_admis" class="txtPag" value="<!--fec_admis-->" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fec_admis', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Duraci&oacute;n de per&iacute;odo de prueba:&nbsp;</td><td class="txtResalt"><input type="text" id="txtPrueba" value="<!--per_prueba-->" class="txtPag" size="10" onkeydown="return onlyNumber(this,event)"> Meses</td></tr>
<tr><td class="itemForm">Salario:&nbsp;</td><td class="txtResalt"><input type="text" id="txtSalario" value="<!--salario-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">Bono:&nbsp;</td><td class="txtResalt"><input type="text" id="txtBono" value="<!--bono-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">Pais:&nbsp;</td>
	<td class="txtResalt"><select id="lsCountry" class="txtPag"><!--optCountry--></select></td></tr>
<tr><td class="itemForm">N&uacute;mero de Cuenta:&nbsp;</td><td class="txtResalt"><input id="txtCta" type="text" value="<!--num_cuenta-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">DUI:&nbsp;</td><td class="txtResalt"><input type="text" id="txtDui" value="<!--dui-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">NIT:&nbsp;</td><td class="txtResalt"><input type="text" id="txtNit" value="<!--nit-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">ISSS:&nbsp;</td><td class="txtResalt"><input type="text" id="txtIsss" value="<!--isss-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">AFP Crecer:&nbsp;</td><td class="txtResalt"><input type="text" id="txtCrecer" value="<!--afpcrecer-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">AFP Confia:&nbsp;</td><td class="txtResalt"><input type="text" id="txtConfia" value="<!--afpconfia-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">Carnet de Minoridad:&nbsp;</td><td class="txtResalt"><input type="text" id="txtMinor" value="<!--minoridad-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">IPSFA:&nbsp;</td><td class="txtResalt"><input type="text" id="txtIpsfa" value="<!--ipsfa-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">Fecha de Nacimiento:&nbsp;</td><td class="txtResalt"><input type="text" id="fec_nac" class="txtPag" value="<!--fec_nac-->" readonly="1" size="25"><img src="images/calendar.jpg" onclick="return showCalendar('fec_nac', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Direcci&oacute;n:&nbsp;</td><td class="txtResalt"><textarea id="txtDireccion" class="txtPag" cols="75" rows="3" style="text-transform:uppercase"><!--direccion--></textarea></td></tr>
<tr><td class="itemForm">Correo:&nbsp;</td><td class="txtResalt"><input type="text" id="txtEmail" value="<!--email-->" class="txtPag" size="40"></td></tr>
<tr><td class="itemForm">Recibir notificaciones?:&nbsp;</td>
	<td colspan="3">
	<select id="lsNotificacion" class="txtPag">
		<!--notificationOpt-->
	</select>
	</td>
</tr> 
<tr><td class="itemForm">Celular:&nbsp;</td><td class="txtResalt"><input type="text" id="txtCelular" value="<!--celular-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">Telefono fijo:&nbsp;</td><td class="txtResalt"><input type="text" id="txtTel" value="<!--tel_casa-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">Profesi&oacute;n:&nbsp;</td><td class="txtResalt"><input type="text" id="txtProfesion" value="<!--profesion-->" class="txtPag" size="40" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">N&deg; de Locker:&nbsp;</td><td class="txtResalt"><input type="text" id="txtLocker" value="<!--locker-->" class="txtPag" size="25"></td></tr>
<tr><td class="itemForm">Agent ID:&nbsp;</td><td class="txtResalt"><input type="text" id="txtAgentID" value="<!--agentID-->" class="txtPag" size="25"></td></tr>
<!--<tr><td class="itemForm">Incontact ID: </td><td class="txtResalt"><input type="text" class="txtPag" id="txtPhoneLogin" value="<!--phone_login-->
<!--" /></td></tr> -->
<tr><td class="showItem" colspan="2" align="center" class="showItem"><input type="button" class="btn" value="Actualizar registro de empleado" onclick="sv_updateemp(<!--id_empleado-->)" /></td></tr>
</table>



