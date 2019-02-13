<?php
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");

$dbEx = new DBX;
$oFec = new OFECHA;
  function cargaPag($urlToLoad){ //funcion para cargar una pagina
    $pagLoad = '';
    $fp=fopen($urlToLoad,"r") or die("Erro al abrir el fichero");
    $pagLoad = fread($fp,30000);
    return $pagLoad;
  }
  
  function getPlazaXEmp($idEmpleado){ //funcion para obtener el nombre de plaza activa del empleado
    $dbEx = new DBX;
	$plaza = "";
	try{
    $sqlText = "select NAME_PLACE from plazaxemp pe inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
		"inner join places pl on pl.id_place=pd.id_place ".
		"where pe.employee_id = ".$idEmpleado." and pe.status_plxemp='A'";
			
	$dtPl = $dbEx->selSql($sqlText);
	$plaza = $dtPl['0']['NAME_PLACE'];
	}
	catch (Exception $e){
        $plaza = "";
	}
	
	return $plaza;

  }
  
  function getDocList($idEmpleado){ //Funcion para obtener el listado de documentos del empleado
    $dbEx = new DBX;
    $sqlText = "select EMP_ATTACH_ID, EMP_ATTACH_NAME from empl_attachments where EMPLOYEE_ID=".$idEmpleado;
	$dtAttach = $dbEx->selSql($sqlText);
	$AttList = '';
	foreach($dtAttach as $dtA){
		$AttList .='<input type="image" src="images/trash.png" width="15" align="absmiddle" alt="Eliminar archivo" onclick="deleteFile('.$dtA['EMP_ATTACH_ID'].','.$idEmpleado.')" style="cursor:pointer;" title="Eliminar archivo">'.
				'   <a href="mtto/archivos/'.$dtA['EMP_ATTACH_NAME'].'" target="_blank">'.$dtA['EMP_ATTACH_NAME'].'</a></br>';
	}
	
	return $AttList;
  }
  
  function getPhoto($idEmpleado){ //Obtener la foto de perfil del empleado
    $dbEx = new DBX;
	$sqlText = "select foto from employees where employee_id = ".$idEmpleado;
	$dtF = $dbEx->selSql($sqlText);
	if(strlen($dtF['0']['foto']) >0){
		$foto .= 'mtto/fotos/'.$dtF['0']['foto'];
	}else{
		$foto .= 'images/photo_icon.png';
	}
	$foto = '<input type="image" src="'.$foto.'" width="80" alt="Foto de empleado" stype="cursor:pointer" title="Actualizar foto de perfil" align="absmiddle" onclick="update_photo('.$idEmpleado.')" />';
	
    return $foto;
  }
 
 switch($_POST['Do']){
 	case 'getSession':
  		echo $_SESSION["usr_id"];
  	break;

	case 'newEmp': //Muestra formulario para ingresar nuevos empleados
		$rslt = cargaPag("../mtto/frm_emp.php");
		$sqlText = "select * from account where account_status='A' order by NAME_ACCOUNT";
		$dtC = $dbEx->selSql($sqlText);
		$optC = "";
		$optC .='<option value="0">Seleccione una cuenta</option>';
		foreach($dtC as $dtC){
				$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}
		//Lista de estados
		$sqlText = "select * from employee_status order by status_name";
		$dtSt = $dbEx->selSql($sqlText);
		$optSt = "";
		foreach($dtSt as $dtSt){
			$optSt .= '<option value = "'.$dtSt['STATUS_ID'].'">'.$dtSt['STATUS_NAME'].'</option>';
  		}
  		
		//Lista de tipos de plaza
		$sqlText = "select * from job_type";
		$dtJt = $dbEx->selSql($sqlText);
	  	$optJt = "";
	  	foreach($dtJt as $dtJt){
			$optJt .='<option value = "'.$dtJt['JOB_TYPE_ID'].'">'.$dtJt['JOB_TYPE_NAME'].'</option>';
		}
		
		$fec_actual = date("d/m/Y");
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--fec_actual-->",$fec_actual,$rslt);
		$rslt = str_replace("<!--optStatus-->",$optSt,$rslt);
		$rslt = str_replace("<!--optJobType-->",$optJt,$rslt);
		
		$sqlText = "select max(substring(username,1,4)) as badge from employees where substring(username,1,4) between '0000' and '9999'";
		$dtBadge = $dbEx->selSql($sqlText);
		$carnet = (int)$dtBadge['0']['badge'];
		$carnet = $carnet +1;
		$diferencia = 4 - strlen($carnet);
		$numeroCeros;
		for($i=0; $i<$diferencia; $i++){
			$numeroCeros .= 0;
		}
		$numeroCeros .= $carnet;
		
		$rslt = str_replace("<!--badge-->",$numeroCeros,$rslt);

		//Listado de paises
		$sqlText = "select geography_code, geography_name ".
				"from geographies ".
				"where geography_type = 'COUNTRY' ".
				"and ifnull(end_date,sysdate() + 1) > sysdate() ".
				"order by geography_name";

		$dtCountry = $dbEx->selSql($sqlText);
		$optCountry = "";
		foreach ($dtCountry as $dtC) {
			$optCountry .='<option value="'.$dtC['geography_code'].'">'.$dtC['geography_name'].'</option>';
		}
		$rslt = str_replace("<!--optCountry-->",$optCountry,$rslt);
		
		echo $rslt;
	break;
	
	case 'getDepart': //Obtiene los departamentos segun Cuenta
		$sqlText = "select distinct(d.id_depart), name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account ac on pd.id_account=ac.id_account where ac.id_account=".$_POST['idC']." and account_status='A' order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		$optD = '<select id="lsDepart" onChange="getPosc(this.value,'.$_POST['idC'].')" class="txtPag">';
		if($dbEx->numrows>0){
			$optD .='<option value="0"><br />Seleccione un departamento</option>';
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
	
	case 'getDepartFiltros':
		$filtro = " where 1 ";
		if($_POST['idC']!=0){
			$filtro .= " and ac.id_account=".$_POST['idC']; 	
		}
	
		$sqlText = "select distinct(d.id_depart), name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account ac on pd.id_account=ac.id_account ".$filtro." order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		$optD = '<select id="lsDepart" onChange="getPoscFiltros2(this.value,'.$_POST['idC'].')" class="txtPag">';
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
	
	case 'getPoscFiltros':
		$filtro = " where 1 ";
		if($_POST['idD']!=0){
			$filtro .= " and pd.id_depart=".$_POST['idD'];	
		}
		$sqlText = "select distinct(p.id_place), name_place from places p inner join placexdep pd on p.id_place=pd.id_place ".$filtro."  order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP = '<select id="lsPosc" class="txtPag">';
		$optP .= '<option value="0">[TODOS]</option>';
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
			$optP .= '<option value="0">[TODOS]</option>';
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
	
	case 'getSuperv': //Obtiene los supervisores o managers de los empleados 
		$sqlText = "select id_account, id_depart, id_place, id_role from placexdep where id_placexdep=".$_POST['idP'];
		$dtC = $dbEx->selSql($sqlText);
		$sqlText = "select id_role, nivel_place from placexdep pd inner join places pl on pd.id_place=pl.id_place where id_placexdep=".$_POST['idP'];
		$dtS = $dbEx->selSql($sqlText);
		if($dtS['0']['id_role']>=3){
			if($dtS['0']['nivel_place']==1){
				$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where id_account=".$dtC['0']['id_account']."  and id_depart=".$dtC['0']['id_depart']." and pd.id_role>=".$dtC['0']['id_role']." and pl.nivel_place=2 and user_status=1 order by firstname";
				}
			else if($dtS['0']['nivel_place']==2){
			$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where pd.id_role>=".$dtC['0']['id_role']." and pl.nivel_place=2 and user_status=1 order by firstname";
			}
			
			$optSup = '<select id="lsSuperv" class="txtPag"><option value="0">Seleccione un supervisor</option>';
		}
		else if(($dtS['0']['id_role']==1) and ($dtS['0']['nivel_place']==1) and $dtC['0']['id_account']==3){
			$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where id_role>3 and nivel_place=2 and user_status=1 order by firstname";
			$optSup = '<select id="lsSuperv" class="txtPag"><option value="-1">Seleccione un supervisor</option>';	
		}
		
		else{
			$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles ur on pd.id_role=ur.id_role where ur.name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
			$optSup = '<select id="lsSuperv" class="txtPag"><option value="-1">Seleccione un supervisor</option>';
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
	
	case 'save_emp':
		$sqlText = "select employee_id from employees where username='".$_POST['cod']."'";
		$dtE = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt = -1; //Ya existe un usuario con el mismo username	
		}
		else if(strlen($_SESSION['usr_id'])==0){
			$rslt = -2; //Sesion no valida
		}
		else{
			try{
				$fec_admis=$oFec->cvDtoY($_POST['fec_admis']);
				$fec_nac = $oFec->cvDtoY($_POST['fec_nac']);
				//Ingresa nuevo empleado
				$sqlText = "insert into employees set ".
					"id_supervisor=".$_POST['superv'].", ".
					"username='".strtoupper(trim($_POST['cod']) )."', ".
					"user_pwd='".md5(strtoupper(trim($_POST['cod']) ) )."', ".
					"firstname='".strtoupper($_POST['nombre'])."', ".
					"lastname='".strtoupper($_POST['apellido'])."', ".
					"user_status= ".$_POST['status'].", ".
					"date_admis='".$fec_admis."', ".
					"salary=".$_POST['salario'].", ".
					"bonus=".$_POST['bono'].", ".
					"email='".$_POST['email']."', ".
					"account_number='".$_POST['numcuenta']."', ".
					"dui='".$_POST['dui']."', ".
					"address='".strtoupper($_POST['direccion'])."', ".
					"nit='".$_POST['nit']."', ".
					"isss='".$_POST['isss']."', ".
					"afpcrecer='".$_POST['crecer']."', ".
					"afpconfia='".$_POST['confia']."', ".
					"profession='".strtoupper($_POST['profesion'])."', ".
					"date_birth='".$fec_nac."', ".
					"minority_card='".$_POST['carnetmin']."', ".
					"ipsfa='".$_POST['ipsfa']."', ".
					"celular='".$_POST['cel']."', ".
					"tel_house='".$_POST['tel']."', ".
					"locker='".$_POST['locker']."', ".
					"agent_id = '".$_POST['agentID']."', ".
					"created_by = ".$_SESSION['usr_id'].", ".
					"geography_code = '".$_POST['pais']."', ".
					"notification_flag = '".$_POST['notificationFlag']."'";
				
				$dbEx->insSql($sqlText);
				
				$sqlText = "select employee_id from employees where username='".$_POST['cod']."'";
				$dtU = $dbEx->selSql($sqlText);

				//Cambiamos el tipo de plaza a CANDIDATO si el status es Aspirante
				$sqlText = "select jb.job_type_id job_type_id, job_type_name ".
					"from job_type jb ".
					"where 1 = (select count(1) from employee_status where status_id = ".$_POST['status']." and status_valid = 'N') ".
					"and jb.job_type_name = 'CANDIDATO'";

				$dtJobType = $dbEx->selSql($sqlText);
				
				if(strlen($dtJobType['0']['job_type_id']) > 0){
					$tipoPlaza = $dtJobType['0']['job_type_id'];
				}
				else{
	                $tipoPlaza =  $_POST['tipoPlaza'];
				}
				
	            //Si no ha seleccionado plaza se ingresa plaza por defecto "CANDIDATE"
				if($_POST['posc'] == 0 or strlen($_POST['posc'] == 0)){
					$sqlText = "select pd.id_placexdep id_placexdep ".
						"from placexdep pd inner join places pl on pd.id_place = pl.id_place ".
						"where pl.name_place = 'CANDIDATE'";
						
					$dtPlaza = $dbEx->selSql($sqlText);
					$idPlaza = $dtPlaza['0']['id_placexdep'];
				}else{
	                $idPlaza = $_POST['posc'];
				}

				//Inserta ap de nuevo empleado mientras no sea aspirante
				if($dtJobType['0']['job_type_name'] <> "CANDIDATO"){
	                $sqlText = "select employee_id from employees where username='".$_POST['cod']."'";
					$dtU = $dbEx->selSql($sqlText);
					$sqlText = "insert into apxemp set id_tpap=15, employee_id=".$dtU['0']['employee_id'].", ".
						"startdate_ap='".$fec_admis."', storagedate_ap=DATE(now()), ".
						"autor_ap=if(".strlen($_SESSION['usr_id'])."=0,null,'".$_SESSION['usr_id']."'), ".
						"comment_ap='".$_POST['coment']."', ".
						"autor_hr=if('".$_SESSION['usr_rol']."'='RECURSOS HUMANOS',".
							"if(".strlen($_SESSION['usr_id'])."=0,null,'".$_SESSION['usr_id']."'),null) ";

					$dbEx->insSql($sqlText);
					$sqlText = "select max(id_apxemp) as id from apxemp where employee_id=".$dtU['0']['employee_id']." and id_tpap=15";
					$dtA = $dbEx->selSql($sqlText);
					$idAp = ", id_apxemp=".$dtA['0']['id'];
				}
				else{
	                $idAp = "";
				}
				
	            //Relaciona al empleado con su plaza
				$sqlText = "insert into plazaxemp set id_placexdep=".$idPlaza.", ".
					"employee_id=".$dtU['0']['employee_id'].", pprueba_plxemp=".$_POST['prueba'].", ".
					"job_type_id = ".$tipoPlaza.",start_date = CURDATE() ".$idAp;
				$dbEx->insSql($sqlText);

				//Asigna las aplicaciones al empleado
				if($dtJobType['0']['job_type_name'] <> "CANDIDATO"){
					$sqlText = "select id_role from placexdep where id_placexdep=".$idPlaza;
					$dtR = $dbEx->selSql($sqlText);
				
					if($dtR['0']['id_role']>2){
						$sqlText = "insert into appxuser set app_id=1, employee_id=".$dtU['0']['employee_id'];
						$dbEx->insSql($sqlText);
						if($dtR['0']['id_role']==7){
							$sqlText = "insert into appxuser set app_id=2, employee_id=".$dtU['0']['employee_id'];
							$dbEx->insSql($sqlText);
							}
					}
					$sqlText = "insert into appxuser set app_id=3, employee_id=".$dtU['0']['employee_id'];
					$dbEx->insSql($sqlText);
					$sqlText = "select name_place from places p inner join placexdep pd on p.id_place=pd.id_place where id_placexdep=".$_POST['posc'];
					$dtPla = $dbEx->selSql($sqlText);
					if($dtPla['0']['name_place']=='QUALITY AGENT'){
						$sqlText = "insert into appxuser set app_id=1, employee_id=".$dtU['0']['employee_id'];
						$dbEx->insSql($sqlText);
					}
	   			}
				$rslt = $dtU['0']['employee_id'];
			}
			catch (Exception $e){
				//Si ocurre un error eliminar la ap, plaza y el empleado y aplicaciones
				$sqlText = "delete from plazaxemp where employee_id = ".$dtU['0']['employee_id'];
        		$dbEx->updSql($sqlText);

				$sqlText = "delete from apxemp where employee_id = ".$dtU['0']['employee_id'];
        		$dbEx->updSql($sqlText);

        		$sqlText = "delete from appxuser where employee_id = ".$dtU['0']['employee_id'];
        		$dbEx->updSql($sqlText);

        		$sqlText = "delete from employees where employee_id = ".$dtU['0']['employee_id'];
        		$dbEx->updSql($sqlText);

        		$rslt = -3;
			}

		}
		echo $rslt;
	break;
	
	case 'loadEmp':
	
		$sqlText = "select *, date_format(date_admis,'%d/%m/%Y') as f1, date_format(date_birth,'%d/%m/%Y') as f2 ".
			" from employees e inner join employee_status st on e.user_status = st.status_id where employee_id=".$_POST['idE'];
		$dtE = $dbEx->selSql($sqlText);
		$supervisor = "";
		if($dtE['0']['ID_SUPERVISOR']!=0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['0']['ID_SUPERVISOR'];
			$dtS = $dbEx->selSql($sqlText);
			$supervisor = $dtS['0']['firstname']." ".$dtS['0']['lastname'];
		}
		
		$sqlText = "select name_place from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id ".
			"inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".
			"where pe.status_plxemp='A' and e.employee_id=".$_SESSION['usr_id'];
	  	$dtPlace = $dbEx->selSql($sqlText);
		
		if($_POST['accion']==1){
			$rslt = cargaPag("../mtto/datosEmp.php");
		}
		if($_POST['accion']==2){
			$rslt = cargaPag("../mtto/edit_emp.php");	
		}
		$rslt = str_replace("<!--id_empleado-->",$_POST['idE'],$rslt);
		$rslt = str_replace("<!--id_supervisor-->",$dtE['0']['ID_SUPERVISOR'],$rslt);
		$rslt = str_replace("<!--supervisor-->",$supervisor,$rslt);
		$rslt = str_replace("<!--username-->", $dtE['0']['USERNAME'],$rslt);
		$rslt = str_replace("<!--nombre-->", $dtE['0']['FIRSTNAME'],$rslt);
		$rslt = str_replace("<!--apellido-->",$dtE['0']['LASTNAME'],$rslt);
		$rslt = str_replace("<!--fec_admis-->",$dtE['0']['f1'],$rslt);
		$rslt = str_replace("<!--email-->",$dtE['0']['EMAIL'],$rslt);
		$rslt = str_replace("<!--notificationFlag-->",$dtE['0']['notification_flag'],$rslt);

		$optNotif = '';
		$sel="";
		if($dtE['0']['notification_flag']=='N'){
			$sel="selected";
		}
		$optNotif .= '<option value="N" '.$sel.'>N</option>';
		$sel="";
		if($dtE['0']['notification_flag']=='Y'){
			$sel="selected";
		}
		$optNotif .= '<option value="Y" '.$sel.'>Y</option>';

		$rslt = str_replace("<!--notificationOpt-->",$optNotif,$rslt);

		if($_SESSION['usr_idrol']<5 and $dtPlace['0']['name_place']!='ACCOUNTING MANAGER' 
			and $dtPlace['0']['name_place']!='RECRUITMENT MANAGER'){
			
			//Si el id del rol es menor o igual a 5 no permitira actualizar
			$rslt = str_replace("<!--salario-->","",$rslt);
			$rslt = str_replace("<!--bono-->","",$rslt);
			$rslt = str_replace("<!--num_cuenta-->","",$rslt);
			$rslt = str_replace("<!--btn_update-->", 2,$rslt);
			
		}else{
			$rslt = str_replace("<!--salario-->",$dtE['0']['SALARY'],$rslt);
			$rslt = str_replace("<!--bono-->",$dtE['0']['BONUS'],$rslt);
			$rslt = str_replace("<!--num_cuenta-->",$dtE['0']['ACCOUNT_NUMBER'],$rslt);
			$rslt = str_replace("<!--btn_update-->", 1,$rslt);
		}
		$rslt = str_replace("<!--dui-->",$dtE['0']['DUI'],$rslt);
		$rslt = str_replace("<!--direccion-->",$dtE['0']['ADDRESS'],$rslt);
		$rslt = str_replace("<!--nit-->",$dtE['0']['NIT'],$rslt);
		$rslt = str_replace("<!--isss-->",$dtE['0']['ISSS'],$rslt);
		$rslt = str_replace("<!--afpcrecer-->",$dtE['0']['AFPCRECER'],$rslt);
		$rslt = str_replace("<!--afpconfia-->",$dtE['0']['AFPCONFIA'],$rslt);
		$rslt = str_replace("<!--profesion-->",$dtE['0']['PROFESSION'],$rslt);
		$rslt = str_replace("<!--fec_nac-->",$dtE['0']['f2'],$rslt);
		$rslt = str_replace("<!--minoridad-->",$dtE['0']['MINORITY_CARD'],$rslt);
		$rslt = str_replace("<!--ipsfa-->",$dtE['0']['IPSFA'],$rslt);
		$rslt = str_replace("<!--celular-->",$dtE['0']['CELULAR'],$rslt);
		$rslt = str_replace("<!--tel_casa-->",$dtE['0']['TEL_HOUSE'],$rslt);
		$rslt = str_replace("<!--locker-->",$dtE['0']['LOCKER'],$rslt);
		$rslt = str_replace("<!--phone_login-->",$dtE['0']['phone_login'],$rslt);
		$rslt = str_replace("<!--agentID-->",$dtE['0']['AGENT_ID'],$rslt);

		//Datos de pais
		$sqlText = "select geography_name from geographies where geography_code = '".$dtE['0']['geography_code']."'";
		$dtPais = $dbEx->selSql($sqlText);
		$rslt = str_replace("<!--pais-->",$dtPais['0']['geography_name'],$rslt);

		$sqlText = "select geography_code, geography_name ".
				"from geographies where geography_type = 'COUNTRY' ".
				"and ifnull(end_date,sysdate() + 1) > sysdate() ".
				"order by geography_name";

		$dtPais = $dbEx->selSql($sqlText);
		
		$optCountry = '<option value="0">Seleccione un pais</option>';
		foreach ($dtPais as $dtP) {
			$sel = "";
			if($dtP['geography_code'] == $dtE['0']['geography_code']){$sel="selected";}
			$optCountry .='<option value="'.$dtP['geography_code'].'" '.$sel.'>'.$dtP['geography_name'].'</option>';
		}
		$rslt = str_replace("<!--optCountry-->",$optCountry,$rslt);		
		
		
		$sqlText = "select pd.id_placexdep, ac.id_account, name_account,d.id_depart, name_depart, pe.pprueba_plxemp pprueba_plxemp, ".
					"pl.id_place, name_place, e.user_status, pd.id_role, pe.job_type_id, pj.job_type_name ".
					"from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id ".
					"inner join job_type pj on pe.job_type_id = pj.job_type_id ".
					"inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".
    				"left outer join account ac on pd.id_account = ac.id_account ".
    				"left outer join depart_exc d on pd.id_depart = d.id_depart ".
   					"inner join places pl on pd.id_place = pl.id_place ".
					"where pe.status_plxemp='A' ".
					"and pe.employee_id=".$_POST['idE'];

		$dtP = $dbEx->selSql($sqlText);
		$rslt = str_replace("<!--nom_cuenta-->",$dtP['0']['name_account'],$rslt);
		$rslt = str_replace("<!--nom_depart-->",$dtP['0']['name_depart'],$rslt);
		$rslt = str_replace("<!--nom_plaza-->",$dtP['0']['name_place'],$rslt);
		$rslt = str_replace("<!--per_prueba-->",$dtP['0']['pprueba_plxemp'],$rslt);
		$rslt = str_replace("<!--tipoPlaza-->",$dtP['0']['job_type_name'],$rslt);
		$rslt = str_replace("<!--estatus-->",$dtE['0']['STATUS_NAME'],$rslt);
		
		//Para editar estado
		$sqlText = "select * from employee_status order by STATUS_NAME";
		$dtSt = $dbEx->selSql($sqlText);
		$optEstado = '<select id="lsEstado" class="txtPag">';
		foreach($dtSt as $dtSt){
			if($dtSt['STATUS_ID']==$dtE['0']['STATUS_ID']){$sel = "selected";}
			else{$sel="";}
			$optEstado .='<option value="'.$dtSt['STATUS_ID'].'" '.$sel.'>'.$dtSt['STATUS_NAME'].'</option>';
		}

		$optEstado .='</select>';
		$rslt = str_replace("<!--optEstado-->",$optEstado,$rslt);
		
		//Para editar el tipo de plaza
		$sqlText = "select * from job_type";
		$dtJt = $dbEx->selSql($sqlText);
		$optTipoPlaza = '<select id="lsPlaza" class="txtPag">';
		foreach($dtJt as $dtJt){
  			if($dtJt['JOB_TYPE_ID']==$dtP['0']['job_type_id']){$sel = "selected";}
			else{$sel="";}
			$optTipoPlaza .='<option value="'.$dtJt['JOB_TYPE_ID'].'" '.$sel.'>'.$dtJt['JOB_TYPE_NAME'].'</option>';
		}
		$optTipoPlaza .= '</select>';
		$rslt = str_replace("<!--optTipoPlaza-->",$optTipoPlaza,$rslt);


		//Datos de la cuenta, posicion, plaza y supervisor si no es aspirante o tiene estos datos registrados

		//Datos de cuenta
		$sqlText = "select * from account where account_status='A' order by NAME_ACCOUNT";
		$dtC = $dbEx->selSql($sqlText);
		$optC = '<select id="lsCuenta" class="txtPag" onChange="getDepart(this.value)">';
  		$optC .='<option value="0">Seleccione una cuenta</option>';
		foreach($dtC as $dtCu){
			if($dtCu['ID_ACCOUNT']==$dtP['0']['id_account']){$sel = "selected";}
			else{$sel="";}
				$optC .='<option value="'.$dtCu['ID_ACCOUNT'].'" '.$sel.'>'.$dtCu['NAME_ACCOUNT'].'</option>';
			}
		$optC .='</select>';
		$rslt = str_replace("<!--optcuenta-->",$optC,$rslt);

		//Mostrar listas de cuenta, departamento, posicion si la plaza no en CANDIDATO
        if($dtP['0']['id_account'] > 0){
			//datos de departamento
			$sqlText = "select distinct(d.id_depart), name_depart from depart_exc d inner join placexdep pd on d.id_depart=pd.id_depart inner join account ac on pd.id_account=ac.id_account where ac.id_account=".$dtP['0']['id_account']." and account_status='A' order by name_depart";
			$dtD = $dbEx->selSql($sqlText);
			$optD = '<select id="lsDepart" onChange="getPosc(this.value,'.$dtP['0']['id_account'].')" class="txtPag">';
			foreach($dtD as $dtD){
				if($dtD['id_depart']==$dtP['0']['id_depart']){$sel="selected";}
				else{$sel="";}
				$optD .='<option value="'.$dtD['id_depart'].'" '.$sel.'>'.$dtD['name_depart'].'</option>';
			}
			$optD .='</select>';
			$rslt = str_replace("<!--optDepto-->",$optD,$rslt);
		
			//datos de posicion
			$sqlText = "select distinct(pd.id_placexdep), name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place where pd.id_depart=".$dtP['0']['id_depart']." and pd.id_account=".$dtP['0']['id_account']." order by name_place";
			$dtPos = $dbEx->selSql($sqlText);
			$optP = '<select id="lsPosc" onChange="getSuperv(this.value)" class="txtPag">';
				foreach($dtPos as $dtPos){
					if($dtPos['id_placexdep']==$dtP['0']['id_placexdep']){$sel="selected";}
					else{$sel="";}
					$optP .='<option value="'.$dtPos['id_placexdep'].'" '.$sel.'>'.$dtPos['name_place'].'</option>';
				}
			$optP .='</select>';
			$rslt = str_replace("<!--optPosicion-->",$optP,$rslt);
		

			//datos de supervisor
			$sqlText = "select id_role, nivel_place from placexdep pd inner join places pl on pd.id_place=pl.id_place where id_placexdep=".$dtP['0']['id_placexdep'];
			$dtS = $dbEx->selSql($sqlText);
			if($dtS['0']['id_role']>=3){
				if($dtS['0']['nivel_place']==1){
					$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where id_account=".$dtP['0']['id_account']."  and id_depart=".$dtP['0']['id_depart']." and pd.id_role>=".$dtP['0']['id_role']." and pl.nivel_place=2 and user_status=1 order by firstname";
				}
				else if($dtS['0']['nivel_place']==2){
					$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join places pl on pl.id_place=pd.id_place where pd.id_role>=".$dtP['0']['id_role']." and pl.nivel_place=2 and user_status=1 order by firstname";
					}
				}
				else if(($dtS['0']['id_role']==1) and ($dtS['0']['nivel_place']==1) and $dtP['0']['id_account']==3){
					$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where id_role>=3 and user_status=1 order by firstname";
				}
		
				else{
					$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep where pd.id_role=3 or pd.id_role=5 and pe.status_plxemp='A' and user_status=1 order by firstname";
				}
				$dtSup = $dbEx->selSql($sqlText);
				$optSup = '<select id="lsSuperv" class="txtPag"><option value="0">NO APLICA</option>';
					foreach($dtSup as $dtSup){
						if($dtSup['employee_id']==$dtE['0']['ID_SUPERVISOR']){$sel="selected";}
						else{$sel="";}
						$optSup .= '<option value="'.$dtSup['employee_id'].'" '.$sel.'>'.$dtSup['firstname']."&nbsp;".$dtSup['lastname'];
					}
				$optSup .= '</select>';
				$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
  			}//fin de listas cuenta, departamento
  			
  			//Listado de adjuntos
			$AttList = getDocList($_POST['idE']);
			$rslt = str_replace("<!--docList-->",$AttList,$rslt);
			
			//Foto del empleado
			$rslt = str_replace("<!--fotoEmpleado-->",(getPhoto($_POST['idE'])),$rslt);

			//Fecha de egreso
			$sqlText = "select date_format(end_date,'%d/%m/%Y') end_date ".
				"from plazaxemp ".
				"where id_plxemp = get_idultimaplaza(".$_POST['idE'].")";

			$dtF = $dbEx->selSql($sqlText);

			$rslt = str_replace("<!--fec_egreso-->",$dtF['0']['end_date'],$rslt);

		echo $rslt;
	break;
	
	case 'rptEmp':
		$rslt = cargaPag("../mtto/filtrosEmp.php");
		$sqlText = "select * from account where account_status='A' order by name_account";
		$dtC = $dbEx->selSql($sqlText);
		$optC = "";
		foreach($dtC as $dtC){
				$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}
		$sqlText ="select * from depart_exc where status_depart = 1 order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		$optD = "";
		foreach($dtD as $dtD){
			$optD .='<option value="'.$dtD['ID_DEPART'].'">'.$dtD['NAME_DEPART'].'</option>';	
		}
		$sqlText = "select * from places order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP ="";
		foreach($dtP as $dtP){
			$optP .='<option value="'.$dtP['ID_PLACE'].'">'.$dtP['NAME_PLACE'].'</option>';	
		}
		
		//Lista de estados
		$sqlText = "select * from employee_status order by status_name";
		$dtSt = $dbEx->selSql($sqlText);
		$optStatus = "";
	 	foreach($dtSt as $dtSt){
			$optStatus .='<option value="'.$dtSt['STATUS_ID'].'">'.$dtSt['STATUS_NAME'].'</option>';
		}
		
		if($_POST['tipoRte']=="Historico"){
			$leyenda = "HISTORICO";
   			$btnAccion = 'onClick="load_rpthistemp()"';
		}
		else{
            $btnAccion = 'onClick="load_rptemp()"';
		}
		
		$rslt = str_replace("<!--flagHistorico-->",$leyenda,$rslt);
		$rslt = str_replace("<!--btnAccion-->",$btnAccion,$rslt);
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--optDepart-->",$optD,$rslt);
		$rslt = str_replace("<!--optPlaza-->",$optP,$rslt);
		$rslt = str_replace("<!--optStatus-->",$optStatus,$rslt);
		
		$sqlText = "select distinct(e.employee_id), firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join places pl on pd.id_place=pl.id_place where nivel_place=2 and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = "";
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname'].'&nbsp;'.$dtS['lastname'].'</option>';	
		}
		$rslt = str_replace("<!--optSuperv-->",$optSup,$rslt);
		
		echo $rslt;
	break;
	
	case 'load_rptemp':

		$filtro = "";
		if($_POST['cuenta']>0){
			$filtro .= " and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depart']>0){
			$filtro .= " and pd.id_depart=".$_POST['depart'];
			}
		if($_POST['posicion']>0){
			$filtro .= " and pd.id_place=".$_POST['posicion'];
			}
		if($_POST['superv']>0){
			$filtro .= " and e.id_supervisor=".$_POST['superv'];
			}
			
		if($_POST['fec_ini']!=""){
			$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
			$filtro .= " and date_admis between date '".$fec_ini."' and date '".$fec_fin."'";
			}
		if($_POST['estado'] <> "*"){
			$filtro .= " and e.user_status=".$_POST['estado'];
		}
		if($_POST['ini_retiro']!=""){
			$ini_retiro = $oFec->cvDtoY($_POST['ini_retiro']);
			$fin_retiro = $oFec->cvDtoY($_POST['fin_retiro']);
			$filtro .= " and pe.end_date between date '".$ini_retiro."' and date '".$fin_retiro."'";
			}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			 $filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";

			}
		if(isset($_POST['username']) && $_POST['username']!=''){
			 $filtro .= " and (e.username like '%".strtoupper($_POST['username'])."%')";
			}

		$sqlText = "select distinct(e.employee_id) as EMPLOYEE_ID, ".
						"USERNAME, FIRSTNAME, LASTNAME, ".
						"date_format(date_admis,'%d/%m/%Y') as f1, pl.name_place ".
					"from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id ".
						"inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
					    "left outer join account c on pd.id_account = c.id_account ".
					    "inner join places pl on pl.id_place = pd.id_place ".
					"where pe.id_plxemp = get_idultimaplaza(e.employee_id) ".$filtro." ".
					"order by firstname";

		
		$rslt .='<table cellpadding="3" cellspacing="0" width="800" class="tblResult" align="center">';
		$dtE = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt .= '<tr class="txtPag"><td colspan="3">Coincidencias:&nbsp;'.$dbEx->numrows.'</td>';
			$rslt .= '<td align="right" colspan="2"><form target="_blank" action="report/xls_rptemptotal.php" method="post">';
			$rslt .= '<input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Exportar a excel" />';
			$rslt .= '&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$filtro.'">';
			$rslt .= '<tr class="showItem"><td>BADGE</td><td>NOMBRE</td><td>FECHA DE INGRESO</td><td>POSICION</td></tr>';
			foreach($dtE as $dtE){
				$rslt .='<tr class="rowCons" onclick="loadEmp('.$dtE['EMPLOYEE_ID'].')"><td>'.$dtE['USERNAME'].'</td><td>'.$dtE['FIRSTNAME'].'&nbsp;'.$dtE['LASTNAME'].'</td><td align="center">'.$dtE['f1'].'</td><td>'.$dtE['name_place'].'</td></tr>';
			}
		}
		else{
			$rslt .= '<tr><td colspan="4">No hay coincidencias para los filtros seleccionados</td></tr>';	
		}
		$rslt .='</table>';
		
		echo $rslt;

	break;
	
	case 'sv_updateemp':
		$sqlText = "select employee_id from employees ".
					"where username='".$_POST['cod']."' and employee_id!=".$_POST['idE'];
		$dtE = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt = -1; //Ya existe un usuario con el mismo username	
		}
		else{
			$fec_admis=$oFec->cvDtoY($_POST['fec_admis']);
			$fec_nac = $oFec->cvDtoY($_POST['fec_nac']);
			$phone = ", phone_login=NULL ";
			if($_POST['phoneLogin']>0){
				$phone = ", phone_login=".$_POST['phoneLogin'];	
			}
			
			//Actualiza empleado
			$sqlText = "update employees set id_supervisor=".$_POST['superv'].", ".
			 			"username='".strtoupper( trim($_POST['cod']) )."', ".
						"firstname='".strtoupper($_POST['nombre'])."', ".
						"lastname='".strtoupper($_POST['apellido'])."', ".
						"date_admis='".$fec_admis."', ".
						"salary=".$_POST['salario'].", ".
						"bonus=".$_POST['bono'].", ".
						"email='".$_POST['email']."', ".
						"account_number='".$_POST['numcuenta']."', ".
						"dui='".$_POST['dui']."', ".
						"address='".strtoupper($_POST['direccion'])."', ".
						"nit='".$_POST['nit']."', ".
						"isss='".$_POST['isss']."', ".
						"afpcrecer='".$_POST['crecer']."', ".
						"afpconfia='".$_POST['confia']."', ".
						"profession='".strtoupper($_POST['profesion'])."', ".
						"date_birth='".$fec_nac."', ".
						"minority_card='".$_POST['carnetmin']."', ".
						"ipsfa='".$_POST['ipsfa']."', ".
						"celular='".$_POST['cel']."', ".
						"tel_house='".$_POST['tel']."', ".
						"locker='".$_POST['locker']."', ".
						//"tp_hiring='".$_POST['tipoPlaza']."', ".
						"user_status=".$_POST['estado']." ".$phone.", ".
						"agent_id = '".$_POST['agentID']."', ".
						"geography_code = '".$_POST['pais']."', ".
						"notification_flag = '".$_POST['notificationFlag']."' ".
						"where employee_id=".$_POST['idE'];
						
			$dbEx->updSql($sqlText);
			
			//Si no ha seleccionado cuenta, departamento y plaza se inserta candidato
			//Cambiamos el tipo de plaza a CANDIDATO si el status es Aspirante
			$sqlText = "select jb.job_type_id job_type_id, job_type_name ".
				"from job_type jb ".
				"where 1 = (select count(1) from employee_status where status_id = ".$_POST['estado']." and status_valid = 'N') ".
				"and jb.job_type_name = 'CANDIDATO'";

			$dtJobType = $dbEx->selSql($sqlText);
			if(strlen($dtJobType['0']['job_type_id']) > 0){
				$tipoPlaza = $dtJobType['0']['job_type_id'];
			}
			else{
                $tipoPlaza =  $_POST['tipoPlaza'];
			}

            //Si no ha seleccionado plaza se ingresa plaza por defecto "CANDIDATE"
			if($_POST['posc'] == 0 or strlen($_POST['posc'] == 0)){
				$sqlText = "select pd.id_placexdep id_placexdep ".
					"from placexdep pd inner join places pl on pd.id_place = pl.id_place ".
					"where pl.name_place = 'CANDIDATE'";

				$dtPlaza = $dbEx->selSql($sqlText);
				$idPlaza = $dtPlaza['0']['id_placexdep'];
			}else{
                $idPlaza = $_POST['posc'];
			}
			
			//Obtener la plaza activa, si ha cambiado se da de baja a la anterior y se crea una nueva
   			$sqlText = "select count(1) count from plazaxemp ".
			   "where employee_id = ".$_POST['idE']." and status_plxemp='A' ".
			   "and end_date is null and (id_placexdep <> ".$idPlaza." or job_type_id <> ".$tipoPlaza.")";
			   
			$cambioPlaza = $dbEx->selSql($sqlText);
			if($cambioPlaza['0']['count'] == 1){
                $sqlText = "update plazaxemp set status_plxemp = 'I', end_date = CURDATE() where employee_id = ".$_POST['idE']." and status_plxemp = 'A'";
				$dbEx->updSql($sqlText);

				//Insertar la plaza activa
				$sqlText = "insert into plazaxemp set id_placexdep=".$idPlaza.", ".
						" employee_id=".$_POST['idE'].", status_plxemp='A', start_date = CURDATE(), job_type_id = ".$tipoPlaza;
				$dbEx->insSql($sqlText);
				
			}
			//Si no hay plaza activa y el estado del empleado es diferente a inactivo se inserta la plaza
			$sqlText = "select count(1) count from plazaxemp ".
			   "where employee_id = ".$_POST['idE']." and status_plxemp='A' ".
			   "and end_date is null";
			
			$cambioPlaza = $dbEx->selSql($sqlText);			

			if ($cambioPlaza['0']['count'] == 0 && $_POST['estado'] <> 0) {
				$sqlText = "insert into plazaxemp set id_placexdep=".$idPlaza.", ".
						" employee_id=".$_POST['idE'].", status_plxemp='A', start_date = CURDATE(), job_type_id = ".$tipoPlaza;
				$dbEx->insSql($sqlText);
			}

			//Si el cambio de status es a Inactivo se da de baja a la aplicacion activa
			if($_POST['estado'] == 0){
                $sqlText = "update plazaxemp set status_plxemp = 'I', end_date = CURDATE() where employee_id = ".$_POST['idE']." and status_plxemp = 'A'";
				$dbEx->updSql($sqlText);
			}
			
			//Actualizar las aplicaciones del empleado de acuerdo a su rol
			if($dtJobType['0']['job_type_name'] <> "CANDIDATO" or strlen($dtJobType['0']['job_type_id']) <= 0){
				$sqlText = "select id_role from placexdep where id_placexdep=".$_POST['posc'];
				$dtR = $dbEx->selSql($sqlText);
			
				if($dtR['0']['id_role']<=2){ //elimina aplicaciones si al usuario es cambiado a agente
					$sqlText = "select * from appxuser where employee_id=".$_POST['idE'];
					$dtApp = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sqlText = "delete from appxuser where employee_id=".$_POST['idE']." and app_id=1";
						$dbEx->updSql($sqlText);
					}
				   //Agrega aplicacion de paystub si no la tiene
				   $sqlText = "insert into appxuser(app_id,employee_id) ".
						"select 3,".$_POST['idE']." from dual where (select count(1) from appxuser where employee_id = ".$_POST['idE']." and app_id=3) = 0";
				   $dbEx->insSql($sqlText);
				}
				if($dtR['0']['id_role']>2){
					$sqlText = "select * from appxuser where employee_id=".$_POST['idE']." and app_id=1";
					$dtApp = $dbEx->selSql($sqlText);
					if($dbEx->numrows==0){
						$sqlText = "insert into appxuser set app_id=1, employee_id=".$_POST['idE'];
						$dbEx->insSql($sqlText);
					}
					if($dtR['0']['id_role']==7){
					$sqlText = "select * from appxuser where employee_id=".$_POST['idE']." and app_id=2";
					$dtApp = $dbEx->selSql($sqlText);
						if($dbEx->numrows==0){
							$sqlText = "insert into appxuser set app_id=2, employee_id=".$_POST['idE'];
							$dbEx->insSql($sqlText);
						}
					}
				}
   			}
			$rslt = $_POST['idE'];
		}
		echo $rslt;	
	break;
	
	case 'load_rpthistemp':
	    $filtro = " where 1 = 1 ";

		if($_POST['cuenta']>0){
			$filtro .= " and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depart']>0){
			$filtro .= " and pd.id_depart=".$_POST['depart'];
			}
		if($_POST['posicion']>0){
			$filtro .= " and pd.id_place=".$_POST['posicion'];
			}
		if($_POST['superv']>0){
			$filtro .= " and e.id_supervisor=".$_POST['superv'];
			}

		if($_POST['fec_ini']!=""){
			$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
			$filtro .= " and date_admis between date '".$fec_ini."' and date '".$fec_fin."'";
			}
		if($_POST['estado'] <> "*"){
			$filtro .= " and e.user_status=".$_POST['estado'];
		}
		if($_POST['ini_retiro']!=""){
			$ini_retiro = $oFec->cvDtoY($_POST['ini_retiro']);
			$fin_retiro = $oFec->cvDtoY($_POST['fin_retiro']);
			$filtro .= " and pe.end_date between date '".$ini_retiro."' and date '".$fin_retiro."'";
			}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			 $filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";
			}
		if(isset($_POST['username']) && $_POST['username']!=''){
			 $filtro .= " and (e.username like '%".strtoupper($_POST['username'])."%')";
			}

        $sqlText = "select e.username, concat(e.firstname,' ',e.lastname) fullname, ".
				"es.status_name, pl.name_place, pj.job_type_name, ".
			    "date_format(pe.start_date,'%d/%m/%Y') start_date, date_format(pe.end_date,'%d/%m/%Y') end_date, ".
			    "pe.status_plxemp ".
			"from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id ".
				"inner join employee_status es on e.user_status = es.status_id ".
				"inner join job_type pj on pe.job_type_id = pj.job_type_id ".
		        "inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
				"inner join places pl on pl.id_place = pd.id_place ".
				$filtro." ".
		        "order by trim(firstname), pe.status_plxemp, pe.start_date desc";

        
        $dtE = $dbEx->selSql($sqlText);
		$rslt .='<table cellpadding="3" cellspacing="0" width="800" class="tblResult" align="center">';
		if($dbEx->numrows>0){
			$rslt .= '<tr class="txtPag"><td colspan="3">Coincidencias:&nbsp;'.$dbEx->numrows.'</td>';
			$rslt .= '<td align="right" colspan="5"><form target="_blank" action="report/xls_rpthistemp.php" method="post">';
			$rslt .= '<input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Exportar a excel" />&nbsp;&nbsp;';
			$rslt .= '<input type="hidden" name="filtro" value="'.$filtro.'">';
			$rslt .= '</td></tr>';

			$rslt .= '<tr class="showItem" style="font-size:12px"><td>BADGE</td><td>NOMBRE</td><td>ESTADO ACTUAL</td><td>PLAZA</td>'.
					'<td>TIPO DE PLAZA</td><td>FECHA INICIAL</td><td>FECHA FINAL</td><td>ESTADO DE PLAZA</td></tr>';
			foreach($dtE as $dtE){
				$rslt .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['fullname'].'</td>'.
				'<td>'.$dtE['status_name'].'</td><td>'.$dtE['name_place'].'</td><td>'.$dtE['job_type_name'].'</td>'.
				'<td>'.$dtE['start_date'].'</td><td>'.$dtE['end_date'].'</td><td>'.$dtE['status_plxemp'].'</td></tr>';
			}
		}
		else{
			$rslt .= '<tr><td colspan="8">No hay coincidencias para los filtros seleccionados</td></tr>';
		}
		$rslt .='</table>';
		echo $rslt;
	
	break;
	
    case 'formNewAttach': //Muestra formulario para ingresar nuevo adjunto
		$rslt = cargaPag("../mtto/frm_newattach.php");
        $rslt = str_replace("<!--id_empleado-->",$_POST['idE'],$rslt);
		echo $rslt;
	break;

	case 'getDocumentList': //Listado de adjunto
 		$rslt = getDocList($_POST['idE']);
 		echo $rslt;

	break;
	
    case 'formNewPhoto': //Muestra formulario para ingresar nueva foto
		$rslt = cargaPag("../mtto/frm_newphoto.php");
        $rslt = str_replace("<!--id_empleado-->",$_POST['idE'],$rslt);
		echo $rslt;
	break;
	
	case 'getPhoto': //Muestra la foto actualizada
 		$rslt = getPhoto($_POST['idE']);
 		echo $rslt;

	break;
	
	case 'deleteFile': //Eliminar adjunto
        $rslt = 1;
		$sqlText = "select EMP_ATTACH_NAME from empl_attachments where EMP_ATTACH_ID = ".$_POST['attachID'];
		$dtAt = $dbEx->selSql($sqlText);
		$dir = '../mtto/archivos/';
		if(file_exists($dir.$dtAt['0']['EMP_ATTACH_NAME'])){
	    	unlink($dir.$dtAt['0']['EMP_ATTACH_NAME']);
 		}

	    $sqlText = "delete from empl_attachments where EMP_ATTACH_ID = ".$_POST['attachID'];
	    $dbEx->updSql($sqlText);
	    
		echo $rslt;
	
 	break;

 }
 
