<input type="hidden" id="txtEmp" value="<!--IdEmp-->" >
<input type="hidden" id="txtEstado" value="<!--idEstado-->" >
<input type="hidden" id="txtFecha" value="<!--fecha-->"/>
<table bgcolor="#FFFFFF" width="600" bordercolor="#069" align="center" cellpadding="6" cellspacing="4">
<tr><td class="showItem" align="center" colspan="2"><b>CHANGE STATUS OF ABSENTEEISM</b></td></tr>
<tr><td class="txtForm" colspan="2">Employee: <!--badge-->,  <!--nombre--></td></tr>
<tr><td class="txtForm">Type absenteeism: </td><td><select id="lsTpAb" class="txtPag"><!--optEstado--></select></td></tr>
<tr><td class="txtForm">Observations: </td><td><textarea id="txtObserv" class="txtPag" cols="70" rows="3"><!--comentario--></textarea></td></tr>
<tr><td class="txtForm" align="center" colspan="2"><input type="button" class="btn" value="Change Status" onClick="saveAbsent()" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" class="btn" value="Cancel" onClick="cancelAbsent()" ></td></tr>
</table>