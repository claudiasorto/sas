<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=LOB_Average.xls");
  
  	$fec_ini = $_POST['fec_ini'];
  	$fec_fin = $_POST['fec_fin'];
  	$start = strtotime($fec_ini);
 	$end = strtotime($fec_fin);
  	//Total de semanas
	$totalSemanas = 0;
	for($i=$start; $i<=$end; $i +=86400){
		$nFecha = strtotime(date("Y/m/d",$i));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia==1){
			$totalSemanas = $totalSemanas + 1;
			$lunes[$totalSemanas] = $i;	
		}
		else if($dia==6){
			$sabado[$totalSemanas] = $i;
		}
	}
	$filtro = "";
	if($_POST['cuenta']>0){
		$filtro .=" and id_account=".$_POST['cuenta'];
	}
		
	$sqlText = "select * from account where account_status='A' and id_typeacc=2 ".$filtro." order by name_account ";
	$dtCuenta = $dbEx->selSql($sqlText);
	
	?>
    
    <table align="center" >
    
    <?php
	if($dbEx->numrows>0){
		foreach($dtCuenta as $dtC){
				$filtro = "";
				if($_POST['sup']>0){
					$filtro .=" and e.employee_id=".$_POST['sup'];
				}
				$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where u.name_role='SUPERVISOR' and pe.status_plxemp='A' and e.user_status=1 ".$filtro." and pd.id_account=".$dtC['ID_ACCOUNT']." order by firstname";	
				$dtSup = $dbEx->selSql($sqlText);
			
				if($dbEx->numrows>0){
					?>
					<tr bgcolor="#006699"><td align="center"><font color="#FFFFFF"><b><?php echo $dtC['NAME_ACCOUNT']; ?></b></td></tr>
					
					<tr>
                    <?php
					$sumaEvaCuenta = 0;
					$nEvaCuenta = 0;
					foreach($dtSup as $dtS){
						$sumaEvaSup=0;
						$nEvaSup = 0;
						?>
                        
						<td>
						<table  class="tblRepQA" border="1" align="center" cellpadding="2" cellspacing="1">
						<tr><td colspan="2" align="center" bgcolor="#000000"><font color="#FFFFFF">TEAM <?php echo $dtS['firstname']." ".$dtS['lastname']; ?></font></td></tr>
						
                        <?php
						for($i=1; $i<=$totalSemanas; $i++){
							$sumaEvaSemana = 0;
							$nEvaSemana = 0;
							//Busca el total de evaluaciones de CS a la semana
							$sqlText = "select employee_id from employees where id_supervisor=".$dtS['employee_id'];
							$dtEmp = $dbEx->selSql($sqlText);
							foreach($dtEmp as $dtE){
								$sumaEvaEmp = 0;
								$nEvaEmp = 0;
								$sqlText = "select sum(monitcsemp_qualification) as sumaCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' and monitcsemp_averages=1";
								$dtSumaCS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0){
									$sumaEvaEmp = $sumaEvaEmp + $dtSumaCS['0']['sumaCS'];
								}
							
								$sqlText = "select count(1) as cantCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' and monitcsemp_averages=1";
								$dtCantCS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0){
									$nEvaEmp= $nEvaEmp + $dtCantCS['0']['cantCS'];
								}
							
								//Busca el total de evaluaciones de NS a la semana
								$sqlText = "select sum(monitnsemp_qualification) as sumaNS from monitoringns_emp  where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' and monitnsemp_averages=1 ";
								$dtSumaNS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0){
									$sumaEvaEmp = $sumaEvaEmp + $dtSumaNS['0']['sumaNS'];
								}
								$sqlText = "select count(1) as cantNS from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' and monitnsemp_averages=1 ";
								$dtCantNS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0){
									$nEvaEmp = $nEvaEmp + $dtCantNS['0']['cantNS'];
								}
							
								//Busca el total de evaluaciones de Sales a la semana
								$sqlText = "select sum(monitsales_qualification) as sumaSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' and monitsales_averages=1 ";
								$dtSumaSales = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0){
									$sumaEvaEmp = $sumaEvaEmp + $dtSumaSales['0']['sumaSales'];
								}
								$sqlText = "select count(1) as cantSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' and monitsales_averages=1 ";
								$dtCantSales = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0){
									$nEvaEmp = $nEvaEmp + $dtCantSales['0']['cantSales'];
								}
								$promEmp = "";
								if($nEvaEmp>0){
									$promEmp = $sumaEvaEmp/$nEvaEmp;
									$nEvaSemana = $nEvaSemana + 1;
									$sumaEvaSemana = $sumaEvaSemana + $promEmp;	
								}
								
							}
							$promSemana = "";
							if($nEvaSemana>0){
								$promSemana = number_format(($sumaEvaSemana/$nEvaSemana),2)."%";
								$nEvaSup = $nEvaSup + 1;
								$sumaEvaSup = $sumaEvaSup + $promSemana;
							}
							?>
							<tr bgcolor="#FFFFFF"><td>Week <?php echo $i; ?></td><td><?php echo $promSemana; ?></td></tr>
                            <?php
						}
						$promSup = "";
						if($nEvaSup>0){
							$promSup = number_format(($sumaEvaSup/$nEvaSup),2)."%";
							$nEvaCuenta = $nEvaCuenta + 1;
							$sumaEvaCuenta = $sumaEvaCuenta + $promSup;
						}
						?>
						<tr bgcolor="#CC6600"><td colspan="2"><font color="#000000"><b> Average: <?php echo $promSup; ?></font></td></tr>
						</table>
						</td><td></td>
                        <?php
					}
					?>
					</tr>
                    <?php
				}
				$promCuenta = "";
				if($nEvaCuenta>0){
					$promCuenta = number_format(($sumaEvaCuenta/$nEvaCuenta),2)."%";	
				}
				?>
				<tr bgcolor="#006699"><td align="center"><font color="#FFFFFF"><b>TOTAL AVERAGE <?php echo $dtC['NAME_ACCOUNT']." ".$promCuenta; ?></b></td></tr><tr><td><br></td></tr>
                <?php
			}	
	}
  
?>