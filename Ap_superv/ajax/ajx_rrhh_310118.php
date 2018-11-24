<?php
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
	function n_dias($fecha_desde,$fecha_hasta)
	{
		$dias= (strtotime($fecha_desde)-strtotime($fecha_hasta))/86400;
		$dias = abs($dias); $dias = floor($dias);
		return  $dias;
	}
	function actual_date()  
	{  
    	$week_days = array ("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");  
    	$months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");  
    	$year_now = date ("Y");  
    	$month_now = date ("n");  
    	$day_now = date ("j");  
    	$week_day_now = date ("w");  
    	$date = $week_days[$week_day_now] . ", " . $day_now . " de " . $months[$month_now] . " de " . $year_now;   
   	 return $date;    
	} 
	
	function verificarAprobAp($IdAp){
		$result = 0;
		//datos de la AP
		$sqlText = "select * from apxemp where id_apxemp = ".$IdAp;
		$dbEx = new DBX;
		$dtAp = $dbEx->selSql($sqlText);
		//Verificamos si la AP ya ha sido aprobada, sino verificamos
		if($dtAp['0']['APPROVED_STATUS']=='A'){
			$result = 1;	
		}
		else{
		//datos del empleado que tiene la AP
		$sqlText = "select name_role, ur.id_role, name_depart, nivel_place from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join depart_exc d on d.id_depart=pd.id_depart inner join places pl on pl.id_place=pd.id_place where e.employee_id=".$dtAp['0']['EMPLOYEE_ID'];
		$dtEmp = $dbEx->selSql($sqlText);
		
		//datos del creador de la AP
		$sqlText = "select name_role, ur.id_role, name_depart, d.id_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role inner join depart_exc d on pd.id_depart=d.id_depart where e.employee_id=".$dtAp['0']['AUTOR_AP'];
		$dtAutor = $dbEx->selSql($sqlText);
		$departAutor = 0;
		if($dbEx->numrows>0){
			$departAutor = $dtAutor['0']['name_depart'];	
		}

		if($dtAp['0']['ID_TPAP']!=15){
			if($dtEmp['0']['name_role']=='AGENTE' or $dtEmp['0']['name_role']=='SUPERVISOR'){
				if($departAutor!='CHAT' and $dtEmp['0']['name_depart']!='CHAT'){
					if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7){
						//Verifica si la Ap ya esta aprobada xq es tipo con goce ó sin goce ó incapacidad no necesita la aprobacion de gerencia
						if($dtAp['0']['AUTOR_WORK']!=0 and $dtAp['0']['APPROVED_WORK']=='S' and $dtAp['0']['AUTOR_AREA']!=0 and $dtAp['0']['APPROVED_AREA']=='S' and $dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;
						}
					}
					//AP de agente y supervisores verbales y escritas no necesitan aprobacion de Workforce y gerencia
					else if($dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
						if($dtAp['0']['AUTOR_AREA']!=0 and $dtAp['0']['APPROVED_AREA']=='S' and $dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}	
					}
					//Ap de contrato de aviso autorizada por gerencia y HR
					else if($dtAp['0']['ID_TPAP']==16){
						if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S' ){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;
						}
					}
					
					//Resto de las AP necesita autorizacion por todos
					else{
						if($dtAp['0']['AUTOR_WORK']!=0 and $dtAp['0']['APPROVED_WORK']=='S' and $dtAp['0']['AUTOR_AREA']!=0 and $dtAp['0']['APPROVED_AREA']=='S' and $dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}	
					}
				}
				
				//Si el creador fue de CHAT solo se autoriza por HR y Gerencia
				else{
					if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7 or $dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
						if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}
					}
					//Ap de contrato de aviso autorizada por gerencia y HR
					else if($dtAp['0']['ID_TPAP']==16){
						if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S' ){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;
						}
					}
					
					else{
						if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
							$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
							$dbEx->updSql($sqlText);
							$result = 1;	
						}	
					} 
				}
		}//Termina verificacion de agentes y supervisores
			if($dtEmp['0']['id_role']>3 and $dtEmp['0']['id_role']<7){
				if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7 or $dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;
					}
				}
				//Ap de contrato de aviso autorizada por gerencia y HR
				else if($dtAp['0']['ID_TPAP']==16){
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S' ){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;
					}
				}
				
				//aprobacion de HR y gerencia
				else{
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
					}
				}
			}
			//Si el empleado es de HR
			if($dtEmp['0']['id_role']==7){
				if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2 or $dtAp['0']['ID_TPAP']==7 or $dtAp['0']['TYPESANCTION_AP']==1 or $dtAp['0']['TYPESANCTION_AP']==2){
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
					}	
				}
				//Ap de contrato de aviso autorizada por gerencia y HR
				else if($dtAp['0']['ID_TPAP']==16){
					if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S' and $dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S' ){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;
					}
				}
				
				else{
					if($dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
					}	
				}
			}
			if($dtEmp['0']['id_role']==8){
				if($dtAp['0']['AUTOR_GENERALMAN']!=0 and $dtAp['0']['APPROVED_GENERAL']=='S'){
						$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
						$dbEx->updSql($sqlText);
						$result = 1;	
				}		
			}
		}
		//Ap de ingreso solo son verificadas por HR
		else{
			if($dtAp['0']['AUTOR_HR']!=0 and $dtAp['0']['APPROVED_HR']=='S'){
				$sqlText = "update apxemp set approved_status='A' where id_apxemp=".$IdAp;
				$dbEx->updSql($sqlText);
				$result = 1;	
			}	
		}
		}
		return $result;
	}

