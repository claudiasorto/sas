<input type="hidden" id="flagUpdate" value="<!--btn_update-->" />
<table class="tblListBack" width="700" align="center">
<tr><td align="center">
<input type="image" src="images/updates.png" width="50" alt="Actualizar" onclick="update_emp(<!--id_empleado-->)" style="cursor:pointer;" title="Pulse para actualizar" align="absmiddle"/>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="mtto/imp_empleado.php?us=<!--username-->&ap=<!--apellido-->&nombre=<!--nombre-->&estatus=<!--estatus-->&
cuenta=<!--nom_cuenta-->&depto=<!--nom_depart-->&plaza=<!--nom_plaza-->&supervisor=<!--supervisor-->&tipo_plaza=<!--tipoPlaza-->
&admis=<!--fec_admis-->&salario=<!--salario-->&bono=<!--bono-->&cta=<!--num_cuenta-->&dui=<!--dui-->&nit=<!--nit-->&isss=<!--isss-->&crecer=<!--afpcrecer-->&confia=<!--afpconfia-->&minor=<!--minoridad-->&ipsfa=<!--ipsfa-->&fec_nac=<!--fec_nac-->&direccion=<!--direccion-->&email=<!--email-->&cel=<!--celular-->&tel=<!--tel_casa-->&profesion=<!--profesion-->&locker=<!--locker-->" target="_blank"><img src="images/Print.png" border="1" width="50" style="cursor:pointer;" title="Pulse para imprimir" align="absmiddle" />
</td>
<td class="showItem"><b>HOJA DE REGISTRO DE EMPLEADO</b>
<div id="lyPhoto"><!--fotoEmpleado-->

</div>
<div id="lyFormPhoto"></div>

</td>
</tr>
<tr><td class="itemForm" width="200">Badge:&nbsp;</td><td class="txtResalt"><!--username--></td></tr>
<tr><td class="itemForm">Nombre:&nbsp;</td><td class="txtResalt"><!--apellido-->, <!--nombre--></td></tr>
<tr><td class="itemForm">Estatus de Empleado:&nbsp;</td><td class="txtResalt"><!--estatus--></td></tr>
<tr><td class="itemForm">Cuenta:&nbsp;</td><td class="txtResalt"><!--nom_cuenta--></td></tr>
<tr><td class="itemForm">Departamento:&nbsp;</td><td class="txtResalt"><!--nom_depart--></td></tr>
<tr><td class="itemForm">Posici&oacute;n:&nbsp;</td><td class="txtResalt"><!--nom_plaza--></td></tr>
<tr><td class="itemForm">Supervisor:&nbsp;</td><td class="txtResalt"><!--supervisor--></td></tr>
<tr><td class="itemForm">Tipo de Plaza:&nbsp;</td><td class="txtResalt"><!--tipoPlaza--></td></tr>
<tr><td class="itemForm">Fecha de Ingreso a la Empresa:&nbsp;</td><td class="txtResalt"><!--fec_admis--></td></tr>
<tr><td class="itemForm">Per&iacute;odo de prueba (meses):&nbsp;</td><td class="txtResalt"><!--per_prueba--></td></tr>
<tr><td class="itemForm">Fecha de Egreso:&nbsp;</td><td class="txtResalt"><!--fec_egreso--></td></tr>
<tr><td class="itemForm">Salario:&nbsp;</td><td class="txtResalt"><!--salario--></td></tr>
<tr><td class="itemForm">Bono:&nbsp;</td><td class="txtResalt"><!--bono--></td></tr>
<tr><td class="itemForm">Pais:&nbsp;</td><td class="txtResalt"><!--pais--></td></tr>
<tr><td class="itemForm">N&uacute;mero de Cuenta:&nbsp;</td><td class="txtResalt"><!--num_cuenta--></td></tr>
<tr><td class="itemForm">DUI:&nbsp;</td><td class="txtResalt"><!--dui--></td></tr>
<tr><td class="itemForm">NIT:&nbsp;</td><td class="txtResalt"><!--nit--></td></tr>
<tr><td class="itemForm">ISSS:&nbsp;</td><td class="txtResalt"><!--isss--></td></tr>
<tr><td class="itemForm">AFP Crecer:&nbsp;</td><td class="txtResalt"><!--afpcrecer--></td></tr>
<tr><td class="itemForm">AFP Confia:&nbsp;</td><td class="txtResalt"><!--afpconfia--></td></tr>
<tr><td class="itemForm">Carnet de Minoridad:&nbsp;</td><td class="txtResalt"><!--minoridad--></td></tr>
<tr><td class="itemForm">IPSFA:&nbsp;</td><td class="txtResalt"><!--ipsfa--></td></tr>
<tr><td class="itemForm">Fecha de Nacimiento:&nbsp;</td><td class="txtResalt"><!--fec_nac--></td></tr>
<tr><td class="itemForm">Direcci&oacute;n:&nbsp;</td><td class="txtResalt"><!--direccion--></td></tr>
<tr><td class="itemForm">Correo:&nbsp;</td><td class="txtResalt"><!--email--></td></tr>
<tr><td class="itemForm">Recibir notificaciones?:&nbsp;</td><td class="txtResalt"><!--notificationFlag--></td></tr>
<tr><td class="itemForm">Celular:&nbsp;</td><td class="txtResalt"><!--celular--></td></tr>
<tr><td class="itemForm">Telefono fijo:&nbsp;</td><td class="txtResalt"><!--tel_casa--></td></tr>
<tr><td class="itemForm">Profesi&oacute;n:&nbsp;</td><td class="txtResalt"><!--profesion--></td></tr>
<tr><td class="itemForm">N&deg; de Locker:&nbsp;</td><td class="txtResalt"><!--locker--></td></tr>
<tr><td class="itemForm">Agent ID</td><td class="txtResalt"><!--agentID--></td></tr>
<tr><td class="itemForm">Adjuntos:</td>
<td title="Click para agregar nuevo adjunto" style="cursor:pointer;">
<input class="btn" onclick="formNewAttach(<!--id_empleado-->)" type="button" value="+"></td></tr>
<tr><td class="itemForm"></td><td class="txtResalt"><div id="lyNewAttach"/></td></tr>
<tr><td class="itemForm"></td><td class="txtResalt"><div id="lyDocs"><!--docList--></div></td></tr>
</table>



