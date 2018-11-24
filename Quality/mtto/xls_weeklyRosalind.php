<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=OkcWeeklyReport.xls");
  
 	$start = strtotime($_POST['fec_ini']);
	$end = strtotime($_POST['fec_fin']); 
  	
	$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".$_POST['filtro']." and status_plxemp='A' order by firstname";

	$dtEmp = $dbEx->selSql($sqlText);
	
	?>
    <table border="1" bordercolor="#003366">
    
    <tr><td colspan="18" align="center">WEEKLY MONITORING REPORT <?php echo $_POST['fechaIni']." - ".$_POST['fechaFin']; ?></td></tr>
	<tr><td colspan="13"></td><td>Excellent</td><td>Great</td><td>Good</td><td>Fair</td><td>Poor</td></tr>
	<tr><td>Badge ID</td>
    <td>REPS</td>
    <td>MON</td>
    <td>TUES</td>
    <td>WED</td>
    <td>THURS</td>
    <td>FRI</td>
    <td>SAT</td>
    <td align="center">1</td><td align="center">2</td><td align="center">3<td>TOTALS</td>
			<td align="center">%</td><td>100%</td><td>99-90%</td><td>89-80%</td><td>79-70%</td><td>69%-BELOW</td></tr>
    
    <?php
	//Acumula el total de promedios
	//Suma los promedios
 	$sumaTotalEva = 0;
	$promTotalEva = 0;
	foreach($dtEmp as $dtE){
		
		$linea = "";
		$flag = false;

	    $linea .='<tr><td>'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';

	//Inicializa el n para guardar un array de las notas
	$notas = array();
	$n = 0;
	for($i = $start; $i<=$end; $i +=86400){
		//Evaluaciones de CS
		//Cantidad de evaluaciones al dia para poner numero de X
		$equis = '';
		$sqlText = "select monitcsemp_qualification from monitoringcs_emp ".$_POST['filtroCS']." and monitcsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
		$dtMonitCs = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtMonitCs as $dtCS){
				$notas[$n]['calif'] = $dtCS['monitcsemp_qualification'];
				$n = $n+1;
			}
		}
		for($j=1; $j<=$dbEx->numrows; $j++){
			$equis .=' X ';	
		}
					
		//Evaluaciones de NS
		$sqlText = "select monitnsemp_qualification from monitoringns_emp ".$_POST['filtroNS']." and monitnsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
		$dtMonitNs = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtMonitNs as $dtNs){
				$notas[$n]['calif'] = $dtNs['monitnsemp_qualification'];
				$n = $n+1;
			}
		}
		for($j=1; $j<=$dbEx->numrows; $j++){
			$equis .=' X ';	
		}
					
		//Evaluaciones de Sales
		$sqlText = "select monitsales_qualification from monitoringsales_emp ".$_POST['filtroSales']." and monitsales_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
		$dtMonitSales = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtMonitSales as $dtS){
				$notas[$n]['calif'] = $dtS['monitsales_qualification'];
				$n = $n+1;
			}
		}
		for($j=1; $j<=$dbEx->numrows; $j++){
			$equis .=' X ';	
		}
		
		$linea .='<td>'.$equis.'</td>';
		
    }
	//Termina de poner X y guardar notas
	$suma = 0;
	$calif = "";
	for($i=0; $i<$n; $i++){
		$suma = $suma + $notas[$i]['calif'];
	}
	$total = 0;
	if($n>0){
		$total = $suma/$n;	
		$flag = true;
	}
	if($n>=1){
		$linea .='<td>'.number_format($notas[0]['calif'],2).'%</td>';
	}
	else{
		$linea .='<td></td>';
	}
	if($n>=2){
		$linea .='<td>'.number_format($notas[1]['calif'],2).'%</td>';
	}
	else{
		$linea .='<td></td>';
	}
	if($n>=3){
		$linea .='<td>'.number_format($notas[2]['calif'],2).'%</td>';

	}
	else{
		$linea .='<td></td>';	
	}
	if($n>0){
		$linea .='<td>'.number_format($suma,2).'</td>
			<td>'.number_format($total,2).'%</td>';
	}
	else{
		$linea .='<td></td><td></td>';
	}
	if($total==100){
		 $linea .='<td align="center">X</td><td></td><td></td><td></td><td></td></tr>';
	}
	else if($total>=90 and $total<100){
		$linea .='<td></td><td align="center">X</td><td></td><td></td><td></td></tr>';	

    }
	else if($total>=80 and $total<90){
		$linea .='<td></td><td></td><td align="center">X</td><td></td><td></td></tr>';
	}
	else if($total>=70 and $total<80){
		$linea .='<td></td><td></td><td></td><td align="center">X</td><td></td></tr>';	
	}
	else if($total<70 and $n>0){
		$linea .='<td></td><td></td><td></td><td></td><td align="center">X</td></tr>';

    }
	else{
		$linea .='<td></td><td></td><td></td><td></td><td></td></tr>';	
	}
	if($flag){
		echo $linea;		
	}
	
}
	?>
    </table>
	