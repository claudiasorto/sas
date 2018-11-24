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
<tr><td align="right" class="txtPag">Tipo de Falta Disciplinaria:&nbsp;</td><td class="txtPag">
<select id="lsTpDisc" class="txtPag"><!--optTipoDisciplinaria--></select>
</td></tr>
<tr><td align="right" class="txtPag">Tipo de Sanci&oacute;n:&nbsp;</td><td class="txtPag">
<select id="lsTpSancion" class="txtPag" onchange="getBlockSuspension(this.value)"><!--optTipoSancion--></select></td></tr>
<tr><td colspan="2" class="txtPag">
<div id="lySuspen" <!--styleSuspension--> >
<br />
D&iacute;as de Suspensi&oacute;n: <input type="text" id="diasSusp" class="txtPag" value="<!--DiasSuspension-->" onkeydown="return onlyNumber(this,event)" size="15"><br />
Fecha de Inicio de Suspensi&oacute;n: <input type="text" name="fecha_inicio" id="fecha_inicio" value="<!--FechaSuspension-->" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_inicio', '%d/%m/%Y');" style="cursor:pointer;" />
</div>
</td></tr>
<tr><td>
<tr><td align="right" class="txtPag">Observaciones: </td><td  colspan="3" class="txtPag"><textarea rows="8" cols="70" id="txtObserv" class="txtPag"><!--comment--></textarea></td></tr>
<tr><td><br /></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="4" align="center" ><input type="button" class="ui-corner-all btn" value="Actualizar" onclick="saveup_disciplinaria()" /></td></tr>
</table>

 