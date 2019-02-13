<?php
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
 
$dbEx = new DBX;
$oFec = new OFECHA;

  function cargaPag($urlToLoad){ //funcion para cargar una pagina
    $pagLoad = '';
    $fp=fopen($urlToLoad,"r") or die("Error to open file");
    $pagLoad = fread($fp,30000);
    return $pagLoad;
  }

switch($_POST['Do']){
	case 'updatePass':
		$rslt = cargaPag("../mtto/formUpdatePass.php");
		$sqlText = "select employee_id,username, firstname, lastname from employees where user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optE = '<option value="0">Select a employee</option>';
			foreach($dtEmp as $dtE){
				$optE .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname']."</option>";
			}
		}
		$rslt = str_replace("<!--optEmp-->",$optE,$rslt); 
		echo $rslt;
	break;
	
	case 'UpdPass':
		$sqlText = "update employees set user_pwd='".md5( trim($_POST['pass']) )."' where employee_id=".$_POST["idE"];
	    $dbEx->updSql($sqlText);
	    $rslt = 2;
		echo $rslt;
	break;
	case 'newApp':
		$rslt = cargaPag("../mtto/formNewApp.php");
		$sqlText = "select employee_id, username, firstname, lastname from employees where user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optE = '<option value="0">Select a agent</option>';
			foreach($dtEmp as $dtE){
				$optE .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname']."</option>";
			}
		}
		$rslt = str_replace("<!--optEmp-->",$optE,$rslt);
		echo $rslt;
	break;
	
	case 'loadAppxemp':
		$sqlText = "select app.app_id, app_name from exc_aplicaciones app inner join appxuser apu on app.app_id=apu.app_id where employee_id=".$_POST['idE'];
		$dtApp = $dbEx->selSql($sqlText);
		$rslt = '<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">';
		if($dbEx->numrows>0){
			$rslt .= '<tr><td colspan="3" class="showItem">Aplications asigned</td></tr>';
			$n=1;
			foreach($dtApp as $dtA){
				$rslt .='<tr class="rowCons"><td>'.$n.'</td><td>'.$dtA['app_name'].'</td><td  style="cursor:pointer" title="Click to delete"><img src="images/elim.png" title="Click to delete" onclick="deleteApp('.$dtA['app_id'].','.$_POST['idE'].')"></td></tr>';
				$n = $n+1;
			}
			$rslt .='<tr><td colspan="3" style="cursor:pointer" title="Click to add new aplication"><b><input type="button" value="+" onclick="formNewApp('.$_POST['idE'].')" class="btn"></b></td></tr>';
		}
		else{
			$rslt .='<tr><td colspan="3" style="cursor:pointer" title="Click to add new aplication"><b><input type="button" value="+" onclick="formNewApp('.$_POST['idE'].')" class="btn"></b></td></tr>';
			$rslt .='<tr><td colspan="3">No aplications asigned</td></tr>';	
		}
		$rslt .='</table><br>';
		$rslt .='<div id="lyNewApp" style="display:none"></div>';
		echo $rslt;
	break;
	
	case 'formNewApp':
		$sqlText = "select app.app_id, app_name from exc_aplicaciones app where app_id not in(select app_id from appxuser where employee_id=".$_POST['idE'].")";
		$dtApp = $dbEx->selSql($sqlText);
		$rslt = '<table cellpadding="2" cellspacing="0" width="500" border="0" class="tblListBack" align="center">';
		$optApp = "";
		if($dbEx->numrows>0){
			$optApp .='<option value="0">Select a application</option>';
			foreach($dtApp as $dtA){
				$optApp .= '<option value="'.$dtA['app_id'].'">'.$dtA['app_name'].'</option>';
			}
			$rslt .='<tr><td class="itemForm">Aplication:</td><td><select id="lsApp">'.$optApp.'</select></td></tr>';
			$rslt .='<tr><td colspan="2" align="center"><input type="button" class="btn" value="Save" onclick="saveApp('.$_POST['idE'].')"></td></tr>';
		}
		else{
			$rslt .='<tr><td colspan="2">No more application for this employee</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'saveApp':
		$sqlText = "insert into appxuser set app_id=".$_POST['idApp'].", employee_id=".$_POST['idE'];
		$dbEx->$dbEx->insertID;($sqlText);
		echo "2";
	break;
	
	case 'deleteApp':
		$sqlText = "delete from appxuser where app_id=".$_POST['idApp']." and employee_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);
		echo "2";
	break;
	
	case 'loadAccount':
		$sqlText = "select * from account ac inner join type_account ta on ac.id_typeacc=ta.id_typeacc order by account_status, name_account";
		$dtAcc = $dbEx->selSql($sqlText);
		$rslt = '<table cellpadding="2" cellspacing="0" width="600" border="0" class="tblListBack" align="center">';
		if($dbEx->numrows>0){
			$rslt .='<tr class="showItem">'.
				'<td width="5%" >Num</td>'.
				'<td width="25%">Account name</td>'.
				'<td width="25%">Account type</td>'.
				'<td width="25%">Description</td>'.
				'<td width="20%">Account status</td></tr>';
			$n = 1;
			foreach($dtAcc as $dtA){
				if($dtA['ACCOUNT_STATUS']=='A'){
					$estado = "ACTIVE";	
				}
				else if($dtA['ACCOUNT_STATUS']=='I'){
					$estado = "INACTIVE";	
				}
				$rslt .='<tr class="rowCons" title="Click to edit" style="cursor:pointer;" onclick="editAccount('.$dtA['ID_ACCOUNT'].')">'.
					'<td>'.$n.'</td>'.
					'<td>'.$dtA['NAME_ACCOUNT'].'</td>'.
					'<td align="center">'.$dtA['NAME_TYPEACC'].'</td>'.
					'<td>'.$dtA['DESC_ACCOUNT'].'</td>'.
					'<td align="center">'.$estado.'</td></tr>
					<tr><td colspan="5" align="center"><div id="lyUpdAcc'.$dtA['ID_ACCOUNT'].'"></div></td></tr>';	
				$n = $n+1;	
			}
			$rslt .='<tr><td colspan="4" style="cursor:pointer" title="Click to add new account"><b><input type="button" value="+" onclick="formNewAccount()" class="btn"></b></td></tr>';
		}
		else{
			$rslt .='<tr><td colspan="4">No matches</td></tr>';	
		}
		$rslt .='</table><br><br>';
		$rslt .='<div id="lyNewAcc" style="display:none"></div>';

		echo $rslt;
	break;

	case 'editAccount':
		$rslt = cargaPag("../mtto/formEditAccount.php");

		$sqlText = "select id_account, name_account, desc_account, account_status, ".
			"name_typeacc, ac.id_typeacc ".
			"from account ac inner join type_account ta on ac.id_typeacc=ta.id_typeacc ".
			"where id_account = ".$_POST['id_account'];

		$dtC = $dbEx->selSql($sqlText);

		$sqlText = "select id_typeacc, name_typeacc from type_account";
		$dtTpAcc = $dbEx->selSql($sqlText);

		$optTpAcc = "";
		foreach ($dtTpAcc as $dtTp) {
			$sel = "";
			if($dtC['0']['id_typeacc'] == $dtTp['id_typeacc']){
				$sel = "selected";
			}	
			$optTpAcc .= '<option value="'.$dtTp['id_typeacc'].'" '.$sel.' >'.$dtTp['name_typeacc'].'</option>';
		}

		$selA = "";
		$selI = "";
		if($dtC['0']['account_status'] == 'A'){
			$selA = "selected";
		}
		else{
			$selI = "selected";
		}

		$optS = '<option value="A" '.$selA.' >ACTIVE</option>';
		$optS .= '<option value="I" '.$selI.' >INACTIVE</option>';


		$rslt = str_replace("<!--id_account-->",$dtC['0']['id_account'],$rslt);
		$rslt = str_replace("<!--name_account-->",$dtC['0']['name_account'],$rslt);
		$rslt = str_replace("<!--desc_account-->",$dtC['0']['desc_account'],$rslt);
		$rslt = str_replace("<!--optTpAcc-->",$optTpAcc,$rslt);
		$rslt = str_replace("<!--optS-->",$optS,$rslt);

		echo $rslt;

	break;

	case 'updateAccount':
		$rslt = 0;
		$sqlText = "select count(1) c ".
			"from employees e inner join plazaxemp pe on pe.employee_id = e.employee_id ".
				"inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".
			"where pe.id_plxemp = get_idultimaplaza(e.employee_id) ".
			"and user_status = 1 ".
			"and pd.id_account = ".$_POST['id_account'];

		$dtC = $dbEx->selSql($sqlText);
		if($dtC['0']['c'] > 0 and $_POST['status'] == 'I'){
			$rslt = 1;
		}
		else{
			try{

				$sqlText = "update account set name_account = '".$_POST['name']."', ".
					"desc_account = '".$_POST['descrip']."', id_typeacc = ".$_POST[type].", ".
					"account_status='".$_POST['status']."' ".
					"where id_account = ".$_POST['id_account'];

				$dbEx->updSql($sqlText);
				$rslt = 2;
			}
			catch (Exception $e){
				$rslt = $e;
			}
		}

		echo $rslt;

	break;
	
	case 'formNewAccount':
		$sqlText = "select * from type_account";
		$dtTpAcc = $dbEx->selSql($sqlText);
		$optTpAcc = "";
		$optTpAcc .='<option value="0">Select a account type</option>';
		foreach($dtTpAcc as $dtTp){
			$optTpAcc .='<option value="'.$dtTp['ID_TYPEACC'].'">'.$dtTp['NAME_TYPEACC'].'</option>';
		}
		
		$rslt = '<table cellpadding="2" cellspacing="0" width="600" border="0" class="tblListBack" align="center">';
		$rslt .= '<tr><td class="showItem" colspan="2" align="center">Account record</td></tr>';
		$rslt .='<tr><td class="itemForm">Account name: </td><td><input type="text" id="txtNameAcc" size="35" class="txtPag"></td></tr>';
		$rslt .='<tr><td class="itemForm">Account description: </td><td><textarea id="txtDesc" class="txtPag" cols="50" rows="3"></textarea></td></tr>';
		$rslt .='<tr><td class="itemForm">Account type: </td><td><select id="lsType" class="txtPag">'.$optTpAcc.'</select></td></tr>';
		$rslt .='<tr><td colspan="2" align="center"><input type="button" class="btn" value="Save" onclick="saveAcc()"></td></tr></table>';
		echo $rslt;
	break;
	
	case 'saveAcc':
		$sqlText = "insert into account set id_typeacc=".$_POST['type'].", name_account='".$_POST['nameAcc']."', desc_account='".$_POST['descrip']."'";
		$dbEx->insSql($sqlText);
		echo "2";
	break;
	
	case 'loadDepto':
		$sqlText = "select * from depart_exc order by status_depart, name_depart";
		$dtDep = $dbEx->selSql($sqlText);
		$rslt = '<table cellpadding="2" cellspacing="0" width="600" border="0" class="tblListBack" align="center">';
		if($dbEx->numrows>0){
			$rslt .='<tr class="showItem"><td width="5%">Num</td><td width="35%">Department name</td><td width="35%">Description</td><td width="20%">Department status</td></tr>';
			$n = 1;
			foreach($dtDep as $dtD){
				if($dtD['STATUS_DEPART']==1){
					$estado = "ACTIVE";	
				}
				else if($dtD['STATUS_DEPART']=='0'){
					$estado = "INACTIVE";	
				}
				$rslt .='<tr class="rowCons"><td>'.$n.'</td><td>'.$dtD['NAME_DEPART'].'</td><td>'.$dtD['DESC_DEPART'].'</td><td align="center">'.$estado.'</td></tr>';	
				$n = $n+1;	
			}
			$rslt .='<tr><td colspan="4" style="cursor:pointer" title="Click to add new department"><b><input type="button" value="+" onclick="formNewDep()" class="btn"></b></td></tr>';
		}
		else{
			$rslt .='<tr><td colspan="4">No matches</td></tr>';	
		}
		$rslt .='</table><br><br>';
		$rslt .='<div id="lyNewDep" style="display:none"></div>';
		echo $rslt;
	break;
	
	case 'formNewDep':
		$rslt = '<table cellpadding="2" cellspacing="0" width="600" border="0" class="tblListBack" align="center">';
		$rslt .= '<tr><td class="showItem" colspan="2" align="center">Department record</td></tr>';
		$rslt .='<tr><td class="itemForm">Department name: </td><td><input type="text" id="txtNameDep" size="35" class="txtPag"></td></tr>';
		$rslt .='<tr><td class="itemForm">Account description: </td><td><textarea id="txtDesc" class="txtPag" cols="50" rows="3"></textarea></td></tr>';
		$rslt .='<tr><td colspan="2" align="center"><input type="button" class="btn" value="Save" onclick="saveDepto()"></td></tr></table>';
		echo $rslt;
	break;
	
	case 'saveDepto':
		$sqlText = "insert into depart_exc set name_depart='".$_POST['nameDep']."', desc_depart='".$_POST['descrip']."'";
		$dbEx->insSql($sqlText);
		echo "2";
	break;
	
	case 'loadPosc':
		$sqlText = "select * from places order by name_place";
		$dtPos = $dbEx->selSql($sqlText);
		$rslt = '<table cellpadding="2" cellspacing="0" width="700" border="0" class="tblListBack" align="center">';
		if($dbEx->numrows>0){
			$rslt .='<tr class="showItem"><td width="5%">Num</td><td width="35%">Position name</td><td width="35%">Position level</td><td width="20%">Description</td></tr>';
			$n = 1;
			foreach($dtPos as $dtP){
				if($dtP['NIVEL_PLACE']==2){
					$nivel = "SUPERVISOR OR MANAGER";
				}
				else if($dtP['NIVEL_PLACE']==1){
					$nivel = "AGENT OR ADMINISTRATIVE";	
				}
				$rslt .='<tr class="rowCons"><td>'.$n.'</td><td>'.$dtP['NAME_PLACE'].'</td><td>'.$nivel.'</td><td>'.$dtP['DESC_PLACE'].'</td></tr>';	
				$n = $n+1;	
			}
			$rslt .='<tr><td colspan="4" style="cursor:pointer" title="Click to add new position"><b><input type="button" value="+" onclick="formNewPosc()" class="btn"></b></td></tr>';
		}
		else{
			$rslt .='<tr><td colspan="4">No matches</td></tr>';	
		}
		$rslt .='</table><br><br>';
		$rslt .='<div id="lyNewPosc" style="display:none"></div>';
		echo $rslt;
	break;
	
	case 'formNewPosc':
		$rslt = '<table cellpadding="2" cellspacing="0" width="800" border="0" class="tblListBack" align="center">';
		$rslt .= '<tr><td class="showItem" colspan="2" align="center">Position record</td></tr>';
		$rslt .='<tr><td class="itemForm">Position name: </td><td><input type="text" id="txtNamePosc" size="35" class="txtPag"></td></tr>';
		$rslt .='<tr><td class="itemForm">Position description: </td><td><textarea id="txtDesc" class="txtPag" cols="50" rows="3"></textarea></td></tr>';
		$rslt .='<tr><td class="itemForm">Position level: </td>';
		$rslt .='<td><select id="lsLevel" class="txtPag"><option value="0">Select a position</option><option value="1">AGENT OR ADMINISTRATIVE</option><option value="2">SUPERVISOR OR MANAGER</option></select></td></tr>';
		$rslt .='<tr><td colspan="2" align="center"><input type="button" class="btn" value="Save" onclick="savePosc()"></td></tr></table>';
		echo $rslt;
	break;
	
	case 'savePosc':
		$sqlText = "insert into places set name_place='".$_POST['namePosc']."', nivel_place=".$_POST['level'].", desc_place='".$_POST['descrip']."'";
		$dbEx->insSql($sqlText);
		echo "2";
	break;
	
	case 'loadPlacexDep':
		$sqlText = "select distinct(pd.id_placexdep) as idPD, name_account, name_place, name_depart, name_role from placexdep pd inner join places p on pd.id_place=p.id_place inner join depart_exc d on d.id_depart=pd.id_depart inner join user_roles u on u.id_role = pd.id_role inner join account ac on pd.id_account=ac.id_account order by name_account, name_depart, name_place, name_role";
		$dtPdep = $dbEx->selSql($sqlText);
		$n = 1;
		$rslt = '<table cellpadding="2" cellspacing="0" width="700" border="0" class="tblListBack" align="center">';
		$rslt .='<tr><td class="showItem" colspan="6" align="center">Positions by department</td></tr>';
		$rslt .='<tr class="showItem"><td width="5%">Num</td><td>ID</td><td width="23%">Account</td><td width="23%">Department</td><td width="23%">Position</td><td width="23%">Role</td></tr>';
		foreach($dtPdep as $dtPd){
			$rslt .='<tr class="rowCons"><td>'.$n.'</td><td>'.$dtPd['idPD'].'</td><td>'.$dtPd['name_account'].'</td><td>'.$dtPd['name_depart'].'</td><td>'.$dtPd['name_place'].'</td><td>'.$dtPd['name_role'].'</td></tr>';	
			$n = $n+1;
		}
		$rslt .='<tr><td colspan="5" style="cursor:pointer" title="Click to add new position by department"><b><input type="button" value="+" onclick="formNewPxDep()" class="btn"></b></td></tr>';
		$rslt .='</table><br><br>';
		$rslt .='<div id="lyNewPxDep" style="display:none"></div>';
		echo $rslt;	
	break;
	
	case 'formNewPxDep':
		$sqlText = "select * from account where account_status='A' order by name_account";
		$dtAc = $dbEx->selSql($sqlText);
		$optAc = "";
		if($dbEx->numrows>0){
			foreach($dtAc as $dtA){
				$optAc .='<option value="'.$dtA['ID_ACCOUNT'].'">'.$dtA['NAME_ACCOUNT'].'</option>';	
			}	
		}
		$sqlText = "select * from depart_exc where status_depart=1 order by name_depart";
		$dtDep = $dbEx->selSql($sqlText);
		$optDep = "";
		if($dbEx->numrows>0){
			foreach($dtDep as $dtD){
				$optDep .='<option value="'.$dtD['ID_DEPART'].'">'.$dtD['NAME_DEPART'].'</option>';
			}
		}
		$sqlText = "select * from places order by name_place";
		$dtPosc = $dbEx->selSql($sqlText);
		$optPosc = "";
		if($dbEx->numrows>0){
			foreach($dtPosc as $dtP){
				$optPosc .='<option value="'.$dtP['ID_PLACE'].'">'.$dtP['NAME_PLACE'].'</option>';
			}	
		}
		$sqlText = "select * from user_roles";
		$dtRol = $dbEx->selSql($sqlText);
		$optRol = "";
		if($dbEx->numrows>0){
			foreach($dtRol as $dtR){
				$optRol .='<option value="'.$dtR['ID_ROLE'].'">'.$dtR['NAME_ROLE'].'</option>';
			}	
		}
		
		$rslt = '<table cellpadding="2" cellspacing="0" width="800" border="0" class="tblListBack" align="center">';
		$rslt .='<tr><td class="showItem" colspan="2" align="center">Position by department record</td></tr>';
		$rslt .='<tr><td class="itemForm">Account: </td>';
		$rslt .='<td><select id="lsAccount" class="txtPag"><option value="0">Select a Account</option>'.$optAc.'</select></td></tr>';
		$rslt .='<tr><td class="itemForm">Department: </td>';
		$rslt .='<td><select id="lsDepart" class="txtPag"><option value="0">Select a Department</option>'.$optDep.'</select></td></tr>';
		$rslt .='<tr><td class="itemForm">Position: </td>';
		$rslt .='<td><select id="lsPosc" class="txtPag"><option value="0">Select a Position</option>'.$optPosc.'</select></td></tr>';
		$rslt .='<tr><td class="itemForm">Role: </td>';
		$rslt .='<td><select id="lsRol" class="txtPag"><option value="0">Select a Role</option>'.$optRol.'</select></td></tr>';
		$rslt .='<tr><td colspan="2" align="center"><input type="button" class="btn" value="Save" onclick="savePxDep()"></td></tr></table>';
		
		echo $rslt;
		
	break;
	
	case 'savePxDep':
		$sqlText = "select * from placexdep where id_account=".$_POST['account']." and id_place=".$_POST['posicion']." and id_depart=".$_POST['depart']." and id_role=".$_POST['rol'];
		$dtPxDep = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt = 1;	
		}
		else{
			$sqlText = "insert into placexdep set id_account=".$_POST['account'].", id_place=".$_POST['posicion'].", id_depart=".$_POST['depart'].", id_role=".$_POST['rol'];	
			$dbEx->insSql($sqlText);
			$rslt = 2;
		}
		echo $rslt;
	break;

	case 'apSetup':
		//Lista de Tipos de AP
		$sqlText = "select id_tpap,name_tpap, has_start_date, has_end_date, has_time, ".
					"date_format(start_date,'%d/%m/%Y') start_date, date_format(end_date,'%d/%m/%Y') end_date, ".
					"affects_salary, inactive_employee, appr_area, appr_workforce, appr_hr, appr_generalman ".
					"from type_ap ";

		$dtStp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt = '<table class="backTablaMain" width="800px" cellpadding="2" cellspacing="2" border="1">';
			$rslt .='<tr><th class="showItem" colspan="6"><b>Personnel actions types setup</b></th>'.
					'<th class="showItem" colspan="4">Approvers</th><th colspan="3" class="showItem"></th></tr>';
			$rslt .='<tr class="itemForm"><th>AP Type</th></td>'.
					'<th>Has start date</th>'.
					'<th>Has end date</th>'.
					'<th>Has time</th>'.
					'<th>Affect salary?</th>'.
					'<th>inactivate employee?</th>'.
					'<th>Area Manager</th>'.
					'<th>Workforce</th>'.
					'<th>HR</th>'.
					'<th>General Manager</th>'.
					'<th>Effective start date</th>'.
					'<th>Effective end date</th><th></th></tr>';
						
			foreach ($dtStp as $dtS) {
				$rslt .='<tr><td>'.$dtS['name_tpap'].'</td></td>'.
					'<td>'.$dtS['has_start_date'].'</td>'.
					'<td>'.$dtS['has_end_date'].'</td>'.
					'<td>'.$dtS['has_time'].'</td>'.
					'<td>'.$dtS['affects_salary'].'</td>'.
					'<td>'.$dtS['inactive_employee'].'</td>'.
					'<td>'.$dtS['appr_area'].'</td>'.
					'<td>'.$dtS['appr_workforce'].'</td>'.
					'<td>'.$dtS['appr_hr'].'</td>'.
					'<td>'.$dtS['appr_generalman'].'</td>'.
					'<td>'.$dtS['start_date'].'</td>'.
					'<td>'.$dtS['end_date'].'</td>'.
					'<td><img src="images/postediticon.png" title="Click to edit" style="cursor:pointer;" onclick="editAP('.$dtS['id_tpap'].')"></td></tr>';

			}
			$rslt .= '<tr><td colspan="13" align="center" class="showItem">';
			$rslt .= '<input type="button" class="btn" value="Create new type" onclick="newAP()">';
			$rslt .= '</td></tr>';
			$rslt .= '</table><br/>';
			$rslt .= '<div id="lyAPType"></div>';
		}

		echo $rslt;

	break;

	case 'newAP':
		$rslt = cargaPag("../mtto/formAP.php");

		echo $rslt;
	break;

	case 'saveAP':

		//Validar que no exista una ap con el mismo nombre
		$sqlText = "select count(1) c from type_ap where name_tpap = '".$_POST['name']."'";
		$dtC = $dbEx->selSql($sqlText);

		if($dtC['0']['c'] > 0 ){
			$rslt = -1;
		}
		else{

			$salary = "";
			if($_POST['salary'] == "igual"){$salary = "="; }
			else if($_POST['salary'] == "mas"){$salary = "+";} 
			else if($_POST['salary'] == "menos"){$salary = "-";} 

			try{
				$sqlText = "insert into type_ap(name_tpap, has_start_date, has_end_date, has_time, ".
						"appr_area, appr_workforce, appr_hr, appr_generalman, ".
						"affects_salary, inactive_employee ) ".
					"values('".$_POST['name']."', ".
							"'".$_POST['startDate']."',".
							"'".$_POST['endDate']."',".
							"'".$_POST['time']."', ".
							"'".$_POST['areaManager']."', ".
							"'".$_POST['workforce']."', ".
							"'".$_POST['hr']."', ".
							"'".$_POST['generalManager']."', ".
							"'".$salary."', ".
							"'".$_POST['inactive']."' )";

				$dbEx->insSql($sqlText);

				$sqlText = "select id_tpap from type_ap where name_tpap = '".$_POST['name']."'";
				$dtAP = $dbEx->selSql($sqlText);

				if($dbEx->numrows > 0){ 
					$rslt = $dtAP['0']['id_tpap'];
				}

			}
			catch(Exception $e){
				$rslt = -2;
			}
		}

		echo $rslt;

	break;

	case 'editAP':
		$rslt = cargaPag("../mtto/formEditAP.php");

		$sqlText = "select name_tpap, has_start_date, has_end_date, has_time, ".
				" date_format(end_date,'%d/%m/%Y') end_date,".
				" appr_area, appr_workforce, appr_hr, appr_generalman, ".
				" affects_salary, inactive_employee ".
				"from type_ap where id_tpap = ".$_POST['id_tpAP'];

		$dtAp = $dbEx->selSql($sqlText);

		$selY = "";
		$selN = "";
		if($dtAp['0']['has_start_date'] == 'Y'){
			$selY = "selected";
		}else{
			$selN = "selected";
		}

		$optFechaIni = '<option value="Y" '.$selY.' >Yes</option>';
		$optFechaIni .= '<option value="N" '.$selN.' >No</option>';

		$selY = "";
		$selN = "";
		if($dtAp['0']['has_end_date'] == 'Y'){
			$selY = "selected";
		}else{
			$selN = "selected";
		}

		$optFechaFin = '<option value="Y" '.$selY.' >Yes</option>';
		$optFechaFin .= '<option value="N" '.$selN.' >No</option>';

		$selY = "";
		$selN = "";
		if($dtAp['0']['has_time'] == 'Y'){
			$selY = "selected";
		}
		else{
			$selN = "selected";
		}

		$optTiempo = '<option value="Y" '.$selY.' >Yes</option>';
		$optTiempo .= '<option value="N" '.$selN.' >No</option>';


		$selIgual = "";
		$selMas = "";
		$selMenos = "";
		if($dtAp['0']['affects_salary'] == '='){
			$selIgual = "selected";
		}
		else if($dtAp['0']['affects_salary'] == '+'){
			$selMas = "selected";
		}
		else if($dtAp['0']['affects_salary'] == '-'){
			$selMenos = "selected";
		}

		$optSalary = '<option value="igual" '.$selIgual.' >=</option>';
		$optSalary .= '<option value="mas" '.$selMas.' >+</option>';
		$optSalary .= '<option value="menos" '.$selMenos.' >-</option>';

		$selY = "";
		$selN = "";
		if($dtAp['0']['inactive_employee'] == 'Y'){
			$selY = "selected";
		}
		else{
			$selN = "selected";
		}

		$optInactive = '<option value="Y" '.$selY.' >Yes</option>';
		$optInactive .= '<option value="N" '.$selN.' >No</option>';

		$selY = "";
		$selN = "";
		if($dtAp['0']['appr_area'] == 'Y'){
			$selY = "selected";
		}
		else{
			$selN = "selected";
		}

		$optArea = '<option value="Y" '.$selY.' >Yes</option>';
		$optArea .= '<option value="N" '.$selN.' >No</option>';

		$selY = "";
		$selN = "";
		if($dtAp['0']['appr_workforce'] == 'Y'){
			$selY = "selected";
		}
		else{
			$selN = "selected";
		}

		$optWorkforce = '<option value="Y" '.$selY.' >Yes</option>';
		$optWorkforce .= '<option value="N" '.$selN.' >No</option>';

		$selY = "";
		$selN = "";
		if($dtAp['0']['appr_hr'] == 'Y'){
			$selY = "selected";
		}
		else{
			$selN = "selected";
		}

		$optHR = '<option value="Y" '.$selY.' >Yes</option>';
		$optHR .= '<option value="N" '.$selN.' >No</option>';

		$selY = "";
		$selN = "";
		if($dtAp['0']['appr_generalman'] == 'Y'){
			$selY = "selected";
		}
		else{
			$selN = "selected";
		}

		$optGenMan = '<option value="Y" '.$selY.' >Yes</option>';
		$optGenMan .= '<option value="N" '.$selN.' >No</option>';



		$rslt = str_replace("<!--id_tpap-->",$_POST['id_tpAP'],$rslt);
		$rslt = str_replace("<!--name_tpap-->",$dtAp['0']['name_tpap'],$rslt);
		$rslt = str_replace("<!--has_start_date-->",$optFechaIni,$rslt);
		$rslt = str_replace("<!--has_end_date-->",$optFechaFin,$rslt);
		$rslt = str_replace("<!--has_time-->",$optTiempo,$rslt);
		$rslt = str_replace("<!--end_date-->",$dtAp['0']['end_date'],$rslt);
		$rslt = str_replace("<!--affects_salary-->",$optSalary,$rslt);
		$rslt = str_replace("<!--inactive_employee-->",$optInactive,$rslt);
		$rslt = str_replace("<!--areaManager-->",$optArea,$rslt);
		$rslt = str_replace("<!--workforce-->",$optWorkforce,$rslt);
		$rslt = str_replace("<!--hr-->",$optHR,$rslt);
		$rslt = str_replace("<!--generalManager-->",$optGenMan,$rslt);

		echo $rslt;

	break;

	case 'saveEditAP':

		if(strlen($_POST['effectiveEnd']) > 0){
			$endDate = $oFec->cvDtoY($_POST['effectiveEnd']);
			$endDate = "'".$endDate."'";
		}
		else{
			$endDate = "null";
		}

		//Validar que no exista una ap con el mismo nombre
		$sqlText = "select count(1) c from type_ap where name_tpap = '".$_POST['name']."' ".
					"and id_tpap <> ".$_POST['id_tpAP'];
		$dtC = $dbEx->selSql($sqlText);

		if($dtC['0']['c'] > 0 ){
			$rslt = -1;
		}
		else{

			$salary = "";
			if($_POST['salary'] == "igual"){$salary = "="; }
			else if($_POST['salary'] == "mas"){$salary = "+";} 
			else if($_POST['salary'] == "menos"){$salary = "-";} 

			try{
				$sqlText = "update type_ap set name_tpap = '".$_POST['name']."'".
							", has_start_date = '".$_POST['startDate']."'".
							", has_end_date = '".$_POST['endDate']."'".
							", has_time = '".$_POST['time']."'".
							", end_date = ".$endDate.
							", appr_area= '".$_POST['areaManager']."'".
							", appr_workforce= '".$_POST['workforce']."'".
							", appr_hr= '".$_POST['hr']."'".
							", appr_generalman= '".$_POST['generalManager']."'".
							", affects_salary= '".$salary."'".
							", inactive_employee= '".$_POST['inactive']."'".
							" where id_tpap = ".$_POST['id_tpAP'];

				$dbEx->insSql($sqlText);

				$rslt = $_POST['id_tpAP'];

			}
			catch(Exception $e){
				$rslt = -2;
			}
		}

		echo $rslt;		

	break;

	case 'holidayForm':
		$rslt = cargaPag("../mtto/formHoliday.php");
		$sqlText = "select geography_code, geography_name ".
					"from geographies ".
					"where geography_type = 'COUNTRY' ".
					"and end_date is null ".
					"order by geography_name";

		$dtGeo = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optG = '<option value="0">Select a country</option>';
			foreach($dtGeo as $dtG){
				$sel = "";
				if($dtG['geography_code'] == 'SV'){
					$sel = "selected";
				}
				$optG .='<option value="'.$dtG['geography_code'].'" '.$sel.'>'.$dtG['geography_name'].'</option>';
			}
		}
		$rslt = str_replace("<!--optG-->",$optG,$rslt); 
		echo $rslt;
	break;

	case 'saveHoliday':

		$date = $oFec->cvDtoY($_POST['date']);

		//Validar que no exista otro feriado el mismo dia en el mismo pais
		$sqlText = "select count(1) c from holidays ".
			"where geography_code = '".$_POST['geo']."' ".
			"and holiday = '".$date."'";

		$dtC = $dbEx->selSql($sqlText);

		if($dtC['0']['c'] == 0){

			$sqlText = "insert into holidays(holiday_name, holiday, geography_code) ".
				"values('".$_POST['name']."','".$date."','".$_POST['geo']."') ";

			$dbEx->insSql($sqlText);
			$id = $dbEx->insertID;
			echo $id;
		}
		else{
			echo "0";
		}

	break;

	case 'searchHoliday':

		$filtro = "";
		if(strlen($_POST['holidayId'])>0){
			$filtro .=" and holiday_id = ".$_POST['holidayId']." ";
		}
		if(strlen($_POST['name'])>0){
			$filtro .=" and holiday_name = '".$_POST['name']."' ";
		}
		if(strlen($_POST['date'])>0){
			$date = $oFec->cvDtoY($_POST['date']);
			$filtro .=" and holiday = '".$date."' ";
		}
		if($_POST['geo']>0){
			$filtro .=" and geography_code = '".$_POST['geo']."' ";
		}

		$sqlText = "select geography_code, holiday_name, holiday_id, ".
			"date_format(holiday,'%d/%m/%Y') holiday ".
			"from holidays ".
			"where 1 = 1 ".$filtro." ".
			"order by holiday";

		$dtHo = $dbEx->selSql($sqlText);
		$tbl = '<table align="center" cellpadding="2" cellspacing="0" width="600" border="0" class="tblListBack">'.
				'<tr class="showItem"><td>Country</td><td>Name</td><td>Date</td><td></td></tr>';
		foreach ($dtHo as $dtH) {
			$tbl .='<tr class="rowCons" align="center"><td>'.$dtH['geography_code'].'</td>'.
				'<td>'.$dtH['holiday_name'].'</td>'.
				'<td>'.$dtH['holiday'].'</td>'.
				'<td><img src="images/elim.png" title="Click to delete" onclick="delHoliday('.$dtH['holiday_id'].')"></td></tr>';
		}
		$tbl .= '</table>';
		echo $tbl;

	break;

	case 'delHoliday':

		$sqlText = "delete from holidays where holiday_id = ".$_POST['holidayId'];
		$dbEx->updSql($sqlText);
		echo $_POST['holidayId'];

	break;

}
?>