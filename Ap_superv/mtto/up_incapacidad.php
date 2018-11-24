<input type="text" id="apxemp" value="<!--apxemp-->" style="visibility:hidden;">

<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">
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
<tr><th colspan="4" class="showItem"><u><!--nom_ap--></u></th></tr>
<tr><td align="right" class="txtPag">Del:</td><td class="txtPag"><input type="text" name="fecha_ini" id="fecha_ini" size="15" class="txtPag" value="<!--startdate-->" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_ini', '%d/%m/%Y');" style="cursor:pointer;" /> </td></tr>
<tr><td align="right" class="txtPag">Al:</td><td class="txtPag"><input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag" value="<!--enddate-->" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right" class="txtPag">Tipo:&nbsp;</td><td><select id="tipo" class="txtPag" ><!--optTI--></select></td></tr></td></tr>
<tr><td align="right" class="txtPag">Incapacidad de:&nbsp;</td><td><select id="center" class="txtPag"><!--optCenter--></select></td></tr>
<tr class="txtPag" align="right"><td>Horas a pagar:&nbsp;</td><td colspan="3" align="left"><select id="txtHoras" class="txtPag"><!--optHoras--></select></td></tr>
<tr><td align="right" class="txtPag">Observaciones: </td><td  colspan="3"><textarea rows="4" cols="70" id="txtObserv" class="txtPag"><!--comment--></textarea></td></tr>

<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="4" align="center" ><input type="button" class="ui-corner-all btn" value="Actualizar" onclick="saveup_incapacidad()" /></td></tr>
</table>

 