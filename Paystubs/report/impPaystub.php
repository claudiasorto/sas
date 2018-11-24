<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Boleta de pago</title>
<style type="text/css">
 BODY{
  font-family:Tahoma;
  font-size:12px;
 }
</style>
</head>
<?php
require_once("../db_funcs.php");
require_once("../salary_funcs.php");
$dbEx = new DBX;
$sFunc = new SAL;

$rslt = $sFunc->getBoletaPago($_GET['pid'],$_GET['eid']);


?>

<body topmargin="0" leftmargin="0" onload="window.print() ">
<table width="800" bordercolor="#000000" align="center" cellpadding="1" cellspacing="3" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">
<tr><td align="right" colspan="5"><img src="../images/LogoSkycom.png" alt="Skycom" width="200"/></td></tr>
<tr><td><?php echo $rslt; ?></td></tr>

</table>
