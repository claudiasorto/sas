<table class="tblListBack" width="700" align="center">
<tr><td class="showItem" colspan="2">FILTROS PARA GENERAR REPORTE <!--flagHistorico--> DE EMPLEADOS</td></tr>
<tr><td class="itemForm">Cuenta</td><td><select id="lsCuenta" onChange="getDepartFiltros(this.value)"><option value="0">[TODOS]</option><!--optCuenta--></select></td></tr>
<tr><td class="itemForm">Departamento:&nbsp;</td><td colspan="3"><span id="lyDepart"><select id="lsDepart" class="txtPag" onchange="getPoscFiltros(this.value)"><option value="0">[TODOS]</option><!--optDepart--></select></span></td></tr>
<tr><td class="itemForm">Posici&oacute;n:&nbsp;</td><td colspan="3"><span id="lyPosc"><select id="lsPosc" class="txtPag"><option value="0">[TODOS]</option><!--optPlaza--></select></span></td></tr>
<tr><td class="itemForm">Jefe Inmediato</td><td colspan="3"><div id="lySuperv"><select id="lsSuperv" class="txtPag"><option value="0">[TODOS]</option><!--optSuperv--></select></div></td></tr>
<tr><td class="itemForm">Per&iacute;odo de Ingreso Del:&nbsp;</td><td class="txtPag"><input type="text" id="fec_ini" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_ini', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;&nbsp;Al:&nbsp;<input type="text" id="fec_fin" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fec_fin', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>

<tr><td class="itemForm">Nombre del Empleado</td><td colspan="3"><input type="text" id="txtNombre" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Badge de Empleado</td><td colspan="3"><input type="text" id="txtUsername" class="txtPag" size="50" style="text-transform:uppercase"></td></tr>
<tr><td class="itemForm">Estatus de Empleado</td><td colspan="3"><select id="lsEstado" class="txtPag">
<option value="*">[TODOS]</option>
<!--optStatus--></select></td></tr>
<tr><td class="itemForm">Per&iacute;odo de Retiro Del:&nbsp;</td><td class="txtPag"><input type="text" id="ini_retiro" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('ini_retiro', '%d/%m/%Y');" style="cursor:pointer;" />&nbsp;&nbsp;&nbsp;Al:&nbsp;<input type="text" id="fin_retiro" class="txtPag" readonly="1" size="15" ><img src="images/calendar.jpg" onclick="return showCalendar('fin_retiro', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr>
<tr><td colspan="4" class="showItem" title="Click para generar reporte"><input type="button" class="btn" value="Generar reporte" <!--btnAccion-->></td></tr>
</table>
<br />
<div id="datos_rpt"></div>

