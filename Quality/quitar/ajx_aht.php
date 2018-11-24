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
  
  function zerofill($entero, $largo){
    	// Limpiamos por si se encontraran errores de tipo en las variables
    	$entero = (int)$entero;
    	$largo = (int)$largo;
     
    	$relleno = '';
  		if (strlen($entero) < $largo){
			$valor = $largo - strlen($entero);
        	$relleno = str_repeat('0', $valor);
    	}
    	return $relleno . $entero;
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
	case 'formUpAHT':
		$rslt = cargaPag("../mttoAHT/formUpAht.php");
		echo $rslt;
	break;
	
	case 'filtrosReportAHT':
		$rslt = cargaPag("../mttoAHT/filtrosReportAHT.php");
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
	
	case 'loadAveragesCall':
		$filtro = " ";
		$filtroCall =" ";
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
			$filtroCall .=" and aht_date between date '".$fechaIni."' and '".$fechaFin."'";	
		}
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A'".$filtro." order by firstname";
		
		$dtEmp = $dbEx->selSql($sqlText);
		$tblResult = '<table class="tblRepQA" width="80%" align="center" cellpadding="2" cellspacing="2">';
		if($dbEx->numrows>0){
			$tblResult .='<tr><td colspan="5" align="center"><b>Times average calls per agent in the period: '.$_POST['fecIni'].'-'.$_POST['fecFin'].'<b></td></tr>';
			$tblResult .='<tr class="showItem"><td width="10%">BADGE</td><td width="45%">AGENT</td><td align="center">TOTAL CALLS</td><td align="center">TOTAL TIME</td><td align="center">AVERAGE TIME</td></tr>';
			foreach($dtEmp as $dtE){
				$sumaTime = '00:00:00';
				$tiempoDecimal = 0;
				$sumaCall = 0;
				$promLlamada = 0;
				$horaPromLlamada = '00:00:00';
				
				//Suma el tiempo total en llamadas

				$sqlText = "select sec_to_time(sum(time_to_sec(aht_totaltime))) as tiempo from aht where employee_id=".$dtE['employee_id']." ".$filtroCall;
				
				$dtTime = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0 and $dtTime['0']['tiempo']!=NULL){
					$sumaTime = $dtTime['0']['tiempo'];
				}
				$tiempoDecimal = hoursToSecods($sumaTime);
				 

				
				//Total de llamadas 			
				$sqlText = "select sum(aht_totalcalls) as totalcalls from aht where employee_id=".$dtE['employee_id']."".$filtroCall;
				$sCall = $dbEx->selSql($sqlText);
				if($sCall['0']['totalcalls']!=NULL){
					$sumaCall = $sCall['0']['totalcalls'];	
				}
				if($sumaCall!='' and $sumaCall>0){
					$promLlamada = $tiempoDecimal / $sumaCall;
					$horaPromLlamada = gmdate("H:i:s",$promLlamada);
				}
			
				$tblResult .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td><td align="center">'.$sumaCall.'</td><td align="center">'.$sumaTime.'</td><td align="center">'.$horaPromLlamada.'</td></tr>'; 
			}
		}
		else{
			$tblResult .='<tr><td colspan="4">No matches</td></tr>';	
		}
		$tblResult .='</table>';
		echo $tblResult;
	break;
}
  
?>
