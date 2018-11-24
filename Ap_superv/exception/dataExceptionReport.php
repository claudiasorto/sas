<input type="hidden" id="idException" value="<!--exception_id-->" >
<table width="700" class="backTablaMain" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">
<tr class="txtForm"><td colspan="2" align="center">Exception number: <!--exception_id--></td></tr>
<tr><td class="itemForm">Employee: </td><td><!--nombre-->&nbsp;<!--apellido--></td></tr>
<tr><td class="itemForm">Badge: </td><td><!--badge--></td></tr>
<tr><td class="itemForm">Supervisor: </td><td><!--supervisor--></td></tr>
<tr><td class="itemForm">Date: </td><td><!--date--></td></tr>
<tr><td class="itemForm">Initial and Final time: </td><td><!--horaIni--> - <!--horaFin--></td></tr>
<tr><td class="itemForm">Reason: </td><td><!--tp_name--></td></tr>
<tr><td class="itemForm">Comments: </td><td><textarea  cols="70" rows="3" disabled="disabled"><!--comment--></textarea></td></tr>
<tr><td align="center" colspan="2"><input type="button" class="btn" value="Update Exception" onClick="updateExceptionReport()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" class="btn" value="New Exception" onClick="newException()"></td></tr>
</table>


