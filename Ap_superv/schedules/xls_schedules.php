<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  
  $sqlText = "select distinct(e.employee_id) emp_id, username, firstname, lastname from schedules sh inner join employees e on sh.employee_id=e.employee_id inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$_POST['filtro']." and pe.status_plxemp='A' order by firstname ";
$dtEmp = $dbEx->selSql($sqlText);
  
  	$start = strtotime($_POST['fechaIni']);
	$end = strtotime($_POST['fechaFin']);
	
	header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  	header("Content-Disposition: attachment; filename=rpt_schedules.xls");
  
  ?>
  <table border="1">
  <tr bgcolor="#003366"><td align="center"><font color="#FFFFFF"><b>BADGE</b></font></td><td align="center"><font color="#FFFFFF"><b>EMPLOYEE</b></td>
  <?php
		//Primer for solo para mostrar las fechas
		for($i=$start; $i<=$end; $i +=86400){
				
	?>
			<td align="center"><b><font color="#FFFFFF"><?php echo date("d/m/Y",$i); ?></font></b></td>
	<?php	
	}
	?>
			</tr>
  
<?php
	//Por empleado busca los horarios del periodo seleccionado
			foreach($dtEmp as $dtE){
				?>
				<tr>
                <td align="center" height="25"><?php echo $dtE['username'];?></td><td height="25"><?php echo $dtE['firstname']." ".$dtE['lastname'];?></td>
				<?php
				for($i = $start; $i<=$end; $i+=86400){
					$sqlText = "select time_format(sch_entry,'%H:%i') as SCH_ENTRY, time_format(sch_break1in,'%H:%i') as SCH_BREAK1IN, ".
					"time_format(sch_break1out,'%H:%i') as SCH_BREAK1OUT, time_format(sch_lunchin,'%H:%i') as SCH_LUNCHIN, time_format(sch_lunchout,'%H:%i') as SCH_LUNCHOUT, ".
					"time_format(sch_break2in,'%H:%i') as SCH_BREAK2IN, time_format(sch_break2out,'%H:%i') as SCH_BREAK2OUT, time_format(sch_departure,'%H:%i') as SCH_DEPARTURE, ".
					"SCH_OFF from schedules where employee_id=".$dtE['emp_id']." and sch_date='".date("Y-m-d",$i)."'";
					$dtSch = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						if($dtSch['0']['SCH_OFF']=='Y'){
							?>
							<td align="center" height="25">OFF</td>
                            <?php
						}
						else{
							?>
                            <td height="25"> 
							<table bgcolor="#FFFFFF" border="1" bordercolor="#003366" style="border:outset">
							<tr><td class="txtForm">Entry </td><td class="txtPag"><?php echo $dtSch['0']['SCH_ENTRY']; ?></td></tr>
							<tr><td class="txtForm">Break 1 </td><td class="txtPag"><?php echo $dtSch['0']['SCH_BREAK1OUT']." - ".$dtSch['0']['SCH_BREAK1IN'];?></td></tr>
							<tr><td class="txtForm">Lunch </td><td class="txtPag"><?php echo $dtSch['0']['SCH_LUNCHOUT']." - ".$dtSch['0']['SCH_LUNCHIN'];?></td></tr>
							<tr><td class="txtForm">Break 2 </td><td class="txtPag"><?php echo $dtSch['0']['SCH_BREAK2OUT']." - ".$dtSch['0']['SCH_BREAK2IN'];?></td></tr>
							<tr><td class="txtForm">End of Duty</td><td class="txtPag"><?php echo $dtSch['0']['SCH_DEPARTURE']; ?></td></tr></table>
                            </td>
					<?php
                    	}
					}
					else{
						?>
						<td align="center" height="25"> - </td>
                        <?php
					}
				}//Termina for
				?>
                
				</tr>
                <?php
			}//Termina foreach de Empleado
			?>
</table>
