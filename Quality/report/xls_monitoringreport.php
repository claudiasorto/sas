<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=monitoringreport.xls"); 
  
  	$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$_POST['filtroCuenta']." and status_plxemp='A' order by firstname";
	$dtEmp = $dbEx->selSql($sqlText);
  	?>
    <table cellpadding="2" cellspacing="1" >
    <?php
	if($dbEx->numrows>0){
			
			$start = strtotime($_POST['fecha_ini']);
			$end = strtotime($_POST['fecha_fin']);
			
			$monitCS = false;
			$monitSales = false;
			$monitNS = false;
			$monitChat = false;
			if($_POST['monit']==0){$monitCS = true; $monitSales = true; $monitNS = true;}
			if($_POST['monit']==1){$monitCS = true;}
			if($_POST['monit']==2){$monitSales = true;}
			if($_POST['monit']==3){$monitNS = true;}
			if($_POST['monit']==4){$monitChat = true;}
			?>
			<tr><td align="center"><b>Badge</b></td><td align="center"><b>Employee</b></td>
			<?php
			$n = 0;
			
			for($i = $start; $i <=$end; $i +=86400){
				$nFecha = strtotime(date("Y/m/d",$i));
				$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
				?>
				<td style="border: 1px #638DBD inset;"><b><?php echo date('d/m/Y',$i); ?></b></td>
				<?php
				$n = $n + 1;
				if($dia==0){
					?>
					<td style="border: 1px #638DBD inset;"><b>Evaluations</td><td style="border: 1px #638DBD inset;"><b>TOTALS</td><td align="center" style="border: 1px #638DBD inset;"><b>%</b></td>
                    <?php
				}
				
			}
			//Busca por agente si se les ha realizado evaluacion
			
			
			foreach($dtEmp as $dtE){
				$flag = false;
				if($monitCS){
					$sqlText = "select e.employee_id from monitoringcs_emp m inner join employees e on e.employee_id=m.employee_id ".$_POST['filtroCS']." ".$_POST['filtroC']." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}
				}
				if($monitSales){
					$sqlText = "select e.employee_id from monitoringsales_emp m inner join employees e on e.employee_id=m.employee_id ".$_POST['filtroSA']." ".$_POST['filtroS']." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}
				}
				if($monitNS){
					$sqlText = "select e.employee_id from monitoringns_emp m inner join employees e on e.employee_id=m.employee_id ".$_POST['filtroNS']." ".$_POST['filtroN']." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}	
				}
				
				if($monitChat){
					$sqlText = "select e.employee_id from monitoringchat_emp m inner join employees e on e.employee_id=m.employee_id ".$_POST['filtroChat']." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}	
				}
				
				if($flag){
				$totalEvaSemana = 0;
				$sumaSemana = 0;
				$totalPorcentSemana = 0;
				$promSemana = 0;
				?>
				<tr><td style="border: 1px #638DBD inset;"><?php echo $dtE['username']; ?></td><td style="border: 1px #638DBD inset;"><?php echo $dtE['firstname']." ".$dtE['lastname']; ?></td>
                <?php
				for($i = $start; $i <=$end; $i +=86400){
					$nFecha = strtotime(date("Y/m/d",$i));
					$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
					if($dia==1){
						$totalEvaSemana = 0;
						$sumaSemana = 0;
						$totalPorcentSemana = 0;
						$promSemana = 0;	
					}
					$nCS = "";
					$promCS = "";
					$nSA = "";
					$promSA = "";
					$nNS = "";
					$promNS = "";
					$nChat = "";
					$promChat = "";
					$nTotal = "";
					$promTotal = "";
					$sumaCS = 0;
					$sumaSA = 0;
					$sumaNS = 0;
					$sumaChat = 0;
					if($monitCS){
					//Conteo por dias para customer services 
						$sqlText = "select sum(monitcsemp_qualification) as sumcs from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date='".date('Y-m-d',$i)."' ".$_POST['filtroC'];
						$dtSumCS = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitcsemp_qualification) as countcs from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date='".date('Y-m-d',$i)."' ".$_POST['filtroC'];
						$dtCountCS = $dbEx->selSql($sqlText);
						if($dtCountCS['0']['countcs']>0){
							$nCS = 	$dtCountCS['0']['countcs'];
							$sumaSemana = $sumaSemana + $dtSumCS['0']['sumcs'];
							$promCS = $dtSumCS['0']['sumcs']/$nCS;
							$sumaCS = $dtSumCS['0']['sumcs'];
						}
					}
					if($monitSales){
						//Conteo por dias para sales
						$sqlText = "select sum(monitsales_qualification) as sumsa from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date='".date('Y-m-d',$i)."' ".$_POST['filtroS'];
						$dtSumSA = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitsales_qualification) as countsa from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date='".date('Y-m-d',$i)."' ".$_POST['filtroS'];
						$dtCountSA = $dbEx->selSql($sqlText);
						if($dtCountSA['0']['countsa']>0){
							$nSA = $dtCountSA['0']['countsa'];
							$sumaSemana = $sumaSemana + $dtSumSA['0']['sumsa'];
							$promSA = $dtSumSA['0']['sumsa']/$nSA;	
							$sumaSA = $dtSumSA['0']['sumsa'];
						}
					}
					if($monitNS){
						//Conteo por dias para new service
						$sqlText = "select sum(monitnsemp_qualification) as sumns from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date='".date('Y-m-d',$i)."' ".$_POST['filtroN'];
						$dtSumNS = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitnsemp_qualification) as countns from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date='".date('Y-m-d',$i)."' ".$_POST['filtroN'];
						$dtCountNS = $dbEx->selSql($sqlText);
						if($dtCountNS['0']['countns']>0){
							$nNS = $dtCountNS['0']['countns'];
							$sumaSemana = $sumaSemana + $dtSumNS['0']['sumns'];
							$promNS = $dtSumNS['0']['sumns']/$nNS;
							$sumaNS = $dtSumNS['0']['sumns'];	
						}
					}
					if($monitChat){
						 //conteo por dias para Chat
						 $sqlText = "select sum(monitchatemp_qualification) as sumchat from monitoringchat_emp where employee_id=".$dtE['employee_id']." and monitchatemp_date='".date('Y-m-d',$i)."' ";
						 $dtSumChat = $dbEx->selSql($sqlText);
						 $sqlText = "select count(monitchatemp_qualification) as countchat from monitoringchat_emp where employee_id=".$dtE['employee_id']." and monitchatemp_date='".date('Y-m-d',$i)."' ";
						 $dtCountChat = $dbEx->selSql($sqlText);
						 if($dtCountChat['0']['countchat']>0){
							 $nChat = $dtCountChat['0']['countchat'];
							 $sumaSemana = $sumaSemana + $dtSumChat['0']['sumchat'];
							 $promChat = $dtSumChat['0']['sumchat']/$nChat;
							 $sumaChat = $dtSumChat['0']['sumchat'];
						 }	
					}
					//Sumar numeros de evaluaciones y promedios
					if($nCS>0 or $nSA>0 or $nNS>0 or $nChat>0){
						$nTotal = $nCS + $nSA + $nNS + $nChat;
						//$promTotal = ($promCS + $promSA + $promNS)/$nTotal;
						$promTotal = ($sumaCS + $sumaSA + $sumaNS + $sumaChat)/$nTotal;
						$promTotal = number_format($promTotal,2)."%";
						$totalEvaSemana = $totalEvaSemana + $nTotal;

					}
					$color = "#FFFFFF";
					if($promTotal>=0 and $promTotal <=75 and $promTotal!=""){
						$color = "#FC252B";
					}
					else if($promTotal>75 and $promTotal<=80){
						$color = "#FF9900";
					}
					else if($promTotal>80 and $promTotal<=90){
						$color = "#00FF33";
					}
					else if($promTotal>90 and $promTotal<=99){
						$color = "#7BBDEE";
					}
					else if($promTotal>99 and $promTotal <=100){
						$color = "#FB9E42";	
					}?>
					<td align="center" bgcolor="<?php echo $color;?>" style="border: 1px #638DBD inset;"><b><?php echo $promTotal; ?></b></td>
					<?php
					if($dia==0){
						if($totalEvaSemana >0){
							$promSemana = 	number_format(($sumaSemana/$totalEvaSemana),2);
						}
					$color = "#FFFFFF";
					if($promSemana>=0 and $promSemana <=75 and $totalEvaSemana>0){
						$color = "#FC252B";
					}
					else if($promSemana>75 and $promSemana<=80){
						$color = "#FF9900";
					}
					else if($promSemana>80 and $promSemana<=90){
						$color = "#00FF33";
					}
					else if($promSemana>90 and $promSemana<=99){
						$color = "#7BBDEE";
					}
					else if($promSemana>99 and $promSemana <=100){
						$color = "#FB9E42";	
					}?>
						<td align="center" style="border: 1px #638DBD inset;"><?php echo $totalEvaSemana;?></td><td style="border: 1px #638DBD inset;"><?php echo number_format($sumaSemana,2); ?></td><td style="border: 1px #638DBD inset;" bgcolor="<?php echo $color;?>" ><b><?php echo number_format($promSemana,2);?></b></td>
                        <?php
					}
					
					}//Termina de ver por empleado
					
				}//Termina for

			}
		}
  
?>