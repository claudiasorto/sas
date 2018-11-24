<form method="post" name="frmDoc" id="frmDoc" enctype="multipart/form-data" target="_self" action="mtto/up_OtherPaystub.php">
<input type="hidden" value="<!--paystub_id-->" id="idPaystub"/>
<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4"
  style="font-size: 11px;
  font-family: Tahoma;
  font-weight: 800;
  color: #666;">
<tr><th colspan="2" align="center">UPLOAD PAY STUBS</th></tr>
<tr><td width="30%" align="right">Delivery date of pay: </td><td><input type="text" name="fecha" id="fecha" size="25" class="txtPag" readonly="1" value="<!--fec_delivery-->" disabled="disabled"/></td></tr> 
<tr><td align="right">File: </td><td><input type="file" name="flDoc" id="flDoc" size="25" class="txtPag"></td></tr>
<tr><td align="center" colspan="2"><input type="button" onClick="upFile()" value="Upload data" class="btn" ></td></tr> 
<tr><td colspan="2" height="1px"></td></tr>
<tr><td colspan="2">Note: The format of the file to load must be in CSV in the following order:<br>
1-BADGE, <br>
2-Employee name, <br>
3-#Horas, <br>
4-Salary,<br />
5-#Additional Hours,<br />
6-$Additional Hours,<br/>
7-#Salary discounts, <br/>
8-Seventh day, <br/>
9-#Nigth hours, <br/>
10-$Nigth hours, <br/>
11-#Day overtime hours,<br />
12-$Day overtime hours,<br />
13-#Night overtime hours, <br />
14-$Night overtime hours, <br />
15-Bonus,<br />
16-Aguinaldo, <br />
17-Vacation,<br />
18-Severance,<br />
19-Other Income, <br />
20-ISR, <br />
21-ISSS, <br />
22-AFP,<br />
23-EMI, <br />
24-CHEFF FACTORY, <br />
25-CAFETERIA, <br />
26-Damaged Equipment, <br />
27-Other Discounts, <br />
28-Loans, <br />
29-Salary advances, <br />
30-Payment to received, <br />
31-Note for other discounts
 </td></tr>

</table>

<iframe id="frUP" name="frUP" src="no.php" style="visibility:hidden;"></iframe>
</form>
