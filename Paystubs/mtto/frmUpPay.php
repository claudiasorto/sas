<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="_self" action="mtto/up_Paystub.php">
<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4"
  style="font-size: 11px;
  font-family: Tahoma;
  font-weight: 800;
  color: #666;">
<tr><th colspan="2" align="center">UPLOAD PAY STUBS</th></tr>
<tr><td width="30%" align="right">Delivery date of pay: </td><td><input type="text" name="fecha" id="fecha" size="25" class="txtPag" readonly="1" /><img src="images/calendar.jpg" onclick="return showCalendar('fecha', '%d/%m/%Y');" style="cursor:pointer;" /></td></tr> 
<tr><td align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag" ></td></tr>
<tr><td align="center" colspan="2"><input type="button" onClick="upFile()" value="Upload data" class="btn" ></td></tr> 
<tr><td colspan="2" height="1px"></td></tr>
<tr><td colspan="2">Note: The format of the document to be loaded is CSV and the order of the columns is:<br>
1-BADGE, <br>
2-Employee name, <br>
3-#Horas, <br>
4-Desc/Horas, <br>
5-#Horas a pagar, <br>
6-Salario por horas,<br>
7-#Horas nocturnas, <br>
8-$Horas nocturnas, <br>
9-#Horas extras diurnas,<br>
10-$Horas extras diurnas,<br> 
11-#Horas extras nocturnas,<br> 
12-$Horas extras nocturnas,<br> 
13-Aguinaldo, vacaciones, <br>
14-Indemnizaci&oacute;n,<br> 
15-Bonos,<br>
16-Otros Ingresos,<br> 
17-Total Ingresos,<br>
18-ISSS,<br> 
19-AFP,<br> 
20-ISR, <br>
21-Total<br> 
22-deducciones,<br> 
23-EMI, <br>
24-Cheff factory, <br>
25-Cafeter&iacute;a Silvia Marroqu&iacute;n,<br> 
26-Equipo Da&ntilde;ado, <br>
27-Opticas de Oro, <br>
28-Prestamos bancos y financieras,<br>  
29-Adelantos de salario,<br> 
30-Total descuentos,<br> 
31-Inicio de periodo, <br>
32-Fin de periodo, <br>
33-Fecha de entrega,<br> 
34-Liquido a recibir.
 </td></tr>

</table>

<iframe id="frUP" name="frUP" src="no.php" style="visibility:hidden;"></iframe>
</form>
