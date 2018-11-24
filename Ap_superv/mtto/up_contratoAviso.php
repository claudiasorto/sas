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
<tr><td align="right" class="txtPag">Posici&oacute;n:&nbsp;</td><td class="txtPag"><!--plaza--><td align="right" class="txtPag">Fecha:&nbsp;</td><td class="txtPag"><!--storage--></td></tr></table>
<br /><br />

<table cellpadding="2" cellspacing="0" width="700" border="0" align="center" bgcolor="#FFFFFF">
<tr><th colspan="4" align="right" class="txtPag">San Salvador, <input type="text" name="fecha_inicio" id="fecha_inicio" size="15" class="txtPag" value="<!--startdate-->" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha_inicio', '%d/%m/%Y');" style="cursor:pointer;" /></th></tr>
<tr><th colspan="4"><u><!--nom_ap--></u></th></tr>
<tr><td colspan="4" class="txtPag" style="font-size:13px" align="justify">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Por este medio, Yo <!--first--> <!--last-->, con n&uacute;mero de carnet
<!--username-->, DUI <!--dui-->, hago constar que luego de repetidas amonestaciones debido a 
<select id="lsTpDisc" class="txtPag" onChange="getUltimasSanciones(this.value,<!--id_emp-->)"><!--optUpdTpDisciplinary--></select>
, Express Teleservices S.A. de C.V. en la cual 
 laboro desde el <!--date_admis--> ejerciendo el cargo de teleoperador (a), considerando 
 dichas acciones, la empresa, ha decidido otorgarme el presente <i>Contrato de aviso</i> dejando constar la 
&uacute;ltima advertencia. Por este mismo medio me comprometo a Cumplir los horarios y los lineamientos
que la empresa me exige quedando yo enterado de que en caso contrario de cometer una falta m&aacute;s
(<span id="lyNombreSancion"> <!--tipo_falta--></span>), se considerar&aacute; una causal para de terminaci&oacute;n de contrato laboral sin responsabilidad patronal.<br><br>
Detalle de &uacute;ltimas faltas cometidas:<br></td></tr>
<tr><td colspan="4">
<div id="lyUltimasFaltas"><!--tblSanciones--></div>
</td></tr>
<br><br>
<tr><td colspan="4" class="txtPag" style="font-size:13px" align="justify"><b>Este contrato es válido por 6 meses desde la fecha de su creación.</b></td></tr>
<tr><td colspan="4" align="center" ><input type="button" class="ui-corner-all btn" value="Actualizar" onclick="sv_upContratoAviso()" /></td></tr></table><br><br>


