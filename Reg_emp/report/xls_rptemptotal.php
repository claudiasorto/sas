<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  
  $sqlText = "select date_format(sysdate(),'%d%m%y%h%i%s') timestamp from dual";
  $dtExt = $dbEx->selSql($sqlText);
  
  $filtro = str_replace("\\","",$_POST['filtro']);
  $datF = explode("--",$filtro);

  
  $sqlText = "select name_place from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id ".
  		"inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".
  		"inner join places p on p.id_place=pd.id_place where e.user_status=1 ".
  		"and pe.status_plxemp='A' and e.employee_id=".$_SESSION['usr_id'];
  		
  $dtPlace = $dbEx->selSql($sqlText);
  
  $sqlText = "select e.employee_id, USERNAME, FIRSTNAME, LASTNAME, ID_SUPERVISOR, ".
      "NAME_ACCOUNT, NAME_DEPART, NAME_PLACE, es.STATUS_NAME USER_STATUS, SALARY, ".
      "BONUS, ACCOUNT_NUMBER, DUI, NIT, ISSS, AFPCRECER, AFPCONFIA, MINORITY_CARD, ".
      "IPSFA, ADDRESS, EMAIL, CELULAR, TEL_HOUSE, PROFESSION, LOCKER, AGENT_ID, ".
      "date_format(date_admis,'%m/%d/%Y') as f1, date_format(date_birth,'%d/%m/%Y') as f2, ".
      "pj.job_type_name JOB_TYPE_NAME, ".
      "date_format(pe.end_date,'%d/%m/%Y') as fechaRetiro, ".
      "g.geography_name, e.notification_flag ".
    "from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id ".
      "inner join employee_status es on e.user_status = es.status_id ".
      "inner join job_type pj on pe.job_type_id = pj.job_type_id ".
      "inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
      "inner join places pl on pl.id_place = pd.id_place ".
      "left outer join account c on pd.id_account = c.id_account ".
      "left outer join depart_exc d on d.id_depart=pd.id_depart ".
      "left outer join geographies g on g.geography_code = e.geography_code ".
    "where pe.id_plxemp = get_idultimaplaza(e.employee_id) ".
    $datF[0]." ".
    "order by trim(firstname) ";

  $dtXls = $dbEx->selSql($sqlText);

  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_totalemp".$dtExt['0']['timestamp'].".xls");
?>
<!--<link rel="stylesheet" href="../css/estilos.css" /> -->
<table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
<?php
	echo "<tr><td>BADGE</td><td>NOMBRE DE EMPLEADO</td><td>ESTATUS DE EMPLEADO</td>
	<td>CUENTA</td><td>DEPARTAMENTO</td><td>POSICION</td><td>SUPERVISOR</td><td>TIPO DE PLAZA</td><td>FECHA DE INGRESO</td><td>FECHA DE EGRESO</td>";
	
echo "<td>SALARIO</td><td>BONO</td><td>PAIS</td><td>NUMERO DE CUENTA</td><td>DUI</td><td>NIT</td><td>ISSS</td><td>AFP Crecer</td><td>AFP CONFIA</td><td>CARNET DE MINORIDAD</td><td>IPSFA</td><td>FECHA DE NACIMIENTO</td><td>DIRECCION</td><td>CORREO</td><td>RECIBE NOTIFICACIONES?</td><td>CELULAR</td><td>TELEFONO FIJO</td><td>PROFESION</td><td>N&deg; LOCKER</td><td>AGENT ID</td></tr>";
	foreach($dtXls as $dr){

	$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dr['ID_SUPERVISOR'];
	$supervisor = $dbEx->selSql($sqlText);


?>
<tr class="trList" align="center">
<td><?php echo $dr['USERNAME'];?></td>
<td><?php echo $dr['FIRSTNAME']."&nbsp;".$dr['LASTNAME'];?></td>
<td><?php echo $dr['USER_STATUS'];?></td>
<td><?php echo $dr['NAME_ACCOUNT'];?></td>
<td><?php echo $dr['NAME_DEPART'];?></td>
<td><?php echo $dr['NAME_PLACE'];?></td>
<td><?php echo $supervisor['0']['firstname']."&nbsp;".$supervisor['0']['lastname'];?></td>
<td><?php echo $dr['JOB_TYPE_NAME'];?></td>
<td><?php echo $dr['f1'];?></td>
<td><?php echo $dr['fechaRetiro'];?></td>
    <?php	

if($_SESSION['usr_idrol']<=5 and $dtPlace['0']['name_place']!='ACCOUNTING MANAGER'){
	?>
		<td></td>
        <td></td>
        <td></td>
	<?php	
}
else{
?>
<td><?php echo $dr['SALARY'];?></td>
<td><?php echo $dr['BONUS'];?></td>
<td><?php echo $dr['geography_name'];?></td>
<td><?php echo $dr['ACCOUNT_NUMBER'];?></td>
<?php
}
?>
<td><?php echo $dr['DUI'];?></td>
<td><?php echo $dr['NIT'];?></td>
<td><?php echo $dr['ISSS'];?></td>
<td><?php echo $dr['AFPCRECER'];?></td>
<td><?php echo $dr['AFPCONFIA'];?></td>
<td><?php echo $dr['MINORITY_CARD'];?></td>
<td><?php echo $dr['IPSFA'];?></td>
<td><?php echo $dr['f2'];?></td>
<td><?php echo $dr['ADDRESS'];?></td>
<td><?php echo $dr['EMAIL'];?></td>
<td><?php echo $dr['notification_flag'];?></td>
<td><?php echo $dr['CELULAR'];?></td>
<td><?php echo $dr['TEL_HOUSE'];?></td>
<td><?php echo $dr['PROFESSION'];?></td>
<td><?php echo $dr['LOCKER'];?></td>
<td><?php echo $dr['AGENT_ID'];?></td>
</tr>
<?php 
}
?>
</table>
