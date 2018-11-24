<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Base de datos de Recursos Humanos</title>
<style type="text/css">
 BODY{
  font-family:Tahoma;
  font-size:12px;
 }
</style>
</head>
<body topmargin="0" leftmargin="0" onload="window.print() ">
<table class="tblResult" width="80%" align="center">
<tr><td align="right" colspan="2"><img src="../images/LogoSkycom.png" alt="skycom" width="200"/></td></tr>
<tr><td colspan="2" align="center" style="font-size:14px"><b><u>ACCI&Oacute;N DE PERSONAL N&deg;<?php echo $_GET['idAp']; ?> RECHAZADA<u><b>
<img src="../images/rejected2.png" width="20%" align="right">
</td></tr>
<tr><td colspan="2" width="30%" align="center"><b> Datos de Empleado<b></td></tr>
<tr><td height="10px"></td></tr>
<tr><td class="itemForm">Badge de Empleado: </td><td><?php echo $_GET['badge']; ?></td></tr>
<tr><td class="itemForm">Nombre de Empleado: </td><td><?php echo $_GET['apellido']; ?>, <?php echo $_GET['nombre'] ?></td></tr>
<tr><td class="itemForm">Cuenta: </td><td><?php echo $_GET['cta']; ?></td></tr>
<tr><td class="itemForm">Departmento: </td><td><?php echo $_GET['dpto']; ?></td></tr>
<tr><td class="itemForm">Posici&oacute;n: </td><td><?php echo $_GET['posicion']; ?></td></tr>
<tr><td height="10px"></td></tr>
<tr><td colspan="2" align="center"><b>Datos de la Acci&oacute;n de Personal</b></td></tr>
<tr><td height="10px"></td></tr>
<tr><td width="40%" class="itemForm">Tipo de acci&oacute;n de personal: </td><td><?php echo $_GET['nametpap']; ?></td></tr>
<tr><td class="itemForm">Fecha de Registro: </td><td><?php echo $_GET['stg']; ?></td></tr>
<tr><td class="itemForm">Fecha efectiva: </td><td><?php echo $_GET['ini']; ?></td></tr>
<tr><td class="itemForm">Creado por: </td><td><?php echo $_GET['autor']; ?></td></tr>
<tr><td class="itemForm">Rechazado por: </td><td><?php echo $_GET['autorRec']; ?></td></tr>
<tr><td class="itemForm">Cargo rechazo: </td><td><?php echo $_GET['cargo']; ?></td></tr>
<tr><td class="itemForm">Observaciones: </td><td <textarea  disabled="disabled" rows="15" cols="100"><?php echo $_GET['comment']; ?></textarea></td></tr>
<tr><td height="10px"></td></tr>
<tr><td colspan="2"><?php echo $_GET['commentAccion']; ?></td></tr>
</table>
