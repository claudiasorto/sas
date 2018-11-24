 <?php
		//Funciones para pay stub	
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
require_once("../salary_funcs.php");
 
$dbEx = new DBX;
$oFec = new OFECHA;
$sFunc = new SAL;

function cargaPag($urlToLoad){ //funcion para cargar una pagina
    $pagLoad = '';
    $fp=fopen($urlToLoad,"r") or die("Error al abrir el fichero");
    $pagLoad = fread($fp,30000);
    return $pagLoad;
  }
function sumarHoras($h1,$h2)
{
	$h2h = date('H', strtotime($h2));
	$h2m = date('i', strtotime($h2));
	$h2s = date('s', strtotime($h2));
	$hora2 =$h2h." hour ". $h2m ." min ".$h2s ." second";

	$horas_sumadas= $h1." + ". $hora2;
	$text=date('H:i:s', strtotime($horas_sumadas)) ;
	return $text;

}
function restarHoras($h1,$h2)
{
    $dbExec = new DBX;
	$sqlText = "select sec_to_time(if((time_to_sec('".$h1."') - time_to_sec('".$h2."'))<0,0,(time_to_sec('".$h1."') - time_to_sec('".$h2."')))) result from dual";
	$result = $dbExec->selSql($sqlText);
	return $result['0']['result'];

}



