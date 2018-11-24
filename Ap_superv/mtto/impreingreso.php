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
<tr><td align="right" colspan="4"><img src="../images/LogoSkycom.png" alt="skycom" width="200"/></td></tr>
<tr><th colspan="4" class="showItem" align="center"><u><b>ACCION DE PERSONAL N&deg; <?php echo $_GET['Idapxemp'];?> </u></b></th></tr>
<tr><td></td></tr>
<tr><td></td></tr>
<tr><td align="right" class="txtPag" colspan="4">N&deg;:<!--apxemp--></td></tr>
<tr><td align="right" class="txtPag">Badge de Empleado:&nbsp; </td><td class="txtPag" colspan="3"><?php echo $_GET['user']; ?></td></tr>
<tr><td align="right" class="txtPag">Nombre de empleado:&nbsp; </td><td class="txtPag" colspan="3"><?php echo $_GET['last'].", &nbsp;".$_GET['first'];?></td></tr>
<tr><td align="right" class="txtPag" colspan="3">Fecha:&nbsp;</td><td class="txtPag"><?php echo $_GET['fecha']?></td></tr>
<tr><td><br /><br /></td></tr>
<tr><th colspan="4" class="showItem"><u><?php echo $_GET['nom_ap']; ?></u></th></tr>
<tr><td  class="txtPag" align="rigth">Efectivo desde:&nbsp;</td><td class="txtPag"><?php echo $_GET['start']; ?></td></tr>
<tr><td  class="txtPag" align="rigth">Cuenta:&nbsp;</td><td class="txtPag"><?php echo $_GET['cuenta']; ?></td></tr>
<tr><td  class="txtPag" align="rigth">Departamento :&nbsp;</td><td class="txtPag"><?php echo $_GET['depto']; ?></td></tr>
<tr><td  class="txtPag" align="rigth">Posici&oacute;n:&nbsp;</td><td class="txtPag"><?php echo $_GET['plaza']; ?></td></tr>
<tr><td class="txtPag">Observaciones:</td></tr>
<tr><td  colspan="4" class="txtPag"><textarea rows="15" cols="100" id="txtObserv" class="txtPag"><?php echo $_GET['observ'] ?></textarea></td></tr>
<?php 
	require_once("../ap_funcs.php");
	$apFun = new APPR;
	echo $apFun->getFirmas($_GET['Idapxemp']); 
?>
<tr><td></td></tr>
<tr><td></td></tr>
</table>
</body>
</html>

 
