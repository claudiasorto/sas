<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  $sqlText = "select date_format(sysdate(),'%d%m%y%h%i%s') timestamp from dual";
  $dtExt = $dbEx->selSql($sqlText);
  
  $filtro = str_replace("\\","",$_POST['filtro']);
  $filtroinact = str_replace("\\","",$_POST['filtroinact']);
  $datF = split("--",$filtro);
  $datFI = split("--",$filtroinact);
  		
  $sqlText = "select ap.id_apxemp, e.employee_id, e.username, e.firstname, e.lastname, tp.id_tpap, tp.name_tpap, ".
			"ap.id_apxemp, ap.id_center, date_format(ap.startdate_ap,'%d/%m/%Y') as f1, ".
			"date_format(ap.storagedate_ap,'%d/%m/%Y') as f2, ap.storagedate_ap as f3, date_format(ap.enddate_ap,'%d/%m/%Y') as f4, ".
			"ap.hours_ap, ap.comment_ap ".
			"from employees e inner join apxemp ap on ap.employee_id=e.employee_id ".
			"inner join type_ap tp on tp.id_tpap=ap.id_tpap ".
			"inner join employee_status es on es.status_id = e.user_status ".
			"inner join plazaxemp pe on pe.employee_id=e.employee_id ".
			"inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$datF[0].
		" union all ".
		"select ap.id_apxemp, e.employee_id, e.username, e.firstname, e.lastname, tp.id_tpap, tp.name_tpap, ".
			"ap.id_apxemp, ap.id_center, date_format(ap.startdate_ap,'%d/%m/%Y') as f1, ".
			"date_format(ap.storagedate_ap,'%d/%m/%Y') as f2, ap.storagedate_ap as f3, date_format(ap.enddate_ap,'%d/%m/%Y') as f4, ".
			"ap.hours_ap, ap.comment_ap ".
			"from employees e inner join apxemp ap on ap.employee_id=e.employee_id ".
			"inner join type_ap tp on tp.id_tpap=ap.id_tpap ".
            "inner join employee_status es on es.status_id = e.user_status ".$datFI[0].
			" order by 1 desc";
  
  $dtXls = $dbEx->selSql($sqlText);
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_totalap".$dtExt['0']['timestamp'].".xls");
?>
<!--<link rel="stylesheet" href="http://192.168.1.79/Ap_superv/css/estilos.css" /> -->
<table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
<?php
	echo "<tr><td>N&deg; de Acci&oacute;n de Personal</td><td>Badge</td><td>Empleado</td><td>Fecha de Elaboraci&oacute;n</td><td>Fecha Efectiva</td><td>Tipo de Acci&oacute;n Disciplinaria</td><td>Sanci&oacute;n disciplinaria</td><td>Motivo de Sanci&oacute;n</td><td>Pagada</td><td>N&uacute;mero de Horas</td><td>Comentarios</td></tr>";
	foreach($dtXls as $dr){
		$pago = "";
				if($dr['id_tpap']==1){$pago='Pagada';}
				if($dr['id_tpap']==2){$pago='No Pagada';}
				$sancion = "";
				if($dr['typesanction_ap']==1){
					$sancion = "VERBAL";	
				}
				else if($dr['typesanction_ap']==2){
					$sancion = "ESCRITA";	
				}
				else if($dr['typesanction_ap']==3){
					$sancion ="SUSPENSION";
				}
				$tpDisc = "";
				if($dr['id_tpdisciplinary']>0){
					$sqlText = "select name_tpdisciplinary from type_disciplinary where id_tpdisciplinary=".$dr['id_tpdisciplinary'];
					$dtDisc = $dbEx->selSql($sqlText);
					$tpDisc = $dtDisc['0']['name_tpdisciplinary'];
				}
?>
<tr class="trList" align="center"><td><?php echo $dr['id_apxemp'];?></td><td><?php echo $dr['username'];?></td><td><?php echo $dr['firstname']."&nbsp;".$dr['lastname'];?></td><td><?php echo $dr['f2'];?></td><td><?php echo $dr['f1'];?></td><td><?php echo $dr['name_tpap'];?></td><td><?php echo $sancion; ?></td><td><?php echo $tpDisc;?></td><td><?php echo $pago;?></td><td><?php echo $dr['hours_ap'];?></td><td><?php echo $dr['comment_ap'];?></td></tr>
<?php 
}
?>
</table>
