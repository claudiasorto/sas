<input type="text" id="idE" value="<!--idemp-->" style="visibility:hidden;">
<input type="text" id="idAp" value="<!--idap-->" style="visibility:hidden;">

<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">
<tr><th colspan="4" class="showItem"><u>ACCION DE PERSONAL</u></th></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td align="right" class="txtPag">Badge de Empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--username--></td></tr>
<tr><td align="right" class="txtPag">Nombre de empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--apellido-->, &nbsp;<!--nombre--></td></tr>
<tr><td align="right" class="txtPag">Cuenta:&nbsp;</td><td class="txtPag" colspan="3"><!--cuenta--></td></tr>
<tr><td align="right" class="txtPag">Departamento:&nbsp;</td><td class="txtPag" colspan="3"><!--depto--></td></tr>
<tr><td align="right" class="txtPag">Posici&oacute;n:&nbsp;</td><td class="txtPag"><!--plaza--><td align="right" class="txtPag">Fecha:&nbsp;</td><td class="txtPag"><!--fecha--></td></tr>
</table>
<br /><br />
<tr><td></td></tr>
<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">
<tr><th colspan="4" class="showItem"><u><!--nombreap--></u></th></tr>
<tr><td align="right" class="txtPag">Del:&nbsp;</td>
<td class="txtPag"><input type="text" name="fecha_inicio" id="fecha_inicio" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_inicio', '%d/%m/%Y');" style="cursor:pointer;" /></td>
<tr><td align="right" class="txtPag">Al:&nbsp;</td>
<td class="txtPag"><input type="text" name="fecha_fin" id="fecha_fin" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td class="txtPag" align="right">Tipo:&nbsp;</td><td colspan="3"><select id="lstipo" class="txtPag" >
<option value="1">Inicial</option>
<option value="2">Prorroga</option>
</select></td></tr>
<tr class="txtPag" align="right"><td>Incapacidad de:&nbsp;</td><td colspan="3" align="left"><select id="lsIncap" class="txtPag"><!--optCenter--></select></td></tr>
<tr class="txtPag" align="right"><td>Horas a pagar:&nbsp;</td><td colspan="3" align="left"><select id="txtHoras" class="txtPag"><!--optHoras--></select></td></tr>
<tr><td align="right" class="txtPag">Observaciones: </td><td  colspan="3"><textarea rows="4" cols="70" id="txtObserv" class="txtPag"></textarea></td></tr>
<tr><td></td></tr>
<tr><td colspan="2" align="center" ><input type="button" class="ui-corner-all btn" value="Aceptar" onclick="sv_incapacidades()" /></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="4" align="center" class="txtPag"><!--dataAp--></td></tr>
</table>

 