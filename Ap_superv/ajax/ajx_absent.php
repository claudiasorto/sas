<?php
		//Funciones para Ausentismos	
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
 
  function restaFechas($dFecIni, $dFecFin)
	{
    $dFecIni = str_replace("-","",$dFecIni);
    $dFecIni = str_replace("/","",$dFecIni);
    $dFecFin = str_replace("-","",$dFecFin);
    $dFecFin = str_replace("/","",$dFecFin);

    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);
    ereg( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);

    $date1 = mktime(0,0,0,$aFecIni[2], $aFecIni[1], $aFecIni[3]);
    $date2 = mktime(0,0,0,$aFecFin[2], $aFecFin[1], $aFecFin[3]);

    return round(($date2 - $date1) / (60 * 60 * 24));
}
	function suma_fechas($fecha,$ndias)
	{
		if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
		list($dia,$mes,$año)=split("/", $fecha);
		if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
		list($dia,$mes,$año)=split("-",$fecha);
		$nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
		$nuevafecha=date("d/m/Y",$nueva);
		return ($nuevafecha); 
	}


switch($_POST['Do']){
	case 'newAbsent':
		$time = time()-3600;
		$hora = date("d/m/Y h : i : s A",$time);
		$fecha_actual= date("Y-m-d");
		$sqlText = "select employee_id, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$rslt = '<table width="825" class="tblResult" align="center" bordercolor="#069" align="center" cellpadding="2" cellspacing="0">';
		if($dbEx->numrows>0){
			$rslt .='<tr class="txtForm"><th colspan="5" align="center">REGISTRATION ABSENTEEISM</th></tr>';
			$rslt .='<tr class="txtForm"><th colspan="5" align="center">DATE AND TIME: '.$hora.'</th></tr>';
			$rslt .='<tr align="center"><td width="10%"><b>BADGE</td><td width="40%"><b>AGENT</td><td width="20%"><b> CURRENT STATUS</td><td width="30%"><b>OBSERVATIONS</td><td><b>CHANCE STATUS</td></tr>';
			
			foreach($dtEmp as $dtE){
				$presente='PRESENT'; //Por defecto todos los empleados estan presentes
				$comentario = "";
				
				//Verificamos si el empleado tiene AP's para este dia
				$sqlText = "select ap.id_tpap, tp.name_tpap, startdate_ap, storagedate_ap, typesanction_ap from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap where employee_id=".$dtE['employee_id']." and startdate_ap='".$fecha_actual."' and ((ap.id_tpap in (1,2,5,7)) or (typesanction_ap=3))";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$nombreAp = $dtAp['0']['name_tpap'];
					if($dtAp['0']['typesanction_ap']==3){
						$nombreAp = "SUSPENSION";	
					}
					$comentario .='This employee has a AP of '.$nombreAp.' for this date';
				}
			
				$boton = '<input type="button" title="click to change the state to absent" class="btn" value="Absent" onclick="absent('.$dtE['employee_id'].',1)">'; //Cuando el boton es 1 quiere decir que el empleado esta ausente, 2 cambia a tarde, 0 a presente.
				$sqlText = "select * from absenteeism where employee_id=".$dtE['employee_id']." and absent_date='".$fecha_actual."'";
				$dtA = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					if($dtA['0']['ABSENT_STATUS']=='A'){
						$presente='ABSENT';
						$boton = '<input type="button" title="click to change the state to tardy" class="btn" value="Tardy" onclick="absent('.$dtE['employee_id'].',2)">';
					}
					else if($dtA['0']['ABSENT_STATUS']=='T'){
						$presente = 'TARDY';
						$boton = '<input type="button" title="Click to change the state to present" class="btn" value="Present" onclick="absent('.$dtE['employee_id'].',0)">';
					}
					else if($dtA['0']['ABSENT_STATUS']=='O'){
						$presente ='DAY OFF';
						$boton = '<input type="button" title="The agent has day off" disabled="disabled" value=" N/A " class="btn">';	
					}
					$comentario = $dtA['0']['ABSENT_COMMENT'];
					
				}
				
				$rslt .='<tr class="rowCons"><td>&nbsp;'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>'.$presente.'</td><td><textarea id="txtComment" rows="2" cols="60" disabled="disabled">'.$comentario.'</textarea></td><td align="center">'.$boton.'</td></tr>';
				
			}  
		}
		else{
			$rslt.='<tr><td colspan="4">You don&rsquo;t have employees supervised</td></tr>';
		}
		$rslt .= "</table>";
		echo $rslt;
	break;
	
	case 'newAbsentAllDays':
		$rslt = cargaPag("../mtto/diasRegAbsent.php");
		
		
		//Obtiene los supervisores
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where pe.status_plxemp='A' and user_status=1 and name_role='SUPERVISOR' order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtSup as $dtS){
				$sel = "";
				if($_SESSION['usr_id']==$dtS['employee_id']){
					$sel = "selected";	
				}
				$optSup .='<option value="'.$dtS['employee_id'].'" '.$sel.'>'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
		}
		$rslt = str_replace("<!--fechaActual-->",date("d/m/Y"),$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		echo $rslt;
	break;
	
	case 'loadFormAbsentDay':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$filtro = " where 1 ";
		if($_POST['sup']>0){
			$filtro .=" and id_supervisor=".$_POST['sup'];
		}
		
		if($_SESSION['usr_rol']=='GERENCIA'){
			$filtro .=" and user_status=1 ";
		}
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .= " and user_status=1 ";
		}
		
		else if($_SESSION['usr_rol']=='WORKFORCE'){
			$filtro .=" and user_status=1 and (name_role='AGENTE' or name_role='SUPERVISOR') ";
		}
		else{
			$sqlText = "select pd.id_account, d.name_depart from placexdep pd inner join plazaxemp pe on pd.id_placexdep=pe.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
			$dtCuenta = $dbEx->selSql($sqlText);
			
			if($dtCuenta['0']['name_depart']=='PIPING HOT DEALS'){
				$filtro .= " and id_supervisor=".$_SESSION['usr_id']." and user_status=1  and name_role='AGENTE' ";
			}
			else{
				$filtro .=" and (id_supervisor=".$_SESSION['usr_id']." or pd.id_account=".$dtCuenta['0']['id_account'].") and user_status=1 and name_role='AGENTE'";
			}
		}
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles r on r.id_role = pd.id_role ".$filtro." and pe.status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$rslt = '<table width="825" class="tblResult" align="center" bordercolor="#069" align="center" cellpadding="2" cellspacing="0">';
		if($dbEx->numrows>0){
			$rslt .='<tr align="center"><td width="10%"><b>BADGE</td><td width="40%"><b>AGENT</td><td width="20%"><b> CURRENT STATUS</td><td width="30%"><b>OBSERVATIONS</td><td><b>CHANCE STATUS</td></tr>';
			
			foreach($dtEmp as $dtE){
				$sqlText = "select * from absenteeism where employee_id=".$dtE['employee_id']." and absent_date='".$fecha."'";
				$dtA = $dbEx->selSql($sqlText);
				
				$optEstado = '<select id="lsEstado" class="txtPag" title="Change the state to absent" style="cursor:pointer">';
				$optEstado .= '<option value="P">PRESENT</option>'; //Por defecto todos los empleados estan presentes
				$presente = "PRESENT";
				$comentario = "";
				
				//Verificamos si el empleado tiene AP's para este dia, este tipo de AP dara estado Ausente justificado
				$sqlText = "select ap.id_tpap, tp.name_tpap, startdate_ap, storagedate_ap, typesanction_ap from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap where employee_id=".$dtE['employee_id']." and startdate_ap='".$fecha."' and ap.id_tpap in (1,2,5,7) and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				$sel = "";
				if($dbEx->numrows>0){
					$nombreAp = $dtAp['0']['name_tpap'];
					$sel = "selected";
					$presente = 'JUSTIFIED ABSENCE';
					$comentario .='This employee has a AP of '.$nombreAp.' for this date';
					if($dtA['0']['ABSENT_STATUS']==NULL){
						$sqlText = "insert into absenteeism set employee_id=".$dtE['employee_id'].", absent_date='".$fecha."', absent_status='AJ', absent_comment='".$comentario."'";
						$dbEx->insSql($sqlText);
					}
					
				}
				if($dtA['0']['ABSENT_STATUS']=='AJ'){
					$presente = 'JUSTIFIED ABSENCE';
					$comentario = $dtA['0']['ABSENT_COMMENT'];
					$sel = "selected";
				}
				$optEstado .='<option value="AJ" '.$sel.'>JUSTIFIED ABSENCE</option>';
				
				//Ap Sin justificacion
				$sqlText = "select ap.id_tpap, tp.name_tpap, startdate_ap, storagedate_ap, typesanction_ap from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap where employee_id=".$dtE['employee_id']." and startdate_ap='".$fecha."' and typesanction_ap=3 and approved_status='A'";
				$dtAp = $dbEx->selSql($sqlText);
				$sel = "";
				if($dbEx->numrows>0){
					$nombreAp = "SUSPENSION";	
					$sel = "selected";
					$presente='UNJUSTIFIED ABSENCE';
					$comentario .='This employee has a AP of '.$nombreAp.' for this date';
					if($dtA['0']['ABSENT_STATUS']==NULL){
						$sqlText = "insert into absenteeism set employee_id=".$dtE['employee_id'].", absent_date='".$fecha."', absent_status='AJ', absent_comment='".$comentario."'";
						$dbEx->insSql($sqlText);
					}
				}
				if($dtA['0']['ABSENT_STATUS']=='A'){
					$sel = "selected";
					$presente='UNJUSTIFIED ABSENCE';
					$comentario = $dtA['0']['ABSENT_COMMENT'];
				}
				$optEstado .='<option value="A" '.$sel.'>UNJUSTIFIED ABSENCE</option>';
				
				$sel = "";
				if($dtA['0']['ABSENT_STATUS']=='O'){
					$sel = "selected";
					$presente ='DAY OFF';
				}
				$optEstado .='<option value="O" '.$sel.'>DAY OFF</option>';
				
				$sel = "";
				if($dtA['0']['ABSENT_STATUS']=='T'){
					$sel ="selected";
					$presente = 'TARDY';	
				}
				$optEstado .='<option value="T" '.$sel.'>TARDY</option>';
				$optEstado .='</select>';
				
				$rslt .='<tr class="rowCons"><td>&nbsp;'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>'.$presente.'</td><td><textarea id="txtComment" rows="2" cols="60" disabled="disabled">'.$comentario.'</textarea></td><td align="center"><input type="button" class="btn" value="Change Status" onclick="changeStatus('.$dtE['employee_id'].')"</td></tr>';
			
			/*
				$presente='PRESENT'; //Por defecto todos los empleados estan presentes
				$comentario = "";
				
				//Verificamos si el empleado tiene AP's para este dia
				$sqlText = "select ap.id_tpap, tp.name_tpap, startdate_ap, storagedate_ap, typesanction_ap from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap where employee_id=".$dtE['employee_id']." and startdate_ap='".$fecha."' and ((ap.id_tpap in (1,2,5,7)) or (typesanction_ap=3))";
				$dtAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$nombreAp = $dtAp['0']['name_tpap'];
					if($dtAp['0']['typesanction_ap']==3){
						$nombreAp = "SUSPENSION";	
					}
					$comentario .='This employee has a AP of '.$nombreAp.' for this date';
				}
			
				$boton = '<input type="button" title="click to change the state to absent" class="btn" value="Absent" onclick="absent('.$dtE['employee_id'].',1)">';
				 //Cuando el boton es 1 quiere decir que el empleado esta ausente, 2 cambia a tarde, 0 a presente.
				$sqlText = "select * from absenteeism where employee_id=".$dtE['employee_id']." and absent_date='".$fecha."'";
				$dtA = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					if($dtA['0']['ABSENT_STATUS']=='A'){
						$presente='ABSENT';
						$boton = '<input type="button" title="click to change the state to tardy" class="btn" value="Tardy" onclick="absent('.$dtE['employee_id'].',2)">';
					}
					else if($dtA['0']['ABSENT_STATUS']=='T'){
						$presente = 'TARDY';
						$boton = '<input type="button" title="Click to change the state to present" class="btn" value="Present" onclick="absent('.$dtE['employee_id'].',0)">';
					}
					else if($dtA['0']['ABSENT_STATUS']=='O'){
						$presente ='DAY OFF';
						$boton = '<input type="button" title="The agent has day off" disabled="disabled" value=" N/A " class="btn">';	
					}
					$comentario = $dtA['0']['ABSENT_COMMENT'];
					
				}
				
				$rslt .='<tr class="rowCons"><td>&nbsp;'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>'.$presente.'</td><td><textarea id="txtComment" rows="2" cols="60" disabled="disabled">'.$comentario.'</textarea></td><td align="center">'.$boton.'</td></tr>';
				*/
			}  
		}
		else{
			$rslt.='<tr><td colspan="4">You don&rsquo;t have employees supervised</td></tr>';
		}
		$rslt .= "</table>";
		echo $rslt;
		
		
		
	break;
	
	case 'changeStatus':
		$rslt = cargaPag("../mtto/commentAbsent.php");
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$sqlText = "select * from absenteeism where employee_id=".$_POST['idE']." and absent_date='".$fecha."'";
		$dtAbsent = $dbEx->selSql($sqlText);
		$comentario = "";
		if($dbEx->numrows>0){
			$comentario = $dtAbsent['0']['ABSENT_COMMENT'];
		}
		$optEstado = "";
		$sel  = "";
		if($dtAbsent['0']['ABSENT_STATUS']=='P'){
			$sel = "selected";
		}
		$optEstado .='<option value="P" '.$sel.'>PRESENT</option>';	
		$sel = "";
		if($dtAbsent['0']['ABSENT_STATUS']=='A'){
			$sel = "selected";
		}
		$optEstado .='<option value="A" '.$sel.'>UNJUSTIFIED ABSENCE</option>';
		$sel = "";
		if($dtAbsent['0']['ABSENT_STATUS']=='AJ'){
			$sel = "selected";
		}
		$optEstado .='<option value="AJ" '.$sel.'>JUSTIFIED ABSENCE</option>';
		$sel = "";
		if($dtAbsent['0']['ABSENT_STATUS']=='T'){
			$sel = "selected";	
		}
		$optEstado .='<option value="T" '.$sel.'>TARDY</option>';
		$sel = "";
		if($dtAbsent['0']['ABSENT_STATUS']=='O'){
			$sel = "selected";
		}
		$optEstado .='<option value="O" '.$sel.'>DAY OFF</option>';
		
		$sqlText = "select employee_id, firstname, lastname, username from employees where employee_id=".$_POST['idE'];
		$dtE = $dbEx->selSql($sqlText);
		
		$rslt = str_replace("<!--IdEmp-->",$_POST['idE'],$rslt);
		$rslt = str_replace("<!--optEstado-->",$optEstado,$rslt);
		$rslt = str_replace("<!--comentario-->",$comentario,$rslt);
		$rslt = str_replace("<!--nombre-->",$dtE['0']['firstname']." ".$dtE['0']['lastname'],$rslt);
		$rslt = str_replace("<!--badge-->",$dtE['0']['username'],$rslt);
		echo $rslt;
	break;
	
	case 'absent':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
	 	$rslt = cargaPag("../mtto/commentAbsent.php");
		$sqlText = "select employee_id, firstname, lastname, username from employees where employee_id=".$_POST['idE'];
		$dtE = $dbEx->selSql($sqlText);
		if($_POST['estado']==0){
			$sqlText = "delete from absenteeism where employee_id=".$_POST['idE']." and absent_date='".$fecha."'";
			$dbEx->updSql($sqlText);
			$rslt = 0;
		}
		else{
			$sqlText = "select * from absenteeism where absent_date='".$fecha."' and employee_id=".$_POST['idE'];
			$dtAb = $dbEx->selSql($sqlText);
			$comentario = "";
			if($dbEx->numrows>0){
				$comentario = $dtAb['0']['ABSENT_COMMENT'];	
			}
			
			if($_POST['estado']==1){
				$nuevo_estado='ABSENT';
				$estado_actual='PRESENT';
			}
			else if($_POST['estado']==2){
				$nuevo_estado ='TARDY';
				$estado_actual = 'ABSENT';	
			}
			$rslt = str_replace("<!--fecha-->",$fecha,$rslt);
			$rslt = str_replace("<!--IdEmp-->",$dtE['0']['employee_id'],$rslt);
			$rslt = str_replace("<!--nombre-->",$dtE['0']['firstname']." ".$dtE['0']['lastname'],$rslt);
			$rslt = str_replace("<!--badge-->",$dtE['0']['username'],$rslt);
			$rslt = str_replace("<!--idEstado-->",$_POST['estado'],$rslt);
			$rslt = str_replace("<!--nuevo_estado-->",$nuevo_estado,$rslt);
			$rslt = str_replace("<!--estado_actual-->",$estado_actual,$rslt);
			$rslt = str_replace("<!--comentario-->",$comentario,$rslt);
		}
		echo $rslt;
	break;
	
	case 'saveAbsent':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		
		$sqlText = "select absent_id from absenteeism where employee_id=".$_POST['idE']." and absent_date='".$fecha."'";
		$dtAb = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update absenteeism set absent_status='".$_POST['estado']."', absent_comment='".$_POST['observ']."' where absent_id=".$dtAb['0']['absent_id'];
			$dbEx->updSql($sqlText);	
		}
		else{
			$sqlText = "insert into absenteeism set employee_id=".$_POST['idE'].", absent_date='".$fecha."', absent_status='".$_POST['estado']."', absent_comment='".$_POST['observ']."'";
			$dbEx->insSql($sqlText);
		}
		echo "2";
	break;
	
	case 'repAbsent':
		$rslt = cargaPag("../mtto/formAbsent.php");
		$sqlText = "select employee_id, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if($dbEx->numrows>0){
				$optE .='<option value="0">[ALL]</option>';
			foreach($dtEmp as $dtE){
				$optE .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';
			}
		}
		else{
			$optE .='<option value="-1">You don&rsquo;t have employees supervised</option>';
		}
		$rslt = str_replace("<!--optEmp-->",$optE,$rslt);
		//La accion es 1 para que despliegue los empleados que supervisa
		$rslt = str_replace("<!--optAccion-->","1",$rslt);
		
		echo $rslt;
	break;
	
	case 'loadReportAbsent':
		$filtro = " where status_plxemp='A' ";
		//Compara si selecciono un empleado o sino busca sus empleados
		if($_POST['idEmp']>0){
			$filtro .= ' and e.employee_id='.$_POST['idEmp'];
		}
		else{
			if($_POST['accion']=="2"){
				$filtro .= ' and user_status=1';
			}
			else{
				$filtro .= ' and e.id_supervisor='.$_SESSION['usr_id'].' and user_status=1';
			}
		}
		if((strlen($_POST['fechaIni']))>0){
			if(strlen($_POST['fechaFin'])>0){
			   	$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
			    $fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
				$filtro2 .= " and ab.absent_date between date '".$fec_ini."' and date '".$fec_fin."'";	
			}
		}
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['jefe']>0){
			$filtro .=" and e.id_supervisor=".$_POST['jefe'];
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and ( firstname like '%".$_POST['nombre']."%' or lastname like '%".$_POST['nombre']."%' or (concat(firstname,' ',lastname)) like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .= " and (username like '%".$_POST['badge']."%') ";
		}
		
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" ";
		}
		else if($_SESSION['usr_rol']=='WORKFORCE'){
			$filtro .=" and (name_role = 'AGENTE' or name_role='SUPERVISOR' )";
		}
		
		$filtro1 = "";
		
		//REcorre el arreglo de estados
		$arrayStatus = $_POST['arrayStatus'];
		$status = explode(" ",$arrayStatus);
		$n = count($status);
		
		//Boleano para ver si dentro de los valores de filtrado esta presente
		$present = false;
		for($i = 0; $i<$n; $i++){
			if($i==0){
				$filtro1 .= " and absent_status in ('".$status[$i]."'";
				if($status[$i]=="P"){
					$present = true;
				}
			}
			else{
				$filtro1 .= "'".$status[$i]."'";
				if($status[$i]=="P"){
					$present = true;
				}
			}	
			if($i+1 < $n){
				$filtro1 .=" , ";	
			}
			else{
				$filtro1 .=")";	
			}
		}
		
		$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor, name_account from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join account a on a.id_account=pd.id_account inner join user_roles ur on ur.id_role=pd.id_role ".$filtro." order by firstname ";
		$dtEmp = $dbEx->selSql($sqlText);
		$start = strtotime($fec_ini);
		$end = strtotime($fec_fin);
		$rslt = '<table width="1000" class="tblResult" align="center" bordercolor="#069" align="center" cellpadding="4" cellspacing="2" >';
		if($dbEx->numrows>0){
			$datos .='<tr class="showItem"><td width="5%">BADGE</td><td width="22%">Employee</td><td width="10%">Account</td><td width="22%">Supervisor</td><td width="10%">Date</td><td width="10%" align="center">Status</td><td width="22%" >Observations</td></tr>';
			$n =0;
			for($i = $start; $i <=$end; $i +=86400){
				foreach($dtEmp as $dtE){
					$sqlText = "select absent_id, absent_status, absent_comment from absenteeism where employee_id=".$dtE['employee_id']." and absent_date='".date('Y-m-d',$i)."' ".$filtro1;
					$dtA = $dbEx->selSql($sqlText);
					$coment = "";
					$estado = "";
					if($dbEx->numrows>0){
						if($dtA['0']['absent_status']=='A'){
							$estado = "Unjustified Absence";	
						}
						else if($dtA['0']['absent_status']=='AJ'){
							$estado = "Justified Absence";
						}
						else if($dtA['0']['absent_status']=='T'){
							$estado = "Tardy";	
						}
						else if($dtA['0']['absent_status']=='O'){
							$estado = "Day Off";
						}
						else if($dtA['0']['absent_status']=='P'){
							$estado = "Present";	
						}
						$coment = $dtA['0']['absent_comment'];
					}
					else if($present){
						$estado = "Present";	
						$coment = "";
					}
					if(strlen($estado)>0){
						$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						$nombreSup = "";
						if($dbEx->numrows>0){
								$nombreSup = $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];	
						} 
						
						$n = $n+1;
						$datos .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>'.$dtE['name_account'].'</td><td>'.$nombreSup.'</td><td>'.date('d/m/Y',$i).'</td><td>'.$estado.'</td><td>'.$coment.'</td></tr>';
					}
				}
			}
			$rslt .='<tr><td colspan="5">Matches: '.$n.'</td>';
			$rslt .='<td align="right"><form target="_blank" action="report/xls_rptabsent.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$filtro.'"><input type="hidden" name="filtro1" value="'.$filtro1.'"><input type="hidden" name="start" value="'.$start.'"><input type="hidden" name="end" value="'.$end.'"><input type="hidden" name="estado" value="'.$present.'"></tr>';
			$rslt .= $datos;		
		}
		else{
			$rslt .='<tr><td colspan="5">No matches</td></tr>';
		}
		$rslt .='</table>';
		echo $rslt;
	
	break;
	
	//Muestra los filtros para ver los ausentismos con el rol de GERENTE
	case 'loadRptAbsComplete':
		$rslt = cargaPag("../mtto/formAbsent.php");
		$filtro = "  ";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" and (u.name_role='AGENTE' or u.name_role='SUPERVISOR') ";
		}
		else if($_SESSION['usr_rol']=='WORKFORCE'){
			$filtro .= " and (u.name_role='AGENTE' or u.name_role='SUPERVISOR') ";
		}
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where user_status=1 and pe.status_plxemp='A' ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if($dbEx->numrows>0){
				$optE .='<option value="0">[ALL]</option>';
			foreach($dtEmp as $dtE){
				$optE .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';
			}
		}
		
		$sqlText = "select * from account where id_typeacc=2 order by name_account";
		$dtCuenta = $dbEx->selSql($sqlText);
		$optC = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtCuenta as $dtC){
				$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
			}
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join places p on pd.id_place=p.id_place where pe.status_plxemp='A' and user_status=1 and nivel_place=2 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtSup as $dtS){
				$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
		}
		
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optE,$rslt);
		//La accion es 2 para que despliegue la info de todos los empleados
		$rslt = str_replace("<!--optAccion-->","2",$rslt);
		
		echo $rslt;
	break;
	
	case 'upDayOffAbsent':
		$rslt = cargaPag("../mtto/uploadDayOff.php");
		echo $rslt;
	break;
	
	case 'newAbsentUnrestricted':
		$rslt = cargaPag("../mtto/formAbsentism.php");
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where u.name_role='AGENTE' and pe.status_plxemp='A' and e.user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$optE .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>' ;
			}
		}
		$rslt = str_replace("<!--optEmp-->",$optE,$rslt);
		
		echo $rslt;
	break;
	
	case 'consultAbsent':
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		$sqlText = "select absent_id, absent_status,absent_comment from absenteeism where employee_id=".$_POST['idE']." and absent_date='".$fecha."'";
		$dtA = $dbEx->selSql($sqlText);
		$comentario = "";
		$estado ="";
		$optAbsent = "";
		if($dbEx->numrows>0){
			$sel ="";
			if($dtA['0']['absent_status']=='A'){
				$sel = "selected";	
			}
			$optAbsent .= '<option value="A" '.$sel.'Unjustified Absense</option>';
			$sel = "";
			if($dtA['0']['absent_status']=='AJ'){
				$sel = "selected";
			}
			$optAbsent .='<option value="AJ" '.$sel.'>Justified Absense</option>';
			$sel = "";
			if($dtA['0']['absent_status']=='T'){
				$sel = "selected";	
			}
			$optAbsent .='<option value="T" '.$sel.'>Tardy</option>';
			$sel = "";
			if($dtA['0']['absent_status']=='O'){
				$sel = "selected";
			}
			$optAbsent .='<option value="O" '.$sel.'>Day Off</option>';
			$sel = "";
			if($dtA['0']['absent_status']=='P'){
					$sel = "selected";	
			}
			$optAbsent .='<option value="P" '.$sel.'>Present</option>';
			$comentario = $dtA['0']['absent_comment'];
		}
		else{
			$optAbsent .='<option value="P" selected >Present</option>';	
			$optAbsent .= '<option value="A" '.$sel.'>Unjustified Absense</option>';
			$optAbsent .= '<option value="AJ" '.$sel.'>Justified Absense</option>';
			$optAbsent .='<option value="T" '.$sel.'>Tardy</option>';
			$optAbsent .='<option value="O" '.$sel.'>Day Off</option>';
		}
		$rslt = cargaPag("../mtto/formRegisterAbsent.php");
		$rslt = str_replace("<!--optAbsent-->",$optAbsent,$rslt);
		$rslt = str_replace("<!--comentario-->",$comentario,$rslt);
		$rslt = str_replace("<!--idEmp-->",$_POST['idE'],$rslt);
		$rslt = str_replace("<!--fecha-->",$fecha,$rslt);
		echo $rslt;			
		
	break;
	
	case 'saveUnrestAbsent':
		$sqlText = "select absent_id, absent_status,absent_comment from absenteeism where employee_id=".$_POST['idE']." and absent_date='".$_POST['fecha']."'";
		$dtAb = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$sqlText = "update absenteeism set absent_status='".$_POST['tpAbs']."', absent_comment='".$_POST['comment']."' where absent_id=".$dtAb['0']['absent_id'];	
			$dbEx->updSql($sqlText);
		}
		else{
			$sqlText = "insert into absenteeism set employee_id=".$_POST['idE'].", absent_date='".$_POST['fecha']."', absent_status='".$_POST['tpAbs']."', absent_comment='".$_POST['comment']."'";	
			$dbEx->insSql($sqlText);
		}
		echo "2";
	break;
}
?>
