<body onLoad="initIt();">
<table width="800" class="backTablaMain" bordercolor="#069" align="center" >
<tr><td colspan="3" align="center" bgcolor="#FFFFFF"><font color="#003366"><b>AGENTS AND SUPERVISORS</b></td></tr>
<tr><td align="center" width="45%">
<select NAME="sel1[]" ID="sel1" SIZE="20" multiple="multiple" style="width: 250px">
<!--optEmployees-->
</select>
</td>
<td width="10%">
<input TYPE="BUTTON" VALUE="->" ONCLICK="addIt();" class="btn"></input>
<br />
<input TYPE="BUTTON" VALUE="<-" ONCLICK="delIt();" class="btn"></input>
</td>
<td  width="45%">
<select NAME="sel2[]" ID="sel2" SIZE="20" multiple="multiple" style="width: 250px">
</select>

</td></tr>
<tr><td align="center" colspan="3" bgcolor="#FFFFFF"><font color="#003366"><b>FORM</b></td></tr>
<tr><td class="itemForm">Type of activity: </td>
<td colspan="2"><select id="lsTpAct" class="txtPag"><!--optTpHorarios--></select></td></tr>
<tr><td class="itemForm">Date: </td><td colspan="2">
<input type="text" name="fecha" id="fecha" size="30" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha', '%d/%m/%Y');"  style="cursor:pointer;" /></td></tr>
<tr><td class="itemForm">Schedule since: </td><td colspan="2">
<select id="lsSalidaHora" class="txtPag"><!--optHora--></select>:
<select id="lsSalidaMinutos" class="txtPag"><!--optMinutos--></select>
to 
<select id="lsEntradaHora" class="txtPag"><!--optHora--></select>:
<select id="lsEntradaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td colspan="3" align="center"><input type="button" class="btn" value="Save Schedule" onClick="saveHorarioEspecial()"/></td></tr>
<tr><td colspan="3" class="txtPag">
<b>Note: </b>Programming will not be saved in the following cases:
<li>Agent does not have schedule for this day</li>
<li>The selected time is not within the agent's schedule</li> 
</table>
</td></tr>
</table>
<br />
<div id="lyDetHorario"></div>
