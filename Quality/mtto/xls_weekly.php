<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=weeklyReport.xls");
  
 	$start = strtotime($_POST['fec_ini']);
	$end = strtotime($_POST['fec_fin']); 
  	
  	$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".$_POST['filtro']." and status_plxemp='A' order by firstname";
	$dtEmp = $dbEx->selSql($sqlText);
	
	//Acumula el total de promedios
	//Suma los promedios
	$sumaTotalEva = 0;
	$promTotalEva = 0;
	
	?>
    <table border="1" bordercolor="#003366">
    <tr><td bgcolor="#003366"><font color="#FFFFFF"> Badge</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">Agents</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">Supervisor</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">Quality Agent</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">MON</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">TUES</font></td>
	<td bgcolor="#003366"><font color="#FFFFFF">WED</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">THURS</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">FRI</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">SAT</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">Total</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">1</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">2</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">3</font></td>
    <td bgcolor="#003366"><font color="#FFFFFF">4</font></td></tr>
    
    <?php
  foreach($dtEmp as $dtE){
		//Inicializa el n para guardar un array de las notas
		$notas = array();
		$n = 0;
		//Obtiene el nombre del supervisor
		$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['id_supervisor'];
		$dtSup = $dbEx->selSql($sqlText);
		$nombreSup = "";
		if($dbEx->numrows>0){
			$nombreSup = $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];
		}
		
		$linea = "";
		$flag = false;
		
		//Titulos
		$linea .= '<tr><td>'.$dtE['username'].'</td>
		<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>
		<td>'.$nombreSup.'</td>';
		//Obtiene los QA agents 
		$listaQa = "";
		$qas = "";
		$nQa = 0;
		$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringcs_emp m inner join employees e on m.qa_agent=e.employee_id ".$_POST['filtroCS']." and monitcsemp_date between '".$_POST['fec_ini']."' and '".$_POST['fec_fin']."' and m.employee_id=".$dtE['employee_id'];
		$dtQa = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtQa as $dtQ){
			$listaQa .=" ".$dtQ['firstname']." ".$dtQ['lastname'];
				if($nQa>0){
					$qas .=", ";
				}
				$qas .= $dtQ['qa_agent'];
				$nQa = $nQa + 1;	
			}
		}
		$filtroQa = "";
		if($nQa >0){
			$filtroQa .= " and qa_agent not in (".$qas.")";	
		}
				
		$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringns_emp m inner join employees e on m.qa_agent=e.employee_id ".$_POST['filtroNS']." and monitnsemp_date between '".$_POST['fec_ini']."' and '".$_POST['fec_fin']."' and m.employee_id=".$dtE['employee_id']." ".$filtroQa;
		$dtQa = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtQa as $dtQ){
				$listaQa .=" ".$dtQ['firstname']." ".$dtQ['lastname'];
				if($nQa>0){
					$qas .=", ";
				}
				$qas .= $dtQ['qa_agent'];
				$nQa = $nQa + 1;	
			}
		}
		$filtroQa = "";
		if($nQa >0){
			$filtroQa .= " and qa_agent not in (".$qas.")";	
		}
				
		$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringsales_emp m inner join employees e on m.qa_agent=e.employee_id ".$_POST['filtroSales']." and monitsales_date between '".$_POST['fec_ini']."' and '".$_POST['fec_fin']."' and m.employee_id=".$dtE['employee_id']." ".$filtroQa;
		$dtQa = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtQa as $dtQ){
				$listaQa .=" ".$dtQ['firstname']." ".$dtQ['lastname'];
				if($nQa>0){
					$qas .=", ";
				}
				$qas .= $dtQ['qa_agent'];
				$nQa = $nQa + 1;	
			}
		}
				//Imprime los Qa creadores
				

		$linea .='<td>'.$listaQa.'</td>';
	
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
			$calif .='<td>'.number_format($notas[$i]['calif'],2).'%</td>';
			$suma = $suma + $notas[$i]['calif'];
		}
		$total = 0;
		if($n>0){
			$total = $suma/$n;	
		}
		$font = 'bgcolor="#F8402C"';
		if($total>69 and $total <80){
			$font = 'bgcolor="#FFCC00"';
		}
		else if($total>=80 and $total<90){
			$font = 'bgcolor="#EEA562"';	
		}
		else if($total>=90 and $total<100){
			$font = 'bgcolor="#339900"';
		}
		else if($total==100){
			$font = 'bgcolor="#0099CC"';
		}
		if($n > 0){
			$totalMostrar = number_format($total,2)."%";
			$sumaTotalEva = $sumaTotalEva + 1;
			$promTotalEva = $promTotalEva + $total;
			$flag = true;
						
		}
		else{
			$totalMostrar = "";
			$font = 'bgcolor="#FFFFFF"';	
		}
		
		$linea .='<td '.$font.'><b>'.$totalMostrar.'</b></td>'.$calif.'</tr>';
		if($flag){
			echo $linea; 
		}
	}
	
	$promEvas = "";
	$font = 'bgcolor="#FFFFFF"';
	if($sumaTotalEva>0){		
		$promEvas = number_format(($promTotalEva/$sumaTotalEva),2)."%";
		if($promEvas<=69){
			$font = 'bgcolor="#F8402C"';	
		}
		else if($promEvas>69 and $promEvas <80){
			$font = 'bgcolor="#FFCC00"';
		}
		else if($promEvas>=80 and $promEvas<90){
			$font = 'bgcolor="#EEA562"';	
		}
		else if($promEvas>=90 and $promEvas<100){
			$font = 'bgcolor="#339900"';
		}
		else if($promEvas==100){
			$font = 'bgcolor="#0099CC"';
		}
	}
	?>
		<tr><td></td><td></td><td></td><td align="right"><b>TOTAL AVERAGE</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td <?php echo $font; ?> ><b><?php echo $promEvas; ?></b></td></tr>
