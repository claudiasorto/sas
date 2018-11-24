<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=monthlyReport.xls");
  
 	$start = strtotime($_POST['fec_ini']);
	$end = strtotime($_POST['fec_fin']);
	
	$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".$_POST['filtro']." and status_plxemp='A' order by firstname";
	$dtEmp = $dbEx->selSql($sqlText);
	?>
	<table border="1" align="center" cellpadding="2" cellspacing="1" >
    <?php
		if($dbEx->numrows>0){
	//Verificar el total de semanas para mostrar los titulos 
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
			?>
			<tr><td  bgcolor="#003366"><font color="#FFFFFF">Badge</td><td  bgcolor="#003366"><font color="#FFFFFF">Month to Date</font></td>
            <?php
			for($i=1; $i<=$totalSemanas; $i++){
				?>
				<td  bgcolor="#003366"><font color="#FFFFFF">Average Week <?php echo $i; ?></font></td>
                <?php
			}
			?>
			<td  bgcolor="#003366"><font color="#FFFFFF">MTD per agent</font></td></tr>
            <?php
			//Empieza a recorrer los agentes para obtener el reporte
			
			foreach($dtEmp as $dtE){
				$nEvaMes = 0;
				$sumaEvaMes = 0;
				$sumaPromMes = 0;
				$nPromMes = 0;
				
				$linea = "";
				$flag = false;
				
				$linea .='<tr><td>'.$dtE['username'].'</td><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td>';

                //Obtiene la suma de scores y total de evaluaciones en la semana 
				for($i=1; $i<=$totalSemanas; $i++){
					$sumaEvaSemana = 0;
					$nEvaSemana = 0;
					//Busca el total de evaluaciones de CS a la semana
					$sqlText = "select sum(monitcsemp_qualification) as sumaCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' ".$_POST['filtroCS'];
					$dtSumaCS = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sumaEvaSemana = $sumaEvaSemana + $dtSumaCS['0']['sumaCS'];
					}
					
					$sqlText2 = "select count(1) as cantCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' ".$_POST['filtroCS'];
					$dtCantCS = $dbEx->selSql($sqlText2);
					if($dbEx->numrows>0){
						$nEvaSemana = $nEvaSemana + $dtCantCS['0']['cantCS'];
					}
					//Busca el total de evaluaciones de NS a la semana
					$sqlText = "select sum(monitnsemp_qualification) as sumaNS from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' ".$_POST['filtroNS'];
					$dtSumaNS = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sumaEvaSemana = $sumaEvaSemana + $dtSumaNS['0']['sumaNS'];
					}
					$sqlText = "select count(1) as cantNS from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' ".$_POST['filtroNS'];
					$dtCantNS = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$nEvaSemana = $nEvaSemana + $dtCantNS['0']['cantNS'];
					}
					
					//Busca el total de evaluaciones de Sales a la semana
					$sqlText = "select sum(monitsales_qualification) as sumaSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' ".$_POST['filtroSales'];
					$dtSumaSales = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sumaEvaSemana = $sumaEvaSemana + $dtSumaSales['0']['sumaSales'];
					}
					$sqlText = "select count(1) as cantSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' ".$_POST['filtroSales'];
					$dtCantSales = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$nEvaSemana = $nEvaSemana + $dtCantSales['0']['cantSales'];
					}
					$promSemana = "";
					if($nEvaSemana>0){
						$promSemana = number_format(($sumaEvaSemana/$nEvaSemana),2)."%";
						$nEvaMes = $nEvaMes + 1;
						$sumaEvaMes = $sumaEvaMes + $promSemana;
						
					}

					$linea .='<td>'.$promSemana.'</td>';
 
				}
				$promMes = "";
				if($nEvaMes>0){
					$promMes = number_format(($sumaEvaMes/$nEvaMes),2)."%";
					$nPromMes = $nPromMes + 1;
					$sumaPromMes = $sumaPromMes + $promMes;
					$flag = true;
				}
				
				$linea .='<td>'.$promMes.'</td></tr>';
				
				if($flag){
					echo $linea;
						
				}
			}
			
		}
		else{
			?>
			<tr><td>No matches</td></tr>
            <?php
		}?>
		</table>