switch($_POST['Do']){
	
	//Carga formulario para crear el paystub
	case 'createPay':
		$rslt = cargaPag("../mtto/formCreatePay.php");
		$sqlText = "select paystub_id, date_format(paystub_delivery,'%d/%m/%Y') as f1 from paystub order by paystub_id desc";
		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtPay as $dtP){
				$optPay .= '<option value="'.$dtP['paystub_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		$rslt = str_replace("<!--payroll_date-->",$optPay,$rslt);
		
		/*Obtiene la configuracion de los descuentos*/
		$sqlText = "select ps.disc_id, ps.disc_label, pa.disc_attributename ".
			"from pay_discount_setup ps, pay_discount_attr pa ".
			"where ps.disc_attributeid = pa.disc_attributeid ".
			"and ifnull(ps.disc_end_date,sysdate() + 1) > sysdate() ".
    		"order by ps.disc_label";
		$dtDisc = $dbEx->selSql($sqlText);
		$tblDisc = '';
		if($dbEx->numrows>0){
			foreach($dtDisc as $dtD){
				$tblDisc .= '<tr><td align="right">'.$dtD['disc_label'].': </td><td><input type="file" name="'.$dtD['disc_attributename'].'" id="'.$dtD['disc_attributename'].'" size="25" class="txtPag" /></td></tr>';
			}
		}
		
		$rslt = str_replace("<!--tblDiscount-->",$tblDisc,$rslt);
		
		echo $rslt;
	break;
	
	//Guarda la nueva planilla y genera las boletas de pago para todos los empleados activos
	case 'saveNewPaystub':
		$fechaEntrega 	= $oFec->cvDtoY($_POST['fecEntrega']);
		$fechaIni 		= $oFec->cvDtoY($_POST['fecIni']);
		$fechaFin 		= $oFec->cvDtoY($_POST['fecFin']);

		$sqlText = "select * from paystub where paystub_delivery='".$fechaEntrega."'";
		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt = -1;
		}
		else{
			//Crea el nuevo paystub
			$sqlText = "insert into paystub set paystub_ini='".$fechaIni."', paystub_fin='".$fechaFin."', paystub_delivery='".$fechaEntrega."'";
			$dbEx->insSql($sqlText);
			$dtIdPay = $dbEx->insertID;

			
			//Genera los paystub para todos los empleados activos hasta la fecha fin de la planilla
   			$sqlText = "select distinct(e.employee_id) employee_id ".
				"from employees e inner join employee_status st on e.user_status = st.status_id ".
				"inner join plazaxemp pe on pe.employee_id = e.employee_id ".
    			"inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
    			"inner join places pl on pl.id_place = pd.id_place ".
				"where '".$fechaIni."' between pe.start_date and ifnull(pe.end_date, '".$fechaFin."') ".
				"and st.status_name <> 'Aspirante' ";
			
            $dtEmp = $dbEx->selSql($sqlText);

			if($dbEx->numrows>0){ //verifica si devolvio resultados
				foreach($dtEmp as $dtE){

					//Actualizar la boleta de pago por empleado
					$boletaRslt = $sFunc->calcularPagoEmpleado(
							$dtE['employee_id'],
							$dtIdPay,
							$fechaIni,
							$fechaFin,
							$fechaEntrega
					);
	
				}	//Fin de busqueda por empleados
			}
			
			$rslt = 2;	
		}
		echo $rslt;
	break;
	
	//Funcion para cargar formulario que despliega el calculo de todos los paystub
	case 'calcularPay':
		$sqlText = "select paystub_id, date_format(paystub_delivery, '%d/%m/%Y') as f1 from paystub order by paystub_delivery desc";
		$dtPay = $dbEx->selSql($sqlText);
		$optDate ='<option value="0">Select a Date</option>';
		if($dbEx->numrows>0){
			foreach($dtPay as $dtP){
				$optDate .= '<option value="'.$dtP['paystub_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		
		$rslt = "";
		$rslt = '<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">';
		$rslt .='<tr bgcolor="#8FBC8F"><td colspan="2" align="center"><font color="#FFFFFF">PAYMENTS SUMMARY</font></td></tr>';
		$rslt .='<tr><td align="right">Select a payment date: </td>';
		$rslt .='<td><select id="lsDate">'.$optDate.'</select></td></tr>';
		$rslt .='<tr><td align="right">Status: </td>';
		$rslt .='<td><select id="lsStatus">
		<option value="-1">[ALL]</option><option value="1">ACTIVE</option><option value="0">INACTIVE</option></select></td></tr>';
		$rslt .='<tr><td colspan="2" align="center"><input type="button" onclick="loadCalculoPay()" value="Load"></td></tr>';
		$rslt .='</table><br><br>';
		$rslt .='<div id="lyData"></div>';
		echo $rslt;
	break;
	
	//Funciona para mostrar los paystub calculados
	case 'loadCalculoPay':
		$filtro ="";
		if($_POST['estado']>=0){
			$filtro .=" and user_status=".$_POST['estado'];
		}
		$sqlText = "select date_format(paystub_ini,'%d/%m/%Y') as f1, ".
					"date_format(paystub_fin, '%d/%m/%Y') as f2, ".
					"date_format(paystub_delivery,'%d/%m/%Y') as f3 ".
					"from paystub where paystub_id=".$_POST['idP'];
		$infoPaystub = $dbEx->selSql($sqlText);

        $sqlText = "select p.*, e.USERNAME, e.FIRSTNAME, e.LASTNAME, ".
					"((ifnull(attribute1,0)) + (ifnull(attribute11,0)) + ".
					"(ifnull(attribute2,0)) + (ifnull(attribute12,0)) + ".
    				"(ifnull(attribute3,0)) + (ifnull(attribute13,0) ) + ".
    				"(ifnull(attribute4,0)) + (ifnull(attribute14,0)) + ".
   					"(ifnull(attribute5,0)) + (ifnull(attribute15,0)) + ".
    				"(ifnull(attribute6,0)) + (ifnull(attribute16,0)) + ".
		    		"(ifnull(attribute7,0)) + (ifnull(attribute17,0)) + ".
    				"(ifnull(attribute8,0)) + (ifnull(attribute18,0)) + ".
    				"(ifnull(attribute9,0)) + (ifnull(attribute19,0)) + ".
	    			"(ifnull(attribute10,0)) + (ifnull(attribute20,0))) DESCUENTOS ".
					"from paystubxemp p inner join employees e on p.employee_id=e.employee_id ".
					"where p.paystub_id=".$_POST['idP']." ".$filtro;


		$dtPay = $dbEx->selSql($sqlText);
		$rslt = '<table width="1000" align="center" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;" bordercolor="#8FBC8F" cellpadding="2" cellspacing="2" >';
		if($dbEx->numrows>0){
			$n = 1;
			$rslt .='<tr><td colspan="5">Matches: '.$dbEx->numrows.'</td>';
			$rslt .='<td align="center"><input type="image" src="images/update.png" alt="update calculations for payment" style="cursor:pointer" width="60" title="update calculations for payment" onclick="updPayment('.$_POST['idP'].')"/></td> ';
			$rslt .='<td align="center"><input type="image" src="images/enable.png" alt="paystub enable" style="cursor:pointer" width="60" title="Click to paystub enable" onclick="enablePaystub('.$_POST['idP'].')"/></td>';
			$rslt .='<td align="center"><form target="_blank" action="report/xls_rptPaystub.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="40" style="cursor:pointer" title="Export to excel" /><input type="hidden" name="idP" value="'.$_POST['idP'].'"><input type="hidden" name="filtro" value="'.$filtro.'"></td></tr>';
			
			$rslt .='<tr bgcolor="#8FBC8F"><td colspan="8" align="center"><font color="#FFFFFF">PAY STUBS FOR THE PERIOD '.$infoPaystub['0']['f1'].' - '.$infoPaystub['0']['f2'].'</font></td></tr>';
			$rslt .='<tr><td width="5%">N&deg;</td><td width="10%">BADGE</td><td width="30%">EMPLOYEE</td><td width="10%">SALARY</td><td width="10%">TOTAL INCOME</td><td width="10%">TOTAL DEDUCTIONS</td><td width="10%">TOTAL DISCOUNTS</td><td width="15%">PAYMENT TO RECEIVE</td></tr>';
			foreach($dtPay as $dtP){
				//Verifica si existe una incidencia de pago para el paystub
				$incTotalIngresos = 0;
				$incTotalDeducciones = 0;
				$incTotalDescuentos = 0;
				$incSalary = 0;
				$incRecibir = 0;
				
				$sqlText = "select pi.*, ".
				    "((ifnull(attribute1,0)) + (ifnull(attribute11,0)) + ".
					"(ifnull(attribute2,0)) + (ifnull(attribute12,0)) + ".
    				"(ifnull(attribute3,0)) + (ifnull(attribute13,0) ) + ".
    				"(ifnull(attribute4,0)) + (ifnull(attribute14,0)) + ".
   					"(ifnull(attribute5,0)) + (ifnull(attribute15,0)) + ".
    				"(ifnull(attribute6,0)) + (ifnull(attribute16,0)) + ".
		    		"(ifnull(attribute7,0)) + (ifnull(attribute17,0)) + ".
    				"(ifnull(attribute8,0)) + (ifnull(attribute18,0)) + ".
    				"(ifnull(attribute9,0)) + (ifnull(attribute19,0)) + ".
	    			"(ifnull(attribute10,0)) + (ifnull(attribute20,0))) DESCUENTOS ".
					"from paystub_incidents pi where payxemp_id=".$dtP['PAYXEMP_ID'];

				$dtInc = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$incTotalIngresos = $dtInc['0']['PAYINC_OTHERINCOME'] + $dtInc['0']['PAYINC_BONO']  + $dtInc['0']['PAYINC_VACATION'] + $dtInc['0']['PAYINC_AGUINALDO'] + $dtInc['0']['PAYINC_OTNOCT'] +$dtInc['0']['PAYINC_OTDIURNAL'] +  $dtInc['0']['PAYINC_HORASNOCT'] + $dtInc['0']['PAYINC_SALARY'] +  $dtInc['0']['PAYINC_ADDITIONALHOURS'] - $dtInc['0']['PAYINC_SALARYDISC'] - $dtInc['0']['PAYINC_SEVENTH'];
					
					$incTotalDeducciones = $dtInc['0']['PAYINC_ISR'] + $dtInc['0']['PAYINC_ISSS'] + $dtInc['0']['PAYINC_AFP']; 
					$incTotalDescuentos = $dtInc['0']['DESCUENTOS'];
					
					$incSeverance = $dtInc['0']['PAYINC_SEVERANCE'];
					$incReintegros = $dtInc['0']['PAYINC_REFUNDS']; 
					
					$incSalary = $dtInc['0']['PAYINC_SALARY'];
					$incRecibir = $dtInc['0']['PAYINC_RECEIVED'];
				}
				
				
				$totalIncome = $dtP['PAYXEMP_OTHERINCOME'] + $dtP['PAYXEMP_BONO'] + $dtP['PAYXEMP_VACATION'] + $dtP['PAYXEMP_AGUINALDO'] + $dtP['PAYXEMP_OTNOCT'] + $dtP['PAYXEMP_OTDIURNAL'] + $dtP['PAYXEMP_HORASNOCT'] + $dtP['PAYXEMP_SALARY'] + $dtP['PAYXEMP_ADDITIONALHOURS'] + $dtP['PAYXEMP_SEVERANCE'];;
				
				//Obtener total de deducciones 
				$sqlText = "select sum(amount) totalDeducciones from paystub_legaldisc where payxemp_id = ".$dtP['PAYXEMP_ID'];
				$dtDs = $dbEx->selSql($sqlText);

				$totalDeductions = $dtDs['0']['totalDeducciones'];
				$totalDiscounts = $dtP['DESCUENTOS'] + $dtP['PAYXEMP_SALARYDISC'] + $dtP['PAYXEMP_SEVENTH'];
				$totalSeverance = $dtP['PAYXEMP_SEVERANCE'];
				$totalReintegro = $dtP['PAYXEMP_REFUNDS'];
				
				$rslt .='<tr><td>'.$n.'</td><td>'.$dtP['USERNAME'].'</td><td>'.$dtP['FIRSTNAME'].' '.$dtP['LASTNAME'].'</td><td>'.number_format($dtP['PAYXEMP_SALARY'] + $incSalary,2).'</td><td>'.number_format($totalIncome + $incTotalIngresos,2).'</td><td>'.number_format($totalDeductions + $incTotalDeducciones,2).'</td><td>'.number_format($totalDiscounts + $incTotalDescuentos,2).'</td><td>'.number_format($dtP['PAYXEMP_LIQUID'] + $incRecibir,2).'</td></tr>';
				$n++;
			}
		}
		else{
			$rslt .= '<tr><td colspan="8"></td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	//Funcion para mostrar el ultimo paystub para el usuario loggeado
	case 'lastPay':

		$sqlText = "select ps.paystub_id from paystubxemp p inner join paystub ps on p.paystub_id=ps.paystub_id ".
					"where employee_id=".$_SESSION['usr_id'].
					" and paystub_delivery=(select max(paystub_delivery) from paystub where paystub_status='A')";
		$dtPay = $dbEx->selSql($sqlText);

		$rslt = "";
		if ($dbEx->numrows > 0) {
			$rslt = $sFunc->getBoletaPago($dtPay['0']['paystub_id'], $_SESSION['usr_id']);	
		}
		
		echo $rslt;

	break;
	
	case 'historicPay':
		$sqlText = "select distinct(p.paystub_id) as p_id, date_format(paystub_delivery, '%d/%m/%Y') as f1 from paystub p inner join paystubxemp pe on p.paystub_id=pe.paystub_id where employee_id=".$_SESSION['usr_id']." and paystub_status='A' order by paystub_delivery desc";
		$dtPay = $dbEx->selSql($sqlText);
		$optDate = "";
		if($dbEx->numrows>0){
			$optDate ='<option value="0">Select a Date</option>';
			foreach($dtPay as $dtP){
				$optDate .= '<option value="'.$dtP['p_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		else{
			$optDate .='<option value="0">Paystubs not available</option>';	
		}
		$rslt .= '<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">';
		$rslt .='<tr bgcolor="#8FBC8F"><td colspan="2" align="center"><font color="#FFFFFF">HISTORIC PAY STUBS</font></td></tr>';
		$rslt .='<tr><td align="right">Select a payment date: </td>';
		$rslt .='<td><select id="lsDate">'.$optDate.'</select></td></tr>';
		$rslt .='<tr><td colspan="2" align="center"><input type="button" onclick="loadPay()" value="Load"></td></tr>';
		$rslt .='</table><br><br>';
		$rslt .='<div id="lyData"></div>';
		echo $rslt;
	break;
	
	case 'loadPay':

		$rslt = $sFunc->getBoletaPago($_POST['idP'], $_SESSION['usr_id']);
		echo $rslt;
	
	break;
	
	case 'employeesPayStubs':
		$filtro = "where 1 and user_status=1 and pe.status_plxemp='A'";
		$sqlText = "select name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id where e.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A' and user_status=1";
		$dtPl = $dbEx->selSql($sqlText);
		
		if($_SESSION['usr_rol']!='GERENCIA' and $_SESSION['usr_rol']!='RECURSOS HUMANOS' and $_SESSION['usr_rol']!="" and $dtPl['0']['name_place']!='ACCOUNTING MANAGER' and $_SESSION['usr_rol']!='GERENTE DE AREA'){
			$filtro .=" and id_supervisor=".$_SESSION['usr_id'];
		}
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" and pd.id_placexdep in (".$_SESSION['permisos'].") ";
		}
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles u on pd.id_role=u.id_role ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if($dbEx->numrows>0){
			$optE .= '<option value="0">Select a Employee</option>';
			foreach($dtEmp as $dtE){
				$optE .= '<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>'; 
			}
		}
		else{
			$optE .='<option value="0">You do not have employees supervised</option>';
		}
		$sqlText = "select paystub_id, date_format(paystub_delivery, '%d/%m/%Y') as f1 from paystub order by paystub_delivery desc";
		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optDate ='<option value="0">Select a Date</option>';
			foreach($dtPay as $dtP){
				$optDate .= '<option value="'.$dtP['paystub_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		else{
			$optDate .='<option value="0">Paystubs not available</option>';	
		}
		$rslt .= '<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">';
		$rslt .='<tr bgcolor="#8FBC8F"><td colspan="2" align="center"><font color="#FFFFFF">PAY STUBS OF EMPLOYEES</font></td></tr>';
		$rslt .='<tr><td align="right">Select a payment date: </td>';
		$rslt .='<td><select id="lsDate">'.$optDate.'</select></td></tr>';
		$rslt .='<tr><td align="right">Status Employee</td>';
		$rslt .='<td><select id="lsStatus" onchange="loadEmp(this.value)"><option value="1">ACTIVE</option><option value="-1">ALL</option><option value="0">INACTIVE</option></select></td></tr>';
		$rslt .='<tr><td align="right">Select a Employee: </td>';
		$rslt .='<td><span id="lyEmp"><select id="lsEmp">'.$optE.'</select></span></td></tr>';
		
		$rslt .='<tr><td colspan="2" align="center"><input type="button" onclick="loadEmployeesPayStubs()" value="Seach"></td></tr>';
		$rslt .='</table><br><br>';
		$rslt .='<div id="lyData"></div>';
		echo $rslt;
	break;
	
	case 'loadEmployeesPayStubs':

		$rslt = $sFunc->getBoletaPago($_POST['fecha'], $_POST['emp']);
		echo $rslt;
		
	break;
	
	case 'upOtherPay':
		$rslt = cargaPag("../mtto/formOtherPaystub.php");
		$sqlText = "select max(paystub_delivery) as max, paystub_id, date_format(paystub_delivery,'%d/%m/%Y') as f1 from paystub";
		$dtPay = $dbEx->selSql($sqlText);
		$rslt = str_replace("<!--paystub_id-->",$dtPay['0']['paystub_id'],$rslt);
		$rslt = str_replace("<!--fec_delivery-->",$dtPay['0']['f1'],$rslt);
		
		echo $rslt;
	break;
	
	case 'loadEmp':
		$sqlText = "select name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id where e.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A' and user_status=1";
		$dtPl = $dbEx->selSql($sqlText);
		$filtro = "where 1";
		if($_SESSION['usr_rol']!='GERENCIA' and $_SESSION['usr_rol']!='RECURSOS HUMANOS' and $dtPl['0']['name_place']!='ACCOUNTING MANAGER' and $_SESSION['usr_rol']!='GERENTE DE AREA'){
			$filtro .=" and id_supervisor=".$_SESSION['usr_id'];
		}
		if($_SESSION['usr_rol']==""){
			$rslt = -1;
			echo $rslt;
			break;
		}
		if($_POST['idEstado']==1){
			$filtro .=" and user_status=1";	
		}
		else if($_POST['idEstado']==0){
			$filtro .=" and user_status=0";
		}
		$sqlText = "select employee_id, username, firstname, lastname from employees ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if($dbEx->numrows>0){
			$optE .= '<option value="0">Select a Employee</option>';
			foreach($dtEmp as $dtE){
				$optE .= '<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>'; 
			}
		}
		else{
			$optE .='<option value="0">No employees for this selection</option>';
		}
		$rslt = '<select id="lsEmp">'.$optE.'</select>';
		echo $rslt;
		
	break;
	
	case 'payIncidents':
		$rslt = cargaPag("../mtto/filtrosPayIncidente.php");
		$sqlText = "select paystub_id, date_format(paystub_delivery,'%d/%m/%Y') as f1 from paystub order by paystub_delivery desc";
		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optPay = "";
			foreach($dtPay as $dtP){
				$optPay .= '<option value="'.$dtP['paystub_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		$sqlText = "select employee_id, firstname, lastname from employees where user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		foreach($dtEmp as $dtE){
			$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].' '.$dtE['lastname'].'</option>';	
		}
		
		$rslt = str_replace("<!--optPay-->",$optPay,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		echo $rslt;
	break;
	
	case 'formRegIncidents':
		$rslt = cargaPag("../mtto/formRegIncidents.php");
		$idEmp = 0;
		if(strlen($_POST['badge'])>0){
			$sqlText = "select employee_id from employees where username = '".$_POST['badge']."'";
			$dtIdE = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$idEmp = $dtIdE['0']['employee_id'];
			}
		}
		else if($_POST['idEmp']>0){
			$idEmp = $_POST['idEmp'];	
		}
		
		$sqlText = "select ps.disc_id, ps.disc_label, pa.disc_attributename ".
			"from pay_discount_setup ps, pay_discount_attr pa ".
			"where ps.disc_attributeid = pa.disc_attributeid ".
			"and ifnull(ps.disc_end_date,sysdate() + 1) > sysdate() ".
    		"order by ps.disc_label ";
    		
		$dtDesc = $dbEx->selSql($sqlText);

		
		$sqlText = "select * from paystubxemp where employee_id=".$idEmp." and paystub_id=".$_POST['idPay'];
		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
		
	  		$tblDisc = '';
			$totalDiscounts = 0;
			$incTotalDescuentos = 0;

			if($dbEx->numrows>0){
				foreach($dtDesc as $dtD){
                    $sqlIncLabel = "select ifnull(".$dtD['disc_attributename'].",0.0) attribute from paystub_incidents where payxemp_id=".$dtPay['0']['PAYXEMP_ID'];
                    $dtLbInc = $dbEx->selSql($sqlIncLabel);
                    $attrInc = 0;
                    if($dbEx->numrows>0){
						$attrInc = $dtLbInc['0']['attribute'];
						$incTotalDescuentos = $incTotalDescuentos + $attrInc;
					}
					$attrInc = number_format($attrInc,2);

					$sqlLabel = "select format(ifnull(".$dtD['disc_attributename'].",0),2) attribute, '".$dtD['disc_label']."' label, ".
					    " format(((ifnull(attribute1,0)) + ".
						" ifnull((select ".$dtD['disc_attributename']." from paystub_incidents where payxemp_id=".$dtPay['0']['PAYXEMP_ID']."),0)),2) total_attr ".
						"from paystubxemp where employee_id=".$idEmp." and paystub_id=".$_POST['idPay'];
						
					$dtLabel = $dbEx->selSql($sqlLabel);
					if($dbEx->numrows>0){
					    $totalDiscounts = $totalDiscounts + $dtLabel['0']['attribute'];
					
						$tblDisc .= '<tr><td>'.$dtLabel['0']['label'].'</td><td>'.$dtLabel['0']['attribute'].'</td>'.
							'<td><input type="text" id="txt'.$dtD['disc_attributename'].'" value="'.$attrInc.'"/></td><td>'.$dtLabel['0']['total_attr'].'</td></tr>';
					}
					
				}

				//Descuentos de ley
				$sqlText = "select ld.legaldisc_name, round(pd.amount,2) amount, pd.pydisc_id, pd.legaldisc_id, ld.taxable_remunation ".
					"from paystub_legaldisc pd inner join legal_discount ld ".
						"on pd.legaldisc_id = ld.legaldisc_id ".
						"inner join paystubxemp pe on pe.payxemp_id = pd.payxemp_id ".
						"inner join employees e on pe.employee_id = e.employee_id ".
					"where ld.geography_code = e.geography_code ".
					"and pd.payxemp_id = ".$dtPay['0']['PAYXEMP_ID']." ".
					"union all ".
					"select ld.legaldisc_name, round(0,2) amount, 0 pydisc_id, ld.legaldisc_id, ld.taxable_remunation  ".
					"from legal_discount ld ".
					"where ld.legaldisc_id not in ( ".
						"select pd.legaldisc_id ".
					    "from paystub_legaldisc pd ".
						    "where pd.payxemp_id = ".$dtPay['0']['PAYXEMP_ID'].") ".
						    "and ld.geography_code = (".
						    	"select e.geography_code ".
						    	"from paystubxemp pe inner join employees e on pe.employee_id = e.employee_id ".
						    	"where pe.payxemp_id = ".$dtPay['0']['PAYXEMP_ID'].") ".
						"order by 5 asc, 4 asc";

				$dtLDesc = $dbEx->selSql($sqlText);
				$tblLD = '';
				foreach ($dtLDesc as $dtLD) {
					$tblLD .= '<tr><td>'.$dtLD['legaldisc_name'].'</td><td>'.$dtLD['amount'].'</td><td></td><td>'.$dtLD['amount'].'</td></tr>';
				}

				$sqlText = "select round(sum(amount),2) total_deducciones ".
							"from paystub_legaldisc where payxemp_id = ".$dtPay['0']['PAYXEMP_ID'];
				$dtDeduc = $dbEx->selSql($sqlText);
				$totalDeductions = 0;
				if($dbEx->numrows>0){
					$totalDeductions = $dtDeduc['0']['total_deducciones'];
				}
				
			}
		    $rslt = str_replace("<!--dataDescuentos-->",$tblDisc,$rslt);
		    $rslt = str_replace("<!--DescuentosLey-->",$tblLD,$rslt);
			$rslt = str_replace("<!--payxemp_id-->",$dtPay['0']['PAYXEMP_ID'],$rslt);
			$rslt = str_replace("<!--employee_id-->",$dtPay['0']['EMPLOYEE_ID'],$rslt);
			$rslt = str_replace("<!--paystub_id-->",$dtPay['0']['PAYSTUB_ID'],$rslt);
			$rslt = str_replace("<!--payxemp_nhoras-->",number_format($dtPay['0']['PAYXEMP_NHORAS'],2),$rslt);
			$rslt = str_replace("<!--payxemp_salary-->",number_format($dtPay['0']['PAYXEMP_SALARY'],2),$rslt);
			$rslt = str_replace("<!--payxemp_nadditionalhours-->",number_format($dtPay['0']['PAYXEMP_NADDITIONALHOURS'],2),$rslt);
			$rslt = str_replace("<!--payxemp_additionalhours-->",number_format($dtPay['0']['PAYXEMP_ADDITIONALHOURS'],2),$rslt);
			$rslt = str_replace("<!--payxemp_salarydisc-->",number_format($dtPay['0']['PAYXEMP_SALARYDISC'],2),$rslt);
			$rslt = str_replace("<!--payxemp_seventh-->",number_format($dtPay['0']['PAYXEMP_SEVENTH'],2),$rslt);
			$rslt = str_replace("<!--payxemp_nhorasnoct-->",number_format($dtPay['0']['PAYXEMP_NHORASNOCT'],2),$rslt);
			$rslt = str_replace("<!--payxemp_horasnoct-->",number_format($dtPay['0']['PAYXEMP_HORASNOCT'],2),$rslt);
			$rslt = str_replace("<!--payxemp_notdiurnal-->",number_format($dtPay['0']['PAYXEMP_NOTDIURNAL'],2),$rslt);
			$rslt = str_replace("<!--payxemp_otdiurnal-->",number_format($dtPay['0']['PAYXEMP_OTDIURNAL'],2),$rslt);
			$rslt = str_replace("<!--payxemp_notnoct-->",number_format($dtPay['0']['PAYXEMP_NOTNOCT'],2),$rslt);
			$rslt = str_replace("<!--payxemp_otnoct-->",number_format($dtPay['0']['PAYXEMP_OTNOCT'],2),$rslt);
			$rslt = str_replace("<!--payxemp_bono-->",number_format($dtPay['0']['PAYXEMP_BONO'],2),$rslt);
			$rslt = str_replace("<!--payxemp_aguinaldo-->",number_format($dtPay['0']['PAYXEMP_AGUINALDO'],2),$rslt);
			$rslt = str_replace("<!--payxemp_vacation-->",number_format($dtPay['0']['PAYXEMP_VACATION'],2),$rslt);
			$rslt = str_replace("<!--payxemp_severance-->",number_format($dtPay['0']['PAYXEMP_SEVERANCE'],2),$rslt);
			$rslt = str_replace("<!--payxemp_otherincome-->",number_format($dtPay['0']['PAYXEMP_OTHERINCOME'],2),$rslt);
			$rslt = str_replace("<!--payxemp_liquid-->",number_format($dtPay['0']['PAYXEMP_LIQUID'],2),$rslt);
			$rslt = str_replace("<!--payxemp_note-->",$dtPay['0']['PAYXEMP_NOTE'],$rslt);
			
			$totalIncome = $dtPay['0']['PAYXEMP_OTHERINCOME'] + 
							$dtPay['0']['PAYXEMP_BONO'] + 
							$dtPay['0']['PAYXEMP_VACATION'] + 
							$dtPay['0']['PAYXEMP_AGUINALDO'] + 
							$dtPay['0']['PAYXEMP_OTNOCT'] + 
							$dtPay['0']['PAYXEMP_OTDIURNAL'] + 
							$dtPay['0']['PAYXEMP_HORASNOCT'] + 
							$dtPay['0']['PAYXEMP_SALARY'] + 
							$dtPay['0']['PAYXEMP_ADDITIONALHOURS'] + 
							$dtPay['0']['PAYXEMP_SEVERANCE'];

			$totalDiscounts	= $totalDiscounts +$dtPay['0']['PAYXEMP_SALARYDISC'] + $dtPay['0']['PAYXEMP_SEVENTH'];
				
			
			
			$rslt = str_replace("<!--totalIngresos-->",number_format($totalIncome,2),$rslt);
			$rslt = str_replace("<!--totalDeducciones-->",number_format($totalDeductions,2),$rslt);
			$rslt = str_replace("<!--totalDescuentos-->",number_format($totalDiscounts,2),$rslt);
			
			$sqlText = "select firstname, lastname, username from employees where employee_id=".$dtPay['0']['EMPLOYEE_ID'];
			$dtEmp = $dbEx->selSql($sqlText);
			$rslt = str_replace("<!--nomb_emp-->",$dtEmp['0']['lastname'].', '.$dtEmp['0']['firstname'],$rslt);
			$rslt = str_replace("<!--badge-->",$dtEmp['0']['username'],$rslt);
			
			$sqlText = "select date_format(paystub_ini,'%d/%m/%Y') as f1, date_format(paystub_fin,'%d/%m/%Y') as f2, date_format(paystub_delivery,'%d/%m/%Y') as f3 from paystub where paystub_id=".$_POST['idPay'];
			$dtFecha = $dbEx->selSql($sqlText);
			
			$rslt = str_replace("<!--fec_ini-->",$dtFecha['0']['f1'],$rslt);
			$rslt = str_replace("<!--fec_fin-->",$dtFecha['0']['f2'],$rslt);
			$rslt = str_replace("<!--fec_entr-->",$dtFecha['0']['f3'],$rslt);
			
			$sqlText = "select * from paystub_incidents where payxemp_id=".$dtPay['0']['PAYXEMP_ID'];
			$dtInc = $dbEx->selSql($sqlText);
			$incId = 0;
			$incNhoras = 0;
			$incSalary = 0;
			$incNaddHoras = 0;
			$incAddHoras = 0;
			$incSalaryDisc = 0;
			$incSeventh = 0;
			$incNhnoct = 0;
			$incHnoct = 0;
			$incNotdia = 0;
			$incOtdia = 0;
			$incNotnoct = 0;
			$incOtnoct = 0;
			$incBono = 0;
			$incAguinaldo = 0;
			$incVacacion = 0;
			$incSeverance = 0;
			$incOtherIncome = 0;
			$incRecibir = 0;
			$incTotalIngresos = 0;
			$incTotalDeducciones = 0;
			
			
			if($dbEx->numrows>0){
				$incId = $dtInc['0']['PAYINC_ID'];
				$incNhoras = $dtInc['0']['PAYINC_NHORAS'];
				$incSalary = $dtInc['0']['PAYINC_SALARY'];;
				$incNaddHoras = $dtInc['0']['PAYINC_NADDITIONALHOURS'];
				$incAddHoras = $dtInc['0']['PAYINC_ADDITIONALHOURS'];
				$incSalaryDisc = $dtInc['0']['PAYINC_SALARYDISC'];
				$incSeventh = $dtInc['0']['PAYINC_SEVENTH'];
				$incNhnoct = $dtInc['0']['payIdNC_NHORASNOCT'];
				$incHnoct =$dtInc['0']['PAYINC_HORASNOCT'];
				$incNotdia = $dtInc['0']['PAYINC_NOTDIURNAL'];
				$incOtdia = $dtInc['0']['PAYINC_OTDIURNAL'];
				$incNotnoct = $dtInc['0']['PAYINC_NOTNOCT'];
				$incOtnoct = $dtInc['0']['PAYINC_OTNOCT'];
				$incBono = $dtInc['0']['PAYINC_BONO'];
				$incAguinaldo = $dtInc['0']['PAYINC_AGUINALDO'];
				$incVacacion = $dtInc['0']['PAYINC_VACATION'];
				$incSeverance = $dtInc['0']['PAYINC_SEVERANCE'];
				$incOtherIncome = $dtInc['0']['PAYINC_OTHERINCOME'];
				
				$incRecibir = $dtInc['0']['PAYINC_RECEIVED'];
				$incTotalIngresos = $incOtherIncome + $incBono  + $incVacacion + $incAguinaldo + $incOtnoct + $incOtdia + $incHnoct + $incSalary + $incAddHoras + $incSeverance;
				 
				$incTotalDescuentos = $incTotalDescuentos + $incSalaryDisc + $incSeventh;
			
			}
			//Datos de Incidencias por si existe ese dato
			$rslt = str_replace("<!--inc_id-->",$incId,$rslt);
			$rslt = str_replace("<!--inc_nhoras-->",number_format($incNhoras,2),$rslt);
			$rslt = str_replace("<!--inc_salary-->",number_format($incSalary,2),$rslt);
			$rslt = str_replace("<!--inc_naddhoras-->",number_format($incNaddHoras,2),$rslt);
			$rslt = str_replace("<!--inc_addhoras-->",number_format($incAddHoras,2),$rslt);
			$rslt = str_replace("<!--inc_salarydisc-->",number_format($incSalaryDisc,2),$rslt);
			$rslt = str_replace("<!--inc_seventh-->",number_format($incSeventh,2),$rslt);
			$rslt = str_replace("<!--inc_nhnoct-->",number_format($incNhnoct,2),$rslt);
			$rslt = str_replace("<!--inc_hnoct-->",number_format($incHnoct,2),$rslt);
			$rslt = str_replace("<!--inc_notdia-->",number_format($incNotdia,2),$rslt);
			$rslt = str_replace("<!--inc_otdia-->",number_format($incOtdia,2),$rslt);
			$rslt = str_replace("<!--inc_notnoct-->",number_format($incNotnoct,2),$rslt);
			$rslt = str_replace("<!--inc_otnoct-->",number_format($incOtnoct,2),$rslt);
			$rslt = str_replace("<!--inc_bono-->",number_format($incBono,2),$rslt);
			$rslt = str_replace("<!--inc_aguinaldo-->",number_format($incAguinaldo,2),$rslt);
			$rslt = str_replace("<!--inc_vacacion-->",number_format($incVacacion,2),$rslt);
			$rslt = str_replace("<!--inc_severance-->",number_format($incSeverance,2),$rslt);
			$rslt = str_replace("<!--inc_otherincome-->",number_format($incOtherIncome,2),$rslt);
			$rslt = str_replace("<!--inc_totalingresos-->",number_format($incTotalIngresos,2),$rslt);
			$rslt = str_replace("<!--inc_recibir-->",number_format($incRecibir,2),$rslt);
			$rslt = str_replace("<!--inc_totalDescuentos-->",number_format($incTotalDescuentos,2),$rslt);
			
			$rslt = str_replace("<!--total_nhoras-->",number_format($dtPay['0']['PAYXEMP_NHORAS'] + $incNhoras,2),$rslt);
			$rslt = str_replace("<!--total_salary-->",number_format($dtPay['0']['PAYXEMP_SALARY'] + $incSalary,2),$rslt);
			$rslt = str_replace("<!--total_naddhoras-->",number_format($dtPay['0']['PAYXEMP_NADDITIONALHOURS'] + $incNaddHoras,2),$rslt);
			$rslt = str_replace("<!--total_addhoras-->", number_format($dtPay['0']['PAYXEMP_ADDITIONALHOURS'] + $incAddHoras,2),$rslt);
			$rslt = str_replace("<!--total_salarydisc-->", number_format($dtPay['0']['PAYXEMP_SALARYDISC'] + $incSalaryDisc,2),$rslt);
			$rslt = str_replace("<!--total_seventh-->", number_format($dtPay['0']['PAYXEMP_SEVENTH'] + $incSeventh,2),$rslt);
			$rslt = str_replace("<!--total_nhorasnoct-->", number_format($dtPay['0']['PAYXEMP_NHORASNOCT'] + $incNhnoct,2),$rslt);	
			$rslt = str_replace("<!--total_horasnoct-->", number_format($dtPay['0']['PAYXEMP_HORASNOCT'] + $incHnoct,2),$rslt);
			$rslt = str_replace("<!--total_notdiurnal-->",number_format($dtPay['0']['PAYXEMP_NOTDIURNAL'] + $incNotdia,2),$rslt);
			$rslt = str_replace("<!--total_otdiurnal-->",number_format($dtPay['0']['PAYXEMP_OTDIURNAL'] + $incOtdia,2),$rslt);
			$rslt = str_replace("<!--total_notnoct-->",number_format($dtPay['0']['PAYXEMP_NOTNOCT'] + $incNotnoct,2),$rslt);
			$rslt = str_replace("<!--total_otnoct-->",number_format($dtPay['0']['PAYXEMP_OTNOCT'] + $incOtnoct,2),$rslt);
			$rslt = str_replace("<!--total_bono-->",number_format($dtPay['0']['PAYXEMP_BONO'] + $incBono,2),$rslt);
			$rslt = str_replace("<!--total_aguinaldo-->", number_format($dtPay['0']['PAYXEMP_AGUINALDO'] + $incAguinaldo,2),$rslt);
			$rslt = str_replace("<!--total_vacation-->",number_format($dtPay['0']['PAYXEMP_VACATION'] + $incVacacion,2),$rslt);
			$rslt = str_replace("<!--total_severance-->",number_format($dtPay['0']['PAYXEMP_SEVERANCE'] + $incSeverance,2),$rslt);
			$rslt = str_replace("<!--total_otherincome-->",number_format($dtPay['0']['PAYXEMP_OTHERINCOME'] + $incOtherIncome,2),$rslt );
			$rslt = str_replace("<!--total_recibir-->", number_format($dtPay['0']['PAYXEMP_LIQUID'] + $incRecibir,2),$rslt);
			$rslt = str_replace("<!--total_totalingresos-->",number_format($totalIncome + $incTotalIngresos,2),$rslt);
			$rslt = str_replace("<!--total_totaldescuentos-->",number_format($totalDiscounts + $incTotalDescuentos,2),$rslt);
		}
		else{
			$rslt = "Information not found";
		}
			
		echo $rslt;
	break;
	
	case 'CalcIncidences':
		$sqlText = "select p.*, ifnull(attribute1,0) attribute1, ifnull(attribute2,0) attribute2, ".
			" ifnull(attribute3,0) attribute3, ".
		    " ifnull(attribute4,0) attribute4, ifnull(attribute5,0) attribute5, ifnull(attribute6,0) attribute6, ".
			" ifnull(attribute7,0) attribute7, ifnull(attribute8,0) attribute8, ifnull(attribute9,0) attribute9, ".
			" ifnull(attribute10,0) attribute10, ifnull(attribute11,0) attribute11, ".
			" ifnull(attribute12,0) attribute12, ".
			" ifnull(attribute13,0) attribute13, ifnull(attribute14,0) attribute14, ".
			" ifnull(attribute15,0) attribute15, ".
			" ifnull(attribute16,0) attribute16, ifnull(attribute17,0) attribute17, ".
			" ifnull(attribute18,0) attribute18, ".
			" ifnull(attribute19,0) attribute19, ifnull(attribute20,0) attribute20, ".
			" paystub_ini, paystub_fin ".
			" from paystubxemp p  inner join paystub py on p.paystub_id = py.paystub_id ".
			" where payxemp_id=".$_POST['payxemp_id'];
		$dtPay = $dbEx->selSql($sqlText);


		$valorHora = $sFunc->getSalarioxHora($dtPay['0']['EMPLOYEE_ID'],
										$dtPay['0']['paystub_ini'],
										$dtPay['0']['paystub_fin']);

		$nHoras = $dtPay['0']['PAYXEMP_NHORAS'] + $_POST['nhoras'];
		$nAdditionalHours = $dtPay['0']['PAYXEMP_NADDITIONALHOURS'] + $_POST['nadditionalhours'];
		$nhorasnoct = $dtPay['0']['PAYXEMP_NHORASNOCT'] + $_POST['nhorasnoct'];
		$notdiurnal = $dtPay['0']['PAYXEMP_NOTDIURNAL'] + $_POST['notdiurnal'];
		$notnoct = $dtPay['0']['PAYXEMP_NOTNOCT'] + $_POST['notnoct'];
		$bono = $dtPay['0']['PAYXEMP_BONO'] + $_POST['bono'];
		$aguinaldo = $dtPay['0']['PAYXEMP_AGUINALDO'] + $_POST['aguinaldo'];
		$vacation = $dtPay['0']['PAYXEMP_VACATION'] + $_POST['vacation'];
		$otherincome = $dtPay['0']['PAYXEMP_OTHERINCOME'] + $_POST['otherincome'];
		$severance = $dtPay['0']['PAYXEMP_SEVERANCE'] + $_POST['severance'];
		$salaryDisc = $dtPay['0']['PAYXEMP_SALARYDISC'] + $_POST['salarydisc'];
		$seventh = $dtPay['0']['PAYXEMP_SEVENTH'] + $_POST['seventh'];

		$salario = $nHoras * $valorHora;
		$dineroHorasAdicionales = $nAdditionalHours * $valorHora;
		$dineroNoct = $nhorasnoct * $valorHora *1.25;
		$dineroExtraDia = $valorHora  * $notdiurnal * 2;
		$dineroExtraNoct = $valorHora   * $notnoct * 2.5;
		$totalIncome = $dtPay['0']['PAYXEMP_SALARY'] +
						$dtPay['0']['PAYXEMP_ADDITIONALHOURS'] +
						$dtPay['0']['PAYXEMP_HORASNOCT'] +
						$dtPay['0']['PAYXEMP_OTDIURNAL'] + 
						$dtPay['0']['PAYXEMP_OTNOCT'] + 
						$dtPay['0']['PAYXEMP_BONO'] + 
						$dtPay['0']['PAYXEMP_AGUINALDO'] +
						$dtPay['0']['PAYXEMP_VACATION'] +  
						$dtPay['0']['PAYXEMP_OTHERINCOME'] + 
						$dtPay['0']['PAYXEMP_SEVERANCE']; 
		
		$totalIngresos = ($nHoras * $valorHora) +
						($nAdditionalHours * $valorHora) +
						($nhorasnoct * $valorHora * 1.25) + 
						($notdiurnal * $valorHora * 2) + 
						($notnoct * $valorHora * 2.5) + 
						$bono + $aguinaldo + $vacation + $otherincome + $severance;	
		
		$DescArray = array();
		$row = 0;
		$descuentos = 0;
		$totalDescuentos = 0;
		//Obtencion de los descuentos mas los incidentes
		$sqlText = "select ps.disc_id, ps.disc_label, pa.disc_attributename ".
				"from pay_discount_setup ps, pay_discount_attr pa ".
				"where ps.disc_attributeid = pa.disc_attributeid ".
				"and ifnull(ps.disc_end_date,sysdate() + 1) > sysdate() ".
    			"order by ps.disc_label";
    			
		$dtDesc = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			foreach($dtDesc as $dtD){
                $DescArray[$row]['attribute'] = $dtD['disc_attributename'];
                $DescArray[$row]['valor'] = $_POST[$dtD['disc_attributename']];
                $descuentos = $descuentos + $dtPay['0'][$dtD['disc_attributename']];
				$totalDescuentos = $totalDescuentos + $_POST[$dtD['disc_attributename']] + $dtPay['0'][$dtD['disc_attributename']];
				$row++;
			}
		}
		$descuentos = $descuentos + $dtPay['0']['PAYXEMP_SALARYDISC'] + $dtPay['0']['PAYXEMP_SEVENTH'];
		$totalDescuentos = $totalDescuentos + $salaryDisc + $seventh;

		//Calcular deducciones
		$sqlText = "select ld.legaldisc_name, round(pd.amount,2) amount, pd.pydisc_id, pd.legaldisc_id, ".
						"ld.taxable_remunation, ".
						"ld.percentage, ".
						"ld.botton_amount, ". 
						"ld.top_amount, ".
						"ld.over_excess, ". 
						"ld.fixed_fee, ". 
						"ld.pension_flag, ".
						"ld.max_quotable ".
					"from paystub_legaldisc pd inner join legal_discount ld on pd.legaldisc_id = ld.legaldisc_id ".
						"inner join employees e on ld.geography_code = e.geography_code ".
					 "where pd.payxemp_id = ".$dtPay['0']['PAYXEMP_ID']." ".
					 "and ifnull(ld.end_date,date(sysdate())) <= date(sysdate()) ".
					 "and e.employee_id = ".$dtPay['0']['EMPLOYEE_ID']." ".
					"union all ".
					"select ld.legaldisc_name, round(0,2) amount, 0 pydisc_id, ld.legaldisc_id, ".
						"ld.taxable_remunation, ".
						"ld.percentage, ".
						"ld.botton_amount, ". 
						"ld.top_amount, ".
						"ld.over_excess, ". 
						"ld.fixed_fee, ".
						"ld.pension_flag, ".
						"ld.max_quotable ".
					"from legal_discount ld inner join employees e on ld.geography_code = e.geography_code ".
					"where ld.legaldisc_id not in ( ".
						"select pd.legaldisc_id ".  
					    "from paystub_legaldisc pd ".
					    "where pd.payxemp_id = ".$dtPay['0']['PAYXEMP_ID']." ".
					") ".
					"and ifnull(ld.end_date,date(sysdate())) <= date(sysdate()) ".
					"and e.employee_id = ".$dtPay['0']['EMPLOYEE_ID']." ".
					"order by 5 asc, 4 asc";

		$dtDescLey = $dbEx->selSql($sqlText);
		$DescGravados = 0;
		$DescPostGravamen = 0;
		$totalDeducPre = 0;
		$tblDeduc = "";

		foreach ($dtDescLey as $dtDL) {
			$montoDesc = 0;
			if($dtDL['taxable_remunation'] == 'N'){
				$montoDesc = $sFunc->getMontoDesc(
								$totalIngresos,
								$dtDL['botton_amount'],
								$dtDL['top_amount'],
								$dtDL['percentage'],
								$dtDL['over_excess'],
								$dtDL['fixed_fee'],
								$dtDL['max_quotable']);

				$totalDeducPre = $totalDeducPre + $dtDL['amount'];
				$DescGravados = $DescGravados + $montoDesc;
				//Imprimir linea de deducciones
				$tblDeduc .= '<tr><td>'.$dtDL['legaldisc_name'].'</td>'.
							'<td>'.$dtDL['amount'].'</td><td></td>'.
							'<td>'.number_format($montoDesc,2).'</td></tr>';

			}elseif($dtDL['taxable_remunation'] == 'Y'){
				$montoDesc = $sFunc->getMontoDesc(
								$totalIngresos - $DescGravados,
								$dtDL['botton_amount'],
								$dtDL['top_amount'],
								$dtDL['percentage'],
								$dtDL['over_excess'],
								$dtDL['fixed_fee'],
								$dtDL['max_quotable']);

				$totalDeducPre = $totalDeducPre + $dtDL['amount'];
				$DescPostGravamen = $DescPostGravamen + $montoDesc;
				// Imprimir descuento
				$tblDeduc .= '<tr><td>'.$dtDL['legaldisc_name'].'</td>'.
							'<td>'.$dtDL['amount'].'</td><td></td>'.
							'<td>'.number_format($montoDesc,2).'</td></tr>';

			}
		}

		$totalDeducciones = $DescGravados + $DescPostGravamen;
		$difTotalDeducciones = $totalDeducciones - $totalDeducPre;
		$TotalRecibir = $totalIngresos - $totalDescuentos - $totalDeducciones;

		//Labels de descuentos 
		$sqlText = "select ps.disc_id, ps.disc_label, pa.disc_attributename ".
			"from pay_discount_setup ps, pay_discount_attr pa ".
			"where ps.disc_attributeid = pa.disc_attributeid ".
			"and ifnull(ps.disc_end_date,sysdate() + 1) > sysdate() ".
    		"order by ps.disc_label ";

		$dtDesc = $dbEx->selSql($sqlText);
		 if($dbEx->numrows>0){
				foreach($dtDesc as $dtD){
					$AttrInc = 0;
                    for($i=0; $i<$row; $i++){
						if($DescArray[$i]['attribute'] == $dtD['disc_attributename']){
                            $AttrInc = $DescArray[$i]['valor'];
						}
	 				}

					$sqlLabel = "select format(ifnull(".$dtD['disc_attributename'].",0),2) attribute, '".$dtD['disc_label']."' label, ".
					    " format(((ifnull(attribute1,0)) + ".
						" ifnull((select ".$dtD['disc_attributename']." from paystub_incidents where payxemp_id=".$dtPay['0']['PAYXEMP_ID']."),0)),2) total_attr ".
						"from paystubxemp where employee_id=".$dtPay['0']['EMPLOYEE_ID']." and paystub_id=".$dtPay['0']['PAYSTUB_ID'];

					$dtLabel = $dbEx->selSql($sqlLabel);
					if($dbEx->numrows>0){
					    $totalAttr = $AttrInc + $dtLabel['0']['attribute'];
						$tblDisc .= '<tr><td>'.$dtLabel['0']['label'].'</td><td>'.$dtLabel['0']['attribute'].'</td>'.
							'<td><input type="text" id="txt'.$dtD['disc_attributename'].'" value="'.number_format($AttrInc,2).'"/></td><td>'.number_format($totalAttr,2).'</td></tr>';
					}
			}
		}

		
		$rslt = "";

        $rslt .='<table width="100%" align="center"  border="1" cellpadding="5" cellspacing="0">';
		$rslt .='<tr><td>Info</td><td>Valor</td><td>Incidente</td><td>Valor con variaciones</td></tr>';
		$rslt .='<tr><td>Horas totales</td><td>'.number_format($dtPay['0']['PAYXEMP_NHORAS'],2).'</td>';
		$rslt .='<td><input type="text" id="txtnhoras" value="'.$_POST['nhoras'].'"/></td><td>'.number_format($nHoras,2).'</td></tr>';
		$rslt .='<tr><td>Salario base</td><td>'.number_format($dtPay['0']['PAYXEMP_SALARY'],2).'</td>';
		$rslt .='<td><input type="text" id="txtsalary" disabled="disabled" value="'.number_format($salario - $dtPay['0']['PAYXEMP_SALARY'],2).'"/></td><td>'.number_format($salario,2).'</td></tr>';
		$rslt .='<tr><td># Horas adicionales</td><td>'.number_format($dtPay['0']['PAYXEMP_NADDITIONALHOURS'],2).'</td>';
		$rslt .='<td><input type="text" id="txtnadditionalhours" value="'.$_POST['nadditionalhours'].'"/></td><td>'.number_format($nAdditionalHours,2).'</td></tr>';
		$rslt .='<tr><td>$ Horas adicionales</td><td>'.number_format($dtPay['0']['PAYXEMP_ADDITIONALHOURS'],2).'</td>';
		$rslt .='<td><input type="text" id="txtadditionalhours" disabled="disabled" value="'.number_format($dineroHorasAdicionales - $dtPay['0']['PAYXEMP_ADDITIONALHOURS'],2).'"/></td><td>'.number_format($dineroHorasAdicionales,2).'</td></tr>';
		$rslt .='<tr><td># Horas nocturnas</td><td>'.number_format($dtPay['0']['PAYXEMP_NHORASNOCT'],2).'</td>';
		$rslt .='<td><input type="text" id="txtnhorasnoct" value="'.$_POST['nhorasnoct'].'"/></td><td>'.number_format($nhorasnoct,2).'</td></tr>';
		$rslt .='<tr><td>$ Horas nocturnas</td><td>'.number_format($dtPay['0']['PAYXEMP_HORASNOCT'],2).'</td>';
		$rslt .='<td><input type="text" id="txthorasnoct" disabled="disabled" value="'.number_format($dineroNoct - $dtPay['0']['PAYXEMP_HORASNOCT'],2).'"/></td><td>'.number_format($dineroNoct,2).'</td></tr>';
		$rslt .='<tr><td># Extras diurnas</td><td>'.number_format($dtPay['0']['PAYXEMP_NOTDIURNAL'],2).'</td>';
		$rslt .='<td><input type="text" id="txtnotdiurnal" value="'.$_POST['notdiurnal'].'"/></td><td>'.number_format($notdiurnal,2).'</td></tr>';
		$rslt .='<tr><td>$ Extras diurnas</td><td>'.number_format($dtPay['0']['PAYXEMP_OTDIURNAL'],2).'</td>';
		$rslt .='<td><input type="text" id="txtotdiurnal" disabled="disabled" value="'.number_format($dineroExtraDia - $dtPay['0']['PAYXEMP_OTDIURNAL'],2).'"/></td><td>'.number_format($dineroExtraDia,2).'</td></tr>';
		$rslt .='<tr><td># Extras nocturnas</td><td>'.number_format($dtPay['0']['PAYXEMP_NOTNOCT'],2).'</td>';
		$rslt .='<td><input type="text" id="txtnotnoct" value="'.$_POST['notnoct'].'"/></td><td>'.number_format($notnoct,2).'</td></tr>';
		$rslt .='<tr><td>$ Extras nocturnas</td><td>'.number_format($dtPay['0']['PAYXEMP_OTNOCT'],2).'</td>';
		$rslt .='<td><input type="text" id="txtotnoct" disabled="disabled" value="'.number_format($dineroExtraNoct -$dtPay['0']['PAYXEMP_OTNOCT'],2).'"/></td><td>'.number_format($dineroExtraNoct,2).'</td></tr>';
		$rslt .='<tr><td>Bono</td><td>'.number_format($dtPay['0']['PAYXEMP_BONO'],2).'</td>';
		$rslt .='<td><input type="text" id="txtbono" value="'.$_POST['bono'].'"/></td><td>'.number_format($bono,2).'</td></tr>';
		$rslt .='<tr><td>Aguinaldo</td><td>'.number_format($dtPay['0']['PAYXEMP_AGUINALDO'],2).'</td>';
		$rslt .='<td><input type="text" id="txtaguinaldo" value="'.$_POST['aguinaldo'].'"/></td><td>'.number_format($aguinaldo,2).'</td></tr>';
		$rslt .='<tr><td>Vacaci&oacute;n</td><td>'.number_format($dtPay['0']['PAYXEMP_VACATION'],2).'</td>';
		$rslt .='<td><input type="text" id="txtvacation" value="'.$_POST['vacation'].'"/></td><td>'.number_format($dineroVacacion,2).'</td></tr>';
		$rslt .='<tr><td>Otros ingresos</td><td>'.number_format($dtPay['0']['PAYXEMP_OTHERINCOME'],2).'</td>';
		$rslt .='<td><input type="text" id="txtotherincome" value="'.$_POST['otherincome'].'"/></td><td>'.number_format($otherincome,2).'</td></tr>';
		$rslt .='<tr><td>Indemnizaci&oacute;n</td><td>'.number_format($dtPay['0']['PAYXEMP_SEVERANCE'],2).'</td>';
		$rslt .='<td><input type="text" id="txtseverance" value="'.$_POST['severance'].'"/></td><td>'.number_format($severance,2).'</td></tr>';
		$rslt .='<tr><td>Total de ingresos</td><td>'.number_format($totalIncome,2).'</td>';
		$rslt .='<td><input type="text" id="txtTotalIncome" disabled="disabled" value="'.number_format($totalIngresos - $totalIncome,2).'"/></td><td><input type="text" id="txtTotalIngresos" disabled="disabled" value="'.number_format($totalIngresos,2).'"/></td></tr>';
		$rslt .= $tblDeduc;

		$rslt .='<tr><td>Total deducciones</td><td>'.number_format($totalDeducPre,2).'</td>';
		$rslt .='<td>'.number_format($totalDeducciones - $totalDeducPre,2).'</td><td>'.number_format($totalDeducciones,2).'</td></tr>';
		$rslt .= $tblDisc;
		$rslt .='<tr><td>$ Descuentos salariales</td><td>'.number_format($dtPay['0']['PAYXEMP_SALARYDISC'],2).'</td>';
		$rslt .='<td><input type="text" id="txtsalarydisc" value="'.$_POST['salarydisc'].'"/></td><td>'.number_format($salaryDisc,2).'</td></tr>';
		$rslt .='<tr><td>$ S&eacute;ptimo</td><td>'.number_format($dtPay['0']['PAYXEMP_SEVENTH'],2).'</td>';
		$rslt .='<td><input type="text" id="txtseventh" value="'.$_POST['seventh'].'"/></td><td>'.number_format($seventh,2).'</td></tr>';
		$rslt .='<tr><td>Total descuentos</td><td>'.number_format($descuentos,2).'</td>';
		$rslt .='<td><input type="text" id="txtTotalDescuentos" disabled="disabled" value="'.number_format($difTotalDescuentos,2).'"/></td><td>'.number_format($totalDescuentos,2).'</td></tr>';
        
		$rslt .='<tr><td>Pago a recibir</td><td>'.number_format($dtPay['0']['PAYXEMP_LIQUID'],2).'</td>';
		$rslt .='<td><input type="text" id="txtliquid" disabled="disabled" value="'.number_format($TotalRecibir - $dtPay['0']['PAYXEMP_LIQUID'],2).'"/></td><td>'.number_format($TotalRecibir,2).'</td></tr>';
		$rslt .='</table>';
		
		echo $rslt;
		
	break;
	
	case 'SaveIncidence':
		if($_POST['payxemp_id']>0){
			$sqlText = "select payxemp_liquid, employee_id from paystubxemp where payxemp_id=".$_POST['payxemp_id'];
			$dtPay = $dbEx->selSql($sqlText);
			if($dtPay['0']['payxemp_liquid']<0){
				$sqlText = "update paystubxemp set payxemp_liquid='0' where payxemp_id=".$_POST['payxemp_id'];	
				$dbEx->updSql($sqlText);
			}
			
			$sqlText = "select payinc_id from paystub_incidents where payxemp_id=".$_POST['payxemp_id'];
			$dtIncExist = $dbEx->selSql($sqlText);
			$incExist = 0;
			if($dbEx->numrows>0){
				$incExist = $dtIncExist['0']['payinc_id'];
			}
			
			if($incExist>0){
				$sqlText = 
				"update paystub_incidents set payinc_date=now(), ".
				"payinc_nhoras='".$_POST['nhoras']."', ".
				"payinc_salary='".$_POST['salary']."', ".
				"payinc_nadditionalhours='".$_POST['nadditionalhours']."', ".
				"payinc_additionalhours ='".$_POST['additionalhours']."', ".
				"payinc_salarydisc='".$_POST['salarydisc']."', ".
				"payinc_seventh='".$_POST['seventh']."', ".
				"payinc_nhorasnoct='".$_POST['nhorasnoct']."', ".
				"payinc_horasnoct= '".$_POST['horasnoct']."', ".
				"payinc_notdiurnal='".$_POST['notdiurnal']."', ".
				"payinc_otdiurnal='".$_POST['otdiurnal']."', ".
				"payinc_notnoct='".$_POST['notnoct']."', ".
				"payinc_otnoct='".$_POST['otnoct']."', ".
				"payinc_bono='".$_POST['bono']."', ".
				"payinc_aguinaldo='".$_POST['aguinaldo']."', ".
				"payinc_vacation='".$_POST['vacation']."', ".
				"payinc_severance='".$_POST['severance']."', ".
				"payinc_otherincome='".$_POST['otherincome']."', ".
				"attribute1='".$_POST['attr1']."', ".
				"attribute2='".$_POST['attr2']."', ".
				"attribute3='".$_POST['attr3']."', ".
				"attribute4='".$_POST['attr4']."', ".
				"attribute5='".$_POST['attr5']."', ".
    			"attribute6='".$_POST['attr6']."', ".
    			"attribute7='".$_POST['attr7']."', ".
    			"attribute8='".$_POST['attr8']."', ".
    			"attribute9='".$_POST['attr9']."', ".
    			"attribute10='".$_POST['attr10']."', ".
    			"attribute11='".$_POST['attr11']."', ".
    			"attribute12='".$_POST['attr12']."', ".
    			"attribute13='".$_POST['attr13']."', ".
    			"attribute14='".$_POST['attr14']."', ".
    			"attribute15='".$_POST['attr15']."', ".
    			"attribute16='".$_POST['attr16']."', ".
    			"attribute17='".$_POST['attr17']."', ".
    			"attribute18='".$_POST['attr18']."', ".
    			"attribute19='".$_POST['attr19']."', ".
    			"attribute20='".$_POST['attr20']."', ".
				"payinc_received='".$_POST['received']."' ".
				"where payinc_id=".$incExist;
				$dbEx->updSql($sqlText);
				$rslt = 2;
			}
			else{
				
				$sqlText = 
				"insert into paystub_incidents ".
				"set payxemp_id=".$_POST['payxemp_id'].", ".
				"payinc_date=now(), ".
				"payinc_nhoras='".$_POST['nhoras']."', ".
				"payinc_salary='".$_POST['salary']."', ".
				"payinc_nadditionalhours='".$_POST['nadditionalhours']."', ".
				"payinc_additionalhours ='".$_POST['additionalhours']."', ".
				"payinc_salarydisc='".$_POST['salarydisc']."', ".
				"payinc_seventh='".$_POST['seventh']."', ".
				"payinc_nhorasnoct='".$_POST['nhorasnoct']."', ".
				"payinc_horasnoct= '".$_POST['horasnoct']."', ".
				"payinc_notdiurnal='".$_POST['notdiurnal']."', ".
				"payinc_otdiurnal='".$_POST['otdiurnal']."', ".
				"payinc_notnoct='".$_POST['notnoct']."', ".
				"payinc_otnoct='".$_POST['otnoct']."', ".
				"payinc_bono='".$_POST['bono']."', ".
				"payinc_aguinaldo='".$_POST['aguinaldo']."', ".
				"payinc_vacation='".$_POST['vacation']."', ".
				"payinc_severance='".$_POST['severance']."', ".
				"payinc_otherincome='".$_POST['otherincome']."', ".
				"attribute1='".$_POST['attr1']."', ".
				"attribute2='".$_POST['attr2']."', ".
				"attribute3='".$_POST['attr3']."', ".
				"attribute4='".$_POST['attr4']."', ".
				"attribute5='".$_POST['attr5']."', ".
    			"attribute6='".$_POST['attr6']."', ".
    			"attribute7='".$_POST['attr7']."', ".
    			"attribute8='".$_POST['attr8']."', ".
    			"attribute9='".$_POST['attr9']."', ".
    			"attribute10='".$_POST['attr10']."', ".
    			"attribute11='".$_POST['attr11']."', ".
    			"attribute12='".$_POST['attr12']."', ".
    			"attribute13='".$_POST['attr13']."', ".
    			"attribute14='".$_POST['attr14']."', ".
    			"attribute15='".$_POST['attr15']."', ".
    			"attribute16='".$_POST['attr16']."', ".
    			"attribute17='".$_POST['attr17']."', ".
    			"attribute18='".$_POST['attr18']."', ".
    			"attribute19='".$_POST['attr19']."', ".
    			"attribute20='".$_POST['attr20']."', ".
				" payinc_received='".$_POST['received']."' ";
			
				$dbEx->insSql($sqlText);
				$rslt = 2;
			}

			//Actualizacion de deducciones
			//Eliminar los datos de la tabla de descuentos legales 
			$sqlText = "delete from paystub_legaldisc where payxemp_id = ".$_POST['payxemp_id'];
			$dbEx->updSql($sqlText);

			$totalIngresos = $_POST['totalIngresos'];
			//Calculo de impuestos de ley si plaza es fija
			$sqlText = "select ld.legaldisc_id, ".
							"ld.legaldisc_name, ".
							"ld.taxable_remunation, ".
						    "ld.percentage, ".
						    "ld.botton_amount, ".
						    "ld.top_amount, ".
						    "ld.over_excess, ".
						    "ld.fixed_fee, ".
						    "ld.pension_flag, ".
						    "ld.max_quotable ".
						"from legal_discount ld inner join employees e ".
							"on ld.geography_code = e.geography_code ".
						"where ifnull(end_date,date(sysdate())) <= date(sysdate()) ".
						    "and e.employee_id = ".$dtPay['0']['employee_id']." ".
						"order by ld.taxable_remunation asc";

			$dtDescLey = $dbEx->selSql($sqlText);
			$DescGravados = 0;
			$DescPostGravamen = 0;

			foreach ($dtDescLey as $dtDL) {
				$montoDesc = 0;
				if($dtDL['taxable_remunation'] == 'N'){
					$montoDesc = $sFunc->getMontoDesc(
									$totalIngresos,
									$dtDL['botton_amount'],
									$dtDL['top_amount'],
									$dtDL['percentage'],
									$dtDL['over_excess'],
									$dtDL['fixed_fee'],
									$dtDL['max_quotable']);

					if($montoDesc > 0){
						$sqlText = "insert into paystub_legaldisc(".
											"payxemp_id, ".
											"legaldisc_id, ".
											"amount) ".
									"values(".$_POST['payxemp_id'].", ".
									$dtDL['legaldisc_id'].", ".
									$montoDesc.")";

						$dbEx->insSql($sqlText);
						$DescGravados = $DescGravados + $montoDesc;
					}
				}elseif($dtDL['taxable_remunation'] == 'Y'){
					$montoDesc = $sFunc->getMontoDesc(
									$totalIngresos - $DescGravados,
									$dtDL['botton_amount'],
									$dtDL['top_amount'],
									$dtDL['percentage'],
									$dtDL['over_excess'],
									$dtDL['fixed_fee'],
									$dtDL['max_quotable']);

					if($montoDesc > 0){

						$sqlText = "insert into paystub_legaldisc(".
											"payxemp_id, ".
											"legaldisc_id, ".
											"amount) ".
									"values(".$_POST['payxemp_id'].", ".
									$dtDL['legaldisc_id'].", ".
									$montoDesc.")";

						$dbEx->insSql($sqlText);
						$DescPostGravamen = $DescPostGravamen + $montoDesc;
					}

				}
			}


		}
		else{
			$rslt = 1;	
		}
		echo $rslt;
	break;
	
	case 'payIncidentesReport':
		
		$sqlText = "select employee_id, username, firstname, lastname from employees where 1 and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if($dbEx->numrows>0){
			$optE .= '<option value="0">Select a Employee</option>';
			foreach($dtEmp as $dtE){
				$optE .= '<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>'; 
			}
		}
		else{
			$optE .='<option value="0">You do not have employees supervised</option>';
		}
		$sqlText = "select paystub_id, date_format(paystub_delivery, '%d/%m/%Y') as f1 from paystub order by paystub_delivery desc";
		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optDate ='<option value="0">Select a Date</option>';
			foreach($dtPay as $dtP){
				$optDate .= '<option value="'.$dtP['paystub_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		else{
			$optDate .='<option value="0">Paystubs not available</option>';	
		}
		$rslt .= '<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">';
		$rslt .='<tr bgcolor="#8FBC8F"><td colspan="2" align="center"><font color="#FFFFFF">Report incidences of payment</font></td></tr>';
		$rslt .='<tr><td align="right">Select a payment date: </td>';
		$rslt .='<td><select id="lsDate">'.$optDate.'</select></td></tr>';
		$rslt .='<tr><td align="right">Status Employee: </td>';
		$rslt .='<td><select id="lsStatus" onchange="loadEmp(this.value)"><option value="1">ACTIVE</option><option value="-1">ALL</option><option value="0">INACTIVE</option></select></td></tr>';
		$rslt .='<tr><td align="right">Select a Employee: </td>';
		$rslt .='<td><span id="lyEmp"><select id="lsEmp">'.$optE.'</select></span></td></tr>';
		$rslt .='<tr><td align="right">Badge: </td><td><input type="text" id="txtBadge"></td></tr>';
		$rslt .='<tr><td align="right">Name: </td><td><input type="text" id="txtNombre"></td></tr>';		
		$rslt .='<tr><td colspan="2" align="center"><input type="button" onclick="loadReportIncidences()" value="Seach"></td></tr>';

		$rslt .='</table><br><br>';
		$rslt .='<div id="lyData"></div>';
		echo $rslt;

	break;
	
	case 'loadReportIncidences':
		$filtro = " where 1";
		if($_POST['payId']>0){
			$filtro .=" and paystub_id=".$_POST['payId'];
		}
		if($_POST['emp']>0){
			$filtro .=" and p.employee_id=".$_POST['emp'];	
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and username like '%".$_POST['badge']."%'";	
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (firstname like '%".$_POST['nombre']."%' or lastname like '%".$_POST['nombre']."%')";	
		}
		if($_POST['estado']>=0){
			$filtro .=" and user_status=".$_POST['estado'];
		}
		
		$sqlText = "select p.payxemp_id, payxemp_liquid, pi.payinc_id, payinc_received, pi.payinc_status, p.employee_id, username, firstname, lastname,  account_number from employees e inner join paystubxemp p on e.employee_id=p.employee_id inner join paystub_incidents pi on p.payxemp_id=pi.payxemp_id ".$filtro." order by firstname";
		$dtPay = $dbEx->selSql($sqlText);
		$rslt = '<table width="1000" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" class="tblInc">';
		
		if($dbEx->numrows>0){
			$rslt .='<tr><td colspan="6">Matches: '.$dbEx->numrows.'</td></tr>';
			$rslt .='<tr bgcolor="#8FBC8F"><th width="5%"><font color="#FFFFFF">Badge</th><th width="25%"><font color="#FFFFFF">Employee</th><th width="10%"><font color="#FFFFFF">Status</font></th><th width="12%"><font color="#FFFFFF">Payment original</th><th width="12%"><font color="#FFFFFF">Payment Incidences</th><th width="12%"><font color="#FFFFFF">Payment received</th><th width="12%"><font color="#FFFFFF">Account number</th><th width="12%"><font color="#FFFFFF">Change status</font></th></tr>';
			foreach($dtPay as $dtP){
				//Identifica si incidencia ha sido pagada o no
				//Boton envia 1 para cambiar incidencia a pagada y 2 para cambiar a incidencia no pagada
				if($dtP['payinc_status']=='NP'){
				 	$estado = '<font color="#990000">Not paid</font>';
					$boton = '<input type="button" value="Incidence paid" title="click to change incidence the state: paid" style="cursor:pointer" onclick="cambiarEstadoIncidencia('.$dtP['payinc_id'].',1)">';
				}
				else if($dtP['payinc_status']=='P'){
					$estado = '<font color="#006600">Paid</font>';
					$boton = '<input type="button" value="Incidence not paid" title="click to change incidence the state: not paid" style="cursor:pointer" onclick="cambiarEstadoIncidencia('.$dtP['payinc_id'].',2)">';	
				}
				
				$rslt .='<tr class="rowCons">
				<td onclick="loadDataInc('.$dtP['employee_id'].','.$_POST['payId'].')">'.$dtP['username'].'</td>
				<td onclick="loadDataInc('.$dtP['employee_id'].','.$_POST['payId'].')">'.$dtP['firstname'].' '.$dtP['lastname'].'</td>
				<td align="center" onclick="loadDataInc('.$dtP['employee_id'].','.$_POST['payId'].')">'.$estado.'</td>
				<td onclick="loadDataInc('.$dtP['employee_id'].','.$_POST['payId'].')">'.number_format($dtP['payxemp_liquid'],2).'</td>
				<td onclick="loadDataInc('.$dtP['employee_id'].','.$_POST['payId'].')">'.number_format($dtP['payinc_received'],2).'</td>
				<td onclick="loadDataInc('.$dtP['employee_id'].','.$_POST['payId'].')">'.number_format($dtP['payxemp_liquid'] + $dtP['payinc_received'],2).'</td>
				<td onclick="loadDataInc('.$dtP['employee_id'].','.$_POST['payId'].')">'.$dtP['account_number'].'</td>
				<td align="center">'.$boton.'</td></tr>';	
			}
		}
		else{
			$rslt .='<tr><td>No Matches</td></tr>';		
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'cambiarEstadoIncidencia':
		//estado 1; cambiar a pagado y estado 2: cambiar a no pagado
		if($_POST['estado']==1){
			$sqlText = "update paystub_incidents set payinc_status='P' where payinc_id=".$_POST['idP'];	
			$dbEx->updSql($sqlText);
		}
		else if($_POST['estado']==2){
			$sqlText = "update paystub_incidents set payinc_status='NP' where payinc_id=".$_POST['idP'];	
			$dbEx->updSql($sqlText);
		}
		echo "2";
	break;

	case 'updPayment':
		//Recupera los datos del paystub 
		$sqlText = "select * from paystub where paystub_id=".$_POST['idP'];
		$dtPay = $dbEx->selSql($sqlText);
		$fechaIni = $dtPay['0']['PAYSTUB_INI'];
		$fechaFin = $dtPay['0']['PAYSTUB_FIN'];
		$fechaEntrega = $dtPay['0']['PAYSTUB_DELIVERY'];
		
		$sqlText = "select payxemp_id from paystubxemp ".
			"where employee_id not in (".
				"select distinct(e.employee_id) employee_id ".
				"from employees e inner join employee_status st on e.user_status = st.status_id ".
				"inner join plazaxemp pe on pe.employee_id = e.employee_id ".
				"inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
				"inner join places pl on pl.id_place = pd.id_place ".
				"where '".$fechaIni."' between pe.start_date and ifnull(pe.end_date, '".$fechaFin."') ".
				"and st.status_name <> 'Aspirante')";
		
		$dtDel = $dbEx->selSql($sqlText);
		if($dbEx->numrows > 0){
			foreach ($dtDel as $dtD) {
				//Eliminar incidentes de empleados inactivos en el periodo de la planilla
				$sqlText = "delete from paystub_incidents where payxemp_id = ".$dtD['payxemp_id'];
				$dbEx->updSql($sqlText);

				//Eliminar deducciones de empleados inactivos en el periodo de la planilla
				$sqlText = "delete from paystub_legaldisc where payxemp_id = ".$dtD['payxemp_id'];
				$dbEx->updSql($sqlText);

				//Eliminar boleta de pago
				$sqlText = "delete from paystubxemp where payxemp_id = ".$dtD['payxemp_id'];
				$dbEx->updSql($sqlText);				
			}
		}

		//Empleados activos hasta la fecha fin de la planilla
		$sqlText = "select distinct(e.employee_id) employee_id ".
			"from employees e inner join employee_status st on e.user_status = st.status_id ".
			"inner join plazaxemp pe on pe.employee_id = e.employee_id ".
			"inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
			"inner join places pl on pl.id_place = pd.id_place ".
			"where '".$fechaIni."' between pe.start_date and ifnull(pe.end_date, '".$fechaFin."') ".
			"and st.status_name <> 'Aspirante' ";
			
		$dtEmp = $dbEx->selSql($sqlText);

		if($dbEx->numrows>0){ //verifica si devolvio resultados
			foreach($dtEmp as $dtE){

				//Actualizar la boleta de pago por empleado
				$boletaRslt = $sFunc->calcularPagoEmpleado(
						$dtE['employee_id'],
						$_POST['idP'],
						$fechaIni,
						$fechaFin,
						$fechaEntrega
				);

			}	//Fin de busqueda por empleados
		}
		
		$rslt = 2;

		echo $rslt;

	break;
	
	case 'enablePaystub':
		$sqlText = "update paystub set paystub_status='A' where paystub_id=".$_POST['idP'];
		$dbEx->updSql($sqlText);
		echo "2";
	break;
	
	case 'acceptPaystub':
		$sqlText = "update paystubxemp set payxemp_status='A' where payxemp_id=".$_POST['idP'];
		$dbEx->updSql($sqlText);
		echo "2";
	break;
	
	case 'payStatusReport':
		$sqlText = "select paystub_id, date_format(paystub_delivery, '%d/%m/%Y') as f1 from paystub order by paystub_delivery desc";
		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$optDate ='<option value="0">Select a Date</option>';
			foreach($dtPay as $dtP){
				$optDate .= '<option value="'.$dtP['paystub_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		else{
			$optDate .='<option value="0">Paystubs not available</option>';	
		}
		
		$sqlText = "select employee_id, username, firstname, lastname from employees where 1 and user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optE = "";
		if($dbEx->numrows>0){
			$optE .= '<option value="0">Select a Employee</option>';
			foreach($dtEmp as $dtE){
				$optE .= '<option value="'.$dtE['employee_id'].'">'.$dtE['firstname']." ".$dtE['lastname'].'</option>'; 
			}
		}
		else{
			$optE .='<option value="0">You do not have employees supervised</option>';
		}
		$rslt .= '<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">';
		$rslt .='<tr bgcolor="#8FBC8F"><td colspan="2" align="center"><font color="#FFFFFF">Paystub status report </font></td></tr>';
		$rslt .='<tr><td align="right">Select a payment date: </td>';
		$rslt .='<td><select id="lsDate">'.$optDate.'</select></td></tr>';
		$rslt .='<tr><td align="right">Status Employee: </td>';
		$rslt .='<td><select id="lsStatus" onchange="loadEmp(this.value)"><option value="1">ACTIVE</option><option value="-1">ALL</option><option value="0">INACTIVE</option></select></td></tr>';
		$rslt .='<tr><td align="right">Select a Employee: </td>';
		$rslt .='<td><span id="lyEmp"><select id="lsEmp">'.$optE.'</select></span></td></tr>';
		$rslt .='<tr><td align="right">Badge: </td><td><input type="text" id="txtBadge"></td></tr>';
		$rslt .='<tr><td align="right">Name: </td><td><input type="text" id="txtNombre"></td></tr>';
		$rslt .='<tr><td align="right">Status of paystub: </td>
		<td><select id="lsStatusPay"><option value="0">[ALL]</option><option value="A">Accepted</option><option value="P">Pending acceptance</option></select></td></tr>';		
		$rslt .='<tr><td colspan="2" align="center"><input type="button" onclick="loadReportPayStatus()" value="Seach"></td></tr>';

		$rslt .='</table><br><br>';
		$rslt .='<div id="lyData"></div>';
		echo $rslt;
		
	break;
	
	case 'loadReportPayStatus':
		$filtro = " where 1 ";
		if($_POST['estatus']!='0'){
			$filtro .=" and user_status='".$_POST['estatus']."'";
		}
		if($_POST['employee']>0){
			$filtro .=" and e.employee_id=".$_POST['employee'];
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and username like '%".$_POST['badge']."%' ";
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (firstname like '%".$_POST['nombre']."%' or lastname like '%".$_POST['nombre']."%' )";
		}
		if($_POST['estadoPay']!='0'){
			$filtro .=" and payxemp_status='".$_POST['estadoPay']."'";
		}
		if($_POST['idPay']>0){
			$filtro .=" and p.paystub_id=".$_POST['idPay'];
		}
		
		$sqlText = $sqlText = "select pe.payxemp_id, e.employee_id, firstname, lastname, username, date_format(paystub_delivery,'%d/%m/%Y') as f1, payxemp_status, payxemp_liquid from employees e inner join paystubxemp pe on e.employee_id=pe.employee_id inner join paystub p on p.paystub_id=pe.paystub_id ".$filtro." order by firstname, p.paystub_id desc ";
		$dtPay = $dbEx->selSql($sqlText);
		$rslt .= '<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">';
		
		if($dbEx->numrows>0){
			$rslt .='<tr bgcolor="#8FBC8F"><td><font color="#FFFFFF">Badge</font></td>
			<td><font color="#FFFFFF">Employee</font></td>
			<td><font color="#FFFFFF">Payment delivery date</font></td>
			<td><font color="#FFFFFF">Status</font></td>
			<td><font color="#FFFFFF">Payment received</td></tr>';
			
			foreach($dtPay as $dtP){
				$estado = "";
				if($dtP['payxemp_status']=='A'){
					$estado = 'Accepted';	
				}
				else if($dtP['payxemp_status']=='P'){
					$estado = 'Pending acceptance';
				}
				$sqlText = "select payinc_received from paystub_incidents where payxemp_id=".$dtP['payxemp_id'];
				$dtInc = $dbEx->selSql($sqlText);
				$incRecibir = 0;
				if($dbEx->numrows>0){
					$incRecibir	= $dtInc['0']['payinc_received'];
				}
				$rslt .='<tr class="rowCons"><td>'.$dtP['username'].'</td>
				<td>'.$dtP['firstname']." ".$dtP['lastname'].'</td>
				<td>'.$dtP['f1'].'</td>
				<td>'.$estado.'</td>
				<td>'.number_format(($dtP['payxemp_liquid'] + $incRecibir),2).'</td></tr>';
			}
			
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'chequearPaystub':
		$rslt = cargaPag("../mtto/chequearPaystub.php");
		$comment ="";
		$idTicket = 0;
		$sqlText = "select * from paystub_tickets where payxemp_id=".$_POST['idPay'];
		$dtTicket = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$idTicket = $dtTicket['0']['PAYTICKET_ID'];
			$comment = $dtTicket['0']['PAYTICKET_COMMENTS'];
		}
		$rslt = str_replace("<!--idTicket-->",$idTicket,$rslt);
		$rslt = str_replace("<!--comment-->",$comment,$rslt);
		$rslt = str_replace("<!--idPaystub-->",$_POST['idPay'],$rslt);
		$rslt = str_replace("<!--idEmp-->",$_POST['idE'],$rslt);
		
		echo $rslt;
	break;
	
	case 'saveTicketPaystub':
		if($_POST['idTicket']>0){
			$sqlText = "update paystub_tickets set payticket_comments='".addslashes($_POST['comment'])."', payticket_status='P' where payticket_id=".$_POST['idTicket'];
			$dbEx->updSql($sqlText);
		}
		else{
			$sqlText = "insert into paystub_tickets set payxemp_id=".$_POST['idPay'].", payticket_comments='".addslashes($_POST['comment'])."', payticket_date=now()";
			$dbEx->insSql($sqlText);	
		}
		echo "2";
	break;
	
	case 'incidencesTickets':
		$rslt = cargaPag("../mtto/filtrosTickets.php");
		//Lista las planillas, por defecto todas
		$sqlText = "select paystub_id, date_format(paystub_delivery,'%d/%m/%Y') as f1 from paystub order by paystub_delivery desc";
		$dtPay = $dbEx->selSql($sqlText);
		$optPaystub = '<option value="0">[ALL]</option>';
		if($dbEx->numrows>0){
			foreach($dtPay as $dtP){
				$optPaystub .='<option value="'.$dtP['paystub_id'].'">'.$dtP['f1'].'</option>';
			}
		}
		$rslt = str_replace("<!--optPaystub-->",$optPaystub,$rslt);
		echo $rslt;
	break;
	
	case 'loadPaystubTicket':
		$filtro = " where 1 ";
		if($_POST['paystub']>0){
			$filtro .=" and p.paystub_id=".$_POST['paystub'];
		}
		if($_POST['status']!='0'){
			$filtro .=" and pt.payticket_status='".$_POST['status']."'";
		}
		if(strlen($_POST['nombre'])>0){
			$filtro .=" and (firstname like '%".$_POST['nombre']."%' or lastname like '%".$_POST['nombre']."%' or concat(firstname,' ',lastname) like '%".$_POST['nombre']."%') ";
		}
		if(strlen($_POST['badge'])>0){
			$filtro .=" and username like '%".$_POST['badge']."%'";
		}
		$sqlText = "select payticket_id, e.employee_id, username, firstname, lastname, date_format(paystub_delivery,'%d/%m/%Y') as fec_pay, payticket_comments, date_format(payticket_date,'%d/%m/%Y') as fec_inc, payticket_status, payticket_authorizer from employees e inner join paystubxemp pe on e.employee_id=pe.employee_id inner join paystub p on p.paystub_id=pe.paystub_id inner join paystub_tickets pt on pt.payxemp_id=pe.payxemp_id ".$filtro." order by payticket_id desc";
		
		$dtTicket = $dbEx->selSql($sqlText);
		
		$rslt = '<table width="900" bordercolor="#8FBC8F" align="center" cellpadding="2" cellspacing="2" class="backTablaMain">';
		if($dbEx->numrows>0){
			$rslt .='<tr bgcolor="#8FBC8F">
			<td width="2%">N&deg;</td>
			<td width="5%">Badge</td>
			<td width="20%">Employee</td>
			<td width="5%">Paystub</td>
			<td width="5%">Record date</td>
			<td width="45%">Comments</td>
			<td width="8%">Status</td>
			<td width="5%"></td>
			<td width="5%"></td></tr>';
			foreach($dtTicket as $dtT){
			//Para mostrar el boton de aceptar o rechazar verificamos el estado
				$estado = "";
				$btnAceptar = "";
				$btnRechazar = "";
				if($dtT['payticket_status']=='P'){
					$estado = '<font color="#CC6600"><b>Pending</font>';
					$btnAceptar = '<img src="images/list_check.png" alt="Accept incidence" style="cursor:pointer" width="20" onclick="acceptTicket('.$dtT['payticket_id'].')">';
					$btnRechazar = '<img src="images/rechazar.png" alt="Reject incidence" style="cursor:pointer" width="20" onclick="rejectTicket('.$dtT['payticket_id'].')">';
				}
				else if($dtT['payticket_status']=='A'){
					$estado = '<font color="#003333"><b>Approved</font>';	
					$btnRechazar = '<img src="images/rechazar.png" alt="Reject incidence" style="cursor:pointer" width="20" onclick="rejectTicket('.$dtT['payticket_id'].')">';
				}
				else if($dtT['payticket_status']=='R'){
					$estado = '<font color="#800000"><b>Rejected</font>';
					$btnAceptar = '<img src="images/list_check.png" alt="Accept incidence" style="cursor:pointer" width="20" onclick="acceptTicket('.$dtT['payticket_id'].')">';
				}
    		$rslt .='<tr><td>'.$dtT['payticket_id'].'</td>
				<td>'.$dtT['username'].'</td>
				<td>'.$dtT['firstname'].' '.$dtT['lastname'].'</td>
				<td>'.$dtT['fec_pay'].'</td>
				<td>'.$dtT['fec_inc'].'</td>
				<td>'.$dtT['payticket_comments'].'</td>
				<td align="center">'.$estado.'</td>
				<td align="center">'.$btnAceptar.'</td>
				<td align="center">'.$btnRechazar.'</td></tr>';
			}
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'acceptTicket':
		if($_SESSION['usr_id']>0){
			$sqlText = "update paystub_tickets set payticket_status='A', payticket_authorizer=".$_SESSION['usr_id'].", payticket_dateauthor=now()";
			$dbEx->updSql($sqlText);
			$rslt = 2;
			
		}
		else{
			$rslt = 1;	
		}
		echo $rslt;
	break;
	
	case 'rejectTicket':
		if($_SESSION['usr_id']>0){
			$sqlText = "update paystub_tickets set payticket_status='R', payticket_authorizer=".$_SESSION['usr_id'].", payticket_dateauthor=now()";
			$dbEx->updSql($sqlText);
			$rslt = 2;
		}
		else{
			$rslt = 1;	
		}
		echo $rslt;
	break;
	
    case 'discountSetup':
        $rslt = cargaPag("../mtto/discountForm.php");
		//Listado de flexfields disponibles
		$sqlText = "select da.disc_attributeid, da.disc_attributename ".
		"from pay_discount_attr da left outer join pay_discount_setup ds on da.disc_attributeid = ds.disc_attributeid ".
		"where da.disc_attributeid not in ".
        "(select disc_attributeid from pay_discount_setup where ifnull(disc_end_date,sysdate() + 1) > sysdate())";
		
		$dtDisc = $dbEx->selSql($sqlText);
		$optDisc = '';
		if($dbEx->numrows>0){
			foreach($dtDisc as $dtD){
				$optDisc .='<option value="'.$dtD['disc_attributeid'].'">'.$dtD['disc_attributename'].'</option>';
			}
		}
		$rslt = str_replace("<!--optAttr-->",$optDisc,$rslt);
		
		//Tabla con descuentos configurados
  		$sqlText = "select ds.disc_id, da.disc_attributename, ds.disc_label, ".
 			"date_format(ds.disc_start_date,'%d/%m/%Y') disc_start_date, date_format(ds.disc_end_date,'%d/%m/%Y') disc_end_date ".
			"from pay_discount_attr da inner join pay_discount_setup ds on da.disc_attributeid = ds.disc_attributeid order by ".
			"da.disc_attributename desc, ds.disc_start_date asc";
			
		$dtDs = $dbEx->selSql($sqlText);
		$tbDisc = '';
		if($dbEx->numrows>0){
		    $tbDisc .= '<table width="800" bordercolor="#8FBC8F" align="center" cellpadding="4" cellspacing="4" style="font-size: 11px; font-family: Tahoma; font-weight: 800; color: #666;">'.
				'<tr bgcolor="#8FBC8F"><td colspan="5" align="center"><font color="#FFFFFF">Descuentos configurados</font></td></tr>';
			$tbDisc .= '<tr bgcolor="#8FBC8F"><font color="#FFFFFF"><td>Flexfield</td><td>Label</td><td>Start date</td><td>End date</td><td>Options</td></tr>';
			
            foreach($dtDs as $dtD){
                $tbDisc .= '<tr class="rowCons"><td>'.$dtD['disc_attributename'].'</td><td>'.$dtD['disc_label'].'</td><td>'.$dtD['disc_start_date'].'</td><td>'.$dtD['disc_end_date'].'</td>'.
				'<td><a href="#" onclick="updDiscountForm('.$dtD['disc_id'].')">Click to update record</a></td></tr><td colspan="5"><div id="lyUpd'.$dtD['disc_id'].'"></td></tr>';
	  		}

		} else{
			$tbDisc = '<font color="#666666">There is no discount settings</font>'	;
		}
		$rslt = str_replace("<!--tblDiscounts-->",$tbDisc,$rslt);

		echo $rslt;
	break;
	
	case 'saveDiscountSetup':
	
	    $sqlText = "select count(1) c from pay_discount_setup where disc_label = '".$_POST['label']."' and ifnull(disc_end_date,sysdate() + 1) > sysdate()";
	    $dtC = $dbEx->selSql($sqlText);
		//Validar si no existe un descuento activo con el label seleccionado
		if($dtC['0']['c'] <> "0"){
			$rslt = 0;
		}
		else{
			$sqlText = "insert into pay_discount_setup(disc_attributeid, disc_label, disc_start_date) ".
				"values(".$_POST['attribute'].",'".$_POST['label']."',sysdate())";
			$dbEx->insSql($sqlText);
			$rows = $dbEx->insertID;

            if ($rows > 0) {
				$rslt = 2;
   			}
		}
		echo $rslt;
	
	break;
	
	case 'updDiscountForm':
		$sqlText = "select disc_label, date_format(disc_end_date,'%d/%m/%Y')disc_end_date from pay_discount_setup where disc_id = ".$_POST['discountId'];
		$dtDisc = $dbEx->selSql($sqlText);
		
  		$rslt = 'Label: <input type="text" class="txtPag" id="txtLabelUpd'.$_POST['discountId'].'" value="'.$dtDisc['0']['disc_label'].'">'.
		  ' End Date: <input type="text" class="txtPag" name="end_date'.$_POST['discountId'].'" id="end_date'.$_POST['discountId'].'" '.
		  'value="'.$dtDisc['0']['disc_end_date'].'" size="15" class="txtPag" /><img src="images/calendar.jpg" align="center" onclick="return showCalendar('."'".'end_date'.$_POST['discountId'].''."'".', '."'".'%d/%m/%Y'."'".');" style="cursor:pointer;" /> '.
		  '<input type="button" onclick="saveUpdDiscSetup('.$_POST['discountId'].')" value="Save update">';
		echo $rslt;

	break;
	
	case 'saveUpdDiscSetup':

		if(strlen($_POST['endDate']) > 0){
			$fecha = "'".$oFec->cvDtoY($_POST['endDate'])."'";
		}
		else{
			$fecha = "null";
		}
			
		/*$sqlText =	"select count(1) c ".
			" from pay_discount_setup a ".
			" where a.disc_id <> ".$_POST['discountId'].
			" and ((a.disc_label = '".$_POST['label']."'".
			" and ifnull(a.disc_end_date,sysdate() + 1) > sysdate()) ".
			" or (exists (select * ".
			" from pay_discount_setup b where b.disc_attributeid = a.disc_attributeid and b.disc_start_date > a.disc_start_date )))";  */

		$sqlText = "select count(ds.disc_id) c".
			" from pay_discount_setup ds ".
			" where (ds.disc_id = ( ".
			" select b.disc_id ".
			" from pay_discount_setup a, pay_discount_setup b ".
			" where a.disc_attributeid = b.disc_attributeid ".
			" and a.disc_id = ".$_POST['discountId']." and b.disc_id <> ".$_POST['discountId']." and b.disc_id > a.disc_id ".
			" ) or ".
			" (ds.disc_id <> ".$_POST['discountId']." ".
			" and ds.disc_label = '".$_POST['label']."'".
			" and ifnull(ds.disc_end_date,sysdate() + 1) > sysdate()))";
			
	    $dtC = $dbEx->selSql($sqlText);
		//Validar si no existe un descuento activo con el label seleccionado y que no hay un registro con el mismo flexfield creado luego de el seleccionado
		if($dtC['0']['c'] <> "0"){
			$rslt = 0;
		}
		else{
			$sqlText = "update pay_discount_setup set disc_label = '".$_POST[label]."' , disc_end_date = ".$fecha." ".
				"where disc_id = ".$_POST['discountId'];
			$dbEx->updSql($sqlText);
			$rows = $dbEx->affectedRows;

            if ($rows > 0) {
				$rslt = 2;
   			}
		}
		echo $rslt;
	
	
	break;

	case 'getFechaPaysub':

		$sqlText = "select date_format(".$_POST['label'].",'%d/%m/%Y') fecha from paystub where paystub_id = ".$_POST['idP'];
		$dtF = $dbEx->selSql($sqlText);
		$rslt = $dtF['0']['fecha'];
		echo $rslt;

	break;

	case 'saveUpdatePaystub':
		$fechaEntrega = $oFec->cvDtoY($_POST['fecEntrega']);
		$fechaIni = $oFec->cvDtoY($_POST['fecIni']);
		$fechaFin = $oFec->cvDtoY($_POST['fecFin']);

		$sqlText = "select * ".
				"from paystub ".
				"where (paystub_delivery='".$fechaEntrega."' ".
 				"or '".$fechaIni."' between paystub_ini and paystub_fin ".
 				"or '".$fechaFin."' between paystub_ini and paystub_fin ) ".
 				"and paystub_id <> ".$_POST['idP'];

 		$dtPay = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$rslt = -1;
		}
		else{
			$sqlText = "update paystub set paystub_ini = '".$fechaIni."', paystub_fin='".$fechaFin."', paystub_delivery='".$fechaEntrega."' ".
				"where paystub_id = ".$_POST['idP'];

			$dbEx->updSql($sqlText);
			$rslt = 2;
		}
		echo $rslt;

	break;

	case 'workSchHours':

		$rslt = cargaPag("../mtto/filtrosWorkSchHours.php");
		echo $rslt;

	break;

	case 'rptWorkSchHours':

		$fechaIni = $oFec->cvDtoY($_POST['fechaInicio']);
		$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
		
		//Inicializacion de variables de sumatorias
		$h_programadas = 0;
		$h_total_trab = 0;
		$h_diurnas = 0;
		$h_nocturnas = 0;
		$h_ap = 0;
		$h_vacac = 0;
		$h_exception = 0;
		$h_adit = 0;
		$h_feriado = 0;
		$h_extra_d = 0;
		$h_extra_n = 0;
		$h_extra_f = 0;


		//Validar que la sesion este activa
		if(strlen($_SESSION['usr_id'])<= 0 or $_SESSION['usr_id'] <= 0){
			$rslt = -1;
		}
		else  {

			$sqlText ="select datediff('".$fechaFin."','".$fechaIni."') diff from dual";
			$cantDias = $dbEx ->selSql($sqlText);

			//Valida que el intervalo de dias sea mayor o igual a cero
			if($cantDias['0']['diff'] >= 0){
				//Recorrer los datos dia a dia
				
				$rslt = '<table class="tblResult" align="center" cellpadding="4" cellspacing="4" border="1">';
				$rslt .= '<tr class="showItem"><td>Fecha</td><td>Programadas</td>';
				$rslt .= '<td>Total Trabajadas</td><td>Trabajadas Diurnas</td>';
				$rslt .= '<td>Trabajadas Nocturnas</td><td>Horas AP</td><td>Vacaciones</td>';
				$rslt .= '<td>Excepci&oacute;n</td><td>Adicionales</td>';
				$rslt .= '<td>Feriado</td><td>Extras diurnas</td>';
				$rslt .= '<td>Extras nocturnas</td><td>Extra Feriado</td></tr>';

				for( $i=0; $i<= ($cantDias['0']['diff']); $i++){

					$sqlText = "select a.*, (w_day + w_night + h_ap + h_vacac + h_excep + ".
								"h_additional + h_holiday + h_day_overtime + h_night_overtime + ".
								"h_holiday_overtime) h_total ".
						"from ( ".
							"select ".
								"(select ifnull(sum(time_to_sec(sch_departure))/3600,0) ".
								   "- ifnull(sum(time_to_sec(sch_entry))/3600,0) ".
					               "- ifnull(sum(time_to_sec(sch_lunchin))/3600,0) ".
					               "- ifnull(sum(time_to_sec(sch_lunchout))/3600,0) sch_proghrs ".
								"from schedules  sc ".
								"where sch_date= f.fecha ".
									"and employee_id = e.employee_id) h_programadas, ".
								"(select ifnull((sum(time_to_sec(payroll_htotal)))/3600,0) as w_total ".
								"from payroll ".
								"where employee_id= e.employee_id and payroll_date = f.fecha ".
								") w_total, ".
								"(select ifnull((sum(time_to_sec(payroll_daytime)))/3600,0) as w_day ".
								"from payroll ".
								"where employee_id= e.employee_id and payroll_date = f.fecha ".
								") w_day, ".
								"(select ifnull((sum(time_to_sec(payroll_nigth)))/3600,0) as w_night ".
								"from payroll ".
								"where employee_id= e.employee_id and payroll_date = f.fecha ".
								") w_night, ".
								"ifnull((select hours_ap ".
									"from apxemp ".
									"where employee_id=e.employee_id and id_tpap in(1,7) and hours_ap!= '' and ".
									"startdate_ap = f.fecha ".
								"),0) h_ap, ".
								"ifnull((select hours_ap ".
									"from apxemp ".
									"where employee_id=e.employee_id and id_tpap in(5) and hours_ap!= '' and  ".
									"startdate_ap = f.fecha ".
								"),0) h_vacac, ".
								"ifnull((select (sum(time_to_sec(exceptionemp_hfin)) - sum(time_to_sec( ".
								"exceptionemp_hini)))/3600 as h_exception ".
								"from exceptionxemp ex inner join exceptions_type et ".
									"on et.exceptiontp_id=ex.exceptiontp_id ".
								"where ex.employee_id=e.employee_id ".
									" and exceptionemp_approved='A' ".
									" and exceptiontp_level=1 ".
									" and exceptionemp_date = f.fecha ".
								"),0) h_excep, ".
								"ifnull((select (sum(time_to_sec(exceptionemp_hfin)) - sum(time_to_sec( ".
									"exceptionemp_hini)))/3600 as h_exception ".
								"from exceptionxemp ex inner join exceptions_type et ".
									"on et.exceptiontp_id=ex.exceptiontp_id ".
								"where ex.employee_id=e.employee_id ".
									"and exceptionemp_approved='A' ".
									"and exceptiontp_level=2 ".
									"and et.exceptiontp_name = 'ADDITIONAL HOURS' ".
									"and exceptionemp_date = f.fecha ".
								"),0) h_additional, ".
								"ifnull((select (sum(time_to_sec(exceptionemp_hfin)) - sum(time_to_sec( ".
								"exceptionemp_hini)))/3600 as h_exception ".
								"from exceptionxemp ex inner join exceptions_type et ".
									"on et.exceptiontp_id=ex.exceptiontp_id ".
								"where ex.employee_id=e.employee_id ".
									"and exceptionemp_approved='A' ".
									"and et.exceptiontp_name = 'PAID HOLIDAY' ".
									"and exceptionemp_date = f.fecha ".
								"),0) h_holiday, ".
								"ifnull((select (sum(time_to_sec(exceptionemp_hfin)) - sum(time_to_sec( ".
									"exceptionemp_hini)))/3600 as h_exception ".
								"from exceptionxemp ex inner join exceptions_type et ".
									"on et.exceptiontp_id=ex.exceptiontp_id ".
								"where ex.employee_id=e.employee_id ".
									"and exceptionemp_approved='A' ".
									"and exceptiontp_level=2 ".
									"and et.exceptiontp_name = 'DAY OVERTIME' ".
									"and exceptionemp_date = f.fecha ".
								"),0) h_day_overtime, ".
								"ifnull((select (sum(time_to_sec(exceptionemp_hfin)) - sum(time_to_sec( ".
								"exceptionemp_hini)))/3600 as h_exception ".
								"from exceptionxemp ex inner join exceptions_type et ".
									"on et.exceptiontp_id=ex.exceptiontp_id ".
								"where ex.employee_id=e.employee_id ".
									"and exceptionemp_approved='A' ".
									"and exceptiontp_level=2 ".
									"and et.exceptiontp_name = 'NIGHT OVERTIME' ".
									"and exceptionemp_date = f.fecha ".
								"),0) h_night_overtime, ".
								"ifnull((select (sum(time_to_sec(exceptionemp_hfin)) - sum(time_to_sec( ".
								"exceptionemp_hini)))/3600 as h_exception ".
								"from exceptionxemp ex inner join exceptions_type et ".
									"on et.exceptiontp_id=ex.exceptiontp_id ".
								"where ex.employee_id=e.employee_id ".
									"and exceptionemp_approved='A' ".
									"and exceptiontp_level=2 ".
									"and et.exceptiontp_name = 'HOLIDAY OVERTIME' ".
									"and exceptionemp_date = f.fecha ".
								"),0) h_holiday_overtime, ".
								"date_format(f.fecha,'%d/%b/%Y') fecha ".
							"from (select employee_id  ".
								"from employees ".
								"where employee_id = ".$_SESSION['usr_id'].") e, ".
								"(select DATE_ADD('".$fechaIni."', INTERVAL ".$i." DAY)  fecha from dual) f ".
							") a ".
							"where (w_day + w_night + h_ap + h_vacac + h_excep + h_additional + ". 
								"h_holiday + h_day_overtime + h_night_overtime + h_holiday_overtime + h_programadas) > 0";

					$data = $dbEx->selSql($sqlText);

					if($dbEx->numrows>0) {
			
						foreach ($data as $dt) {
					
							$rslt .= '<tr align="center"><td>'.$dt['fecha'].'</td>';
							$rslt .= '<td>'.round($dt['h_programadas'],2).'</td><td>'.round($dt['h_total'],2).'</td>';
							$rslt .= '<td>'.round($dt['w_day'],2).'</td>';
							$rslt .= '<td>'.round($dt['w_night'],2).'</td><td>'.round($dt['h_ap'],2).'</td><td>'.round($dt['h_vacac'],2).'</td>';
							$rslt .= '<td>'.round($dt['h_excep'],2).'</td><td>'.round($dt['h_additional'],2).'</td>';
							$rslt .= '<td>'.round($dt['h_holiday'],2).'</td><td>'.round($dt['h_day_overtime'],2).'</td>';
							$rslt .= '<td>'.round($dt['h_night_overtime'],2).'</td><td>'.round($dt['h_holiday_overtime'],2).'</td>';
							$rslt .= '</tr>';

							$h_programadas += $dt['h_programadas'];
							$h_total_trab += $dt['h_total'];
							$h_diurnas += $dt['w_day'];
							$h_nocturnas += $dt['w_night'];
							$h_ap += $dt['h_ap'];
							$h_vacac += $dt['h_vacac'];
							$h_exception += $dt['h_excep'];
							$h_adit += $dt['h_additional'];
							$h_feriado += $dt['h_holiday'];
							$h_extra_d += $dt['h_day_overtime'];
							$h_extra_n += $dt['h_night_overtime'];
							$h_extra_f += $dt['h_holiday_overtime'];

						}				
					}
				}

				//Imprimir totales
				$rslt .= '<tr class="showItem"><td>TOTALES</td>';
				$rslt .= '<td>'.round($h_programadas,2).'</td><td>'.round($h_total_trab,2).'</td>';
				$rslt .= '<td>'.round($h_diurnas,2).'</td>';
				$rslt .= '<td>'.round($h_nocturnas,2).'</td><td>'.round($h_ap,2).'</td><td>'.round($h_vacac,2).'</td>';
				$rslt .= '<td>'.round($h_exception,2).'</td><td>'.round($h_adit,2).'</td>';
				$rslt .= '<td>'.round($h_feriado,2).'</td><td>'.round($h_extra_d,2).'</td>';
				$rslt .= '<td>'.round($h_extra_n,2).'</td><td>'.round($h_extra_f,2).'</td>';
				$rslt .= '</tr>';

			}
			else{
				$rslt = -2;
			}
		}

		echo $rslt;

	break;

	case 'legalDiscSetup':
        $rslt = cargaPag("../mtto/formLegalDisc.php");
        $tbDisc = '';
        $optCountry = '';

		//Listado de paises
		$sqlText = "select geography_code, geography_name ".
				"from geographies where geography_type = 'COUNTRY' ".
				"and ifnull(end_date,sysdate() + 1) > sysdate() ".
				"order by geography_name";
		
		$dtCountry = $dbEx->selSql($sqlText);
		
		if($dbEx->numrows>0){
			foreach($dtCountry as $dtC){
				$optCountry .='<option value="'.$dtC['geography_code'].'">'.$dtC['geography_name'].'</option>';
			}
		}
		$rslt = str_replace("<!--optCountry-->",$optCountry,$rslt);

		$sqlText = "select legaldisc_id, ".
					"legaldisc_name, ".
					"gp.geography_name, ".
					"percentage, ".
					"taxable_remunation, ".
					"botton_amount, ".
					"top_amount, ".
					"over_excess, ".
					"fixed_fee, ".
					"pension_flag, ".
					"max_quotable, ".
					"date_format(ld.start_date,'%d-%M-%Y') start_date, ".
					"date_format(ld.end_date,'%d-%M-%Y') end_date ".
					"from legal_discount ld inner join geographies gp on ld.geography_code = gp.geography_code ".
					"order by gp.geography_code, legaldisc_name";

		$dtDs = $dbEx->selSql($sqlText);
		if($dbEx->numrows>0){
			$tbDisc .= '<table class="tablaVerde" cellpadding="4" cellspacing="4">'.
				'<tr bgcolor="#8FBC8F"><td colspan="13" align="center"><font color="#FFFFFF">Configured Discounts</font></td></tr>';
			$tbDisc .= '<tr class="itemBlanco">'.
				'<td>Pais</td>'.
				'<td>Nombre de descuento</td>'.
				'<td>Sobre remunerac&oacute;n gravada?</td>'.
				'<td>% de Descuento</td>'.
				'<td>Monto desde</td>'.
				'<td>Monto hasta</td>'.
				'<td>Sobre exceso de</td>'.
				'<td>Cuota fija</td>'.
				'<td>Maximo cotizable</td>'.
				'<td>Descuento de pensi&oacute;n?</td>'.
				'<td>Fecha inicio</td>'.
				'<td>Fecha fin</td>'.
				'<td></td></tr>';
			
            foreach($dtDs as $dtD){
                $tbDisc .= '<tr class="rowCons">'.
                		'<td>'.$dtD['geography_name'].'</td>'.
                		'<td>'.$dtD['legaldisc_name'].'</td>'.
                		'<td>'.$dtD['taxable_remunation'].'</td>'.
                		'<td>'.$dtD['percentage'].'</td>'.
                		'<td>'.$dtD['botton_amount'].'</td>'.
                		'<td>'.$dtD['top_amount'].'</td>'.
                		'<td>'.$dtD['over_excess'].'</td>'.
                		'<td>'.$dtD['fixed_fee'].'</td>'.
                		'<td>'.$dtD['max_quotable'].'</td>'.
                		'<td>'.$dtD['pension_flag'].'</td>'.
                		'<td>'.$dtD['start_date'].'</td>'.
                		'<td>'.$dtD['end_date'].'</td>'.
						'<td><a href="#" onclick="updLegalDiscForm('.$dtD['legaldisc_id'].')">Click to update record</a></td></tr><td colspan="6"><div id="lyUpd'.$dtD['legaldisc_id'].'"></td></tr>';
	  		}
		}
		else{
			$tbDisc = '';
		}

		$rslt = str_replace("<!--tblDisc-->",$tbDisc,$rslt);

		$sqlText = "select date_format(sysdate(),'%d/%m/%Y') sysdate from dual";

		$dtSysdate = $dbEx->selSql($sqlText);
		$rslt = str_replace("<!--sysdate-->",$dtSysdate['0']['sysdate'],$rslt);		
	
		echo $rslt;
	break;

	case 'saveLegalDisc':

		$country 		= $_POST['country'];
		$name 			= $_POST['name'];
		$perc 			= $_POST['perc'];
		$startDate 		= $oFec->cvDtoY($_POST['startDate']);
		$bottonAmount 	= $_POST['bottonAmount'];
		$topAmount 		= $_POST['topAmount'];
		$flagCalculo 	= $_POST['flagCalculo'];
		$over_excess 	= $_POST['overExcess'];
		$fixedFee 		= $_POST['fixedFee'];
		$flagPension	= $_POST['flagPension'];
		$maxQuotable 	= $_POST['maxQuotable'];


		$rslt = 1;

		//Validar que no exista registro activo con el mismo dato
		$sqlText = "select count(1) c from legal_discount ".
					"where legaldisc_name = '".$name."' and geography_code = '".$country."' ".
					"and ifnull(end_date,sysdate() + 1) > sysdate()";

		$dtC = $dbEx->selSql($sqlText);
		if($dtC['0']['c'] > 0){
			$rslt = 0;
		}

		if($rslt > 0){
			//Validar fecha de inicio no puede ser menor a fecha actual
			$sqlText = "select 1 from dual where '".$startDate."' < date(sysdate())";
			$dtF = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				$rslt = -1;
			}
		}
		//Porcentaje ingresado debe ser mayor o igual a 0
		if($rslt > 0){
			try{
				 if(is_numeric($perc)) {
				 	if($perc < 0){
				 		$rslt = -2;
				 	}
				 }
				 else{
				 	$rslt = -2;	
				 }
			}
			catch(Exception $e){
				$rslt = -2;
			}
		}
		//Si se han ingresado montos limites deben ser valores numeros
		if($rslt > 0){
			if(strlen($bottonAmount) >0 and strlen($topAmount)>0 ){
				try{
				 	if(is_numeric($bottonAmount) and is_numeric($topAmount)) {
				 		if($bottonAmount < 0 or $topAmount < 0){
				 			$rslt = -3;
				 		}
				 	}
				 	else{
				 		$rslt = -3;	
				 	}
				}
				catch(Exception $e){
					$rslt = -3;
				}

			}
		}
		//Valor "sobre exceso de" debe ser numerico
		if($rslt > 0){
			if(strlen($over_excess)>0 ){
				try{
				 	if(is_numeric($over_excess)) {
				 		if($over_excess < 0){
				 			$rslt = -4;
				 		}
				 	}
				 	else{
				 		$rslt = -4;	
				 	}
				}
				catch(Exception $e){
					$rslt = -4;
				}

			}
		}


		//Valor "cuota fija" deber ser numerico
		if($rslt > 0){
			if(strlen($fixedFee) >0 ){
				try{
				 	if(is_numeric($fixedFee)) {
				 		if($fixedFee < 0){
				 			$rslt = -5;
				 		}
				 	}
				 	else{
				 		$rslt = -5;	
				 	}
				}
				catch(Exception $e){
					$rslt = -5;
				}

			}
		}


		//Insertando datos
		if($rslt > 0){

			if(strlen($bottonAmount)==0){$bottonAmount="null";}
			if(strlen($topAmount)==0){$topAmount="null";}
			if(strlen($overExcess)==0){$over_excess="null";}
			if(strlen($fixedFee)==0){$fixedFee="null";}
			if(strlen($maxQuotable)==0){$maxQuotable="null";}

			$sqlText = "insert into legal_discount(".
				"legaldisc_name, ".
				"geography_code, ".
				"taxable_remunation, ".
				"percentage, ".
				"botton_amount, ".
				"top_amount, ".
				"over_excess, ".
				"fixed_fee, ".
				"pension_flag, ".
				"max_quotable, ".
				"start_date) ".
				"values('".$name."',".
					"'".$country."',".
					"'".$flagCalculo."', ".
					$perc.", ".
					$bottonAmount.", ".
					$topAmount.", ".
					$over_excess.", ".
					$fixedFee.", ".
					"'".$flagPension."', ".
					$maxQuotable.", ".
					"'".$startDate."')";

			$dbEx->insSql($sqlText);
			if($dbEx->insertID > 0){
				$rslt = 2;
			}
		}
		echo $rslt;

	break;

	case 'updLegalDiscForm':
		$sqlText = "select legaldisc_name, ".
			"percentage, ".
			"taxable_remunation, ".
			"botton_amount, ".
			"top_amount, ".
			"over_excess, ".
			"fixed_fee, ".
			"pension_flag, ".
			"max_quotable, ".
			"date_format(ld.end_date,'%d/%m/%Y') disc_end_date, ".
			"gp.geography_code, ".
			"gp.geography_name ".
			"from legal_discount ld inner join geographies gp on ld.geography_code = gp.geography_code ".
			"where legaldisc_id = ".$_POST['discountId'];
		$dtDisc = $dbEx->selSql($sqlText);

		$sqlText = "select geography_code, geography_name ".
				"from geographies where geography_type = 'COUNTRY' ".
				"and ifnull(end_date,sysdate() + 1) > sysdate() ".
				"order by geography_name";

		$dtCountry = $dbEx->selSql($sqlText);
		$optCountry = "";
		foreach ($dtCountry as $dtC) {
			$sel = "";
			if($dtC['geography_code'] == $dtDisc['0']['geography_code']){
				$sel = "selected";
			}
			$optCountry .='<option value="'.$dtC['geography_code'].'" '.$sel.'>'.$dtC['geography_name'].'</option>';
		}
		
  		$rslt = '<table align = "center" class="tablaVerde">';
  		$rslt .= '<tr><td>Pais:</td><td> <select id="lsCountry'.$_POST['discountId'].'">'.$optCountry.'</select>';
  		$rslt .= '<tr><td>Nombre de descuento:</td><td> <input type="text" class="txtPag" id="txtLabelUpd'.$_POST['discountId'].'" value="'.$dtDisc['0']['legaldisc_name'].'"></td></tr>';
  		
  		$rslt .= '<tr><td>Calculo sobre:</td><td> <select id="lsFlagCalculo'.$_POST['discountId'].'">'.
					'<option value="NA">Seleccione una opci&oacute;n</option>';

					$sel = ''; if($dtDisc['0']['taxable_remunation']=='N'){ $sel = "selected";}
		$rslt .=	'<option value="N" '.$sel.'>Remuneraciones no gravadas</option>';

					$sel = ''; if($dtDisc['0']['taxable_remunation']=='Y'){ $sel = "selected";}
		$rslt .=    '<option value="Y" '.$sel.'>Remuneraciones gravadas</option>'.
				'</select></td></tr>';
  		$rslt .= '<tr><td>% de Descuento:</td><td> <input type="text" class="txtPag" id="txtPerc'.$_POST['discountId'].'" value="'.$dtDisc['0']['percentage'].'"></td></tr>';
  		$rslt .= '<tr><td>Monto desde:</td><td> <input type="text" id="txtBottonAmount'.$_POST['discountId'].'" class="txtPag" value="'.$dtDisc['0']['botton_amount'].'"></td></tr>';
  		$rslt .= '<tr><td>Monto hasta:</td><td> <input type="text" id="txtTopAmount'.$_POST['discountId'].'" class="txtPag" value="'.$dtDisc['0']['top_amount'].'"></td></tr>';
  		$rslt .= '<tr><td>Sobre exceso de:</td><td> <input type="text" id="txtOverExcess'.$_POST['discountId'].'" class="txtPag" value="'.$dtDisc['0']['over_excess'].'"></td></tr>';
  		$rslt .= '<tr><td>Cuota fija:</td><td> <input type="text" id="txtFixedFee'.$_POST['discountId'].'" class="txtPag" value="'.$dtDisc['0']['fixed_fee'].'"></td></tr>';

  		$rslt .= '<tr><td>Descuento de pensi&oacute;n?:</td><td> <select id="lsFlagPension'.$_POST['discountId'].'">';

					$sel = ''; if($dtDisc['0']['pension_flag']=='N'){ $sel = "selected";}
		$rslt .=	'<option value="N" '.$sel.'>No</option>';

					$sel = ''; if($dtDisc['0']['pension_flag']=='Y'){ $sel = "selected";}
		$rslt .=    '<option value="Y" '.$sel.'>Si</option>'.
				'</select></td></tr>';

		$rslt .= '<tr><td>Maximo cotizable:</td><td> <input type="text" id="txtMaxQuotable'.$_POST['discountId'].'" class="txtPag" value="'.$dtDisc['0']['max_quotable'].'"></td></tr>';

		$rslt .= '<tr><td>Fecha fin:</td><td><input type="text" class="txtPag" name="end_date'.$_POST['discountId'].'" id="end_date'.$_POST['discountId'].'" '.
		  	'value="'.$dtDisc['0']['disc_end_date'].'" size="15" class="txtPag" /><img src="images/calendar.jpg" align="center" onclick="return showCalendar('."'".'end_date'.$_POST['discountId'].''."'".', '."'".'%d/%m/%Y'."'".');" style="cursor:pointer;" /></td></tr> '.
		  	'<tr><td colspan = "2"><input type="button" onclick="saveUpdLegalDisc('.$_POST['discountId'].')" value="Guardar">'.
		  	'<td></tr></table';

		echo $rslt;

	break;

	case 'saveUpdLegalDisc':

		$discountId		= $_POST['discountId'];
		$country 		= $_POST['country'];
		$name 			= $_POST['name'];
		$perc 			= $_POST['perc'];
		$bottonAmount 	= $_POST['bottonAmount'];
		$topAmount 		= $_POST['topAmount'];
		$flagCalculo 	= $_POST['flagCalculo'];
		$overExcess 	= $_POST['overExcess'];
		$fixedFee 		= $_POST['fixedFee'];
		$flagPension	= $_POST['flagPension'];
		$maxQuotable    = $_POST['maxQuotable'];

		$rslt = 1;

		if(strlen($_POST['endDate']) > 0){
			$fecha = "'".$oFec->cvDtoY($_POST['endDate'])."'";
		}
		else{
			$fecha = "null";
		}

		$sqlText = "select count(1) c from legal_discount ".
					"where legaldisc_name = '".$name."' and geography_code = '".$country."' ".
					"and ifnull(end_date,sysdate() + 1) > sysdate() ".
					"and legaldisc_id <> ".$discountId;
			
	    $dtC = $dbEx->selSql($sqlText);
		//Validar si no existe un descuento para el mismo pais y con el mismo nombre

		if($dtC['0']['c'] <> "0"){
			$rslt = 0;
		}

		//Porcentaje ingresado debe ser mayor a 0
		if($rslt > 0){
			try{
				 if(is_numeric($perc)) {
				 	if($perc < 0){
				 		$rslt = -2;
				 	}
				 }else{$rslt = -2;}
			}
			catch(Exception $e){
				$rslt = -2;
			}
		}
		//Si se han ingresado montos limites deben ser valores numeros
		if($rslt > 0){
			if(strlen($bottonAmount) >0 and strlen($topAmount)>0 ){
				try{
				 	if(is_numeric($bottonAmount) and is_numeric($topAmount)) {
				 		if($bottonAmount < 0 or $topAmount < 0){
				 			$rslt = -3;
				 		}
				 	}
				 	else{
				 		$rslt = -3;	
				 	}
				}
				catch(Exception $e){
					$rslt = -3;
				}

			}
		}
		//Valor "sobre exceso de" debe ser numerico
		if($rslt > 0){
			if(strlen($over_excess)>0 ){
				try{
				 	if(is_numeric($over_excess)) {
				 		if($over_excess < 0){
				 			$rslt = -4;
				 		}
				 	}
				 	else{
				 		$rslt = -4;	
				 	}
				}
				catch(Exception $e){
					$rslt = -4;
				}

			}
		}
		//Valor "cuota fija" deber ser numerico
		if($rslt > 0){
			if(strlen($fixedFee) >0 ){
				try{
				 	if(is_numeric($fixedFee)) {
				 		if($fixedFee < 0){
				 			$rslt = -5;
				 		}
				 	}
				 	else{
				 		$rslt = -5;	
				 	}
				}
				catch(Exception $e){
					$rslt = -5;
				}

			}
		}


		if($rslt > 0){
	 		//Si el porcentaje es un valor numerico mayor a 1 hacer update

			if(strlen($bottonAmount)==0){$bottonAmount="null";}
			if(strlen($topAmount)==0){$topAmount="null";}
			if(strlen($overExcess)==0){$overExcess="null";}
			if(strlen($fixedFee)==0){$fixedFee="null";}

	 		$sqlText = "update legal_discount set ".
	 			"legaldisc_name = '".$name."', ".
	 			"taxable_remunation = '".$flagCalculo."', ".
				"geography_code = '".$country."', ".
				"percentage = ".$perc.", ". 
				"botton_amount=".$bottonAmount.", ".
				"top_amount=".$topAmount.", ".
				"over_excess=".$overExcess.", ".
				"fixed_fee=".$fixedFee.", ".
				"pension_flag='".$flagPension."', ".
				"max_quotable=".$maxQuotable.", ".
				"end_date = ".$fecha." ".
				"where legaldisc_id = ".$discountId;

			$dbEx->updSql($sqlText);
			$rows = $dbEx->affectedRows;

            if ($rows > 0) {
				$rslt = 2;
   			}
   			else{
   				$rslt = -1;
   			}
   		}
			
		echo $rslt;
		
	break;

}
?>
