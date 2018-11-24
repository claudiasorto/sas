<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Base de Recursos Humanos</title>
<style type="text/css">
 BODY{
  font-family:Tahoma;
  font-size:12px;
 }
</style>
</head>
<body topmargin="0" leftmargin="0" onload="window.print() ">
<table cellpadding="4" cellspacing="2" width="500" border="0" class="tblListBack" align="center">
<tr><td align="right" colspan="4"><img src="../images/LogoSkycom.png" alt="skycom" width="200"/></td></tr></table>
<table cellpadding="2" cellspacing="0" width="700" border="0" align="center" bgcolor="#FFFFFF">
<tr><th colspan="4" align="right" class="txtPag">San Salvador, <?php echo $_GET['start'];?></th></tr>
<tr><th colspan="4"><u><?php echo $_GET['nom_ap'];?></u></th></tr>
<tr><td colspan="4" class="txtPag" style="font-size:13px" align="justify">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Por este medio, Yo <?php echo $_GET['first']." ".$_GET['last'];?>, con n&uacute;mero de carnet
<?php echo $_GET['username'];?>, DUI <?php echo $_GET['dui'];?>, hago constar que luego de repetidas amonestaciones debido a <?php echo $_GET['tipo_falta'];?>, Express Teleservices S.A. de C.V. en la cual 
 laboro desde el <?php echo $_GET['date_admis'];?> ejerciendo el cargo de teleoperador (a), considerando 
 dichas acciones, la empresa, ha decidido otorgarme el presente <i>Contrato de aviso</i> dejando constar la 
&uacute;ltima advertencia. Por este mismo medio me comprometo a Cumplir los horarios y los lineamientos
que la empresa me exige quedando yo enterado de que en caso contrario de cometer una falta m&aacute;s
(<?php echo $_GET['tipo_falta'];?>), se considerar&aacute; una causal para de terminaci&oacute;n de contrato laboral sin responsabilidad patronal.<br><br>


Detalle de &uacute;ltimas faltas cometidas:<br></td></tr>
<tr><td colspan="4">
<table cellpadding="2" cellspacing="0" width="600" border="1" bordercolor="#003366" align="center" bgcolor="#FFFFFF" style="border-bottom-style:solid;">
<?php echo $_GET['tblImpSanciones']; ?>
</table>
</td></tr>
<tr class="txtPag"><td colspan="2" align="center"><br /><br /><br /><?php echo $_GET['aprob_emp']; ?></td><td colspan="2" align="center"><br /><?php echo $_GET['aprob_hr']; ?></td></tr>
<tr class="txtPag"><td colspan="2" align="center"><br /><br /><br /><?php echo $_GET['aprob_sup']; ?></td><td colspan="2" align="center"><?php echo $_GET['aprob_gen']; ?></td></tr>
<tr><td></td></tr>
<tr><td></td></tr>
</table>
</body>
</html>
