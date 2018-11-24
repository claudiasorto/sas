<?php
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_absent.xls");
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  function restaHoras($horaIni, $horaFin){
	return (date("H:i:s", strtotime("00:00:00") + strtotime($horaFin) - strtotime($horaIni) ));
	}
  
  $sqlText = "select distinct(ex.exceptionemp_id), e.employee_id, date_format(exceptionemp_date,'%d/%m/%Y') as f1, exceptionemp_hini, exceptionemp_hfin, ex.exceptiontp_id, exceptionemp_comment, exceptionemp_approved, exceptiontp_name, e.username, firstname, lastname, id_supervisor from exceptionxemp ex inner join exceptions_type tp on ex.exceptiontp_id=tp.exceptiontp_id inner join employees e on e.employee_id=ex.employee_id inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".$_POST['filtro']." order by exceptionemp_date desc, ex.exceptionemp_id desc";
  
	$dt = $dbEx->selSql($sqlText);
 ?>
 <table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
 <?php 
 if($dbEx->numrows>0){
 ?>
 <tr><td>N&deg;</td><td>BADGE</td width="30%"><td>EMPLOYEE</td><td>SUPERVISOR</td><td>TYPE EXCEPTION</td><td>DATE</td><td>TOTAL TIME</td><td>OBSERVATIONS</td><td>STATUS</td></tr>
 <?php foreach($dt as $dtE){
 		$tiempoTotal = restaHoras($dtE['exceptionemp_hini'],$dtE['exceptionemp_hfin']);
		$estado = "";
		if($dtE['exceptionemp_approved']=='A'){
			$estado = 'Approved';
		}
		else if($dtE['exceptionemp_approved']=='P'){
			$estado = 'In progress';
		}
		$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['id_supervisor'];
		$dtSup = $dbEx->selSql($sqlText);
		$nombreSup = "";
		if($dbEx->numrows>0){
			$nombreSup = $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];	
		}
		
 		 ?>
			<tr><td><?php echo $dtE['exceptionemp_id']; ?></td><td><?php echo $dtE['username']; ?></td><td><?php echo $dtE['firstname']." ".$dtE['lastname']; ?></td><td><?php echo $nombreSup; ?></td><td><?php echo $dtE['exceptiontp_name']; ?></td><td><?php echo $dtE['f1']; ?></td><td><?php echo $tiempoTotal; ?></td><td><?php echo $dtE['exceptionemp_comment']; ?></td><td><?php echo $estado; ?></td></tr>
		
 <?php
 		}
 }
 ?>
