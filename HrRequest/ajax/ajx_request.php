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
  
switch($_POST['Do']){
	
	case 'formGetRequest':
		$rslt = cargaPag("../mtto/filtrosGetRequest.php");
		
		$sqlText = "select * from type_request where tpreq_status='A'";
		$dtTpReq = $dbEx->selSql($sqlText);
		$optReq = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtTpReq as $dtT){
				$optReq .='<option value="'.$dtT['TPREQ_ID'].'">'.$dtT['TPREQ_NAME'].'</option>';
			}
		}
		$rslt = str_replace("<!--optTpRequest-->",$optReq,$rslt);
		
		echo $rslt;
	break;
	case 'getRequest':
		$filtro = "";
		if($_POST['status']!='0'){
			$filtro .=" and hrreq_status='".$_POST['status']."'";
		}
		if($_POST['tpReq']>0){
			$filtro .=" and h.tpreq_id=".$_POST['tpReq'];
		}
		if(strlen($_POST['fechaIni'])>0){
			$fecIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fecFin = $oFec->cvDtoY($_POST['fechaFin']);
			$filtro .=" and hrreq_date between date '".$fecIni."' and '".$fecFin."'";
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (firstname like '%".$_POST['nombre']."%' or lastname like '%".$_POST['nombre']."%' or concat(firstname,' ',lastname) like '%".$_POST['nombre']."%') ";	
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and username like '%".$_POST['badge']."%'";
		}
		
		$sqlText = "select firstname, lastname, username, h.hrreq_id, h.tpreq_id, hrreq_authorizer, hrreq_content, date_format(hrreq_date,'%d/%m/%Y') as fecReq, date_format(hrreq_dayresponse,'%d/%m/%Y') as fecRespuesta, hrreq_response, hrreq_status, tpreq_name from hrrequest h inner join type_request tr on h.tpreq_id=tr.tpreq_id inner join employees e on e.employee_id=h.employee_id where 1 ".$filtro;
		
		$dtReq = $dbEx->selSql($sqlText);
		$tblResult = '<table cellpadding="3" cellspacing="1" width="80%" border="1" class="backTablaMain" align="center" bordercolor="#BFD1DF">';
		$tblResult .='<tr><td colspan="10">Matches: '.$dbEx->numrows.'</td></tr>';
		$tblResult .='<tr class="showItem" >
		<td width="2%">#</td>
		<td width="5%">Badge</td>
		<td width="17%">Employee</td>
		<td width="15%">Category</td>
		<td width="5%">Date</td>
		<td width="16%">Assigned to</td>
		<td width="10%">Date of resolution</td>
		<td width="20%">Reply</td>
		<td width="5%">Form</td>
		<td width="5%"></td
		</tr>';
		if($dbEx->numrows>0){
			foreach($dtReq as $dtR){
				$responsable = "";
				if($dtR['hrreq_authorizer']>0){
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtR['hrreq_authorizer'];
					$dtA = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$responsable = $dtA['0']['firstname']." ".$dtA['0']['lastname'];	
					}
				}
				
				$btn = "";
				if($dtR['hrreq_status']=='O'){
					$btn = '<img src="images/close.png" alt="Close request" title="Close request"  width="80" onclick="closeReq('.$dtR['hrreq_id'].')" >';
				}
								
				$tblResult .='<tr class="rowCons" >
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['hrreq_id'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['username'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['firstname'].' '.$dtR['lastname'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['tpreq_name'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['fecReq'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$responsable.'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['fecRespuesta'].'</td>
				<td onclick="getDetallesRequest('.$dtR['hrreq_id'].')">'.$dtR['hrreq_response'].'</td>
				<td><img src="images/llenar_formulario.jpg" alt="create document" title="create document" style="cursor:pointer" width="40" onclick="createDoc('.$dtR['hrreq_id'].','.$dtR['tpreq_id'].')" ></td>
				<td>'.$btn.'</td>
				</tr>';
			}
		}
		$tblResult .='</table>';
		
		echo $tblResult;
		
		
	break;
	
	case 'closeReq':
		$rslt = cargaPag("../mtto/formCloseReq.php");
		$rslt = str_replace("<!--idRequest-->",$_POST['idR'],$rslt);
		echo $rslt;
	break;	
	
	case 'saveCloseRequest':
		$sqlText = "update hrrequest set hrreq_authorizer=".$_SESSION['usr_id'].", hrreq_dayresponse=now(), hrreq_response='".$_POST['descrip']."', hrreq_status='C'";
		$dbEx->updSql($sqlText);
		echo "2";
	break;
	
	case 'hrForms':
		$sqlText = "select * from type_request where tpreq_status='A' order by tpreq_name";
		$dtReq = $dbEx->selSql($sqlText);
		
		$tblResult = '<table cellpadding="3" cellspacing="1" width="70%" border="1" class="backTablaMain" align="center" bordercolor="#BFD1DF">';
		if($dbEx->numrows>0){
			$i = 0;
			foreach($dtReq as $dtR){
				$fechaActual = actual_date();
				$tblResult .='<tr><td>'.$dtR['TPREQ_NAME'].'</td>
				<td align="center" width="20%">
				<form target="_blank" id="form'.$i.'" action="mtto/'.$dtR['TPREQ_FORMAT'].'" method="post">
				<input type="image" src="images/doc.png" alt="Export format" width="35" style="cursor:pointer" title="download form">
				<input type="hidden" value="'.$fechaActual.'" id="txtFechaActual" name="txtFechaActual">
				</form>
				</td></tr>';
				$i++;
			}
		}
		echo $tblResult;
	break;
	
	case 'createDoc':
		$rslt = "";
		//Obtiene datos del tipo de forma para mostrar el formato segun la base de datos
		$sqlText = "select * from type_request where tpreq_id=".$_POST['idTpReq'];
		$dtTpReq = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt = cargaPag("../forms/".$dtTpReq['0']['TPREQ_FORMAT']);
		}
		
		//Obtiene los datos registrados por el agente para la peticion de requisicion
		$sqlText = "select hrreq_id, r.employee_id, username, firstname, lastname from hrrequest r inner join employees e on r.employee_id=e.employee_id where hrreq_id=".$_POST['idR'];
		$dtReq = $dbEx->selSql($sqlText);
		
		//Lista de fechas de planillas
		$sqlText = "select paystub_id, date_format(paystub_delivery,'%d/%m/%Y') as fecPlanilla from paystub where paystub_status='A' order by paystub_id desc";
		$dtPay = $dbEx->selSql($sqlText);
		$optPaystub = '<option value="0">Select a date</option>';
		if($dbEx->numrows>0){
			foreach($dtPay as $dtP){
				$optPaystub .='<option value="'.$dtP['paystub_id'].'">'.$dtP['fecPlanilla'].'</option>';
			}
		}
		
		//Mostrar los nombres de todos los agentes activos para la parte de las firmas
		$sqlText = "select employee_id, firstname, lastname from employees where user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		if($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>';
			}
		}
		
		$rslt = str_replace("<!--IdRequest-->",$_POST['idR'],$rslt);
		$rslt = str_replace("<!--nombre-->",$dtReq['0']['lastname'].", ".$dtReq['0']['firstname'],$rslt);
		$rslt = str_replace("<!--fecha_actual-->",actual_date(),$rslt);
		$rslt = str_replace("<!--optPaystub-->",$optPaystub,$rslt);
		$rslt = str_replace("<!--optEmployees-->",$optEmp,$rslt);
		
		echo $rslt;
	break;
	
	case 'saveDoc':
		$arrayFirmas = $_POST['arrayFirmas'];
		$firmas = explode(" ",$arrayFirmas);
		
		$listFirmas = "";
		for($i=0; $i<count($firmas); $i++){
			if($i>0){
				$listFirmas .= ",";
			}
			$listFirmas .= $firmas[$i];
		}
		
		$sqlText = "update hrrequest set hrreq_money='".$_POST['dinero']."', paystub_id=".$_POST['idPaystub'].", hrreq_description='".$_POST['descripcion']."', hrreq_firmas='".$listFirmas."' where hrreq_id=".$_POST['idR'];
		$dbEx->updSql($sqlText);
		
		echo "2";

		
	break;
	
	case 'loadDoc':
		$sqlText = "select h.hrreq_id, tpreq_format from hrrequest h inner join type_request t on h. where hrreq_id=".$_POST['idR'];
		$dtReq = $dbEx->selSql($sqlText);
		//Verifica si se devuelven resultados de la busqueda del HRRequest, si no devuelve nada mande 1
		if($dbEx->numrows>0){
			if($dtReq['0']['TPREQ_ID']==1){
				$rslt = cargaPag("../forms/loadDescuentoPlanilla.php");
			}
		}
		else{
			$rslt = 1;	
		}
		echo $rslt;
	break;
}
  
?>