<body onLoad="initIt();">
<table width="800" class="backTablaMain" bordercolor="#069" align="center" cellpadding="4" cellspacing="4">
<tr><td colspan="3" align="center" bgcolor="#FFFFFF"><font color="#003366"><b>AGENTS AND SUPERVISORS</b></td></tr>
<tr><td align="center">
<select NAME="sel1[]" ID="sel1" SIZE="20" multiple="multiple" style="width: 250px">
<!--optEmployees-->
</select>
</td>
<td>
<input TYPE="BUTTON" VALUE="->" ONCLICK="addIt();" class="btn"></input>
<br />
<input TYPE="BUTTON" VALUE="<-" ONCLICK="delIt();" class="btn"></input>
</td>
<td>
<select NAME="sel2[]" ID="sel2" SIZE="20" multiple="multiple" style="width: 250px">
</select>

</td></tr>
<tr><td align="center" colspan="2" bgcolor="#FFFFFF"><font color="#003366"><b>FORM</b></td><td align="center" bgcolor="#FFFFFF"><font color="#003366"><b>CALENDAR</td></tr>
<tr>
<td align="center" colspan="2"><table cellpadding="3" cellspacing="6" bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">
<tr><td class="txtForm">Time of entry </td>
<td><select id="lsEntradaHora" class="txtPag"><!--optHora--></select>:
<select id="lsEntradaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">Break 1 out </td>
<td><select id="lsBreak1EntradaHora" class="txtPag"><!--optHora--></select>:
<select id="lsBreak1EntradaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">Break 1 in </td>
<td><select id="lsBreak1SalidaHora" class="txtPag"><!--optHora--></select>:
<select id="lsBreak1SalidaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">Lunch out </td>
<td><select id="lsLunchEntradaHora" class="txtPag"><!--optHora--></select>:
<select id="lsLunchEntradaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">Lunch in </td>
<td><select id="lsLunchSalidaHora" class="txtPag"><!--optHora--></select>:
<select id="lsLunchSalidaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">Break 2 out </td>
<td><select id="lsBreak2EntradaHora" class="txtPag"><!--optHora--></select>:
<select id="lsBreak2EntradaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">Break 2 in </td>
<td><select id="lsBreak2SalidaHora" class="txtPag"><!--optHora--></select>:
<select id="lsBreak2SalidaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">End of Duty </td>
<td><select id="lsSalidaHora" class="txtPag"><!--optHora--></select>:
<select id="lsSalidaMinutos" class="txtPag"><!--optMinutos--></select>
</td></tr>
<tr><td class="txtForm">OFF </td>
<td><input type="checkbox" id="chOff" name="chOff" /></td></tr>
</table>
</td>
<td align="center">
<table>
<tr><td cellpadding="4" cellspacing="4">
<input type="text" name="fecha" id="fecha" size="30" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha', '%d/%m/%Y');"  style="cursor:pointer;" />
<br />
<input type="text" name="fecha2" id="fecha2" size="30" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha2', '%d/%m/%Y');"  style="cursor:pointer;" />
<br />
<input type="text" name="fecha3" id="fecha3" size="30" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha3', '%d/%m/%Y');"  style="cursor:pointer;" />
<br />
<input type="text" name="fecha4" id="fecha4" size="30" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha4', '%d/%m/%Y');"  style="cursor:pointer;" />
<br />
<input type="text" name="fecha5" id="fecha5" size="30" class="txtPag" readonly="1"/><img src="images/calendar.jpg" onClick="return showCalendar('fecha5', '%d/%m/%Y');"  style="cursor:pointer;" />
<br /><br /><br />
</td></tr>
<tr><td rowspan="5" align="center"><input type="button" class="btn" value="Save Schedule" onClick="saveHorario()"/></td></tr>
</table>
</td></tr>
<tr><td colspan="3" align="right"><img src="images/abajo.png" width="30" style="cursor:pointer;" onClick="getDetalleHorario()" title="schedules this week" /></td></tr>
</table>
<br />
<div id="lyDetHorario"></div>
