<input type="text" id="idE" value="<!--id_emp-->" style="visibility:hidden;">
<input type="text" id="idAp" value="<!--idap-->" style="visibility:hidden;">
<input type="text" id="apxemp" value="<!--apxemp-->" style="visibility:hidden;">

<table cellpadding="2" cellspacing="0" width="90%" border="0" class="tblListBack" align="center">
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
<a href='mtto/imppermiso.php?Idapxemp=<!--apxemp-->&user=<!--username-->&last=<!--last-->&first=<!--first-->&cuenta=<!--cuenta-->&depto=<!--depto-->&plaza=<!--plaza-->&fecha=<!--storage-->&nom_ap=<!--nom_ap-->&start=<!--startdate-->&end=<!--enddate-->&horas=<!--horas-->&observ=<!--comment-->' target="_blank"><img src="images/print.png" border="0" width="50" style="cursor:pointer;" title="Pulse para imprimir" align="absmiddle" /></a>
</td></tr>
<tr><th colspan="4" class="showItem"><u><!--nom_ap--></u></th></tr>
<tr><td align="right" class="txtPag">Del:</td><td class="txtPag"><!--startdate--></td></tr>
<tr><td align="right" class="txtPag">Al:</td><td class="txtPag"><!--enddate--></td></tr>
<tr><td align="right" class="txtPag">Horas:</td><td class="txtPag"><!--horas--></td></tr>
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
<tr><td align="rigth" >
<form target="_blank" action="report/xls_permiso.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="25" style="cursor:pointer" title="Exportar a excel" /><input type="hidden" name="apxemp" value="<!--apxemp-->">
<input type="hidden" name="username" value="<!--username-->">
<input type="hidden" name="nombre" value="<!--first-->">
<input type="hidden" name="apellido" value="<!--last-->">
<input type="hidden" name="cuenta" value="<!--cuenta-->">
<input type="hidden" name="depto" value="<!--depto-->">
<input type="hidden" name="plaza" value="<!--plaza-->">
<input type="hidden" name="storage" value="<!--storage-->">
<input type="hidden" name="nomap" value="<!--nom_ap-->">
<input type="hidden" name="start" value="<!--startdate-->">
<input type="hidden" name="end" value="<!--enddate-->">
<input type="hidden" name="horas" value="<!--horas-->">
<input type="hidden" name="observ" value="<!--comment-->">
</form>&nbsp;&nbsp;
</td></tr>
</table>

 