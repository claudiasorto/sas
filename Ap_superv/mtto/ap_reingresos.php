<input type="text" id="idE" value="<!--idemp-->" style="visibility:hidden;">
<input type="text" id="idAp" value="<!--idap-->" style="visibility:hidden;">
<input type="text" id="idPxe" value="<!--idplxemp-->" style="visibility:hidden;">
<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">
<tr><th colspan="4" class="showItem"><u>ACCION DE PERSONAL</u></th></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td align="right" class="txtPag">Badge de Empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--username--></td></tr>
<tr><td align="right" class="txtPag">Nombre de empleado:&nbsp; </td><td class="txtPag" colspan="3"><!--apellido-->, &nbsp;<!--nombre--></td></tr>
<tr><td align="right" class="txtPag">&Uacute;ltima Cuenta:&nbsp;</td><td class="txtPag" colspan="3"><!--cuenta--></td></tr>
<tr><td align="right" class="txtPag">&Uacute;ltimo Departamento:&nbsp;</td><td class="txtPag" colspan="3"><!--depto--></td></tr>
<tr><td align="right" class="txtPag">&Uacute;ltima Posici&oacute;n:&nbsp;</td><td class="txtPag"><!--plaza--><td align="right" class="txtPag">Fecha:&nbsp;</td><td class="txtPag"><!--fecha--></td></tr>
</table>
<br /><br />
<tr><td></td></tr>
<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">
<tr><th colspan="4" class="showItem"><u><!--nombreap--></u></th></tr>
<tr><td align="right" class="txtPag"> Fecha de inicio:&nbsp;</td><td><input type="text" name="fecha_inicio" id="fecha_inicio" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_inicio', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td align="right" class="txtPag">Cuenta:&nbsp;</td><td><!--optcuenta--></td></tr>;
<tr><td align="right" class="txtPag">Departamento:&nbsp;</td><td><span id="lyDepart"><!--optDepto--></span></td></tr>
<tr><td align="right" class="txtPag">Nueva posici&oacute;n:&nbsp;</td><td><span id="lyPlaza"><select id="lsPosc" class="txtPag" disabled="disabled"><option value="0">Seleccione una posici&oacute;n</option></select></span></td></tr>
<tr><td align="right" class="txtPag">Supervisor:&nbsp;</td><td class="txtPag"><span id="lySuperv"><!--optSuperv--></span></td></tr>
<tr><td align="right" class="txtPag">Observaciones: </td><td  colspan="3"><textarea rows="4" cols="70" id="txtObserv" class="txtPag"></textarea></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="2" align="center" ><input type="button" class="ui-corner-all btn" value="Aceptar" onclick="sv_reingresos()" /></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="4" align="center" class="txtPag"><!--dataAp--></td></tr>
</table>

 