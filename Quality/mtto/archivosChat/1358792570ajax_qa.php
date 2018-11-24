<?php
//Funciones para Agent Scorecard	
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
switch($_POST['Do']){
	case 'newMonitoring';
		$rslt = cargaPag("../mtto/filtrosMonitoreo.php");
		$sqlText = "select distinct(e.employee_id) as employee, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='AGENTE' or name_role='SUPERVISOR') and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		if($dbEx->numrows>0){
			$optEmp .= '<option value="0">SELECT A AGENT NAME</option>';
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
			}
		}
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--evaluador-->",$_SESSION['usr_nombre']." ".$_SESSION['usr_apellido'],$rslt);
		$rslt = str_replace("<!--fechaActual-->",date("d/m/Y"),$rslt);
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
			$optItems ='<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
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
			$btn = '<tr><td colspan="3" align="center"><input type="button" value="Save" class="btn" onclick="saveFormCS()"/></td></tr>';
			$rslt = $optItems;
			
		}
		
		//Carga forma para sales
		else if($_POST['tpEval']==2){
			$comments = "";
			$nItems = 0;
			$nCat = 0;
			$sqlText = "select * from category_form_sales where catsales_status='A' order by id_catsales";
			$dtCat = $dbEx->selSql($sqlText);
			$optItems ='<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$optItems .='<tr><td colspan="3">Enrollement ID: <input type="text" id=enrollID size="35"/>';

			foreach($dtCat as $dtC){
				$nCat = $nCat + 1;
				$optItems .='<tr class="showItem"><td colspan="3"><b>'.$dtC['CATSALES_NAME'].'</b></td></tr>';
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
			$btn = '<tr><td colspan="3" align="center"><input type="button" value="Save" class="btn" onclick="saveFormSales()"/></td></tr>';
			$rslt = $optItems;
		}
		
		//Carga forma de New Service
		else if($_POST['tpEval']==3){
			$nItems = 0;
			$nCat = 0;
			$sqlText = "select * from category_form_newservice where catns_status='A' order by id_catns";
			$dtCat = $dbEx->selSql($sqlText);
			$optItems ='<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$optItems .='<tr><td colspan="3">Time: <input type="text" id="txtTime" size="35"></td></tr>';
			$optItems .='<tr><td colspan="3">Enrollment ID: <input type="text" id="txtEnroll" class="txtPag" size="35"></td></tr>';
			$optItems .='<tr><td colspan="3">Contact ID: <input type="text" id="txtContact" size="35"/></td></tr>';
			foreach($dtCat as $dtC){
				$nCat = $nCat + 1;
				$optItems .='<tr class="showItem"><td colspan="3"><b>'.$dtC['CATNS_NAME'].'</b></td></tr>';
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
			$btn = '<tr><td colspan="3" align="center"><input type="button" value="Save" class="btn" onclick="saveFormNS()"/></td></tr>';
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
		$rslt .= '<br><br>'.$comments;
		$rslt .= $btn;
		$rslt .='</table>';
		$rslt .='<input type="hidden" id="nCat" value="'.$nCat.'">';
		$rslt .='<input type="hidden" id="nItems" value="'.$nItems.'">';
		
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
		
		//Crea la evaluacion de QA para Customer services
		$sqlText = "insert into monitoringcs_emp set employee_id=".$_POST['agente'].", qa_agent=".$_SESSION['usr_id'].", monitcsemp_date='".$fechaActual."', monitcsemp_contactid='".$_POST['contactId']."', monitcsemp_callreason='".$_POST['razon']."', monitcsemp_account='".$_POST['cuenta']."', monitcsemp_fail='".$_POST['fail']."', monitcsemp_comment1='".$_POST['comment1']."', monitcsemp_comment2='".$_POST['comment2']."', monitcsemp_comment3='".$_POST['comment3']."', monitcsemp_comment4='".$_POST['comment4']."', monitcsemp_comment5='".$_POST['comment5']."', monitcsemp_comment6='".$_POST['comment6']."', monitcsemp_comment7='".$_POST['comment7']."', monitcsemp_comment8='".$_POST['comment8']."', monitcsemp_comment9='".$_POST['comment9']."', monitcsemp_comment10='".$_POST['comment10']."', monitcsemp_comment11='".$_POST['comment11']."'";
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_monitcsemp) as IdEva from monitoringcs_emp where employee_id=".$_POST['agente'];
		$dtEva = $dbEx->selSql($sqlText);
		
		$sqlText = "select * from category_form_cs where catcs_status='A' order by id_catcs";
		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
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
		$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		
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
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "insert into itemcs_monitoring set itemcs_total='".$valor."', id_monitcsemp=".$dtEva['0']['IdEva'].", id_formcs=".$dtI['id_formcs'].", itemcs_resp='".$respuesta."'";		
				
				$dbEx->insSql($sqlText);
				$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		
		//Verifica si selecciono una categoria de Auto fail y asigna valor de 0 a la evaluacion
		if($_POST['listSubFail']>0){
			$sqlText = "update monitoringcs_emp set monitcsemp_qualification='0', fail_id=".$_POST['listSubFail']." where id_monitcsemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		else{
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringcs_emp set monitcsemp_qualification='".$valorEva."' where id_monitcsemp=".$dtEva['0']['IdEva'];
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
		
		//Crea la evaluacion de QA para sales
		$sqlText = "insert into monitoringsales_emp set employee_id=".$_POST['agente'].", qa_agent=".$_SESSION['usr_id'].", monitsales_date='".$fechaActual."', monitsales_enrollid='".$_POST['enrollId']."', monitsales_fail='".$_POST['fail']."', monitsales_comment1='".$_POST['comment1']."', monitsales_comment2='".$_POST['comment2']."', monitsales_comment3='".$_POST['comment3']."', monitsales_comment4='".$_POST['comment4']."', monitsales_comment5='".$_POST['comment5']."', monitsales_comment6='".$_POST['comment6']."', monitsales_comment7='".$_POST['comment7']."', monitsales_comment8='".$_POST['comment8']."', monitsales_comment9='".$_POST['comment9']."', monitsales_comment10='".$_POST['comment10']."', monitsales_comment11='".$_POST['comment11']."'";
		$dbEx->insSql($sqlText);
		$sqlText = "select max(id_monitsalesemp) as IdEva from monitoringsales_emp where employee_id=".$_POST['agente'];
		$dtEva = $dbEx->selSql($sqlText);
		
		$sqlText = "select * from category_form_sales where catsales_status='A' order by id_catsales";
		$dtCat = $dbEx->selSql($sqlText);
		//Recorre categorias la primera vez para ver si tiene preguntas en la categoria, sino su valor se redistribuye equitativamente en las otras categorias.
		$valorCatDistribuir = 0;
		$nCatDistribuir = 0;
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
		$valorCatAdicional = $valorCatDistribuir/$nCatDistribuir;
		
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
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "insert into itemsales_monitoring set itemsales_total='".$valor."', id_monitsalesemp=".$dtEva['0']['IdEva'].", id_formsales=".$dtI['id_formsales'].", itemsales_resp='".$respuesta."'";		
				
				$dbEx->insSql($sqlText);
				$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		//Verifica si selecciono una categoria de Auto fail y asigna valor de 0 a la evaluacion
		if($_POST['listSubFail']>0){
			$sqlText = "update monitoringsales_emp set monitsales_qualification='0', fail_id=".$_POST['listSubFail']." where id_monitsalesemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		else{
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringsales_emp set monitsales_qualification='".$valorEva."' where id_monitsalesemp=".$dtEva['0']['IdEva'];
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
		
		//Crea la evaluacion de QA para New Service
		$sqlText = "insert into monitoringns_emp set employee_id=".$_POST['agente'].", qa_agent=".$_SESSION['usr_id'].", monitnsemp_date='".$fechaActual."',monitnsemp_time='".$_POST['time']."',  monitnsemp_enrollid='".$_POST['enrollId']."', monitnsemp_contactid='".$_POST['contact']."', monitnsemp_fail='".$_POST['fail']."', monitnsemp_comment1='".$_POST['comment1']."', monitnsemp_comment2='".$_POST['comment2']."', monitnsemp_comment3='".$_POST['comment3']."', monitnsemp_comment4='".$_POST['comment4']."', monitnsemp_comment5='".$_POST['comment5']."', monitnsemp_comment6='".$_POST['comment6']."', monitnsemp_comment7='".$_POST['comment7']."', monitnsemp_comment8='".$_POST['comment8']."', monitnsemp_comment9='".$_POST['comment9']."'";
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
					$valor = $valorPregunta;
				}
				else if($item[$idIni]==2){
					$respuesta = "N";
					$valor = 0;
				}
				else if($item[$idIni]==3){
					$respuesta = "NA";
					$valor =0;
				}
				$sqlText = "insert into itemns_monitoring set itemns_total='".$valor."', id_monitnsemp=".$dtEva['0']['IdEva'].", id_formns=".$dtI['id_formns'].", itemns_resp='".$respuesta."'";		
				
				$dbEx->insSql($sqlText);
				$valorEva = $valorEva + $valor;
				$idIni = $idIni +1;
			}//Termina segundo foreach
		}//Termina Categorias
		//Verifica si selecciono una categoria de Auto fail y asigna valor de 0 a la evaluacion
		if($_POST['listSubFail']>0){
			$sqlText = "update monitoringsales_emp set monitsales_qualification='0', fail_id=".$_POST['listSubFail']." where id_monitsalesemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		else{
			//Cuenta el resultado de los items para actualizar total
			$sqlText = "update monitoringns_emp set monitnsemp_qualification='".$valorEva."' where id_monitnsemp=".$dtEva['0']['IdEva'];
			$dbEx->updSql($sqlText);
		}
		echo $dtEva['0']['IdEva'];
	break;
	
	
	//Muestra el formulario de CS guardado segun el parametro IdM enviado
	case 'loadMonitoringCS':
		$sqlText = "select ID_MONITCSEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitcsemp_date,'%d/%m/%Y') as f1, MONITCSEMP_QUALIFICATION, MONITCSEMP_CALLREASON, MONITCSEMP_CONTACTID, MONITCSEMP_ACCOUNT, FAIL_ID, MONITCSEMP_FAIL, MONITCSEMP_COMMENT1, MONITCSEMP_COMMENT2, MONITCSEMP_COMMENT3, MONITCSEMP_COMMENT4, MONITCSEMP_COMMENT5, MONITCSEMP_COMMENT6, MONITCSEMP_COMMENT7, MONITCSEMP_COMMENT8, MONITCSEMP_COMMENT9, MONITCSEMP_COMMENT10, MONITCSEMP_COMMENT11  from monitoringcs_emp m inner join employees e on e.employee_id=m.employee_id where id_monitcsemp=".$_POST['idM'];
		$dtMonit = $dbEx->selSql($sqlText);
		
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			
			$tblForm = '<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Customer Service Monitoring Form</b></td></tr>';
			$tblForm .='<tr><td width="15%">QA: </td><td width="35%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%">Contact ID: </td><td width="35%">'.$dtMonit['0']['MONITCSEMP_CONTACTID'].'</td></tr>';
			$tblForm .='<tr><td>Agent name: </td><td>'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td><td>Account #: </td><td>'.$dtMonit['0']['MONITCSEMP_ACCOUNT'].'</td></tr>';
			$tblForm .='<tr><td>Date: </td><td colspan="3">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td>Supervisor: </td><td colspan="3">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td>Call Reason: </td><td colspan="3">'.$dtMonit['0']['MONITCSEMP_CALLREASON'].'</td></tr>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formcs.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br>';
			$tblForm.='<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			
			$sqlText = "select * from itemcs_monitoring where id_monitcsemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_cs f inner join category_form_cs c on f.id_catcs=c.id_catcs where f.id_formcs=".$dtI['ID_FORMCS'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATCS'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItem"><td colspan="3"><b>'.$dtDatosItems['0']['CATCS_NAME'].'</b></td></tr>';
				}
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMCS_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMCS_TEXT'].'</td><td>'.$dtI['ITEMCS_RESP'].'</td></tr>';
			}	
			$tblForm .='<tr class="showItem"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE &nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITCSEMP_QUALIFICATION'],2).'%</b></td></tr>';
			
			$sqlText = "select distinct(f.id_catcs) as idC, catcs_name from itemcs_monitoring i inner join form_monitoring_cs f on i.id_formcs=f.id_formcs inner join category_form_cs c on c.id_catcs=f.id_catcs where id_monitcsemp=".$_POST['idM'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtFail['0']['FAIL_IDFATHER'];
				$dtFailF = $dbEx->selSql($sqlText);
				$tblForm .='<tr><td colspan="3">FAIL: '.$dtFailF['0']['FAIL_TEXT'].'->'.$dtFail['0']['FAIL_TEXT'].'</td></tr>';
				$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="160" rows="3">'.$dtMonit['0']['MONITCSEMP_FAIL'].'</textarea></td></tr>';
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
					$tblForm .=	'<tr><td colspan="3"><textarea id="txtCat11" cols="100" rows="3">'.$dtMonit['0']['MONITCSEMP_COMMENT4'].'</textarea></td></tr>';
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
		$sqlText = "select ID_MONITSALESEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitsales_date,'%d/%m/%Y') as f1, MONITSALES_QUALIFICATION, MONITSALES_ENROLLID, FAIL_ID, MONITSALES_FAIL, MONITSALES_COMMENT1, MONITSALES_COMMENT2, MONITSALES_COMMENT3, MONITSALES_COMMENT4, MONITSALES_COMMENT5, MONITSALES_COMMENT6, MONITSALES_COMMENT7, MONITSALES_COMMENT8, MONITSALES_COMMENT9, MONITSALES_COMMENT10, MONITSALES_COMMENT11  from monitoringsales_emp m inner join employees e on e.employee_id=m.employee_id where id_monitsalesemp=".$_POST['idM'];
		$dtMonit = $dbEx->selSql($sqlText);
		
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			
			$tblForm = '<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>Outbound Wireless New Service</b></td></tr>';
			$tblForm .='<tr><td width="15%">QA: </td><td width="35%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%">Date: </td><td width="35%">'.$dtMonit['0']['f1'].'</td></tr>';
			$tblForm .='<tr><td>Agent name:'.$_POST['idM'].' </td><td>'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td><td>Enrollment ID: </td><td>'.$dtMonit['0']['MONITSALES_ENROLLID'].'</td></tr>';
			
			$tblForm .='<tr><td>Supervisor: </td><td colspan="3">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td colspan="4" align="right"><form target="_blank" action="report/xls_formsales.php" method="post"><input type="image" src="images/excel.png" alt="Export to excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$_POST['idM'].'"></td></tr></table><br>';

			$tblForm.='<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			
			$sqlText = "select * from itemsales_monitoring where id_monitsalesemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_sales f inner join category_form_sales c on f.id_catsales=c.id_catsales where f.id_formsales=".$dtI['ID_FORMSALES'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATSALES'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItem"><td colspan="3"><b>'.$dtDatosItems['0']['CATSALES_NAME'].'</b></td></tr>';
				}
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMSALES_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMSALES_TEXT'].'</td><td>'.$dtI['ITEMSALES_RESP'].'</td></tr>';
			}	
			$tblForm .='<tr class="showItem"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE&nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITSALES_QUALIFICATION'],2).'%</b></td></tr>';
			
			$sqlText = "select distinct(f.id_catsales) as idC, catsales_name from itemsales_monitoring i inner join form_monitoring_sales f on i.id_formsales=f.id_formsales inner join category_form_sales c on c.id_catsales=f.id_catsales where id_monitsalesemp=".$_POST['idM'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtFail['0']['FAIL_IDFATHER'];
				$dtFailF = $dbEx->selSql($sqlText);
				$tblForm .='<tr><td colspan="3">FAIL: '.$dtFailF['0']['FAIL_TEXT'].'->'.$dtFail['0']['FAIL_TEXT'].'</td></tr>';
				$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="160" rows="3">'.$dtMonit['0']['MONITSALES_FAIL'].'</textarea></td></tr>';
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
		$sqlText = "select ID_MONITNSEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitnsemp_date,'%d/%m/%Y') as f1, MONITNSEMP_QUALIFICATION, MONITNSEMP_TIME, MONITNSEMP_ENROLLID, MONITNSEMP_CONTACTID, FAIL_ID, MONITNSEMP_FAIL, MONITNSEMP_COMMENT1, MONITNSEMP_COMMENT2, MONITNSEMP_COMMENT3, MONITNSEMP_COMMENT4, MONITNSEMP_COMMENT5, MONITNSEMP_COMMENT6, MONITNSEMP_COMMENT7, MONITNSEMP_COMMENT8, MONITNSEMP_COMMENT9  from monitoringns_emp m inner join employees e on e.employee_id=m.employee_id where id_monitnsemp=".$_POST['idM'];
		$dtMonit = $dbEx->selSql($sqlText);
		
		$idCat = 0;
		$nuevaIdCat = 0;

		if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			
			$tblForm = '<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			$tblForm .='<tr><td colspan="4" align="center"><b>New Services Monitoring Form</b></td></tr>';
			$tblForm .='<tr><td width="15%">QA: </td><td width="35%">'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td width="15%">Enrollment ID: </td><td width="35%">'.$dtMonit['0']['MONITNSEMP_ENROLLID'].'</td></tr>';
			$tblForm .='<tr><td>Agent name:'.$_POST['idM'].' </td><td>'.$dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'].'</td><td>Contact ID:</td><td>'.$dtMonit['0']['MONITNSEMP_CONTACTID'].'</td></tr>';
			$tblForm .='<tr><td width="15%">Date: </td><td width="35%">'.$dtMonit['0']['f1'].'</td>';
			
			$tblForm .='<tr><td>Supervisor: </td><td colspan="3">'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td></tr>';
			$tblForm .='<tr><td>Time:</td><td colspan="3">'.$dtMonit['0']['MONITNSEMP_TIME'].'</td></tr></table><br>';
			$tblForm.='<table class="tblHead" width="800px" align="center" cellpadding="2" cellspacing="2">';
			
			$sqlText = "select * from itemns_monitoring where id_monitnsemp=".$_POST['idM'];
			$dtItems = $dbEx->selSql($sqlText);
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_ns f inner join category_form_newservice c on f.id_catns=c.id_catns where f.id_formns=".$dtI['ID_FORMNS'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATNS'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					$tblForm .='<tr class="showItem"><td colspan="3"><b>'.$dtDatosItems['0']['CATNS_NAME'].'</b></td></tr>';
				}
				$tblForm .='<tr><td align="center">'.$dtDatosItems['0']['FORMNS_ITEM'].'</td><td>'.$dtDatosItems['0']['FORMNS_TEXT'].'</td><td>'.$dtI['ITEMNS_RESP'].'</td></tr>';
			}	
			$tblForm .='<tr class="showItem"><td colspan="2" align="right"><b>QA PERCENTAGE TOTAL SCORE&nbsp;&nbsp;&nbsp;&nbsp; '.number_format($dtMonit['0']['MONITNSEMP_QUALIFICATION'],2).'%</b></td></tr>';
			
			$sqlText = "select distinct(f.id_catns) as idC, catns_name from itemns_monitoring i inner join form_monitoring_ns f on i.id_formns=f.id_formns inner join category_form_newservice c on c.id_catns=f.id_catns where id_monitnsemp=".$_POST['idM'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtFail['0']['FAIL_IDFATHER'];
				$dtFailF = $dbEx->selSql($sqlText);
				$tblForm .='<tr><td colspan="3">FAIL: '.$dtFailF['0']['FAIL_TEXT'].'->'.$dtFail['0']['FAIL_TEXT'].'</td></tr>';
				$tblForm .='<tr><td colspan="3"><textarea id="txtFail" cols="160" rows="3">'.$dtMonit['0']['MONITNS_FAIL'].'</textarea></td></tr>';
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
	
	case 'MonitLog':
		$rslt = cargaPag("../mtto/filtros_monitlog.php");

		echo $rslt;
	break;
	
	case 'load_Monitlog':
		//Verifica que usuario se ha loggeado
		if($_SESSION['usr_rol']!=""){
			$filtro = " where 1";
			//Si el usuario es Agente de QA solo muestra sus evaluaciones
			if($_SESSION['usr_rol']=='AGENTE'){
				$filtro .=" and qa_agent=".$_SESSION['usr_id'];
			}
			//Verifica datos de evaluacion si la evaluacion es para CS
			if($_POST['tpEval']==1){
				$sqlText = " select id_monitcsemp, m.employee_id, qa_agent, date_format(monitcsemp_date,'%d/%m/%Y') as f1, monitcsemp_qualification, firstname, lastname , id_supervisor from employees e inner join monitoringcs_emp m on e.employee_id=m.employee_id ";	
		
				if(strlen($_POST['fec_ini'])>0){
					if(strlen($_POST['fec_fin']>0)){
						$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
						$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
						$filtro .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
					}	
				}
				$sqlText .= $filtro;
				$dtMonit = $dbEx->selSql($sqlText);
				$tblMonit = '<table class="tblResult" width="900px" align="center" cellpadding="2" cellspacing="2">';
				if($dbEx->numrows>0){
					$tblMonit .='<tr><th colspan="5" align="center"><b>Customer Service Monitoring Form</b></th></tr>';
					$tblMonit .='<tr><td colspan="5">Matches: '.$dbEx->numrows.'</td></tr>';
					$tblMonit .='<tr bgcolor="#FFFFFF" align="center"><th width="25%">QA</th><th width="25%">Agent Name</th><th width="25%">Supervisor</th><th width="12%">Date</th><th width="13%">QA PERCENTAGE TOTAL SCORE</th></tr>';
					foreach($dtMonit as $dtM){
						$sqlText ="select employee_id, firstname, lastname from employees where employee_id=".$dtM['qa_agent'];
						$dtQa = $dbEx->selSql($sqlText);
						$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dtM['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						
						$tblMonit .='<tr class="rowCons" onclick="loadMonitoringCS('.$dtM['id_monitcsemp'].')"><td>'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td>'.$dtM['firstname'].' '.$dtM['lastname'].'</td><td>'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td><td align="center">'.$dtM['f1'].'</td><td align="center">'.number_format($dtM['monitcsemp_qualification'],2).'%</td></tr>';
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
				$sqlText = " select id_monitsalesemp, m.employee_id, qa_agent, date_format(monitsales_date,'%d/%m/%Y') as f1, monitsales_qualification, firstname, lastname , id_supervisor from employees e inner join monitoringsales_emp m on e.employee_id=m.employee_id ";	
		
				if(strlen($_POST['fec_ini'])>0){
					if(strlen($_POST['fec_fin']>0)){
						$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
						$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
						$filtro .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
					}	
				}
				$sqlText .= $filtro;
				$dtMonit = $dbEx->selSql($sqlText);
				$tblMonit = '<table class="tblResult" width="900px" align="center" cellpadding="2" cellspacing="2">';
				if($dbEx->numrows>0){
					$tblMonit .='<tr><th colspan="5" align="center"><b>Outbound Wireless New Service</b></th></tr>';
					$tblMonit .='<tr><td colspan="5">Matches: '.$dbEx->numrows.'</td></tr>';
					$tblMonit .='<tr bgcolor="#FFFFFF" align="center"><th width="25%">QA</th><th width="25%">Agent Name</th><th width="25%">Supervisor</th><th width="12%">Date</th><th width="13%">QA PERCENTAGE TOTAL SCORE</th></tr>';
					foreach($dtMonit as $dtM){
						$sqlText ="select employee_id, firstname, lastname from employees where employee_id=".$dtM['qa_agent'];
						$dtQa = $dbEx->selSql($sqlText);
						$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dtM['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						
						$tblMonit .='<tr class="rowCons" onclick="loadMonitoringSales('.$dtM['id_monitsalesemp'].')"><td>'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td>'.$dtM['firstname'].' '.$dtM['lastname'].'</td><td>'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td><td align="center">'.$dtM['f1'].'</td><td align="center">'.number_format($dtM['monitsales_qualification'],2).'%</td></tr>';
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
				$sqlText = " select id_monitnsemp, m.employee_id, qa_agent, date_format(monitnsemp_date,'%d/%m/%Y') as f1, monitnsemp_qualification, firstname, lastname , id_supervisor from employees e inner join monitoringns_emp m on e.employee_id=m.employee_id ";	
		
				if(strlen($_POST['fec_ini'])>0){
					if(strlen($_POST['fec_fin']>0)){
						$fec_ini = $oFec->cvDtoY($_POST['fec_ini']);
						$fec_fin = $oFec->cvDtoY($_POST['fec_fin']);
						$filtro .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
					}	
				}
				$sqlText .= $filtro;
				$dtMonit = $dbEx->selSql($sqlText);
				$tblMonit = '<table class="tblResult" width="900px" align="center" cellpadding="2" cellspacing="2">';
				if($dbEx->numrows>0){
					$tblMonit .='<tr><th colspan="5" align="center"><b>New Service Monitoring Form</b></th></tr>';
					$tblMonit .='<tr><td colspan="5">Matches: '.$dbEx->numrows.'</td></tr>';
					$tblMonit .='<tr bgcolor="#FFFFFF" align="center"><th width="25%">QA</th><th width="25%">Agent Name</th><th width="25%">Supervisor</th><th width="12%">Date</th><th width="13%">QA PERCENTAGE TOTAL SCORE</th></tr>';
					foreach($dtMonit as $dtM){
						$sqlText ="select employee_id, firstname, lastname from employees where employee_id=".$dtM['qa_agent'];
						$dtQa = $dbEx->selSql($sqlText);
						$sqlText = "select employee_id, firstname, lastname from employees where employee_id=".$dtM['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						
						$tblMonit .='<tr class="rowCons" onclick="loadMonitoringNS('.$dtM['id_monitnsemp'].')"><td>'.$dtQa['0']['firstname'].' '.$dtQa['0']['lastname'].'</td><td>'.$dtM['firstname'].' '.$dtM['lastname'].'</td><td>'.$dtSup['0']['firstname'].' '.$dtSup['0']['lastname'].'</td><td align="center">'.$dtM['f1'].'</td><td align="center">'.number_format($dtM['monitnsemp_qualification'],2).'%</td></tr>';
					}						
				}	
				else{
					$tblMonit .='<tr><td colspan="5">No matches</td>';	
				}
				$tblMonit .='</table>';
				$rslt = $tblMonit;
			} //Termina de mostrar evaluacion de New service
		
		}else{
			$rslt = "-1";
		}
		echo $rslt;
	break;
	
	case 'ReportsQa':
		$rslt = cargaPag("../mtto/filtrosReportes.php");
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optEmp = "";
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}
		}
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = "";
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where name_depart='QUALITY' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtQa = $dbEx->selSql($sqlText);
		$optQa = "";
		foreach($dtQa as $dtQ){
			$optQa .='<option value="'.$dtQ['employee_id'].'">'.$dtQ['firstname']." ".$dtQ['lastname'].'</option>';		
		}
		
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
	
	
	case 'loadReportQaDetails':
		$filtroCS = "";
		$filtroSales = "";
		$filtroNS = "";
		$filtro = " where 1 ";
		$jointable = " ";
		$sqlText = "";
		$totales[] = "";
		
		if(strlen($_POST['fecha_ini'])>0 and strlen($_POST['fecha_fin'])>0){
			$fec_ini  = $oFec->cvDtoY($_POST['fecha_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fecha_fin']);
			$filtroCS .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroSales .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroNS .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and employee_id=".$_POST['emp'];
		}
		if($_POST['qa']>0){
			$filtro .=" and qa_agent =".$_POST['qa'];	
		}
		if($_POST['monit']==1){
			$filtro .= $filtroCS;
			$jointable .=" from employees e inner join monitoringcs_emp m on m.employee_id=e.employee_id";
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitcsemp, qa_agent, date_format(monitcsemp_date,'%d/%m/%Y') as f1, monitcsemp_qualification ".$jointable." ".$filtro." order by firstname, monitcsemp_date desc";
			
			$sqlItem = "select id_formcs as id, id_catcs, formcs_item as item, formcs_text from form_monitoring_cs where formcs_status='A' order by formcs_item";
			
			$jointable2 .="from employees e inner join monitoringcs_emp m on m.employee_id=e.employee_id inner join itemcs_monitoring it on it.id_monitcsemp=m.id_monitcsemp inner join form_monitoring_cs f on f.id_formcs=it.id_formcs inner join category_form_cs c on c.id_catcs = f.id_catcs ";
		}
		else if($_POST['monit']==2){
			$filtro .= $filtroSales;
			$jointable .=" from employees e inner join monitoringsales_emp m on m.employee_id=e.employee_id ";
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitsalesemp, qa_agent, date_format(monitsales_date,'%d/%m/%Y') as f1, monitsales_qualification ".$jointable." ".$filtro." order by firstname, monitsales_date desc";
			
			$sqlItem = "select id_formsales as id, id_catsales, formsales_item as item, formsales_text from form_monitoring_sales where formsales_status='A' order by formsales_item";
			
			$jointable2 .="from employees e inner join monitoringsales_emp m on m.employee_id=e.employee_id inner join itemsales_monitoring it on it.id_monitsalesemp=m.id_monitsalesemp inner join form_monitoring_sales f on f.id_formsales=it.id_formsales inner join category_form_sales c on c.id_catsales = f.id_catsales ";
		}
		else if($_POST['monit']==3){
			$filtro .= $filtroNS;
			$jointable .= " from employees e inner join monitoringns_emp m on m.employee_id=e.employee_id";
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitnsemp, qa_agent, date_format(monitnsemp_date,'%d/%m/%Y') as f1, monitnsemp_qualification ".$jointable." ".$filtro." order by firstname, monitnsemp_date desc";
			$sqlItem = "select id_formns as id, id_catns, formns_item as item, formns_text from form_monitoring_ns where formns_status='A' order by formns_item";
			
			$jointable2 .="from employees e inner join monitoringns_emp m on m.employee_id=e.employee_id inner join itemsales_monitoring it on it.id_monitnsemp=m.id_monitnsemp inner join form_monitoring_ns f on f.id_formns=it.id_formns inner join category_form_newservice c on c.id_catns = f.id_catns ";
		}
		
		
		$dtEva = $dbEx->selSql($sqlText);
		$tblResult = '<table class="tblHead" width="900px" align="center" cellpadding="2" cellspacing="1">';
		if($dbEx->numrows>0){
			
			$dtItem = $dbEx->selSql($sqlItem);
			//Evalua reporte para reporte de detalles de Customer services
			
			if($_POST['monit']==1){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td>Date</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemcs, itemcs_total, itemcs_resp, formcs_item from itemcs_monitoring it inner join form_monitoring_cs f on f.id_formcs = it.id_formcs where id_monitcsemp=".$dtEv['id_monitcsemp']." and formcs_status='A' order by formcs_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$tblResult .='<tr class="rowCons" onclick="loadMonitoringCS('.$dtEv['id_monitcsemp'].')"><td><font size="-2">'.$dtEv['username'].'</td><td><font size="-2">'.$dtEv['firstname']." ".$dtEv['lastname'].'</td><td>'.$dtEv['f1'].'</td>';
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemcs_resp']=='Y'){ $color = ' bgcolor="#006600"';}
							else if($dtItemE['itemcs_resp']=='N'){$color = ' bgcolor="#FF0000"';}
							else{$color = 'bgcolor="#333333"';}
							$tblResult .='<td align="center"  '.$color.'><font color="#FFFFFF">'.$dtItemE['itemcs_resp'].'</font></td>';	
							$totales[$dtItemE['formcs_item']] = $totales[$dtItemE['formcs_item']] + $dtItemE['itemcs_total'];
						}	
						$tblResult .='<td>'.number_format($dtEv['monitcsemp_qualification'],2).'%</td></tr>';
					}
				}
				$tblResult .='<tr><td></td><td></td><td></td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td><font size="-9">'.number_format($totales[$dtI['item']],2).'</td>';
				}
				$tblResult .='</tr>';
			}//Termina tabla de reporte detalles CS
			
			//Reporte de detalles sales
			
			else if($_POST['monit']==2){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td>Date</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemsales, itemsales_total, itemsales_resp, formsales_item from itemsales_monitoring it inner join form_monitoring_sales f on f.id_formsales = it.id_formsales where id_monitsalesemp=".$dtEv['id_monitsalesemp']." and formsales_status='A' order by formsales_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$tblResult .='<tr class="rowCons" onclick="loadMonitoringSales('.$dtEv['id_monitsalesemp'].')"><td><font size="-2">'.$dtEv['username'].'</td><td><font size="-2">'.$dtEv['firstname']." ".$dtEv['lastname'].'</td><td>'.$dtEv['f1'].'</td>';
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemsales_resp']=='Y'){ $color = ' bgcolor="#006600"';}
							else if($dtItemE['itemsales_resp']=='N'){$color = ' bgcolor="#FF0000"';}
							else{$color = 'bgcolor="#333333"';}
							$tblResult .='<td align="center"  '.$color.'><font color="#FFFFFF">'.$dtItemE['itemsales_resp'].'</font></td>';	
							$totales[$dtItemE['formsales_item']] = $totales[$dtItemE['formsales_item']] + $dtItemE['itemsales_total'];
						}	
						$tblResult .='<td>'.number_format($dtEv['monitsales_qualification'],2).'%</td></tr>';
					}
				}
				$tblResult .='<tr><td></td><td></td><td></td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td><font size="-9">'.number_format($totales[$dtI['item']],2).'</td>';
				}
				$tblResult .='</tr>';
				
			}//Termina de evaluacion evaluacion de Sales
			
			
			//Evalua reporte para New Services
			else if($_POST['monit']==3){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td>Date</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEva as $dtEv){
					$sqlText = "select id_itemns, itemns_total, itemns_resp, formns_item from itemns_monitoring it inner join form_monitoring_ns f on f.id_formns = it.id_formns where id_monitnsemp=".$dtEv['id_monitnsemp']." and formns_status='A' order by formns_item";
					$dtItemEv = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$tblResult .='<tr class="rowCons" onclick="loadMonitoringNS('.$dtEv['id_monitnsemp'].')"><td><font size="-2">'.$dtEv['username'].'</td><td><font size="-2">'.$dtEv['firstname']." ".$dtEv['lastname'].'</td><td>'.$dtEv['f1'].'</td>';
						foreach($dtItemEv as $dtItemE){
							if($dtItemE['itemns_resp']=='Y'){ $color = ' bgcolor="#006600"';}
							else if($dtItemE['itemns_resp']=='N'){$color = ' bgcolor="#FF0000"';}
							else{$color = 'bgcolor="#333333"';}
							$tblResult .='<td align="center"  '.$color.'><font color="#FFFFFF">'.$dtItemE['itemns_resp'].'</font></td>';	
							$totales[$dtItemE['formns_item']] = $totales[$dtItemE['formns_item']] + $dtItemE['itemns_total'];
						}	
						$tblResult .='<td>'.number_format($dtEv['monitnsemp_qualification'],2).'%</td></tr>';
					}
				}
				$tblResult .='<tr><td></td><td></td><td></td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td><font size="-9">'.number_format($totales[$dtI['item']],2).'</td>';
				}
				$tblResult .='</tr>';
				
			}
		}
		else{
			$tblResult .='<tr><td colspan="4">No matches</td></tr>';
		}
		$tblResult .= '</table>';
		echo $tblResult;
	break;
	
	
	//Reporte de promedios en un periodo y por tipo de evaluacion
	case 'loadReportQaTotal':
		$filtroCS = "";
		$filtroSales = "";
		$filtroNS = "";
		$filtro = " where 1 ";
		$jointable = " ";
		$sqlText = "";
		$totales[] = "";
		
		if(strlen($_POST['fecha_ini'])>0 and strlen($_POST['fecha_fin'])>0){
			$fec_ini  = $oFec->cvDtoY($_POST['fecha_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fecha_fin']);
			$filtroCS .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroSales .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroNS .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
		}
		if($_POST['sup']>0){
			$filtro .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtro .=" and m.employee_id=".$_POST['emp'];
		}
		if($_POST['qa']>0){
			$filtro .=" and qa_agent =".$_POST['qa'];	
		}
		if($_POST['monit']==1){
			$filtro .= $filtroCS;
			$jointable .=" from employees e inner join monitoringcs_emp m on m.employee_id=e.employee_id ";
			$sqlTextEmp = "select distinct(e.employee_id) as emp,username, lastname, firstname ".$jointable." ".$filtro." order by firstname";
			
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitcsemp, qa_agent, date_format(monitcsemp_date,'%d/%m/%Y') as f1, monitcsemp_qualification ".$jointable." ".$filtro." order by firstname, monitcsemp_date desc";
			
			$sqlItem = "select id_formcs as id, id_catcs, formcs_item as item, formcs_text from form_monitoring_cs where formcs_status='A' order by formcs_item";
		}
		else if($_POST['monit']==2){
			$filtro .= $filtroSales;
			$jointable .=" from employees e inner join monitoringsales_emp m on m.employee_id=e.employee_id ";
			$sqlTextEmp = "select distinct(e.employee_id) as emp,username, lastname, firstname ".$jointable." ".$filtro." order by firstname";
			
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitsalesemp, qa_agent, date_format(monitsales_date,'%d/%m/%Y') as f1, monitsales_qualification ".$jointable." ".$filtro." order by firstname, monitsales_date desc";
			
			$sqlItem = "select id_formsales as id, id_catsales, formsales_item as item, formsales_text from form_monitoring_sales where formsales_status='A' order by formsales_item";

		}
		else if($_POST['monit']==3){
			$filtro .= $filtroNS;
			$jointable .= " from employees e inner join monitoringns_emp m on m.employee_id=e.employee_id";
			$sqlTextEmp = "select distinct(e.employee_id) as emp,username, lastname, firstname ".$jointable." ".$filtro." order by firstname";
			
			$sqlText = "select e.employee_id, username, firstname, lastname, id_monitnsemp, qa_agent, date_format(monitnsemp_date,'%d/%m/%Y') as f1, monitnsemp_qualification ".$jointable." ".$filtro." order by firstname, monitnsemp_date desc";
			
			$sqlItem = "select id_formns as id, id_catns, formns_item as item, formns_text from form_monitoring_ns where formns_status='A' order by formns_item";

		}
		
		
		$dtEmp = $dbEx->selSql($sqlTextEmp);
		$tblResult = '<table class="tblHead" width="900px" align="center" cellpadding="2" cellspacing="1">';
		if($dbEx->numrows>0){
			
			$dtItem = $dbEx->selSql($sqlItem);
			//Evalua  para reporte de detalles de Customer services
			
			if($_POST['monit']==1){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td><font size="-3">N</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEmp as $dtE){
					$tblResult .='<tr class="rowCons"><td><font size="-2">'.$dtE['username'].'</td><td><font size="-2">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitcsemp) as cant from monitoringcs_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitcsemp_qualification) as calif from monitoringcs_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					$tblResult .='<td>'.$dtCount['0']['cant'].'</td>';
					foreach($dtItem as $dtI){
						$sqlText = "select sum(itemcs_total) as total from itemcs_monitoring it inner join monitoringcs_emp m on it.id_monitcsemp=m.id_monitcsemp ".$filtro." and employee_id=".$dtE['emp']." and it.id_formcs=".$dtI['id'];
						$dtTotalItem = $dbEx->selSql($sqlText);
						$totalItem = 0;
						if($dbEx->numrows>0 and $dtCount['0']['cant']>0){
							$totalItem = $dtTotalItem['0']['total']/$dtCount['0']['cant'];
						}
						$tblResult .='<td>'.number_format($totalItem,2).'</td>';
					}
					$tblResult .='<td>'.number_format($promEva,2).'%</td></tr>';
				}
				
			}//Termina tabla de reporte detalles CS
			
			//Reporte de detalles sales
			
			else if($_POST['monit']==2){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td><font size="-3">N</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEmp as $dtE){
					$tblResult .='<tr class="rowCons"><td><font size="-2">'.$dtE['username'].'</td><td><font size="-2">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitsalesemp) as cant from monitoringsales_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitsales_qualification) as calif from monitoringsales_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					$tblResult .='<td>'.$dtCount['0']['cant'].'</td>';
					foreach($dtItem as $dtI){
						$sqlText = "select sum(itemsales_total) as total from itemsales_monitoring it inner join monitoringsales_emp m on it.id_monitsalesemp=m.id_monitsalesemp ".$filtro." and employee_id=".$dtE['emp']." and it.id_formsales=".$dtI['id'];
						$dtTotalItem = $dbEx->selSql($sqlText);
						$totalItem = 0;
						if($dbEx->numrows>0 and $dtCount['0']['cant']>0){
							$totalItem = $dtTotalItem['0']['total']/$dtCount['0']['cant'];
						}
						$tblResult .='<td>'.number_format($totalItem,2).'</td>';
					}
					$tblResult .='<td>'.number_format($promEva,2).'%</td></tr>';
				}
				
			}//Termina de evaluacion evaluacion de Sales
			
			
			//Evalua reporte para New Services
			else if($_POST['monit']==3){
				$tblResult .='<tr class="showItem"><td width="3%"><font size="-2">BADGE</td><td width="20%"><font size="-3">EMPLOYEE</td><td><font size="-3">N</td>';
				foreach($dtItem as $dtI){
					$tblResult .='<td>'.$dtI['item'].'</td>';
					$totales[$dtI['item']] = 0;
				}
				$tblResult .='</tr>';
				foreach($dtEmp as $dtE){
					$tblResult .='<tr class="rowCons"><td><font size="-2">'.$dtE['username'].'</td><td><font size="-2">'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
					//Cuenta la cantidad de evaluaciones que ha tenido el agente
					$sqlTextCount = "select count(id_monitnsemp) as cant from monitoringns_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtCount = $dbEx->selSql($sqlTextCount);
					
					$sqlTextSum = "select sum(monitnsemp_qualification) as calif from monitoringns_emp ".$filtro." and employee_id=".$dtE['emp'];
					$dtSum = $dbEx->selSql($sqlTextSum);
					$promEva = 0;
					if($dtSum['0']['calif']>0 and $dtCount['0']['cant']>0){
						$promEva = $dtSum['0']['calif']/$dtCount['0']['cant'];
					}
					$tblResult .='<td>'.$dtCount['0']['cant'].'</td>';
					foreach($dtItem as $dtI){
						$sqlText = "select sum(itemns_total) as total from itemns_monitoring it inner join monitoringns_emp m on it.id_monitnsemp=m.id_monitnsemp ".$filtro." and employee_id=".$dtE['emp']." and it.id_formns=".$dtI['id'];
						$dtTotalItem = $dbEx->selSql($sqlText);
						$totalItem = 0;
						if($dbEx->numrows>0 and $dtCount['0']['cant']>0){
							$totalItem = $dtTotalItem['0']['total']/$dtCount['0']['cant'];
						}
						$tblResult .='<td>'.number_format($totalItem,2).'</td>';
					}
					$tblResult .='<td>'.number_format($promEva,2).'%</td></tr>';
				}
				
			}
		}
		else{
			$tblResult .='<tr><td colspan="4">No matches</td></tr>';
		}
		$tblResult .= '</table>';
		echo $tblResult;
		
	break;
	
	case 'WeeklyReport':
		$rslt =  cargaPag("../mtto/filtrosWeekly.php");
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where (name_role='SUPERVISOR' or name_role='AGENTE') and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optEmp = "";
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}
		}
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join user_roles u on pd.id_role=u.id_role where name_role='SUPERVISOR' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = "";
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname']." ".$dtS['lastname'].'</option>';
		}
		
		$sqlText = "select e.employee_id, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pe.id_placexdep=pd.id_placexdep inner join depart_exc d on d.id_depart=pd.id_depart where name_depart='QUALITY' and pe.status_plxemp='A' and user_status=1 order by firstname";
		$dtQa = $dbEx->selSql($sqlText);
		$optQa = "";
		foreach($dtQa as $dtQ){
			$optQa .='<option value="'.$dtQ['employee_id'].'">'.$dtQ['firstname']." ".$dtQ['lastname'].'</option>';		
		}
		
		$rslt = str_replace("<!--optSup-->",$optSup,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optQa-->",$optQa,$rslt);
		echo $rslt;
	break;
	
	case 'loadMonitoringReport':
		$filtroCS = " where 1 ";
		$filtroSA = " where 1 ";
		$filtroNS = " where 1 ";
		$filtro = " where 1 ";

		if(strlen($_POST['fecha_ini'])>0 and strlen($_POST['fecha_fin'])>0){
			$fec_ini  = $oFec->cvDtoY($_POST['fecha_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fecha_fin']);
			$filtroCS .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroSA .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroNS .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
		}
		if($_POST['sup']>0){
			$filtroCS .=" and e.id_supervisor=".$_POST['sup'];
			$filtroSA .=" and e.id_supervisor=".$_POST['sup'];
			$filtroNS .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtroCS .=" and m.employee_id=".$_POST['emp'];
			$filtroSA .=" and m.employee_id=".$_POST['emp'];
			$filtroNS .=" and m.employee_id=".$_POST['emp'];
		}
		if($_POST['qa']>0){
			$filtroCS .=" and qa_agent =".$_POST['qa'];
			$filtroSA .=" and qa_agent =".$_POST['qa'];
			$filtroNS .=" and qa_agent =".$_POST['qa'];	
		}

		$sqlText = "select employee_id, username, firstname, lastname from employees";
		$dtEmp = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="1" >';
		
		if($dbEx->numrows>0){
			

			$start = strtotime($fec_ini);
			$end = strtotime($fec_fin);
			
			$tblResult = '<tr><td align="center"><b>Badge</b></td><td align="center"><b>Employee</b></td>';
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
			
			//Dependiendo el tipo de evaluacion de monitoreo que se seleccione hace la comparancion y querys
			$monitCS = false;
			$monitSales = false;
			$monitNS = false;
			if($_POST['monit']==0){$monitCS = true; $monitSales = true; $monitNS = true;}
			if($_POST['monit']==1){$monitCS = true;}
			if($_POST['monit']==2){$monitSales = true;}
			if($_POST['monit']==3){$monitNS = true;}
			foreach($dtEmp as $dtE){
				$flag = false;
				if($monitCS){
					$sqlText = "select e.employee_id from monitoringcs_emp m inner join employees e on e.employee_id=m.employee_id ".$filtroCS." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}
				}
				if($monitSales){
					$sqlText = "select e.employee_id from monitoringsales_emp m inner join employees e on e.employee_id=m.employee_id ".$filtroSA." and m.employee_id=".$dtE['employee_id'];
					$dtEmpEva = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$flag = true;	
					}
				}
				if($monitNS){
					$sqlText = "select e.employee_id from monitoringns_emp m inner join employees e on e.employee_id=m.employee_id ".$filtroNS." and m.employee_id=".$dtE['employee_id'];
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
					$nTotal = "";
					$promTotal = "";
					if($monitCS){
					//Conteo por dias para customer services 
						$sqlText = "select sum(monitcsemp_qualification) as sumcs from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date='".date('Y-m-d',$i)."' ";
						$dtSumCS = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitcsemp_qualification) as countcs from monitoringcs_emp where employee_id=".$dtE['employee_id']." and monitcsemp_date='".date('Y-m-d',$i)."' ";
						$dtCountCS = $dbEx->selSql($sqlText);
						if($dtCountCS['0']['countcs']>0){
							$nCS = 	$dtCountCS['0']['countcs'];
							$sumaSemana = $sumaSemana + $dtSumCS['0']['sumcs'];
							$promCS = $dtSumCS['0']['sumcs']/$nCS;
						}
					}
					if($monitSales){
						//Conteo por dias para sales
						$sqlText = "select sum(monitsales_qualification) as sumsa from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date='".date('Y-m-d',$i)."' ";
						$dtSumSA = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitsales_qualification) as countsa from monitoringsales_emp where employee_id=".$dtE['employee_id']." and monitsales_date='".date('Y-m-d',$i)."' ";
						$dtCountSA = $dbEx->selSql($sqlText);
						if($dtCountSA['0']['countsa']>0){
							$nSA = $dtCountSA['0']['countsa'];
							$sumaSemana = $sumaSemana + $dtSumSA['0']['sumsa'];
							$promSA = $dtSumSA['0']['sumsa']/$nSA;	
						}
					}
					if($monitNS){
						//Conteo por dias para new service
						$sqlText = "select sum(monitnsemp_qualification) as sumns from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date='".date('Y-m-d',$i)."' ";
						$dtSumNS = $dbEx->selSql($sqlText);
						$sqlText = "select count(monitnsemp_qualification) as countns from monitoringns_emp where employee_id=".$dtE['employee_id']." and monitnsemp_date='".date('Y-m-d',$i)."' ";
						$dtCountNS = $dbEx->selSql($sqlText);
						if($dtCountNS['0']['countns']>0){
							$nNS = $dtCountNS['0']['countns'];
							$sumaSemana = $sumaSemana + $dtSumNS['0']['sumns'];
							$promNS = $dtSumNS['0']['sumns']/$nNS;	
						}
					}
					//Sumar numeros de evaluaciones y promedios
					
					if($nCS>0 or $nSA>0 or $nNS>0){
						$nTotal = $nCS + $nSA + $nNS;
						$promTotal = $promCS + $promSA + $promNS;
						$promTotal = number_format($promTotal,2);
						$totalEvaSemana = $totalEvaSemana + $nTotal;
					}
					$color = "#FFFFFF";
					if($promTotal>=0 and $promTotal <=75 and $promTotal!=""){
						$color = "#FF0000";
					}
					else if($promTotal>75 and $promTotal<=80){
						$color = "#FFCC00";
					}
					else if($promTotal>80 and $promTotal<=90){
						$color = "#009933";
					}
					else if($promTotal>90 and $promTotal<=99){
						$color = "#0066CC";
					}
					else if($promTotal>99 and $promTotal <=100){
						$color = "#FB9E42";	
					}
					$tblResult .='<td align="center" bgcolor="'.$color.'" style="border: 1px #638DBD inset;"><b>'.$promTotal.'</b></td>';
					
					if($dia==0){
						if($totalEvaSemana >0){
							$promSemana = 	number_format(($sumaSemana/$totalEvaSemana),2);
						}
						$color = "#FFFFFF";
						if($promSemana>=0 and $promSemana <=75  and $totalEvaSemana>0){
							$color = "#FF0000";
						}
						else if($promSemana>75 and $promSemana<=80){
							$color = "#FFCC00";
						}
						else if($promSemana>80 and $promSemana<=90){
							$color = "#009933";
						}
						else if($promSemana>90 and $promSemana<=99){
							$color = "#0066CC";
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
		echo $rslt;
	
	break;
	
	/*case 'loadWeeklyReport':
		$filtroCS = " where 1 ";
		$filtroSA = " where 1 ";
		$filtroNS = " where 1 ";

		if(strlen($_POST['fecha_ini'])>0 and strlen($_POST['fecha_fin'])>0){
			$fec_ini  = $oFec->cvDtoY($_POST['fecha_ini']);
			$fec_fin = $oFec->cvDtoY($_POST['fecha_fin']);
			$filtroCS .=" and monitcsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroSA .=" and monitsales_date between date '".$fec_ini."' and '".$fec_fin."' ";
			$filtroNS .=" and monitnsemp_date between date '".$fec_ini."' and '".$fec_fin."' ";
		}
		if($_POST['sup']>0){
			$filtroCS .=" and e.id_supervisor=".$_POST['sup'];
			$filtroSA .=" and e.id_supervisor=".$_POST['sup'];
			$filtroNS .=" and e.id_supervisor=".$_POST['sup'];
		}
		if($_POST['emp']>0){
			$filtroCS .=" and m.employee_id=".$_POST['emp'];
			$filtroSA .=" and m.employee_id=".$_POST['emp'];
			$filtroNS .=" and m.employee_id=".$_POST['emp'];
		}
		if($_POST['qa']>0){
			$filtroCS .=" and qa_agent =".$_POST['qa'];
			$filtroSA .=" and qa_agent =".$_POST['qa'];
			$filtroNS .=" and qa_agent =".$_POST['qa'];	
		}
		$start = strtotime($fec_ini);
		$end = strtotime($fec_fin);
		$rslt = '<table class="tblHead" width="900px" align="center" cellpadding="2" cellspacing="2">';
		$tblResult = "";
		$flag = false;
		$sqlText = "select distinct(m.employee_id) as emp, firstname, lastname from monitoringcs_emp m inner join employees e on e.employee_id = m.employee_id ".$filtroCS." order by firstname";
		$dtEmpCS = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$flag = true;
		}
		$sqlText = "select distinct(m.employee_id) as emp, firstname, lastname from monitoringsales_emp m inner join employees e on e.employee_id = m.employee_id ".$filtroSA." order by firstname";
		$dtEmpSA = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$flag = true;
		}
		$sqlText = "select distinct(m.employee_id) as emp, firstname, lastname from monitoringns_emp m inner join employees e on e.employee_id = m.employee_id ".$filtroNS." order by firstname";
		$dtEmpNS = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$flag = true;
		}
		
		//Si se ha realizado evaluacion en esas fechas se genera el reporte 
		if($flag = true){
			$tblResult .='<tr><td>REPS</td>';
			$n = 1;
			for($i = $start; $i <=$end; $i +=86400){
				$tblResult .='<td>'.date('d/m/Y',$i).'</td>';
				$n = $n + 1;
			}
			$tblResult .='<td>1</td><td>2</td>3</td></tr>';
			foreach($dtEmpCS as $dtE){
				$tblResult .='<tr><td>'.$dtE['firstname']." ".$dtE['lastname'].'</td>';
				for($i = $start; $i <=$end; $i +=86400){
					$sqlText = "select monitcsemp_qualification from monitoringcs_emp where employee_id=".$dtE['emp']." and monitcsemp_date='".date('Y-m-d',$i)."' ";
					$dtCalif = $dbEx->selSql($sqlText);
					$tbl = '<td></td>';
					if($dbEx->numrows>0){
						$x = "";
						foreach($dtCalif as $dtC){
							$x .= ' X '; 	
						}
						$tbl = '<td>'.$x.'</td>';
					}
					$tblResult .=$tbl;
				}
				$tblResult .='<td>';
			}
			
			
		
			$tituloCS .='<tr><td  align="center" colspan="'.$n.'">WEEKLY MONITORING REPORT CUSTOMER SERVICE '.$_POST['fecha_ini'].' - '.$_POST['fecha_fin'].'</td></tr>';
		}
		else{
			$rslt = '<tr><td>No matches</td></tr>';
		}
		echo $rslt;
	break;*/
	
}
?>

