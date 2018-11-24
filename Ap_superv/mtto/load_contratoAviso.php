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
<tr><td colspan="4" align="right">
<input type="image" src="images/update.png" width="40" alt="Actualizar" onclick="update_ap()" style="cursor:pointer;" title="Pulse para actualizar" align="absmiddle"/>&nbsp;&nbsp;
<a href='mtto/impContratoAviso.php?start=<!--startdate-->&nom_ap=<!--nom_ap-->&first=<!--first-->&last=<!--last-->&username=<!--username-->&dui=<!--dui-->&date_admis=<!--date_admis-->&tipo_falta=<!--tipo_falta-->&aprob_emp=<!--aprob_emp-->&aprob_sup=<!--aprob_superv-->&aprob_area=<!--aprob_area-->&aprob_work=<!--aprob_work-->&aprob_hr=<!--aprob_hr-->&aprob_gen=<!--aprob_gen-->&tblImpSanciones=<!--tblImpSanciones-->' target="_blank"><img src="images/print.png" border="0" width="50" style="cursor:pointer;" title="Pulse para imprimir" align="absmiddle" /></a>
</td></tr>
</table>
<br /><br />
<table cellpadding="2" cellspacing="0" width="700" border="0" align="center" bgcolor="#FFFFFF">
<tr><th colspan="4" align="right" class="txtPag">San Salvador, <!--startdate--> </th></tr>
<tr><th colspan="4"><u><!--nom_ap--></u></th></tr>
<tr><td colspan="4" class="txtPag" style="font-size:13px" align="justify">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Por este medio, Yo <!--first--> <!--last-->, con n&uacute;mero de carnet
<!--username-->, DUI <!--dui-->, hago constar que luego de repetidas amonestaciones debido a <!--tipo_falta-->, Express Teleservices S.A. de C.V. en la cual 
 laboro desde el <!--date_admis--> ejerciendo el cargo de teleoperador (a), considerando 
 dichas acciones, la empresa, ha decidido otorgarme el presente <i>Contrato de aviso</i> dejando constar la 
&uacute;ltima advertencia. Por este mismo medio me comprometo a Cumplir los horarios y los lineamientos
que la empresa me exige quedando yo enterado de que en caso contrario de cometer una falta m&aacute;s
(<!--tipo_falta-->), se considerar&aacute; una causal para de terminaci&oacute;n de contrato laboral sin responsabilidad patronal.<br><br>


Detalle de &uacute;ltimas faltas cometidas:<br></td></tr>
<tr><td colspan="4">
<!--tblSanciones-->
</td></tr>

<tr><td colspan="4" class="txtPag" style="font-size:13px" align="justify">

</td></tr>
<tr><td colspan="2" align="center" title="Clic para autorizar acci&oacute;n de personal"><!--btn_autor--></td>
<td colspan="2" align="center" title="Clic para rechazar acci&oacute;n de personal"><!--btn_rechaz--></td></tr>
<tr><td colspan="4" align="center" class="txtPag">
<div id="lyComentRechazo" style="display:none">
	Observaciones:<br /> <textarea class="txtPag" id="txtComentRechazo" cols="50" rows="3"></textarea>
    <br /><input type="button" class="btn" onclick="sv_AutorizacionRechazo()" value="Guardar Comentario"/><br /><br /><br />
</div>
</td></tr>

<tr class="txtPag"><td colspan="2" align="center"><!--aprob_emp--></td><td colspan="2" align="center"><!--aprob_hr--></td></tr>
<tr class="txtPag"><td colspan="2" align="center"><!--aprob_superv--></td><td colspan="2" align="center"><!--aprob_gen--></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<br><br>
</table>
<br /><Br />