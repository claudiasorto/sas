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
<table class="tblListBack" width="500" align="center" cellpadding="4" cellspacing="2">
<tr><td align="right" colspan="2"><img src="../images/LogoSkycom.png" alt="Skycom" width="200"/></td></tr>
<tr><td colspan="2" class="showItem" align="center"><b><br /><br />HOJA DE REGISTRO DE EMPLEADO</b><br /><br /></td></tr>
<tr><td class="itemForm" width="200">Badge:&nbsp;</td><td class="txtResalt"><?php echo $_GET['us']; ?></td></tr>
<tr><td class="itemForm">Nombre:&nbsp;</td><td class="txtResalt"><?php echo $_GET['ap'];?>, <?php echo $_GET['nombre']?></td></tr>
<tr><td class="itemForm">Estatus de Empleado:&nbsp;</td><td class="txtResalt"><?php echo $_GET['estatus'];?></td></tr>
<tr><td class="itemForm">Cuenta:&nbsp;</td><td class="txtResalt"><?php echo $_GET['cuenta']; ?></td></tr>
<tr><td class="itemForm">Departamento:&nbsp;</td><td class="txtResalt"><?php echo $_GET['depto']; ?></td></tr>
<tr><td class="itemForm">Posici&oacute;n:&nbsp;</td><td class="txtResalt"><?php echo $_GET['plaza']; ?></td></tr>
<tr><td class="itemForm">Supervisor:&nbsp;</td><td class="txtResalt"><?php echo $_GET['supervisor']; ?></td></tr>
<tr><td class="itemForm">Tipo de Plaza:&nbsp;</td><td class="txtResalt"><?php echo $_GET['tipo_plaza']; ?></td></tr>
<tr><td class="itemForm">Fecha de Ingreso a la Empresa:&nbsp;</td><td class="txtResalt"><?php echo $_GET['admis']; ?></td></tr>
<tr><td class="itemForm">Salario:&nbsp;</td><td class="txtResalt"><?php echo $_GET['salario']; ?></td></tr>
<tr><td class="itemForm">Bono:&nbsp;</td><td class="txtResalt"><?php echo $_GET['bono']; ?></td></tr>
<tr><td class="itemForm">N&uacute;mero de Cuenta:&nbsp;</td><td class="txtResalt"><?php echo $_GET['cta']; ?></td></tr>
<tr><td class="itemForm">DUI:&nbsp;</td><td class="txtResalt"><?php echo $_GET['dui']; ?></td></tr>
<tr><td class="itemForm">NIT:&nbsp;</td><td class="txtResalt"><?php echo $_GET['nit']; ?></td></tr>
<tr><td class="itemForm">ISSS:&nbsp;</td><td class="txtResalt"><?php echo $_GET['isss']; ?></td></tr>
<tr><td class="itemForm">AFP Crecer:&nbsp;</td><td class="txtResalt"><?php echo $_GET['crecer']; ?></td></tr>
<tr><td class="itemForm">AFP Confia:&nbsp;</td><td class="txtResalt"><?php echo $_GET['confia']; ?></td></tr>
<tr><td class="itemForm">Carnet de Minoridad:&nbsp;</td><td class="txtResalt"><?php echo $_GET['minor']; ?></td></tr>
<tr><td class="itemForm">IPSFA:&nbsp;</td><td class="txtResalt"><?php echo $_GET['ipsfa']; ?></td></tr>
<tr><td class="itemForm">Fecha de Nacimiento:&nbsp;</td><td class="txtResalt"><?php echo $_GET['fec_nac']; ?></td></tr>
<tr><td class="itemForm">Direcci&oacute;n:&nbsp;</td><td class="txtResalt"><?php echo $_GET['direccion']; ?></td></tr>
<tr><td class="itemForm">Correo:&nbsp;</td><td class="txtResalt"><?php echo $_GET['email']; ?></td></tr>
<tr><td class="itemForm">Celular:&nbsp;</td><td class="txtResalt"><?php echo $_GET['celular']; ?></td></tr>
<tr><td class="itemForm">Telefono fijo:&nbsp;</td><td class="txtResalt"><?php echo $_GET['tel']; ?></td></tr>
<tr><td class="itemForm">Profesi&oacute;n:&nbsp;</td><td class="txtResalt"><?php echo $_GET['profesion']; ?></td></tr>
<tr><td class="itemForm">N&deg; de Locker:&nbsp;</td><td class="txtResalt"><?php echo $_GET['locker']; ?></td></tr>
</table>



