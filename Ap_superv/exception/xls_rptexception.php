<?php
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_absent.xls");
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  
  $sqlText = "select distinct(e.employee_id), e.username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".$_POST['filtroTotalEmp']." order by firstname";

	$dt = $dbEx->selSql($sqlText);
 ?>
 <table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
 <?php 
 if($dbEx->numrows>0){
 ?>
 <tr><td>BADGE</td><td>EMPLOYEE</td><td>TOTAL TIME</td></tr>
 <?php foreach($dt as $dtE){ 
		$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex where ex.employee_id=".$dtE['employee_id']." ".$_POST['filtroTotalExc']." group by employee_id ";
		$dtEx = $dbEx->selSql($sqlText);
					$horasException = "0.0";
					if($dbEx->numrows>0){
						$horas = $dtEx['0']['hora']; 
						$min = $dtEx['0']['minutos']; 
						$minutos = $min%60; 
						$minutos = round($minutos/60,2);
						$formatMinutos = explode(".",$minutos);
						$h=0; 
						$h=(int)($min/60); 
						$horas+=$h;
						$horasException = $horas.".".$formatMinutos[1];	
						?>
						<tr><td><?php echo $dtE['username'];?></td><td><?php echo $dtE['firstname'].' '.$dtE['lastname']; ?></td><td> <?php echo round($horasException,2); ?></td></tr>
                        <?php
					}
 		}
 }
 ?>
