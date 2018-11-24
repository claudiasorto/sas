<input type="hidden" id="txtIdR" value="<!--IdRequest-->" >
<table cellpadding="3" cellspacing="0" width="70%" class="backTablaMain" align="center" bordercolor="#069">
<tr><td colspan="3"><b>A QUIEN INTERESE: </b>
<br><br>
Por medio de la presente hago constar que al agente <!--nombre-->  por error de planilla se le pago un extra de $ <input type="text" class="txtPag" id="txtDinero" size="15"> en la planilla del <select id="lsPaytub" class="txtPag"><!--optPaystub--></select>.
<br><br>
Por lo mismo firmamos este documento en el cual el agente nos autoriza a hacer el descuento respectivo de estos $--.-- de la siguiente manera: <input type="text" class="txtPag" size="100" id="txtDescripcion"></td></tr>
<tr><td>Firmas: <select NAME="sel1[]" ID="sel1" SIZE="5" class="txtPag" multiple="multiple" style="width: 250px">
<!--optEmployees-->
</select>
</td>
<td>
<input TYPE="BUTTON" VALUE="->" ONCLICK="addIt();" class="btn"></input>
<br />
<input TYPE="BUTTON" VALUE="<-" ONCLICK="delIt();" class="btn"></input>
</td>
<td>
<select NAME="sel2[]" ID="sel2" SIZE="5" multiple="multiple" class="txtPag" style="width: 250px">
</select></td></tr>
<tr><td colspan="3" align="center"><input type="button" class="btn" value="Save" onclick="saveDoc()" /></td></tr>
</table>