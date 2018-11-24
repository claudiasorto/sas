<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=averagesQA.xls"); 
  
  
  	$dtEmp = $dbEx->selSql($_POST['sqlTextEmp']);
	?>
	<table class="tblHead"  align="center" cellpadding="2" cellspacing="1" border="1" bordercolor="#003366">
    <?php
	if($dbEx->numrows>0){
		$dtItem = $dbEx->selSql($_POST['sqlItem']);
			//Evalua  para reporte de detalles de Customer services
			
			if($_POST['monit']==1){
				?>
				<tr style="background-color:#069; color:#FFF; font:Tahoma;"><td width="5px"><font size="-2">BADGE</td><td width="30px"><font size="-3">EMPLOYEE</td><td width="5px"><font size="-3">Num evaluations</td>
				<?php 
                foreach($dtItem as $dtI){
					?>
					<td width="5px"><?php echo $dtI['item'];?></td>
					<?php
                    $totales[$dtI['item']] = 0;
				}
				?>
                </tr>
				<?php
				foreach($dtEmp as $dtE){
					?>
					<tr style="font:Tahoma; color:#003"><td><font size="-2"><?php echo $dtE['username']; ?></td><td><font size="-2"><?php echo $dtE['firstname']." ".$dtE['lastname']; ?></td>
					<?php
                    
                    //Cuenta la cantidad de evaluaciones que ha tenido el agente 
					$sqlTextCount = "select count(id_monitcsemp) as cant from monitoringcs_emp where 1 ".$_POST['filtroCS']." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitcsemp_qualification) as calif from monitoringcs_emp where 1 ".$_POST['filtroCS']." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					?>
					
					<td width="5px"><?php echo $dtCount['0']['cant'];?></td>
					<?php
                    foreach($dtItem as $dtI){
						//Cuenta por item cuantas preguntas positivas  y negativas ha tenido
						$sqlText = "select count(id_itemcs) as citems from itemcs_monitoring it inner join monitoringcs_emp m on it.id_monitcsemp=m.id_monitcsemp where 1 ".$_POST['filtroCS']." and employee_id=".$dtE['emp']." and it.id_formcs=".$dtI['id']." and (itemcs_resp='Y' or itemcs_resp='N')";
						$dtCantPreg = $dbEx->selSql($sqlText);
						//Cuenta la cantidad de positivas
						$sqlText = "select count(id_itemcs) as yitems from itemcs_monitoring it inner join monitoringcs_emp m on it.id_monitcsemp=m.id_monitcsemp where 1 ".$_POST['filtroCS']." and employee_id=".$dtE['emp']." and it.id_formcs=".$dtI['id']." and itemcs_resp='Y'";
						$dtCantY = $dbEx->selSql($sqlText);

						//Cuenta la cantidad de negativas
						$sqlText = "select count(id_itemcs) as nitems from itemcs_monitoring it inner join monitoringcs_emp m on it.id_monitcsemp=m.id_monitcsemp where 1 ".$_POST['filtroCS']." and employee_id=".$dtE['emp']." and it.id_formcs=".$dtI['id']." and itemcs_resp='N'";
						$dtCantN = $dbEx->selSql($sqlText);
						
						if($dtCantPreg['0']['citems']>0){
							$totalY = number_format(($dtCantY['0']['yitems']/$dtCantPreg['0']['citems'])*100,2)."%";
							$totalN = number_format(($dtCantN['0']['nitems']/$dtCantPreg['0']['citems'])*100,2)."%";
						}
						else{
							$totalY = "N/A";
							$totalN = "N/A";	
						}
						$color = "#FFFFFF";
						if($totalY>=0 and $totalY <=75 and $totalY!="N/A"){
							$color = "#FF0000";
						}
						else if($totalY>75 and $totalY<=80){
							$color = "#FFCC00";
						}
						else if($totalY>80 and $totalY<=90){
							$color = "#009933";
						}
						else if($totalY>90 and $totalY<=99){
							$color = "#0066CC";
						}
						else if($totalY>99 and $totalY <=100){
							$color = "#FB9E42";	
						}
						?>
						<td bgcolor="<?php echo $color;?>"><?php echo $totalY;?></td>
                        <?php
					}
					?>
					<td><?php echo number_format($promEva,2);?>%</td></tr>
				<?php
                }
				
			}//Termina tabla de reporte detalles CS
			
			//Reporte de detalles sales
			
			else if($_POST['monit']==2){
				?>
				<tr class="showItem"><td width="5px"><font size="-2">BADGE</td><td width="30px"><font size="-3">EMPLOYEE</td><td width="5px"><font size="-3">Num evaluations</td>
				<?php
                foreach($dtItem as $dtI){
					?>
					<td width="5px"><?php echo $dtI['item'];?></td>
					<?php
					$totales[$dtI['item']] = 0;
				}
				?>
				</tr>
                <?php
				foreach($dtEmp as $dtE){
					?>
					<tr class="rowCons"><td><font size="-2"><?php echo $dtE['username'];?></td><td><font size="-2"><?php echo $dtE['firstname']." ".$dtE['lastname'];?></td>
                    <?php
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitsalesemp) as cant from monitoringsales_emp where 1 ".$_POST['filtroSales']." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitsales_qualification) as calif from monitoringsales_emp where 1 ".$_POST['filtroSales']." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					?>
					
                    <td><?php echo $dtCount['0']['cant'];?></td>
					<?php
                    foreach($dtItem as $dtI){
						//Cuenta por item cuantas preguntas positivas  y negativas ha tenido
						$sqlText = "select count(id_itemsales) as citems from itemsales_monitoring it inner join monitoringsales_emp m on it.id_monitsalesemp=m.id_monitsalesemp where 1 ".$_POST['filtroSales']." and employee_id=".$dtE['emp']." and it.id_formsales=".$dtI['id']." and (itemsales_resp='Y' or itemsales_resp='N')";
						$dtCantPreg = $dbEx->selSql($sqlText);
						//Cuenta la cantidad de positivas
						$sqlText = "select count(id_itemsales) as yitems from itemsales_monitoring it inner join monitoringsales_emp m on it.id_monitsalesemp=m.id_monitsalesemp where 1 ".$_POST['filtroSales']." and employee_id=".$dtE['emp']." and it.id_formsales=".$dtI['id']." and itemsales_resp='Y'";
						$dtCantY = $dbEx->selSql($sqlText);

						//Cuenta la cantidad de negativas
						$sqlText = "select count(id_itemsales) as nitems from itemsales_monitoring it inner join monitoringsales_emp m on it.id_monitsalesemp=m.id_monitsalesemp where 1 ".$_POST['filtroSales']." and employee_id=".$dtE['emp']." and it.id_formsales=".$dtI['id']." and itemsales_resp='N'";
						$dtCantN = $dbEx->selSql($sqlText);
						
						if($dtCantPreg['0']['citems']>0){
							$totalY = number_format(($dtCantY['0']['yitems']/$dtCantPreg['0']['citems'])*100,2)."%";
							$totalN = number_format(($dtCantN['0']['nitems']/$dtCantPreg['0']['citems'])*100,2)."%";
						}
						else{
							$totalY = "N/A";
							$totalN = "N/A";	
						}
						$color = "#FFFFFF";
						if($totalY>=0 and $totalY <=75 and $totalY!="N/A"){
							$color = "#FF0000";
						}
						else if($totalY>75 and $totalY<=80){
							$color = "#FFCC00";
						}
						else if($totalY>80 and $totalY<=90){
							$color = "#009933";
						}
						else if($totalY>90 and $totalY<=99){
							$color = "#0066CC";
						}
						else if($totalY>99 and $totalY <=100){
							$color = "#FB9E42";	
						}
						?>
						<td bgcolor="<?php echo $color;?>"><?php echo $totalY;?></td>
					<?php
                    }
					?>
					<td><?php echo number_format($promEva,2);?>%</td></tr>
                    <?php
				}
				
			}//Termina de evaluacion evaluacion de Sales
			
			//Evalua reporte para New Services
			else if($_POST['monit']==3){
				?>
				<tr class="showItem"><td width="5px"><font size="-2">BADGE</td><td width="25px" ><font size="-3">EMPLOYEE</td><td width="5px"><font size="-3">Num. Evaluations</td>
				<?php
                foreach($dtItem as $dtI){
					?>
					<td width="5px"><?php echo $dtI['item'];?></td>
                    <?php 
					$totales[$dtI['item']] = 0;
				}?>
				</tr>
				<?php 
                foreach($dtEmp as $dtE){
					?>
					<tr class="rowCons"><td><font size="-2"><?php echo $dtE['username'];?></td><td><font size="-2"><?php echo $dtE['firstname']." ".$dtE['lastname'];?></td>
                    <?php 
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitnsemp) as cant from monitoringns_emp where 1 ".$_POST['filtroNS']." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitnsemp_qualification) as calif from monitoringns_emp where 1 ".$_POST['filtroNS']." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}?>
					<td><?php echo $dtCount['0']['cant'];?></td>
					<?php 
                    foreach($dtItem as $dtI){
						//Cuenta por item cuantas preguntas positivas  y negativas ha tenido
						$sqlText = "select count(id_itemns) as citems from itemns_monitoring it inner join monitoringns_emp m on it.id_monitnsemp=m.id_monitnsemp where 1 ".$_POST['filtroNS']." and employee_id=".$dtE['emp']." and it.id_formns=".$dtI['id']." and (itemns_resp='Y' or itemns_resp='N')";
						$dtCantPreg = $dbEx->selSql($sqlText);
						//Cuenta la cantidad de positivas
						$sqlText = "select count(id_itemns) as yitems from itemns_monitoring it inner join monitoringns_emp m on it.id_monitnsemp=m.id_monitnsemp where 1 ".$_POST['filtroNS']." and employee_id=".$dtE['emp']." and it.id_formns=".$dtI['id']." and itemns_resp='Y'";
						$dtCantY = $dbEx->selSql($sqlText);

						//Cuenta la cantidad de negativas
						$sqlText = "select count(id_itemns) as nitems from itemns_monitoring it inner join monitoringns_emp m on it.id_monitnsemp=m.id_monitnsemp where 1 ".$_POST['filtroNS']." and employee_id=".$dtE['emp']." and it.id_formns=".$dtI['id']." and itemns_resp='N'";
						$dtCantN = $dbEx->selSql($sqlText);
						
						if($dtCantPreg['0']['citems']>0){
							$totalY = number_format(($dtCantY['0']['yitems']/$dtCantPreg['0']['citems'])*100,2)."%";
							$totalN = number_format(($dtCantN['0']['nitems']/$dtCantPreg['0']['citems'])*100,2)."%";
						}
						else{
							$totalY = "N/A";
							$totalN = "N/A";	
						}
						$color = "#FFFFFF";
						if($totalY>=0 and $totalY <=75 and $totalY!="N/A"){
							$color = "#FF0000";
						}
						else if($totalY>75 and $totalY<=80){
							$color = "#FFCC00";
						}
						else if($totalY>80 and $totalY<=90){
							$color = "#009933";
						}
						else if($totalY>90 and $totalY<=99){
							$color = "#0066CC";
						}
						else if($totalY>99 and $totalY <=100){
							$color = "#FB9E42";	
						}
						?>
						<td bgcolor="<?php echo $color;?>"><?php echo $totalY;?></td>
                        <?php
					}?>
					<td><?php echo number_format($promEva,2);?>%</td></tr>
                    <?php
				}
				
			}//Termina New Service

		}
		else{?>
			<tr><td colspan="4">No matches</td></tr>
            <?php
		}
		?>
		</table>
