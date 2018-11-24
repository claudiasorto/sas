<input type="text" id="idE" value="<!--id_emp-->" style="visibility:hidden;">
<input type="text" id="idAp" value="<!--idap-->" style="visibility:hidden;">
<input type="text" id="apxemp" value="<!--apxemp-->" style="visibility:hidden;">

<table cellpadding="2" cellspacing="0" width="700" border="0" class="tblListBack" align="center">
<tr><th colspan="4" class="showItem"><u>ACCION DE PERSONAL</u></th></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td align="right" class="txtPag" colspan="4">N&deg;:<!--apxemp--></td></tr>
<tr><td align="right" class="txtPag">Badge de Empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--username--></td></tr>
<tr><td align="right" class="txtPag">Nombre de empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--last-->, &nbsp;<!--first--></td></tr>
<tr><td align="right" class="txtPag" colspan="3">Fecha:&nbsp;</td><td class="txtPag"><!--storage--></td></tr>
<br /><br />
<tr><td colspan="4" align="right">
	<!--btn_back-->
<input type="image" src="images/update.png" width="40" alt="Actualizar" onclick="update_ap()" style="cursor:pointer;" title="Pulse para actualizar" align="absmiddle"/>&nbsp;
<a href='mtto/impreingreso.php?Idapxemp=<!--apxemp-->&user=<!--username-->&last=<!--last-->&first=<!--first-->&cuenta=<!--cuenta-->&depto=<!--depto-->&plaza=<!--plaza-->&fecha=<!--storage-->&nom_ap=<!--nom_ap-->&start=<!--startdate-->&observ=<!--comment-->' target="_blank"><img src="images/print.png" border="0" width="50" style="cursor:pointer;" title="Pulse para imprimir" align="absmiddle" /></a>&nbsp;

</td></tr>
<tr><th colspan="4" class="showItem"><u><!--nom_ap--></u></th></tr>
<tr><td  class="txtPag" align="rigth">Efectivo desde:&nbsp;</td><td class="txtPag"><!--startdate--></td></tr>
<tr><td  class="txtPag" align="rigth">Cuenta:&nbsp;</td><td class="txtPag"><!--cuenta--></td></tr>
<tr><td  class="txtPag" align="rigth">Departamento :&nbsp;</td><td class="txtPag"><!--depto--></td></tr>
<tr><td  class="txtPag" align="rigth">Posici&oacute;n:&nbsp;</td><td class="txtPag"><!--plaza--></td></tr>
<tr><td  class="txtPag">Observaciones: </td><td  colspan="3" class="txtPag"><textarea rows="8" cols="70" id="txtObserv" class="txtPag" disabled="disabled"><!--comment--></textarea></td></tr>
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

 