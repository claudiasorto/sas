<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=OfflineActivityReport.xls");
  
 	$sqlText = "select * from types_schedules where tpsch_status='A'";
	$dtTp = $dbEx->selSql($sqlText);
  
?>

<table border="1" bordercolor="#003366">
	<tr><td colspan="5" align="center" bgcolor="#003366"><font color="#FFFFFF">
    <b>Activities for the period <?php echo $_POST['fechaIni'].' - '.$_POST['fechaFin'];?></b></font></td></tr>
    <?php
    foreach($dtTp as $dtT){
			
		$sqlText = "select e.employee_id, username, firstname, lastname, date_format(schact_date,'%d/%m/%Y') as f1, date_format(schact_start,'%H:%i') as t1, date_format(schact_end,'%H:%i') as t2 from schedulesactiv_emp sch inner join employees e on sch.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtro." and tpsch_id=".$dtT['TPSCH_ID']." and pe.status_plxemp='A' order by schact_date, firstname";
		$dtActiv = $dbEx->selSql($sqlText);
	?>
	<tr><td class="txtForm" colspan="5" bgcolor="#003366"><font color="#FFFFFF"><?php echo $dtT['TPSCH_NAME'].': '.$dbEx->numrows; ?></font></td></tr>
    <?php
		if($dbEx->numrows>0){
	?>
		<tr><td bgcolor="#AED7FF">Badge</td>
        <td bgcolor="#AED7FF">Employee</td>
        <td bgcolor="#AED7FF">Date</td>
        <td bgcolor="#AED7FF">Initial hour</td>
        <td bgcolor="#AED7FF">Final hour</td></tr>
        <?php
			foreach($dtActiv as $dtA){
		?>
				<tr><td><?php echo $dtA['username'];?></td><td><?php echo $dtA['firstname']." ".$dtA['lastname']; ?></td><td><?php echo $dtA['f1']; ?></td><td><?php echo $dtA['t1']; ?></td><td><?php echo $dtA['t2'];?></td></tr>
                <?php 
				}
			}
		}
		?>
    
</table>