<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=detailsQA.xls"); 
  
  $dtEva = $dbEx->selSql($_POST['sqlText']); 
  
  //Guardara nombres de los items y contabiliza Y, No y NA-
	$textos[] = "";
	$sumaYesItem[] = "";
	$sumaNoItem[] = "";
	$sumaNAItemp[] = "";
  
  ?>
  <table class="tblHead" align="center" cellpadding="6" cellspacing="1" border="1" bordercolor="#003366">
  <?php 
  if($dbEx->numrows>0){
			
			$dtItem = $dbEx->selSql($_POST['sqlItem']);
			//Evalua reporte para reporte de detalles de Customer services
			
			if($_POST['monit']==1){
				?>
				<tr class="showItem"><td width="3%"><b>BADGE</td><td><b>EMPLOYEE</td><td><b>Date</td>
				<?php
                foreach($dtItem as $dtI){
					?>
					<td><?php echo $dtI['item'];?></td>
                    <?php
					$textos[$dtI['item']] = $dtI['formcs_text'];
					$sumaYesItem[$dtI['item']] = 0;
					$sumaNoItem[$dtI['item']] = 0;
					$sumaNAItem[$dtI['item']] = 0;
				}
				?>
				</tr>
				<?php
                foreach($dtEva as $dtEv){
					$sqlText = "select id_itemcs, itemcs_total, itemcs_resp, formcs_item from itemcs_monitoring it inner join form_monitoring_cs f on f.id_formcs = it.id_formcs where id_monitcsemp=".$dtEv['id_monitcsemp']." and formcs_status='A' order by formcs_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						?>
						<tr><td><font size="-2"><?php echo $dtEv['username'];?></td><td><?php echo $dtEv['firstname']." ".$dtEv['lastname'];?></td><td><?php echo $dtEv['f1'];?></td>
                        <?php
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemcs_resp']=='Y'){ 
								$color = ' bgcolor="#006600"';
								$sumaYesItem[$dtItemE['formcs_item']] = $sumaYesItem[$dtItemE['formcs_item']] + 1;
							}
							else if($dtItemE['itemcs_resp']=='N'){
								$color = ' bgcolor="#FF0000"';
								$sumaNoItem[$dtItemE['formcs_item']] = $sumaNoItem[$dtItemE['formcs_item']] + 1;
							}
							else{
								$color = 'bgcolor="#333333"';
								$sumaNAItem[$dtItemE['formcs_item']] = $sumaNAItem[$dtItemE['formcs_item']] + 1;
							}
							?>
							<td align="center" <?php echo $color;?> ><font color="#FFFFFF"><?php echo $dtItemE['itemcs_resp'];?></font></td>	
							<?php
						}	
						?>
						<td><?php echo number_format($dtEv['monitcsemp_qualification'],2);?>%</td></tr>
					<?php 
                    }
				}

			}//Termina tabla de reporte detalles CS
			
			//Reporte de detalles sales
			
			else if($_POST['monit']==2){
				?>
				<tr><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td>Date</td>
                <?php 
				foreach($dtItem as $dtI){
					?>
					<td><?php echo $dtI['item'];?></td>
					<?php
                    $textos[$dtI['item']] = $dtI['formsales_text'];
					$sumaYesItem[$dtI['item']] = 0;
					$sumaNoItem[$dtI['item']] = 0;
					$sumaNAItem[$dtI['item']] = 0;
				}
				?>
				</tr>
                <?php
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemsales, itemsales_total, itemsales_resp, formsales_item from itemsales_monitoring it inner join form_monitoring_sales f on f.id_formsales = it.id_formsales where id_monitsalesemp=".$dtEv['id_monitsalesemp']." and formsales_status='A' order by formsales_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						?>
						<tr><td><?php echo $dtEv['username'];?></td><td><?php echo $dtEv['firstname']." ".$dtEv['lastname'];?></td><td><?php echo $dtEv['f1'];?></td>
						<?php
                        foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemsales_resp']=='Y'){ 
								$color = ' bgcolor="#006600"';
								$sumaYesItem[$dtItemE['formsales_item']] = $sumaYesItem[$dtItemE['formsales_item']] + 1;	
							}
							else if($dtItemE['itemsales_resp']=='N'){
								$color = ' bgcolor="#FF0000"';
								$sumaNoItem[$dtItemE['formsales_item']] = $sumaNoItem[$dtItemE['formsales_item']] + 1;		
							}
							else{
								$color = 'bgcolor="#333333"';
								$sumaNAItem[$dtItemE['formsales_item']] = $sumaNAItem[$dtItemE['formsales_item']] + 1;		
							}
							?>
							<td align="center" <?php echo $color;?> ><font color="#FFFFFF"><?php echo $dtItemE['itemsales_resp'];?></font></td>
							<?php 
						}	
						?>
						<td><?php echo number_format($dtEv['monitsales_qualification'],2);?>%</td></tr>
                        <?php
					}
				}
				
			}//Termina de evaluacion evaluacion de Sales
			
			
			//Evalua reporte para New Services
			else if($_POST['monit']==3){
				?>
				<tr><td ><B>BADGE</td><td align="center"><font size="-3"><B>EMPLOYEE</td><td><b>Date</td>
				<?php
                foreach($dtItem as $dtI){
					?>
					<td><?php echo $dtI['item'];?></td>
					<?php
                    $textos[$dtI['item']] = $dtI['formns_text'];
					$sumaYesItem[$dtI['item']] = 0;
					$sumaNoItem[$dtI['item']] = 0;
					$sumaNAItem[$dtI['item']] = 0;
				}
				?>
				</tr>
                <?php
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemns, itemns_total, itemns_resp, formns_item from itemns_monitoring it inner join form_monitoring_ns f on f.id_formns = it.id_formns where id_monitnsemp=".$dtEv['id_monitnsemp']." and formns_status='A' order by formns_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						?>
						<tr><td><?php echo $dtEv['username'];?></td><td><?php echo $dtEv['firstname']." ".$dtEv['lastname'];?></td><td><?php echo $dtEv['f1'];?></td>
						<?php
                        foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemns_resp']=='Y'){ 
								$color = ' bgcolor="#006600"';
								$sumaYesItem[$dtItemE['formns_item']] = $sumaYesItem[$dtItemE['formns_item']] + 1;
							}
							else if($dtItemE['itemns_resp']=='N'){
								$color = ' bgcolor="#FF0000"';
								$sumaNoItem[$dtItemE['formns_item']] = $sumaNoItem[$dtItemE['formns_item']] + 1;
							}
							else{
								$color = 'bgcolor="#333333"';
								$sumaNAItem[$dtItemE['formns_item']] = $sumaNAItem[$dtItemE['formns_item']] + 1;
							}
							?>
							<td align="center"  <?php echo $color;?> ><font color="#FFFFFF"><?php echo $dtItemE['itemns_resp'];?></font></td>	
							<?php 
						}	
						?>
						<td><?php echo number_format($dtEv['monitnsemp_qualification'],2);?>%</td></tr>
                        <?php
					}
				}
				
			}//Termina evaluacion para New service
			
			else if($_POST['monit']==4){
				?>
				<tr><td ><B>BADGE</td><td align="center"><font size="-3"><B>EMPLOYEE</td><td><b>Date</td>
				<?php
                foreach($dtItem as $dtI){
					?>
					<td><?php echo $dtI['item'];?></td>
					<?php
                    $totales[$dtI['item']] = 0;
				}
				?>
				</tr>
                <?php
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemchat, itemchat_total, itemchat_resp, formchat_item from itemchat_monitoring it inner join form_monitoring_chat f on f.id_formchat = it.id_formchat where id_monitchatemp=".$dtEv['id_monitchatemp']." and formchat_status='A' order by formchat_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						?>
						<tr><td><?php echo $dtEv['username'];?></td><td><?php echo $dtEv['firstname']." ".$dtEv['lastname'];?></td><td><?php echo $dtEv['f1'];?></td>
						<?php
                        foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemchat_resp']=='Y'){ $color = ' bgcolor="#006600"';}
							else if($dtItemE['itemchat_resp']=='N'){$color = ' bgcolor="#FF0000"';}
							else{$color = 'bgcolor="#333333"';}
							?>
							<td align="center"  <?php echo $color;?> ><font color="#FFFFFF"><?php echo $dtItemE['itemchat_resp'];?></font></td>	
							<?php 
                            $totales[$dtItemE['formchat_item']] = $totales[$dtItemE['formchat_item']] + $dtItemE['itemchat_total'];
						}	
						?>
						<td><?php echo number_format($dtEv['monitchatemp_qualification'],2);?>%</td></tr>
                        <?php
					}
				}	
			}
			?>
			<tr><td></Td></tr>
            <tr><td colspan="5" align="center"><b>Summary of Total Yes, No, NA by question</b></td></tr>
			<tr><td></td><td></td><td>Total Yes</td><td>Total No</td><td>Total NA</td></tr>
            <?php
			foreach($dtItem as $dtI){
				?>
				<tr><td><?php echo $dtI['item']; ?></td><td><?php echo $textos[$dtI['item']]; ?></td><td><?php echo $sumaYesItem[$dtI['item']]; ?></td><td><?php echo $sumaNoItem[$dtI['item']]; ?></td><td><?php echo $sumaNAItem[$dtI['item']]; ?></td></tr>
                <?php
			}
			
			
		}
		else{
			?>
			<tr><td colspan="4">No matches</td></tr>
		<?php
        }
		?>
		</table>
  


