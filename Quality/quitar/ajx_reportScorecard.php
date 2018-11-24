<?php
//Funciones para AHT	
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
 
$dbEx = new DBX;
$oFec = new OFECHA;
  function cargaPag($urlToLoad){ //funcion para cargar una pagina
    $pagLoad = '';
    $fp=fopen($urlToLoad,"r") or die("Error al abrir el fichero");
    $pagLoad = fread($fp,30000);
    return $pagLoad;
  }
  
  function n_dias($fecha_desde,$fecha_hasta)
{
	$dias= (strtotime($fecha_desde)-strtotime($fecha_hasta))/86400;
	$dias = abs($dias); $dias = floor($dias);
	return  $dias;
}
function hoursToSecods ($hour) { // $hour must be a string type: "HH:mm:ss"

		$parse = array();
		if (!preg_match ('#^(?<hours>[\d]{2}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#',$hour,$parse)) {
			// Throw error, exception, etc
			throw new RuntimeException ("Hour Format not valid".$hour." ");
		}

		return (int) $parse['hours'] * 3600 + (int) $parse['mins'] * 60 + (int) $parse['secs'];

	} 
  
switch($_POST['Do']){
	case 'filtrosConsolidado':
		$rslt = cargaPag("../mttoReportScorecard/filtrosConsolidado.php");
		$sqlText = "select * from account where id_typeacc=2 and account_status='A' order by name_account";
		$dtCuenta = $dbEx->selSql($sqlText);
		$optC = '<option value="0">[ALL]</option>';
		foreach($dtCuenta as $dtC){
			$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where name_role='SUPERVISOR' and e.user_status=1 and pe.status_plxemp='A'";
		
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = '<option value="0">[ALL]</option>';
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = '<option value="0">[ALL]</option>';
		foreach($dtEmp as $dtE){
			$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
		}
		
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		
		echo $rslt;
	break;

	//Genera consolidado de los datos de AHT, Refused Call, Eficiency, Quality score
	case 'loadConsolidado':
		$filtro = " ";
		$filtroAht =" ";
		$filtroRefused = "";
		$filtroEficiencia = "";
		
		$filtroCS = "";
		$filtroSales = "";
		$filtroNS = "";
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];	
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and e.employee_id=".$_POST['emp'];
		}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			$filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";
		}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			$filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";	
		}
		if(strlen($_POST['fecIni'])>0 and strlen($_POST['fecFin']>0)){
			$fechaIni = $oFec->cvDtoY($_POST['fecIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fecFin']);
			$filtroAht .=" and aht_date between date '".$fechaIni."' and '".$fechaFin."'";	
			$filtroRefused .=" and refused_date between date '".$fechaIni."' and '".$fechaFin."'";
			$filtroEficiencia .=" and efficiency_date between date '".$fechaIni."' and '".$fechaFin."'";
			
			$filtroCS .=" where monitcsemp_date between date '".$fechaIni."' and '".$fechaFin."' ";
			$filtroSales .=" where monitsales_date between date '".$fechaIni."' and '".$fechaFin."' ";
			$filtroNS .=" where monitnsemp_date between date '".$fechaIni."' and '".$fechaFin."' ";

		}
		
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A'".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="tblRepQA" width="70%" align="center" cellpadding="2" cellspacing="2">';
		if($dbEx->numrows>0){
			$rslt .='<tr><td colspan="6" align="right"><form target="_blank" action="mttoReportScorecard/xls_reportScorecard.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" /><input type="hidden" name="filtro" value="'.$filtro.'"><input type="hidden" name="filtroAht" value="'.$filtroAht.'"><input type="hidden" name="filtroRefused" value="'.$filtroRefused.'"><input type="hidden" name="filtroEficiencia" value="'.$filtroEficiencia.'"><input type="hidden" name="filtroCS" value="'.$filtroCS.'"><input type="hidden" name="filtroSales" value="'.$filtroSales.'"><input type="hidden" name="filtroNS" value="'.$filtroNS.'"></td></tr>';
			$rslt .= '<tr class="showItem"><td>Badge</td><td>Agent</td><td>AHT</td><td>Refused calls</td><td>Efficiency</td><td>Quality score</td></tr>';
			foreach($dtEmp as $dtE){
				
				//Obtiene el promedio de tiempo en llamadas
				$sumaTime = '00:00:00';
				$tiempoDecimal = 0;
				$sumaCall = 0;
				$promLlamada = 0;
				$horaPromLlamada = '00:00:00';
				
				//Suma el tiempo total en llamadas

				$sqlText = "select sec_to_time(sum(time_to_sec(aht_totaltime))) as tiempo from aht where employee_id=".$dtE['employee_id']." ".$filtroAht;
				
				$dtTime = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtTime['0']['tiempo']!=NULL){
					$sumaTime = $dtTime['0']['tiempo'];
				}
				$tiempoDecimal = hoursToSecods($sumaTime);
							
				$sqlText = "select sum(aht_totalcalls) as totalcalls from aht where employee_id=".$dtE['employee_id']." ".$filtroAht;
				$sCall = $dbEx->selSql($sqlText);
				if($sCall['0']['totalcalls']!=NULL){
					$sumaCall = $sCall['0']['totalcalls'];	
				}
				if($sumaCall!='' and $sumaCall>0){
					$promLlamada = $tiempoDecimal / $sumaCall;
					$horaPromLlamada = gmdate("H:i:s",$promLlamada);
				}

				
				//Obtiene el promedio de refused call
				
				$sqlText = "select sum(refused_totalrefused) as sumRefused from refused_calls where employee_id=".$dtE['employee_id']." ".$filtroRefused;
				$dtRefused = $dbEx->selSql($sqlText);
				/*
				$sqlText  = "select count(refused_id) as countRefused from refused_calls where employee_id=".$dtE['employee_id']." ".$filtroRefused;
				$dtCountRefused = $dbEx->selSql($sqlText);
				*/
				$sumaRefused = 0;
				$promRefused = 0;
				if($dtRefused['0']['sumRefused']!=NULL){
					$sumaRefused = $dtRefused['0']['sumRefused'];
				}
				if($sumaRefused >0){
					$promRefused = $sumaRefused/($sumaCall + $sumaRefused);
				}
				/*
				if($dtCountRefused['0']['countRefused']!=NULL and $dbEx->numrows>0){
					$countRegistrosRefused = $dtCountRefused['0']['countRefused'];
				}
				
				if($countRegistrosRefused>0 and $sumaRefused>=0){
					$promRefused = $sumaRefused / $countRegistrosRefused;
				}
				*/
				
				//Obtiene el promedio de eficiencia
				$sqlText = "select sum(efficiency_percent) as sumEfficiency from efficiency where employee_id=".$dtE['employee_id']." ".$filtroEficiencia;
				$dtEficiencia = $dbEx->selSql($sqlText);
				
				$sqlText = "select count(efficiency_id) as countEfficiency from efficiency where employee_id=".$dtE['employee_id']." ".$filtroEficiencia;
				$dtCountEficiencia = $dbEx->selSql($sqlText);
				
				$sumaEficiencia = 0;
				$countRegistrosEficiencia = 0;
				$promEficiencia = 0;
				if($dtEficiencia['0']['sumEfficiency']!=NULL){
					$sumaEficiencia = $dtEficiencia['0']['sumEfficiency'];
				}
				if($dtCountEficiencia['0']['countEfficiency']!=NULL and $dbEx->numrows>0){
					$countRegistrosEficiencia = $dtCountEficiencia['0']['countEfficiency'];
				}
				
				if($countRegistrosEficiencia>0 and $sumaEficiencia>=0){
					$promEficiencia = $sumaEficiencia/$countRegistrosEficiencia;
				}
				
				$promEficiencia = $promEficiencia * 100;
				$promRefused = $promRefused * 100;
				
				//Obtiene la puntuacion de calidad para el periodo
				$promEva = 0; 
				$sumaEva = 0;
				$cantidadEva = 0;
				

				$sqlText = "select sum(monitcsemp_qualification) as sumCS from monitoringcs_emp ".$filtroCS." and employee_id=".$dtE['employee_id'];
				$dtSumCS = $dbEx->selSql($sqlText);
				if($dtSumCS['0']['sumCS']!=NULL){
					$sumaEva = $sumaEva + $dtSumCS['0']['sumCS'];
				}
				$sqlText = "select count(id_monitcsemp) as countCS from monitoringcs_emp ".$filtroCS." and employee_id=".$dtE['employee_id'];
				$dtCountCS = $dbEx->selSql($sqlText);
				if($dtCountCS['0']['countCS']!=NULL){
					$cantidadEva = $cantidadEva + $dtCountCS['0']['countCS'];
					
				}
				
				$sqlText = "select sum(monitsales_qualification) as sumSales from monitoringsales_emp ".$filtroSales." and employee_id=".$dtE['employee_id'];
				$dtSumSales = $dbEx->selSql($sqlText);
				if($dtSumSales['0']['sumSales']!=NULL){
					$sumaEva = $sumaEva + $dtSumSales['0']['sumSales'];
					
				}
				
				$sqlText = "select count(id_monitsalesemp) as countSales from monitoringsales_emp ".$filtroSales." and employee_id=".$dtE['employee_id'];
				$dtCountSales = $dbEx->selSql($sqlText);
				if($dtCountSales['0']['countSales']!=NULL or $dtCountSales['0']['countSales']>0){
					$cantidadEva = $cantidadEva + $dtCountSales['0']['countSales'];
					
				}
				
				$sqlText = "select sum(monitnsemp_qualification) as sumNS from monitoringns_emp ".$filtroNS." and employee_id=".$dtE['employee_id'];
				$dtSumNS = $dbEx->selSql($sqlText);
				if($dtSumNS['0']['sumNS']!=NULL){
					$sumaEva = $sumaEva + $dtSumNS['0']['sumNS'];
				}
				
				$sqlText = "select count(id_monitnsemp) as countNS from monitoringns_emp ".$filtroNS." and employee_id=".$dtE['employee_id'];
				$dtCountNS = $dbEx->selSql($sqlText);
				if($dtCountNS['0']['countNS']!=NULL or $dtCountNS['0']['countNS']>0){
					$cantidadEva = $cantidadEva + $dtCountNS['0']['countNS'];
				}
				if($cantidadEva > 0){
					$promEva = $sumaEva/$cantidadEva;
				}
				
				$rslt .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td><td align="center">'.$horaPromLlamada.'</td><td align="center">'.number_format($promRefused,2).'%</td><td align="center">'.number_format($promEficiencia,2).'%</td><td align="center">'.number_format($promEva,2).'%</td></tr>';
				
				
			}//Termina de evaluar por empleado
		}
		else{
			$rslt .='<tr><td colspan="5">No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
		
	break;

}
?>