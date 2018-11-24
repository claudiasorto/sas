<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;

  $sqlText = "select date_format(sysdate(),'%d%m%y%h%i%s') timestamp from dual";
  $dtExt = $dbEx->selSql($sqlText);

  $filtro = str_replace("\\","",$_POST['filtro']);
  $datF = split("--",$filtro);

  $sqlText = 

  $sqlText = "select e.username, concat(e.firstname,' ',e.lastname) fullname, ".
				"es.status_name, pl.name_place, pj.job_type_name, ".
			    "date_format(pe.start_date,'%d/%m/%Y') start_date, date_format(pe.end_date,'%d/%m/%Y') end_date, ".
			    "pe.status_plxemp, NAME_ACCOUNT, NAME_DEPART ".
			"from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id ".
				"inner join employee_status es on e.user_status = es.status_id ".
				"inner join job_type pj on pe.job_type_id = pj.job_type_id ".
		        "inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
				"inner join places pl on pl.id_place = pd.id_place ".
				"left outer join depart_exc d on d.id_depart=pd.id_depart ".
  				"left outer join account ac on ac.id_account=pd.id_account ".
				$datF[0]." ".
		        "order by trim(firstname), pe.status_plxemp, pe.start_date desc";


  $dtXls = $dbEx->selSql($sqlText);
  header("Content-type: application/vnd.ms-excel; charset=utf-8");
  header("Content-Disposition: attachment; filename=rpt_HistoricoEmp".$dtExt['0']['timestamp'].".xls");
?>
<table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
<?php
	echo "<tr><td>BADGE</td><td>NOMBRE DE EMPLEADO</td><td>ESTADO ACTUAL</td>
	<td>CUENTA</td><td>DEPARTAMENTO</td><td>POSICION</td><td>TIPO DE PLAZA</td><td>FECHA INICIAL</td>
	<td>FECHA FINAL</td><td>ESTADO DE PLAZA</td>";
	if($_POST['fechaRetiro']){
		echo "<td>FECHA DE RETIRO</td>";
	}

	echo "</tr>";
	foreach($dtXls as $dr){

?>
<tr class="trList" align="center">
<td><?php echo $dr['username'];?></td>
<td><?php echo $dr['fullname'];?></td>
<td><?php echo $dr['status_name'];?></td>
<td><?php echo $dr['NAME_ACCOUNT'];?></td>
<td><?php echo $dr['NAME_DEPART'];?></td>
<td><?php echo $dr['name_place'];?></td>
<td><?php echo $dr['job_type_name'];?></td>
<td><?php echo $dr['start_date'];?></td>
<td><?php echo $dr['end_date'];?></td>
<td><?php echo $dr['status_plxemp'];?></td>
<?php
if($_POST['fechaRetiro']){
	?>
    <td><?php echo $dr['fechaRetiro'];?></td>
    <?php
}  ?>
</tr>
<?php
}
?>
</table>
