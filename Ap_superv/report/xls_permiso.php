<?php
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_traslado.xls");
 ?>
<!--<link rel="stylesheet" href="http://192.168.1.79/Ap_superv/css/estilos.css" />-->
<table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
<?php
	echo '<tr><td colspan="2" align="center">ACCION DE PERSONAL</td></tr>';
	echo "<tr><td>N&deg; Badge del Empleado:&nbsp;</td><td>".$_POST['username']."</td></tr>";
	echo "<tr><td>Nombre del Empleado:&nbsp;</td><td>".$_POST['apellido'].",&nbsp;".$_POST['nombre']."</td></tr>";
	echo "<tr><td>Departamento:&nbsp;</td><td>".$_POST['depto']."</td></tr>";
	echo '<tr><td colspan="2" align="rigth">Fecha:&nbsp;'.$_POST['storage'].'</td></tr>';
	echo '<tr><td colspan="2" align="center"><br><br>'.$_POST['nomap'].'</td></tr>';
	echo '<tr><td>Del:</td><td>'.$_POST['start'].'</td></tr>';
	echo '<tr><td>Al:</td><td>'.$_POST['end'].'</td></tr>';
	echo '<tr><td>Horas:</td><td>'.$_POST['horas'].'</td></tr>';
	echo '<tr><td colspan="2">Comentarios:</td></tr>';
	echo '<tr><td colspan="2">'.$_POST['observ'].'</td></tr>';
	
?>
</table>
