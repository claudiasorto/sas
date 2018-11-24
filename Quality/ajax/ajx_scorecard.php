<?php
//Funciones para Quality	
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
  
switch($_POST['Do']){
	case 'newMonitoring';
		$rslt = cargaPag("../mtto/filtrosMonitoreo.php");
		
		$sqlText = "select e.employee_id, name_place, name_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart = pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and e.user_status=1 and status_plxemp='A'";
		$dtPlaza = $dbEx->selSql($sqlText);
		
		//Variable pasa para verificar que los usuarios tengan empleados para evaluar
		$pasa = false;
		//Si cumple las siguientes condiciones le muestra todos los empleados
		if($dtPlaza['0']['name_depart']=='QUALITY' or $_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
			$sqlText = "select distinct(e.employee_id) as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and pe.status_plxemp='A' and user_status=1 order by firstname";
			$pasa = true;
		}
		//Si es gerente de area solo le muestra los agentes de los departamentes q tiene permisos
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select e.employee_id as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id where user_status=1 and pe.status_plxemp='A' and pe.id_placexdep in (".$_SESSION['permisos'].") order by firstname";
			$pasa = true;
		}
		
		//Si es supervisor, verifica la cuenta a la cual pertenece y le muestra los agentes de esa cuenta 
		else if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select employee_id as employee, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1  order by firstname";
			$pasa = true;
		}
		if($dbEx->numrows>0){
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		if($pasa){
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				$optEmp .= '<option value="0">SELECT A AGENT NAME</option>';
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
				}
			}
		}
		else{
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		$sqlText = "select * from tp_skills order by skill_name";
		$dtSkill = $dbEx->selSql($sqlText);
		$optSkill = '<option value="">SELECT A SKILL</option>';
		foreach($dtSkill as $dtSk){
			$optSkill .='<option value="'.$dtSk['SKILL_ID'].'">'.$dtSk['SKILL_NAME'].'</option>';
		}
		
		
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--evaluador-->",$_SESSION['usr_nombre']." ".$_SESSION['usr_apellido'],$rslt);
		$rslt = str_replace("<!--fechaActual-->",date("d/m/Y"),$rslt);
		$rslt = str_replace("<!--optSkill-->",$optSkill,$rslt);
		echo $rslt;
	break;
	
	case 'getSuperv':
		$sqlText = "select firstname, lastname from employees where employee_id=(select id_supervisor from employees where employee_id=".$_POST['idE'].")";
		$dtSup = $dbEx->selSql($sqlText);
		echo $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];
		
	break;
	
	case 'loadForm':
	
		//Carga forma para Costumer services
		if($_POST['tpEval']==1){
			$comments = "";
			$nItems = 0;
			$nCat = 0;
			$sqlText = "select * from category_form_cs where catcs_status='A' order by id_catcs";
			$dtCat = $dbEx->selSql($sqlText);
			$optItems ='<table class="tblRepQA" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$optItems .='<tr><td colspan="3">Contact ID: <input type="text" id="txtContactId" size="35"></td></tr>';
			$optItems .='<tr><td colspan="3">Account #: <input type="text" id="txtAccount" class="txtPag" size="35"></td></tr>';
			$optItems .='<tr><td colspan="3">Call Reason: <input type="text" id="txtReason" size="35"/></td></tr>';
			foreach($dtCat as $dtC){
				$nCat = $nCat + 1;
				$optItems .='<tr class="showItem"><td colspan="3"><b>'.$dtC['CATCS_NAME'].'</b></td></tr>';
				$sqlText = "select id_formcs, id_catcs, formcs_item, formcs_text from form_monitoring_cs where formcs_status='A' and id_catcs=".$dtC['ID_CATCS']." order by formcs_item";
				$dtitems = $dbEx->selSql($sqlText);
				
				if($dbEx->numrows>0){
					foreach($dtitems as $dtI){
						$nItems = $nItems +1;
						$optItems .= '<tr><td align="center">'.$dtI['formcs_item'].'</td><td>'.$dtI['formcs_text'].'</td>';
						$optItems .='<td align="center"><select id="item" name="item[]" class="txtPag">';
						$optItems .='<option value="0"></option>';
						$optItems .='<option value="1">YES</option>';
						$optItems .='<option value="2">No</option>';
						$optItems .='<option value="3">N/A</option></select></td></tr>';
					}
				}
				
				$comments .='<tr><td colspan="3">'.$dtC['CATCS_NAME'].':<br>';
				if($nCat == 1){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments1" name="txtComments1"></textarea></td></tr>';
				}
				else if($nCat == 2){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments2" name="txtComments2"></textarea></td></tr>';
				}
				else if($nCat == 3){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments3" name="txtComments3"></textarea></td></tr>';
				}
				else if($nCat == 4){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments4" name="txtComments4"></textarea></td></tr>';
				}
				else if($nCat == 5){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments5" name="txtComments5"></textarea></td></tr>';
				}
				else if($nCat == 6){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments6" name="txtComments6"></textarea></td></tr>';
				}
				else if($nCat == 7){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments7" name="txtComments7"></textarea></td></tr>';
				}
				else if($nCat == 8){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments8" name="txtComments8"></textarea></td></tr>';
				}
				else if($nCat == 9){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments9" name="txtComments9"></textarea></td></tr>';
				}
				else if($nCat == 10){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments10" name="txtComments10"></textarea></td></tr>';
				}
				else if($nCat == 11){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments11" name="txtComments11"></textarea></td></tr>';
				}
			}
			$btn = '<tr><td colspan="3" align="center"><input type="button" value="Save" id="btnSaveCS" class="btn" onclick="saveFormCS()"/></td></tr>';
			$rslt = $optItems;
			
		}

		
		//Carga forma de New Service
		else if($_POST['tpEval']==3){
			$nItems = 0;
			$nCat = 0;
			$sqlText = "select * from category_form_newservice where catns_status='A' order by id_catns";
			$dtCat = $dbEx->selSql($sqlText);
			$optItems ='<table class="tblRepQA" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$optItems .='<tr><td colspan="3">Time: <input type="text" id="txtTime" size="35"></td></tr>';
			$optItems .='<tr><td colspan="3">Enrollment ID: <input type="text" id="txtEnroll" class="txtPag" size="35"></td></tr>';
			$optItems .='<tr><td colspan="3">Contact ID: <input type="text" id="txtContact" size="35"/></td></tr>';
			foreach($dtCat as $dtC){
				$nCat = $nCat + 1;
				$optItems .='<tr class="showItemRed"><td colspan="3"><b>'.$dtC['CATNS_NAME'].'</b></td></tr>';
				$sqlText = "select id_formns, id_catns, formns_item, formns_text from form_monitoring_ns where formns_status='A' and id_catns=".$dtC['ID_CATNS']." order by formns_item";
				$dtitems = $dbEx->selSql($sqlText);
				
				if($dbEx->numrows>0){
					foreach($dtitems as $dtI){
						$nItems = $nItems +1;
						$optItems .= '<tr><td align="center">'.$dtI['formns_item'].'</td><td>'.$dtI['formns_text'].'</td>';
						$optItems .='<td align="center"><select id="item" name="item[]" class="txtPag">';
						$optItems .='<option value="0"></option>';
						$optItems .='<option value="1">YES</option>';
						$optItems .='<option value="2">No</option>';
						$optItems .='<option value="3">N/A</option></select></td></tr>';
					}
				}
				
				$comments .='<tr><td colspan="3">'.$dtC['CATNS_NAME'].':<br>';
				if($nCat == 1){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments1" name="txtComments1"></textarea></td></tr>';
				}
				else if($nCat == 2){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments2" name="txtComments2"></textarea></td></tr>';
				}
				else if($nCat == 3){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments3" name="txtComments3"></textarea></td></tr>';
				}
				else if($nCat == 4){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments4" name="txtComments4"></textarea></td></tr>';
				}
				else if($nCat == 5){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments5" name="txtComments5"></textarea></td></tr>';
				}
				else if($nCat == 6){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments6" name="txtComments6"></textarea></td></tr>';
				}
				else if($nCat == 7){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments7" name="txtComments7"></textarea></td></tr>';
				}
				else if($nCat == 8){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments8" name="txtComments8"></textarea></td></tr>';
				}
				else if($nCat == 9){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments9" name="txtComments9"></textarea></td></tr>';
				}
			}
			$btn = '<tr><td colspan="3" align="center"><input type="button" value="Save" id="btnSaveNS" class="btn" onclick="saveFormNS()"/></td></tr>';
			$rslt = $optItems;
		}
		
		//Recupera listas de autofail
		$sqlText = "select * from category_monit_autofail where fail_idfather is NULL";
		$dtCatFail = $dbEx->selSql($sqlText);
		$optCatFail = "";
		foreach($dtCatFail as $dtCatF){
			$optCatFail .='<option value="'.$dtCatF['FAIL_ID'].'">'.$dtCatF['FAIL_TEXT'].'</option>';
		}
		$rslt .='<tr><td colspan="3">Auto-Fail: <select id="lsFail" onchange="getSubFail(this.value)" class="txtPag"><option value="0">--</option>'.$optCatFail.'</select>';
		$rslt .='<span id="lySubFail"><select id="lsSubFail"><option value="0"></option></select></span></td></tr>';

		$rslt .='<tr><td colspan="3">AutoFail: <br><textarea id="txtFail" class="txtPag" cols="100" rows="3"></textarea></td></tr>';
		
		//Carga forma para sales
		if($_POST['tpEval']==2){
			$comments = "";
			$nItems = 0;
			$nCat = 0;
			$sqlText = "select * from category_form_sales where catsales_status='A' order by id_catsales";
			$dtCat = $dbEx->selSql($sqlText);
			$optItems ='<table class="tblRepQA" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$optItems .='<tr><td colspan="3">Enrollement ID: <input type="text" id=enrollID size="35"/>';

			foreach($dtCat as $dtC){
				$nCat = $nCat + 1;
				$optItems .='<tr class="showItemGreen"><td colspan="3"><b>'.$dtC['CATSALES_NAME'].'</b></td></tr>';
				$sqlText = "select id_formsales, id_catsales, formsales_item, formsales_text from form_monitoring_sales where formsales_status='A' and id_catsales=".$dtC['ID_CATSALES']." order by formsales_item";
				$dtitems = $dbEx->selSql($sqlText);
				
				if($dbEx->numrows>0){
					foreach($dtitems as $dtI){
						$nItems = $nItems +1;
						$optItems .= '<tr><td align="center">'.$dtI['formsales_item'].'</td><td>'.$dtI['formsales_text'].'</td>';
						$optItems .='<td align="center"><select id="item" name="item[]" class="txtPag">';
						$optItems .='<option value="0"></option>';
						$optItems .='<option value="1">YES</option>';
						$optItems .='<option value="2">No</option>';
						$optItems .='<option value="3">N/A</option></select></td></tr>';
						
					}
				}

				$comments .='<tr><td colspan="3">'.$dtC['CATSALES_NAME'].':<br>';
				if($nCat == 1){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments1" name="txtComments1"></textarea></td></tr>';
				}
				else if($nCat == 2){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments2" name="txtComments2"></textarea></td></tr>';
				}
				else if($nCat == 3){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments3" name="txtComments3"></textarea></td></tr>';
				}
				else if($nCat == 4){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments4" name="txtComments4"></textarea></td></tr>';
				}
				else if($nCat == 5){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments5" name="txtComments5"></textarea></td></tr>';
				}
				else if($nCat == 6){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments6" name="txtComments6"></textarea></td></tr>';
				}
				else if($nCat == 7){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments7" name="txtComments7"></textarea></td></tr>';
				}
				else if($nCat == 8){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments8" name="txtComments8"></textarea></td></tr>';
				}
				else if($nCat == 9){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments9" name="txtComments9"></textarea></td></tr>';
				}
				else if($nCat == 10){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments10" name="txtComments10"></textarea></td></tr>';
				}
				else if($nCat == 11){
					$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments11" name="txtComments11"></textarea></td></tr>';
				}
			}
			$btn = '<tr><td colspan="3" align="center"><input type="button" value="Save" class="btn" id="btnSaveSales" onclick="saveFormSales()"/></td></tr>';
			$rslt = $optItems;
			
			//Recupera la lista de autofail para sales
			$sqlText = "select * from category_autofail_sales order by failsales_text";
			$dtFail = $dbEx->selSql($sqlText);
			$optCatFail = "";
			foreach($dtFail as $dtF){
				$optCatFail .='<option value="'.$dtF['FAILSALES_ID'].'">'.$dtF['FAILSALES_TEXT'].'</option>';
			}
			$rslt .='<tr><td colspan="3">Auto-Fail: <select id="lsFail" class="txtPag"><option value="0">--</option>'.$optCatFail.'</select></td></tr>';
			$rslt .='<tr><td colspan="3">AutoFail: <br><textarea id="txtFail" class="txtPag" cols="100" rows="3"></textarea></td></tr>';
		}
		
		
		//Carga Evaluacion para chat
		if($_POST['tpEval']==4){
			$nItems = 0;
			$nCat = 0;
			$sqlText = "select * from category_form_chat where catchat_status='A' order by id_catchat";
			$dtCat = $dbEx->selSql($sqlText);
			$optItems ='<table class="tblRepQA" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$optItems .='<tr><td colspan="3">Account#: <input type="text" id="txtAccount" size="35"></td></tr>';
			$optItems .='<tr><td colspan="3">Reason for chat: <input type="text" id="txtReason" class="txtPag" size="50"></td></tr>';
			foreach($dtCat as $dtC){
				$nCat = $nCat + 1;
				$optItems .='<tr class="showItemChat"><td colspan="3"><b>'.$dtC['CATCHAT_NAME'].'</b></td></tr>';
				$sqlText = "select id_formchat, id_catchat, formchat_item, formchat_text from form_monitoring_chat where formchat_status='A' and id_catchat=".$dtC['ID_CATCHAT']." order by formchat_item";
				$dtitems = $dbEx->selSql($sqlText);
				
				if($dbEx->numrows>0){
					foreach($dtitems as $dtI){
						$nItems = $nItems +1;
						$optItems .= '<tr><td align="center">'.$dtI['formchat_item'].'</td><td>'.$dtI['formchat_text'].'</td>';
						$optItems .='<td align="center"><select id="item" name="item[]" class="txtPag">';
						$optItems .='<option value="0"></option>';
						$optItems .='<option value="1">YES</option>';
						$optItems .='<option value="2">No</option>';
						$optItems .='<option value="3">N/A</option></select></td></tr>';
					}
				}
			}
			$comments .='<tr><td colspan="3">Comments: <br>';
			$comments .='<textarea class="txtPag" cols="100" rows="3" id="txtComments" name="txtComments"></textarea></td></tr>';
			$btn = '<tr><td colspan="3" align="center"><input type="button" value="Save" class="btn" onclick="saveFormChat()"/></td></tr>';
			$rslt = $optItems;
			
			//Recupera la lista de autofail para chat
			$sqlText = "select * from category_autofail_chat order by failchat_text";
			$dtFail = $dbEx->selSql($sqlText);
			$optCatFail = "";
			foreach($dtFail as $dtF){
				$optCatFail .='<option value="'.$dtF['FAILCHAT_ID'].'">'.$dtF['FAILCHAT_TEXT'].'</option>';
			}
			$rslt .='<tr><td colspan="3">Auto-Fail: <select id="lsFail" class="txtPag"><option value="0">--</option>'.$optCatFail.'</select></td></tr>';
			$rslt .='<tr><td colspan="3">AutoFail: <br><textarea id="txtFail" class="txtPag" cols="100" rows="3"></textarea></td></tr>';
		
		}
		$rslt .= '<br><br>'.$comments;
		$loading = '<tr><td colspan="3" align="center"><div class="loadP" id="lyMsg" style="display:none;"><img src="images/PleaseWait.gif" width="400"></div></td></tr>';
		$rslt .='<tr><td colspan="3">'.$loading.'</td></tr>';
		$rslt .= $btn;
		$rslt .='</table>';
		$rslt .='<input type="hidden" id="nCat" value="'.$nCat.'">';
		$rslt .='<input type="hidden" id="nItems" value="'.$nItems.'">';
		$rslt .='<input type="hidden" id="idSk" value="'.$_POST['tpSkill'].'">';
		
		echo $rslt;
	break;
	
	//Obtiene las subcategorias de Auto fail
	case 'getSubFail':
		$rslt = '<select id="lsSubFail"><option value="0"></option>';
		if($_POST['idF']>0){
			$sqlText = "select * from category_monit_autofail where fail_idfather=".$_POST['idF'];
			$dtFail = $dbEx->selSql($sqlText);
			foreach($dtFail as $dtF){
				$rslt .='<option value="'.$dtF['FAIL_ID'].'">'.$dtF['FAIL_TEXT'].'</option>';
			}	
		}
		$rslt .='</select>';
		echo $rslt;
	break;
	
	//Guarda el form de monitoreo de CS
	case 'saveFormCS':
		$fechaActual = date("Y-m-d");
		$items = $_POST['arrayItems'];
		$item = explode(" ",$items);
		/*$comments = $_POST['arrayComments'];
		$comment = explode(" ",$comments);*/
		$n=0;
		$m = 0;
		$valorEva = 0;
		$totalY = 0;
		$totalN = 0;
		
		//Evalua si el q crea la evaluacion es de QA para guardar q tipo de usuario hizo la evaluacion
		$sqlText = "select name_depart from plazaxemp pe inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join depart_exc d on pd.id_depart=d.id_depart where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
		$dtQa = $dbEx->selSql($sqlText);
		$maker = "O";
		if($dtQa['0']['name_depart']=='QUALITY'){
			$maker = "Q";
		}
		
		//Crea la evaluacion de QA para Customer services
		if($_POST['idSkill']>0){
			$sqlText = "insert into monitoringcs_emp set employee_id=".$_POST['agente'].", 
			qa_agent=".$_SESSION['usr_id'].", 
			monitcsemp_date='".$fechaActual."', 
			monitcsemp_contactid='".$_POST['contactId']."', 
			monitcsemp_callreason='".$_POST['razon']."', 
			monitcsemp_account='".$_POST['cuenta']."', 
			monitcsemp_fail='".addslashes($_POST['fail'])."', 
			monitcsemp_comment1='".addslashes($_POST['comment1'])."', 
			monitcsemp_comment2='".addslashes($_POST['comment2'])."', 
			monitcsemp_comment3='".addslashes($_POST['comment3'])."', 
			monitcsemp_comment4='".addslashes($_POST['comment4'])."',
			monitcsemp_comment5='".addslashes($_POST['comment5'])."',
			monitcsemp_comment6='".addslashes($_POST['comment6'])."', 
			monitcsemp_comment7='".addslashes($_POST['comment7'])."', 
			monitcsemp_comment8='".addslashes($_POST['comment8'])."', 
			monitcsemp_comment9='".addslashes($_POST['comment9'])."', 
			monitcsemp_comment10='".addslashes($_POST['comment10'])."', 
			monitcsemp_comment11='".addslashes($_POST['comment11'])."', skill_id=".$_POST['idSkill'].", monitcsemp_maker='".$maker."', monitcsemp_averages=".$_POST['query'];
		}
		else{
			$sqlText = "insert into monitoringcs_emp set employee_id=".$_POST['agente'].", 
			qa_agent=".$_SESSION['usr_id'].", 
			monitcsemp_date='".$fechaActual."', 
			monitcsemp_contactid='".$_POST['contactId']."', 
			monitcsemp_callreason='".$_POST['razon']."', 
			monitcsemp_account='".$_POST['cuenta']."', 
			monitcsemp_fail='".addslashes($_POST['fail'])."', 
			monitcsemp_comment1='".addslashes($_POST['comment1'])."', 
			monitcsemp_comment2='".addslashes($_POST['comment2'])."', 
			monitcsemp_comment3='".addslashes($_POST['comment3'])."', 
			monitcsemp_comment4='".addslashes($_POST['comment4'])."', 
			monitcsemp_comment5='".addslashes($_POST['comment5'])."', 
			monitcsemp_comment6='".addslashes($_POST['comment6'])."', 
			monitcsemp_comment7='".addslashes($_POST['comment7'])."', 
			monitcsemp_comment8='".addslashes($_POST['comment8'])."', 
			monitcsemp_comment9='".addslashes($_POST['comment9'])."', 
			monitcsemp_comment10='".addslashes($_POST['comment10'])."', 
			monitcsemp_comment11='".addslashes($_POST['comment11'])."', monitcsemp_maker='".$maker."', monitcsemp_averages=".$_POST['query'];
		}
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_monitcsemp) as IdEva from monitoringcs_emp where employee_id=".$_POST['agente'];
		$dtEva = $dbEx->selSql($sqlText);
		
		$sqlText = "select * from category_form_cs where catcs_status='A' order by id_catcs";
		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
		$valorCatAdicional = 0;
		foreach($dtCat as $dtCt){
			$flag = true;
			$sqlText = "select id_formcs, id_catcs, formcs_item, formcs_text from form_monitoring_cs where formcs_status='A' and id_catcs=".$dtCt['ID_CATCS']." order by formcs_item";
			$dtitems = $dbEx->selSql($sqlText);
			foreach($dtitems as $dtIt){
				if($item[$m]==1 or $item[$m]==2 and $flag==true){
					$flag = false;
				}
				$m = $m+1;
			}
			if($flag == false){
				$nCatDistribuir = $nCatDistribuir + 1;
			}
			else if($flag==true){
				$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATCS_RATE'];
			}
		}
		if($nCatDistribuir >0){
				$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		}
		
		
		foreach($dtCat as $dtC){
			$sqlText = "select id_formcs, id_catcs, formcs_item, formcs_text from form_monitoring_cs where formcs_status='A' and id_catcs=".$dtC['ID_CATCS']." order by formcs_item";
			$dtitems = $dbEx->selSql($sqlText);
			$totalItems = 0;
			$valorPregunta = 0;
			//Id del primer item por categoria
			$idIni = $n;
			//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
			foreach($dtitems as $dtIt){
				if($item[$n]==1 or $item[$n]==2){
					$totalItems = $totalItems +1;
				}
				$n = $n+1;
			}
			//Id del ultimo item de la categoria
			$idFin = $n-1;
			if($totalItems>0){
				$valorPregunta = ($dtC['CATCS_RATE'] + $valorCatAdicional)/$totalItems;
			}
			foreach($dtitems as $dtI){
				if($item[$idIni]==1){
					$respuesta = "Y";
					$totalY = $totalY + 1;
					$valor = $valorPregunta;
					
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$totalN = $totalN + 1;
					$valor = 0;
					
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "insert into itemcs_monitoring set itemcs_total='".$valor."', id_monitcsemp=".$dtEva['0']['IdEva'].", id_formcs=".$dtI['id_formcs'].", itemcs_resp='".$respuesta."'";		
				
				$dbEx->insSql($sqlText);
				//$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		
		//Verifica si selecciono una categoria de Auto fail y asigna valor de 0 a la evaluacion
		if($_POST['listSubFail']>0){
			$sqlText = "update monitoringcs_emp set monitcsemp_qualification='0', fail_id=".$_POST['listSubFail']." where id_monitcsemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		else{
			if($totalY>0){
				$valorEva = ($totalY/($totalY + $totalN))*100;
			}
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringcs_emp set monitcsemp_qualification='".number_format($valorEva,0)."' where id_monitcsemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		echo $dtEva['0']['IdEva'];
		
	break;
	
	
	//Guarda la evaluacion de Sales 
	case 'saveFormSales':
		$fechaActual = date("Y-m-d");
		$items = $_POST['arrayItems'];
		$item = explode(" ",$items);
		$n=0;
		$m = 0;
		$valorEva = 0;
		$totalY = 0;
		$totalN = 0;
		
		//Evalua si el q crea la evaluacion es de QA para guardar q tipo de usuario hizo la evaluacion
		$sqlText = "select name_depart from plazaxemp pe inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join depart_exc d on pd.id_depart=d.id_depart where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
		$dtQa = $dbEx->selSql($sqlText);
		$maker = "O";
		if($dtQa['0']['name_depart']=='QUALITY'){
			$maker = "Q";
		}
		
		//Crea la evaluacion de QA para sales
		if($_POST['idSkill']>0){
			$sqlText = "insert into monitoringsales_emp set employee_id=".$_POST['agente'].", 
			qa_agent=".$_SESSION['usr_id'].", 
			monitsales_date='".$fechaActual."', 
			monitsales_enrollid='".$_POST['enrollId']."', 
			monitsales_fail='".addslashes($_POST['fail'])."', 
			monitsales_comment1='".addslashes($_POST['comment1'])."', 
			monitsales_comment2='".addslashes($_POST['comment2'])."', 
			monitsales_comment3='".addslashes($_POST['comment3'])."', 
			monitsales_comment4='".addslashes($_POST['comment4'])."', 
			monitsales_comment5='".addslashes($_POST['comment5'])."', 
			monitsales_comment6='".addslashes($_POST['comment6'])."', 
			monitsales_comment7='".addslashes($_POST['comment7'])."', 
			monitsales_comment8='".addslashes($_POST['comment8'])."', 
			monitsales_comment9='".addslashes($_POST['comment9'])."', 
			monitsales_comment10='".addslashes($_POST['comment10'])."', 
			monitsales_comment11='".addslashes($_POST['comment11'])."', skill_id=".$_POST['idSkill'].", monitsales_maker='".$maker."', monitsales_averages=".$_POST['query'];
		}
		else{
			$sqlText = "insert into monitoringsales_emp set employee_id=".$_POST['agente'].", 
			qa_agent=".$_SESSION['usr_id'].", 
			monitsales_date='".$fechaActual."', 
			monitsales_enrollid='".$_POST['enrollId']."', 
			monitsales_fail='".addslashes($_POST['fail'])."', 
			monitsales_comment1='".addslashes($_POST['comment1'])."', 
			monitsales_comment2='".addslashes($_POST['comment2'])."', 
			monitsales_comment3='".addslashes($_POST['comment3'])."', 
			monitsales_comment4='".addslashes($_POST['comment4'])."', 
			monitsales_comment5='".addslashes($_POST['comment5'])."', 
			monitsales_comment6='".addslashes($_POST['comment6'])."', 
			monitsales_comment7='".addslashes($_POST['comment7'])."', 
			monitsales_comment8='".addslashes($_POST['comment8'])."', 
			monitsales_comment9='".addslashes($_POST['comment9'])."', 
			monitsales_comment10='".addslashes($_POST['comment10'])."', 
			monitsales_comment11='".addslashes($_POST['comment11'])."', monitsales_maker='".$maker."', monitsales_averages=".$_POST['query'];	
		}
		
		
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_monitsalesemp) as IdEva from monitoringsales_emp where employee_id=".$_POST['agente'];
		$dtEva = $dbEx->selSql($sqlText);
		
		$sqlText = "select * from category_form_sales where catsales_status='A' order by id_catsales";
		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
		$valorCatAdicional = 0;
		foreach($dtCat as $dtCt){
			$flag = true;
			$sqlText = "select id_formsales, id_catsales, formsales_item, formsales_text from form_monitoring_sales where formsales_status='A' and id_catsales=".$dtCt['ID_CATSALES']." order by formsales_item";
			$dtitems = $dbEx->selSql($sqlText);
			foreach($dtitems as $dtIt){
				if($item[$m]==1 or $item[$m]==2 and $flag==true){
					$flag = false;
				}
				$m = $m+1;
			}
			if($flag == false){
				$nCatDistribuir = $nCatDistribuir + 1;
			}
			else if($flag==true){
				$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATSALES_RATE'];
			}
		}
		if($nCatDistribuir >0){
				$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		}
		
		
		foreach($dtCat as $dtC){
			$sqlText = "select id_formsales, id_catsales, formsales_item, formsales_text from form_monitoring_sales where formsales_status='A' and id_catsales=".$dtC['ID_CATSALES']." order by formsales_item";
			$dtitems = $dbEx->selSql($sqlText);
			$totalItems = 0;
			$valorPregunta = 0;
			//Id del primer item por categoria
			$idIni = $n;
			//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
			foreach($dtitems as $dtIt){
				if($item[$n]==1 or $item[$n]==2){
					$totalItems = $totalItems +1;
				}
				$n = $n+1;
			}
			//Id del ultimo item de la categoria
			$idFin = $n-1;
			if($totalItems>0){
				$valorPregunta = ($dtC['CATSALES_RATE'] + $valorCatAdicional)/$totalItems;
			}
			foreach($dtitems as $dtI){
				if($item[$idIni]==1){
					$respuesta = "Y";
					$totalY = $totalY + 1;
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$totalN = $totalN + 1;
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "insert into itemsales_monitoring set itemsales_total='".$valor."', id_monitsalesemp=".$dtEva['0']['IdEva'].", id_formsales=".$dtI['id_formsales'].", itemsales_resp='".$respuesta."'";		
				
				$dbEx->insSql($sqlText);
				//$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		//Verifica si selecciono una categoria de Auto fail y asigna valor de 0 a la evaluacion
		if($_POST['listFail']>0){
			$sqlText = "update monitoringsales_emp set monitsales_qualification='0', fail_id=".$_POST['listFail']." where id_monitsalesemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		else{
			if($totalY>0){
				$valorEva = ($totalY/($totalY + $totalN))*100;
			}
			
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringsales_emp set monitsales_qualification='".number_format($valorEva,0)."' where id_monitsalesemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		echo $dtEva['0']['IdEva'];
		
	break;
	
	//Guarda el formulario de New service
	case 'saveFormNS':
		$fechaActual = date("Y-m-d");
		$items = $_POST['arrayItems'];
		$item = explode(" ",$items);
		$n=0;
		$m = 0;
		$valorEva = 0;
		$totalY = 0;
		$totalN = 0;
		
		//Evalua si el q crea la evaluacion es de QA para guardar q tipo de usuario hizo la evaluacion
		$sqlText = "select name_depart from plazaxemp pe inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join depart_exc d on pd.id_depart=d.id_depart where pe.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A'";
		$dtQa = $dbEx->selSql($sqlText);
		$maker = "O";
		if($dtQa['0']['name_depart']=='QUALITY'){
			$maker = "Q";
		}
		
		//Crea la evaluacion de QA para New Service
		if($_POST['idSkill']>0){
			$sqlText = "insert into monitoringns_emp 
			set employee_id=".$_POST['agente'].", 
			qa_agent=".$_SESSION['usr_id'].", 
			monitnsemp_date='".$fechaActual."',
			monitnsemp_time='".$_POST['time']."', 
			monitnsemp_enrollid='".$_POST['enrollId']."', 
			monitnsemp_contactid='".$_POST['contact']."', 
			monitnsemp_fail='".addslashes($_POST['fail'])."', 
			monitnsemp_comment1='".addslashes($_POST['comment1'])."', 
			monitnsemp_comment2='".addslashes($_POST['comment2'])."', 
			monitnsemp_comment3='".addslashes($_POST['comment3'])."', 
			monitnsemp_comment4='".addslashes($_POST['comment4'])."', 
			monitnsemp_comment5='".addslashes($_POST['comment5'])."', 
			monitnsemp_comment6='".addslashes($_POST['comment6'])."', 
			monitnsemp_comment7='".addslashes($_POST['comment7'])."', 
			monitnsemp_comment8='".addslashes($_POST['comment8'])."', 
			monitnsemp_comment9='".addslashes($_POST['comment9'])."', skill_id=".$_POST['idSkill'].", monitnsemp_maker='".$maker."', monitnsemp_averages=".$_POST['query'];
		}
		else{
			$sqlText = "insert into monitoringns_emp set employee_id=".$_POST['agente'].",
			 qa_agent=".$_SESSION['usr_id'].", 
			 monitnsemp_date='".$fechaActual."',
			 monitnsemp_time='".$_POST['time']."',  
			 monitnsemp_enrollid='".$_POST['enrollId']."',
			 monitnsemp_contactid='".$_POST['contact']."', 
			 monitnsemp_fail='".addslashes($_POST['fail'])."', 
			 monitnsemp_comment1='".addslashes($_POST['comment1'])."', 
			 monitnsemp_comment2='".addslashes($_POST['comment2'])."', 
			 monitnsemp_comment3='".addslashes($_POST['comment3'])."', 
			 monitnsemp_comment4='".addslashes($_POST['comment4'])."', 
			 monitnsemp_comment5='".addslashes($_POST['comment5'])."', 
			 monitnsemp_comment6='".addslashes($_POST['comment6'])."', 
			 monitnsemp_comment7='".addslashes($_POST['comment7'])."', 
			 monitnsemp_comment8='".addslashes($_POST['comment8'])."', 
			 monitnsemp_comment9='".addslashes($_POST['comment9'])."', monitnsemp_maker='".$maker."', monitnsemp_averages=".$_POST['query'];	
		}
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_monitnsemp) as IdEva from monitoringns_emp where employee_id=".$_POST['agente'];
		$dtEva = $dbEx->selSql($sqlText);
		
		$sqlText = "select * from category_form_newservice where catns_status='A' order by id_catns";
		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
		foreach($dtCat as $dtCt){
			$flag = true;
			$sqlText = "select id_formns, id_catns, formns_item, formns_text from form_monitoring_ns where formns_status='A' and id_catns=".$dtCt['ID_CATNS']." order by formns_item";
			$dtitems = $dbEx->selSql($sqlText);
			foreach($dtitems as $dtIt){
				if($item[$m]==1 or $item[$m]==2 and $flag==true){
					$flag = false;
				}
				$m = $m+1;
			}
			if($flag == false){
				$nCatDistribuir = $nCatDistribuir + 1;
			}
			else if($flag==true){
				$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATNS_RATE'];
			}
		}
		$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		
		foreach($dtCat as $dtC){
			$sqlText = "select id_formns, id_catns, formns_item, formns_text from form_monitoring_ns where formns_status='A' and id_catns=".$dtC['ID_CATNS']." order by formns_item";
			$dtitems = $dbEx->selSql($sqlText);
			$totalItems = 0;
			$valorPregunta = 0;
			//Id del primer item por categoria
			$idIni = $n;
			//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
			foreach($dtitems as $dtIt){
				if($item[$n]==1 or $item[$n]==2){
					$totalItems = $totalItems +1;
				}
				$n = $n+1;
			}
			//Id del ultimo item de la categoria
			$idFin = $n-1;
			if($totalItems>0){
				$valorPregunta = ($dtC['CATNS_RATE'] + $valorCatAdicional)/$totalItems;
			}
			foreach($dtitems as $dtI){
				if($item[$idIni]==1){
					$respuesta = "Y";
					$totalY = $totalY + 1;
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$totalN = $totalN + 1;
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "insert into itemns_monitoring set itemns_total='".$valor."', id_monitnsemp=".$dtEva['0']['IdEva'].", id_formns=".$dtI['id_formns'].", itemns_resp='".$respuesta."'";		
				
				$dbEx->insSql($sqlText);
				//$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		//Verifica si selecciono una categoria de Auto fail y asigna valor de 0 a la evaluacion
		if($_POST['listSubFail']>0){
			$sqlText = "update monitoringns_emp set monitnsemp_qualification='0', fail_id=".$_POST['listSubFail']." where id_monitnsemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		else{
			if($totalY>0){				
				$valorEva = ($totalY/($totalY + $totalN))*100;
			}
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringns_emp set monitnsemp_qualification='".number_format($valorEva,0)."' where id_monitnsemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		echo $dtEva['0']['IdEva'];
	break;
	
	//Guarda la evaluacion de Chat
	case 'saveFormChat':
		$fechaActual = date("Y-m-d");
		$items = $_POST['arrayItems'];
		$item = explode(" ",$items);
		$n=0;
		$m = 0;
		$valorEva = 0;
		$totalY = 0;
		$totalN = 0;
		
		//Crea la evaluacion de QA para Chat
		if($_POST['idSkill']>0){
			$sqlText = "insert into monitoringchat_emp set employee_id=".$_POST['agente'].", qa_agent=".$_SESSION['usr_id'].", monitchatemp_date='".$fechaActual."', monitchatemp_reason='".$_POST['reason']."', monitchatemp_account='".$_POST['account']."', monitchatemp_fail='".$_POST['fail']."', monitchatemp_comment='".$_POST['comment']."', skill_id=".$_POST['idSkill'];
		}
		else{
			$sqlText = "insert into monitoringchat_emp set employee_id=".$_POST['agente'].", qa_agent=".$_SESSION['usr_id'].", monitchatemp_date='".$fechaActual."', monitchatemp_reason='".$_POST['reason']."', monitchatemp_account='".$_POST['account']."', monitchatemp_fail='".$_POST['fail']."', monitchatemp_comment='".$_POST['comment']."'";
				
		}
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_monitchatemp) as IdEva from monitoringchat_emp where employee_id=".$_POST['agente'];
		$dtEva = $dbEx->selSql($sqlText);
		
		$sqlText = "select * from category_form_chat where catchat_status='A' order by id_catchat";
		$dtCat = $dbEx->selSql($sqlText);
		
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
		foreach($dtCat as $dtCt){
			$flag = true;
			$sqlText = "select id_formchat, id_catchat, formchat_item, formchat_text from form_monitoring_chat where formchat_status='A' and id_catchat=".$dtCt['ID_CATCHAT']." order by formchat_item";
			$dtitems = $dbEx->selSql($sqlText);
			foreach($dtitems as $dtIt){
				if($item[$m]==1 or $item[$m]==2 and $flag==true){
					$flag = false;
				}
				$m = $m+1;
			}
			if($flag == false){
				$nCatDistribuir = $nCatDistribuir + 1;
			}
			else if($flag==true){
				$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATCHAT_RATE'];
			}
		}
		$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		
		foreach($dtCat as $dtC){
			$sqlText = "select id_formchat, id_catchat, formchat_item, formchat_text from form_monitoring_chat where formchat_status='A' and id_catchat=".$dtC['ID_CATCHAT']." order by formchat_item";
			$dtitems = $dbEx->selSql($sqlText);
			$totalItems = 0;
			$valorPregunta = 0;
			//Id del primer item por categoria
			$idIni = $n;
			//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
			foreach($dtitems as $dtIt){
				if($item[$n]==1 or $item[$n]==2){
					$totalItems = $totalItems +1;
				}
				$n = $n+1;
			}
			//Id del ultimo item de la categoria
			$idFin = $n-1;
			if($totalItems>0){
				$valorPregunta = ($dtC['CATCHAT_RATE'] + $valorCatAdicional)/$totalItems;
			}
			foreach($dtitems as $dtI){
				if($item[$idIni]==1){
					$respuesta = "Y";
					$totalY = $totalY +1 ;
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$totalN = $totalN + 1;
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}

				$sqlText = "insert into itemchat_monitoring set itemchat_total='".$valor."', id_monitchatemp=".$dtEva['0']['IdEva'].", id_formchat=".$dtI['id_formchat'].", itemchat_resp='".$respuesta."'";		
				
				$dbEx->insSql($sqlText);
				//Contabilizaba el valor de la evaluacion segun las categorias
				//$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		//Verifica si selecciono una categoria de Auto fail y asigna valor de 0 a la evaluacion
		if($_POST['listFail']>0){
			$sqlText = "update monitoringchat_emp set monitchatemp_qualification='0', failchat_id=".$_POST['listFail']." where id_monitchatemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		else{
			if($totalY>0){
				$valorEva = ($totalY/($totalY + $totalN))*100;
			}
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringchat_emp set monitchatemp_qualification='".$valorEva."' where id_monitchatemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		echo $dtEva['0']['IdEva'];
		
	break;
	
	
	//Muestra el formulario de CS guardado segun el parametro IdM enviado
	case 'loadMonitoringCS':
		$sqlText = "select ID_MONITCSEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitcsemp_date,'%d/%m/%Y') as f1, MONITCSEMP_QUALIFICATION, MONITCSEMP_CALLREASON, MONITCSEMP_CONTACTID, MONITCSEMP_ACCOUNT, FAIL_ID, SKILL_ID, MONITCSEMP_FAIL, MONITCSEMP_COMMENT1, MONITCSEMP_COMMENT2, MONITCSEMP_COMMENT3, MONITCSEMP_COMMENT4, MONITCSEMP_COMMENT5, MONITCSEMP_COMMENT6, MONITCSEMP_COMMENT7, MONITCSEMP_COMMENT8, MONITCSEMP_COMMENT9, MONITCSEMP_COMMENT10, MONITCSEMP_COMMENT11, MONITCSEMP_ATTACH  from monitoringcs_emp m inner join employees e on e.employee_id=m.employee_id where id_monitcsemp=".$_POST['idM'];
		$dtMonit = $dbEx->selSql($sqlText);
		
		$nomSkill = "";
		if($dtMonit['0']['SKILL_ID']>0){
			$sqlText = "select skill_name from tp_skills where skill_id=".$dtMonit['0']['SKILL_ID'];
			$dtSkill = $dbEx->selSql($sqlText);
			$nomSkill = $dtSkill['0']['skill_name'];
		}
		
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			
			$tblForm = '<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Customer Service Monitoring Form Number '.$dtMonit['0']['ID_MONITCSEMP'].'</b></td></tr>';
			$tblForm .='<tr>
			<td width="15%"><b>QA: </td>
			<td width="50%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td>
			<td width="15%"><b>Contact ID: </td>
			<td width="20%">'.$dtMonit['0']['MONITCSEMP_CONTACTID'].'</td></tr>';
			$tblForm .='<tr><td width="15%"><b>Agent name: </td><td width="50%">'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td><td  width="15%"><b>Account #: </td><td width="20%">'.$dtMonit['0']['MONITCSEMP_ACCOUNT'].'</td></tr>';
			$tblForm .='<tr><td width="15%"><b>Date: </td><td colspan="3">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td width="15%"><b>Supervisor: </td><td colspan="3">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td width="15%"><b>Call Reason: </td><td colspan="3">'.$dtMonit['0']['MONITCSEMP_CALLREASON'].'</td></tr>';
			$tblForm .='<tr><td width="15%"><b>Skill: </td><td colspan="3">'.$nomSkill.'</td></tr>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formcs.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr>';
			$tblForm .='</table><br>';
			$tblForm.='<table class="tblHead" width="60%" align="center" cellpadding="2" cellspacing="2">';
			
			if($dtMonit['0']['MONITCSEMP_ATTACH']!=""){
				$tblForm .='<tr><td colspan="3">
				<a href="mtto/archivosCS/'.$dtMonit['0']['MONITCSEMP_ATTACH'].'" target="_blank"><img src="images/adjunto.png" alt="Adjunto" width="40" style="cursor:pointer" title="Attachment"></a>
			</td></tr>';	
			}
			else{
				$tblForm .='<tr><td colspan="3">No attachments</td></tr>';
			}
			$tblForm .='<tr><td colspan="3">
			<input type="button" class="btn" value="Change Attachment" onclick="changeAttachCS('.$_POST['idM'].')"/></td></tr>
			<div id="lyDoc"></div>
			</td></tr>';
			
			$sqlText = "select * from itemcs_monitoring where id_monitcsemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_cs f inner join category_form_cs c on f.id_catcs=c.id_catcs where f.id_formcs=".$dtI['ID_FORMCS'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATCS'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItem"><td colspan="3"><b>'.$dtDatosItems['0']['CATCS_NAME'].'</b></td></tr>';
				}
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMCS_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMCS_TEXT'].'</td><td>'.$dtI['ITEMCS_RESP'].'</td></tr>';
				if($dtI['ITEMCS_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMCS_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMCS_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItem"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE &nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITCSEMP_QUALIFICATION'],2).'%</b></td></tr>';
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			
			$sqlText = "select distinct(f.id_catcs) as idC, catcs_name from itemcs_monitoring i inner join form_monitoring_cs f on i.id_formcs=f.id_formcs inner join category_form_cs c on c.id_catcs=f.id_catcs where id_monitcsemp=".$_POST['idM'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtFail['0']['FAIL_IDFATHER'];
				$dtFailF = $dbEx->selSql($sqlText);
				$tblForm .='<tr><td colspan="3">FAIL: '.$dtFailF['0']['FAIL_TEXT'].'->'.$dtFail['0']['FAIL_TEXT'].'</td></tr>';
				$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_FAIL'].'</textarea></td></tr>';
			}
			
			$n = 1;
			foreach($dtCat as $dtC){
				$tblForm .='<tr><td colspan="3">'.$dtC['catcs_name'].'</td></tr>';
				if($n==1){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat1" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT1'].'</textarea></td></tr>';
				}
				else if($n==2){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat2" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT2'].'</textarea></td></tr>';
				}
				else if($n==3){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat3" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT3'].'</textarea></td></tr>';
				}
				else if($n==4){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat4" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT4'].'</textarea></td></tr>';
				}
				else if($n==5){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat5" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT5'].'</textarea></td></tr>';
				}
				else if($n==6){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat6" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT6'].'</textarea></td></tr>';
				}
				else if($n==7){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat7" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT7'].'</textarea></td></tr>';
				}
				else if($n==8){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat8" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT8'].'</textarea></td></tr>';
				}
				else if($n==9){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat9" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT9'].'</textarea></td></tr>';
				}
				else if($n==10){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat10" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT10'].'</textarea></td></tr>';
				}
				else if($n==11){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat11" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT11'].'</textarea></td></tr>';
				}
				$n = $n+1;
			}
			$tblForm .='</table>';
			$rslt = $tblForm;
		}
		else{
			$rslt = -1;	
		}
		echo $rslt;
	break;
	
	//Muestra el formulario de Sales guardado segun el parametro IdM enviado
	case 'loadMonitoringSales':
		$sqlText = "select ID_MONITSALESEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitsales_date,'%d/%m/%Y') as f1, MONITSALES_QUALIFICATION, MONITSALES_ENROLLID, FAIL_ID, SKILL_ID, MONITSALES_FAIL, MONITSALES_COMMENT1, MONITSALES_COMMENT2, MONITSALES_COMMENT3, MONITSALES_COMMENT4, MONITSALES_COMMENT5, MONITSALES_COMMENT6, MONITSALES_COMMENT7, MONITSALES_COMMENT8, MONITSALES_COMMENT9, MONITSALES_COMMENT10, MONITSALES_COMMENT11, MONITSALES_ATTACH  from monitoringsales_emp m inner join employees e on e.employee_id=m.employee_id where id_monitsalesemp=".$_POST['idM'];
		$dtMonit = $dbEx->selSql($sqlText);
		
		$nomSkill = "";
		if($dtMonit['0']['SKILL_ID']>0){
			$sqlText = "select skill_name from tp_skills where skill_id=".$dtMonit['0']['SKILL_ID'];
			$dtSkill = $dbEx->selSql($sqlText);
			$nomSkill = $dtSkill['0']['skill_name'];
		}
		
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			
			$tblForm = '<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Outbound Wireless New Service Number '.$dtMonit['0']['ID_MONITSALESEMP'].'</b></td></tr>';
			$tblForm .='<tr><td width="15%"><b>QA: </td><td width="50%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%"><b>Date: </td><td width="20%">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td><b>Agent name: </td><td>'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td><td><b>Enrollment ID: </td><td>'.$dtMonit['0']['MONITSALES_ENROLLID'].'</td></tr>';
			
			$tblForm .='<tr><td><b>Supervisor: </td><td colspan="3">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td><b>Skill: </td><td colspan="3">'.$nomSkill.'</td></tr>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formsales.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr>';
			$tblForm .='</table><br>';

			$tblForm.='<table class="tblHead" width="60%" align="center" cellpadding="2" cellspacing="2">';
			
			if($dtMonit['0']['MONITSALES_ATTACH']!=""){
				$tblForm .='<tr><td colspan="3">
				<a href="mtto/archivosSales/'.$dtMonit['0']['MONITSALES_ATTACH'].'" target="_blank"><img src="images/adjunto.png" alt="Adjunto" width="40" style="cursor:pointer" title="Attachment"></a>
			</td></tr>';	
			}
			else{
				$tblForm .='<tr><td colspan="3">No attachments</td></tr>';
			}
			$tblForm .='<tr><td colspan="3">
			<input type="button" class="btn" value="Change Attachment" onclick="changeAttachSales('.$_POST['idM'].')"/></td></tr>
			<div id="lyDoc"></div>
			</td></tr>';
			
			$sqlText = "select * from itemsales_monitoring where id_monitsalesemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_sales f inner join category_form_sales c on f.id_catsales=c.id_catsales where f.id_formsales=".$dtI['ID_FORMSALES'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATSALES'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItemGreen"><td colspan="3"><b>'.$dtDatosItems['0']['CATSALES_NAME'].'</b></td></tr>';
				}
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMSALES_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMSALES_TEXT'].'</td><td>'.$dtI['ITEMSALES_RESP'].'</td></tr>';
				if($dtI['ITEMSALES_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMSALES_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMSALES_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItemGreen"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE&nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITSALES_QUALIFICATION'],2).'%</b></td></tr>';
			
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			
			$sqlText = "select distinct(f.id_catsales) as idC, catsales_name from itemsales_monitoring i inner join form_monitoring_sales f on i.id_formsales=f.id_formsales inner join category_form_sales c on c.id_catsales=f.id_catsales where id_monitsalesemp=".$_POST['idM'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_autofail_sales where failsales_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$tblForm .='<tr><td colspan="3">FAIL: '.$dtFail['0']['FAILSALES_TEXT'].'</td></tr>';
				$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_FAIL'].'</textarea></td></tr>';
			}
			
			$n = 1;
			foreach($dtCat as $dtC){
				$tblForm .='<tr><td colspan="3">'.$dtC['catsales_name'].'</td></tr>';
				if($n==1){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat1" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT1'].'</textarea></td></tr>';
				}
				else if($n==2){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat2" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT2'].'</textarea></td></tr>';
				}
				else if($n==3){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat3" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT3'].'</textarea></td></tr>';
				}
				else if($n==4){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat4" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT4'].'</textarea></td></tr>';
				}
				else if($n==5){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat5" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT5'].'</textarea></td></tr>';
				}
				else if($n==6){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat6" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT6'].'</textarea></td></tr>';
				}
				else if($n==7){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat7" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT7'].'</textarea></td></tr>';
				}
				else if($n==8){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat8" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT8'].'</textarea></td></tr>';
				}
				else if($n==9){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat9" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT9'].'</textarea></td></tr>';
				}
				else if($n==10){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat10" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT10'].'</textarea></td></tr>';
				}
				else if($n==11){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat11" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT11'].'</textarea></td></tr>';
				}
				$n = $n+1;
			}
			$tblForm .='</table>';
			$rslt = $tblForm;
		}
		else{
			$rslt = -1;	
		}
		echo $rslt;
	break;
	
	//Muestra la evaluacion guardada segun su Id
	case 'loadMonitoringNS':
		$sqlText = "select ID_MONITNSEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitnsemp_date,'%d/%m/%Y') as f1, MONITNSEMP_QUALIFICATION, MONITNSEMP_TIME, MONITNSEMP_ENROLLID, MONITNSEMP_CONTACTID, FAIL_ID, SKILL_ID, MONITNSEMP_FAIL, MONITNSEMP_COMMENT1, MONITNSEMP_COMMENT2, MONITNSEMP_COMMENT3, MONITNSEMP_COMMENT4, MONITNSEMP_COMMENT5, MONITNSEMP_COMMENT6, MONITNSEMP_COMMENT7, MONITNSEMP_COMMENT8, MONITNSEMP_COMMENT9, MONITNSEMP_ATTACH  from monitoringns_emp m inner join employees e on e.employee_id=m.employee_id where id_monitnsemp=".$_POST['idM'];
		$dtMonit = $dbEx->selSql($sqlText);
		$nomSkill = "";
		if($dtMonit['0']['SKILL_ID']>0){
			$sqlText = "select skill_name from tp_skills where skill_id=".$dtMonit['0']['SKILL_ID'];
			$dtSkill = $dbEx->selSql($sqlText);
			$nomSkill = $dtSkill['0']['skill_name'];
		}
		
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			
			$tblForm = '<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>New Services Monitoring Form NUMBER '.$dtMonit['0']['ID_MONITNSEMP'].'</b></td></tr>';
			$tblForm .='<tr><td width="15%"><b>QA: </td><td width="50%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%"><b>Enrollment ID: </td><td width="20%">'.$dtMonit['0']['MONITNSEMP_ENROLLID'].'</td></tr>';
			$tblForm .='<tr><td><b>Agent name: </td><td>'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td><td><b>Contact ID:</td><td>'.$dtMonit['0']['MONITNSEMP_CONTACTID'].'</td></tr>';
			$tblForm .='<tr><td width="15%"><b>Date: </td><td width="35%">'.$dtMonit['0']['f1'].'</td>';
			
			$tblForm .='<tr><td><b>Supervisor: </td><td colspan="3">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td><b>Time:</td><td colspan="3">'.$dtMonit['0']['MONITNSEMP_TIME'].'</td></tr>';
			$tblForm .='<tr><td><b>Skill: </td><td colspan="3">'.$nomSkill.'</td></tr>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formNewService.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br>';
			
			
			$tblForm.='<table class="tblHead" width="60%" align="center" cellpadding="2" cellspacing="2">';
			
			if($dtMonit['0']['MONITNSEMP_ATTACH']!=""){
				$tblForm .='<tr><td colspan="3">
				<a href="mtto/archivosNS/'.$dtMonit['0']['MONITNSEMP_ATTACH'].'" target="_blank"><img src="images/adjunto.png" alt="Adjunto" width="40" style="cursor:pointer" title="Attachment"></a>
			</td></tr>';	
			}
			else{
				$tblForm .='<tr><td colspan="3">No attachments</td></tr>';
			}
			$tblForm .='<tr><td colspan="3">
			<input type="button" class="btn" value="Change Attachment" onclick="changeAttachNS('.$_POST['idM'].')"/></td></tr>
			<div id="lyDoc"></div>
			</td></tr>';
			
			$sqlText = "select * from itemns_monitoring where id_monitnsemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_ns f inner join category_form_newservice c on f.id_catns=c.id_catns where f.id_formns=".$dtI['ID_FORMNS'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATNS'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItemRed"><td colspan="3"><b>'.$dtDatosItems['0']['CATNS_NAME'].'</b></td></tr>';
				}
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMNS_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMNS_TEXT'].'</td><td>'.$dtI['ITEMNS_RESP'].'</td></tr>';
				if($dtI['ITEMNS_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMNS_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMNS_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItemRed"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE&nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITNSEMP_QUALIFICATION'],2).'%</b></td></tr>';
			
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			
			$sqlText = "select distinct(f.id_catns) as idC, catns_name from itemns_monitoring i inner join form_monitoring_ns f on i.id_formns=f.id_formns inner join category_form_newservice c on c.id_catns=f.id_catns where id_monitnsemp=".$_POST['idM'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtFail['0']['FAIL_IDFATHER'];
				$dtFailF = $dbEx->selSql($sqlText);
				$tblForm .='<tr><td colspan="3">FAIL: '.$dtFailF['0']['FAIL_TEXT'].'->'.$dtFail['0']['FAIL_TEXT'].'</td></tr>';
				$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITNS_FAIL'].'</textarea></td></tr>';
			}
			
			$n = 1;
			foreach($dtCat as $dtC){
				$tblForm .='<tr><td colspan="3">'.$dtC['catns_name'].'</td></tr>';
				if($n==1){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat1" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT1'].'</textarea></td></tr>';
				}
				else if($n==2){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat2" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT2'].'</textarea></td></tr>';
				}
				else if($n==3){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat3" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT3'].'</textarea></td></tr>';
				}
				else if($n==4){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat4" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT4'].'</textarea></td></tr>';
				}
				else if($n==5){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat5" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT5'].'</textarea></td></tr>';
				}
				else if($n==6){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat6" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT6'].'</textarea></td></tr>';
				}
				else if($n==7){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat7" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT7'].'</textarea></td></tr>';
				}
				else if($n==8){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat8" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT8'].'</textarea></td></tr>';
				}
				else if($n==9){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat9" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT9'].'</textarea></td></tr>';
				}
				$n = $n+1;
			}
			$tblForm .='</table>';
			$rslt = $tblForm;
		}
		else{
			$rslt = -1;	
		}
		echo $rslt;
	break;
	
	//Muestra formulario guardado de Chat
	//Muestra el formulario de CS guardado segun el parametro IdM enviado
	case 'loadMonitoringChat':
		$sqlText = "select ID_MONITCHATEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitchatemp_date,'%d/%m/%Y') as f1, MONITCHATEMP_QUALIFICATION, MONITCHATEMP_REASON, MONITCHATEMP_ACCOUNT, FAILCHAT_ID, SKILL_ID, MONITCHATEMP_FAIL, MONITCHATEMP_COMMENT, MONITCHATEMP_ATTACH  from monitoringchat_emp m inner join employees e on e.employee_id=m.employee_id where id_monitchatemp=".$_POST['idM'];
		$dtMonit = $dbEx->selSql($sqlText);
		
		$nomSkill = "";
		if($dtMonit['0']['SKILL_ID']>0){
			$sqlText = "select skill_name from tp_skills where skill_id=".$dtMonit['0']['SKILL_ID'];
			$dtSkill = $dbEx->selSql($sqlText);
			$nomSkill = $dtSkill['0']['skill_name'];
		}
		
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			
			$tblForm = '<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Chat Monitoring Form Number '.$dtMonit['0']['ID_MONITCHATEMP'].'</b></td></tr>';
			$tblForm .='<tr><td width="15%"><b>QA: </td><td colspan="3">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td><b>Agent name: </td><td colspan="3">'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td></tr>';
			$tblForm .='<tr><td><b>Account #: </td><td colspan="3">'.$dtMonit['0']['MONITCHATEMP_ACCOUNT'].'</td></tr>';
			$tblForm .='<tr><td><b>Date: </td><td colspan="3">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td><b>Reason for chat: </td><td colspan="3">'.$dtMonit['0']['MONITCHATEMP_REASON'].'</td></tr>';
			$tblForm .='<tr><td><b>Skill: </td><td colspan="3">'.$nomSkill.'</td></tr>';
			$tblForm .='<tr><td align="right" colspan="4">
			
			<form target="_blank" action="report/xls_formchat.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br/>';
			$tblForm .='<table class="tblHead" width="60%" align="center" cellpadding="2" cellspacing="2">';
			if($dtMonit['0']['MONITCHATEMP_ATTACH']!=""){
				$tblForm .='<tr><td colspan="3">
				<a href="mtto/archivosChat/'.$dtMonit['0']['MONITCHATEMP_ATTACH'].'" target="_blank"><img src="images/adjunto.png" alt="Adjunto" width="40" style="cursor:pointer" title="Attachment"></a>
			</td></tr>';	
			}
			else{
				$tblForm .='<tr><td colspan="3">No attachments</td></tr>';
			}
			$tblForm .='<tr><td colspan="3">
			<input type="button" class="btn" value="Change Attachment" onclick="changeAttachChat('.$_POST['idM'].')"/></td></tr>
			<div id="lyDoc"></div>
			</td></tr>';
			$sqlText = "select * from itemchat_monitoring where id_monitchatemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_chat f inner join category_form_chat c on f.id_catchat=c.id_catchat where f.id_formchat=".$dtI['ID_FORMCHAT'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATCHAT'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItemChat"><td colspan="3"><b>'.$dtDatosItems['0']['CATCHAT_NAME'].'</b></td></tr>';
				}
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMCHAT_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMCHAT_TEXT'].'</td><td>'.$dtI['ITEMCHAT_RESP'].'</td></tr>';
				if($dtI['ITEMCHAT_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMCHAT_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMCHAT_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItemChat"><td colspan="2" align="right"><b>PERCENT CHAT QUALITY SCORE &nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITCHATEMP_QUALIFICATION'],2).'%</b></td></tr>';
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			
			if($dtMonit['0']['FAILCHAT_ID']>0){
				$sqlText = "select * from category_autofail_chat where failchat_id=".$dtMonit['0']['FAILCHAT_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$tblForm .='<tr><td colspan="3">FAIL: '.$dtFail['0']['FAILCHAT_TEXT'].'</td></tr>';
				$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITCHATEMP_FAIL'].'</textarea></td></tr>';
			}
			$tblForm .='<tr><td colspan="3">Comments: </td></tr>';
			$tblForm .='<tr><td colspan="3"><textarea id_txtComment cols="100" rows="3" disabled="disabled">'.$dtMonit['0']['MONITCHATEMP_COMMENT'].'</textarea></td></tr>';
			
			$tblForm .='</table>';
			$rslt = $tblForm;
		}
		else{
			$rslt = -1;	
		}
		echo $rslt;

		
	break;
	
	case 'MonitLog':
		$sqlText = "select e.employee_id, name_place, name_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart = pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and e.user_status=1 and status_plxemp='A'";
		$dtPlaza = $dbEx->selSql($sqlText);
	
		$pasa = false;
		if($dtPlaza['0']['name_depart']=='QUALITY' or $_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
			$sqlText = "select distinct(e.employee_id) as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and pe.status_plxemp='A' and user_status=1 order by firstname";
			$pasa = true;
		}
		//Genente de area solo vera los deptos q tiene permiso
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select e.employee_id as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id where user_status=1 and pe.status_plxemp='A' and pe.id_placexdep in (".$_SESSION['permisos'].") order by firstname ";
			$pasa = true;
		}
		
		//Si es supervisor, verifica la cuenta a la cual pertenece y le muestra los agentes de esa cuenta 
		else if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select employee_id as employee, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1 order by firstname";
			$pasa = true;
		}
		
		
		if($dbEx->numrows>0){
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		if($pasa){
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				$optEmp .= '<option value="0">SELECT A AGENT NAME</option>';
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
				}
			}
		}
		else{
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		$rslt = cargaPag("../mtto/filtros_monitlog.php");
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);

		echo $rslt;
	break;
	
	case 'load_Monitlog':
		$sqlText = "select name_place, name_depart from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart=pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and status_plxemp='A' and user_status=1";
		$dtP = $dbEx->selSql($sqlText);
		
		//Verifica que usuario se ha loggeado
		if($_SESSION['usr_rol']!=""){
			$filtro = " where pe.status_plxemp='A'";
			
			
			//Si el usuario es Agente de QA solo muestra sus evaluaciones
			if($dtP['0']['name_depart']=='QUALITY'){
				$filtro .=" and (qa_agent=".$_SESSION['usr_id']." or e.id_supervisor=".$_SESSION['usr_id']." )";
			}
			else if($_SESSION['usr_rol']=='SUPERVISOR'){
				$filtro .=" and e.id_supervisor=".$_SESSION['usr_id'];
			}
			else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
				$filtro .=" and pe.id_placexdep in (".$_SESSION['permisos'].") ";
			}
			
			if($_POST['idEmp']>0){
				$filtro .=" and e.employee_id=".$_POST['idEmp'];	
			}
			
			//Verifica datos de evaluacion si la evaluacion es para CS
			if($_POST['tpEval']==1){
				$sqlText = " select id_monitcsemp, m.employee_id, qa_agent, date_format(monitcsemp_date,'%d/%m/%Y') as f1, monitcsemp_qualification, firstname, lastname , id_supervisor from employees e inner join monitoringcs_emp m on e.employee_id=m.employee_id inner join plazaxemp pe on e.employee_id=pe.employee_id ";	
		
				if(strlen($_POST['fec_ini'])>0){
					if(strlen($_POST['fec_fin']>0)){
						$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
						$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
						$filtro .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
					}	
				}
				$sqlText .= $filtro." order by id_monitcsemp desc";
				$dtMonit = $dbEx->selSql($sqlText);
				$tblMonit = '<table class="tblResult" width="1000px" align="center" cellpadding="2" cellspacing="2">';
				if($dbEx->numrows>0){
					$tblMonit .='<tr><th colspan="7" align="center"><b>Customer Service Monitoring Form</b></th></tr>';
					$tblMonit .='<tr><td colspan="5">Matches: '.$dbEx->numrows.'</td></tr>';
					$tblMonit .='<tr bgcolor="#FFFFFF" align="center">
					<th width="8%">Evaluation number</th>
					<th width="20%">QA</th>
					<th width="20%">Agent Name</th>
					<th width="20%">Supervisor</th>
					<th width="12%">Date</th>
					<th width="13%">QA PERCENTAGE TOTAL SCORE</th>
					<th></th>
					<th></th></tr>';
					foreach($dtMonit as $dtM){
						$sqlText ="select employee_id, firstname, lastname from employees where employee_id=".$dtM['qa_agent'];
						$dtQa = $dbEx->selSql($sqlText);
						$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dtM['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						
						$tblMonit .='<tr class="rowCons">
						<td onclick="loadMonitoringCS('.$dtM['id_monitcsemp'].')" align="center">'.$dtM['id_monitcsemp'].'</td>
						<td onclick="loadMonitoringCS('.$dtM['id_monitcsemp'].')">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td>
						<td onclick="loadMonitoringCS('.$dtM['id_monitcsemp'].')">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>
						<td onclick="loadMonitoringCS('.$dtM['id_monitcsemp'].')">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td>
						<td align="center" onclick="loadMonitoringCS('.$dtM['id_monitcsemp'].')">'.$dtM['f1'].'</td>
						<td align="center" onclick="loadMonitoringCS('.$dtM['id_monitcsemp'].')">'.number_format($dtM['monitcsemp_qualification'],2).'%</td>
						<td align="center"><input type="image" src="images/update.png" alt="update evaluation" style="cursor:pointer" width="50" title="update form" onclick="updEvaCS('.$dtM['id_monitcsemp'].')"/></td>
						<td align="center"><input type="image" src="images/delete.png" alt="delete form" style="cursor:pointer" width="50" title="delete form" onclick="delEvaCS('.$dtM['id_monitcsemp'].')"/></td>
						</tr>';
					}						
				}	
				else{
					$tblMonit .='<tr><td colspan="5">No matches</td>';	
				}
				$tblMonit .='</table>';
				$rslt = $tblMonit;
			} //Termina de mostrar evaluacion de CS
			
			//Evalua para monitoreo de Sales
			else if($_POST['tpEval']==2){
				$sqlText = " select id_monitsalesemp, m.employee_id, qa_agent, date_format(monitsales_date,'%d/%m/%Y') as f1, monitsales_qualification, firstname, lastname , id_supervisor from employees e inner join monitoringsales_emp m on e.employee_id=m.employee_id inner join plazaxemp pe on e.employee_id=pe.employee_id ";	
		
				if(strlen($_POST['fec_ini'])>0){
					if(strlen($_POST['fec_fin']>0)){
						$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
						$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
						$filtro .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
					}	
				}
				$sqlText .= $filtro." order by id_monitsalesemp desc";
				$dtMonit = $dbEx->selSql($sqlText);
				$tblMonit = '<table class="tblResult" width="900px" align="center" cellpadding="2" cellspacing="2">';
				if($dbEx->numrows>0){
					$tblMonit .='<tr><th colspan="7" align="center"><b>Outbound Wireless New Service</b></th></tr>';
					$tblMonit .='<tr><td colspan="7">Matches: '.$dbEx->numrows.'</td></tr>';
					$tblMonit .='<tr bgcolor="#FFFFFF" align="center">
					<th width="8%">Evaluation number</th>
					<th width="20%">QA</th>
					<th width="20%">Agent Name</th>
					<th width="20%">Supervisor</th>
					<th width="12%">Date</th>
					<th width="13%">QA PERCENTAGE TOTAL SCORE</th>
					<th></th>
					<th></th></tr>';
					foreach($dtMonit as $dtM){
						$sqlText ="select employee_id, firstname, lastname from employees where employee_id=".$dtM['qa_agent'];
						$dtQa = $dbEx->selSql($sqlText);
						$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dtM['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						
						$tblMonit .='<tr class="rowCons">
						<td onclick="loadMonitoringSales('.$dtM['id_monitsalesemp'].')">'.$dtM['id_monitsalesemp'].'</td>
						<td onclick="loadMonitoringSales('.$dtM['id_monitsalesemp'].')">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td>
						<td onclick="loadMonitoringSales('.$dtM['id_monitsalesemp'].')">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>
						<td onclick="loadMonitoringSales('.$dtM['id_monitsalesemp'].')">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td>
						<td align="center" onclick="loadMonitoringSales('.$dtM['id_monitsalesemp'].')">'.$dtM['f1'].'</td>
						<td align="center" onclick="loadMonitoringSales('.$dtM['id_monitsalesemp'].')">'.number_format($dtM['monitsales_qualification'],2).'%</td>
						<td align="center"><input type="image" src="images/update.png" alt="update evaluation" style="cursor:pointer" width="50" title="update evaluation" onclick="updEvaSales('.$dtM['id_monitsalesemp'].')"/></td>
						<td align="center"><input type="image" src="images/delete.png" alt="delete form" style="cursor:pointer" width="50" title="delete form" onclick="delEvaSales('.$dtM['id_monitsalesemp'].')"/></td>
						</tr>';
					}						
				}	
				else{
					$tblMonit .='<tr><td colspan="5">No matches</td>';	
				}
				$tblMonit .='</table>';
				$rslt = $tblMonit;
			}//Termina monitoreo de Sales
		
		
		//Verifica datos de evaluacion si la evaluacion es para New services
			if($_POST['tpEval']==3){
				$sqlText = " select id_monitnsemp, m.employee_id, qa_agent, date_format(monitnsemp_date,'%d/%m/%Y') as f1, monitnsemp_qualification, firstname, lastname , id_supervisor from employees e inner join monitoringns_emp m on e.employee_id=m.employee_id inner join plazaxemp pe on e.employee_id=pe.employee_id ";	
		
				if(strlen($_POST['fec_ini'])>0){
					if(strlen($_POST['fec_fin']>0)){
						$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
						$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
						$filtro .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
					}	
				}
				$sqlText .= $filtro." order by id_monitnsemp desc";
				$dtMonit = $dbEx->selSql($sqlText);
				$tblMonit = '<table class="tblResult" width="900px" align="center" cellpadding="2" cellspacing="2">';
				if($dbEx->numrows>0){
					$tblMonit .='<tr><th colspan="7" align="center"><b>New Service Monitoring Form</b></th></tr>';
					$tblMonit .='<tr><td colspan="7">Matches: '.$dbEx->numrows.'</td></tr>';
					$tblMonit .='<tr bgcolor="#FFFFFF" align="center">
					<th width="8%">Evaluation number</th>
					<th width="20%">QA</th>
					<th width="20%">Agent Name</th>
					<th width="20%">Supervisor</th>
					<th width="12%">Date</th>
					<th width="13%">QA PERCENTAGE TOTAL SCORE</th>
					<th></th>
					<th></th>
					</tr>';
					foreach($dtMonit as $dtM){
						$sqlText ="select employee_id, firstname, lastname from employees where employee_id=".$dtM['qa_agent'];
						$dtQa = $dbEx->selSql($sqlText);
						$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dtM['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						
						$tblMonit .='<tr class="rowCons">
						<td onclick="loadMonitoringNS('.$dtM['id_monitnsemp'].')" align="center">'.$dtM['id_monitnsemp'].'</td>
						<td onclick="loadMonitoringNS('.$dtM['id_monitnsemp'].')">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td>
						<td onclick="loadMonitoringNS('.$dtM['id_monitnsemp'].')">'.$dtM['firstname'].' '.$dtM['lastname'].'</td>
						<td onclick="loadMonitoringNS('.$dtM['id_monitnsemp'].')">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td>
						<td align="center" onclick="loadMonitoringNS('.$dtM['id_monitnsemp'].')">'.$dtM['f1'].'</td>
						<td align="center" onclick="loadMonitoringNS('.$dtM['id_monitnsemp'].')">'.number_format($dtM['monitnsemp_qualification'],2).'%</td>
						<td align="center"><input type="image" src="images/update.png" alt="update evaluation" style="cursor:pointer" width="50" title="update evaluation" onclick="updEvaNS('.$dtM['id_monitnsemp'].')"/></td>
						<td align="center"><input type="image" src="images/delete.png" alt="delete form" style="cursor:pointer" width="50" title="delete form" onclick="delEvaNS('.$dtM['id_monitnsemp'].')"/>
						</tr>';
					}						
				}	
				else{
					$tblMonit .='<tr><td colspan="5">No matches</td>';	
				}
				$tblMonit .='</table>';
				$rslt = $tblMonit;
			} //Termina de mostrar evaluacion de New service
			
			//Verifica datos de evaluacion si la evaluacion es para Chat
			if($_POST['tpEval']==4){
				$sqlText = " select id_monitchatemp, m.employee_id, qa_agent, date_format(monitchatemp_date,'%d/%m/%Y') as f1, monitchatemp_qualification, firstname, lastname , id_supervisor from employees e inner join monitoringchat_emp m on e.employee_id=m.employee_id ";	
		
				if(strlen($_POST['fec_ini'])>0){
					if(strlen($_POST['fec_fin']>0)){
						$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
						$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
						$filtro .=" and monitchatemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
					}	
				}
				$sqlText .= $filtro." order by id_monitchatemp desc";
				$dtMonit = $dbEx->selSql($sqlText);
				$tblMonit = '<table class="tblResult" width="900px" align="center" cellpadding="2" cellspacing="2">';
				if($dbEx->numrows>0){
					$tblMonit .='<tr><th colspan="7" align="center"><b>Chat Monitoring Form</b></th></tr>';
					$tblMonit .='<tr><td colspan="7">Matches: '.$dbEx->numrows.'</td></tr>';
					$tblMonit .='<tr bgcolor="#FFFFFF" align="center"><th width="8%">Evaluation number</th><th width="20%">QA</th><th width="20%">Agent Name</th><th width="20%">Supervisor</th><th width="12%">Date</th><th width="13%">QA PERCENTAGE TOTAL SCORE</th><th></th></tr>';
					foreach($dtMonit as $dtM){
						$sqlText ="select employee_id, firstname, lastname from employees where employee_id=".$dtM['qa_agent'];
						$dtQa = $dbEx->selSql($sqlText);
						$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dtM['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						
						$tblMonit .='<tr class="rowCons"><td onclick="loadMonitoringChat('.$dtM['id_monitchatemp'].'" align="center">'.$dtM['id_monitchatemp'].'</td><td onclick="loadMonitoringChat('.$dtM['id_monitchatemp'].')">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td onclick="loadMonitoringChat('.$dtM['id_monitchatemp'].')">'.$dtM['firstname'].' '.$dtM['lastname'].'</td><td onclick="loadMonitoringChat('.$dtM['id_monitchatemp'].')">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td><td align="center" onclick="loadMonitoringChat('.$dtM['id_monitchatemp'].')">'.$dtM['f1'].'</td><td align="center" onclick="loadMonitoringChat('.$dtM['id_monitchatemp'].')">'.number_format($dtM['monitchatemp_qualification'],2).'%</td><td align="center"><input type="image" src="images/update.png" alt="update evaluation" style="cursor:pointer" width="50" title="update evaluation" onclick="updEvaChat('.$dtM['id_monitchatemp'].')"/></td></tr>';
					}						
				}	
				else{
					$tblMonit .='<tr><td colspan="5">No matches</td>';	
				}
				$tblMonit .='</table>';
				$rslt = $tblMonit;
			} //Termina de mostrar evaluacion de Chat
			
		
		}else{
			$rslt = "-1";
		}
		echo $rslt;
	break;
	
	case 'Reports':
		if($_POST['tpRep']==1){
			$rslt = cargaPag("../mtto/filtrosReportes.php");
			
		}
		else if($_POST['tpRep']==2){
			$rslt = cargaPag("../mtto/filtrosReporteSbs.php");	
		}
		$sqlText = "select e.employee_id, firstname, lastname, name_place, name_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart = pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and e.user_status=1 and status_plxemp='A'";
		$dtPlaza = $dbEx->selSql($sqlText);
		
		//Variable pasa para verificar que los usuarios tengan empleados para evaluar
		$pasa = false;
		//Si cumple las siguientes condiciones le muestra todos los empleados
		if($dtPlaza['0']['name_depart']=='QUALITY' or $_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
			$sqlText = "select distinct(e.employee_id) as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and pe.status_plxemp='A' and user_status=1 order by firstname";
			$pasa = true;
		}
		//Si es gerente de area solo para los q tiene permisos
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select e.employee_id as employee, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id where pe.status_plxemp='A' and user_status=1 and pe.id_placexdep in (".$_SESSION['permisos'].") order by firstname";
			$pasa = true;
		}
		
		//Si es supervisor, verifica la cuenta a la cual pertenece y le muestra los agentes de esa cuenta 
		else if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select employee_id as employee, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1  order by firstname";
			$pasa = true;
		}
		if($dbEx->numrows>0){
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		if($pasa){
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
				}
			}
		}
		else{
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		$optSup = "";
		$optCuenta = "";
		if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='WORKFORCE' or $dtPlaza['0']['name_depart']=='QUALITY')
		{
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
			$dtSup = $dbEx->selSql($sqlText);
			$optSup .='<option value="0">[ALL]</option>';
			foreach($dtSup as $dtS){
				$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
			//Lista de cuentas
			$sqlText = "select * from account where account_status='A' order by name_account ";
			$dtCuenta = $dbEx->selSql($sqlText);
			$optCuenta = '<option value="0">[ALL]</option>';
			foreach($dtCuenta as $dtC){
				$optCuenta .= '<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
			}
			
		}
		else{
			$optSup ='<option value="'.$dtPlaza['0']['employee_id'].'">'.$dtPlaza['0']['firstname']." ".$dtPlaza['0']['lastname'].'</option>';
			
			$optCuenta .='<option value="0"></option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where name_depart='QUALITY' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtQa = $dbEx->selSql($sqlText);
		$optQa = "";
		foreach($dtQa as $dtQ){
			$optQa .='<option value="'.$dtQ['employee_id'].'">'.$dtQ['firstname']." ".$dtQ['lastname'].'</option>';		
		}

		$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optQa-->",$optQa,$rslt);
		echo $rslt;
	break;
	
	case 'getEmployees':
		$sqlText = "select employee_id, firstname, lastname from employees where id_supervisor=".$_POST['idS']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = '<select id="lsEmp">';
		if($dbEx->numrows>0){
			$optEmp .= '<option value="0">[ALL]</option>';
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}	
		}
		else $optEmp .= '<option value="-1"> No agents</option>';
		$optEmp .='</select>';
		echo $optEmp;
	break; 
	
	case 'getMultipleEmployees':
		$sqlText = "select employee_id, firstname, lastname from employees where id_supervisor=".$_POST['idS']." and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = '<select NAME="sel1[]" ID="sel1" SIZE="5" multiple="multiple" style="width: 250px">';
		if($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}	
		}
		else {$optEmp .= '<option value="-1"> No agents</option>';}
		$optEmp .='</select>';
		echo $optEmp;
	break;
	
	case 'loadReportQaDetails':
		$filtroCS = "";
		$filtroSales = "";
		$filtroNS = "";
		$filtroChat = "";
		$filtro = " where 1 ";
		$jointable = " from employees e ";
		$sqlText = "";
		$totales[] = "";
		$filtro2 ="";
		
		//Guardara nombres de los items y contabiliza Y, No y NA-
		$textos[] = "";
		$sumaYesItem[] = "";
		$sumaNoItem[] = "";
		$sumaNAItemp[] = "";
		
		
		
		if(strlen($_POST['fecha_ini'])>0 and strlen($_POST['fecha_fin'])>0){
			$fec_ini  = $oFec->cvDtoY($_POST['fecha_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fecha_fin']);
			$filtroCS .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroSales .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroNS .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroChat .=" and monitchatemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and e.employee_id=".$_POST['emp'];
		}
		if($_POST['qa']>0){
			$filtro .=" and qa_agent =".$_POST['qa'];	
		}
		if($_POST['cuenta']>0){
			$filtro2 =" and ".$_POST['cuenta']."=(select pd.id_account from employees emp inner join plazaxemp pe on emp.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where emp.employee_id=e.employee_id and status_plxemp='A') ";	
		}
		if($_POST['posicion']!='0'){
			$filtroCS .=" and monitcsemp_maker='".$_POST['posicion']."' ";
			$filtroNS .=" and monitnsemp_maker='".$_POST['posicion']."' ";
			$filtroSales .=" and monitsales_maker='".$_POST['posicion']."' ";
			
		}
		//Si el usuario es gerente de area
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$jointable .=" inner join plazaxemp pe on pe.employee_id=e.employee_id ";
			$filtro .=" and pe.id_placexdep in (".$_SESSION['permisos'].") and pe.status_plxemp='A' ";
		}
		
		//Genera reporte para Customer service
		if($_POST['monit']==1){
			$filtro .= $filtroCS;
			$jointable .=" inner join monitoringcs_emp m on m.employee_id=e.employee_id ";
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitcsemp, qa_agent, date_format(monitcsemp_date,'%d/%m/%Y') as f1, monitcsemp_qualification ".$jointable." ".$filtro." ".$filtro2." order by firstname, monitcsemp_date desc";
			
			$sqlItem = "select id_formcs as id, id_catcs, formcs_item as item, formcs_text from form_monitoring_cs where formcs_status='A' order by formcs_item";
			
		}
		//Genera reporte para Sales
		else if($_POST['monit']==2){
			$filtro .= $filtroSales;
			$jointable .=" inner join monitoringsales_emp m on m.employee_id=e.employee_id ";
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitsalesemp, qa_agent, date_format(monitsales_date,'%d/%m/%Y') as f1, monitsales_qualification ".$jointable." ".$filtro." ".$filtro2." order by firstname, monitsales_date desc";
			
			$sqlItem = "select id_formsales as id, id_catsales, formsales_item as item, formsales_text from form_monitoring_sales where formsales_status='A' order by formsales_item";
			
		}
		
		//Genera reporte para New Services
		else if($_POST['monit']==3){
			$filtro .= $filtroNS;
			$jointable .= " inner join monitoringns_emp m on m.employee_id=e.employee_id";
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitnsemp, qa_agent, date_format(monitnsemp_date,'%d/%m/%Y') as f1, monitnsemp_qualification ".$jointable." ".$filtro." ".$filtro2." order by firstname, monitnsemp_date desc";
			$sqlItem = "select id_formns as id, id_catns, formns_item as item, formns_text from form_monitoring_ns where formns_status='A' order by formns_item";
			
		}
		
		//Genera reporte para Chat
		else if($_POST['monit']==4){
			$filtro .= $filtroChat;
			$jointable .="  inner join monitoringchat_emp m on m.employee_id=e.employee_id ";
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitchatemp, qa_agent, date_format(monitchatemp_date,'%d/%m/%Y') as f1, monitchatemp_qualification ".$jointable." ".$filtro." ".$filtro2." order by firstname, monitchatemp_date desc";
			
			$sqlItem = "select id_formchat as id, id_catchat, formchat_item as item, formchat_text from form_monitoring_chat where formchat_status='A' order by formchat_item";
		}
		
		
		$dtEva = $dbEx->selSql($sqlText);
		$tblResult ='<form target="_blank" action="report/xls_ReportQADetails.php" method="post">
		<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
		<input type="hidden" name="sqlText" value="'.$sqlText.'">
		<input type="hidden" name="sqlItem" value="'.$sqlItem.'">
		<input type="hidden" name="monit" value="'.$_POST['monit'].'"></form>';

		$tblResult .='<div class="scroll">';
		$tblResult .= '<table class="tblHead" align="center" cellpadding="6" cellspacing="1">';
		if($dbEx->numrows>0){
			
			$dtItem = $dbEx->selSql($sqlItem);
			//Evalua reporte para reporte de detalles de Customer services
			
			if($_POST['monit']==1){
				$tblResult .='<tr class="showItem"><td width="3%"><font>BADGE</td><td width="100px"><font>EMPLOYEE</td><td>Date</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$textos[$dtI['item']] = $dtI['formcs_text'];
					$sumaYesItem[$dtI['item']] = 0;
					$sumaNoItem[$dtI['item']] = 0;
					$sumaNAItem[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemcs, itemcs_total, itemcs_resp, formcs_item from itemcs_monitoring it inner join form_monitoring_cs f on f.id_formcs = it.id_formcs where id_monitcsemp=".$dtEv['id_monitcsemp']." and formcs_status='A' order by formcs_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$tblResult .='<tr class="rowCons" onclick="loadMonitoringCSbyReport('.$dtEv['id_monitcsemp'].')"><td><font size="-2">'.$dtEv['username'].'</td><td><font size="-2">'.$dtEv['firstname']." ".$dtEv['lastname'].'</td><td>'.$dtEv['f1'].'</td>';
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemcs_resp']=='Y'){ 
								$color = ' bgcolor="#006600"';
								$sumaYesItem[$dtItemE['formcs_item']] = $sumaYesItem[$dtItemE['formcs_item']] + 1;
							}
							else if($dtItemE['itemcs_resp']=='N'){
								$color = ' bgcolor="#FF0000"';
								$sumaNoItem[$dtItemE['formcs_item']] = $sumaNoItem[$dtItemE['formcs_item']] + 1;	
							}
							else{
								$color = 'bgcolor="#333333"';
								$sumaNAItem[$dtItemE['formcs_item']] = $sumaNAItem[$dtItemE['formcs_item']] + 1;
							}
							$tblResult .='<td align="center"  '.$color.'><font color="#FFFFFF">'.$dtItemE['itemcs_resp'].'</font></td>';	
						}	
						$tblResult .='<td>'.number_format($dtEv['monitcsemp_qualification'],2).'%</td></tr>';
					}
				}
				
			}//Termina tabla de reporte detalles CS
			
			//Reporte de detalles sales
			
			else if($_POST['monit']==2){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td>Date</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$textos[$dtI['item']] = $dtI['formsales_text'];
					$sumaYesItem[$dtI['item']] = 0;
					$sumaNoItem[$dtI['item']] = 0;
					$sumaNAItem[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemsales, itemsales_total, itemsales_resp, formsales_item from itemsales_monitoring it inner join form_monitoring_sales f on f.id_formsales = it.id_formsales where id_monitsalesemp=".$dtEv['id_monitsalesemp']." and formsales_status='A' order by formsales_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$tblResult .='<tr class="rowCons" onclick="loadMonitoringSalesbyReport('.$dtEv['id_monitsalesemp'].')"><td><font size="-2">'.$dtEv['username'].'</td><td><font size="-2">'.$dtEv['firstname']." ".$dtEv['lastname'].'</td><td>'.$dtEv['f1'].'</td>';
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemsales_resp']=='Y'){ 
								$color = ' bgcolor="#006600"';
								$sumaYesItem[$dtItemE['formsales_item']] = $sumaYesItem[$dtItemE['formsales_item']] + 1;
							}
							else if($dtItemE['itemsales_resp']=='N'){
								$color = ' bgcolor="#FF0000"';
								$sumaNoItem[$dtItemE['formsales_item']] = $sumaNoItem[$dtItemE['formsales_item']] + 1;	
							}
							else{
								$color = 'bgcolor="#333333"';
								$sumaNAItem[$dtItemE['formsales_item']] = $sumaNAItem[$dtItemE['formsales_item']] + 1;	
							}
							$tblResult .='<td align="center"  '.$color.'><font color="#FFFFFF">'.$dtItemE['itemsales_resp'].'</font></td>';	

						}	
						$tblResult .='<td>'.number_format($dtEv['monitsales_qualification'],2).'%</td></tr>';
					}
				}
				
			}//Termina de evaluacion evaluacion de Sales
			
			
			//Evalua reporte para New Services
			else if($_POST['monit']==3){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td>Date</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$textos[$dtI['item']] = $dtI['formns_text'];
					$sumaYesItem[$dtI['item']] = 0;
					$sumaNoItem[$dtI['item']] = 0;
					$sumaNAItem[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemns, itemns_total, itemns_resp, formns_item from itemns_monitoring it inner join form_monitoring_ns f on f.id_formns = it.id_formns where id_monitnsemp=".$dtEv['id_monitnsemp']." and formns_status='A' order by formns_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$tblResult .='<tr class="rowCons" onclick="loadMonitoringNSbyReport('.$dtEv['id_monitnsemp'].')"><td><font size="-2">'.$dtEv['username'].'</td><td><font size="-2">'.$dtEv['firstname']." ".$dtEv['lastname'].'</td><td>'.$dtEv['f1'].'</td>';
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemns_resp']=='Y'){ 
								$color = ' bgcolor="#006600"';
								$sumaYesItem[$dtItemE['formns_item']] = $sumaYesItem[$dtItemE['formns_item']] + 1;
							}
							else if($dtItemE['itemns_resp']=='N'){
								$color = ' bgcolor="#FF0000"';
								$sumaNoItem[$dtItemE['formns_item']] = $sumaNoItem[$dtItemE['formns_item']] + 1;	
							}
							else{
								$color = 'bgcolor="#333333"';
								$sumaNAItem[$dtItemE['formns_item']] = $sumaNAItem[$dtItemE['formns_item']] + 1;
							}
							$tblResult .='<td align="center"  '.$color.'><font color="#FFFFFF">'.$dtItemE['itemns_resp'].'</font></td>';	

						}	
						$tblResult .='<td>'.number_format($dtEv['monitnsemp_qualification'],2).'%</td></tr>';
					}
				}
				
			}//Termina evaluacion de New Services
			
			else if($_POST['monit']==4){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td>Date</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemchat, itemchat_total, itemchat_resp, formchat_item from itemchat_monitoring it inner join form_monitoring_chat f on f.id_formchat = it.id_formchat where id_monitchatemp=".$dtEv['id_monitchatemp']." and formchat_status='A' order by formchat_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$tblResult .='<tr class="rowCons" onclick="loadMonitoringChatbyReport('.$dtEv['id_monitchatemp'].')"><td><font size="-2">'.$dtEv['username'].'</td><td><font size="-2">'.$dtEv['firstname']." ".$dtEv['lastname'].'</td><td>'.$dtEv['f1'].'</td>';
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemchat_resp']=='Y'){ $color = ' bgcolor="#006600"';}
							else if($dtItemE['itemchat_resp']=='N'){$color = ' bgcolor="#FF0000"';}
							else{$color = 'bgcolor="#333333"';}
							$tblResult .='<td align="center"  '.$color.'><font color="#FFFFFF">'.$dtItemE['itemchat_resp'].'</font></td>';	
							$totales[$dtItemE['formchat_item']] = $totales[$dtItemE['formchat_item']] + $dtItemE['itemchat_total'];
						}	
						$tblResult .='<td>'.number_format($dtEv['monitchatemp_qualification'],2).'%</td></tr>';
					}
				}
			}
			$resumen = '<table class="tblRepQA" align="center" cellpadding="6" cellspacing="1">';
			$resumen .='<tr><td colspan="4" align="center"><b>Summary of Total Yes, No, NA by question</b></td></tr>';
			$resumen .='<tr><td></td><td></td><td>Total Yes</td><td>Total No</td><td>Total NA</td></tr>';
			foreach($dtItem as $dtI){
				$resumen .='<tr><td>'.$dtI['item'].'</td><td>'.$textos[$dtI['item']].'</td><td>'.$sumaYesItem[$dtI['item']].'</td><td>'.$sumaNoItem[$dtI['item']].'</td><td>'.$sumaNAItem[$dtI['item']].'</td></tr>';		
			}
			$resumen .='</table>';
			
		}
		else{
			$tblResult .='<tr><td colspan="4">No matches</td></tr>';
		}
		$tblResult .= '</table></div>';
		if(strlen($resumen)>0){
			$tblResult .='<br><br>'.$resumen.'<br>';	
		}
		echo $tblResult;
	break;
	
	
	//Reporte de promedios en un periodo y por tipo de evaluacion
	case 'loadReportQaTotal':
		$filtroCS = "";
		$filtroSales = "";
		$filtroNS = "";
		$filtroChat = "";
		$filtro = " where 1 ";
		$jointable = " from employees e ";
		$sqlText = "";
		$totales[] = "";
		$filtro2 ="";
		
		if(strlen($_POST['fecha_ini'])>0 and strlen($_POST['fecha_fin'])>0){
			$fec_ini  = $oFec->cvDtoY($_POST['fecha_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fecha_fin']);
			$filtroCS .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroSales .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroNS .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroChat .=" and monitchatemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and m.employee_id=".$_POST['emp'];
		}
		if($_POST['qa']>0){
			$filtroCS .=" and qa_agent =".$_POST['qa'];
			$filtroSales .=" and qa_agent =".$_POST['qa'];
			$filtroNS .=" and qa_agent =".$_POST['qa'];	
			$filtroChat .=" and qa_agent =".$_POST['qa'];
		}
		if($_POST['cuenta']>0){
			$filtro2 =" and ".$_POST['cuenta']."=(select pd.id_account from employees emp inner join plazaxemp pe on emp.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep where emp.employee_id=e.employee_id and status_plxemp='A') ";	
		}
		if($_POST['posicion']!='0'){
			$filtroCS .=" and monitcsemp_maker='".$_POST['posicion']."' ";
			$filtroNS .=" and monitnsemp_maker='".$_POST['posicion']."' ";
			$filtroSales .=" and monitsales_maker='".$_POST['posicion']."' ";
			
		}
		
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$jointable .=" inner join plazaxemp pe on pe.employee_id=e.employee_id ";
			$filtro .=" and pe.id_placexdep in (".$_SESSION['permisos'].") and pe.status_plxemp='A' ";
		}
		
		if($_POST['monit']==1){
			$filtro .= $filtroCS;
			$jointable .=" inner join monitoringcs_emp m on m.employee_id=e.employee_id ";
			$sqlTextEmp = "select distinct(e.employee_id) as emp, username, lastname, firstname ".$jointable." ".$filtro." ".$filtro2." order by firstname";
			
			/*$sqlText = "select e.employee_id, username, firstname, lastname, id_monitcsemp, qa_agent, date_format(monitcsemp_date,'%d/%m/%Y') as f1, monitcsemp_qualification ".$jointable." ".$filtro." order by firstname, monitcsemp_date desc";
			*/
			$sqlItem = "select id_formcs as id, id_catcs, formcs_item as item, formcs_text from form_monitoring_cs where formcs_status='A' order by formcs_item";
		}
		else if($_POST['monit']==2){
			$filtro .= $filtroSales;
			$jointable .=" inner join monitoringsales_emp m on m.employee_id=e.employee_id ";
			$sqlTextEmp = "select distinct(e.employee_id) as emp,username, lastname, firstname ".$jointable." ".$filtro." ".$filtro2." order by firstname";
			
			/*$sqlText = "select e.employee_id, username, firstname, lastname, id_monitsalesemp, qa_agent, date_format(monitsales_date,'%d/%m/%Y') as f1, monitsales_qualification ".$jointable." ".$filtro." order by firstname, monitsales_date desc";*/
			
			$sqlItem = "select id_formsales as id, id_catsales, formsales_item as item, formsales_text from form_monitoring_sales where formsales_status='A' order by formsales_item";

		}
		else if($_POST['monit']==3){
			$filtro .= $filtroNS;
			$jointable .= " inner join monitoringns_emp m on m.employee_id=e.employee_id ";
			$sqlTextEmp = "select distinct(e.employee_id) as emp,username, lastname, firstname ".$jointable." ".$filtro." ".$filtro2." order by firstname";
			
			/*$sqlText = "select e.employee_id, username, firstname, lastname, id_monitnsemp, qa_agent, date_format(monitnsemp_date,'%d/%m/%Y') as f1, monitnsemp_qualification ".$jointable." ".$filtro." order by firstname, monitnsemp_date desc";*/
			
			$sqlItem = "select id_formns as id, id_catns, formns_item as item, formns_text from form_monitoring_ns where formns_status='A' order by formns_item";

		}
		else if($_POST['monit']==4){
			$filtro .=$filtroChat;
			$jointable .=" inner join monitoringchat_emp m on m.employee_id=e.employee_id ";
			$sqlTextEmp = "select distinct(e.employee_id) as emp, username, lastname, firstname ".$jointable." ".$filtro." ".$filtro2." order by firstname";
			
			/*$sqlText = "select e.employee_id, username, firstname, lastname, id_monitchatemp, qa_agent,date_format(monitchatemp_date,'%d/%m/%Y') as f1, monitchatemp_qualification ".$jointable." ".$filtro." order by firstname, monitchatemp_date desc";*/
			
			$sqlItem = "select id_formchat as id, id_catchat, formchat_item as item, formchat_text from form_monitoring_chat where formchat_status='A' order by formchat_item";
		}
		
		
		$dtEmp = $dbEx->selSql($sqlTextEmp);
		
		$tblResult .='<form target="_blank" action="report/xls_DetailsQA.php" method="post">
		<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
		<input type="hidden" name="filtro" value="'.$filtro.'">
		<input type="hidden" name="sqlTextEmp" value="'.$sqlTextEmp.'">
		<input type="hidden" name="filtroCS" value="'.$filtroCS.'">
		<input type="hidden" name="filtroNS" value="'.$filtroNS.'">
		<input type="hidden" name="filtroSales" value="'.$filtroSales.'">
		<input type="hidden" name="sqlItem" value="'.$sqlItem.'">
		<input type="hidden" name="monit" value="'.$_POST['monit'].'"></form>';
		$tblResult .= '<div class="scroll">';
		$tblResult .= '<table class="tblHead"  align="center" cellpadding="2" cellspacing="1">';
		if($dbEx->numrows>0){
			
			$dtItem = $dbEx->selSql($sqlItem);
			//Evalua  para reporte de detalles de Customer services
			
			if($_POST['monit']==1){
				$tblResult .='<tr class="showItem"><td width="5px"><font size="-2">BADGE</td><td width="30px"><font size="-3">EMPLOYEE</td><td width="5px"><font size="-3">Num. Evaluations</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td width="5px">'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEmp as $dtE){
					$tblResult .='<tr class="rowCons"><td><font size="-2">'.$dtE['username'].'</td><td><font size="-2">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
					//Cuenta la cantidad de evaluaciones que ha tenido el agente 
					$sqlTextCount = "select count(id_monitcsemp) as cant from monitoringcs_emp where 1 ".$filtroCS." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitcsemp_qualification) as calif from monitoringcs_emp where 1 ".$filtroCS." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					$tblResult .='<td width="5px">'.$dtCount['0']['cant'].'</td>';
					foreach($dtItem as $dtI){
						//Cuenta por item cuantas preguntas positivas  y negativas ha tenido
						$sqlText = "select count(id_itemcs) as citems from itemcs_monitoring it inner join monitoringcs_emp m on it.id_monitcsemp=m.id_monitcsemp where 1 ".$filtroCS." and employee_id=".$dtE['emp']." and it.id_formcs=".$dtI['id']." and (itemcs_resp='Y' or itemcs_resp='N')";
						$dtCantPreg = $dbEx->selSql($sqlText);
						//Cuenta la cantidad de positivas
						$sqlText = "select count(id_itemcs) as yitems from itemcs_monitoring it inner join monitoringcs_emp m on it.id_monitcsemp=m.id_monitcsemp where 1 ".$filtroCS." and employee_id=".$dtE['emp']." and it.id_formcs=".$dtI['id']." and itemcs_resp='Y'";
						$dtCantY = $dbEx->selSql($sqlText);

						//Cuenta la cantidad de negativas
						$sqlText = "select count(id_itemcs) as nitems from itemcs_monitoring it inner join monitoringcs_emp m on it.id_monitcsemp=m.id_monitcsemp where 1 ".$filtroCS." and employee_id=".$dtE['emp']." and it.id_formcs=".$dtI['id']." and itemcs_resp='N'";
						$dtCantN = $dbEx->selSql($sqlText);
						
						if($dtCantPreg['0']['citems']>0){
							$totalY = number_format(($dtCantY['0']['yitems']/$dtCantPreg['0']['citems'])*100,2)."%";
							$totalN = number_format(($dtCantN['0']['nitems']/$dtCantPreg['0']['citems'])*100,2)."%";
						}
						else{
							$totalY = "N/A";
							$totalN = "N/A";	
						}
						$color = "#FFFFFF";
						if($totalY>=0 and $totalY <=75 and $totalY!="N/A"){
							$color = "#FF0000";
						}
						else if($totalY>75 and $totalY<=80){
							$color = "#FFCC00";
						}
						else if($totalY>80 and $totalY<=90){
							$color = "#009933";
						}
						else if($totalY>90 and $totalY<=99){
							$color = "#0066CC";
						}
						else if($totalY>99 and $totalY <=100){
							$color = "#FB9E42";	
						}
						$tblResult .='<td bgcolor="'.$color.'">'.$totalY.'</td>';
					}
					$tblResult .='<td>'.number_format($promEva,2).'%</td></tr>';
				}
				
			}//Termina tabla de reporte detalles CS
			
			//Reporte de detalles sales
			
			else if($_POST['monit']==2){
				$tblResult .='<tr class="showItem"><td width="5px"><font size="-2">BADGE</td><td width="30px"><font size="-3">EMPLOYEE</td><td width="5px"><font size="-3">Num. Evaluations</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td width="5px">'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEmp as $dtE){
					$tblResult .='<tr class="rowCons"><td><font size="-2">'.$dtE['username'].'</td><td><font size="-2">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitsalesemp) as cant from monitoringsales_emp where 1 ".$filtroSales." and employee_id=".$dtE['emp'];
					
					$rslt = $sqlTextCount;
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitsales_qualification) as calif from monitoringsales_emp where 1 ".$filtroSales." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					$tblResult .='<td>'.$dtCount['0']['cant'].'</td>';
					foreach($dtItem as $dtI){
						//Cuenta por item cuantas preguntas positivas  y negativas ha tenido
						$sqlText = "select count(id_itemsales) as citems from itemsales_monitoring it inner join monitoringsales_emp m on it.id_monitsalesemp=m.id_monitsalesemp where 1 ".$filtroSales." and employee_id=".$dtE['emp']." and it.id_formsales=".$dtI['id']." and (itemsales_resp='Y' or itemsales_resp='N')";
						$dtCantPreg = $dbEx->selSql($sqlText);
						//Cuenta la cantidad de positivas
						$sqlText = "select count(id_itemsales) as yitems from itemsales_monitoring it inner join monitoringsales_emp m on it.id_monitsalesemp=m.id_monitsalesemp where 1 ".$filtroSales." and employee_id=".$dtE['emp']." and it.id_formsales=".$dtI['id']." and itemsales_resp='Y'";
						$dtCantY = $dbEx->selSql($sqlText);

						//Cuenta la cantidad de negativas
						$sqlText = "select count(id_itemsales) as nitems from itemsales_monitoring it inner join monitoringsales_emp m on it.id_monitsalesemp=m.id_monitsalesemp where 1 ".$filtroSales." and employee_id=".$dtE['emp']." and it.id_formsales=".$dtI['id']." and itemsales_resp='N'";
						$dtCantN = $dbEx->selSql($sqlText);
						
						if($dtCantPreg['0']['citems']>0){
							$totalY = number_format(($dtCantY['0']['yitems']/$dtCantPreg['0']['citems'])*100,2)."%";
							$totalN = number_format(($dtCantN['0']['nitems']/$dtCantPreg['0']['citems'])*100,2)."%";
						}
						else{
							$totalY = "N/A";
							$totalN = "N/A";	
						}
						$color = "#FFFFFF";
						if($totalY>=0 and $totalY <=75 and $totalY!="N/A"){
							$color = "#FF0000";
						}
						else if($totalY>75 and $totalY<=80){
							$color = "#FFCC00";
						}
						else if($totalY>80 and $totalY<=90){
							$color = "#009933";
						}
						else if($totalY>90 and $totalY<=99){
							$color = "#0066CC";
						}
						else if($totalY>99 and $totalY <=100){
							$color = "#FB9E42";	
						}
						$tblResult .='<td bgcolor="'.$color.'">'.$totalY.'</td>';
					}
					$tblResult .='<td>'.number_format($promEva,2).'%</td></tr>';
				}
				
			}//Termina de evaluacion evaluacion de Sales
			
			
			//Evalua reporte para New Services
			else if($_POST['monit']==3){
				$tblResult .='<tr class="showItem"><td width="5px"><font size="-2">BADGE</td><td width="25px" ><font size="-3">EMPLOYEE</td><td width="5px"><font size="-3">Num. Evaluations</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td width="5px">'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEmp as $dtE){
					$tblResult .='<tr class="rowCons"><td><font size="-2">'.$dtE['username'].'</td><td><font size="-2">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitnsemp) as cant from monitoringns_emp where 1 ".$filtroNS." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitnsemp_qualification) as calif from monitoringns_emp where 1 ".$filtroNS." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					$tblResult .='<td>'.$dtCount['0']['cant'].'</td>';
					foreach($dtItem as $dtI){
						//Cuenta por item cuantas preguntas positivas  y negativas ha tenido
						$sqlText = "select count(id_itemns) as citems from itemns_monitoring it inner join monitoringns_emp m on it.id_monitnsemp=m.id_monitnsemp where 1 ".$filtroNS." and employee_id=".$dtE['emp']." and it.id_formns=".$dtI['id']." and (itemns_resp='Y' or itemns_resp='N')";
						$dtCantPreg = $dbEx->selSql($sqlText);
						//Cuenta la cantidad de positivas
						$sqlText = "select count(id_itemns) as yitems from itemns_monitoring it inner join monitoringns_emp m on it.id_monitnsemp=m.id_monitnsemp where 1 ".$filtroNS." and employee_id=".$dtE['emp']." and it.id_formns=".$dtI['id']." and itemns_resp='Y'";
						$dtCantY = $dbEx->selSql($sqlText);

						//Cuenta la cantidad de negativas
						$sqlText = "select count(id_itemns) as nitems from itemns_monitoring it inner join monitoringns_emp m on it.id_monitnsemp=m.id_monitnsemp where 1 ".$filtroNS." and employee_id=".$dtE['emp']." and it.id_formns=".$dtI['id']." and itemns_resp='N'";
						$dtCantN = $dbEx->selSql($sqlText);
						
						if($dtCantPreg['0']['citems']>0){
							$totalY = number_format(($dtCantY['0']['yitems']/$dtCantPreg['0']['citems'])*100,2)."%";
							$totalN = number_format(($dtCantN['0']['nitems']/$dtCantPreg['0']['citems'])*100,2)."%";
						}
						else{
							$totalY = "N/A";
							$totalN = "N/A";	
						}
						$color = "#FFFFFF";
						if($totalY>=0 and $totalY <=75 and $totalY!="N/A"){
							$color = "#FF0000";
						}
						else if($totalY>75 and $totalY<=80){
							$color = "#FFCC00";
						}
						else if($totalY>80 and $totalY<=90){
							$color = "#009933";
						}
						else if($totalY>90 and $totalY<=99){
							$color = "#0066CC";
						}
						else if($totalY>99 and $totalY <=100){
							$color = "#FB9E42";	
						}
						$tblResult .='<td bgcolor="'.$color.'">'.$totalY.'</td>';
					}
					$tblResult .='<td>'.number_format($promEva,2).'%</td></tr>';
				}
				
			}//Termina reporte para new services
			
			//Evalua reporte para Chat
			else if($_POST['monit']==4){
				$tblResult .='<tr class="showItem"><td width="5px"><font size="-2">BADGE</td><td width="25px" ><font size="-3">EMPLOYEE</td><td width="5px"><font size="-3">Num. Evaluations</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td width="5px">'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEmp as $dtE){
					$tblResult .='<tr class="rowCons"><td><font size="-2">'.$dtE['username'].'</td><td><font size="-2">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitchatemp) as cant from monitoringchat_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitchatemp_qualification) as calif from monitoringchat_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					$tblResult .='<td>'.$dtCount['0']['cant'].'</td>';
					foreach($dtItem as $dtI){
						//Cuenta por item cuantas preguntas positivas  y negativas ha tenido
						$sqlText = "select count(id_itemchat) as citems from itemchat_monitoring it inner join monitoringchat_emp m on it.id_monitchatemp=m.id_monitchatemp ".$filtro." and employee_id=".$dtE['emp']." and it.id_formchat=".$dtI['id']." and (itemchat_resp='Y' or itemchat_resp='N')";
						$dtCantPreg = $dbEx->selSql($sqlText);
						//Cuenta la cantidad de positivas
						$sqlText = "select count(id_itemchat) as yitems from itemchat_monitoring it inner join monitoringchat_emp m on it.id_monitchatemp=m.id_monitchatemp ".$filtro." and employee_id=".$dtE['emp']." and it.id_formchat=".$dtI['id']." and itemchat_resp='Y'";
						$dtCantY = $dbEx->selSql($sqlText);

						//Cuenta la cantidad de negativas
						$sqlText = "select count(id_itemchat) as nitems from itemchat_monitoring it inner join monitoringchat_emp m on it.id_monitchatemp=m.id_monitchatemp ".$filtro." and employee_id=".$dtE['emp']." and it.id_formchat=".$dtI['id']." and itemchat_resp='N'";
						$dtCantN = $dbEx->selSql($sqlText);
						
						if($dtCantPreg['0']['citems']>0){
							$totalY = number_format(($dtCantY['0']['yitems']/$dtCantPreg['0']['citems'])*100,2)."%";
							$totalN = number_format(($dtCantN['0']['nitems']/$dtCantPreg['0']['citems'])*100,2)."%";
						}
						else{
							$totalY = "N/A";
							$totalN = "N/A";	
						}
						$color = "#FFFFFF";
						if($totalY>=0 and $totalY <=75 and $totalY!="N/A"){
							$color = "#FF0000";
						}
						else if($totalY>75 and $totalY<=80){
							$color = "#FFCC00";
						}
						else if($totalY>80 and $totalY<=90){
							$color = "#009933";
						}
						else if($totalY>90 and $totalY<=99){
							$color = "#0066CC";
						}
						else if($totalY>99 and $totalY <=100){
							$color = "#FB9E42";	
						}
						$tblResult .='<td bgcolor="'.$color.'">'.$totalY.'</td>';
					}
					$tblResult .='<td>'.number_format($promEva,2).'%</td></tr>';
				}
				
			}//Termina reporte Chat
		}
		else{
			$tblResult .='<tr><td colspan="4">No matches</td></tr>';
		}
		$tblResult .= '</table></div>';
		echo $tblResult;
	break;
	
	
	case 'loadMonitoringReport':
		$filtroCS = " where 1 ";
		$filtroSA = " where 1 ";
		$filtroNS = " where 1 ";
		$filtroChat = " where 1 ";
		$filtroC = "";
		$filtroS = "";
		$filtroN = "";
		$filtro = " ";
		$filtroCuenta = " where 1 ";
		//Dependiendo el tipo de evaluacion de monitoreo que se seleccione hace la comparancion y querys
			$monitCS = false;
			$monitSales = false;
			$monitNS = false;
			$monitChat = false;
			
			if($_POST['monit']==0){$monitCS = true; $monitSales = true; $monitNS = true;}
			if($_POST['monit']==1){$monitCS = true;}
			if($_POST['monit']==2){$monitSales = true;}
			if($_POST['monit']==3){$monitNS = true;}
			if($_POST['monit']==4){$monitChat = true;}

		if(strlen($_POST['fecha_ini'])>0 and strlen($_POST['fecha_fin'])>0){
			$fec_ini  = $oFec->cvDtoY($_POST['fecha_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fecha_fin']);
			$filtroCS .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroSA .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroNS .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroChat .=" and monitchatemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
		}
		if($_POST['sup']>0){
			$filtroCS .=" and e.id_supervisor=".$_POST['sup'];
			$filtroSA .=" and e.id_supervisor=".$_POST['sup'];
			$filtroNS .=" and e.id_supervisor=".$_POST['sup'];
			$filtroChat .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtroCS .=" and m.employee_id=".$_POST['emp'];
			$filtroSA .=" and m.employee_id=".$_POST['emp'];
			$filtroNS .=" and m.employee_id=".$_POST['emp'];
			$filtroChat .=" and m.employee_id=".$_POST['emp'];
		}
		if($_POST['qa']>0){
			$filtroCS .=" and qa_agent =".$_POST['qa'];
			$filtroSA .=" and qa_agent =".$_POST['qa'];
			$filtroNS .=" and qa_agent =".$_POST['qa'];	
			$filtroChat .=" and qa_agent =".$_POST['qa'];
		}
		if($_POST['cuenta']>0){
			$filtroCuenta .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['posicion']!='0'){
			$filtroC .=" and monitcsemp_maker='".$_POST['posicion']."' ";
			$filtroN .=" and monitnsemp_maker='".$_POST['posicion']."' ";
			$filtroS .=" and monitsales_maker='".$_POST['posicion']."' ";
			
		}
		
		//REaliza filtro de gerente de area
		$filtroG = "";
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtroG .=" and pd.id_placexdep in (".$_SESSION['permisos'].") ";
		}

		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".$filtroCuenta." ".$filtroG." and status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt ='<div id="lyDetalle"></div><br><br>';
		$rslt .='<form target="_blank" action="report/xls_monitoringreport.php" method="post">
		<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
		<input type="hidden" name="fecha_ini" value="'.$fec_ini.'">
		<input type="hidden" name="fecha_fin" value="'.$fec_fin.'">
		<input type="hidden" name="filtroCS" value="'.$filtroCS.'">
		<input type="hidden" name="filtroSA" value="'.$filtroSA.'">
		<input type="hidden" name="filtroNS" value="'.$filtroNS.'">
		<input type="hidden" name="filtroC" value="'.$filtroC.'">
		<input type="hidden" name="filtroS" value="'.$filtroS.'">
		<input type="hidden" name="filtroN" value="'.$filtroN.'">
		<input type="hidden" name="filtroChat" value="'.$filtroChat.'">
		<input type="hidden" name="filtroCuenta" value="'.$filtroCuenta.'">
		<input type="hidden" name="monit" value="'.$_POST['monit'].'"></form>';
		$rslt .= '<div class="scroll">';
		$rslt .= '<table class="tblRepQA" align="center" cellpadding="2" cellspacing="1" >';
		
		if($dbEx->numrows>0){
			

			$start = strtotime($fec_ini);
			$end = strtotime($fec_fin);
			
			$tblResult = '<tr><td align="center"><b>Badge</b></td><td align="center" width="250px"><b>Employee</b></td>';
			$n = 0;
			
			for($i = $start; $i <=$end; $i +=86400){
				$nFecha = strtotime(date("Y/m/d",$i));
				$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
				$tblResult .='<td style="border: 1px #638DBD inset;"><b>'.date('d/m/Y',$i).'</b></td>';
				$n = $n + 1;
				if($dia==0){
					$tblResult .='<td style="border: 1px #638DBD inset;"><b>Evaluations</td><td style="border: 1px #638DBD inset;"><b>TOTALS</td><td align="center" style="border: 1px #638DBD inset;"><b>%</b></td>';	
				}
				
			}
			//Busca por agente si se les ha realizado evaluacion
			
			
			foreach($dtEmp as $dtE){
				$flag = false;
				if($monitCS){
					$sqlText = "select e.employee_id from monitoringcs_emp m inner join employees e on e.employee_id=m.employee_id ".$filtroCS." ".$filtroC." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}
				}
				if($monitSales){
					$sqlText = "select e.employee_id from monitoringsales_emp m inner join employees e on e.employee_id=m.employee_id ".$filtroSA."  ".$filtroS." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}
				}
				if($monitNS){
					$sqlText = "select e.employee_id from monitoringns_emp m inner join employees e on e.employee_id=m.employee_id ".$filtroNS." ".$filtroN." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}	
				}
				if($monitChat){
					$sqlText = "select e.employee_id from monitoringchat_emp m inner join employees e on e.employee_id=m.employee_id ".$filtroChat." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}
				}
				
				if($flag){
				$totalEvaSemana = 0;
				$sumaSemana = 0;
				$totalPorcentSemana = 0;
				$promSemana = 0;
				$tblResult .='<tr><td style="border: 1px #638DBD inset;">'.$dtE['username'].'</td><td style="border: 1px #638DBD inset;">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
				for($i = $start; $i <=$end; $i +=86400){
					$nFecha = strtotime(date("Y/m/d",$i));
					$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
					if($dia==1){
						$totalEvaSemana = 0;
						$sumaSemana = 0;
						$totalPorcentSemana = 0;
						$promSemana = 0;	
					}
					$nCS = "";
					$promCS = "";
					$nSA = "";
					$promSA = "";
					$nNS = "";
					$promNS = "";
					$nChat = "";
					$promChat = "";
					$nTotal = "";
					$promTotal = "";
					$sumaCS = 0;
					$sumaSA = 0;
					$sumaNS = 0;
					$sumaChat = 0;
					if($monitCS){
					//Conteo por dias para customer services 
						$sqlText = "select sum(monitcsemp_qualification) as sumcs from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date='".date('Y-m-d',$i)."' ".$filtroC;
						$dtSumCS = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitcsemp_qualification) as countcs from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date='".date('Y-m-d',$i)."' ".$filtroC;
						$dtCountCS = $dbEx->selSql($sqlText);
						if($dtCountCS['0']['countcs']>0){
							$nCS = 	$dtCountCS['0']['countcs'];
							$sumaSemana = $sumaSemana + $dtSumCS['0']['sumcs'];
							$promCS = $dtSumCS['0']['sumcs']/$nCS;
							$sumaCS = $dtSumCS['0']['sumcs'];
						}
					}
					if($monitSales){
						//Conteo por dias para sales
						$sqlText = "select sum(monitsales_qualification) as sumsa from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date='".date('Y-m-d',$i)."' ".$filtroS;
						$dtSumSA = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitsales_qualification) as countsa from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date='".date('Y-m-d',$i)."' ".$filtroS;
						$dtCountSA = $dbEx->selSql($sqlText);
						if($dtCountSA['0']['countsa']>0){
							$nSA = $dtCountSA['0']['countsa'];
							$sumaSemana = $sumaSemana + $dtSumSA['0']['sumsa'];
							$promSA = $dtSumSA['0']['sumsa']/$nSA;	
							$sumaSA = $dtSumSA['0']['sumsa'];
						}
					}
					if($monitNS){
						//Conteo por dias para new service
						$sqlText = "select sum(monitnsemp_qualification) as sumns from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date='".date('Y-m-d',$i)."' ".$filtroN;
						$dtSumNS = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitnsemp_qualification) as countns from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date='".date('Y-m-d',$i)."' ".$filtroN;
						$dtCountNS = $dbEx->selSql($sqlText);
						if($dtCountNS['0']['countns']>0){
							$nNS = $dtCountNS['0']['countns'];
							$sumaSemana = $sumaSemana + $dtSumNS['0']['sumns'];
							$promNS = $dtSumNS['0']['sumns']/$nNS;	
							$sumaNS = $dtSumNS['0']['sumns'];
						}
					}
					if($monitChat){
						 //conteo por dias para Chat
						 $sqlText = "select sum(monitchatemp_qualification) as sumchat from monitoringchat_emp where employee_id=".$dtE['employee_id']." and monitchatemp_date='".date('Y-m-d',$i)."' ";
						 $dtSumChat = $dbEx->selSql($sqlText);
						 $sqlText = "select count(monitchatemp_qualification) as countchat from monitoringchat_emp where employee_id=".$dtE['employee_id']." and monitchatemp_date='".date('Y-m-d',$i)."' ";
						 $dtCountChat = $dbEx->selSql($sqlText);
						 if($dtCountChat['0']['countchat']>0){
							 $nChat = $dtCountChat['0']['countchat'];
							 $sumaSemana = $sumaSemana + $dtSumChat['0']['sumchat'];
							 $promChat = $dtSumChat['0']['sumchat']/$nChat;
							 $sumaChat = $dtSumChat['0']['sumchat'];
						 }	
					}
					//Sumar numeros de evaluaciones y promedios
					$click = "";
					if($nCS>0 or $nSA>0 or $nNS>0 or $nChat>0){
						$nTotal = $nCS + $nSA + $nNS + $nChat;
						//$promTotal = ($promCS + $promSA + $promNS)/$nTotal;
						$promTotal = ($sumaCS + $sumaSA + $sumaNS + $sumaChat)/$nTotal;
						$promTotal = number_format($promTotal,2)."%";
						$totalEvaSemana = $totalEvaSemana + $nTotal;
						$click = ' style="cursor:help"  title="Click to see detail of monitoring" onClick="loadDetallePromedio('.$i.','.$dtE['employee_id'].', '.$_POST['monit'].')" ';
					}
					$color = 'bgcolor="#FFFFFF"';
					if($promTotal>=0 and $promTotal <=75 and $promTotal!=""){
						$color = 'bgcolor="#FC252B"';
					}
					else if($promTotal>75 and $promTotal<=80){
						$color = 'bgcolor="#FF9900"';
					}
					else if($promTotal>80 and $promTotal<=90){
						$color = 'bgcolor="#00FF33"';
					}
					else if($promTotal>90 and $promTotal<=99){
						$color = 'bgcolor="#7BBDEE"';
					}
					else if($promTotal>99 and $promTotal <=100){
						$color = 'bgcolor="#FB9E42"';	
					}
					//$tblResult .='<td>'.$promTotal.' '.$totalEvaSemana.'</td>';
					$tblResult .='<td align="center" '.$color.' style="border: 1px #638DBD inset;" '.$click.'><b>'.$promTotal.'</b></td>';
					
					if($dia==0){
						if($totalEvaSemana >0){
							$promSemana = 	number_format(($sumaSemana/$totalEvaSemana),2);
						}
						$color = "#FFFFFF";
						if($promSemana>=0 and $promSemana <=75  and $totalEvaSemana>0){
							$color = "#FC252B";
						}
						else if($promSemana>75 and $promSemana<=80){
							$color = "#FF9900";
						}
						else if($promSemana>80 and $promSemana<=90){
							$color = "#00FF33";
						}
						else if($promSemana>90 and $promSemana<=99){
							$color = "#7BBDEE";
						}
						else if($promSemana>99 and $promSemana<=100){
							$color = "#FB9E42";	
						}
						$tblResult .='<td align="center" style="border: 1px #638DBD inset;">'.$totalEvaSemana.'</td><td style="border: 1px #638DBD inset;">'.number_format($sumaSemana,2).'</td><td style="border: 1px #638DBD inset;" bgcolor="'.$color.'"><b>'.number_format($promSemana,2).'</b></td>';
					}
					
					}//Termina de ver por empleado
					
				}//Termina for
				
				
			}
			$rslt .=  $tblResult;
			
		}
		else{
			$rslt .= "<tr><td>No matches</td></tr>";	
		}
		$rslt .="</table></div>";
		echo $rslt;
	
	break;
	
	case 'loadDetallePromedio':
		$monitCS = false;
		$monitNS = false;
		$monitSales = false;	
		$monitChat = false;
		if($_POST['monit']==0){
			$monitCS = true;
			$monitNS = true;
			$monitSales = true;	
			$monitChat = true;
		}
		else if($_POST['monit']==1){
			$monitCS = true;	
		}
		else if($_POST['monit']==2){
			$monitSales = true;
		}
		else if($_POST['monit']==3){
			$monitNS = true;
		}
		else if($_POST['monit']==4){
			$monitChat = true;	
		}
		$sqlText = "select username, firstname, lastname, id_supervisor from employees where employee_id=".$_POST['idE'];
		$dtEmp = $dbEx->selSql($sqlText);
		$sqlText = "select firstname, lastname from employees where employee_id=".$dtEmp['0']['id_supervisor'];
		$dtSup = $dbEx->selSql($sqlText);
		
		$tblResult = '<table class="tblRepQA" width="600px" align="center" cellpadding="2" cellspacing="1" >';
		$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="3" align="center"><b>Monitoring results of the day '.date('d/m/Y',$_POST['fecha']).'</td></tr>';
		$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="3" align="center"><b>Agent: '.$dtEmp['0']['username'].' '.$dtEmp['0']['firstname'].' '.$dtEmp['0']['lastname'].'</td></tr>';
		$tblResult .='<tr bgcolor="#FFFFFF"><td colspan="3" align="center"><b>Supervisor: '.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td></tr>';
		
		$tblResult .='<tr><td width="33%" align="center"><b>TYPE EVALUATION</b></td><td width="33%" align="center"><b>EVALUATOR</b></td><td width="33%" align="center"><b>QA PERCENTAGE TOTAL SCORE</b></td></tr>';

		//Si el tipo de monitoreo en los filtro incluia CS
		if($monitCS){
			$sqlText = "select MONITCSEMP_QUALIFICATION, (select concat(firstname,' ',lastname) from employees where employee_id=qa_agent) as elaborador from monitoringcs_emp where employee_id=".$_POST['idE']." and monitcsemp_date='".date('Y-m-d',$_POST['fecha'])."' ";
			$dtEvaCS = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtEvaCS as $dtCS){
					$tblResult .='<tr class="rowCons"><td align="center">Customer Services</td><td align="center">'.$dtCS['elaborador'].'</td><td align="center">'.number_format($dtCS['MONITCSEMP_QUALIFICATION'],2).'%</td></tr>';
				}
			}
			
		}//TErmina CS
		
		//Sales
		if($monitSales){
			$sqlText = "select MONITSALES_QUALIFICATION, (select concat(firstname,' ',lastname) from employees where employee_id=m.qa_agent) as elaborador from monitoringsales_emp m where employee_id=".$_POST['idE']." and monitsales_date='".date('Y-m-d',$_POST['fecha'])."' ";
			$dtEvaSales = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtEvaSales as $dtSales){
					$tblResult .='<tr class="rowCons"><td align="center">Sales</td><td align="center">'.$dtSales['elaborador'].'</td><td align="center">'.number_format($dtSales['MONITSALES_QUALIFICATION'],2).'%</td></tr>';
				}
			}	
		}//Termina sales
		
		if($monitNS){
			$sqlText = "select MONITNSEMP_QUALIFICATION, (select concat(firstname,' ',lastname) from employees where employee_id=qa_agent) as elaborador from monitoringns_emp where employee_id=".$_POST['idE']." and monitnsemp_date='".date('Y-m-d',$_POST['fecha'])."' ";
			$dtEvaNS = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtEvaNS as $dtNS){
					$tblResult .='<tr class="rowCons"><td align="center">New Service</td><td align="center">'.$dtNS['elaborador'].'</td><td align="center">'.number_format($dtNS['MONITNSEMP_QUALIFICATION'],2).'%</td></tr>';	
				}
			}
		}//Termina NS
		
		if($monitChat){
			$sqlText = "select MONITCHATEMP_QUALIFICATION, (select concat(firstname,' ',lastname) from employees where employee_id=qa_agent) as elaborador from monitoringchat_emp where employee_id=".$_POST['idE']." and monitchatemp_date='".date('Y-m-d',$_POST['fecha'])."' ";
			$dtEvaChat = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				foreach($dtEvaChat as $dtChat){
					$tblResult .='<tr class="rowCons"><td align="center">Chat</td><td align="center">'.$dtChat['elaborador'].'</td><td align="center">'.number_format($dtChat['MONITCHATEMP_QUALIFICATION'],2).'%</td></tr>';	
				}	
			}	
		}//Termina Chat
		$tblResult .='</table>';
	echo $tblResult;
	break;
	
	
	//Carga formulario para actualiazar evaluacion de Customer services
	case 'updEvaCS':
		$sqlText = "select ID_MONITCSEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitcsemp_date,'%d/%m/%Y') as f1, MONITCSEMP_QUALIFICATION, MONITCSEMP_CALLREASON, MONITCSEMP_CONTACTID, MONITCSEMP_ACCOUNT, FAIL_ID, SKILL_ID, MONITCSEMP_FAIL, MONITCSEMP_COMMENT1, MONITCSEMP_COMMENT2, MONITCSEMP_COMMENT3, MONITCSEMP_COMMENT4, MONITCSEMP_COMMENT5, MONITCSEMP_COMMENT6, MONITCSEMP_COMMENT7, MONITCSEMP_COMMENT8, MONITCSEMP_COMMENT9, MONITCSEMP_COMMENT10, MONITCSEMP_COMMENT11  from monitoringcs_emp m inner join employees e on e.employee_id=m.employee_id where id_monitcsemp=".$_POST['idM'];
		
		$dtMonit = $dbEx->selSql($sqlText);
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A' order by firstname";
			$dtEmp = $dbEx->selSql($sqlText);
			foreach($dtEmp as $dtE){
				$sel = "";
				if($dtE['employee_id']==$dtMonit['0']['EMPLOYEE_ID']){
					$sel = "selected";	
				}	
				$optEmp .='<option value="'.$dtE['employee_id'].'" '.$sel.'>'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}
			
			$sqlText = "select * from tp_skills order by skill_name";
			$dtSkill = $dbEx->selSql($sqlText);

			$optSkill = '<option value="0"> -- </option>';
			foreach($dtSkill as $dtSk){
				$sel = "";
				if($dtSk['SKILL_ID']==$dtMonit['0']['SKILL_ID']){
					$sel = "selected";	
				}
				$optSkill .='<option value="'.$dtSk['SKILL_ID'].'" '.$sel.'>'.$dtSk['SKILL_NAME'].'</option>';
			}
			
			
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			$tblForm = '<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Customer Service Monitoring Form Number '.$dtMonit['0']['ID_MONITCSEMP'].'</b></td></tr>';
			$tblForm .='<tr><td width="15%"><b>QA: </td><td width="50%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%"><b>Contact ID: </td><td width="20%"><input type="text" id="txtContactId" class="txtPag" value="'.$dtMonit['0']['MONITCSEMP_CONTACTID'].'"/></td></tr>';
			$tblForm .='<tr><td width="15%"><b>Agent name: </td>
			<td width="50%"><select id="lsEmp" onchange="getSuperv(this.value)" class="txtPag">'.$optEmp.'</select></td>
			<td width="15%"><b>Account #: </td>
			<td width="20%"><input type="text" id="txtAccount" value="'.$dtMonit['0']['MONITCSEMP_ACCOUNT'].'" class="txtPag"/></td></tr>';
			$tblForm .='<tr><td width="15%"><b>Date: </td><td colspan="3">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td width="15%"><b>Supervisor: </td><td colspan="3"><input type="text" id="txtSuperv" class="txtPag" value="'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'" size="35" disabled="disabled" /></td></tr>';
			$tblForm .='<tr><td width="15%"><b>Call Reason: </td>
			<td colspan="3"><input type="text" id="txtReason" value="'.$dtMonit['0']['MONITCSEMP_CALLREASON'].'" class="txtPag"/></td></tr>';
			$tblForm .='<tr><td width="15%"><b>Skill: </td><td colspan="3"><select id="lsSkill" class="txtPag">'.$optSkill.'</select></td></tr>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formcs.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br>';
			$tblForm.='<table class="tblHead" width="600px" align="center" cellpadding="2" cellspacing="2">';
			$sqlText = "select * from itemcs_monitoring where id_monitcsemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			$nCat = 0;
			$nItems = 0;
			foreach($dtItems as $dtI){
				$nItems = $nItems +1;
				$sqlText = "select * from form_monitoring_cs f inner join category_form_cs c on f.id_catcs=c.id_catcs where f.id_formcs=".$dtI['ID_FORMCS'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATCS'];
				if($idCat != $nuevaIdCat){
					$nCat = $nCat + 1;
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItem"><td colspan="3"><b>'.$dtDatosItems['0']['CATCS_NAME'].'</b></td></tr>';
				}
				//Para verificar el item seleccionado
				$optItems = '<select id="item" name="item[]" class="txtPag">';
				$sel ="";
				if($dtI['ITEMCS_RESP']=='Y'){
					$sel="selected";	
				}
				$optItems .='<option value="1" '.$sel.'>YES</option>';
				$sel ="";
				if($dtI['ITEMCS_RESP']=='N'){
					$sel ="selected";	
				}
				$optItems .='<option value="2" '.$sel.'>NO</option>';
				$sel ="";
				if($dtI['ITEMCS_RESP']=='NA'){
					$sel ="selected";
				}
				$optItems .='<option value="3" '.$sel.'>N/A</option>';	
				$optItems .='</select>';
				
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMCS_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMCS_TEXT'].'</td><td>'.$optItems.'</td></tr>';
				if($dtI['ITEMCS_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMCS_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMCS_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItem"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE &nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITCSEMP_QUALIFICATION'],2).'%</b></td></tr>';
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			
			$sqlText = "select distinct(f.id_catcs) as idC, catcs_name from itemcs_monitoring i inner join form_monitoring_cs f on i.id_formcs=f.id_formcs inner join category_form_cs c on c.id_catcs=f.id_catcs where id_monitcsemp=".$_POST['idM'];
			$dtCat = $dbEx->selSql($sqlText);
			//Si tiene autofail recupera su categoria y sub categoria
			$selCatFail = "";
			$selSubFail = "";
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select fail_idfather from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtPadreFail = $dbEx->selSql($sqlText);
				$selCatFail = $dtPadreFail['0']['fail_idfather'];
				
				//Lista de autofail
				$selSubFail = $dtMonit['0']['FAIL_ID'];
				
				$optCatFail = '<option value=""></option>';
				
				$sqlText = "select * from category_monit_autofail where fail_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$sel = "";
					if($selCatFail==$dtCatF['FAIL_ID']){ $sel = "selected";}
						$optCatFail .='<option value="'.$dtCatF['FAIL_ID'].'" '.$sel.'>'.$dtCatF['FAIL_TEXT'].'</option>';
				}
				
				$sqlText = "select * from category_monit_autofail where fail_idfather=".$selCatFail;
				$dtSubFail = $dbEx->selSql($sqlText);
				$optSubFail = "";
				foreach($dtSubFail as $dtSFail){
					$sel = "";
					if($selSubFail = $dtSFail['FAIL_ID']){ $sel = "selected";}
						$optSubFail .='<option value="'.$dtSFail['FAIL_ID'].'" '.$sel.'>'.$dtSFail['FAIL_TEXT'].'</option>';
				}
				
			}
			//Si no tiene autofail
			else{
				$sqlText = "select * from category_monit_autofail where fail_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$optCatFail .='<option value="'.$dtCatF['FAIL_ID'].'">'.$dtCatF['FAIL_TEXT'].'</option>';
				}
				$optSubFail = '<option value=""></option>';
			}
			
			$tblForm .='<tr><td colspan="3">Auto-Fail: <select id="lsFail" onchange="getSubFail(this.value)">'.$optCatFail.'</select><br><span id="lySubFail"><select id="lsSubFail">'.$optSubFail.'</select></span></td></tr>';
			$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_FAIL'].'</textarea></td></tr>';
			
			$n = 1;
			foreach($dtCat as $dtC){
				$tblForm .='<tr><td colspan="3">'.$dtC['catcs_name'].'</td></tr>';
				if($n==1){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments1" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT1'].'</textarea></td></tr>';
				}
				else if($n==2){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments2" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT2'].'</textarea></td></tr>';
				}
				else if($n==3){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments3" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT3'].'</textarea></td></tr>';
				}
				else if($n==4){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments4" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT4'].'</textarea></td></tr>';
				}
				else if($n==5){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments5" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT5'].'</textarea></td></tr>';
				}
				else if($n==6){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments6" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT6'].'</textarea></td></tr>';
				}
				else if($n==7){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments7" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT7'].'</textarea></td></tr>';
				}
				else if($n==8){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments8" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT8'].'</textarea></td></tr>';
				}
				else if($n==9){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments9" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT9'].'</textarea></td></tr>';
				}
				else if($n==10){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments10" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT10'].'</textarea></td></tr>';
				}
				else if($n==11){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments11" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT11'].'</textarea></td></tr>';
				}
				$n = $n+1;
			}
			$tblForm .='<tr><td colspan="4"><input type="button" class="btn" value="Update form" onclick="saveUpFormCS('.$_POST['idM'].')"></td></tr>';
			$tblForm .='<tr><td><input type="hidden" id="nCat" value="'.$nCat.'"><input type="hidden" id="nItems" value="'.$nItems.'">';
			$tblForm .='</table>';
			
		}
		echo $tblForm;
		
	break;
	
	case 'saveUpFormCS':
		$items = $_POST['arrayItems'];
		$item = explode(" ",$items);
		$n=0;
		$m = 0;
		$valorEva = 0;
		$totalY = 0;
		$totalN = 0;
		
		$sqlText = "select * from category_form_cs where catcs_status='A' order by id_catcs";
		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
		$valorCatAdicional = 0;
		foreach($dtCat as $dtCt){
			$flag = true;
			$sqlText = "select id_formcs, id_catcs, formcs_item, formcs_text from form_monitoring_cs where formcs_status='A' and id_catcs=".$dtCt['ID_CATCS']." order by formcs_item";
			$dtitems = $dbEx->selSql($sqlText);
			foreach($dtitems as $dtIt){
				if($item[$m]==1 or $item[$m]==2 and $flag==true){
					$flag = false;
				}
				$m = $m+1;
			}
			if($flag == false){
				$nCatDistribuir = $nCatDistribuir + 1;
			}
			else if($flag==true){
				$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATCS_RATE'];
			}
		}
		if($nCatDistribuir >0){
			$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		}
		
		foreach($dtCat as $dtC){
			$sqlText = "select id_formcs, id_catcs, formcs_item, formcs_text from form_monitoring_cs where formcs_status='A' and id_catcs=".$dtC['ID_CATCS']." order by formcs_item";
			$dtitems = $dbEx->selSql($sqlText);
			$totalItems = 0;
			$valorPregunta = 0;
			//Id del primer item por categoria
			$idIni = $n;
			//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
			foreach($dtitems as $dtIt){
				if($item[$n]==1 or $item[$n]==2){
					$totalItems = $totalItems +1;
				}
				$n = $n+1;
			}
			//Id del ultimo item de la categoria
			$idFin = $n-1;
			if($totalItems>0){
				$valorPregunta = ($dtC['CATCS_RATE'] + $valorCatAdicional)/$totalItems;
			}
			foreach($dtitems as $dtI){
				if($item[$idIni]==1){
					$respuesta = "Y";
					$totalY = $totalY + 1;
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$totalN = $totalN + 1;
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "update itemcs_monitoring set itemcs_total='".$valor."', itemcs_resp='".$respuesta."' where id_monitcsemp=".$_POST['idM']." and id_formcs=".$dtI['id_formcs'];		
				
				$dbEx->updSql($sqlText);
				//$valorEva = $valorEva + $valor;
				
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		if($totalY > 0){
			$valorEva = ($totalY/($totalY + $totalN))*100;
		}
		
		if($_POST['listSubFail']>0){
			$sqlText = "update monitoringcs_emp set employee_id=".$_POST['emp'].", 
			monitcsemp_callreason='".$_POST['reason']."', 
			monitcsemp_contactid='".$_POST['contactId']."', 
			monitcsemp_account='".$_POST['account']."', 
			skill_id=".$_POST['skill'].", 
			monitcsemp_qualification='0', 
			fail_id=".$_POST['listSubFail'].", 
			monitcsemp_fail='".addslashes($_POST['fail'])."', 
			monitcsemp_comment1='".addslashes($_POST['comment1'])."', 
			monitcsemp_comment2='".addslashes($_POST['comment2'])."', 
			monitcsemp_comment3='".addslashes($_POST['comment3'])."', 
			monitcsemp_comment4='".addslashes($_POST['comment4'])."', 
			monitcsemp_comment5='".addslashes($_POST['comment5'])."', 
			monitcsemp_comment6='".addslashes($_POST['comment6'])."', 
			monitcsemp_comment7='".addslashes($_POST['comment7'])."', 
			monitcsemp_comment8='".addslashes($_POST['comment8'])."', 
			monitcsemp_comment9='".addslashes($_POST['comment9'])."',
			monitcsemp_comment10='".addslashes($_POST['comment10'])."', 
			monitcsemp_comment11='".addslashes($_POST['comment11'])."' where id_monitcsemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}
		else{
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringcs_emp set employee_id=".$_POST['emp'].", 
			monitcsemp_callreason='".$_POST['reason']."', 
			monitcsemp_contactid='".$_POST['contactId']."', 
			monitcsemp_account='".$_POST['account']."', 
			skill_id=".$_POST['skill'].", 
			monitcsemp_qualification='".number_format($valorEva,0)."', 
			fail_id='', 
			monitcsemp_fail='".addslashes($_POST['fail'])."', 
			monitcsemp_comment1='".addslashes($_POST['comment1'])."', 
			monitcsemp_comment2='".addslashes($_POST['comment2'])."', 
			monitcsemp_comment3='".addslashes($_POST['comment3'])."', 
			monitcsemp_comment4='".addslashes($_POST['comment4'])."', 
			monitcsemp_comment5='".addslashes($_POST['comment5'])."', 
			monitcsemp_comment6='".addslashes($_POST['comment6'])."', 
			monitcsemp_comment7='".addslashes($_POST['comment7'])."', 
			monitcsemp_comment8='".addslashes($_POST['comment8'])."', 
			monitcsemp_comment9='".addslashes($_POST['comment9'])."', 
			monitcsemp_comment10='".addslashes($_POST['comment10'])."', 
			monitcsemp_comment11='".addslashes($_POST['comment11'])."' where id_monitcsemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}
		echo $_POST['idM'];
	break;
	
	
		//Carga formulario para actualizar evaluacion de Sales
	case 'updEvaSales':
		$sqlText = "select ID_MONITSALESEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitsales_date,'%d/%m/%Y') as f1, MONITSALES_QUALIFICATION, MONITSALES_ENROLLID, FAIL_ID, SKILL_ID, MONITSALES_FAIL, MONITSALES_COMMENT1, MONITSALES_COMMENT2, MONITSALES_COMMENT3, MONITSALES_COMMENT4, MONITSALES_COMMENT5, MONITSALES_COMMENT6, MONITSALES_COMMENT7, MONITSALES_COMMENT8, MONITSALES_COMMENT9, MONITSALES_COMMENT10, MONITSALES_COMMENT11  from monitoringsales_emp m inner join employees e on e.employee_id=m.employee_id where id_monitsalesemp=".$_POST['idM'];
		
		$dtMonit = $dbEx->selSql($sqlText);
		$idCat = 0;
		$nuevaIdCat = 0;
		

		if($dbEx->numrows>0){
			
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A' order by firstname";
			$dtEmp = $dbEx->selSql($sqlText);
			foreach($dtEmp as $dtE){
				$sel = "";
				if($dtE['employee_id']==$dtMonit['0']['EMPLOYEE_ID']){
					$sel = "selected";	
				}	
				$optEmp .='<option value="'.$dtE['employee_id'].'" '.$sel.'>'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}
			
			$sqlText = "select * from tp_skills order by skill_name";
			$dtSkill = $dbEx->selSql($sqlText);

			$optSkill = '<option value="0"> -- </option>';
			foreach($dtSkill as $dtSk){
				$sel = "";
				if($dtSk['SKILL_ID']==$dtMonit['0']['SKILL_ID']){
					$sel = "selected";	
				}
				$optSkill .='<option value="'.$dtSk['SKILL_ID'].'" '.$sel.'>'.$dtSk['SKILL_NAME'].'</option>';
			}
			
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			$tblForm = '<table class="tblGreen" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Outbound Wireless New Service Number '.$dtMonit['0']['ID_MONITSALESEMP'].'</b></td></tr>';
			$tblForm .='<tr><td width="15%"><b>QA: </td><td width="50%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%"><b>Date: </td><td width="20%">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td><b>Agent name: </td><td><select id="lsEmp" onchange="getSuperv(this.value)" class="txtPag">'.$optEmp.'</select></td>
			<td><b>Enrollment ID: </td><td><input type="text" class="txtPag" value="'.$dtMonit['0']['MONITSALES_ENROLLID'].'" id="txtEnrollId"/></td></tr>';
			
			$tblForm .='<tr><td><b>Supervisor: </td>
			<td colspan="3"><input type="text" id="txtSuperv" class="txtPag" value="'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'" size="35" disabled="disabled" /></td></tr>';
			$tblForm .='<tr><td><b>Skill: </td><td colspan="3"><select id="lsSkill" class="txtPag">'.$optSkill.'</select></td></tr>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formsales.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br>';

			$tblForm.='<table class="tblHead" width="600px" align="center" cellpadding="2" cellspacing="2">';
			$sqlText = "select * from itemsales_monitoring where id_monitsalesemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			$nCat = 0;
			$nItems = 0;
			foreach($dtItems as $dtI){
				$nItems = $nItems +1;
				$sqlText = "select * from form_monitoring_sales f inner join category_form_sales c on f.id_catsales=c.id_catsales where f.id_formsales=".$dtI['ID_FORMSALES'];

				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATSALES'];
				if($idCat != $nuevaIdCat){
					$nCat = $nCat + 1;
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItemGreen"><td colspan="3"><b>'.$dtDatosItems['0']['CATSALES_NAME'].'</b></td></tr>';
				}
				//Para verificar el item seleccionado
				$optItems = '<select id="item" name="item[]" class="txtPag">';
				$sel ="";
				if($dtI['ITEMSALES_RESP']=='Y'){
					$sel="selected";	
				}
				$optItems .='<option value="1" '.$sel.'>YES</option>';
				$sel ="";
				if($dtI['ITEMSALES_RESP']=='N'){
					$sel ="selected";	
				}
				$optItems .='<option value="2" '.$sel.'>NO</option>';
				$sel ="";
				if($dtI['ITEMSALES_RESP']=='NA'){
					$sel ="selected";
				}
				$optItems .='<option value="3" '.$sel.'>N/A</option>';	
				$optItems .='</select>';
				
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMSALES_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMSALES_TEXT'].'</td><td>'.$optItems.'</td></tr>';
				if($dtI['ITEMSALES_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMSALES_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMSALES_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItemGreen"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE &nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITSALES_QUALIFICATION'],2).'%</b></td></tr>';
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			$sqlText = "select distinct(f.id_catsales) as idC, catsales_name from itemsales_monitoring i inner join form_monitoring_sales f on i.id_formsales=f.id_formsales inner join category_form_sales c on c.id_catsales=f.id_catsales where id_monitsalesemp=".$_POST['idM'];
			
			$dtCat = $dbEx->selSql($sqlText);
			//Si tiene autofail recupera su categoria y sub categoria
			$selCatFail = "";
			if($dtMonit['0']['FAIL_ID']>0){
				$selCatFail = $dtMonit['0']['FAIL_ID'];
				$sqlText = "select * from category_autofail_sales where failsales_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$sel = "";
					if($selCatFail==$dtCatF['FAILSALES_ID']){ $sel = "selected";}
						$optCatFail .='<option value="'.$dtCatF['FAILSALES_ID'].'" '.$sel.'>'.$dtCatF['FAILSALES_TEXT'].'</option>';
				}
				
			}
			//Si no tiene autofail
			else{
				$sqlText = "select * from category_autofail_sales where failsales_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$optCatFail .='<option value="'.$dtCatF['FAILSALES_ID'].'">'.$dtCatF['FAILSALES_TEXT'].'</option>';
				}
			}
			
			$tblForm .='<tr><td colspan="3">Auto-Fail: <select id="lsFail">'.$optCatFail.'</select></td></tr>';
			$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_FAIL'].'</textarea></td></tr>';
			
			$n = 1;
			foreach($dtCat as $dtC){
				$tblForm .='<tr><td colspan="3">'.$dtC['catcs_name'].'</td></tr>';
				if($n==1){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments1" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT1'].'</textarea></td></tr>';
				}
				else if($n==2){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments2" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT2'].'</textarea></td></tr>';
				}
				else if($n==3){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments3" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT3'].'</textarea></td></tr>';
				}
				else if($n==4){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments4" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT4'].'</textarea></td></tr>';
				}
				else if($n==5){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments5" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT5'].'</textarea></td></tr>';
				}
				else if($n==6){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments6" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT6'].'</textarea></td></tr>';
				}
				else if($n==7){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments7" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT7'].'</textarea></td></tr>';
				}
				else if($n==8){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments8" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT8'].'</textarea></td></tr>';
				}
				else if($n==9){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments9" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT9'].'</textarea></td></tr>';
				}
				else if($n==10){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments10" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT10'].'</textarea></td></tr>';
				}
				else if($n==11){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments11" cols="100" rows="3">'.$dtMonit['0']['MONITSALES_COMMENT11'].'</textarea></td></tr>';
				}
				$n = $n+1;
			}
			$tblForm .='<tr><td colspan="4"><input type="button" class="btn" value="Update form" onclick="saveUpFormSales('.$_POST['idM'].')"></td></tr>';
			$tblForm .='<tr><td><input type="hidden" id="nCat" value="'.$nCat.'"><input type="hidden" id="nItems" value="'.$nItems.'">';
			$tblForm .='</table>';
			
		}
		echo $tblForm;
		
	break;
	
	case 'saveUpFormSales':
			$items = $_POST['arrayItems'];
			$item = explode(" ",$items);
			$n=0;
			$m = 0;
			$valorEva = 0;
			$totalY = 0;
			$totalN = 0;
			$sqlText = "select * from category_form_sales where catsales_status='A' order by id_catsales";
		
			$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
			$valorCatDistribuir = 0;
			$nCatDistribuir = 0;
			$valorCatAdicional = 0;
			foreach($dtCat as $dtCt){
				$flag = true;
				$sqlText = "select id_formsales, id_catsales, formsales_item, formsales_text from form_monitoring_sales where formsales_status='A' and id_catsales=".$dtCt['ID_CATSALES']." order by formsales_item";
			
				$dtitems = $dbEx->selSql($sqlText);
				foreach($dtitems as $dtIt){
					if($item[$m]==1 or $item[$m]==2 and $flag==true){
						$flag = false;
					}
					$m = $m+1;
				}
				if($flag == false){
					$nCatDistribuir = $nCatDistribuir + 1;
					}
				else if($flag==true){
					$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATSALES_RATE'];
				}
			}
			if($nCatDistribuir >0){
				$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
			}
			foreach($dtCat as $dtC){
				$sqlText = "select id_formsales, id_catsales, formsales_item, formsales_text from form_monitoring_sales where formsales_status='A' and id_catsales=".$dtC['ID_CATSALES']." order by formsales_item";
			
				$dtitems = $dbEx->selSql($sqlText);
				$totalItems = 0;
				$valorPregunta = 0;
				//Id del primer item por categoria
				$idIni = $n;
				//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
				foreach($dtitems as $dtIt){
					if($item[$n]==1 or $item[$n]==2){
						$totalItems = $totalItems +1;
					}
					$n = $n+1;
				}
				//Id del ultimo item de la categoria
				$idFin = $n-1;
				if($totalItems>0){
					$valorPregunta = ($dtC['CATSALES_RATE'] + $valorCatAdicional)/$totalItems;
				}
				foreach($dtitems as $dtI){
					if($item[$idIni]==1){
						$respuesta = "Y";
						$totalY = $totalY + 1;
						$valor = $valorPregunta;
					}
					else if($item[$idIni]==2){
						$respuesta = "N";
						$totalN = $totalN + 1;
						$valor = 0;
					}
					else if($item[$idIni]==3){
						$respuesta = "NA";
						$valor =0;
					}
					$sqlText = "update itemsales_monitoring set itemsales_total='".$valor."', itemsales_resp='".$respuesta."' where id_monitsalesemp=".$_POST['idM']." and id_formsales=".$dtI['id_formsales'];		
				
					$dbEx->updSql($sqlText);
					//$valorEva = $valorEva + $valor;
					$idIni = $idIni +1;
				}//Termina segundo foreach
			}//Termina Categorias
			if($totalY > 0){
				$valorEva = ($totalY/($totalY + $totalN))*100;	
			}
			
			if($_POST['listFail']>0){
			$sqlText = "update monitoringsales_emp set employee_id=".$_POST['emp'].", 
			monitsales_enrollid='".$_POST['enrollId']."', 
			monitsales_qualification='0', 
			fail_id=".$_POST['listFail'].", 
			skill_id=".$_POST['skill'].", 
			monitsales_fail='".addslashes($_POST['fail'])."', 
			monitsales_comment1='".addslashes($_POST['comment1'])."', 
			monitsales_comment2='".addslashes($_POST['comment2'])."', 
			monitsales_comment3='".addslashes($_POST['comment3'])."', 
			monitsales_comment4='".addslashes($_POST['comment4'])."', 
			monitsales_comment5='".addslashes($_POST['comment5'])."', 
			monitsales_comment6='".addslashes($_POST['comment6'])."', 
			monitsales_comment7='".addslashes($_POST['comment7'])."', 
			monitsales_comment8='".addslashes($_POST['comment8'])."', 
			monitsales_comment9='".addslashes($_POST['comment9'])."', 
			monitsales_comment10='".addslashes($_POST['comment10'])."', 
			monitsales_comment11='".addslashes($_POST['comment11'])."' where id_monitsalesemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}
		else{
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringsales_emp set employee_id=".$_POST['emp'].", 
			monitsales_enrollid='".$_POST['enrollId']."', 
			monitsales_qualification='".number_format($valorEva,0)."', 
			fail_id='', 
			skill_id=".$_POST['skill'].", 
			monitsales_fail='".addslashes($_POST['fail'])."', 
			monitsales_comment1='".addslashes($_POST['comment1'])."', 
			monitsales_comment2='".addslashes($_POST['comment2'])."', 
			monitsales_comment3='".addslashes($_POST['comment3'])."', 
			monitsales_comment4='".addslashes($_POST['comment4'])."', 
			monitsales_comment5='".addslashes($_POST['comment5'])."', 
			monitsales_comment6='".addslashes($_POST['comment6'])."', 
			monitsales_comment7='".addslashes($_POST['comment7'])."', 
			monitsales_comment8='".addslashes($_POST['comment8'])."', 
			monitsales_comment9='".addslashes($_POST['comment9'])."', 
			monitsales_comment10='".addslashes($_POST['comment10'])."', 
			monitsales_comment11='".addslashes($_POST['comment11'])."' where id_monitsalesemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}
		echo $_POST['idM'];
			
	break;
		
		
		//Carga evaluacion para editar registro de new service
	case 'updEvaNS':
			$sqlText = "select ID_MONITNSEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitnsemp_date,'%d/%m/%Y') as f1, MONITNSEMP_QUALIFICATION, MONITNSEMP_TIME, MONITNSEMP_ENROLLID, MONITNSEMP_CONTACTID, FAIL_ID, SKILL_ID, MONITNSEMP_FAIL, MONITNSEMP_COMMENT1, MONITNSEMP_COMMENT2, MONITNSEMP_COMMENT3, MONITNSEMP_COMMENT4, MONITNSEMP_COMMENT5, MONITNSEMP_COMMENT6, MONITNSEMP_COMMENT7, MONITNSEMP_COMMENT8, MONITNSEMP_COMMENT9  from monitoringns_emp m inner join employees e on e.employee_id=m.employee_id where id_monitnsemp=".$_POST['idM'];
		
		
		$dtMonit = $dbEx->selSql($sqlText);
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and e.user_status=1 and pe.status_plxemp='A' order by firstname";
			$dtEmp = $dbEx->selSql($sqlText);
			foreach($dtEmp as $dtE){
				$sel = "";
				if($dtE['employee_id']==$dtMonit['0']['EMPLOYEE_ID']){
					$sel = "selected";	
				}	
				$optEmp .='<option value="'.$dtE['employee_id'].'" '.$sel.'>'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}
			
			$sqlText = "select * from tp_skills order by skill_name";
			$dtSkill = $dbEx->selSql($sqlText);

			$optSkill = '<option value="0"> -- </option>';
			foreach($dtSkill as $dtSk){
				$sel = "";
				if($dtSk['SKILL_ID']==$dtMonit['0']['SKILL_ID']){
					$sel = "selected";	
				}
				$optSkill .='<option value="'.$dtSk['SKILL_ID'].'" '.$sel.'>'.$dtSk['SKILL_NAME'].'</option>';
			}
			
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			$tblForm = '<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>New Services Monitoring Form NUMBER '.$dtMonit['0']['ID_MONITNSEMP'].'</b></td></tr>';
			$tblForm .='<tr><td width="15%"><b>QA: </td><td width="50%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%"><b>Enrollment ID: </td><td width="20%"><input type="text" class="txtPag" id="txtEnrollId" value="'.$dtMonit['0']['MONITNSEMP_ENROLLID'].'" /></td></tr>';
			$tblForm .='<tr><td><b>Agent name: </td>
			<td><select id="lsEmp" onchange="getSuperv(this.value)" class="txtPag">'.$optEmp.'</select></td>
			<td><b>Contact ID:</td>
			<td><input type="text" id="txtContactId" value="'.$dtMonit['0']['MONITNSEMP_CONTACTID'].'" class="txtPag"/></td></tr>';
			$tblForm .='<tr><td width="15%"><b>Date: </td><td width="35%">'.$dtMonit['0']['f1'].'</td>';
			
			$tblForm .='<tr><td><b>Supervisor: </td><td colspan="3"><input type="text" id="txtSuperv" class="txtPag" value="'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'" size="35" disabled="disabled" /></td></tr>';
			$tblForm .='<tr><td><b>Time:</td><td colspan="3"><input type="text" id="txtTime" class="txtPag" value="'.$dtMonit['0']['MONITNSEMP_TIME'].'"/></td></tr>';
			$tblForm .='<tr><td><b>Skill: </td><td colspan="3"><select id="lsSkill" class="txtPag">'.$optSkill.'</select>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formNewService.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br>';
			
			$tblForm.='<table class="tblHead" width="600px" align="center" cellpadding="2" cellspacing="2">';
			$sqlText = "select * from itemns_monitoring where id_monitnsemp=".$_POST['idM'];
			
			$dtItems = $dbEx->selSql($sqlText);
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			$nCat = 0;
			$nItems = 0;
			foreach($dtItems as $dtI){
				$nItems = $nItems +1;
				$sqlText = "select * from form_monitoring_ns f inner join category_form_newservice c on f.id_catns=c.id_catns where f.id_formns=".$dtI['ID_FORMNS'];

				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATNS'];
				if($idCat != $nuevaIdCat){
					$nCat = $nCat + 1;
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItemRed"><td colspan="3"><b>'.$dtDatosItems['0']['CATNS_NAME'].'</b></td></tr>';
				}
				//Para verificar el item seleccionado
				$optItems = '<select id="item" name="item[]" class="txtPag">';
				$sel ="";
				if($dtI['ITEMNS_RESP']=='Y'){
					$sel="selected";	
				}
				$optItems .='<option value="1" '.$sel.'>YES</option>';
				$sel ="";
				if($dtI['ITEMNS_RESP']=='N'){
					$sel ="selected";	
				}
				$optItems .='<option value="2" '.$sel.'>NO</option>';
				$sel ="";
				if($dtI['ITEMNS_RESP']=='NA'){
					$sel ="selected";
				}
				$optItems .='<option value="3" '.$sel.'>N/A</option>';	
				$optItems .='</select>';
				
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMNS_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMNS_TEXT'].'</td><td>'.$optItems.'</td></tr>';
				if($dtI['ITEMNS_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMNS_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMNS_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItemRed"><td colspan="3" align="right"><b>QA PERCENTAGE TOTAL SCORE &nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITNSEMP_QUALIFICATION'],2).'%</b></td></tr>';
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			$sqlText = "select distinct(f.id_catns) as idC, catns_name from itemns_monitoring i inner join form_monitoring_ns f on i.id_formns=f.id_formns inner join category_form_newservice c on c.id_catns=f.id_catns where id_monitnsemp=".$_POST['idM'];
			
			$dtCat = $dbEx->selSql($sqlText);
			//Si tiene autofail recupera su categoria y sub categoria
			$selCatFail = "";
			$selSubFail = "";
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select fail_idfather from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtPadreFail = $dbEx->selSql($sqlText);
				$selCatFail = $dtPadreFail['0']['fail_idfather'];
				
				//Lista de autofail
				$selSubFail = $dtMonit['0']['FAIL_ID'];
				
				$optCatFail = '<option value=""></option>';
				
				$sqlText = "select * from category_monit_autofail where fail_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$sel = "";
					if($selCatFail==$dtCatF['FAIL_ID']){ $sel = "selected";}
						$optCatFail .='<option value="'.$dtCatF['FAIL_ID'].'" '.$sel.'>'.$dtCatF['FAIL_TEXT'].'</option>';
				}
				
				$sqlText = "select * from category_monit_autofail where fail_idfather=".$selCatFail;
				$dtSubFail = $dbEx->selSql($sqlText);
				$optSubFail = "";
				foreach($dtSubFail as $dtSFail){
					$sel = "";
					if($selSubFail = $dtSFail['FAIL_ID']){ $sel = "selected";}
						$optSubFail .='<option value="'.$dtSFail['FAIL_ID'].'" '.$sel.'>'.$dtSFail['FAIL_TEXT'].'</option>';
				}
				
			}
			//Si no tiene autofail
			else{
				$sqlText = "select * from category_monit_autofail where fail_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$optCatFail .='<option value="'.$dtCatF['FAIL_ID'].'">'.$dtCatF['FAIL_TEXT'].'</option>';
				}
				$optSubFail = '<option value=""></option>';
			}
			
			$tblForm .='<tr><td colspan="3">Auto-Fail: <select id="lsFail" onchange="getSubFail(this.value)">'.$optCatFail.'</select><br><span id="lySubFail"><select id="lsSubFail">'.$optSubFail.'</select></span></td></tr>';
			$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_FAIL'].'</textarea></td></tr>';
			
			$n = 1;
			foreach($dtCat as $dtC){
				$tblForm .='<tr><td colspan="3">'.$dtC['catns_name'].'</td></tr>';
				if($n==1){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments1" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT1'].'</textarea></td></tr>';
				}
				else if($n==2){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments2" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT2'].'</textarea></td></tr>';
				}
				else if($n==3){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments3" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT3'].'</textarea></td></tr>';
				}
				else if($n==4){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments4" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT4'].'</textarea></td></tr>';
				}
				else if($n==5){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments5" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT5'].'</textarea></td></tr>';
				}
				else if($n==6){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments6" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT6'].'</textarea></td></tr>';
				}
				else if($n==7){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments7" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT7'].'</textarea></td></tr>';
				}
				else if($n==8){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments8" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT8'].'</textarea></td></tr>';
				}
				else if($n==9){
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtComments9" cols="100" rows="3">'.$dtMonit['0']['MONITNSEMP_COMMENT9'].'</textarea></td></tr>';
				}
				$n = $n+1;
			}
			$tblForm .='<tr><td colspan="4"><input type="button" class="btn" value="Update form" onclick="saveUpFormNS('.$_POST['idM'].')"></td></tr>';
			$tblForm .='<tr><td><input type="hidden" id="nCat" value="'.$nCat.'"><input type="hidden" id="nItems" value="'.$nItems.'">';
			$tblForm .='</table>';
			
		}
		echo $tblForm;
		
	break;
	
	case 'saveUpFormNS':
		$items = $_POST['arrayItems'];
		$item = explode(" ",$items);
		$n=0;
		$m = 0;
		$valorEva = 0;
		$totalY = 0;
		$totalN = 0;
		
		$sqlText = "select * from category_form_newservice where catns_status='A' order by id_catns";

		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
		$valorCatAdicional = 0;
		foreach($dtCat as $dtCt){
			$flag = true;
			$sqlText = "select id_formns, id_catns, formns_item, formns_text from form_monitoring_ns where formns_status='A' and id_catns=".$dtCt['ID_CATNS']." order by formns_item";
			
			$dtitems = $dbEx->selSql($sqlText);
			foreach($dtitems as $dtIt){
				if($item[$m]==1 or $item[$m]==2 and $flag==true){
					$flag = false;
				}
				$m = $m+1;
			}
			if($flag == false){
				$nCatDistribuir = $nCatDistribuir + 1;
			}
			else if($flag==true){
				$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATCS_RATE'];
			}
		}
		if($nCatDistribuir >0){
			$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		}
		
		foreach($dtCat as $dtC){
			$sqlText = "select id_formns, id_catns, formns_item, formns_text from form_monitoring_ns where formns_status='A' and id_catns=".$dtC['ID_CATNS']." order by formns_item";
			
			$dtitems = $dbEx->selSql($sqlText);
			$totalItems = 0;
			$valorPregunta = 0;
			//Id del primer item por categoria
			$idIni = $n;
			//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
			foreach($dtitems as $dtIt){
				if($item[$n]==1 or $item[$n]==2){
					$totalItems = $totalItems +1;
				}
				$n = $n+1;
			}
			//Id del ultimo item de la categoria
			$idFin = $n-1;
			if($totalItems>0){
				$valorPregunta = ($dtC['CATNS_RATE'] + $valorCatAdicional)/$totalItems;
			}
			foreach($dtitems as $dtI){
				if($item[$idIni]==1){
					$respuesta = "Y";
					$totalY = $totalY + 1;
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$totalN = $totalN + 1;
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "update itemns_monitoring set itemns_total='".$valor."', itemns_resp='".$respuesta."' where id_monitnsemp=".$_POST['idM']." and id_formns=".$dtI['id_formns'];		
				
				$dbEx->updSql($sqlText);
				//$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		
		if($_POST['listSubFail']>0){
			$sqlText = "update monitoringns_emp set monitnsemp_qualification='0', 
			employee_id=".$_POST['emp'].", 
			monitnsemp_time='".$_POST['time']."', 
			monitnsemp_enrollid='".$_POST['enrollId']."', 
			monitnsemp_contactid='".$_POST['contactId']."', 
			skill_id=".$_POST['skill'].",  
			fail_id=".$_POST['listSubFail'].", 
			monitnsemp_fail='".addslashes($_POST['fail'])."', 
			monitnsemp_comment1='".addslashes($_POST['comment1'])."', 
			monitnsemp_comment2='".addslashes($_POST['comment2'])."', 
			monitnsemp_comment3='".addslashes($_POST['comment3'])."', 
			monitnsemp_comment4='".addslashes($_POST['comment4'])."', 
			monitnsemp_comment5='".addslashes($_POST['comment5'])."', 
			monitnsemp_comment6='".addslashes($_POST['comment6'])."', 
			monitnsemp_comment7='".addslashes($_POST['comment7'])."', 
			monitnsemp_comment8='".addslashes($_POST['comment8'])."',
			 monitnsemp_comment9='".addslashes($_POST['comment9'])."' where id_monitnsemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}
		else{
			if($totalY > 0){
				$valorEva =($totalY/($totalY + $totalN))*100;
			}
			
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringns_emp set monitnsemp_qualification='".number_format($valorEva,0)."', 
			employee_id=".$_POST['emp'].", 
			monitnsemp_time='".$_POST['time']."', 
			monitnsemp_enrollid='".$_POST['enrollId']."', 
			monitnsemp_contactid='".$_POST['contactId']."', 
			skill_id=".$_POST['skill'].", 
			fail_id='', 
			monitnsemp_fail='".addslashes($_POST['fail'])."',
			monitnsemp_comment1='".addslashes($_POST['comment1'])."',
			monitnsemp_comment2='".addslashes($_POST['comment2'])."', 
			monitnsemp_comment3='".addslashes($_POST['comment3'])."', 
			monitnsemp_comment4='".addslashes($_POST['comment4'])."', 
			monitnsemp_comment5='".addslashes($_POST['comment5'])."', 
			monitnsemp_comment6='".addslashes($_POST['comment6'])."', 
			monitnsemp_comment7='".addslashes($_POST['comment7'])."', 
			monitnsemp_comment8='".addslashes($_POST['comment8'])."', 
			monitnsemp_comment9='".addslashes($_POST['comment9'])."' where id_monitnsemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}
		echo $_POST['idM'];
	break;
	
	//Editar registro de chat
	case 'updEvaChat':
			$sqlText = "select ID_MONITCHATEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitchatemp_date,'%d/%m/%Y') as f1, MONITCHATEMP_QUALIFICATION, MONITCHATEMP_REASON, MONITCHATEMP_ACCOUNT, FAILCHAT_ID, SKILL_ID, MONITCHATEMP_FAIL, MONITCHATEMP_COMMENT  from monitoringchat_emp m inner join employees e on e.employee_id=m.employee_id where id_monitchatemp=".$_POST['idM'];
		
		
		$dtMonit = $dbEx->selSql($sqlText);
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			$tblForm = '<table class="tblRepQA" width="60%" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Chat Monitoring Form NUMBER '.$dtMonit['0']['ID_MONITCHATEMP'].'</b></td></tr>';
			$tblForm .='<tr><td width="15%"><b>QA: </td><td colspan="3">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td><b>Agent name: </td><td colspan="3">'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td></tr>';
			$tblForm .='<tr><td><b>Account #: </td><td colspan="3">'.$dtMonit['0']['MONITCHATEMP_ACCOUNT'].'</td></tr>';
			$tblForm .='<tr><td><b>Date: </td><td colspan="3">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td><b>Reason for chat: </td><td colspan="3">'.$dtMonit['0']['MONITCHATEMP_REASON'].'</td></tr>';
			$tblForm .='<tr><td><b>Skill: </td><td colspan="3">'.$nomSkill.'</td></tr>';
			
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formchat.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br>';

			$tblForm.='<table class="tblHead" width="600px" align="center" cellpadding="2" cellspacing="2">';
			$sqlText = "select * from itemchat_monitoring where id_monitchatemp=".$_POST['idM'];
			
			$dtItems = $dbEx->selSql($sqlText);
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			$nCat = 0;
			$nItems = 0;
			foreach($dtItems as $dtI){
				$nItems = $nItems +1;
				$sqlText = "select * from form_monitoring_chat f inner join category_form_chat c on f.id_catchat=c.id_catchat where f.id_formchat=".$dtI['ID_FORMCHAT'];

				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATCHAT'];
				if($idCat != $nuevaIdCat){
					$nCat = $nCat + 1;
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItemChat"><td colspan="3"><b>'.$dtDatosItems['0']['CATCHAT_NAME'].'</b></td></tr>';
				}
				//Para verificar el item seleccionado
				$optItems = '<select id="item" name="item[]" class="txtPag">';
				$sel ="";
				if($dtI['ITEMCHAT_RESP']=='Y'){
					$sel="selected";	
				}
				$optItems .='<option value="1" '.$sel.'>YES</option>';
				$sel ="";
				if($dtI['ITEMCHAT_RESP']=='N'){
					$sel ="selected";	
				}
				$optItems .='<option value="2" '.$sel.'>NO</option>';
				$sel ="";
				if($dtI['ITEMCHAT_RESP']=='NA'){
					$sel ="selected";
				}
				$optItems .='<option value="3" '.$sel.'>N/A</option>';	
				$optItems .='</select>';
				
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMCHAT_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMCHAT_TEXT'].'</td><td>'.$optItems.'</td></tr>';
				if($dtI['ITEMCHAT_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMCHAT_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMCHAT_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			$tblForm .='<tr class="showItemchat"><td colspan="3" align="right"><b>PERCENT CHAT QUALITY SCORE &nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITCHATEMP_QUALIFICATION'],2).'%</b></td></tr>';
			$tblForm .='<tr><td colspan="2"><b>Total Yes: '.$totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA.'</b></td></tr>';
			$sqlText = "select distinct(f.id_catchat) as idC, catchat_name from itemchat_monitoring i inner join form_monitoring_chat f on i.id_formchat=f.id_formchat inner join category_form_chat c on c.id_catchat=f.id_catchat where id_monitchatemp=".$_POST['idM'];
			
			$dtCat = $dbEx->selSql($sqlText);
			//Si tiene autofail recupera su categoria y sub categoria
			$selCatFail = "";
			if($dtMonit['0']['FAILCHAT_ID']>0){
				$selCatFail = $dtMonit['0']['FAILCHAT_ID'];
				$sqlText = "select * from category_autofail_chat where failchat_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$sel = "";
					if($selCatFail==$dtCatF['FAILCHAT_ID']){ $sel = "selected";}
						$optCatFail .='<option value="'.$dtCatF['FAILCHAT_ID'].'" '.$sel.'>'.$dtCatF['FAILCHAT_TEXT'].'</option>';
				}
				
			}
			//Si no tiene autofail
			else{
				$sqlText = "select * from category_autofail_chat where failchat_idfather is null";
				$dtCatFail = $dbEx->selSql($sqlText);
				$optCatFail = '<option value=""></option>';
				foreach($dtCatFail as $dtCatF){
					$optCatFail .='<option value="'.$dtCatF['FAILCHAT_ID'].'">'.$dtCatF['FAILCHAT_TEXT'].'</option>';
				}
			}
			
			$tblForm .='<tr><td colspan="3">Auto-Fail: <select id="lsFail">'.$optCatFail.'</select></td></tr>';
			$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="100" rows="3">'.$dtMonit['0']['MONITCHATEMP_FAIL'].'</textarea></td></tr>';
			$tblForm .='<tr><td colspan="3">Comments: </td></tr>';
			$tblForm .='<tr><td colspan="3"><textarea id="txtComment" cols="100" rows="3">'.$dtMonit['0']['MONITCHATEMP_COMMENT'].'</textarea></td></tr>';
			
			$tblForm .='<tr><td colspan="4"><input type="button" class="btn" value="Update form" onclick="saveUpFormChat('.$_POST['idM'].')"></td></tr>';
			$tblForm .='<tr><td><input type="hidden" id="nCat" value="'.$nCat.'"><input type="hidden" id="nItems" value="'.$nItems.'">';
			$tblForm .='</table>';
			
		}
		echo $tblForm;
		
	break;
	
	case 'saveUpFormChat':
		$items = $_POST['arrayItems'];
		$item = explode(" ",$items);
		$n=0;
		$m = 0;
		$totalY = 0;
		$totalN = 0;
		$valorEva = 0;
		
		$sqlText = "select * from category_form_chat where catchat_status='A' order by id_catchat";

		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
		$valorCatAdicional = 0;
		foreach($dtCat as $dtCt){
			$flag = true;
			$sqlText = "select id_formchat, id_catchat, formchat_item, formchat_text from form_monitoring_chat where formchat_status='A' and id_catchat=".$dtCt['ID_CATCHAT']." order by formchat_item";
			
			$dtitems = $dbEx->selSql($sqlText);
			foreach($dtitems as $dtIt){
				if($item[$m]==1 or $item[$m]==2 and $flag==true){
					$flag = false;
				}
				$m = $m+1;
			}
			if($flag == false){
				$nCatDistribuir = $nCatDistribuir + 1;
			}
			else if($flag==true){
				$valorCatDistribuir = $valorCatDistribuir + $dtCt['CATCHAT_RATE'];
			}
		}
		if($nCatDistribuir >0){
			$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		}
		foreach($dtCat as $dtC){
			$sqlText = "select id_formchat, id_catchat, formchat_item, formchat_text from form_monitoring_chat where formchat_status='A' and id_catchat=".$dtC['ID_CATCHAT']." order by formchat_item";
			
			$dtitems = $dbEx->selSql($sqlText);
			$totalItems = 0;
			$valorPregunta = 0;
			//Id del primer item por categoria
			$idIni = $n;
			//Recorre primera vez para encontrar la cantidad de preguntas contestadas de la categoria
			foreach($dtitems as $dtIt){
				if($item[$n]==1 or $item[$n]==2){
					$totalItems = $totalItems +1;
				}
				$n = $n+1;
			}
			//Id del ultimo item de la categoria
			$idFin = $n-1;
			if($totalItems>0){
				$valorPregunta = ($dtC['CATCHAT_RATE'] + $valorCatAdicional)/$totalItems;
			}
			foreach($dtitems as $dtI){
				if($item[$idIni]==1){
					$respuesta = "Y";
					$totalY = $totalY + 1;
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$totalN = $totalN + 1;
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "update itemchat_monitoring set itemchat_total='".$valor."', itemchat_resp='".$respuesta."' where id_monitchatemp=".$_POST['idM']." and id_formchat=".$dtI['id_formchat'];		
				
				$dbEx->updSql($sqlText);
				$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		
		if($totalY > 0){
			$valorEva = ($totalY/($totalY + $totalN))*100;	
		}
		
		if($_POST['listFail']>0){
			$sqlText = "update monitoringchat_emp set monitchatemp_qualification='0', failchat_id=".$_POST['listFail'].", monitchatemp_fail='".$_POST['fail']."', monitchatemp_comment='".$_POST['comment']."' where id_monitchatemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}
		else{
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringchat_emp set monitchatemp_qualification='".$valorEva."', failchat_id='', monitchatemp_fail='".$_POST['fail']."', monitchatemp_comment='".$_POST['comment']."' where id_monitchatemp=".$_POST['idM'];
			$dbEx->updSql($sqlText);
		}

		echo $_POST['idM'];
	break;
	
	case 'changeAttachChat':
		$rslt = cargaPag("../mtto/formUpAttachChat.php");
		$rslt = str_replace("<!--IdM-->",$_POST['idM'],$rslt);
		echo $rslt;
		
	break;
	
	case 'changeAttachCS':
		$rslt = cargaPag("../mtto/formUpAttachCS.php");
		$rslt = str_replace("<!--IdM-->",$_POST['idM'],$rslt);
		echo $rslt;
		
	break;
	
	case 'changeAttachSales':
		$rslt = cargaPag("../mtto/formUpAttachSales.php");
		$rslt = str_replace("<!--IdM-->",$_POST['idM'],$rslt);
		echo $rslt;
		
	break;
	
	case 'changeAttachNS':
		$rslt = cargaPag("../mtto/formUpAttachNS.php");
		$rslt = str_replace("<!--IdM-->",$_POST['idM'],$rslt);
		echo $rslt;
		
	break;
	
	//Filtros del weekly
	case 'filtrosWeekly':
		$rslt = cargaPag("../mtto/filtrosWeekly.php");
		$sqlText = "select e.employee_id, firstname, lastname, name_place, name_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart = pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and e.user_status=1 and status_plxemp='A'";
		$dtPlaza = $dbEx->selSql($sqlText);
		
		//Variable pasa para verificar que los usuarios tengan empleados para evaluar
		$pasa = false;
		//Si cumple las siguientes condiciones le muestra todos los empleados
		if($dtPlaza['0']['name_depart']=='QUALITY' or $_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
			$sqlText = "select distinct(e.employee_id) as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and pe.status_plxemp='A' and user_status=1 order by firstname";
			$pasa = true;
		}
		//Si es gerente de area solo para los q tiene permisos
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select e.employee_id as employee, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id where pe.status_plxemp='A' and user_status=1 and pe.id_placexdep in (".$_SESSION['permisos'].") order by firstname";
			$pasa = true;
		}
		
		//Si es supervisor, verifica la cuenta a la cual pertenece y le muestra los agentes de esa cuenta 
		else if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select employee_id as employee, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1  order by firstname";
			$pasa = true;
		}
		if($dbEx->numrows>0){
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		if($pasa){
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
				}
			}
		}
		else{
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		$optSup = "";
		$optCuenta = "";
		if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='WORKFORCE' or $dtPlaza['0']['name_depart']=='QUALITY')
		{
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
			$dtSup = $dbEx->selSql($sqlText);
			$optSup .='<option value="0">[ALL]</option>';
			foreach($dtSup as $dtS){
				$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
			//Lista de cuentas
			$sqlText = "select * from account where account_status='A' and id_typeacc=2 order by name_account ";
			$dtCuenta = $dbEx->selSql($sqlText);
			$optCuenta = '<option value="0">[ALL]</option>';
			foreach($dtCuenta as $dtC){
				$optCuenta .= '<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
			}
			
		}
		else{
			$optSup ='<option value="'.$dtPlaza['0']['employee_id'].'">'.$dtPlaza['0']['firstname']." ".$dtPlaza['0']['lastname'].'</option>';
			
			$optCuenta .='<option value="0"></option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where name_depart='QUALITY' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtQa = $dbEx->selSql($sqlText);
		$optQa = "";
		foreach($dtQa as $dtQ){
			$optQa .='<option value="'.$dtQ['employee_id'].'">'.$dtQ['firstname']." ".$dtQ['lastname'].'</option>';		
		}

		$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optQa-->",$optQa,$rslt);
		echo $rslt;
	break;
	
	//Weekly Report
	case 'loadWeeklyReport':
		$arrayAgentes = $_POST['arrayAgentes'];
		$agentes = explode(" ",$arrayAgentes);
		$nAgentes = count($agentes);
		
		$listaAgentes = "";
		for($i = 0; $i<$nAgentes; $i++){
			if($i==0){
				$listaAgentes .= $agentes[$i];	
			}
			else{
				$listaAgentes .=", ".$agentes[$i];	
			}
		}
		
		$filtroCS = " where monitcsemp_maker='Q' and monitcsemp_averages=1 ";
		$filtroNS = " where monitnsemp_maker='Q' and monitnsemp_averages=1 ";
		$filtroSales = " where monitsales_maker='Q' and monitsales_averages=1 ";
		$filtro = " where (pd.id_role=2) and p.name_place!='ATEAM AGENT' ";
		
		$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$start = strtotime($fec_ini);
		$end = strtotime($fec_fin);
		//Verificamos que el primer dia seleccionado sea lunes y el ultimo seleccionado sea sabado y la cantidad de dias entre las dos fechas sea 6
		$nFecha = strtotime(date("Y/m/d",$start));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia>1){
			$rslt = 1;
			echo $rslt;
			break;	
		}
		$nFecha = strtotime(date("Y/m/d",$end));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia!=6){
			$rslt = 2;
			echo $rslt;
			break;	
		}
		$ndias = n_dias($fec_ini,$fec_fin);
		if($ndias>7){
			$rslt = 3;
			echo $rslt;
			break;
		}
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		
		if($_POST['agentes0']>0){
			$filtro .=" and e.employee_id in (".$listaAgentes.") ";

		}
		if($_POST['qa']>0){
			$filtroCS .=" and qa_agent =".$_POST['qa'];
			$filtroNS .=" and qa_agent =".$_POST['qa'];
			$filtroSales .=" and qa_agent =".$_POST['qa'];
		}
		if($_POST['status']>=0){
			$filtro .=" and user_status= ".$_POST['status'];
		}

		$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".$filtro." and status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$rslt = "";
		$rslt .= '<div class="scroll">';
		$rslt .= '<table class="tblRepQA" border="1" align="center" cellpadding="2" cellspacing="1" >';
		if($dbEx->numrows>0){
			$rslt .='<tr><td>
			<form target="_blank" action="mtto/xls_weekly.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="filtroCS" value="'.$filtroCS.'">
			<input type="hidden" name="filtroNS" value="'.$filtroNS.'">
			<input type="hidden" name="filtroSales" value="'.$filtroSales.'">
			<input type="hidden" name="fec_ini" value="'.$fec_ini.'">
			<input type="hidden" name="fec_fin" value="'.$fec_fin.'">
			</td></tr>';

			$rslt .='<tr class="showItem"><td>Badge</td><td>Agents</td><td>Supervisor</td><td>Quality Agent</td><td>MON</td><td>TUES</td>
			<td>WED</td><td>THURS</td><td>FRI</td><td>SAT</td><td>Total</td><td>1</td><td>2</td><td>3</td><td>4</td></tr>';
			
			//Acumula el total de promedios
			//Suma los promedios
			$sumaTotalEva = 0;
			$promTotalEva = 0;
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
				//Declarar nueva linea con todos los datos del registro del agente, si el agente posee evaluaciones escribir sus datos, si no tiene evaluaciones no se escribe la linea
				$linea = "";
				$flag = false;
				//Titulos
				$linea .='<tr><td>'.$dtE['username'].'</td>
				<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>
				<td>'.$nombreSup.'</td>';
				
				//Obtiene los QA agents 
				$listaQa = "";
				$qas = "";
				$nQa = 0;
				$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringcs_emp m inner join employees e on m.qa_agent=e.employee_id ".$filtroCS." and monitcsemp_date between '".$fec_ini."' and '".$fec_fin."' and m.employee_id=".$dtE['employee_id'];
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
				
				$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringns_emp m inner join employees e on m.qa_agent=e.employee_id ".$filtroNS." and monitnsemp_date between '".$fec_ini."' and '".$fec_fin."' and m.employee_id=".$dtE['employee_id']." ".$filtroQa;
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
				
				$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringsales_emp m inner join employees e on m.qa_agent=e.employee_id ".$filtroSales."  and monitsales_date between '".$fec_ini."' and '".$fec_fin."' and m.employee_id=".$dtE['employee_id']." ".$filtroQa;
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
					$sqlText = "select monitcsemp_qualification from monitoringcs_emp ".$filtroCS." and monitcsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
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
					$sqlText = "select monitnsemp_qualification from monitoringns_emp ".$filtroNS." and monitnsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
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
					$sqlText = "select monitsales_qualification from monitoringsales_emp ".$filtroSales." and monitsales_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
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
				
				if($total<=69){
					$font = 'bgcolor="#F8402C"';	
				}
				else if($total>69 and $total <80){
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
				$linea .='<td '.$font.'><b>'.$totalMostrar.'</b></td>';
				$linea .= $calif.'</tr>';
				if($flag){
					$rslt .= $linea;	
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
			$rslt .='<tr><td></td><td></td><td></td><td align="right"><b>TOTAL AVERAGE</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td '.$font.'><b>'.$promEvas.'</b></td></tr>';
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
		//Filtros del Monthly
	case 'filtrosMonthly':
		$rslt = cargaPag("../mtto/filtrosMonthly.php");
		$sqlText = "select e.employee_id, firstname, lastname, name_place, name_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart = pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and e.user_status=1 and status_plxemp='A'";
		$dtPlaza = $dbEx->selSql($sqlText);
		
		//Variable pasa para verificar que los usuarios tengan empleados para evaluar
		$pasa = false;
		//Si cumple las siguientes condiciones le muestra todos los empleados
		if($dtPlaza['0']['name_depart']=='QUALITY' or $_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
			$sqlText = "select distinct(e.employee_id) as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and pe.status_plxemp='A' and user_status=1 order by firstname";
			$pasa = true;
		}
		//Si es gerente de area solo para los q tiene permisos
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select e.employee_id as employee, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id where pe.status_plxemp='A' and user_status=1 and pe.id_placexdep in (".$_SESSION['permisos'].") order by firstname";
			$pasa = true;
		}
		
		//Si es supervisor, verifica la cuenta a la cual pertenece y le muestra los agentes de esa cuenta 
		else if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select employee_id as employee, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1  order by firstname";
			$pasa = true;
		}
		if($dbEx->numrows>0){
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		if($pasa){
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
				}
			}
		}
		else{
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		$optSup = "";
		$optCuenta = "";
		if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='WORKFORCE' or $dtPlaza['0']['name_depart']=='QUALITY')
		{
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
			$dtSup = $dbEx->selSql($sqlText);
			$optSup .='<option value="0">[ALL]</option>';
			foreach($dtSup as $dtS){
				$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
			//Lista de cuentas
			$sqlText = "select * from account where account_status='A' and id_typeacc=2 order by name_account ";
			$dtCuenta = $dbEx->selSql($sqlText);
			$optCuenta = '<option value="0">[ALL]</option>';
			foreach($dtCuenta as $dtC){
				$optCuenta .= '<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
			}
			
		}
		else{
			$optSup ='<option value="'.$dtPlaza['0']['employee_id'].'">'.$dtPlaza['0']['firstname']." ".$dtPlaza['0']['lastname'].'</option>';
			
			$optCuenta .='<option value="0"></option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where name_depart='QUALITY' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtQa = $dbEx->selSql($sqlText);
		$optQa = "";
		foreach($dtQa as $dtQ){
			$optQa .='<option value="'.$dtQ['employee_id'].'">'.$dtQ['firstname']." ".$dtQ['lastname'].'</option>';		
		}

		$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optQa-->",$optQa,$rslt);
		echo $rslt;
	break;
	
	case 'loadMonthlyReport':
		$arrayAgentes = $_POST['arrayAgentes'];
		$agentes = explode(" ",$arrayAgentes);
		$nAgentes = count($agentes);
		
		$listaAgentes = "";
		for($i = 0; $i<$nAgentes; $i++){
			if($i==0){
				$listaAgentes .= $agentes[$i];	
			}
			else{
				$listaAgentes .=", ".$agentes[$i];	
			}
		}
		$filtroCS = " and monitcsemp_averages=1 ";
		$filtroNS = " and monitnsemp_averages=1 ";
		$filtroSales = " and monitsales_averages=1 ";

		$filtro = " where (pd.id_role=2) and p.name_place!='ATEAM AGENT' ";
		
		$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$start = strtotime($fec_ini);
		$end = strtotime($fec_fin);
		//Verificamos que el primer dia seleccionado sea lunes y el ultimo seleccionado sea sabado
		$nFecha = strtotime(date("Y/m/d",$start));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia>1){
			$rslt = 1;
			echo $rslt;
			break;	
		}
		$nFecha = strtotime(date("Y/m/d",$end));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia!=6){
			$rslt = 2;
			echo $rslt;
			break;	
		}
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		
		if($_POST['agentes0']>0){
			$filtro .=" and e.employee_id in (".$listaAgentes.") ";

		}
		if($_POST['qa']>0){
			$filtroCS .=" and qa_agent =".$_POST['qa'];
			$filtroNS .=" and qa_agent =".$_POST['qa'];
			$filtroSales .=" and qa_agent =".$_POST['qa'];
		}
		if($_POST['status']>=0){
			$filtro .=" and user_status= ".$_POST['status'];
		}
		
		$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".$filtro." and status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$rslt = "";
		$rslt .= '<div class="scroll">';
		$rslt .= '<table class="tblRepQA" border="1" align="center" cellpadding="2" cellspacing="1" >';
		if($dbEx->numrows>0){
			$rslt .='<tr><td align="center">
			<form target="_blank" action="mtto/xls_monthly.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="filtroCS" value="'.$filtroCS.'">
			<input type="hidden" name="filtroNS" value="'.$filtroNS.'">
			<input type="hidden" name="filtroSales" value="'.$filtroSales.'">
			<input type="hidden" name="fec_ini" value="'.$fec_ini.'">
			<input type="hidden" name="fec_fin" value="'.$fec_fin.'">
			</td></tr>';
			
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
			$rslt .='<tr class="showItem"><td width="10%">Badge</td><td width="30%">Month to Date</td>';
			for($i=1; $i<=$totalSemanas; $i++){
				$rslt .='<td width="10%">Average Week '.$i.'</td>';
			}
			$rslt .='<td width="10%">MTD per agent</td></tr>';
			//Empieza a recorrer los agentes para obtener el reporte
			
			foreach($dtEmp as $dtE){
				$nEvaMes = 0;
				$sumaEvaMes = 0;
				$sumaPromMes = 0;
				$nPromMes = 0;
				
				//Declarar nueva linea con todos los datos del registro del agente, si el agente posee evaluaciones escribir sus datos, si no tiene evaluaciones no se escribe la linea
				$linea = "";
				$flag = false;
				
				$linea .='<tr><td>'.$dtE['username'].'</td><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
				//Obtiene la suma de scores y total de evaluaciones en la semana 
				for($i=1; $i<=$totalSemanas; $i++){
					$sumaEvaSemana = 0;
					$nEvaSemana = 0;
					//Busca el total de evaluaciones de CS a la semana
					$sqlText = "select sum(monitcsemp_qualification) as sumaCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' ".$filtroCS;
					$dtSumaCS = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sumaEvaSemana = $sumaEvaSemana + $dtSumaCS['0']['sumaCS'];
					}
					
					$sqlText2 = "select count(1) as cantCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' ".$filtroCS;
					$dtCantCS = $dbEx->selSql($sqlText2);
					if($dbEx->numrows>0){
						$nEvaSemana = $nEvaSemana + $dtCantCS['0']['cantCS'];
					}
					//Busca el total de evaluaciones de NS a la semana
					$sqlText = "select sum(monitnsemp_qualification) as sumaNS from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' ".$filtroNS;
					$dtSumaNS = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sumaEvaSemana = $sumaEvaSemana + $dtSumaNS['0']['sumaNS'];
					}
					$sqlText = "select count(1) as cantNS from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' ".$filtroNS;
					$dtCantNS = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$nEvaSemana = $nEvaSemana + $dtCantNS['0']['cantNS'];
					}
					
					//Busca el total de evaluaciones de Sales a la semana
					$sqlText = "select sum(monitsales_qualification) as sumaSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' ".$filtroSales;
					$dtSumaSales = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$sumaEvaSemana = $sumaEvaSemana + $dtSumaSales['0']['sumaSales'];
					}
					$sqlText = "select count(1) as cantSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' ".$filtroSales;
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
					$rslt .= $linea;
						
				}
			}
			
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
		
	break;
	
	case 'filtrosLobAverage':
		$rslt = cargaPag("../mtto/filtrosLobAve.php");
		$optSup = "";
		$optCuenta = "";

		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup .='<option value="0">[ALL]</option>';
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
		}
		//Lista de cuentas
		$sqlText = "select * from account where account_status='A' and id_typeacc=2 order by name_account ";
		$dtCuenta = $dbEx->selSql($sqlText);
		$optCuenta = '<option value="0">[ALL]</option>';
		foreach($dtCuenta as $dtC){
			$optCuenta .= '<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}

		$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		
		echo $rslt;
	break;
	
	case 'loadLobAverage':

		$filtroEmp = " where (pd.id_role=2) and p.name_place!='ATEAM AGENT' ";
		
		$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$start = strtotime($fec_ini);
		$end = strtotime($fec_fin);
		//Verificamos que el primer dia seleccionado sea lunes y el ultimo seleccionado sea sabado
		$nFecha = strtotime(date("Y/m/d",$start));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia>1){
			$rslt = 1;
			echo $rslt;
			break;	
		}
		$nFecha = strtotime(date("Y/m/d",$end));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia!=6){
			$rslt = 2;
			echo $rslt;
			break;	
		}
		
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
		
		
		$filtro = "";
		if($_POST['cuenta']>0){
			$filtro .=" and id_account=".$_POST['cuenta'];
		}
		
		$sqlText = "select * from account where account_status='A' and id_typeacc=2 ".$filtro." order by name_account ";
		$dtCuenta = $dbEx->selSql($sqlText);
		
		$rslt = "";
		$rslt .= '<div class="scroll">';
		$rslt .= '<table class="tblRepQA" align="center" cellpadding="2" cellspacing="1" >';
			
		if($dbEx->numrows>0){
			$rslt .='<tr><td align="center">
			<form target="_blank" action="mtto/xls_lobAverages.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="fec_ini" value="'.$fec_ini.'">
			<input type="hidden" name="fec_fin" value="'.$fec_fin.'">
			<input type="hidden" name="cuenta" value="'.$_POST['cuenta'].'">
			<input type="hidden" name="sup" value="'.$_POST['sup'].'">
			</form>';
			foreach($dtCuenta as $dtC){
				$filtro = "";
				if($_POST['sup']>0){
					$filtro .=" and e.employee_id=".$_POST['sup'];
				}
				$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where u.name_role='SUPERVISOR' and pe.status_plxemp='A' and e.user_status=1 ".$filtro." and pd.id_account=".$dtC['ID_ACCOUNT']." order by firstname";	
				$dtSup = $dbEx->selSql($sqlText);
			
				if($dbEx->numrows>0){
					$rslt .='<tr bgcolor="#006699"><td align="center"><font color="#FFFFFF"><b>'.$dtC['NAME_ACCOUNT'].'</b></td></tr>';
					
					$rslt .='<tr>';
					$sumaEvaCuenta = 0;
					$nEvaCuenta = 0;
					foreach($dtSup as $dtS){
						$sumaEvaSup=0;
						$nEvaSup = 0;
						$rslt .='<td>';
						$rslt .='<table  class="tblRepQA" border="1" align="center" cellpadding="2" cellspacing="1">';
						$rslt .='<tr><td colspan="2" align="center" bgcolor="#000000"><font color="#FFFFFF">TEAM '.$dtS['firstname']." ".$dtS['lastname'].'</font></td></tr>';
						
						for($i=1; $i<=$totalSemanas; $i++){
							$sumaEvaSemana = 0;
							$nEvaSemana = 0;
							//Busca el total de evaluaciones de CS a la semana
							$sqlText = "select employee_id from employees where id_supervisor=".$dtS['employee_id']." order by firstname";
							$dtEmp = $dbEx->selSql($sqlText);

							foreach($dtEmp as $dtE){
								$sumaEvaEmp = 0;
								$nEvaEmp = 0;
								$sqlText = "select sum(monitcsemp_qualification) as sumaCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' and monitcsemp_averages=1 ";
								$dtSumaCS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0 and $dtSumaCS['0']['sumaCS']!=NULL){
									$sumaEvaEmp = $sumaEvaEmp + $dtSumaCS['0']['sumaCS'];
								}
							
								$sqlText = "select count(1) as cantCS from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitcsemp_maker='Q' and monitcsemp_averages=1 ";
								$dtCantCS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0 and $dtCantCS['0']['cantCS']!=NULL){
									$nEvaEmp= $nEvaEmp + $dtCantCS['0']['cantCS'];
								}
							
								//Busca el total de evaluaciones de NS a la semana
								$sqlText = "select sum(monitnsemp_qualification) as sumaNS from monitoringns_emp  where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' and monitnsemp_averages=1 ";
								$dtSumaNS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0 and $dtSumaNS['0']['sumaNS']!=NULL){
									$sumaEvaEmp = $sumaEvaEmp + $dtSumaNS['0']['sumaNS'];
								}
								$sqlText = "select count(1) as cantNS from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitnsemp_maker='Q' and monitnsemp_averages=1 ";
								$dtCantNS = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0 and $dtCantNS['0']['cantNS']!=NULL){
									$nEvaEmp = $nEvaEmp + $dtCantNS['0']['cantNS'];
								}
							
								//Busca el total de evaluaciones de Sales a la semana
								$sqlText = "select sum(monitsales_qualification) as sumaSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' and monitsales_averages=1 ";
								$dtSumaSales = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0 and $dtSumaSales['0']['sumaSales']!=NULL){
									$sumaEvaEmp = $sumaEvaEmp + $dtSumaSales['0']['sumaSales'];
								}
								$sqlText = "select count(1) as cantSales from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date between date '".date("Y-m-d",$lunes[$i])."' and '".date("Y-m-d",$sabado[$i])."' and monitsales_maker='Q' and monitsales_averages=1 ";
								$dtCantSales = $dbEx->selSql($sqlText);
								if($dbEx->numrows>0 and $dtCantSales['0']['cantSales']!=NULL){
									$nEvaEmp = $nEvaEmp + $dtCantSales['0']['cantSales'];
								}
								$promEmp = "";
								if($nEvaEmp>0){
									$promEmp = $sumaEvaEmp/$nEvaEmp;
									$nEvaSemana = $nEvaSemana + 1;
									$sumaEvaSemana = $sumaEvaSemana + $promEmp;	
								}
								
							}
							$promSemana = "";
							if($nEvaSemana>0){
								$promSemana = number_format(($sumaEvaSemana/$nEvaSemana),2)."%";
								$nEvaSup = $nEvaSup + 1;
								$sumaEvaSup = $sumaEvaSup + $promSemana;
							}
							$rslt .='<tr bgcolor="#FFFFFF"><td>Week '.$i.'</td><td>'.$promSemana.'</td></tr>';
						}
						$promSup = "";
						if($nEvaSup>0){
							$promSup = number_format(($sumaEvaSup/$nEvaSup),2)."%";
							$nEvaCuenta = $nEvaCuenta + 1;
							$sumaEvaCuenta = $sumaEvaCuenta + $promSup;
						}
						$rslt .='<tr bgcolor="#CC6600"><td colspan="2"><font color="#000000"><b> Average: '.$promSup.'</font></td></tr>';
						
						$rslt .='</table>';
						$rslt .='</td>';
					}
					$rslt .='</tr>';
					$promCuenta = "";
					if($nEvaCuenta>0){
						$promCuenta = number_format(($sumaEvaCuenta/$nEvaCuenta),2)."%";	
					}
					$rslt .='<tr bgcolor="#006699"><td align="center"><font color="#FFFFFF"><b>TOTAL AVERAGE '.$dtC['NAME_ACCOUNT'].' '.$promCuenta.'</b></td></tr><tr><td><br></td></tr>';
				}
				
			}
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
		
	break;
	
	case 'delEvaCS':
		$rslt = cargaPag("../mtto/formAutorizarCS.php");
		$rslt = str_replace("<!--idCS-->",$_POST['idCS'],$rslt);
		echo $rslt;
	break;
	
	case 'saveDelEvaCS':
		$sqlText = "select * from employees where username='".$_POST['user']."'";
		$dtUser = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){

			if(md5($_POST['clave'])==$dtUser['0']['USER_PWD']){
				if($dtUser['0']['USER_STATUS']==1){
					$sqlText = "select name_place, name_role from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join user_roles ur on ur.id_role=pd.id_role where e.employee_id=".$dtUser['0']['EMPLOYEE_ID']." and status_plxemp='A'";
					$dtPlaza = $dbEx->selSql($sqlText);
					if($dtPlaza['0']['name_place']=='QUALITY SUPERVISOR' or $dtPlaza['0']['name_role']=='GERENTE DE AREA' or $dtPlaza['0']['name_role']=='GERENCIA'){
						$sqlText = "delete from itemcs_monitoring where id_monitcsemp=".$_POST['idCS'];
						$dbEx->updSql($sqlText);
						$sqlText = "delete from monitoringcs_emp where id_monitcsemp=".$_POST['idCS'];
						$dbEx->updSql($sqlText);
						$rslt = 3;	
					}
					else{
						//Usuario no autorizado
						$rslt =4;	
					}
				
				}
				else{
					//Usuario no esta activo
					$rslt = 2;	
				}
			}
			else{
				//contrase;a incorrecta
				$rslt = 1;	
			}
		}
		else{
					//Agente no existe
			$rslt = 0;
		}	
		echo $rslt;
		
	break;
	
	case 'delEvaSales':
		$rslt = cargaPag("../mtto/formAutorizarSales.php");
		$rslt = str_replace("<!--idSales-->",$_POST['idSales'],$rslt);
		echo $rslt;
	break;
	
	case 'saveDelEvaSales':
		$sqlText = "select * from employees where username='".$_POST['user']."'";
		$dtUser = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){

			if(md5($_POST['clave'])==$dtUser['0']['USER_PWD']){
				if($dtUser['0']['USER_STATUS']==1){
					$sqlText = "select name_place, name_role from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join user_roles ur on ur.id_role=pd.id_role where e.employee_id=".$dtUser['0']['EMPLOYEE_ID']." and status_plxemp='A'";
					$dtPlaza = $dbEx->selSql($sqlText);
					if($dtPlaza['0']['name_place']=='QUALITY SUPERVISOR' or $dtPlaza['0']['name_role']=='GERENTE DE AREA' or $dtPlaza['0']['name_role']=='GERENCIA'){
						$sqlText = "delete from itemsales_monitoring where id_monitsalesemp=".$_POST['idSales'];
						$dbEx->updSql($sqlText);
						$sqlText = "delete from monitoringsales_emp where id_monitsalesemp=".$_POST['idSales'];
						$dbEx->updSql($sqlText);
						$rslt = 3;	
					}
					else{
						//Usuario no autorizado
						$rslt =4;	
					}
				
				}
				else{
					//Usuario no esta activo
					$rslt = 2;	
				}
			}
			else{
				//contrase;a incorrecta
				$rslt = 1;	
			}
		}
		else{
					//Agente no existe
			$rslt = 0;
		}	
		echo $rslt;
		
	break;
	
	case 'delEvaNS':
		$rslt = cargaPag("../mtto/formAutorizarNS.php");
		$rslt = str_replace("<!--idNS-->",$_POST['idNS'],$rslt);
		echo $rslt;
	break;
	
	case 'saveDelEvaNS':
		$sqlText = "select * from employees where username='".$_POST['user']."'";
		$dtUser = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){

			if(md5($_POST['clave'])==$dtUser['0']['USER_PWD']){
				if($dtUser['0']['USER_STATUS']==1){
					$sqlText = "select name_place, name_role from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join user_roles ur on ur.id_role=pd.id_role where e.employee_id=".$dtUser['0']['EMPLOYEE_ID']." and status_plxemp='A'";
					$dtPlaza = $dbEx->selSql($sqlText);
					if($dtPlaza['0']['name_place']=='QUALITY SUPERVISOR' or $dtPlaza['0']['name_role']=='GERENTE DE AREA' or $dtPlaza['0']['name_role']=='GERENCIA'){
						$sqlText = "delete from itemns_monitoring where id_monitnsemp=".$_POST['idNS'];
						$dbEx->updSql($sqlText);
						$sqlText = "delete from monitoringns_emp where id_monitnsemp=".$_POST['idNS'];
						$dbEx->updSql($sqlText);
						$rslt = 3;	
					}
					else{
						//Usuario no autorizado
						$rslt =4;	
					}
				
				}
				else{
					//Usuario no esta activo
					$rslt = 2;	
				}
			}
			else{
				//contrase;a incorrecta
				$rslt = 1;	
			}
		}
		else{
					//Agente no existe
			$rslt = 0;
		}	
		echo $rslt;
		
	break;
	
	case 'filtrosRosalindWeekly':
		$rslt = cargaPag("../mtto/filtrosRosalindWeekly.php");
		$sqlText = "select e.employee_id, firstname, lastname, name_place, name_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart = pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and e.user_status=1 and status_plxemp='A'";
		$dtPlaza = $dbEx->selSql($sqlText);
		
		//Variable pasa para verificar que los usuarios tengan empleados para evaluar
		$pasa = false;
		//Si cumple las siguientes condiciones le muestra todos los empleados
		if($dtPlaza['0']['name_depart']=='QUALITY' or $_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
			$sqlText = "select distinct(e.employee_id) as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and pe.status_plxemp='A' and user_status=1 order by firstname";
			$pasa = true;
		}
		//Si es gerente de area solo para los q tiene permisos
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select e.employee_id as employee, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id where pe.status_plxemp='A' and user_status=1 and pe.id_placexdep in (".$_SESSION['permisos'].") order by firstname";
			$pasa = true;
		}
		
		//Si es supervisor, verifica la cuenta a la cual pertenece y le muestra los agentes de esa cuenta 
		else if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select employee_id as employee, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id']." and user_status=1  order by firstname";
			$pasa = true;
		}
		if($dbEx->numrows>0){
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		if($pasa){
			$dtEmp = $dbEx->selSql($sqlText);
			$optEmp = "";
			if($dbEx->numrows>0){
				foreach($dtEmp as $dtE){
					$optEmp .='<option value="'.$dtE['employee'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
				}
			}
		}
		else{
			$optEmp = '<option value="0">NO EMPLOYEES FOR THIS SELECTION</option>';	
		}
		
		$optSup = "";
		$optCuenta = "";
		if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='WORKFORCE' or $dtPlaza['0']['name_depart']=='QUALITY')
		{
			$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
			$dtSup = $dbEx->selSql($sqlText);
			$optSup .='<option value="0">[ALL]</option>';
			foreach($dtSup as $dtS){
				$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
			}
			//Lista de cuentas
			$sqlText = "select * from account where account_status='A' and id_typeacc=2 order by name_account ";
			$dtCuenta = $dbEx->selSql($sqlText);
			$optCuenta = '<option value="0">[ALL]</option>';
			foreach($dtCuenta as $dtC){
				$optCuenta .= '<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
			}
			
		}
		else{
			$optSup ='<option value="'.$dtPlaza['0']['employee_id'].'">'.$dtPlaza['0']['firstname']." ".$dtPlaza['0']['lastname'].'</option>';
			
			$optCuenta .='<option value="0"></option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where name_depart='QUALITY' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtQa = $dbEx->selSql($sqlText);
		$optQa = "";
		foreach($dtQa as $dtQ){
			$optQa .='<option value="'.$dtQ['employee_id'].'">'.$dtQ['firstname']." ".$dtQ['lastname'].'</option>';		
		}

		$rslt = str_replace("<!--optCuenta-->",$optCuenta,$rslt);
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optQa-->",$optQa,$rslt);
		echo $rslt;
	break;
	
	case 'loadRosalindWeeklyReport':
		$arrayAgentes = $_POST['arrayAgentes'];
		$agentes = explode(" ",$arrayAgentes);
		$nAgentes = count($agentes);
		
		$listaAgentes = "";
		for($i = 0; $i<$nAgentes; $i++){
			if($i==0){
				$listaAgentes .= $agentes[$i];	
			}
			else{
				$listaAgentes .=", ".$agentes[$i];	
			}
		}
		
		$filtroCS = " where monitcsemp_maker='Q' and monitcsemp_averages=1 ";
		$filtroNS = " where monitnsemp_maker='Q' and monitnsemp_averages=1 ";
		$filtroSales = " where monitsales_maker='Q' and monitsales_averages=1 ";
		$filtro = " where (pd.id_role=2) and p.name_place!='ATEAM AGENT' ";
		
		$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$start = strtotime($fec_ini);
		$end = strtotime($fec_fin);
		//Verificamos que el primer dia seleccionado sea lunes y el ultimo seleccionado sea sabado y la cantidad de dias entre las dos fechas sea 6
		$nFecha = strtotime(date("Y/m/d",$start));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia>1){
			$rslt = 1;
			echo $rslt;
			break;	
		}
		$nFecha = strtotime(date("Y/m/d",$end));
		$dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m",$nFecha), date("d",$nFecha),date("Y",$nFecha)),0);
		if($dia!=6){
			$rslt = 2;
			echo $rslt;
			break;	
		}
		$ndias = n_dias($fec_ini,$fec_fin);
		if($ndias>7){
			$rslt = 3;
			echo $rslt;
			break;
		}
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		
		if($_POST['agentes0']>0){
			$filtro .=" and e.employee_id in (".$listaAgentes.") ";

		}
		if($_POST['qa']>0){
			$filtroCS .=" and qa_agent =".$_POST['qa'];
			$filtroNS .=" and qa_agent =".$_POST['qa'];
			$filtroSales .=" and qa_agent =".$_POST['qa'];
		}
		if($_POST['status']>=0){
			$filtro .=" and user_status= ".$_POST['status'];
		}
		$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".$filtro." and status_plxemp='A' order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$rslt = "";
		$rslt .= '<div class="scroll">';
		$rslt .= '<table class="tblRepQA" border="1" align="center" cellpadding="2" cellspacing="1" >';
		if($dbEx->numrows>0){
			$rslt .='<tr><td>
			<form target="_blank" action="mtto/xls_weeklyRosalind.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="filtroCS" value="'.$filtroCS.'">
			<input type="hidden" name="filtroNS" value="'.$filtroNS.'">
			<input type="hidden" name="filtroSales" value="'.$filtroSales.'">
			<input type="hidden" name="fec_ini" value="'.$fec_ini.'">
			<input type="hidden" name="fec_fin" value="'.$fec_fin.'">
			<input type="hidden" name="fechaIni" value="'.$_POST['fechaIni'].'">
			<input type="hidden" name="fechaFin" value="'.$_POST['fechaFin'].'">
			</td></tr>';
			$rslt .='<tr><td class="showItem" colspan="18">WEEKLY MONITORING REPORT '.$_POST['fechaIni']." - ".$_POST['fechaFin'].'</td></tr>';
			$rslt .='<tr><td colspan="13"></td><td>Excellent</td><td>Great</td><td>Good</td><td>Fair</td><td>Poor</td></tr>';
			
			$rslt .='<tr><td>Badge ID</td><td>REPS</td><td>MON</td><td>TUES</td>
			<td>WED</td><td>THURS</td><td>FRI</td><td>SAT</td><td align="center">1</td><td align="center">2</td><td align="center">3<td>TOTALS</td>
			<td align="center">%</td><td>100%</td><td>99-90%</td><td>89-80%</td><td>79-70%</td><td>69%-BELOW</td></tr>';
			
			//Acumula el total de promedios
			//Suma los promedios
			$sumaTotalEva = 0;
			$promTotalEva = 0;
			foreach($dtEmp as $dtE){
				//Declarar nueva linea con todos los datos del registro del agente, si el agente posee evaluaciones escribir sus datos, si no tiene evaluaciones no se escribe la linea
				$linea = "";
				$flag = false;
				
				$linea .='<tr><td>'.$dtE['username'].'</td>
				<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>';
				
				//Inicializa el n para guardar un array de las notas
				$notas = array();
				$n = 0;
				for($i = $start; $i<=$end; $i +=86400){
					//Evaluaciones de CS
					//Cantidad de evaluaciones al dia para poner numero de X
					$equis = '';
					$sqlText = "select monitcsemp_qualification from monitoringcs_emp ".$filtroCS." and monitcsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
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
					$sqlText = "select monitnsemp_qualification from monitoringns_emp ".$filtroNS." and monitnsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
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
					$sqlText = "select monitsales_qualification from monitoringsales_emp ".$filtroSales." and monitsales_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
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
					$linea .='<td>'.number_format($suma,2).'</td>';
					$linea .='<td>'.number_format($total,2).'%</td>';
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
					$rslt .= $linea;		
				}
			}
			
			
		}
		else{
			$rslt .='<tr><td>No Matches</td></tr>';	
		}
		echo $rslt;
		
	break;
	
	case 'loadReportSbs':
		$filtroCS = " where monitcsemp_maker='O' and monitcsemp_averages=1 ";
		$filtroNS = " where monitnsemp_maker='O' and monitnsemp_averages=1 ";
		$filtroSales = " where monitsales_maker='O' and monitsales_averages=1 ";
		
		$filtro = "";
		$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		
		$start = strtotime($fec_ini);
		$end = strtotime($fec_fin);
		
		if($_POST['cuenta']>0){
			$filtro .=" and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and e.employee_id=".$_POST['emp'];	
		}
		$sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place ".$filtro." and status_plxemp='A' order by firstname";
		
		$dtEmp = $dbEx->selSql($sqlText);
		$rslt = "";
		$rslt .= '<div class="scroll">';
		$rslt .= '<table class="tblRepQA" border="1" align="center" cellpadding="2" cellspacing="1" >';
		if($dbEx->numrows>0){
			$rslt .='<tr><td>
			<form target="_blank" action="mtto/xls_supervisorSbs.php" method="post">
			<input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />
			<input type="hidden" name="filtro" value="'.$filtro.'">
			<input type="hidden" name="filtroCS" value="'.$filtroCS.'">
			<input type="hidden" name="filtroNS" value="'.$filtroNS.'">
			<input type="hidden" name="filtroSales" value="'.$filtroSales.'">
			<input type="hidden" name="fec_ini" value="'.$fec_ini.'">
			<input type="hidden" name="fec_fin" value="'.$fec_fin.'">
			</td></tr>';
			
			$encabezado = '<tr class="showItem">
			<td>Badge</td>
			<td>Agents</td>
			<td>Supervisor</td>
			<td>Evaluator</td>
			<td>MTD</td>';
			
			//Acumula el total de promedios
			//Suma los promedios
			$sumaTotalEva = 0;
			$promTotalEva = 0;
			$tbl = "";
			//Max N es para identificar la maxima cantidad de evaluaciones realizadas en el periodo para ser usado ese numero en los titulos
				$maxN = 0;

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
				
				//Declarar nueva linea con todos los datos del registro del agente, si el agente posee evaluaciones escribir sus datos, si no tiene evaluaciones no se escribe la linea
				$linea = "";
				$flag = false;
				//Titulos
				$linea .='<tr><td>'.$dtE['username'].'</td>
				<td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td>
				<td>'.$nombreSup.'</td>';
				
				//Obtiene los QA agents 
				$listaQa = "";
				$qas = "";
				$nQa = 0;
				$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringcs_emp m inner join employees e on m.qa_agent=e.employee_id ".$filtroCS." and monitcsemp_date between '".$fec_ini."' and '".$fec_fin."' and m.employee_id=".$dtE['employee_id'];
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
				
				$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringns_emp m inner join employees e on m.qa_agent=e.employee_id ".$filtroNS." and monitnsemp_date between '".$fec_ini."' and '".$fec_fin."' and m.employee_id=".$dtE['employee_id']." ".$filtroQa;
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
				
				$sqlText = "select distinct(qa_agent) as qa_agent, firstname, lastname from monitoringsales_emp m inner join employees e on m.qa_agent=e.employee_id ".$filtroSales."  and monitsales_date between '".$fec_ini."' and '".$fec_fin."' and m.employee_id=".$dtE['employee_id']." ".$filtroQa;
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
					$sqlText = "select monitcsemp_qualification from monitoringcs_emp ".$filtroCS." and monitcsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
					$dtMonitCs = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						foreach($dtMonitCs as $dtCS){
							$notas[$n]['calif'] = $dtCS['monitcsemp_qualification'];
							$n = $n+1;
						}
					}
					
					//Evaluaciones de NS
					$sqlText = "select monitnsemp_qualification from monitoringns_emp ".$filtroNS." and monitnsemp_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
					$dtMonitNs = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						foreach($dtMonitNs as $dtNs){
							$notas[$n]['calif'] = $dtNs['monitnsemp_qualification'];
							$n = $n+1;
						}
					}

					
					//Evaluaciones de Sales
					$sqlText = "select monitsales_qualification from monitoringsales_emp ".$filtroSales." and monitsales_date='".date("Y-m-d",$i)."' and employee_id=".$dtE['employee_id'];
					$dtMonitSales = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						foreach($dtMonitSales as $dtS){
							$notas[$n]['calif'] = $dtS['monitsales_qualification'];
							$n = $n+1;
						}
					}
				}
				//Termina de guardar notas
				
				
				$suma = 0;
				$calif = "";
				
				for($i=0; $i<$n; $i++){
					$calif .='<td>'.number_format($notas[$i]['calif'],2).'%</td>';
					$suma = $suma + $notas[$i]['calif'];
				}
				$total = 0;
				if($n>0){
					$total = $suma/$n;
					if($n>$maxN){
						$maxN = $n;	
					}
				}
				
				if($total<=69){
					$font = 'bgcolor="#F8402C"';	
				}
				else if($total>69 and $total <80){
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
				$linea .='<td '.$font.'><b>'.$totalMostrar.'</b></td>';
				$linea .= $calif.'</tr>';
				if($flag){
					$tbl .= $linea;	
				}
			
			}
			for($i=1; $i<=$maxN; $i++){
				$encabezado .='<td align="center">'.$i.'</td>';
			}
			$encabezado .='</tr>';
			
			$rslt .=$encabezado;
			$rslt .=$tbl;
			
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
			$rslt .='<tr><td></td><td></td><td></td><td align="right"><b>TOTAL AVERAGE</b></td><td '.$font.'><b>'.$promEvas.'</b></td></tr>';
			
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
		
		
	break;
}
?>

