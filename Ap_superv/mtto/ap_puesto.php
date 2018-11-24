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
<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">
<tr><th colspan="4" class="showItem"><u><!--nombreap--></u></th></tr>
<tr><td align="right" class="txtPag">Tipo de Plaza:&nbsp;</td><td><select id="lsPlaza" class="txtPag"><!--optTipoPlaza--></select></td></tr>
<tr><td align="right" class="txtPag">Nueva Cuenta:&nbsp;</td><td class="txtPag"><!--optcuentaPuesto--></td></tr>
<tr><td align="right" class="txtPag">Nuevo Departamento:&nbsp; </td><td><span id="lyDepart"><!--optDeptoPuesto--></span></td></tr>
<tr><td align="right" class="txtPag">Nueva posici&oacute;n:&nbsp;</td><td><span id="lyPlaza"><select id="lsPosc" class="txtPag"><!--optPosicionPuesto--></select></span></td></tr>
<tr><td align="right" class="txtPag">Jefe Inmediato:&nbsp;</td><td class="txtPag"><span id="lySuperv"><!--optSupervisorPuesto--></span></td></tr>
<tr><td align="right" class="txtPag"> Efectivo desde:&nbsp;</td><td><input type="text" name="fecha_inicio" id="fecha_inicio" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_inicio', '%d/%m/%Y');" style="cursor:pointer;" /></td>
<tr><td align="right" class="txtPag">Salario:&nbsp;</td><td><input type="text" id="txtSalario" size="15"  class="txtPag" /></td></tr>
<tr><td align="right" class="txtPag">Duraci&oacute;n per&iacute;odo de prueba:&nbsp;</td><td><input type="text" id="txtPrueba"  class="txtPag" size="15" onkeydown="return onlyNumber(this,event)" /></td></tr>
<tr><td align="right" class="txtPag">Observaciones: </td><td colspan="3"><textarea rows="3" cols="70" id="txtObserv" class="txtPag"></textarea></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="4" align="center" ><input type="button" class="ui-corner-all btn" value="Aceptar" onclick="sv_puesto()" /></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="4" align="center" class="txtPag"><!--dataAp--></td></tr>
</table>

 
