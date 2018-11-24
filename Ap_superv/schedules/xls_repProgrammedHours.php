<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=ProgrammedHoursReport.xls");
	
  	$sqlText = "select distinct(e.employee_id) employee_id, username, firstname, lastname from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$_POST['filtro']." and pe.status_plxemp='A' order by firstname ";
	$dtEmp = $dbEx->selSql($sqlText);
	
	$start = strtotime($_POST['fechaIni']);
	$end = strtotime($_POST['fechaFin']);
	
 ?>
 
 	<table cellpadding="2" cellspacing="0" width="600" border="1" bordercolor="#003366" align="center">
 	<tr><td align="center" bgcolor="#003366"><font color="#FFFFFF">Badge</font></td>
	<td bgcolor="#003366"><font color="#FFFFFF">Employee</font></td>
  <?php
  	$i = 0;
	while ( strtotime($_POST['fechaIni'] . ' +'.$i.' day') <= $end){

	?>
		<td align="center" bgcolor="#003366"><b><font color="#FFFFFF"><?php echo date("d/m/Y",strtotime($_POST['fechaIni'] . ' +'.$i.' day')); 
		$i++;
	?></font></td>
  <?php
	}
	?>
			
	<td align="center" bgcolor="#003366"><font color="#FFFFFF">Total Hrs</font></td></tr>

  <?php
  foreach($dtEmp as $dtE){
	 ?>
	<tr><td align="center"><?php echo $dtE['username'];?></td><td><?php echo $dtE['firstname'].' '.$dtE['lastname'];?></td>
    <?php
		//Obtener las horas programadas dia a dia
    	$i = 0;
    	while ( strtotime($_POST['fechaIni'] . ' +'.$i.' day') <= $end){
		//for($i=$start; $i<=$end; $i+=86400){
			$progh = 0;
			//$sqlText = "select sch_proghrs from schedules where employee_id=".$dtE['employee_id']." and sch_date='".date("Y-m-d",$i)."'";
            $sqlText = "select round((( ifnull(TIME_TO_SEC(sch_departure),0) - ifnull(TIME_TO_SEC(sch_entry),0)) - ".
                        "( ifnull(TIME_TO_SEC(sch_lunchin),0) - ifnull(TIME_TO_SEC(sch_lunchout),0) ))/3600,2) sch_proghrs ".
						" from schedules where employee_id=".$dtE['employee_id']." and sch_date='".date("Y-m-d",strtotime($_POST['fechaIni'] . ' +'.$i.' day'))."'";

			$dtProgH = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0 and $dtProgH['0']['sch_proghrs']>0){
				$progh = $dtProgH['0']['sch_proghrs'];	
			}
			$i++;
			?>
			<td align="center"><?php echo $progh; ?></td>
			<?php
        }
		//Obtener la sumatoria de horas
		$totalProgramadas = 0;
		//$sqlText = "select sum(sch_proghrs) as sumhoras from schedules where employee_id=".$dtE['employee_id']." and sch_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."'";
        $sqlText = "select round((((SUM(ifnull(TIME_TO_SEC(sch_departure),0))) - (SUM(ifnull(TIME_TO_SEC(sch_entry),0)))) -  ".
                            "((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0)))))/3600,2) sumhoras  ".
    						" from schedules ".
    						" where employee_id = ".$dtE['employee_id'].
							" and sch_date between date '".$_POST['fechaIni']."' and '".$_POST['fechaFin']."'";
		$dtSumHorario = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0 and $dtSumHorario['0']['sumhoras']!=NULL){
			$totalProgramadas = $dtSumHorario['0']['sumhoras'];
		}
		?>
		<td><?php echo $totalProgramadas; ?></td></tr>
		<?php	
	}
				
?>
</table>
