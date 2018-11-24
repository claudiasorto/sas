<input type="text" id="idE" value="<!--idemp-->" style="visibility:hidden;">
<input type="text" id="idAp" value="<!--idap-->" style="visibility:hidden;">
<table cellpadding="2" cellspacing="0" width="700" border="0" class="tblListBack" align="center">
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
<table cellpadding="2" cellspacing="0" width="700" border="0" align="center" bgcolor="#FFFFFF">
<tr><th colspan="4" align="right" class="txtPag">San Salvador, <!--fecActualLetras--> </th></tr>

<tr><th colspan="4"><u><!--nombreap--></u></th></tr>
<tr><td colspan="4" class="txtPag" style="font-size:13px" align="justify">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Por este medio, Yo <!--nombre--> <!--apellido-->, con n&uacute;mero de carnet
<!--username-->, DUI <!--dui-->, hago constar que luego de repetidas amonestaciones debido a
<select id="lsTpDisc" class="txtPag" onChange="getUltimasSanciones(this.value,<!--idemp-->)"><option value="0">Seleccione un tipo de Falta disciplinaria</option><!--tp_falta--></select>, Express Teleservices S.A. de C.V. en la cual 
 laboro desde el <!--ingreso--> ejerciendo el cargo de teleoperador (a), considerando 
 dichas acciones, la empresa, ha decidido otorgarme el presente <i>Contrato de aviso</i> dejando constar la 
&uacute;ltima advertencia. Por este mismo medio me comprometo a Cumplir los horarios y los lineamientos
que la empresa me exige quedando yo enterado de que en caso contrario de cometer una falta m&aacute;s
(<span id="lyNombreSancion"></span>), se considerar&aacute; una causal para de terminaci&oacute;n de contrato laboral sin responsabilidad patronal.<br><br>


Detalle de &uacute;ltimas faltas cometidas:
<div id="lyUltimasFaltas"></div>
<br><br><br><br><br>

_____________________________________<br>
 <b>Nombre: </b><!--nombre--> <!--apellido-->

<br><br>
<b>Este contrato es válido por 6 meses desde la fecha de su creación.</b></td></tr>
<tr><td colspan="4" align="center" ><input type="button" class="ui-corner-all btn" value="Guardar" onclick="sv_contratoAviso()" /></td></tr></table><br><br>



 