switch($_POST['Do']){
	
	case 'newAp': //Nueva acción de personal
	  	$rslt = cargaPag("../mtto/mt_newap.php");
		$filtro = '';
		if($_SESSION['usr_rol']!='RECURSOS HUMANOS' and $_SESSION['usr_rol']!='GERENCIA'){
			$filtro = " where id_tpap = 1 or id_tpap=2 or id_tpap=3 or id_tpap=5 or id_tpap=6 or id_tpap=7 or id_tpap=9 or id_tpap=13 or id_tpap=16"; 
			}
		else if($_SESSION['usr_rol']=='GERENCIA'){
			$filtro = " where id_tpap = 1 or id_tpap=2 or id_tpap=3 or id_tpap=5 or id_tpap=6 or id_tpap=7 or id_tpap=8 or id_tpap=9 or id_tpap=12 or id_tpap=13 or id_tpap=14 or id_tpap=16"; 
		}
		else{
			$filtro = " where id_tpap!=15 ";
		}
	  	$sqlText = "select * from type_ap".$filtro;
      	$tp = $dbEx->selSql($sqlText);
		$optTp = "<option value=0>Seleccione una acción de personal</option>";
	  	foreach($tp as $tp){
			  $optTp .= '<option value="'.$tp['ID_TPAP'].'">'.$tp['NAME_TPAP'].'</option>';
		  }
	  	
	  	$rslt = str_replace("<!--optTap-->",$optTp,$rslt);
	  	echo $rslt;
		
	break;
	
	case 'emp'://Función para desplegar los empleados segun su estatus, su supervisor y su rol
		$rslt = '';
		$filtro = '';
		$sqlText = "select name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
		$id_pl = $dbEx->selSql($sqlText);
		if($_SESSION['usr_rol']=='SUPERVISOR'){
			if($id_pl['0']['name_place']=='QUALITY SUPERVISOR'){
				$filtro .= " and pd.id_role=2 and pd.id_account!=3 ";
				}
			else{
				$filtro .= " and e.id_supervisor=".$_SESSION['usr_id'];
			}
		}
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" ";
		}
		
		
		else if(($_SESSION['usr_rol']!='RECURSOS HUMANOS') and ($_SESSION['usr_rol']!='SUPERVISOR') and ($_SESSION['usr_rol']!='AGENTE')){
			//Traficc solo podra poner AP a agentes
			if($id_pl['0']['name_place']=='WORKFORCE ANALYST'){
				$filtro .= " and id_role=2 ";	
			}
			else{
				$filtro .= " and id_role<".$_SESSION['usr_idrol']." or e.id_supervisor=".$_SESSION['usr_id'];
			}
		}
		else if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$filtro .= " and e.employee_id!=".$_SESSION['usr_sup'];	
		}
		else if($id_pl['0']['name_place']=='QUALITY AGENT' or $id_pl['0']['name_place']=='CHAT QUALITY AGENT'){
			$filtro .= " and pd.id_role=2";
		}
		//Si el empleado no es de ninguno de los roles anteriores se le niegan los empleados
		else{
			$filtro .= " and 1=2";;
		}
	
		if($_POST['idAp']==3){ //si la ap es traslados mostramos todos los empleados activos y a los cuales no supervisa
			if($_SESSION['usr_rol']=='SUPERVISOR'){
				$filtro = " and id_supervisor!= ".$_SESSION['usr_id']." and id_role<".$_SESSION['usr_idrol'];
			}
			$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where user_status=1 and status_plxemp='A' and e.employee_id!=".$_SESSION['usr_id']."".$filtro." order by firstname";
		}
		
		else if($_POST['idAp']==4){ //Si la ap es reingreso mostramos los empleados con status=0
			$sqlText = "select employee_id, firstname, lastname from employees where user_status=0 order by firstname";
		}
		else{
			$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where user_status=1 and status_plxemp='A' and e.employee_id!=".$_SESSION['usr_id']." ".$filtro." order by firstname";
		}
		
	  	$dtAg = $dbEx->selSql($sqlText);
		$optAg .= '<select id="lsemp" class="txtPag">';
	  	if($dbEx->numrows>0){
			$optAg .= "<option value=0>Seleccione un agente</option>";
	  		foreach($dtAg as $dtA){
				$optAg .= '<option value="'.$dtA['employee_id'].'">'.$dtA['firstname']." ".$dtA['lastname'].'</option>'; 
				}
	  	}
	  	else {$optAg .= "<option value=-1>NO HAY EMPLEADOS PARA LA SELECCION</option>";}
		$optAg .= "</select>";
		$rslt = $optAg;
		echo $rslt;
			
	break;
	
	case 'empxdep': // Muestra los empleados de un determinado departamento (usado para filtros de reportes)
		$filtro = "";
		$filtro .= " and e.id_supervisor=".$_SESSION['usr_id'];
		$filtro .= " and pd.id_role< ".$_SESSION['usr_idrol'];
		
		
		if($_POST['idD']==0){
			$sqlText = "select distinct(e.employee_id), username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where status_plxemp='A' and e.user_status=1 ".$filtro." order by firstname";	
		}
		else{
		$sqlText = "select distinct(e.employee_id), username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where pd.id_depart=".$_POST['idD']." and status_plxemp='A' and user_status=1 ".$filtro." order by firstname";
		}
		$dtE = $dbEx->selSql($sqlText);
		$optE = '<select id="lsAg" class="txtPag" ><option value="0">[TODOS]</option>';
		foreach($dtE as $dtE){
				$optE .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']."&nbsp;".$dtE['lastname']."</option>";
		}
		$optE .='</select>';
		echo $optE;
	break;
	
	case 'frmap': //Formulario de nueva ap según selección  
		if($_POST['ap']==1 or $_POST['ap']==2){
				$rslt = cargaPag("../mtto/ap_permiso.php");
		}
		if($_POST['ap']==3){
				$rslt = cargaPag("../mtto/ap_traslados.php");
		}
		if($_POST['ap']==4){
				$rslt = cargaPag("../mtto/ap_reingresos.php");
		}
		if($_POST['ap']==5){
				$rslt = cargaPag("../mtto/ap_vacaciones.php");
		}
		if($_POST['ap']==6){
				$rslt = cargaPag("../mtto/ap_disciplinarias.php");	
		}
		if($_POST['ap']==7){
				$rslt = cargaPag("../mtto/ap_incapacidades.php");	
		}
		if($_POST['ap']==8){
				$rslt = cargaPag("../mtto/ap_puesto.php");	
		}
		if($_POST['ap']>=9 and $_POST['ap']<=14){
				$rslt = cargaPag("../mtto/ap_retiros.php");
		}
		if($_POST['ap']==16){
			$rslt = cargaPag("../mtto/ap_contratoAviso.php");
		}
		
		
		$sqlText = "select e.EMPLOYEE_ID, ID_SUPERVISOR, USERNAME, FIRSTNAME, LASTNAME, dui,date_format(date_admis,'%d/%m/%Y') as d, pe.id_plxemp as idplxemp, d.ID_DEPART, NAME_DEPART, cc.ID_ACCOUNT, NAME_ACCOUNT, pc.ID_PLACE, NAME_PLACE, pd.id_placexdep from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep inner join depart_exc d on pd.ID_DEPART = d.ID_DEPART inner join places pc on pc.id_place = pd.id_place inner join account cc on pd.id_account=cc.id_account where e.EMPLOYEE_ID=".$_POST['emp']." and pe.status_plxemp = 'A'";
		$dtE = $dbEx -> selSql($sqlText);
		$rslt = str_replace("<!--idemp-->", $dtE['0']['EMPLOYEE_ID'], $rslt);
		$rslt = str_replace("<!--username-->", $dtE['0']['USERNAME'], $rslt);
		$rslt = str_replace("<!--nombre-->", $dtE['0']['FIRSTNAME'], $rslt);
		$rslt = str_replace("<!--apellido-->", $dtE['0']['LASTNAME'], $rslt);
		$rslt = str_replace("<!--dui-->",$dtE['0']['dui'],$rslt);
		$rslt = str_replace("<!--ingreso-->",$dtE['0']['d'], $rslt);
		$rslt = str_replace("<!--id_depto-->", $dtE['0']['ID_DEPART'], $rslt);
		$rslt = str_replace("<!--depto-->", $dtE['0']['NAME_DEPART'],$rslt);
		$rslt = str_replace("<!--idcuenta-->",$dtE['0']['ID_ACCOUNT'],$rslt);
		$rslt = str_replace("<!--cuenta-->",$dtE['0']['NAME_ACCOUNT'],$rslt);
		$rslt = str_replace("<!--idplaza-->",$dtE['0']['ID_PLACE'],$rslt);
		$rslt = str_replace("<!--plaza-->", $dtE['0']['NAME_PLACE'],$rslt);
		$rslt = str_replace("<!--idplxemp-->",$dtE['0']['idplxemp'],$rslt);
		$rslt = str_replace("<!--fecActualLetras-->",actual_date(),$rslt);
					
		$optHoras = "";
		$i=0;
		while($i<=36){
			$optHoras .= '<option value="'.$i.'">'.$i.'</option>'; 
			$i=$i+0.50;
		}
		$rslt = str_replace("<!--optHoras-->",$optHoras,$rslt);
		
		$fechaactual = date("d/m/Y");
		$rslt = str_replace("<!--fecha-->", $fechaactual, $rslt);
		$optSuperv = '<select id="lsSuperv" class="txtPag"><option value='.$_SESSION['usr_id'].'>'.$_SESSION['usr_nombre']."&nbsp;".$_SESSION['usr_apellido'].'</option></select>';
		$rslt = str_replace("<!--optSuperv-->",$optSuperv,$rslt);
		
		//Funciones para traslados si el usuario no es de RRHH
		if($_SESSION['usr_rol']!='RECURSOS HUMANOS'){
			//Muestra las cuentas del supervisor
				
			$sqlText = "select ac.name_account, ac.id_account from account ac where ac.id_account in (select c.id_account from account c inner join placexdep pd on c.id_account = pd.id_account inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id where e.id_supervisor=".$_SESSION['usr_id']." or e.employee_id=".$_SESSION['usr_id'].")";
			
			$dtC = $dbEx->selSql($sqlText);
			$lscuenta = '<select id="lsCuenta" class="txtPag" onChange="getDepartTras(this.value)">';
			foreach($dtC as $dtCu){
				$lscuenta .= '<option value="'.$dtCu['id_account'].'">'.$dtCu['name_account'].'</option>';
			}
			$lscuenta .='</select>';
			$rslt = str_replace("<!--optcuenta-->",$lscuenta,$rslt);
			//Lista Departamentos de la cuenta

			$sqlText = "select distinct (d.id_depart) as idD, name_depart from depart_exc d inner join placexdep pd on pd.id_depart=d.id_depart inner join places pl on pl.id_place=pd.id_place where pd.id_account=".$dtC['0']['id_account'];
			
			$dtD = $dbEx->selSql($sqlText);
			$optD = '<select id="lsDpto" class="txtPag" onchange="getPosc2(this.value,'.$dtC['0']['id_account'].')">';
			$optD .= '<option value="0">Seleccione un departamento</option>';
			foreach($dtD as $dtD){
				$optD .= '<option value="'.$dtD['idD'].'">'.$dtD['name_depart'].'</option>';
			}
			$optD .='</select>';
			$rslt = str_replace("<!--optDepto-->",$optD,$rslt);
		}
		//Funciones para traslado si el usuario es de Recursos humanos
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$sqlText = "select * from account order by NAME_ACCOUNT";
			$dtC = $dbEx->selSql($sqlText);
			$optC = '<select id="lsCuenta" class="txtPag" onChange="getDepart(this.value)">';
			$optC .='<option value="0">Seleccione una cuenta</option>';
			foreach($dtC as $dtCu){
				$optC .='<option value="'.$dtCu['ID_ACCOUNT'].'">'.$dtCu['NAME_ACCOUNT'].'</option>';
			}	
			$optC .='</select>';
			$rslt = str_replace("<!--optcuenta-->",$optC,$rslt);
			$rslt = str_replace("<!--optDepto-->",'<select id="lsDpto" class="txtPag" disabled="disabled"><option value="0">Seleccione un departamento</option></select>',$rslt);
		}
		
		//selecciona las plazas del departamento del empleado
		$sqlText = "select * from places pl inner join placexdep pd on pd.id_place=pl.id_place where pd.id_depart=".$dtE['0']['ID_DEPART']." order by NAME_PLACE"; 
		$dtP = $dbEx->selSql($sqlText);
		$optPl = '<option value="0">Seleccione una plaza</option>';
		foreach($dtP as $dtP){
			$optPl .= '<option value="'.$dtP['ID_PLACE'].'"'.$_sel.'">'.$dtP['NAME_PLACE'].'</option>';
			}
		$rslt = str_replace("<!--optPlaza-->",$optPl,$rslt);
		

		//Muestra los tipos de sanción disciplinaria del empleado
		$sqlText = "select * from type_disciplinary";
		$dtFalta = $dbEx->selSql($sqlText);
		$optF = "";
		foreach($dtFalta as $dtF){
			$optF .= '<option value="'.$dtF['ID_TPDISCIPLINARY'].'">'.$dtF['NAME_TPDISCIPLINARY'].'</option>';
		}
		$rslt = str_replace("<!--tp_falta-->",$optF,$rslt);

		$sqlText = "select date_admis from employees where employee_id=".$_POST['emp'];
		$fec_admis = $dbEx->selSql($sqlText);

		//Lista los tipos de centros de salud
		$sqlText = "select * from centercare";
		$dtC = $dbEx->selSql($sqlText);
		$optC = '<option value="0">Seleccione un centro de salud</option>';
		foreach($dtC as $dtC){
			$optC .= '<option value="'.$dtC['ID_CENTER'].'">'.$dtC['NAME_CENTER'].'</option>';
		}			
		$rslt = str_replace("<!--optCenter-->",$optC,$rslt);
		
		//Datos para ap de nuevo puesto
		$sqlText = "select * from account order by name_account";
		$dtAccount = $dbEx->selSql($sqlText);
		$optCuentaPuesto = '<select id="lsCuenta" class="txtPag" onChange="getDepart(this.value)">';
		foreach($dtAccount as $dtAc){
			$sel = "";
			if($dtAc['ID_ACCOUNT']==$dtE['0']['ID_ACCOUNT']){ $sel = "selected";}
			$optCuentaPuesto .='<option value="'.$dtAc['ID_ACCOUNT'].'" '.$sel.'>'.$dtAc['NAME_ACCOUNT'].'</option>';
		}
		$optCuentaPuesto .='</select>';
		
		$sqlText = "select distinct(d.id_depart) as id_depart, name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account c on c.id_account=pd.id_account where c.id_account=".$dtE['0']['ID_ACCOUNT']." order by name_depart ";
		$dtDepart = $dbEx->selSql($sqlText);
		$optDepartPuesto = '<select id="lsDpto" class="txtPag" onchange="getPosc(this.value,'.$dtE['0']['ID_ACCOUNT'].')">';
		foreach($dtDepart as $dtDep){
			$sel = "";
			if($dtDep['id_depart']==$dtE['0']['ID_DEPART']){
				$sel = "selected";	
			}
			$optDepartPuesto .='<option value="'.$dtDep['id_depart'].'" '.$sel.'>'.$dtDep['name_depart'].'</option>';
		}
		$optDepartPuesto .='</select>';
		
		$sqlText = "select distinct(id_placexdep) as id_place, name_place from places p inner join placexdep pd on p.id_place=pd.id_place inner join depart_exc d on d.id_depart=pd.id_depart inner join account c on c.id_account = pd.id_account where c.id_account=".$dtE['0']['ID_ACCOUNT']." and d.id_depart=".$dtE['0']['ID_DEPART'];
		$dtPosicion = $dbEx->selSql($sqlText);
		$optPosicionPuesto = "";
		foreach($dtPosicion as $dtPos){
			$sel = "";
			if($dtPos['id_place']==$dtE['0']['id_placexdep']){
				$sel = "selected";	
			}
			$optPosicionPuesto .='<option value="'.$dtPos['id_place'].'" '.$sel.'>'.$dtPos['name_place'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place where p.nivel_place=2 and pe.status_plxemp='A' and user_status=1";
		$dtSupervisor = $dbEx->selSql($sqlText);
		$optSupPuesto = '<select id="lsSuperv" class="txtPag">';
		foreach($dtSupervisor as $dtSup){
			$sel = "";
			if($dtE['0']['ID_SUPERVISOR']==$dtSup['employee_id']){
				$sel = "selected";	
			}
			$optSupPuesto .='<option value="'.$dtSup['employee_id'].'" '.$sel.'>'.$dtSup['firstname'].' '.$dtSup['lastname'].'</option>';
		}
		$optSupPuesto .='</select>';
		
		
		$rslt = str_replace("<!--optcuentaPuesto-->", $optCuentaPuesto,$rslt);
		$rslt = str_replace("<!--optDeptoPuesto-->",$optDepartPuesto,$rslt);
		$rslt = str_replace("<!--optPosicionPuesto-->",$optPosicionPuesto,$rslt);
		$rslt = str_replace("<!--optSupervisorPuesto-->",$optSupPuesto,$rslt);
				
		//Muestra los datos de la ap
		$sqlText2 = "select * from type_ap where ID_TPAP=".$_POST['ap'];
		$dtAp = $dbEx->selSql($sqlText2);
		$rslt = str_replace("<!--idap-->", $dtAp['0']['ID_TPAP'], $rslt);
		$rslt = str_replace("<!--nombreap-->", $dtAp['0']['NAME_TPAP'], $rslt);
		$rslt = str_replace("<!--descap-->", $dtAp['0']['DESC_TPAP'], $rslt);

		//Consulta si el empleado posee ap's
		$aps = 0;
		$sqlText = "select * from apxemp ae inner join type_ap t on ae.ID_TPAP=t.ID_TPAP where ae.employee_id=".$_POST['emp']." order by t.ID_TPAP";
		$dtA = $dbEx->selSql($sqlText);
		$aps = $dbEx->numrows;
		if($dbEx->numrows==0){
				$tblA = "El empleado no posee registro de Acciones de personal";
			}
		else{
				$tblA = 'El empleado posee acciones de personal, haga <a href="#" onclick="consultap('.$_POST['emp'].')">click aquí</a> para verlas';
				$tblA .='<div id="cons_ap"></div>';
			}
		$rslt = str_replace("<!--dataAp-->", $tblA, $rslt);
		echo $rslt;

	break;
	
	case 'getDescFalta'://Muestra la descripcion de un tipo de sancion de personal
		$sqlText = "select desc_tpdisciplinary from type_disciplinary where id_tpdisciplinary=".$_POST['idF'];
		$dtF = $dbEx->selSql($sqlText);
		$rslt = "<u><i>".$dtF['0']['desc_tpdisciplinary']."</i></u>";
		echo $rslt;
		
		break;
		
	case 'getTpDisciplinaria':
		$sqlText = "select date_admis from employees where employee_id=".$_POST['idE'];
		$fec_admis = $dbEx->selSql($sqlText);
		//Cambia el status de las ap a Inactivas cada 6 meses
		$dias = restaFechas($fec_admis['0']['date_admis'],$fechaactual)+1;
		$meses = $dias / 182; //calcula la cantidad de dias posterior al cumplimiento de otros 6 meses.
		if($meses>0){
			$meses = $meses * 6;
			$nueva_fecha = date($fec_admis['0']['date_admis'],strtotime("+ ".$meses." months"));
			$sqlText = "update apxemp set status_ap='I' where employee_id=".$_POST['emp']." and id_tpap=6 and storagedate_ap between '".$fec_admis['0']['date_admis']."' and ".$nueva_fecha."'";
		}
		
		$tp1='<input type="radio" id="optDisc" name="optDisc" onclick="suspend(1)"/>&nbsp;Verbal&nbsp;&nbsp;&nbsp;';
		$tp2='<input type="radio" id="optDisc" name="optDisc" onclick="suspend(2)"/>&nbsp; Escrita&nbsp;&nbsp;&nbsp;';
		$tp3='<input type="radio" id="optDisc" name="optDisc" onclick="suspend(3)"/>&nbsp;Suspensi&oacute;n';/*
		$sqlText = "select max(typesanction_ap) as ts from apxemp where employee_id=".$_POST['idE']." and status_ap='A' and id_tpdisciplinary = ".$_POST['idF'];
	
		$dtS = $dbEx->selSql($sqlText);
		if($dtS['0']['ts']!=0){
			if($dtS['0']['ts']==1){$tp_disc = $tp2.$tp3;} //Muestra opciones W y S
			if($dtS['0']['ts']==2){$tp_disc = $tp3;} //Muestra opcion S
			if($dtS['0']['ts']==3){$tp_disc = 'EL EMPLEADO YA CUMPLIO SU MAXIMO NUMERO DE ACCIONES DISCIPLINARIAS POSIBLES DURANTE 6 MESES, PONGASE EN CONTACTO CON RECURSOS HUMANOS';} //No muestra opciones
		}
		else{
			$tp_disc = $tp1.$tp2.$tp3; //muestra todas las opciones
			}
			*/
		$tp_disc = "";
		$sqlText = "select date_format(storagedate_ap,'%d/%m/%Y') as f1, comment_ap, typesanction_ap from apxemp  where employee_id=".$_POST['idE']." and status_ap='A' and id_tpdisciplinary=".$_POST['idF']." and approved_status='A'";
		$dtAp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$tp_disc .='<tr><td colspan="4"><i>Disciplinarias aplicadas por este mismo motivo: <i></td><br></tr>';
			foreach($dtAp as $dtA){
				if($dtA['typesanction_ap']==1){
					$tipo = "Verbal";	
				}
				else if($dtA['typesanction_ap']==2){
					$tipo = "Escrita";
				}
				else if($dtA['typesanction_ap']==3){
					$tipo = "Suspension";
				}
				$tp_disc .='<tr><td class="txtPag">* '.$dtA['f1'].'</td><td>&nbsp;'.$tipo.'&nbsp;</td><td colspan="2">&nbsp;'.$dtA['comment_ap'].'&nbsp;</td><br></tr>';	
			}
				
		}
		echo $tp_disc;
		
	break;
	
	case 'getDepartTras': //Muestra los departamentos segun cuenta para traslados
			$sqlText = "select distinct (d.id_depart) as idD, name_depart from depart_exc d inner join placexdep pd on pd.id_depart=d.id_depart inner join places pl on pl.id_place=pd.id_place where pl.nivel_place=1 and pd.id_account=".$_POST['idC'];
			
			$dtD = $dbEx->selSql($sqlText);
			$optD = '<select id="lsDpto" class="txtPag" onchange="getPosc2(this.value,'.$_POST['idC'].')">';
			$optD .= '<option value="0">Seleccione un departamento</option>';
			foreach($dtD as $dtD){
				$optD .= '<option value="'.$dtD['idD'].'">'.$dtD['name_depart'].'</option>';
			}
			$optD .='</select>';
			$rslt = $optD;
			echo $rslt;
	break;
	
	case 'getDepart': //Obtiene los departamentos segun Cuenta
		$sqlText = 'select distinct(pd.id_depart), name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account ac on pd.id_account=ac.id_account where ac.id_account='.$_POST['idC']." order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		$optD = '<select id="lsDpto" onChange="getPosc(this.value,'.$_POST['idC'].')" class="txtPag">';
		if($dbEx->numrows>0){
			$optD .='<option value="0">Seleccione un departamento</option>';
			foreach($dtD as $dtD){
					$optD .='<option value="'.$dtD['id_depart'].'">'.$dtD['name_depart'].'</option>';
			}
		}
		else{
			$optD .='La cuenta no posee departamentos';
			}
		$optD .='</select>';
		echo $optD;
	break;
	
	case 'getPosc': //Obtiene las plazas de los departamentos
		$sqlText = "select distinct(pd.id_placexdep), name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place where pd.id_depart=".$_POST['idD']." and pd.id_account=".$_POST['idC']." order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP = '<select id="lsPosc" onChange="getSuperv(this.value)" class="txtPag">';
		if($dbEx->numrows>0){
			$optP .= '<option value="0">Seleccione un Posici&oacute;n</option>';
			foreach($dtP as $dtP){
				$optP .='<option value="'.$dtP['id_placexdep'].'">'.$dtP['name_place'].'</option>';	
			}
		}
		else{
			$optP .='El departamento no posee posiciones';	
		}
		$optP .='</select>';
		echo $optP;
	
	break;
	
	case 'getPosc2': //Obtiene las plazas de los departamentos si el usuario no es de recursos humanos
		$sqlText = "select distinct(pd.id_placexdep), name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place where pd.id_depart=".$_POST['idD']." and pd.id_account=".$_POST['idC']." and nivel_place=1";
		$dtP = $dbEx->selSql($sqlText);
		$optP = '<select id="lsPosc" class="txtPag">';
		if($dbEx->numrows>0){
			$optP .= '<option value="0">Seleccione un Posici&oacute;n</option>';
			foreach($dtP as $dtP){
				$optP .='<option value="'.$dtP['id_placexdep'].'">'.$dtP['name_place'].'</option>';	
			}
		}
		else{
			$optP .='El departamento no posee posiciones';	
		}
		$optP .='</select>';
		echo $optP;
	
	break;
	
	case 'getSuperv':
		$sqlText = "select id_account, id_depart, id_place, id_role from placexdep where id_placexdep=".$_POST['idP'];
		$dtC = $dbEx->selSql($sqlText);
		$sqlText = "select id_role, nivel_place from placexdep pd inner join places pl on pd.id_place=pl.id_place where id_placexdep=".$_POST['idP'];
		$dtS = $dbEx->selSql($sqlText);
		if($dtS['0']['id_role']>2){
			if($dtS['0']['nivel_place']==1){
				$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where id_account=".$dtC['0']['id_account']."  and id_depart=".$dtC['0']['id_depart']." and pd.id_role=".$dtC['0']['id_role']." and pl.nivel_place=2 and user_status=1 order by firstname";
				}
			else if($dtS['0']['nivel_place']==2){
				$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where pd.id_role>=".$dtC['0']['id_role']." and pl.nivel_place=2 and user_status=1 order by firstname";
			}
			
			$optSup = '<select id="lsSuperv" class="txtPag"><option value="0">Seleccione un supervisor</option>';
		}
		else if($dtS['0']['id_role']<=2 and $_SESSION['usr_idrol']>=3){
			if($dtS['0']['nivel_place']==1 and $dtC['0']['id_account']==3){
				$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where id_role>3 and nivel_place=2 and user_status=1 order by firstname";
				$optSup = '<select id="lsSuperv" class="txtPag"><option value="-1">Seleccione un supervisor</option>';	
				}
			else{
				$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep where pd.id_role=3 and pe.status_plxemp='A' and pd.id_account=".$dtC['0']['id_account']." and user_status=1 order by firstname";
				$optSup = '<select id="lsSuperv" class="txtPag"><option value="-1">Seleccione un supervisor</option>';
				}	
		} 
		else{
			$sqlText = "select e.employee_id, firstname, lastname from employees e where employee_id=".$_SESSION['usr_id'];
			$optSup = '<select id="lsSuperv" class="txtPag" disabled="disabled">';
		}
			$dtSup = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtSup as $dtSup){
					$optSup .= '<option value="'.$dtSup['employee_id'].'">'.$dtSup['firstname']."&nbsp;".$dtSup['lastname'];
				}
			}
			$optSup .= '</select>';
			$rslt = $optSup;
		echo $rslt;
	break;
	
	case 'getDepart2': //Obtiene los departamentos segun Cuenta para reportes
		$filtro = ' where pd.id_role<'.$_SESSION['usr_idrol'];
		if($_SESSION['usr_rol']!='RECURSOS HUMANOS'){
			$filtro .= ' and pd.id_role!=1 ';	
		}
		$jointable = '';
		if($_POST['idC']>0){
			$filtro .= ' and ac.id_account='.$_POST['idC'];	
		}
		//si el usuario es supervisor de un departamento diferente a Quality mostrar solo sus dptos.
		$sqlText = "select name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
		$id_pl = $dbEx->selSql($sqlText);
		if($_SESSION['usr_rol']=='SUPERVISOR' and $id_pl['0']['name_place']!='QUALITY SUPERVISOR'){
				$jointable .= ' inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id ';
				$filtro .=" and pe.status_plxemp='A' and (e.id_supervisor=".$_SESSION['usr_id']." or e.employee_id=".$_SESSION['usr_id']." )";
		}
		
		$sqlText = 'select distinct(pd.id_depart), name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account ac on pd.id_account=ac.id_account '.$jointable.' '.$filtro.' order by name_depart';
		$dtD = $dbEx->selSql($sqlText);
		$optD = '<select id="lsDpto" onChange="getEmp2(this.value,'.$_POST['idC'].')" class="txtPag">';
		if($dbEx->numrows>0){
			$optD .='<option value="0">[TODOS]</option>';
			foreach($dtD as $dtD){
					$optD .='<option value="'.$dtD['id_depart'].'">'.$dtD['name_depart'].'</option>';
			}
		}
		else{
			$optD .='La cuenta no posee departamentos';
			}
		$optD .='</select>';
		echo $optD;
	break;
	
	case 'getEmp2':
		$sqlText = 'select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where pd.id_depart='.$_POST['idD'].' and pd.id_account='.$_POST['idC']." and pe.status_plxemp='A' order by firstname";
		$dtE = $dbEx->selSql($sqlText);
		$optE = '<select id="lsAg" class="txtPag"><option value="0">[TODOS]</option>';
		foreach($dtE as $dtE){
			$optE .= '<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']."&nbsp;".$dtE['lastname']."</option>";
		}
		$optE .= '</select>';
		echo $optE;
	break;
	
	case 'getUltimasSanciones': //Muestra las ultimas faltas disciplinarias cometidas en 6 meses del tipo de sancion seleccionada
		$fechaActual = date("Y-m-d");
		
		$fechaAnterior = date('Y-m-d',strtotime('-6 month'));
		
		$sqlText = "select name_tpdisciplinary from type_disciplinary where id_tpdisciplinary=".$_POST['idTpSanc'];
		$nombreSancion = $dbEx->selSql($sqlText);
	
		$sqlText = "select date_format(startdate_ap, '%d/%m/%Y') as f1, typesanction_ap, comment_ap from apxemp where employee_id=".$_POST['idE']." and id_tpdisciplinary=".$_POST['idTpSanc']." and storagedate_ap between date '".$fechaAnterior."' and '".$fechaActual."' and id_tpap=6 order by storagedate_ap";
		$dtAp = $dbEx->selSql($sqlText);
		$rslt = '<table cellpadding="2" cellspacing="0" width="600" border="1" bordercolor="#003366" align="center" bgcolor="#FFFFFF" style="border-bottom-style:solid;">';
		if($dbEx->numrows>0){
			
			$rslt .='<tr class="showItem"><td width="15%">Fecha</td><td width="15%">Tipo de Sanci&oacute;n</td><td width="60%">Comentarios</td></tr>';
			foreach($dtAp as $dtA){
				if($dtA['typesanction_ap'] == 1){ $tpSancion = "Verbal";}
				if($dtA['typesanction_ap'] == 2){ $tpSancion = "Escrita";}
				if($dtA['typesanction_ap'] == 3){ $tpSancion = "Suspensi&oacute;n";}
				$rslt .='<tr class="txtPag"><td>'.$dtA['f1'].'</td><td>'.$tpSancion.'</td><td>'.$dtA['comment_ap'].'</td></tr>';
			}
		}
		else{
			$rslt .='<tr><td class="txtPag">No existen sanciones disciplinarias por '.$nombreSancion['0']['name_tpdisciplinary'].' en los &uacute;ltimos 6 meses</td></tr>';	
		}
		echo $rslt;
		
	break;
	
	case 'getNombreSanciones';
		$sqlText = "select name_tpdisciplinary from type_disciplinary where id_tpdisciplinary=".$_POST['idTpSanc'];
		$nombreSancion = $dbEx->selSql($sqlText);
		echo $nombreSancion['0']['name_tpdisciplinary'];
	break;
	
	case 'consultap': //Muestra todas las ap de un empleado
		$sqlText = "select * from type_ap";
		$dtAps = $dbEx->selSql($sqlText);
		$tblA .='<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">';
		foreach ($dtAps as $dtAp){ // Recupera las apxemp
			$sqlText = "select date_format(startdate_ap,'%d/%m/%Y') as f1, date_format(enddate_ap,'%d/%m/%Y') as f2, date_format(storagedate_ap,'%d/%m/%Y') as f3, comment_ap from apxemp where EMPLOYEE_ID=".$_POST['idE']." and ID_TPAP=".$dtAp['ID_TPAP'];
			$dtAg = $dbEx->selSql($sqlText);
			if($dbEx->numrows >0){
					$tblA .='<tr><td colspan="4" class="showItem">'.$dtAp['NAME_TPAP'].'</td></tr>';	
					$tblA .='<tr class="showItem"><td width="100">Del</td><td width="100">Al</td><td width="100">Fecha de Registro</td><td width="200">Observaciones</td></tr>';
					foreach($dtAg as $dtAg){
							$tblA .= '<tr class="txtPag"><td>'.$dtAg['f1'].'</td><td>'.$dtAg['f2'].'</td><td>'.$dtAg['f3'].'</td><td>'.$dtAg['comment_ap'].'</td></tr>';
					}
			}
		}
		$tblA .='<tr><td colspan="4" align="right" ><img src="images/arriba.png" width="28" align="absmiddle" border="0" onclick="upcons()"></td></tr>';
		$tblA .='</table>';	
		echo $tblA;
	break;
	
	case 'sv_appermiso': //guarda las ap de permisos con y sin goce de sueldo
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
		$insert = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}
		
		$sqlText = "insert into apxemp set EMPLOYEE_ID=".$_POST['idE'].", ID_TPAP=".$_POST['idAp'].", STARTDATE_AP='".$fec_ini."', ENDDATE_AP='".$fec_fin."', HOURS_AP='".$_POST['horas']."', STORAGEDATE_AP=now(), autor_ap=".$_SESSION['usr_id'].",  comment_ap='".$_POST['observ']."'".$insert;
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE'];
		$idC = $dbEx->selSql($sqlText);
		$rslt = $idC['0']['id'];
		echo $rslt;
	break;
		
	case 'saveup_permiso':
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
		$sqlText = "update apxemp set startdate_ap='".$fec_ini."', enddate_ap='".$fec_fin."', storagedate_ap=now(), hours_ap='".$_POST['horas']."', comment_ap='".$_POST['observ']."' where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		
		//Si la actualizacion es por rechazo se quita el id de quien rechazo.
		$sqlText = "select autor_work, approved_work, autor_area, approved_area, autor_hr, approved_hr, autor_generalman, approved_general from apxemp where id_apxemp = ".$_POST['id'];
		$dtAutorNegar = $dbEx->selSql($sqlText);
		if($dtAutorNegar['0']['approved_work']=='N'){
			$sqlText = "update apxemp set autor_work =0, approved_work='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText); 
		}
		if($dtAutorNegar['0']['approved_area']=='N'){
			$sqlText = "update apxemp set autor_area=0, approved_area='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_hr']=='N'){
			$sqlText = "update apxemp set autor_hr=0, approved_hr='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_general']=='N'){
			$sqlText = "update apxemp set autor_generalman=0, approved_general='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		
		echo $_POST['id'];
	break;
	
	case 'sv_traslados': //guarda las ap de traslados
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$insert = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}
		
		//Obtiene los datos anteriores y nuevos del agente
		$sqlText = "select id_plxemp, id_placexdep from plazaxemp where employee_id=".$_POST['idE']." and status_plxemp='A'";
		$dtPlazaOld = $dbEx->selSql($sqlText);
		
		$dtPlazaNew = $_POST['plaza'];
		
		$sqlText = "select id_supervisor from employees where employee_id=".$_POST['idE'];
		$dtOldSup = $dbEx->selSql($sqlText);
		
		//Actualizamos el supervisor del empleado
		$sqlText = "update employees set id_supervisor=".$_POST['superv']." where employee_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);		
		
		//Guarda la nueva ap de traslado
		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].", startdate_ap='".$fec_ini."', storagedate_ap=now(), id_placexdep_new=".$dtPlazaNew.", id_placexdep_old=".$dtPlazaOld['0']['id_placexdep'].", supervisor_new=".$_POST['superv'].", supervisor_old=".$dtOldSup['0']['id_supervisor'].", autor_ap=".$_SESSION['usr_id'].", comment_ap='".$_POST['observ']."'".$insert; 
		$dbEx->insSql($sqlText);
		
		
		//Buscamos el ultimo id de ap ingresado
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp'];
		$idC = $dbEx->selSql($sqlText);
		$rslt = $idC['0']['id'];
		
		//actualiza el registro de plaza del empleado a inactiva
		$sqlText = "update plazaxemp set status_plxemp='I' where id_PLXEMP=".$_POST['idPxe'];
		$dbEx->updSql($sqlText);
		
		//ingresa la nueva plaza del empleado
		$sqlText = 	"insert into plazaxemp set id_placexdep =".$_POST['plaza'].", employee_id=".$_POST['idE'].", status_plxemp='A', id_apxemp=".$idC['0']['id'];	
		$dbEx->insSql($sqlText);
		
		echo $rslt;
	break;
	
	case 'saveup_traslado'://Actualiza los datos de un traslado
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		
		$sqlText = " update apxemp set startdate_ap='".$fec_ini."', comment_ap='".$_POST['observ']."', id_placexdep_new=".$_POST['posicion'].", supervisor_new=".$_POST['supervisor']." where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		$sqlText = "update plazaxemp set id_placexdep=".$_POST['posicion']." where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		$sqlText = "select employee_id from plazaxemp where id_apxemp=".$_POST['id'];
		$dtE = $dbEx->selSql($sqlText);
		$sqlText = "update employees set id_supervisor=".$_POST['supervisor']." where employee_id=".$dtE['0']['employee_id'];
		$dbEx->updSql($sqlText);
		
		//Si la actualizacion es por rechazo se quita el id de quien rechazo.
		$sqlText = "select autor_work, approved_work, autor_area, approved_area, autor_hr, approved_hr, autor_generalman, approved_general from apxemp where id_apxemp = ".$_POST['id'];
		$dtAutorNegar = $dbEx->selSql($sqlText);
		if($dtAutorNegar['0']['approved_work']=='N'){
			$sqlText = "update apxemp set autor_work =0, approved_work='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText); 
		}
		if($dtAutorNegar['0']['approved_area']=='N'){
			$sqlText = "update apxemp set autor_area=0, approved_area='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_hr']=='N'){
			$sqlText = "update apxemp set autor_hr=0, approved_hr='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_general']=='N'){
			$sqlText = "update apxemp set autor_generalman=0, approved_general='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		
		$rslt = $_POST['id'];
		echo $rslt;
		
	break;
	
	case 'sv_reingresos': //guarda las ap de reingresos
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$insert = "";
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}
		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].", startdate_ap='".$fec_ini."', storagedate_ap=now(), autor_ap=".$_SESSION['usr_id'].", comment_ap='".$_POST['observ']."'".$insert;
		$dbEx->insSql($sqlText);
		//actualiza el registro de plaza del empleado a inactiva
		$sqlText = "update plazaxemp set status_plxemp='I' where id_PLXEMP=".$_POST['idPxe'];
		$dbEx->updSql($sqlText);
		//Buscamos el ultimo id de ap ingresado
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp'];
		$idC = $dbEx->selSql($sqlText);
		$rslt = $idC['0']['id'];
		//ingresa la nueva plaza del empleado
		$sqlText = 	"insert into plazaxemp set id_placexdep =".$_POST['plaza'].", employee_id=".$_POST['idE'].", status_plxemp='A', id_apxemp=".$idC['0']['id'];	
		$dbEx->insSql($sqlText);
		
		//Actualizamos datos del empleado
		$sqlText = "update employees set id_supervisor=".$_POST['superv'].", user_status=1 where employee_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);
		echo $rslt;
	break;
	
	case 'sv_vacaciones': //guarda las ap de vacaciones
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$fec_fin = suma_fechas($_POST['fec_ini'],$_POST['dias']-1);
		$fecha_fin = $oFec->cvDtoY($fec_fin);
		$insert = "";
		
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}

		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].",  startdate_ap='".$fec_ini."', enddate_ap='".$fecha_fin."', storagedate_ap=now(), autor_ap=".$_SESSION['usr_id'].", comment_ap='".$_POST['observ']."'".$insert;
		$dbEx->insSql($sqlText);
		
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp'];
		$idC = $dbEx->selSql($sqlText);
		$rslt = $idC['0']['id'];
		echo $rslt;
	break;
	
	case 'saveup_vacaciones': //Actualiza vacaciones
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$fec_fin = suma_fechas($_POST['fec_ini'],$_POST['dias']-1);
		$fecha_fin = $oFec->cvDtoY($fec_fin);
		$sqlText = "update apxemp set startdate_ap='".$fec_ini."', enddate_ap='".$fecha_fin."', comment_ap='".$_POST['observ']."' where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		
		//Si la actualizacion es por rechazo se quita el id de quien rechazo.
		$sqlText = "select autor_work, approved_work, autor_area, approved_area, autor_hr, approved_hr, autor_generalman, approved_general from apxemp where id_apxemp = ".$_POST['id'];
		$dtAutorNegar = $dbEx->selSql($sqlText);
		if($dtAutorNegar['0']['approved_work']=='N'){
			$sqlText = "update apxemp set autor_work =0, approved_work='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText); 
		}
		if($dtAutorNegar['0']['approved_area']=='N'){
			$sqlText = "update apxemp set autor_area=0, approved_area='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_hr']=='N'){
			$sqlText = "update apxemp set autor_hr=0, approved_hr='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_general']=='N'){
			$sqlText = "update apxemp set autor_generalman=0, approved_general='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		
		echo $_POST['id'];
		
	break;

	case 'sv_disciplinarias': // Guardar disciplinarias
		$fec_ini = $oFec->cvDtoY($_POST['fecha']);
		$fec_fin = '0000-00-00';
		if($_POST['dias']>0){
			$fin = suma_fechas($_POST['fecha'],$_POST['dias']-1);
			$fec_fin = $oFec->cvDtoY($fin);
			}	
			
		$insert = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}
		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].", startdate_ap='".$fec_ini."', enddate_ap='".$fec_fin."', storagedate_ap=now(), typesanction_ap=".$_POST['disc'].", id_tpdisciplinary=".$_POST['falta']." , autor_ap=".$_SESSION['usr_id'].", comment_ap='".$_POST['observ']."'".$insert;
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp'];
		$idC = $dbEx->selSql($sqlText);
		$rslt = $idC['0']['id'];
		echo $rslt;

	break;
	
	case 'saveup_disciplinaria': // Actualiza sancion disciplinaria
		$fec_ini = $oFec->cvDtoY($_POST['fechaInicio']);
		$fec_fin = '0000-00-00';
		if($_POST['diasSuspension']>0 and $_POST['tpSancion']==3){
			$fin = suma_fechas($_POST['fechaInicio'],$_POST['diasSuspension'] - 1);
			$fec_fin = $oFec->cvDtoY($fin);
		}
		
		$sqlText = "update apxemp set startdate_ap='".$fec_ini."', enddate_ap='".$fec_fin."', typesanction_ap=".$_POST['tpSancion'].", id_tpdisciplinary=".$_POST['tpDisc'].", comment_ap='".$_POST['observ']."' where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		echo $_POST['id'];
	break;
	
	case 'sv_incapacidades': // Guardad incapacidades
		$fecha_ini = $oFec->cvDtoY($_POST['fecha_ini']);
		$fecha_fin = $oFec->cvDtoY($_POST['fecha_fin']);
		
		/*
		if($_POST['incap']==1){   //Si la incapacidad es del ISSS verifica si la incapacidad sera pagada o no
			$n = 0;
			$anio = date("Y");
			$sqlText = "select date_format(startdate_ap,'%d/%m/%Y') as f1, date_format(enddate_ap,'%d/%m/%Y') as f2 from apxemp where id_center=1 and employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp']." and YEAR(enddate_ap)=".$anio;
			$incap = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0)
			{ //Verifica si tiene registros de incapacidades
				foreach($incap as $incap){
					$dias = restaFechas($incap['f1'],$incap['f2']) + 1;
					$n = $n + $dias;
				}
			}
			$dias_incap = restaFechas($_POST['fecha_ini'],$_POST['fecha_fin']) + 1;
			if($n>=3){ $observ = "Incapacidad no pagada por tener 3/3 d&iacute;as pagados en el a&ntilde;o";}
			if($n==2){ $observ = "Se pagar&aacute;n 1 de ".$dias_incap." d&iacute;as de incapacidad";}
			if($n==1){
				if($dias_incap>=2){$observ = "Se pagar&aacute;m 2 de ".$dias_incap." d&iacute;as de incapacidad";}
				else if($dias_incap ==1){ $observ = "Se pagar&aacute; 1 d&iacute;a de incapacidad";}
				}
			if($n==0){
				if($dias_incap>=3){$observ = "Se pagar&aacute;n 3 de ".$dias_incap." d&iacute;as de incapacidad";}
				else if($dias_incap ==2){$observ = "Se pagar&aacute;n 2 de 2 d&iacute;as de incapacidad";}
				else if($dias_incap ==1){$observ = "Se pagar&aacute; 1 d&iacute;a de incapacidad";}
			}
			
		}
		else{
			$observ = "Incapacidad no pagada por ser de Medico Particular/Unidad de Salud.** Para ser pagada debe presertar validez del ISSS **";
		}*/
		$insert = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}
		
		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].", id_center=".$_POST['incap'].", startdate_ap='".$fecha_ini."', enddate_ap='".$fecha_fin."', hours_ap=".$_POST['horas'].", storagedate_ap=now(), typeincap_ap=".$_POST['tipo'].", autor_ap=".$_SESSION['usr_id'].", comment_ap='".$_POST['observ']."' ".$insert;
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp'];
		$idC = $dbEx->selSql($sqlText);
		$rslt = $idC['0']['id'];
		echo $rslt;
	break;

	case 'saveup_incapacidad': //Actualiza las incapacidades
		$fecha_ini = $oFec->cvDtoY($_POST['fecha_ini']);
		$fecha_fin = $oFec->cvDtoY($_POST['fecha_fin']);
		$sqlText = "select employee_id, id_tpap, startdate_ap from apxemp where id_apxemp=".$_POST['idAp'];
		$dtE = $dbEx->selSql($sqlText);/*
		if($_POST['center']==1){   //Si la incapacidad es del ISSS verifica si la incapacidad sera pagada o no
			$n = 0;
			$anio = date("Y");
			$sqlText = "select date_format(startdate_ap,'%d/%m/%Y') as f1, date_format(enddate_ap,'%d/%m/%Y') as f2 from apxemp where id_center=1 and employee_id=".$dtE['0']['employee_id']." and YEAR(enddate_ap)=".$anio." and startdate_ap<'".$dtE['0']['startdate_ap']."' and id_tpap=".$dtE['0']['id_tpap'];
			$incap = $dbEx->selSql($sqlText);
			foreach($incap as $incap){
				$dias = restaFechas($incap['f1'],$incap['f2']) + 1;
				$n = $n + $dias;
			}
			$dias_incap = restaFechas($_POST['fecha_ini'],$_POST['fecha_fin']) + 1;
			if($n>=3){ $observ = "Incapacidad no pagada por tener 3/3 d&iacute;as pagados en el a&ntilde;o";}
			if($n==2){ $observ = "Se pagar&aacute;n 1 de ".$dias_incap." d&iacute;as de incapacidad";}
			if($n==1){
				if($dias_incap>=2){$observ = "Se pagar&aacute;m 2 de ".$dias_incap." d&iacute;as de incapacidad";}
				else if($dias_incap ==1){ $observ = "Se pagar&aacute; 1 d&iacute;a de incapacidad";}
				}
			if($n==0){
				if($dias_incap>=3){$observ = "Se pagar&aacute;n 3 de ".$dias_incap." d&iacute;as de incapacidad";}
				else if($dias_incap ==2){$observ = "Se pagar&aacute;n 2 de 2 d&iacute;as de incapacidad";}
				else if($dias_incap ==1){$observ = "Se pagar&aacute; 1 d&iacute;as de incapacidad";}
			}
		}
		else{
			$observ = "Incapacidad no pagada por ser de Medico Particular/Unidad de Salud.** Para ser pagada debe presertar validez del ISSS **";
		}*/
		$sqlText = "update apxemp set id_center=".$_POST['center'].", startdate_ap='".$fecha_ini."', enddate_ap='".$fecha_fin."', hours_ap=".$_POST['horas'].", typeincap_ap=".$_POST['tipo'].", comment_ap='".$_POST['observ']."' where id_apxemp=".$_POST['idAp'];
		$dbEx->updSql($sqlText);
		
		//Si la actualizacion es por rechazo se quita el id de quien rechazo.
		$sqlText = "select autor_work, approved_work, autor_area, approved_area, autor_hr, approved_hr, autor_generalman, approved_general from apxemp where id_apxemp = ".$_POST['idAp'];
		$dtAutorNegar = $dbEx->selSql($sqlText);
		if($dtAutorNegar['0']['approved_work']=='N'){
			$sqlText = "update apxemp set autor_work =0, approved_work='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText); 
		}
		if($dtAutorNegar['0']['approved_area']=='N'){
			$sqlText = "update apxemp set autor_area=0, approved_area='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_hr']=='N'){
			$sqlText = "update apxemp set autor_hr=0, approved_hr='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_general']=='N'){
			$sqlText = "update apxemp set autor_generalman=0, approved_general='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText);
		}
		
		echo $_POST['idAp'];
	
	break;

	case 'sv_retiros'://Guarda los diferentes tipos de retiros
		$ultimo = $oFec->cvDtoY($_POST['ultimo']);
		/*
		$sqlText = "update employees set user_status=0 where employee_id =".$_POST['idE'];
		$dbEx->updSql($sqlText);*/
		$insert = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}
		
		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].", startdate_ap='".$ultimo."', storagedate_ap=now(), autor_ap=".$_SESSION['usr_id'].", comment_ap='".$_POST['observ']."'".$insert;
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp'];
		$idC = $dbEx->selSql($sqlText);
		$rslt = $idC['0']['id'];
		echo $rslt;
		
	break;
	case 'saveup_retiros': //Permite actualizar una ap de retiro
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$sqlText = "update apxemp set startdate_ap='".$fec_ini."', comment_ap='".$_POST['observ']."' where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		$rslt = $_POST['id'];
		
		//Si la actualizacion es por rechazo se quita el id de quien rechazo.
		$sqlText = "select autor_work, approved_work, autor_area, approved_area, autor_hr, approved_hr, autor_generalman, approved_general from apxemp where id_apxemp = ".$_POST['id'];
		$dtAutorNegar = $dbEx->selSql($sqlText);
		if($dtAutorNegar['0']['approved_work']=='N'){
			$sqlText = "update apxemp set autor_work =0, approved_work='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText); 
		}
		if($dtAutorNegar['0']['approved_area']=='N'){
			$sqlText = "update apxemp set autor_area=0, approved_area='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_hr']=='N'){
			$sqlText = "update apxemp set autor_hr=0, approved_hr='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_general']=='N'){
			$sqlText = "update apxemp set autor_generalman=0, approved_general='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		
		echo $rslt;	
	break;
	
	case 'sv_puesto': //Guarda una ap de nuevo puesto/periodo de prueba
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$sqlText = "select id_supervisor from employees where employee_id=".$_POST['idE'];
		$dtOldSup = $dbEx->selSql($sqlText);
		
		$sqlText = "update employees set id_supervisor=".$_POST['supervisor'].", salary=".$_POST['salario'].", tp_hiring='".$_POST['plaza']."' where employee_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);
		$insert = "";

		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}

		$sqlText = "select id_plxemp, id_placexdep from plazaxemp where employee_id=".$_POST['idE']." and status_plxemp='A'";
		$dtPlazaOld = $dbEx->selSql($sqlText);
		
		$dtPlazaNew = $_POST['posicion'];
		
		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].", startdate_ap='".$fec_ini."', storagedate_ap=now(), id_placexdep_new=".$dtPlazaNew.", id_placexdep_old=".$dtPlazaOld['0']['id_placexdep'].", supervisor_new=".$_POST['supervisor'].", supervisor_old=".$dtOldSup['0']['id_supervisor'].", autor_ap=".$_SESSION['usr_id'].", comment_ap='".$_POST['observ']."'".$insert; 
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=".$_POST['idAp'];
		$idC = $dbEx->selSql($sqlText);
		
		$sqlText = "update plazaxemp set status_plxemp='I' where id_plxemp=".$dtPlazaOld['0']['id_plxemp'];
		$dbEx->updSql($sqlText);
		
		$sqlText = "insert into plazaxemp set id_placexdep=".$dtPlazaNew.", employee_id=".$_POST['idE'].", pprueba_plxemp=".$_POST['prueba'].", status_plxemp='A', id_apxemp=".$idC['0']['id'];
		$dbEx->insSql($sqlText);
		$rslt = $idC['0']['id'];
		echo $rslt;
	
	break;
	
	case 'saveup_puesto': //Actualiza ap por puesto
		$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
		$sqlText = " update apxemp set startdate_ap='".$fec_ini."', comment_ap='".$_POST['observ']."', id_placexdep_new=".$_POST['posicion'].", supervisor_new=".$_POST['supervisor']." where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		$sqlText = "select employee_id from apxemp where id_apxemp=".$_POST['id'];
		$dtE = $dbEx->selSql($sqlText);
		$sqlText = " update employees set salary=".$_POST['salario'].", tp_hiring='".$_POST['plaza']."', id_supervisor=".$_POST['supervisor']." where employee_id=".$dtE['0']['employee_id'];
		$dbEx->updSql($sqlText);
		$sqlText = " update plazaxemp set pprueba_plxemp=".$_POST['prueba'].", id_placexdep=".$_POST['posicion']." where id_apxemp=".$_POST['id'];
		
		$dbEx->updSql($sqlText);
		
		//Si la actualizacion es por rechazo se quita el id de quien rechazo.
		$sqlText = "select autor_work, approved_work, autor_area, approved_area, autor_hr, approved_hr, autor_generalman, approved_general from apxemp where id_apxemp = ".$_POST['id'];
		$dtAutorNegar = $dbEx->selSql($sqlText);
		if($dtAutorNegar['0']['approved_work']=='N'){
			$sqlText = "update apxemp set autor_work =0, approved_work='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText); 
		}
		if($dtAutorNegar['0']['approved_area']=='N'){
			$sqlText = "update apxemp set autor_area=0, approved_area='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_hr']=='N'){
			$sqlText = "update apxemp set autor_hr=0, approved_hr='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_general']=='N'){
			$sqlText = "update apxemp set autor_generalman=0, approved_general='0' where id_apxemp=".$_POST['id'];
			$dbEx->updSql($sqlText);
		}
		
		echo $_POST['id'];
	break; 
	
	//Guarda el contrato de aviso
	case 'sv_contratoAviso':
		$insert = "";
		
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$insert .= ", autor_area=".$_SESSION['usr_id'].", approved_area='S' ";	
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$insert .= ", autor_work=".$_SESSION['usr_id'].", approved_work='S' ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$insert .= ", autor_hr=".$_SESSION['usr_id'].", approved_hr='S'";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$insert .= ", autor_generalman=".$_SESSION['usr_id'].", approved_general='S'";	
		}
	
		$sqlText = "insert into apxemp set id_tpap=".$_POST['idAp'].", employee_id=".$_POST['idE'].", startdate_ap=now(), storagedate_ap=now(), id_tpdisciplinary=".$_POST['tpDisc'].", autor_ap=".$_SESSION['usr_id']." ".$insert;
		$dbEx->insSql($sqlText);
		
		$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$_POST['idE']." and id_tpap=16";
		$dtAp = $dbEx->selSql($sqlText);
		echo $dtAp['0']['id'];
		
	break;
	
	//Actualiza contrato de Aviso
	case 'sv_upContratoAviso':
		$fec_ini = $oFec->cvDtoY($_POST['fecha']);
		$sqlText = "update apxemp set startdate_ap='".$fec_ini."', id_tpdisciplinary=".$_POST['tpDisc']." where id_apxemp=".$_POST['idAp'];
		$dbEx->updSql($sqlText);
		
		//Si la actualizacion es por rechazo se quita el id de quien rechazo.
		$sqlText = "select autor_work, approved_work, autor_area, approved_area, autor_hr, approved_hr, autor_generalman, approved_general from apxemp where id_apxemp = ".$_POST['idAp'];
		$dtAutorNegar = $dbEx->selSql($sqlText);
		if($dtAutorNegar['0']['approved_work']=='N'){
			$sqlText = "update apxemp set autor_work =0, approved_work='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText); 
		}
		if($dtAutorNegar['0']['approved_area']=='N'){
			$sqlText = "update apxemp set autor_area=0, approved_area='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_hr']=='N'){
			$sqlText = "update apxemp set autor_hr=0, approved_hr='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText);
		}
		if($dtAutorNegar['0']['approved_general']=='N'){
			$sqlText = "update apxemp set autor_generalman=0, approved_general='0' where id_apxemp=".$_POST['idAp'];
			$dbEx->updSql($sqlText);
		}
		echo $_POST['idAp'];
		
	break;
	
	case 'loadApxE': //Muestra la ap recien guardada
		$sqlText = "select *, date_format(startdate_ap,'%d/%m/%Y') as start, date_format(enddate_ap,'%d/%m/%Y') as end, ".
			"date_format(storagedate_ap,'%d/%m/%Y') as stg, date_format(date_admis,'%d/%m/%Y') as admis, replace(comment_ap,'#','No') comment_ap_replace ".
			" from apxemp ape inner join employees em on ape.employee_id = em.employee_id inner join type_ap ta on ta.id_tpap = ape.id_tpap ".
			" where id_apxemp=".$_POST['id'];
		$dtAp = $dbEx->selSql($sqlText);
		
		//verifica las firmas
		$aprob_emp ="";
		$aprob_work = "";
		$aprob_area = "";
		$aprob_hr = "";
		$aprob_gen = "";
		$sqlText = "select pd.id_role, name_role from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where e.employee_id=".$dtAp['0']['AUTOR_AP'];
		$rol_autor = $dbEx->selSql($sqlText);
		$sqlText = "select id_role from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where e.employee_id=".$dtAp['0']['EMPLOYEE_ID'];
		$rol_emp = $dbEx->selSql($sqlText);
		
		$sqlText = "select e.employee_id, e.firstname, e.lastname from employees e inner join apxemp ape on e.employee_id=ape.autor_ap=".$dtAp['0']['AUTOR_AP'];
		
		$sqlText2 = "select e.employee_id, e.firstname, e.lastname, pl.name_place from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places pl on pl.id_place=pd.id_place inner join apxemp ape on e.employee_id=";
		
		$sqlText3 = "select d.id_depart, name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id where e.employee_id=".$dtAp['0']['AUTOR_AP'];
		$dep_autor = $dbEx->selSql($sqlText3);

		$sqlText = "select name_depart from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join depart_exc d on pd.id_depart=d.id_depart where e.employee_id=".$dtAp['0']['EMPLOYEE_ID']." and pe.status_plxemp='A'";
		$dep_emp = $dbEx->selSql($sqlText);
	
		if($rol_emp['0']['id_role'] <=3){ //Si el empleado al q se le realizo la ap es agente o supervisor	
			if($rol_autor['0']['name_role']!='GERENTE DE AREA' and $dtAp['0']['ID_TPAP']!=15  and $dtAp['0']['ID_TPAP']!=16 and $dep_autor['0']['name_depart']!='CHAT' and $dep_emp['0']['name_depart']!='CHAT'){
			if($dtAp['0']['APPROVED_AREA']=='0'){$aprob_area='Gerente de Area<br>Pendiente de aprobaci&oacute;n<br><br>';}
			else{
			$sqlText = $sqlText2." ape.autor_area where e.employee_id=".$dtAp['0']['AUTOR_AREA']." and pe.status_plxemp='A'";
				$dtarea = $dbEx->selSql($sqlText);
				if($dtAp['0']['APPROVED_AREA']=='S'){
					$aprob_area='________________________<br>'.$dtarea['0']['name_place'].'<br>'.$dtarea['0']['firstname'].'&nbsp;'.$dtarea['0']['lastname'].'<br>Aprobado<br>';}
				if($dtAp['0']['APPROVED_AREA']=='N'){
					$aprob_area='________________________<br>'.$dtarea['0']['name_place'].'<br>'.$dtarea['0']['firstname'].'&nbsp;'.$dtarea['0']['lastname'].'<br>Rechazado<br>';			}
			}	
			}
			
			if($rol_autor['0']['name_role']!='WORKFORCE' and $dtAp['0']['ID_TPAP']!=15  and $dtAp['0']['ID_TPAP']!=16 and $dtAp['0']['TYPESANCTION_AP']!=1 and $dtAp['0']['TYPESANCTION_AP']!=2 and $dep_autor['0']['name_depart']!='CHAT' ){
			if($dtAp['0']['APPROVED_WORK']=='0'){$aprob_work='Workforce<br>Pendiente de aprobaci&oacute;n<br><br>';}
			else{
				$sqlText = $sqlText2." ape.autor_work where e.employee_id=".$dtAp['0']['AUTOR_WORK']." and pe.status_plxemp='A'";
				$dtwork = $dbEx->selSql($sqlText);
				if($dtAp['0']['APPROVED_WORK']=='S'){
					$aprob_work='________________________<br>'.$dtwork['0']['name_place'].'<br>'.$dtwork['0']['firstname'].'&nbsp;'.$dtwork['0']['lastname'].'<br>Aprobado<br>';}
				if($dtAp['0']['APPROVED_WORK']=='N'){
					$aprob_work='________________________<br>'.$dtwork['0']['name_place'].'<br>'.$dtwork['0']['firstname'].'&nbsp;'.$dtwork['0']['lastname'].'<br>Rechazado<br>';				}
				}
			}
		}
		if($rol_emp['0']['id_role']<=7){
			if($rol_autor['0']['name_role']!='GERENTE GENERAL' and $dtAp['0']['ID_TPAP']!=15 and $dtAp['0']['ID_TPAP']!=7 and $dtAp['0']['ID_TPAP']!=1 and $dtAp['0']['ID_TPAP']!=2 and $dtAp['0']['TYPESANCTION_AP']!=1 and $dtAp['0']['TYPESANCTION_AP']!=2){
			if($dtAp['0']['APPROVED_GENERAL']=='0'){$aprob_gen='Gerente General<br>Pendiente de aprobaci&oacute;n<br><br>';}
		else{
			$sqlText = $sqlText2." ape.autor_generalman where e.employee_id=".$dtAp['0']['AUTOR_GENERALMAN']." and pe.status_plxemp='A'";
			$dtgen = $dbEx->selSql($sqlText);
			if($dtAp['0']['APPROVED_GENERAL']=='S'){
				$aprob_gen='________________________<br>'.$dtgen['0']['name_place'].'<br>'.$dtgen['0']['firstname'].'&nbsp;'.$dtgen['0']['lastname'].'<br>Aprobado<br>';}
			if($dtAp['0']['APPROVED_GENERAL']=='N'){
				$aprob_gen='________________________<br>'.$dtgen['0']['name_place'].'<br>'.$dtgen['0']['firstname'].'&nbsp;'.$dtgen['0']['lastname'].'<br>Rechazado<br>';
			}
			}	
			}
		}

		$aprob_emp='________________________<br>Empleado<br>'.$dtAp['0']['FIRSTNAME'].'&nbsp;'.$dtAp['0']['LASTNAME'];
		$sqlText = $sqlText2." ape.autor_ap where e.employee_id=".$dtAp['0']['AUTOR_AP']." and pe.status_plxemp='A'";
		$dtsup = $dbEx->selSql($sqlText);
		$aprob_superv = '________________________<br>'.$dtsup['0']['name_place'].'<br>'.$dtsup['0']['firstname'].'&nbsp;'.$dtsup['0']['lastname'].'<br>Aprobado<br>';

		if($rol_autor['0']['name_role']!='RECURSOS HUMANOS'){
		if($dtAp['0']['APPROVED_HR']=='0'){$aprob_hr='Recursos Humanos<br>Pendiente de aprobaci&oacute;n<br><br>';}
		else{
			$sqlText = $sqlText2." ape.autor_hr where e.employee_id=".$dtAp['0']['AUTOR_HR']." and pe.status_plxemp='A'";
			$dthr = $dbEx->selSql($sqlText);
			if($dtAp['0']['APPROVED_HR']=='S'){
				$aprob_hr='________________________<br>'.$dthr['0']['name_place'].'<br>'.$dthr['0']['firstname'].'&nbsp;'.$dthr['0']['lastname'].'<br>Aprobado<br>';}
			if($dtAp['0']['APPROVED_HR']=='N'){
				$aprob_hr='________________________<br>'.$dthr['0']['name_place'].'<br>'.$dthr['0']['firstname'].'&nbsp;'.$dthr['0']['lastname'].'<br>Rechazado<br>';}
		}
		}

		
		if($_POST['accion']==1){ //Si la accion es ver las ap
			if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2){
				$rslt = cargaPag("../mtto/load_permiso.php");	
			}
			if($dtAp['0']['ID_TPAP']==3){
				$rslt = cargaPag("../mtto/load_traslado.php");	
			}
			if($dtAp['0']['ID_TPAP']==4){
				$rslt = cargaPag("../mtto/load_reingreso.php");	
			}
			if($dtAp['0']['ID_TPAP']==5){
				$rslt = cargaPag("../mtto/load_vacaciones.php");
				}
			if($dtAp['0']['ID_TPAP']==6){
				$rslt = cargaPag("../mtto/load_disciplinarias.php");
			}
			if($dtAp['0']['ID_TPAP']==7){
				$rslt = cargaPag("../mtto/load_incapacidad.php");	
			}
			if($dtAp['0']['ID_TPAP']==8 or $dtAp['0']['ID_TPAP']==15){
				$rslt = cargaPag("../mtto/load_puesto.php");	
			}
			if($dtAp['0']['ID_TPAP']>=9 and $dtAp['0']['ID_TPAP']<=14){
				$rslt = cargaPag("../mtto/load_retiros.php");	
			}
			if($dtAp['0']['ID_TPAP']==16){
				$rslt = cargaPag("../mtto/load_contratoAviso.php");	
			}
			
		}
		else if($_POST['accion']==2){ //Si la accion es actualizar la ap
			$aprob = 0;
			if($dtAp['0']['APPROVED_AREA']!='N' and $dtAp['0']['APPROVED_WORK']!='N' and $dtAp['0']['APPROVED_HR']!='N' and $dtAp['0']['APPROVED_GENERAL']!='N'){ //Si la AP fue rechazada permite al sup actualizar
					$aprob = $dtAp['0']['AUTOR_WORK'] + $dtAp['0']['AUTOR_AREA'] + $dtAp['0']['AUTOR_HR'] + $dtAp['0']['AUTOR_GENERALMAN'];
				if($rol_autor['0']['name_role']=='GERENTE DE AREA'){
					$aprob = $dtAp['0']['AUTOR_WORK'] + $dtAp['0']['AUTOR_HR'] + $dtAp['0']['AUTOR_GENERALMAN'];
				}
				else if($rol_autor['0']['name_role']=='WORKFORCE'){
					$aprob = $dtAp['0']['AUTOR_AREA'] + $dtAp['0']['AUTOR_HR'] + $dtAp['0']['AUTOR_GENERALMAN'];
				}
				else if($rol_autor['0']['name_role']=='RECURSOS HUMANOS'){
					$aprob = $dtAp['0']['AUTOR_AREA'] + $dtAp['0']['AUTOR_WORK'] + $dtAp['0']['AUTOR_GENERALMAN'];
				}
				else if($rol_autor['0']['name_role']=='GERENCIA'){
					$aprob = $dtAp['0']['AUTOR_AREA'] + $dtAp['0']['AUTOR_WORK'] + $dtAp['0']['AUTOR_HR'];
				}
			}
				if($aprob>0){
					$rslt = -1;	
					echo $rslt;
					break;
				}
				else if($_SESSION['usr_idrol']<$rol_autor['0']['id_role']){
					$rslt = -2;
					echo $rslt;
					break;
				}

			else{	
				if($dtAp['0']['ID_TPAP']==1 or $dtAp['0']['ID_TPAP']==2){
					$rslt = cargaPag("../mtto/up_permiso.php");
				}
				if($dtAp['0']['ID_TPAP']==3){
					$rslt = cargaPag("../mtto/up_traslado.php");
				}
				if($dtAp['0']['ID_TPAP']==4){
					$rslt = cargaPag("../mtto/up_reingreso.php");	
				}
				if($dtAp['0']['ID_TPAP']==5){
					$rslt = cargaPag("../mtto/up_vacaciones.php");
				}
				if($dtAp['0']['ID_TPAP']==6){
					$rslt = cargaPag("../mtto/up_disciplinarias.php");	
				}
				if($dtAp['0']['ID_TPAP']==7){
					$rslt = cargaPag("../mtto/up_incapacidad.php");	
				}
				if($dtAp['0']['ID_TPAP']==8 or $dtAp['0']['ID_TPAP']==15){
					$rslt = cargaPag("../mtto/up_puesto.php");	
				}
				if($dtAp['0']['ID_TPAP']>=9 and $dtAp['0']['ID_TPAP']<=14){
					$rslt = cargaPag("../mtto/up_retiros.php");	
				}
				if($dtAp['0']['ID_TPAP']==16){
					$rslt = cargaPag("../mtto/up_contratoAviso.php");
				}
			}
		}
		if($_POST['autorizar']==1){
			$si = "S";
			$no = "N";
			$autor = '<input type="button" class="btn" value="Autorizar" onclick="ResultAutorAp('.$_POST['id'].',0)">';
			$rechaz = '<input type="button" class="btn" value="Rechazar" onclick="ResultAutorAp('.$_POST['id'].',1)">';
			$rslt = str_replace("<!--btn_autor-->",$autor,$rslt);
			$rslt = str_replace("<!--btn_rechaz-->",$rechaz,$rslt);	
		}
		else{
			$rslt = str_replace("<!--btn_autor-->","",$rslt);
			$rslt = str_replace("<!--btn_rechaz-->","",$rslt);	
		}
		$rslt = str_replace("<!--aprob_emp-->",$aprob_emp,$rslt);
		$rslt = str_replace("<!--aprob_superv-->",$aprob_superv,$rslt);
		$rslt = str_replace("<!--aprob_area-->",$aprob_area,$rslt);
		$rslt = str_replace("<!--aprob_work-->",$aprob_work,$rslt);
		$rslt = str_replace("<!--aprob_hr-->",$aprob_hr,$rslt);
		$rslt = str_replace("<!--aprob_gen-->",$aprob_gen,$rslt);

		$rslt = str_replace("<!--apxemp-->",$_POST['id'],$rslt);
		$rslt = str_replace("<!--nom_ap-->",$dtAp['0']['NAME_TPAP'],$rslt);
		$rslt = str_replace("<!--id_ap-->",$dtAp['0']['ID_TPAP'],$rslt);
		$rslt = str_replace("<!--id_emp-->", $dtAp['0']['EMPLOYEE_ID'],$rslt);
		$rslt = str_replace("<!--username-->",$dtAp['0']['USERNAME'],$rslt);
		$rslt = str_replace("<!--first-->",$dtAp['0']['FIRSTNAME'],$rslt);
		$rslt = str_replace("<!--last-->",$dtAp['0']['LASTNAME'],$rslt);
		$rslt = str_replace("<!--salario-->",$dtAp['0']['SALARY'],$rslt);
		$rslt = str_replace("<!--dui-->",$dtAp['0']['DUI'],$rslt);
		$rslt = str_replace("<!--startdate-->",$dtAp['0']['start'],$rslt);
		$rslt = str_replace("<!--enddate-->",$dtAp['0']['end'],$rslt);
		$rslt = str_replace("<!--horas-->",$dtAp['0']['HOURS_AP'],$rslt);
		$rslt = str_replace("<!--storage-->",$dtAp['0']['stg'],$rslt);
		$rslt = str_replace("<!--comment-->",$dtAp['0']['comment_ap_replace'],$rslt);
		$rslt = str_replace("<!--typesanction-->",$dtAp['0']['TYPESANCTION_AP'],$rslt);
		$rslt = str_replace("<!--date_admis-->",$dtAp['0']['admis'],$rslt);
		
		$optHoras = "";
		$i=0;
		while($i<=36){
			$sel = "";
			if($i==$dtAp['0']['HOURS_AP']){$sel="selected";}
			$optHoras .= '<option value="'.$i.'" '.$sel.'>'.$i.'</option>'; 
			$i=$i+0.50;
		}
		$rslt = str_replace("<!--optHoras-->",$optHoras,$rslt);
		
		if($dtAp['0']['TP_HIRING']=='F'){$tp_plaza = "FIJA";}
		if($dtAp['0']['TP_HIRING']=='T'){$tp_plaza = "TEMPORAL";}
		$rslt = str_replace("<!--tipo_plaza-->",$tp_plaza,$rslt);
		$optPl = "";
			if($dtAp['0']['TP_HIRING']=='F'){$sel="selected";}else{$sel="";}
				$optPl .= '<option value="F" '.$sel.'>FIJA</option>';
			if($dtAp['0']['TP_HIRING']=='T'){$sel="selected";}else{$sel="";}
				$optPl .= '<option value="T" '.$sel.'>TEMPORAL</option>';
		$rslt = str_replace("<!--optTipoPlaza-->",$optPl,$rslt);
		
		$dias_incap = restaFechas($dtAp['0']['start'],$dtAp['0']['end'])+1;
		$rslt = str_replace("<!--dias_incap-->",$dias_incap,$rslt);
		
		//Sección de sanciones disciplinarias
		$sqlText = "select name_tpdisciplinary from type_disciplinary where id_tpdisciplinary=".$dtAp['0']['ID_TPDISCIPLINARY'];
		$dtFalta = $dbEx->selSql($sqlText);
		$rslt = str_replace("<!--tipo_falta-->",$dtFalta['0']['name_tpdisciplinary'],$rslt);
		
		if($dtAp['0']['TYPESANCTION_AP']==1){$tipo_sancion="Verbal";}
		if($dtAp['0']['TYPESANCTION_AP']==2){$tipo_sancion="Escrita";}
		if($dtAp['0']['TYPESANCTION_AP']==3){$tipo_sancion="Suspensi&oacute;n";}
		$rslt = str_replace("<!--tipo_sancion-->",$tipo_sancion,$rslt);
		$suspension = "";
		$dias = "";
		if($dtAp['0']['TYPESANCTION_AP']==3){
			$suspension .= '<tr class="txtPag"><td align="right">D&iacute;as de Suspensi&oacute;n:&nbsp;</td><td>';
			$dias = restaFechas($dtAp['0']['start'],$dtAp['0']['end']) + 1;
			$suspension .= $dias.'</td></tr>';
			$suspension .= '<tr class="txtPag"><td align="rigth">Fecha de Suspensi&oacute;n</td><td>'.$dtAp['0']['start'].'</td></tr>';
			}
		$rslt = str_replace("<!--dias-->",$dias,$rslt);
		$rslt = str_replace("<!--suspension-->",$suspension,$rslt);
		
		//Opciones de edicion para disciplinarias
		$sqlText = "select * from type_disciplinary order by name_tpdisciplinary";
		$dtTpDisc = $dbEx->selSql($sqlText);
		$optTpDisc = "";
		foreach($dtTpDisc as $dtTpD){
			$sel = "";
			if($dtAp['0']['ID_TPDISCIPLINARY']==$dtTpD['ID_TPDISCIPLINARY']){
				$sel = "selected";
			}
			$optTpDisc .= '<option value="'.$dtTpD['ID_TPDISCIPLINARY'].'" '.$sel.'>'.$dtTpD['NAME_TPDISCIPLINARY'].'</option>';
		}
		
		$optTpSancion = "";
		$estadoSancion  = ' style="display:none" ';
		$diasSuspension = 0;
		$sel = "";
		if($dtAp['0']['TYPESANCTION_AP']==1){ $sel = "selected";}
			$optTpSancion .= '<option value="1" '.$sel.'>Verbal</option>';
		$sel = "";
		if($dtAp['0']['TYPESANCTION_AP']==2){ $sel = "selected";}
			$optTpSancion .= '<option value="2" '.$sel.'>Escrita</option>';
		$sel = "";
		if($dtAp['0']['TYPESANCTION_AP']==3){
			$sel = "selected";
			$estadoSancion = ' style="display:block" ';	
			$diasSuspension = n_dias($dtAp['0']['STARTDATE_AP'],$dtAp['0']['ENDDATE_AP']) + 1; 
		}
		
		$optTpSancion .= '<option value="3" '.$sel.'>Suspensi&oacute;n</option>';
		
		
		$rslt = str_replace("<!--DiasSuspension-->",$diasSuspension,$rslt);
		$rslt = str_replace("<!--FechaSuspension-->",$dtAp['0']['start'],$rslt);
		$rslt = str_replace("<!--optTipoDisciplinaria-->",$optTpDisc,$rslt);
		$rslt = str_replace("<!--optTipoSancion-->",$optTpSancion,$rslt);
		$rslt = str_replace("<!--styleSuspension-->", $estadoSancion, $rslt);

		//Sección de incapacidades
		$incap = "";
		if($dtAp['0']['TYPEINCAP_AP']==1)$incap = "Inicial";
		if($dtAp['0']['TYPEINCAP_AP']==2)$incap = "Prorroga";
		$rslt = str_replace("<!--typeincap-->",$incap,$rslt);
		
		if($dtAp['0']['ID_CENTER']!=NULL){
			$sqlText = "select name_center from centercare where id_center=".$dtAp['0']['ID_CENTER'];
			$dtC = $dbEx->selSql($sqlText);
			$rslt = str_replace("<!--center-->",$dtC['0']['name_center'],$rslt);
		}
		
		$optI = "";
		for ($i=1; $i<=2; $i++){
			if($i==$dtAp['0']['TYPEINCAP_AP']){$sel="selected";}
			else {$sel = "";}
			if($i==1){$optI .= '<option value="1"'.$sel.'>Inicial</option>';}
			if($i==2){$optI .= '<option value="2"'.$sel.'>Prorroga</option>';}
		}
		$rslt = str_replace("<!--optTI-->",$optI,$rslt);
		
		$sqlText = "select * from centercare";
		$dtC = $dbEx->selSql($sqlText);
		foreach($dtC as $dtC){
			if($dtC['ID_CENTER']==$dtAp['0']['ID_CENTER']){$sel="selected";}
			else{$sel="";}
			$optCenter .= '<option value="'.$dtC['ID_CENTER'].'"'.$sel.'>'.$dtC['NAME_CENTER'];
			}
		$rslt = str_replace("<!--optCenter-->",$optCenter,$rslt);		
		 			
		$sqlText = "select pd.ID_PLACEXDEP, pd.ID_DEPART, NAME_DEPART, pd.ID_ACCOUNT, NAME_ACCOUNT, pd.ID_PLACE, NAME_PLACE, date_format(date_admis,'%d/%m/%Y') as d, pe.id_plxemp as idplxemp, e.id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep inner join depart_exc d on pd.ID_DEPART = d.ID_DEPART inner join places pc on pc.id_place = pd.id_place inner join account cc on pd.id_account=cc.id_account where e.EMPLOYEE_ID=".$dtAp['0']['EMPLOYEE_ID']." and status_plxemp='A'";
		
		$dtE = $dbEx->selSql($sqlText);
		$rslt = str_replace("<!--id_depto-->", $dtE['0']['ID_DEPART'], $rslt);
		$rslt = str_replace("<!--depto-->", $dtE['0']['NAME_DEPART'],$rslt);
		$rslt = str_replace("<!--idcuenta-->",$dtE['0']['ID_ACCOUNT'],$rslt);
		$rslt = str_replace("<!--cuenta-->",$dtE['0']['NAME_ACCOUNT'],$rslt);
		$rslt = str_replace("<!--idplaza-->",$dtE['0']['ID_PLACE'],$rslt);
		$rslt = str_replace("<!--plaza-->", $dtE['0']['NAME_PLACE'],$rslt);
		$rslt = str_replace("<!--idplxemp-->",$dtE['0']['idplxemp'],$rslt);
		
		//Datos de nuevo puesto
		$cuentaOldPuesto = "";
		$departOldPuesto = "";
		$posicionOldPuesto = "";
		$cuentaNewPuesto = "";
		$departNewPuesto = "";
		$posicionNewPuesto = "";
		$optCuentaPuesto = "";
		$optDepartPuesto = "";
		$optPosicionPuesto = "";
		$optSupPuesto = "";
		
		if($dtAp['0']['ID_PLACEXDEP_NEW']>0){
			$sqlText = "select p.id_place, name_place, c.id_account, name_account, d.id_depart, name_depart from placexdep pd inner join places p on p.id_place=pd.id_place inner join account c on c.id_account=pd.id_account inner join depart_exc d on d.id_depart=pd.id_depart where id_placexdep=".$dtAp['0']['ID_PLACEXDEP_NEW'];
			$dtNewPuesto = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$cuentaNewPuesto = $dtNewPuesto['0']['name_account'];
				$departNewPuesto = $dtNewPuesto['0']['name_depart'];
				$posicionNewPuesto = $dtNewPuesto['0']['name_place'];
			}
			$sqlText = "select name_place, name_account, name_depart from placexdep pd inner join places p on p.id_place=pd.id_place inner join account c on c.id_account=pd.id_account inner join depart_exc d on d.id_depart=pd.id_depart where id_placexdep=".$dtAp['0']['ID_PLACEXDEP_OLD'];
			$dtOldPuesto = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$cuentaOldPuesto = $dtOldPuesto['0']['name_account'];
				$departOldPuesto = $dtOldPuesto['0']['name_depart'];
				$posicionOldPuesto = $dtOldPuesto['0']['name_place'];	
			}
			
				//Cuentas para poder actualizar nuevos puestos
			$sqlText = "select * from account order by name_account";
			$dtAccount = $dbEx->selSql($sqlText);
			$optCuentaPuesto = '<select id="lsCuenta" class="txtPag" onChange="getDepart(this.value)">';
			foreach($dtAccount as $dtAc){
			$sel = "";
			if($dtAc['ID_ACCOUNT']==$dtNewPuesto['0']['id_account']){ $sel = "selected";}
				$optCuentaPuesto .='<option value="'.$dtAc['ID_ACCOUNT'].'" '.$sel.'>'.$dtAc['NAME_ACCOUNT'].'</option>';
			}
			$optCuentaPuesto .='</select>';
			
			//Departamentos para actualizar nuevos puestos
			$sqlText = "select distinct(d.id_depart) as id_depart, name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account c on c.id_account=pd.id_account where c.id_account=".$dtNewPuesto['0']['id_account']." order by name_depart ";
			$dtDepart = $dbEx->selSql($sqlText);
			$optDepartPuesto = '<select id="lsDpto" class="txtPag" onchange="getPosc(this.value,'.$dtE['0']['ID_ACCOUNT'].')">';
			foreach($dtDepart as $dtDep){
				$sel = "";
				if($dtDep['id_depart']==$dtNewPuesto['0']['id_depart']){
					$sel = "selected";	
				}
				$optDepartPuesto .='<option value="'.$dtDep['id_depart'].'" '.$sel.'>'.$dtDep['name_depart'].'</option>';
			}
			$optDepartPuesto .='</select>';
		
			//Posiciones para actualizar nuevos puestos
			$sqlText = "select distinct(id_placexdep) as id_pl, name_place from places p inner join placexdep pd on p.id_place=pd.id_place inner join depart_exc d on d.id_depart=pd.id_depart inner join account c on c.id_account = pd.id_account where c.id_account=".$dtNewPuesto['0']['id_account']." and d.id_depart=".$dtNewPuesto['0']['id_depart'];
			$dtPosicion = $dbEx->selSql($sqlText);
			$optPosicionPuesto = "";
			foreach($dtPosicion as $dtPos){
				$sel = "";
				if($dtPos['id_pl']==$dtAp['0']['ID_PLACEXDEP_NEW']){
					$sel = "selected";	
				}
				$optPosicionPuesto .='<option value="'.$dtPos['id_pl'].'" '.$sel.'>'.$dtPos['name_place'].'</option>';
			}
			//Supervisores para actulizar nuevos puestos
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place where p.nivel_place=2 and pe.status_plxemp='A' and user_status=1";
			$dtSupervisor = $dbEx->selSql($sqlText);
			$optSupPuesto = '<select id="lsSuperv" class="txtPag">';
			foreach($dtSupervisor as $dtSup){
				$sel = "";
				if($dtE['0']['id_supervisor']==$dtSup['employee_id']){
					$sel = "selected";	
				}
				$optSupPuesto .='<option value="'.$dtSup['employee_id'].'" '.$sel.'>'.$dtSup['firstname'].' '.$dtSup['lastname'].'</option>';
			}
			$optSupPuesto .='</select>';
			

		}
		$nombreSupOld = "";
		$nombreSupNew = "";
		if($dtAp['0']['SUPERVISOR_NEW']>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtAp['0']['SUPERVISOR_NEW'];
			$dtSupervisor = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$nombreSupNew = $dtSupervisor['0']['firstname']." ".$dtSupervisor['0']['lastname'];	
			}
			
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtAp['0']['SUPERVISOR_OLD'];
			$dtSupervisor = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$nombreSupOld = $dtSupervisor['0']['firstname']." ".$dtSupervisor['0']['lastname'];
			}
		}
		
		
		$rslt = str_replace("<!--cuentaOldPuesto-->",$cuentaOldPuesto,$rslt);
		$rslt = str_replace("<!--departOldPuesto-->",$departOldPuesto,$rslt);
		$rslt = str_replace("<!--posicionOldPuesto-->",$posicionOldPuesto,$rslt);
		$rslt = str_replace("<!--cuentaNewPuesto-->",$cuentaNewPuesto,$rslt);
		$rslt = str_replace("<!--departNewPuesto-->",$departNewPuesto,$rslt);
		$rslt = str_replace("<!--posicionNewPuesto-->",$posicionNewPuesto,$rslt);
		$rslt = str_replace("<!--SupervisorNewPuesto-->",$nombreSupNew,$rslt);
		$rslt = str_replace("<!--SupervisorOldPuesto-->",$nombreSupOld,$rslt);
		$rslt = str_replace("<!--optcuentaPuesto-->", $optCuentaPuesto,$rslt);
		$rslt = str_replace("<!--optDeptoPuesto-->",$optDepartPuesto,$rslt);
		$rslt = str_replace("<!--optPosicionPuesto-->",$optPosicionPuesto,$rslt);
		$rslt = str_replace("<!--optSupervisorPuesto-->",$optSupPuesto,$rslt);
		
		//Datos de traslado
		if($dtAp['0']['ID_TPAP']==3){
			$sqlText = "select max(id_plxemp) as me from plazaxemp where status_plxemp='I' and employee_id=".$dtAp['0']['EMPLOYEE_ID']." and id_apxemp<".$_POST['id'];
			$dtT = $dbEx->selSql($sqlText);
		
			$sqlText = "select d.id_depart, d.name_depart, c.id_account, c.name_account, po.id_place, po.name_place from plazaxemp pe inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join depart_exc d on pd.id_depart=d.id_depart inner join places po on po.id_place=pd.id_place inner join account c on c.id_account=pd.id_account where pe.id_plxemp=".$dtT['0']['me'];
			$dtP = $dbEx->selSql($sqlText);
			$rslt = str_replace("<!--idUltimaPlxemp->",$dtT['0']['me'],$rslt);
			$rslt = str_replace("<!--ultimaIdDpto-->",$dtP['0']['id_depart'],$rslt);
			$rslt = str_replace("<!--ultimaNomDpto-->",$dtP['0']['name_depart'],$rslt);
			$rslt = str_replace("<!--ultimaIdCuenta-->",$dtP['0']['id_account'],$rslt);
			$rslt = str_replace("<!--ultimaNomCuenta-->",$dtP['0']['name_account'],$rslt);
			$rslt = str_replace("<!--ultimaIdPos-->",$dtP['0']['id_place'],$rslt);
			$rslt = str_replace("<!--ultimaNomPos-->",$dtP['0']['name_place'],$rslt);
		}
		
		if($dtAp['0']['ID_TPAP']==8 or $dtAp['0']['ID_TPAP']==15 ){
			$sqlText = "select pprueba_plxemp from plazaxemp where id_apxemp=".$_POST['id'];
			$dtP = $dbEx->selSql($sqlText);
			$rslt = str_replace("<!--prueba-->",$dtP['0']['pprueba_plxemp'],$rslt);
		}

		if($_SESSION['usr_rol']!='RECURSOS HUMANOS'){
			//Muestra la cuenta del supervisor
			$sqlText = "select ac.name_account as na, ac.id_account as ida from account ac inner join placexdep pd on ac.id_account = pd.id_account inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep where pe.employee_id=".$_SESSION['usr_id'];
			$dtC = $dbEx->selSql($sqlText);
			$lscuenta = '<select id="lsCuenta" class="txtPag" disabled="disabled">';
			$lscuenta .= '<option value="'.$dtC['0']['ida'].'">'.$dtC['0']['na'].'</option></select>';
			$rslt = str_replace("<!--optCuenta-->",$lscuenta,$rslt);
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$sqlText = "select * from account";
			$dtC = $dbEx->selSql($sqlText);
			$optC = '<select id="lsCuenta" class="txtPag" onChange="getDepart(this.value)">';
			foreach($dtC as $dtCu){
				if($dtC['ID_ACCOUNT']==$dtE['0']['ID_ACCOUNT']){$sel="selected";}
				else{$sel = "";}
				$optC .='<option value="'.$dtCu['ID_ACCOUNT'].'"'.$sel.'>'.$dtCu['NAME_ACCOUNT'].'</option>';
			}	
			$optC .='</select>';
			$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		}	
		//Lista Departamentos de la cuenta
			$sqlText = "select distinct (d.id_depart) as idD, name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account c on pd.id_account=c.id_account where c.id_account=".$dtE['0']['ID_ACCOUNT'];   		     
			$dtD = $dbEx->selSql($sqlText);
			$optD = '<select id="lsDpto" class="txtPag" onchange="getPosc(this.value, '.$dtC['0']['ida'].')">';
			foreach($dtD as $dtD){
				if($dtD['id_depart']==$dtE['0']['ID_DEPART']){$sel = "selected";}
		  		else{$sel="";}
				$optD .= '<option value="'.$dtD['idD'].'" '.$sel.'>'.$dtD['name_depart'].'</option>';
			}
			$optD .='</select>';
			$rslt = str_replace("<!--optDepto-->",$optD,$rslt);
		
		//Selecciona las posiciones del departamento
		$sqlText = "select id_placexdep, name_place from places pl inner join placexdep pd on pd.id_place=pl.id_place where pd.id_depart=".$dtE['0']['ID_DEPART']." and pd.id_account=".$dtE['0']['ID_ACCOUNT'];
		$dtP = $dbEx->selSql($sqlText);
		$optPl = '';
		foreach($dtP as $dtp){
			if($dtp['id_placexdep']==$dtE['0']['ID_PLACEXDEP']){$sel = "selected";}
			else{$sel="";}
			$optPl .= '<option value="'.$dtp['id_placexdep'].'" '.$sel.'>'.$dtp['name_place'].'</option>';
			}
		$rslt = str_replace("<!--optPlaza-->",$optPl,$rslt);
		//Selecciona el supervisor
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id= e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where pl.nivel_place=2 and pd.id_depart=".$dtE['0']['ID_DEPART']." and pd.id_account=".$dtE['0']['ID_ACCOUNT'];
		$dtS = $dbEx->selSql($sqlText);
		$optSup = '<select id="lsSuperv" class="txtPag">';
		foreach($dtS as $dtS){
			if($dtS['employee_id']==$dtE['0']['id_supervisor']){$sel="selected";}
			else{$sel="";}
			$optSup .='<option value="'.$dtS['employee_id'].'" '.$sel.'>'.$dtS['firstname'].'&nbsp;'.$dtS['lastname'].'</option>';	
		}
		$optSup .='</select>';
		$rslt = str_replace("<!--optSuperv-->",$optSup,$rslt);
		
		//Opciones de edicion y carga de Contrato de aviso
		
		$fechaAnterior = date('Y-m-d', strtotime($dtAp['0']['STARTDATE_AP']." - 6 month"));
		$sqlText = "select date_format(startdate_ap, '%d/%m/%Y') as f1, typesanction_ap, comment_ap from apxemp where employee_id=".$dtAp['0']['EMPLOYEE_ID']." and id_tpdisciplinary=".$dtAp['0']['ID_TPDISCIPLINARY']." and storagedate_ap between date '".$fechaAnterior."' and '".$dtAp['0']['STARTDATE_AP']."' and id_tpap=6 order by storagedate_ap";
		$dtCaviso = $dbEx->selSql($sqlText);
		$tblCaviso = '<table cellpadding="2" cellspacing="0" width="600" border="1" bordercolor="#003366" align="center" bgcolor="#FFFFFF" style="border-bottom-style:solid;">';
		//Elementos de tabla usados unicamente para la impresion de la misma tabla
		$tblCav = "";
		if($dbEx->numrows>0){
			$tblCaviso .='<tr class="showItem"><td width="15%">Fecha</td><td width="15%">Tipo de Sanci&oacute;n</td><td width="60%">Comentarios</td></tr>';
			$tblCav .='<tr><td width="15%" align="center">Fecha</td><td width="15%" align="center">Tipo de Sanci&oacute;n</td><td width="60%" align="center">Comentarios</td></tr>';
			foreach($dtCaviso as $dtCa){
				if($dtCa['typesanction_ap'] == 1){ $tpSancion = "Verbal";}
				if($dtCa['typesanction_ap'] == 2){ $tpSancion = "Escrita";}
				if($dtCa['typesanction_ap'] == 3){ $tpSancion = "Suspensi&oacute;n";}
				$tblCaviso .='<tr class="txtPag"><td>'.$dtCa['f1'].'</td><td>'.$tpSancion.'</td><td>'.$dtCa['comment_ap'].'</td></tr>';
				$tblCav .='<tr class="txtPag"><td>'.$dtCa['f1'].'</td><td>'.$tpSancion.'</td><td>'.$dtCa['comment_ap'].'</td></tr>';
				
			}
		}
		else{
			$tblCaviso .='<tr><td class="txtPag">No existen sanciones disciplinarias por '.$nombreSancion['0']['name_tpdisciplinary'].' en los &uacute;ltimos 6 meses</td></tr>';
			$tblCav .='<tr><td class="txtPag">No existen sanciones disciplinarias por '.$nombreSancion['0']['name_tpdisciplinary'].' en los &uacute;ltimos 6 meses</td></tr>';	
		}
		$tblCaviso .='</table>';
		$rslt = str_replace("<!--tblSanciones-->",$tblCaviso,$rslt);
		$rslt = str_replace("<!--tblImpSanciones-->",$tblCav,$rslt);
		
		$sqlText = "select * from type_disciplinary order by name_tpdisciplinary";
		$dtDisc = $dbEx->selSql($sqlText);
		$optTpDisc = "";
		foreach($dtDisc as $dtDi){
			$sel = "";
			if($dtDi['ID_TPDISCIPLINARY']==$dtAp['0']['ID_TPDISCIPLINARY']){
				$sel = "selected";	
			}
			$optTpDisc .='<option value="'.$dtDi['ID_TPDISCIPLINARY'].'" '.$sel.'>'.$dtDi['NAME_TPDISCIPLINARY'].'</option>';
			
		}
		$rslt = str_replace("<!--optUpdTpDisciplinary-->",$optTpDisc,$rslt);
		
		echo $rslt;
	
	break;
	
	case 'reportap':  //Genera las opciones de filtrado para nuevo reporte
		$rslt = cargaPag("../mtto/filtros_ap.php"); 
		$filtro = " where status_plxemp='A' and e.employee_id!=".$_SESSION['usr_id'];
		$filtro_ap = " where id_tpap = 1 or id_tpap=2 or id_tpap=3 or id_tpap=4 or id_tpap=5 or id_tpap=6 or id_tpap=7 or id_tpap=9 or id_tpap=13 or id_tpap=16";
		if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
			$filtro1 = ' where ';
			$id_pl = $dbEx->selSql($sqlText);
			if($id_pl['0']['name_place']=='QUALITY SUPERVISOR'){
				$filtro1 .= " pd.id_role=2 and pd.id_account!=3 ";
			}
			else{
				$filtro1 .= " e.id_supervisor=".$_SESSION['usr_id']." or e.employee_id=".$_SESSION['usr_id'];
			}
			$sqlText = "select ac.name_account, ac.id_account from account ac where ac.id_account in (select c.id_account from account c inner join placexdep pd on c.id_account = pd.id_account inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id ".$filtro1." and user_status=1)";
			
			$dtC = $dbEx->selSql($sqlText);
			$optCuenta = '<select id="lsCuenta" class="txtPag" onchange="getDepart2(this.value)">';
			$optCuenta .= '<option value="0">[TODOS]</option>';
			foreach($dtC as $dtCu){
				$optCuenta .= '<option value="'.$dtCu['id_account'].'">'.$dtCu['name_account'].'</option>';
			}
			$optCuenta .= '</select>';
			$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
			
			$filtro2 = '';
			if($id_pl['0']['name_place']=='QUALITY SUPERVISOR'){
				$filtro2 .= " where NAME_ROLE='AGENTE'";
				$filtro .= " and NAME_ROLE='AGENTE'";
			}
			else{
				$filtro2 .= " where e.id_supervisor=".$_SESSION['usr_id']." or e.employee_id=".$_SESSION['usr_id'];
				$filtro .= " and e.id_supervisor=".$_SESSION['usr_id'];
			}
			$sqlText = "select distinct(d.id_depart) as idD, name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join user_roles ur on ur.id_role=pd.id_role inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id = pe.employee_id ".$filtro2." and user_status=1";

			$dtD = $dbEx->selSql($sqlText);
			//onchange="empxdep(this.value)"
			$optD .= '<select id="lsDpto" class="txtPag"><option value="0" >[TODOS]</option>';
			foreach($dtD as $dtD){       //Muestra los departamento de la cuenta a la que pertenece el supervisor
				$optD .= '<option value="'.$dtD['idD'].'">'.$dtD['name_depart'].'</option>';
			}
			$rslt = str_replace("<!--optDepto-->",$optD,$rslt);		
			
		}//Terminan filtros si el rol del usuario es supervisor
		
		if($_SESSION['usr_rol']!='SUPERVISOR' and $_SESSION['usr_rol']!='RECURSOS HUMANOS'){
			$sqlText = "select id_account, name_account from account order by name_account";
			$dtC = $dbEx->selSql($sqlText);
			//filtros cuenta
			$selCuenta = '<select id="lsCuenta" class="txtPag" onChange="getDepartFiltros(this.value)"><option value="0">[TODOS]</option>';
			foreach($dtC as $dtC){
				$selCuenta .='<option value="'.$dtC['id_account'].'">'.$dtC['name_account'].'</option>';
			}	
			$selCuenta .='</select>';
			//filtros departamento
			$sqlText ="select * from depart_exc where status_depart = 1 order by name_depart";
			$dtD = $dbEx->selSql($sqlText);
			$optD = '<select id="lsDpto" class="txtPag">';
			$optD .= '<option value="0">[TODOS]</option>';
			foreach($dtD as $dtD){
				$optD .='<option value="'.$dtD['ID_DEPART'].'">'.$dtD['NAME_DEPART'].'</option>';	
			}
			$optD .='</select>';
		
			$rslt = str_replace("<!--optCuenta-->",$selCuenta,$rslt);
			$rslt = str_replace("<!--optDepto-->",$optD,$rslt);

			//Filtro para mostrar los empleados
			if($_SESSION['usr_rol']=='GERENTE DE AREA'){
				$filtro .="  ";
			}
			else{
				$filtro .= " and pd.id_role<".$_SESSION['usr_idrol']." or e.id_supervisor=".$_SESSION['usr_id'];
			}
		}
		
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$sqlText = "select id_account, name_account from account order by name_account";

			$dtC = $dbEx->selSql($sqlText);
			$selCuenta = '<select id="lsCuenta" class="txtPag" onChange="getDepartFiltros(this.value)"><option value="0">[TODOS]</option>';
			foreach($dtC as $dtC){
				$selCuenta .='<option value="'.$dtC['id_account'].'">'.$dtC['name_account'].'</option>';
			}	
			$selCuenta .='</select>';
			$rslt = str_replace("<!--optCuenta-->",$selCuenta,$rslt);
			
			$sqlText ="select * from depart_exc where status_depart = 1 order by name_depart";
			$dtD = $dbEx->selSql($sqlText);
			$optD = '<select id="lsDpto" class="txtPag">';
			$optD .= '<option value="0">[TODOS]</option>';
			foreach($dtD as $dtD){
				$optD .='<option value="'.$dtD['ID_DEPART'].'">'.$dtD['NAME_DEPART'].'</option>';	
			}
			$optD .='</select>';
			$rslt = str_replace("<!--optDepto-->",$optD,$rslt);
			
			$filtro .= " and e.employee_id!=".$_SESSION['usr_sup'];	
			$filtro_ap = " where 1 ";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$filtro_ap = " where 1 ";
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role ".$filtro." order by firstname";
		$dtAg = $dbEx->selSql($sqlText);
		foreach($dtAg as $dtAg){ //Muestra los empleados de supervisor
			$optAg .= '<option value="'.$dtAg['employee_id'].'">'.$dtAg['firstname'].'&nbsp;'.$dtAg['lastname'].'</option>';
		}
		$rslt = str_replace("<!--optAg-->",$optAg,$rslt);
		
		$sqlText = "select id_tpap, name_tpap from type_ap ".$filtro_ap;
		$dtAp = $dbEx->selSql($sqlText);  
		foreach($dtAp as $dtAp){  //Muestra aps para supervisores
			$optAp .= '<option value="'.$dtAp['id_tpap'].'">'.$dtAp['name_tpap'].'</option>';
		}
		$rslt = str_replace("<!--optAp-->",$optAp,$rslt);
		
		$sqlText = "select * from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1";
		
		
		echo $rslt;
	break;
	
	case 'getDepartFiltros':
		$filtro = " where 1 ";
		if($_POST['idC']!=0){
			$filtro .= " and ac.id_account=".$_POST['idC']; 	
		}
	
		$sqlText = "select distinct(d.id_depart), name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account ac on pd.id_account=ac.id_account ".$filtro." order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		$optD = '<select id="lsDpto" class="txtPag">';
		if($dbEx->numrows>0){
			$optD .='<option value="0"><br />[TODOS]</option>';
			foreach($dtD as $dtD){
					$optD .='<option value="'.$dtD['id_depart'].'">'.$dtD['name_depart'].'</option>';
			}
		}
		else{
			$optD .='La cuenta no posee departamentos';
			}
		$optD .='</select>';
		echo $optD;
	break;
	
	case 'getDepartFiltros2':
		$filtro = " where 1 ";
		if($_POST['idC']!=0){
			$filtro .= " and ac.id_account=".$_POST['idC']; 	
		}
	
		$sqlText = "select distinct(d.id_depart), name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account ac on pd.id_account=ac.id_account ".$filtro." order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		$optD = '<select id="lsDpto" class="txtPag" onchange="getPoscFiltros2(this.value,'.$_POST['idC'].')">';
		if($dbEx->numrows>0){
			$optD .='<option value="0"><br />[ALL]</option>';
			foreach($dtD as $dtD){
					$optD .='<option value="'.$dtD['id_depart'].'">'.$dtD['name_depart'].'</option>';
			}
		}
		else{
			$optD .='La cuenta no posee departamentos';
			}
		$optD .='</select>';
		echo $optD;
	break;
	
	case 'getPoscFiltros':
		$filtro = " where 1 ";
		if($_POST['idD']!=0){
			$filtro .= " and pd.id_depart=".$_POST['idD'];	
		}
		$sqlText = "select distinct(p.id_place), name_place from places p inner join placexdep pd on p.id_place=pd.id_place ".$filtro."  order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP = '<select id="lsPosc" class="txtPag">';
		$optP .= '<option value="0">[ALL]</option>';
		foreach($dtP as $dtP){
			$optP .='<option value="'.$dtP['id_place'].'">'.$dtP['name_place'].'</option>';	
		}
		$optP .='</select>';
		echo $optP;
	break;
	
	case 'getPoscFiltros2':
		$filtro = " where 1 ";
		if($_POST['idD']!=0){
			$filtro .= " and pd.id_depart=".$_POST['idD'];	
		}
		if($_POST['idC']!=0){
			$filtro .= " and pd.id_account=".$_POST['idC'];	
		}
	
		$sqlText = "select distinct(pl.id_place), name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place ".$filtro." order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP = '<select id="lsPosc" class="txtPag">';
		if($dbEx->numrows>0){
			$optP .= '<option value="0">[ALL]</option>';
			foreach($dtP as $dtP){
				$optP .='<option value="'.$dtP['id_place'].'">'.$dtP['name_place'].'</option>';	
			}
		}
		else{
			$optP .='El departamento no posee posiciones';	
		}
		$optP .='</select>';
		echo $optP;
	break;
	
	case 'loadrpt': //Carga reporte segun filtros seleccionados
		$rslt = '';
		$filtro .= " where pe.status_plxemp='A' ";
		$estado = "";
		if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
			$id_pl = $dbEx->selSql($sqlText);
			if($id_pl['0']['name_place']=='QUALITY SUPERVISOR'){
				$filtro .= " and pd.id_role=2 and pd.id_account!=3 ";
			}
			else{
				$filtro .= " and e.id_supervisor=".$_SESSION['usr_id'];
			}
		}
		/*else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" and (ap.autor_ap=".$_SESSION['usr_id']." ) ";
		}*/
		else if($_SESSION['usr_rol']!='SUPERVISOR' and $_SESSION['usr_rol']!='RECURSOS HUMANOS'){
			$filtro .= " and (ap.autor_ap=".$_SESSION['usr_id']." or pd.id_role<".$_SESSION['usr_idrol'].")";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$filtro .= " and e.employee_id!=".$_SESSION['usr_sup'];	
		}
		if($_POST['cuenta']>0){
			$filtro .= " and pd.id_account=".$_POST['cuenta'];
			}
		if($_POST['dpto']>0){
			$filtro .= " and pd.id_depart=".$_POST['dpto'];
			}
		if($_POST['idAp']>0){
			$filtro .= " and ap.id_tpap =".$_POST['idAp'];
			}
		if($_POST['idAg']>0){
			$filtro .= " and e.employee_id =".$_POST['idAg'];
			}
		if((strlen($_POST['fec_ini']))>0){
			if(strlen($_POST['fec_fin'])>0){
			   	$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
			    $fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
				$filtro .= " and ap.storagedate_ap between date '".$fec_ini."' and date '".$fec_fin."'";	
			}
		}
		if(isset($_POST['emp']) && $_POST['emp']!=''){
			$filtro .= " and (e.firstname like '%".strtoupper($_POST['emp'])."%' or e.lastname like '%".strtoupper($_POST['emp'])."%')";	
		}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			 $filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";
		}
		if($_POST['estado']>0){
			if($_POST['estado']==1){
				$estado = 1;
			}
			else if($_POST['estado']==2){
				$estado = 2;
			}
			else if($_POST['estado']==3){
				$estado = 3;
			}
		}
		$sqlText = "select distinct(ap.id_apxemp), e.employee_id, e.username, e.firstname, e.lastname, tp.id_tpap, tp.name_tpap, ap.id_apxemp, ap.id_center, date_format(ap.startdate_ap,'%d/%m/%Y') as f1, date_format(ap.storagedate_ap,'%d/%m/%Y') as f2, date_format(ap.enddate_ap,'%d/%m/%Y') as f3, ap.hours_ap, ap.comment_ap from employees e inner join apxemp ap on ap.employee_id=e.employee_id inner join type_ap tp on tp.id_tpap=ap.id_tpap inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep".$filtro." order by ap.id_apxemp desc";
		$dtAp = $dbEx->selSql($sqlText);
		$rslt .= '<table cellpadding="3" cellspacing="0" width="950" class="tblResult" align="center" bordercolor="#069">';
		if($dbEx->numrows>0){
			$rslt .= '<tr class="txtPag"><td align="right" colspan="9"><form target="_blank" action="report/xls_rptaptotal.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Exportar a excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$filtro.'"></td><td></td></tr>';
			$rslt .= '<tr class="showItem"><td width="8">#</td><td width="50">Badge</td><td width="120">Empleado</td><td width="60">Fecha de Elaboraci&oacute;n</td><td width="60">Fecha Efectiva</td><td width="60">Tipo de Acci&oacute;n de Personal</td><td width="50">Pagada</td><td width="50">N&uacute;mero de Horas</td><td width="50">Comentarios</td><td></td></tr>';
			$count = 0;
			foreach($dtAp as $dtAp){
				
				$pasa = 1; //Si pasa = 1 se escribe la AP en pantalla, pasa se resetea a 0 segun los filtros de estado 
				//Si el filtro seleccionado es para Ap aprobada
				if($estado == 1){
					$pasa = verificarAprobAp($dtAp['id_apxemp']);
				}
				else if($estado == 2){ //Ap pendientes
					$rechazadas = 0;
					$aprobadas = verificarAprobAp($dtAp['id_apxemp']);
					$sqlText = "select approved_work, approved_area, approved_hr, approved_general from apxemp where id_apxemp=".$dtAp['id_apxemp'];
					$dtEstado = $dbEx->selSql($sqlText);
					if($dtEstado['0']['approved_work']=='N' or $dtEstado['0']['approved_area']=='N' or $dtEstado['0']['approved_hr']=='N' or $dtEstado['0']['approved_general']=='N'){
						$rechazadas = 1;	
					}
					if($aprobadas==1 or $rechazadas==1){
						$pasa = 0;	
					}
				}
				else if($estado == 3){ //Ap rechazadas
					$sqlText = "select approved_work, approved_area, approved_hr, approved_general from apxemp where id_apxemp=".$dtAp['id_apxemp'];
					$dtEstado = $dbEx->selSql($sqlText);
					if(($dtEstado['0']['approved_work']=='N') or ($dtEstado['0']['approved_area']=='N') or ($dtEstado['0']['approved_hr']=='N') or ($dtEstado['0']['approved_general']=='N')){
						$pasa = 1;	
					}
					else{
						$pasa = 0;	
					}
				}
			if($pasa == 1){
				$count = $count +1;
				$pago = "";
				if($dtAp['id_tpap']==1){$pago='Pagada';}
				if($dtAp['id_tpap']==2){$pago='No Pagada';}
				/*if($dtAp['id_tpap']==7){  //Si el tipo de Ap es Incapacidad
		
				if($dtAp['id_center']==1){   //Si la incapacidad es del ISSS verifica si la incapacidad sera pagada o no
					$n = 0;
					$sqlText = "select YEAR(enddate_ap) as anio from apxemp where id_apxemp =".$dtAp['id_apxemp'];
					$a = $dbEx->selSql($sqlText);
					$anio = $a['0']['anio'];
					$sqlText = "select date_format(startdate_ap,'%d/%m/%Y') as f1, date_format(enddate_ap,'%d/%m/%Y') as f2 from apxemp where id_center=1 and employee_id=".$dtAp['employee_id']." and YEAR(enddate_ap)=".$anio." and startdate_ap<'".$dtAp['startdate_ap']."' and id_tpap=".$dtAp['id_tpap'];
					$incap = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						foreach($incap as $incap){
							$dias = restaFechas($incap['f1'],$incap['f2']) + 1;
							$n = $n + $dias;
						}
					}
					$dias_incap = restaFechas($dtAp['f1'],$dtAp['f3']) + 1;
					if($n>=3){ $pago = "No Pagada";}
					if($n==2){ $pago = "1/".$dias_incap." Pagada";}
					if($n==1){
						if($dias_incap>=2){$pago = "2/".$dias_incap." Pagada";}
						else if($dias_incap ==1){ $pago = "1/".$dias_incap." Pagada";}
					}
					if($n==0){
						if($dias_incap>=3){$pago = "3/".$dias_incap." Pagada";}
						else if($dias_incap ==2){$pago = "2/2 Pagada";}
						else if($dias_incap ==1){$pago = "1/1 Pagada";}
					}
				}
				else{
					$pago = "No Pagada";}
				}*/
				$del ="";
				if($_SESSION['usr_rol']=='GERENCIA'){
					$del = '<td><img src="images/elim.png" title="Click para eliminar" onclick="deleteApReport('.$dtAp['id_apxemp'].')"></td>';
				}
				
				$rslt .='<tr class="rowCons">
				<td class="txtPag" width="5%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['id_apxemp'].'</td>
				<td class="txtPag" width="10%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['username'].'</td>
				<td class="txtPag" width="15%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['firstname'].'&nbsp;'.$dtAp['lastname'].'</td>
				<td class="txtPag" width="10%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['f2'].'</td>
				<td class="txtPag" width="10%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['f1'].'</td>
				<td class="txtPag" width="15%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['name_tpap'].'</td>
				<td class="txtPag" width="5%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$pago.'</td>
				<td class="txtPag" width="5%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['hours_ap'].'</td>
				<td class="txtPag" width="25%" onclick="loadApxE('.$dtAp['id_apxemp'].')">'.$dtAp['comment_ap'].'</td>'.$del.'</tr>';
				
				
				}//termina pasa
			}
			$rslt .= '<tr class="txtPag"><td colspan="9">Coincidencias:&nbsp;'.$count.'</td></tr>';
		}
		else{
			
			$rslt .= '<tr><td colspan="7">No hay coincidencias para los filtros seleccionados</td></tr></table>';	}
	echo $rslt;
	break;
	
	case 'autorizarap': //Muestra ap por autorizar segun el rol del usuario

		$filtro = " where 1";
		$filtro2 = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .= " and autor_area=0 ";
			$filtro2 = " and ap.id_tpap!=15 and ap.id_tpap!=16 and e.employee_id in (select emp.employee_id from employees emp inner join plazaxemp pxe on emp.employee_id=pxe.employee_id inner join placexdep pxd on pxd.id_placexdep=pxe.id_placexdep inner join depart_exc d on d.id_depart=pxd.id_depart where pxd.id_role<=3 and d.name_depart!='CHAT' and pxe.status_plxemp='A') ";
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$filtro .= " and ap.id_tpap!=15 and ap.id_tpap!=16 and ((autor_area!=0 and approved_area!='N') or (autor_area=0 and e.employee_id in (select emp.employee_id from employees emp inner join plazaxemp pxe on emp.employee_id=pxe.employee_id inner join placexdep pxd on pxd.id_placexdep=pxe.id_placexdep inner join depart_exc d on d.id_depart=pxd.id_depart where pxd.id_role<=3 and d.name_depart='CHAT' and pxe.status_plxemp='A' )) ) and autor_work=0 and typesanction_ap!=1 and typesanction_ap!=2 ";
				
			$filtro2 = " and e.employee_id in (select emp.employee_id from employees emp inner join plazaxemp pxe on emp.employee_id=pxe.employee_id inner join placexdep pxd on pxd.id_placexdep=pxe.id_placexdep where pxd.id_role<=3)";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$filtro .= " and autor_hr=0 and (true=((autor_area=0 and ap.id_tpap in(4,5,8,10,11,12,14,16)) or ((autor_area!=0 and approved_area!='N') ) or (autor_ap in (select empleado.employee_id from employees empleado inner join plazaxemp plazaemp on empleado.employee_id=plazaemp.employee_id inner join placexdep pladep on pladep.id_placexdep=plazaemp.id_placexdep inner join depart_exc departamento on departamento.id_depart=pladep.id_depart where departamento.name_depart='CHAT'))) or 'GERENCIA'=(select urole.name_role from employees emp inner join plazaxemp pemp on pemp.employee_id=emp.employee_id inner join placexdep pdep on pdep.id_placexdep = pemp.id_placexdep inner join user_roles urole on urole.id_role=pdep.id_role where emp.employee_id=autor_ap and pemp.status_plxemp='A' )) ";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			//and autor_generalman=0
			$filtro .= " and ap.id_tpap not in(15,1,2,7)  and autor_hr!=0 and approved_hr='S' and autor_generalman=0 and typesanction_ap!=1 and typesanction_ap!=2";	
		}
		if($_SESSION['usr_rol']==""){
			$rslt = -1;
			echo $rslt;
			break;
		}
		
		$sqlText = "select ap.id_apxemp, ap.id_tpap, tp.name_tpap, e.employee_id, e.username, e.firstname, e.lastname, date_format(startdate_ap,'%d/%m/%Y') as f1, hours_ap, date_format(storagedate_ap, '%d/%m/%Y') as f2, ap.autor_ap, comment_ap from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap inner join employees e on ap.employee_id = e.employee_id inner join plazaxemp ple on ple.employee_id=e.employee_id ".$filtro." and ple.status_plxemp='A' and autor_ap not in (select ee.employee_id from employees ee inner join plazaxemp pe on ee.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on pd.id_role=ur.id_role where ur.name_role='".$_SESSION['usr_rol']."' and pe.status_plxemp='A')".$filtro2." order by ap.id_apxemp desc";
		$dtAp = $dbEx->selSql($sqlText);
		$rslt ='<table cellpadding="3" cellspacing="0" width="900" class="tblResult" align="center">';
		
		if($dbEx->numrows>0){
			$rslt .= '<tr><td colspan="8" align="center"><b>ACCIONES DE PERSONAL PENDIENTES DE AUTORIZAR<b></td></tr>';
			$rslt .= '<tr><td colspan="8">Total de Acciones de Personal: '.$dbEx->numrows.'</td></tr>';
			$rslt .= '<tr class="showItem"><td width="5%">AP#</td><td width="8%">BADGE</td><td width="25%">EMPLEADO</td><td width="10%">FECHA DE ELABORACION</td><td width="10%">FECHA EFECTIVA</td><td width="15%">TIPO DE ACCION DE PERSONAL</td><td width="5%">NUMERO DE HORAS</td><td width="20%">COMENTARIOS</td><td width="5%"></td></tr>';
			foreach($dtAp as $dtAp){

					$rslt .= '<tr class="rowCons"><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['id_apxemp'].'</td><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['username'].'</td><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['firstname'].'&nbsp;'.$dtAp['lastname'].'</td><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['f2'].'</td><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['f1'].'</td><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['name_tpap'].'</td><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['hours_ap'].'</td><td onclick="loadAutor('.$dtAp['id_apxemp'].')">'.$dtAp['comment_ap'].'</td><td><img src="images/elim.png" title="Click para eliminar" onclick="deleteAp('.$dtAp['id_apxemp'].')"></td></tr>';
			}
		}
		else{
			$rslt .= '<tr><td class="txtForm"><br><br><br>No posee acciones de personal pendientes de autorizaci&oacute;n<br><br><br</td></tr>';
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'ResultAutorAp':
		$filtro = "";
		$filtro2 = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro = " autor_area ";
			$filtro2 = " approved_area ";
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			$filtro = " autor_work ";	
			$filtro2 = " approved_work ";
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$filtro = " autor_hr ";
			$filtro2 = " approved_hr ";
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			$filtro = " autor_generalman ";	
			$filtro2 = " approved_general ";
		}
		$sqlText = "update apxemp set ".$filtro."=".$_SESSION['usr_id'].", ".$filtro2."='".$_POST['accion']."' where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		
		$result = verificarAprobAp($_POST['id']);
		
		$sqlText = "select * from apxemp where id_apxemp=".$_POST['id'];
		$dtAp = $dbEx->selSql($sqlText);
		if($dtAp['0']['ID_TPAP']>=9 and $dtAp['0']['ID_TPAP']<15){
			if($result ==1){
				$sqlText = "update employees set user_status=0 where employee_id =".$dtAp['0']['EMPLOYEE_ID'];
				$dbEx->updSql($sqlText);	
			}	
		}
		
	echo $_POST['id']; 
	break;
	
	case 'ResultRechazAp':
		$update = "";
		$sqlText = "select autor_ap from apxemp where id_apxemp=".$_POST['id'];
		$dtAutor = $dbEx->selSql($sqlText);
		
		$sqlText = "select name_role from user_roles ur inner join placexdep pd on pd.id_role=ur.id_role inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id where e.employee_id=".$dtAutor['0']['autor_ap'];
		$dtRolAutor = $dbEx->selSql($sqlText);
		
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			if($dtRolAutor['0']['name_role']=='WORKFORCE'){
				$update = " autor_area=".$_SESSION['usr_id'].", approved_area='N', autor_hr=0, approved_hr='0', autor_generalman=0, approved_general='0'";	
			}
			if($dtRolAutor['0']['name_role']=='RECURSOS HUMANOS'){
				$update = " autor_area=".$_SESSION['usr_id'].", approved_area='N', autor_work=0, approved_work='0', autor_generalman=0, approved_general='0'";	
			}
			if($dtRolAutor['0']['name_role']=='GERENCIA'){
				$update = " autor_area=".$_SESSION['usr_id'].", approved_area='N', autor_hr=0, approved_hr='0', autor_work=0, approved_work='0'";	
			}
			else{
				$update = " autor_area=".$_SESSION['usr_id'].", approved_area='N', autor_work=0, approved_work='0', autor_hr=0, approved_hr='0', autor_generalman=0, approved_general='0'";	
			}
		}
		if($_SESSION['usr_rol']=='WORKFORCE'){
			if($dtRolAutor['0']['name_role']=='GERENTE DE AREA'){
				$update = " autor_work=".$_SESSION['usr_id'].", approved_work='N', autor_hr=0, approved_hr='0', autor_generalman=0, approved_general='0'";	
			}
			if($dtRolAutor['0']['name_role']=='RECURSOS HUMANOS'){
				$update = " autor_work=".$_SESSION['usr_id'].", approved_work='N', autor_area=0, approved_area='0', autor_generalman=0, approved_general='0'";	
			}
			if($dtRolAutor['0']['name_role']=='GERENCIA'){
				$update = " autor_work=".$_SESSION['usr_id'].", approved_work='N', autor_hr=0, approved_hr='0', autor_area=0, approved_area='0'";	
			}
			else{
				$update = " autor_work=".$_SESSION['usr_id'].", approved_work='N', autor_area=0, approved_area='0', autor_hr=0, approved_hr='0', autor_generalman=0, approved_general='0'";	
			}
		}
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			if($dtRolAutor['0']['name_role']=='GERENTE DE AREA'){
				$update = " autor_hr=".$_SESSION['usr_id'].", approved_hr='N', autor_work=0, approved_work='0', autor_generalman=0, approved_general='0'";	
			}
			else if($dtRolAutor['0']['name_role']=='WORKFORCE'){
				$update = " autor_hr=".$_SESSION['usr_id'].", approved_hr='N', autor_area=0, approved_area='0', autor_generalman=0, approved_general='0'";	
			}
			else if($dtRolAutor['0']['name_role']=='GERENCIA'){
				$update = " autor_hr=".$_SESSION['usr_id'].", approved_hr='N', autor_work=0, approved_work='0', autor_area=0, approved_area='0'";	
			}
			else{
				$update = " autor_hr=".$_SESSION['usr_id'].", approved_hr='N', autor_work=0, approved_work='0', autor_area=0, approved_area='0', autor_generalman=0, approved_general='0'";	
			}
		}
		if($_SESSION['usr_rol']=='GERENCIA'){
			if($dtRolAutor['0']['name_role']=='GERENTE DE AREA'){
				$update = " autor_generalman=".$_SESSION['usr_id'].", approved_general='N', autor_work=0, approved_work='0', autor_hr=0, approved_hr='0'";	
			}
			else if($dtRolAutor['0']['name_role']=='WORKFORCE'){
				$update = " autor_generalman=".$_SESSION['usr_id'].", approved_general='N', autor_area=0, approved_area='0', autor_hr=0, approved_hr='0'";	
			}
			else if($dtRolAutor['0']['name_role']=='RECURSOS HUMANOS'){
				$update = " autor_generalman=".$_SESSION['usr_id'].", approved_general='N', autor_area=0, approved_area='0', autor_work=0, approved_work='0'";	
			}
			else{
				$update = " autor_generalman=".$_SESSION['usr_id'].", approved_general='N', autor_work=0, approved_work='0', autor_area=0, approved_area='0', autor_hr=0, approved_hr='0'";	
			}
		}
	 	$sqlText = "update apxemp set ".$update.", rejected_comments='".$_POST['comment']."' where id_apxemp=".$_POST['id'];
		$dbEx->updSql($sqlText);
		echo $_POST['id'];
		
	break;
	
	case 'loadApRechazada':
		$rslt = cargaPag("../mtto/ApRechazada.php");
		
		$sqlText = "select ap.id_apxemp, ap.id_tpap, name_tpap, ap.employee_id, firstname, lastname, username, id_center, date_format(startdate_ap, '%d/%m/%Y') as f1, date_format(enddate_ap,'%d/%m/%Y') as f2, hours_ap, date_format(storagedate_ap,'%d/%m/%Y') as f3, autor_ap, autor_work, approved_work, autor_area, approved_area,autor_hr, approved_hr, approved_general, autor_generalman , id_tpdisciplinary, typesanction_ap, typeincap_ap, comment_ap, rejected_comments, name_account, name_depart, name_place from apxemp ap inner join type_ap tp on tp.id_tpap=ap.id_tpap inner join employees e on e.employee_id=ap.employee_id inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart inner join account c on c.id_account=pd.id_account inner join places pl on pl.id_place=pd.id_place where pe.status_plxemp='A' and ap.id_apxemp = ".$_POST['id'];
		$dtE = $dbEx->selSql($sqlText);
	
		$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['0']['autor_ap'];
		$dtAutor = $dbEx->selSql($sqlText);
		
		if($dtE['0']['autor_work']>0 and $dtE['0']['approved_work']=='N'){
			$filtro = $dtE['0']['autor_work'];	
		}
		if($dtE['0']['autor_area']>0 and $dtE['0']['approved_area']=='N'){
			$filtro = $dtE['0']['autor_area'];
		}
		if($dtE['0']['autor_hr']>0 and $dtE['0']['approved_hr']=='N'){
			$filtro = $dtE['0']['autor_hr'];
		}
		if($dtE['0']['autor_generalman']>0 and $dtE['0']['approved_general']=='N'){
			$filtro = $dtE['0']['autor_generalman'];
		}
		$sqlText = "select e.employee_id, firstname, lastname, name_place from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where e.employee_id=".$filtro." and status_plxemp='A'";
		$dtAutorNegar = $dbEx->selSql($sqlText);
		
		if($dtE['0']['autor_ap']==$_SESSION['usr_id']){
		   	$comentario = "Nota: <i>Realice las actualizaciones necesarias para que esta acci&oacute;n de personal pueda ser tomada en cuenta nuevamente<i>";
			$btnComentario ='<br><input type="button" class="btn" onClick="loadApxE('.$_POST['id'].')" value="Actualizar acci&oacute;n de personal">';
		}
		else{
			$comentario = 'Nota:<i> La acci&oacute;n de personal ha sido enviada nuevamente a su creador para que realice las modificaciones necesarias.</i>';
			$btnComentario ="";	
		}
		$print = "";
		if($_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			$print = '<a href="mtto/impRejectAp.php?idAp=<!--id_apxemp-->&badge=<!--badge-->&nombre=<!--nombre-->&apellido=<!--apellido-->&cta=<!--cuenta-->&dpto=<!--departamento-->&posicion=<!--posicion-->&nametpap=<!--name_tpap-->&stg=<!--f_stg-->&ini=<!--f_ini-->&autor=<!--autor_ap-->&autorRec=<!--autor_negar-->&cargo=<!--cargo_negar-->&comment=<!--coment_rechaz-->&commentAccion=<!--txtComentAccion-->" target="_blanck"><img src="images/Print.png" border="0" width="50" style="cursor:pointer;" title="Pulse para imprimir" align="absmiddle" /></a>';	
		}

		$rslt = str_replace("<!--id_tpap-->",$dtE['0']['id_tpap'],$rslt);
		$rslt = str_replace("<!--name_tpap-->",$dtE['0']['name_tpap'],$rslt);
		$rslt = str_replace("<!--id_apxemp-->",$dtE['0']['id_apxemp'],$rslt);
		$rslt = str_replace("<!--id_emp-->",$dtE['0']['employee_id'],$rslt);
		$rslt = str_replace("<!--nombre-->",$dtE['0']['firstname'],$rslt);
		$rslt = str_replace("<!--apellido-->",$dtE['0']['lastname'],$rslt);
		$rslt = str_replace("<!--badge-->",$dtE['0']['username'],$rslt);
		$rslt = str_replace("<!--id_center-->",$dtE['0']['id_center'],$rslt);
		$rslt = str_replace("<!--f_ini-->",$dtE['0']['f1'],$rslt);
		$rslt = str_replace("<!--f_fin-->",$dtE['0']['f2'],$rslt);
		$rslt = str_replace("<!--f_stg-->",$dtE['0']['f3'],$rslt);
		$rslt = str_replace("<!--horas-->",$dtE['0']['hours_ap'],$rslt);
		$rslt = str_replace("<!--idtpdisc-->",$dtE['0']['id_tpdisciplinary'],$rslt);
		$rslt = str_replace("<!--tpsanc-->",$dtE['0']['typesanction_ap'],$rslt);
		$rslt = str_replace("<!--tpincap-->",$dtE['0']['typeincap_ap'],$rslt);
		$rslt = str_replace("<!--comentario-->",$dtE['0']['comment_ap'],$rslt);
		$rslt = str_replace("<!--coment_rechaz-->",$dtE['0']['rejected_comments'],$rslt);
		$rslt = str_replace("<!--cuenta-->",$dtE['0']['name_account'],$rslt);
		$rslt = str_replace("<!--departamento-->",$dtE['0']['name_depart'],$rslt);
		$rslt = str_replace("<!--posicion-->",$dtE['0']['name_place'],$rslt);
		$rslt = str_replace("<!--autor_ap-->",$dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'],$rslt);
		$rslt = str_replace("<!--autor_negar-->",$dtAutorNegar['0']['firstname']." ".$dtAutorNegar['0']['lastname'],$rslt);
		$rslt = str_replace("<!--cargo_negar-->",$dtAutorNegar['0']['name_place'],$rslt);
		$rslt = str_replace("<!--txtComentAccion-->",$comentario,$rslt);
		$rslt = str_replace("<!--btnAcccion-->",$btnComentario,$rslt);
		$rslt = str_replace("<!--print-->",$print,$rslt);
		
		echo $rslt;
		
	break; 
	
	
	case 'reporteApRechazada':
		$sqlText = "select ap.id_apxemp, ap.id_tpap, e.employee_id, date_format(startdate_ap, '%d/%m/%Y') as f1, date_format(enddate_ap,'%d/%m/%Y') as f2, hours_ap, date_format(storagedate_ap,'%d/%m/%Y') as f3, name_tpap, username, firstname, lastname, rejected_comments from apxemp ap inner join type_ap tp on ap.id_tpap=tp.id_tpap inner join employees e on e.employee_id=ap.employee_id where autor_ap=".$_SESSION['usr_id']." and (approved_work='N' or approved_area='N' or approved_hr='N' or approved_general='N') order by ap.id_apxemp";
		$dtAp = $dbEx->selSql($sqlText);
		$rslt = '<table class="tblResult" align="center" width="825">';
		if($dbEx->numrows>0){
			$rslt .='<tr class="showItem" ><td colspan="5" align="center"><b>LISTADO DE ACCIONES DE PERSONAL RECHAZADAS<b></td></tr>';
			$rslt .='<tr bgcolor="#FFFFFF">
			<td width="10%">N&deg; de AP</td>
			<td width="25%">TIPO DE AP</td>
			<td width="10%">BAGDE</td>
			<td width="40%">NOMBRE DE EMPLEADO</td>
			<td width="15%">FECHA DE REGISTRO</td>
			</tr>';
			foreach($dtAp as $dtA){
				$rslt .='<tr class="rowCons"><td onclick="loadApRechazada('.$dtA['id_apxemp'].')">'.$dtA['id_apxemp'].'</td><td onclick="loadApRechazada('.$dtA['id_apxemp'].')">'.$dtA['name_tpap'].'</td><td onclick="loadApRechazada('.$dtA['id_apxemp'].')">'.$dtA['username'].'</td><td onclick="loadApRechazada('.$dtA['id_apxemp'].')">'.$dtA['firstname'].' '.$dtA['lastname'].'</td><td onclick="loadApRechazada('.$dtA['id_apxemp'].')">'.$dtA['f3'].'</td></tr>';
			}
		}
		else{
			$rslt .='<tr><td>No posee acciones de personal rechazadas</td></tr>';
		}
		$rslt .= '</table>';
		echo $rslt;
	break;
	
	case 'mis_ap':
		$sqlText = "select username, firstname, lastname from employees where employee_id=".$_SESSION['usr_id'];
		$dtE = $dbEx->selSql($sqlText);
		$sqlText = "select ap.id_apxemp, tp.id_tpap, tp.name_tpap, date_format(ap.startdate_ap,'%d/%m/%Y') as f1, date_format(ap.storagedate_ap,'%d/%m/%Y') as f2, date_format(ap.enddate_ap,'%d/%m/%Y') as f4, ap.comment_ap, autor_ap from apxemp ap inner join type_ap tp on tp.id_tpap=ap.id_tpap where employee_id=".$_SESSION['usr_id']." order by ap.id_apxemp desc";
		$dtAp = $dbEx->selSql($sqlText);
		$rslt .= '<br><table cellpadding="3" cellspacing="0" width="775" class="tblResult" align="center" bordercolor="#069">';
		if($dbEx->numrows>0){
			$rslt .= '<tr class="txtPag"><td colspan="7">Acciones de personal registradas al empleado '.$dtE['0']['firstname']."&nbsp;".$dtE['0']['lastname'].'</td></tr>';
			$rslt .= '<tr class="txtPag"><td colspan="7">Total de acciones de personal:&nbsp;'.$dbEx->numrows.'</td></tr>';
			$rslt .= '<tr class="showItem"><td width="25">Ap#</td><td>Fecha de Elaboraci&oacute;n</td><td>Fecha Efectiva</td><td>Tipo de Acci&oacute;n de Personal</td><td>Creador de la AP</td><td>Comentarios</td></tr>';
			foreach($dtAp as $dtAp){
				$sqlText = "select firstname, lastname from employees where employee_id=".$dtAp['autor_ap'];
				$dtAutor = $dbEx->selSql($sqlText);
				$rslt .= '<tr class="rowCons"><td>'.$dtAp['id_apxemp'].'</td><td>'.$dtAp['f2'].'</td><td>'.$dtAp['f1'].'</td><td>'.$dtAp['name_tpap'].'</td><td>'.$dtAutor['0']['firstname']."&nbsp;".$dtAutor['0']['lastname'].'</td><td>'.$dtAp['comment_ap'].'</td></tr>';
			}
		}
		else {
			$rslt .= '<tr><td>No posee acciones de personal</td></tr>';	
		}
		$rslt .= '</table><br><br>';
		echo $rslt;
	break;
	
	case 'cambiosWork':
		$sqlText =  "select e.employee_id, e.id_supervisor, username, firstname, lastname, d.name_depart, pl.name_place, c.name_account from employees e inner join plazaxemp pe on pe.employee_id = e.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join account c on c.id_account = pd.id_account inner join depart_exc d on d.id_depart=pd.id_depart inner join places pl on pl.id_place=pd.id_place inner join user_roles ur on ur.id_role = pd.id_role where ur.name_role='AGENTE' and e.user_status='1' and pe.status_plxemp = 'A' order by firstname";
		$dtAgents = $dbEx->selSql($sqlText);
		$rslt = "";
		$rslt .='<table class="backTablaMain" cellpadding="4" cellspacing="2">';
		$rslt .='<tr class="showItem"><td colspan="5" align="center"><b>LISTADO DE AGENTES<b></td></tr>';
		$rslt .='<tr class="showItem"><td>BADGE</td><td>EMPLEADO</td><td>DEPARTAMENTO</td><td>POSICION</td><td>SUPERVISOR</td></tr>';
		foreach($dtAgents as $dtA){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtA['id_supervisor'];
			$dtSup = $dbEx->selSql($sqlText);
			$nombreSup ="";
			if($dbEx->numrows>0){
				$nombreSup = $dtSup['0']['firstname'].' '.$dtSup['0']['lastname'];	
			}
			$rslt .='<tr class="rowCons" onclick="EditSuperv('.$dtA['employee_id'].')" style="cursor:pointer" title="Haga click para cambiar departamento o supervisor"><td>'.$dtA['username'].'</td><td>'.$dtA['firstname'].' '.$dtA['lastname'].'</td><td>'.$dtA['name_depart'].'</td><td>'.$dtA['name_place'].'</td><td>'.$nombreSup.'</td></tr>';
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	//Funcion para Workforce, hacer traslado sin generar AP
	case 'EditSuperv':
		$rslt = cargaPag("../mtto/EditSuperv.php");
		
		$sqlText = "select e.EMPLOYEE_ID, id_supervisor, USERNAME, FIRSTNAME, LASTNAME, pe.id_plxemp, pd.id_placexdep, d.ID_DEPART, NAME_DEPART, cc.ID_ACCOUNT, NAME_ACCOUNT, pc.ID_PLACE, NAME_PLACE from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep inner join depart_exc d on pd.ID_DEPART = d.ID_DEPART inner join places pc on pc.id_place = pd.id_place inner join account cc on pd.id_account=cc.id_account where e.EMPLOYEE_ID=".$_POST['idE']." and pe.status_plxemp = 'A'";
		$dtE = $dbEx -> selSql($sqlText);
		$rslt = str_replace("<!--idemp-->", $dtE['0']['EMPLOYEE_ID'], $rslt);
		$rslt = str_replace("<!--username-->", $dtE['0']['USERNAME'], $rslt);
		$rslt = str_replace("<!--nombre-->", $dtE['0']['FIRSTNAME'], $rslt);
		$rslt = str_replace("<!--apellido-->", $dtE['0']['LASTNAME'], $rslt);
		$rslt = str_replace("<!--id_depto-->", $dtE['0']['ID_DEPART'], $rslt);
		$rslt = str_replace("<!--depto-->", $dtE['0']['NAME_DEPART'],$rslt);
		$rslt = str_replace("<!--idcuenta-->",$dtE['0']['ID_ACCOUNT'],$rslt);
		$rslt = str_replace("<!--cuenta-->",$dtE['0']['NAME_ACCOUNT'],$rslt);
		$rslt = str_replace("<!--idplaza-->",$dtE['0']['ID_PLACE'],$rslt);
		$rslt = str_replace("<!--plaza-->", $dtE['0']['NAME_PLACE'],$rslt);
		$rslt = str_replace("<!--idplxemp-->",$dtE['0']['id_plxemp'],$rslt);
		
		
		$sqlText = "select * from account where id_typeacc=2 order by name_account";
			$dtC = $dbEx->selSql($sqlText);
			$optC = '<select id="lsCuenta" class="txtPag" onChange="getDepart(this.value)">';
			foreach($dtC as $dtCu){
				$sel = "";
				if($dtCu['ID_ACCOUNT']==$dtE['0']['ID_ACCOUNT']){
					$sel = "selected";
					}
				$optC .='<option value="'.$dtCu['ID_ACCOUNT'].'"'.$sel.'>'.$dtCu['NAME_ACCOUNT'].'</option>';
			}	
			$optC .='</select>';
			$rslt = str_replace("<!--optcuenta-->",$optC,$rslt);
			
		$sqlText = "select distinct(d.id_depart), d.name_depart from depart_exc d inner join placexdep pd on pd.id_depart=d.id_depart inner join account c on c.id_account=pd.id_account where c.id_typeacc=2 and c.id_account=".$dtE['0']['ID_ACCOUNT']." order by name_depart ";
			$dtDep = $dbEx->selSql($sqlText);
			$optD = "";
			$optD .='<select id="lsDpto" class="txtPag" onChange="getPosc(this.value,'.$dtE['0']['ID_ACCOUNT'].')">';
			foreach($dtDep as $dtD){
				$sel = "";
				if($dtD['id_depart']==$dtE['0']['ID_DEPART']){
					$sel = "selected";
					}	
				$optD .= '<option value="'.$dtD['id_depart'].'" '.$sel.' >'.$dtD['name_depart'].'</option>';
			}
			$optD .='</select>';			
			$rslt = str_replace("<!--optDepto-->",$optD,$rslt);
			
			$sqlText = "select distinct(pd.id_placexdep), name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place where pd.id_depart=".$dtE['0']['ID_DEPART']." and pd.id_account=".$dtE['0']['ID_ACCOUNT']." order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP = '<select id="lsPosc" class="txtPag">';
			foreach($dtP as $dtPos){
				$sel = "";
				if($dtPos['id_placexdep']==$dtE['0']['id_placexdep']){
					$sel = "selected";
				}
				$optP .='<option value="'.$dtPos['id_placexdep'].'"'.$sel.'>'.$dtPos['name_place'].'</option>';	
			}
		$optP .= '</select>';
		$rslt = str_replace("<!--optPosicion-->",$optP,$rslt);
			
			$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id= e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where pl.nivel_place=2 and pd.id_role=3 and user_status=1";
			$dtS = $dbEx->selSql($sqlText);
			$optSup = '<select id="lsSuperv" class="txtPag")>';
			foreach($dtS as $dtSup){
				if($dtSup['employee_id']==$dtE['0']['id_supervisor']){$sel="selected";}
				else{$sel="";}
				$optSup .='<option value="'.$dtSup['employee_id'].'" '.$sel.'>'.$dtSup['firstname'].'&nbsp;'.$dtSup['lastname'].'</option>';	
			}
			$optSup .='</select>';
			$rslt = str_replace("<!--optSuperv-->",$optSup,$rslt);
			
			echo $rslt; 
	break;
	
	case 'sv_EditSup':
		$sqlText = "update plazaxemp set id_placexdep=".$_POST['plaza']." where id_plxemp=".$_POST['idPxe']." and employee_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);
		$sqlText = "update employees set id_supervisor=".$_POST['superv']." where employee_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);
		echo $_POST['idE'];
	break;
	
	case 'loadEditSup':
		$sqlText = "select e.EMPLOYEE_ID, USERNAME, FIRSTNAME, LASTNAME, id_supervisor, pe.id_plxemp, d.ID_DEPART, NAME_DEPART, cc.ID_ACCOUNT, NAME_ACCOUNT, pc.ID_PLACE, NAME_PLACE from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep = pd.id_placexdep inner join depart_exc d on pd.ID_DEPART = d.ID_DEPART inner join places pc on pc.id_place = pd.id_place inner join account cc on pd.id_account=cc.id_account where e.EMPLOYEE_ID=".$_POST['idE']." and pe.status_plxemp = 'A'";
		$dtE = $dbEx->selSql($sqlText);
		
		$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['0']['id_supervisor'];
		$dtS = $dbEx->selSql($sqlText);
		
		$rslt ="";
		$rslt .='<br><br><table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">';
		$rslt .= '<tr><th colspan="4" class="showItem"><u>DATOS DE EMPLEADO</u></th></tr>';
		$rslt .= '<tr><td align="right" class="txtPag">Badge de Empleado:&nbsp; </td><td class="txtPag" colspan="3">'.$dtE['0']['USERNAME'].'</td></tr>';
		$rslt .= '<tr><td align="right" class="txtPag">Nombre de empleado:&nbsp; </td><td class="txtPag" colspan="3">'.$dtE['0']['FIRSTNAME'].', '.$dtE['0']['LASTNAME'].'</td></tr>';
 		$rslt .= '<tr><td align="right" class="txtPag">Cuenta:&nbsp;</td><td class="txtPag" colspan="3">'.$dtE['0']['NAME_ACCOUNT'].'</td></tr>';
		$rslt .= '<tr><td align="right" class="txtPag">Departamento:&nbsp;</td><td class="txtPag" colspan="3">'.$dtE['0']['NAME_DEPART'].'</td></tr>';
		$rslt .= '<tr><td align="right" class="txtPag">Posici&oacute;n:&nbsp;</td><td class="txtPag" colspan="3">'.$dtE['0']['NAME_PLACE'].'</td></tr>';
		$rslt .='<tr><td align="right" class="txtPag">Supervisor: </td><td class="txtPag" colspan="3">'.$dtS['0']['firstname'].' '.$dtS['0']['lastname'].'</td></tr>';
		$rslt .='</table><br><br><br>';
		echo $rslt;
	break;
	
	case 'deleteAp':
		$sqlText = "select id_tpap, employee_id from apxemp where id_apxemp=".$_POST['id'];
		$dtAp = $dbEx->selSql($sqlText);
		if($dtAp['0']['id_tpap']==3 or $dtAp['0']['id_tpap']==4 or $dtAp['0']['id_tpap']==8 or $dtAp['0']['id_tpap']==15){
			$rslt = "1";
		}
		else{
			$sqlText = "delete from apxemp where id_apxemp=".$_POST['id'];	
			$dbEx->updSql($sqlText);
			$rslt = "2";
		}
		echo $rslt;
	break;
	
}
?>
