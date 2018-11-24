<input type="text" id="idE" value="<!--idemp-->" style="visibility:hidden;">
<input type="text" id="idAp" value="<!--idap-->" style="visibility:hidden;">
<input type="text" id="idDisc" style="visibility:hidden;">

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
<tr><td align="right" class="txtPag"> Fecha:&nbsp; </td><td class="txtPag"><!--fecha--></td></tr>
<tr><td align="right" class="txtPag"> Tipo de Falta Disciplinaria:&nbsp; </td><td class="txtPag"><select id="lsFalta" class="txtPag" onchange="getDescFalta(this.value, <!--idemp--> )"><option value="0">Seleccione un tipo de falta disciplinaria</option><!--tp_falta--></select></td>
<td class="txtPag"><div id="lyDescripF"></div></td></tr>
<tr><td align="right" class="txtPag"> Tipo de Sanci&oacute;n&nbsp;</td><td class="txtPag" colspan="3">
<input type="radio" id="optDisc" name="optDisc" onclick="suspend(1)"/>&nbsp;Verbal&nbsp;&nbsp;&nbsp;
<input type="radio" id="optDisc" name="optDisc" onclick="suspend(2)"/>&nbsp; Escrita&nbsp;&nbsp;&nbsp;
<input type="radio" id="optDisc" name="optDisc" onclick="suspend(3)"/>&nbsp;Suspensi&oacute;n

</td></tr>

<tr class="txtPag"><td colspan="4" align="center">
    <div id="lySusp" style="display:none"> 
			D&iacute;as de Suspensi&oacute;n<input type="text" id="diasSusp" class="txtPag" onkeydown="return onlyNumber(this,event)" size="15"><br />
            Fecha de Inicio de Suspensi&oacute;n<input type="text" name="fecha_inicio" id="fecha_inicio" size="15" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_inicio', '%d/%m/%Y');" style="cursor:pointer;" />
	</div>
</tr></td>
<tr><td align="right" class="txtPag">Observaciones: </td><td  colspan="3"><textarea rows="4" cols="70" id="txtObserv" class="txtPag"></textarea></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="2" align="center" ><input type="button" class="ui-corner-all btn" value="Aceptar" onclick="sv_disciplinarias()" /></td></tr>
<tr><td></td></tr>
<tr><td colspan="4">
<div id="lyTpDisc" class="txtPag">
</div>
</td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td colspan="4" align="center" class="txtPag"><!--dataAp--></td></tr>
</table>

 