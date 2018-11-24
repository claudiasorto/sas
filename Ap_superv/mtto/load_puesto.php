<input type="text" id="idE" value="<!--id_emp-->" style="visibility:hidden;">
<input type="text" id="idAp" value="<!--idap-->" style="visibility:hidden;">
<input type="text" id="apxemp" value="<!--apxemp-->" style="visibility:hidden;">

<table cellpadding="2" cellspacing="0" width="650" border="0" class="tblListBack" align="center">
<tr><th colspan="4" class="showItem"><u>ACCION DE PERSONAL</u></th></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td align="right" class="txtPag" colspan="4">N&deg;:<!--apxemp--></td></tr>
<tr><td align="right" class="txtPag">Badge de Empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--username--></td></tr>
<tr><td align="right" class="txtPag">Nombre de empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--last-->, &nbsp;<!--first--></td></tr>
<tr><td align="right" class="txtPag">Cuenta:&nbsp;</td><td class="txtPag" colspan="3"><!--cuenta--></td></tr>
<tr><td align="right" class="txtPag">Departamento:&nbsp;</td><td class="txtPag" colspan="3"><!--depto--></td></tr>
<tr><td align="right" class="txtPag">Posici&oacute;n:&nbsp;</td><td class="txtPag"><!--plaza--><td align="right" class="txtPag">Fecha:&nbsp;</td><td class="txtPag"><!--storage--></td></tr>
<br /><br />
<tr><td colspan="4" align="right">
<!--btn_back-->
<input type="image" src="images/update.png" width="40" alt="Actualizar" onclick="update_ap()" style="cursor:pointer;" title="Pulse para actualizar" align="absmiddle"/>&nbsp;&nbsp;
<a href='mtto/imppuesto.php?Idapxemp=<!--apxemp-->&user=<!--username-->&last=<!--last-->&first=<!--first-->&cuenta=<!--cuenta-->&depto=<!--depto-->&plaza=<!--plaza-->&cuentaOld=<!--cuentaOldPuesto-->&departOld=<!--departOldPuesto-->&posicionOld=<!--posicionOldPuesto-->&supOld=<!--SupervisorOldPuesto-->&cuentaNew=<!--cuentaNewPuesto-->&departNew=<!--departNewPuesto-->&posicionNew=<!--posicionNewPuesto-->&supNew=<!--SupervisorNewPuesto-->&fecha=<!--storage-->&nom_ap=<!--nom_ap-->&tipo_plaza=<!--tipo_plaza-->&start=<!--startdate-->&salario=<!--salario-->&prueba=<!--prueba-->&observ=<!--comment-->' target="_blank"><img src="images/print.png" border="0" width="50" style="cursor:pointer;" title="Pulse para imprimir" align="absmiddle" /></a>
</td></tr>
<tr><th colspan="4" class="showItem"><u><!--nom_ap--></u></th></tr>
<tr><td align="right" class="txtPag"><font color="#993300">Cuenta anterior: </td><td class="txtPag"><font color="#993300"><!--cuentaOldPuesto--></td></tr>
<tr><td align="right" class="txtPag"><font color="#993300">Departamento anterior: </td><td class="txtPag"><font color="#993300"><!--departOldPuesto--></td></tr>
<tr><td align="right" class="txtPag"><font color="#993300">Posici&oacute;n anterior: </td><td class="txtPag"><font color="#993300"><!--posicionOldPuesto--></td></tr>
<tr><td align="right" class="txtPag"><font color="#993300">Jefe anterior: </td><td class="txtPag"><font color="#993300"><!--SupervisorOldPuesto--></td></tr>

<tr><td align="right" class="txtPag">Nueva Cuenta:&nbsp;</td><td class="txtPag"><!--cuentaNewPuesto--></td></tr>
<tr><td align="right" class="txtPag">Nuevo Departamento:&nbsp; </td><td class="txtPag"><!--departNewPuesto--></td></tr>
<tr><td align="right" class="txtPag">Nueva posici&oacute;n:&nbsp;</td><td class="txtPag"><!--posicionNewPuesto--></td></tr>
<tr><td align="right" class="txtPag">Jefe Inmediato:&nbsp;</td><td class="txtPag"><!--SupervisorNewPuesto--></td></tr>
<tr><td align="right" class="txtPag">Tipo de Plaza:&nbsp;</td><td class="txtPag"><!--tipo_plaza--></td></tr>
<tr><td align="right" class="txtPag">Efectivo desde:&nbsp;</td><td class="txtPag"><!--startdate--></td></tr>
<tr><td align="right" class="txtPag">Salario:</td><td class="txtPag"><!--salario--></td></tr>
<tr><td align="right" class="txtPag">Duraci&oacute;n per&iacute;odo de prueba:&nbsp;</td><td class="txtPag"><!--prueba--></td></tr>
<tr><td align="right" class="txtPag">Observaciones: </td><td  colspan="3" class="txtPag"><textarea rows="8" cols="70" id="txtObserv" class="txtPag" disabled="disabled"><!--comment--></textarea></td></tr>
<tr><td><br /></td></tr>
<tr><td colspan="2" align="center" title="Clic para autorizar acci&oacute;n de personal"><!--btn_autor--></td>
<td colspan="2" align="center" title="Clic para rechazar acci&oacute;n de personal"><!--btn_rechaz--></td></tr>
<tr><td colspan="4" align="center" class="txtPag">
<div id="lyComentRechazo" style="display:none">
	Observaciones:<br /> <textarea class="txtPag" id="txtComentRechazo" cols="50" rows="3"></textarea>
    <br /><input type="button" class="btn" onclick="sv_AutorizacionRechazo()" value="Guardar Comentario"/><br /><br /><br />
</div>
</td></tr>
<!--firmas-->
<tr><td></td></tr>
<tr><td></td></tr>
</table>

 