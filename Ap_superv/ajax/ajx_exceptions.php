<?php 
//Funciones para Exceptions
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
  
function restaHoras($horaIni, $horaFin){
	return (date("H:i:s", strtotime("00:00:00") + strtotime($horaFin) - strtotime($horaIni) ));
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
function zerofill($entero, $largo){
    // Limpiamos por si se encontraran errores de tipo en las variables
    $entero = (int)$entero;
    $largo = (int)$largo;
     
    $relleno = '';
     
    /**
     * Determinamos la cantidad de caracteres utilizados por $entero
     * Si este valor es mayor o igual que $largo, devolvemos el $entero
     * De lo contrario, rellenamos con ceros a la izquierda del número
     **/
    if (strlen($entero) < $largo){
		$valor = $largo - strlen($entero);
        $relleno = str_repeat('0', $valor);
    }
    return $relleno . $entero;
}
function DiasFecha($fecha,$dias,$operacion){
	list($dia, $mes, $anio) = split('/',$fecha);
	$fecha = $anio.'-'.$mes.'-'.$dia;
  Switch($operacion){
    case "sumar":
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    return $varFecha;
    break;
    case "restar":
    $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
    return $varFecha;
    break;
    default:
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    break;
  }
}
function n_dias($fecha_desde,$fecha_hasta)
{
	$dias= (strtotime($fecha_desde)-strtotime($fecha_hasta))/86400;
	$dias = abs($dias); $dias = floor($dias);
	return  $dias;
}

switch($_POST['Do']){
	//Carga formulario de nueva exception
	case 'newException';
		$rslt = cargaPag("../exception/formException.php");
		//Selecciona los tipos de excepciones, si es supervisor le muestra todos los tipos y sino le muestra solo los designados en la base de datos segun el departamento del usuario
		if($_SESSION['usr_rol']=='SUPERVISOR' or $_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1";
		}
		else{
			$sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1 and (".$_SESSION['usr_depart']." in (exceptiontp_depart))";	
		}
		$dtEx = $dbEx->selSql($sqlText);
		$optEx = '<option value="0">Select a type exception</option>';
		foreach($dtEx as $dtE){
			$optEx .='<option value="'.$dtE['exceptiontp_id'].'">'.$dtE['exceptiontp_name'].'</option>';
		}
		//Selecciona los empleados
		
		//Si es supervisor le muestra sus agentes
		if($_SESSION['usr_rol']=='SUPERVISOR'){
			$sqlText = "select employee_id, username, firstname, lastname from employees where id_supervisor=".$_SESSION['usr_id'].' and user_status=1 order by firstname';
		}
		else{
			$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where pe.status_plxemp='A' and user_status=1 and (name_role='AGENTE' or name_role='SUPERVISOR') order by firstname";
			
		}
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		if($dbEx->numrows>0){
			$optEmp .='<option value="0">Select one employee</option>';
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].'&nbsp;'.$dtE['lastname'].'</option>';	
			}
		}
		else{
			$optEmp .='<option value="0">It has no employees supervised</option>';	
		}
		$optHora = "";
		for($i=0;$i<=23; $i++){
			$n = zerofill($i, 2);
			$optHora .='<option value="'.$n.'">'.$n.'</option>';
		}
		$optMinutos = "";
		for($i=0; $i<=59;$i++){
			$n = zerofill($i, 2);
			$optMinutos .='<option value="'.$n.'">'.$n.'</option>';	
		}
		$rslt = str_replace("<!--optExcep-->",$optEx,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optHora-->",$optHora,$rslt);
		$rslt = str_replace("<!--optMinutos-->",$optMinutos,$rslt);
		echo $rslt;
	break;
	
	case 'newExceptionAllEmp':
		$rslt = cargaPag("../exception/formException.php");
		//Selecciona los tipos de excepciones
		$sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1";
		$dtEx = $dbEx->selSql($sqlText);
		$optEx = '<option value="0">Select a type exception</option>';
		foreach($dtEx as $dtE){
			$optEx .='<option value="'.$dtE['exceptiontp_id'].'">'.$dtE['exceptiontp_name'].'</option>';
		}
		//Selecciona los empleados
		
		//Si user es Gerente de area solo muestra agentes y supervisores
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join user_roles u on u.id_role=pd.id_role where user_status=1 and pe.status_plxemp='A' and (u.name_role='AGENTE' or u.name_role='SUPERVISOR') order by firstname";
			
		}
		else{
			$sqlText = "select employee_id, username, firstname, lastname from employees where user_status=1 order by firstname";
		}
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		if($dbEx->numrows>0){
			$optEmp .='<option value="0">Select an employee</option>';
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].'&nbsp;'.$dtE['lastname'].'</option>';	
			}
		}
		else{
			$optEmp .='<option value="0">It has no employees supervised</option>';	
		}
		$optHora = "";
		for($i=0;$i<=23; $i++){
			$n = zerofill($i, 2);
			$optHora .='<option value="'.$n.'">'.$n.'</option>';
		}
		$optMinutos = "";
		for($i=0; $i<=59;$i++){
			$n = zerofill($i, 2);
			$optMinutos .='<option value="'.$n.'">'.$n.'</option>';	
		}
		$rslt = str_replace("<!--optExcep-->",$optEx,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optHora-->",$optHora,$rslt);
		$rslt = str_replace("<!--optMinutos-->",$optMinutos,$rslt);
		echo $rslt;
	break;
	
	//Guarda una nueva exception
	case 'saveException':
		$horaIni = $_POST['horaInicial'].":".$_POST['minutoInicial'].":00";
		$horaFin = $_POST['horaFinal'].":".$_POST['minutoFinal'].":00";
		$fecha = $oFec->cvDtoY($_POST['fecha']);
		//Verificamos el tipo de exception seleccionada
		$sqlText = "select exceptiontp_level from exceptions_type where exceptiontp_id=".$_POST['razon'];
		$nivelExc = $dbEx->selSql($sqlText);
		if($nivelExc['0']['exceptiontp_level']==1){
			//Comprueba si ya posee 88 horas, de ser asi cambia la Exception a Hora adicional.
			$sqlText = "select paystub_id, date_format(paystub_fin,'%d/%m/%Y')as maxFecPay  from paystub where paystub_fin=(select max(paystub_fin) from paystub where paystub_fin<='".$fecha."')";

			$fechaUltimoPay = $dbEx->selSql($sqlText);
			$InicioPago = "";
			$ndias = 0;
			if($dbEx->numrows>0){
				$InicioPago = DiasFecha($fechaUltimoPay['0']['maxFecPay'],"1","sumar");
				$ndias = n_dias($InicioPago,$fecha);
			}
			$totalHoras = 0;
			$horasPagar = restaHoras($horaIni,$horaFin);
			$formatHora = explode(":",$horasPagar);
			$minutos = number_format((($formatHora[1]*100)/60),0);
			$horasPagar  = $formatHora[0].'.'.$minutos;
			if(strlen($InicioPago)>0 and $ndias<=14 and $ndias>0){
				$exception = 0;
				
				//Suma las horas del payroll
				$sqlText = "select sum(payroll_htotal) as total from payroll where employee_id=".$_POST['empleado']." and payroll_date between date '".$InicioPago."' and '".$fecha."'";
				$totalPayroll = $dbEx->selSql($sqlText);
				$totalHoras = $totalHoras + $totalPayroll['0']['total'];
				//Suma horas de las exception en el periodo
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$_POST['empleado']." and (exceptionemp_date between date '".$InicioPago."' and '".$fecha."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
				$dtEx = $dbEx->selSql($sqlText);
				$horasException = 0;
				if($dbEx->numrows>0){
					$horas = $dtEx['0']['hora']; 
					$min = $dtEx['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasException = $horas.".".$formatMinutos[1];	
				}
				$totalHoras = $totalHoras + $horasException;
				//Suma horas de AP
				$sqlText = "select sum(hours_ap) as hap from apxemp where employee_id=".$_POST['empleado']." and id_tpap in(1,7) and hours_ap!='' and (startdate_ap between date '".$InicioPago."' and '".$fecha."') and approved_status='A'";
				$horasAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$totalHoras = $totalHoras + $horasAp['0']['hap'];	
				}
				if($totalHoras>=88){
					$exception = 1;
					$horasExc = $horasPagar;
				}
				else{
					$total = $totalHoras + $horasPagar;
					if($total>88){
						$exception = 2;
						$horasExc = $total - 88;
						$horasPagar = $horasPagar-$horasExc;	
					}
					else{
						$exception = 0;	
					}	
				}
			}
			else{
				$exception = 0;	
			}
			
			//Si el departamento del creador de la exception corresponde al tipo de exception que ese departamento aprueba sera aprobada la exception de forma automatica
			$insert ="";
			$sqlText = "select count(1) as ce from exceptions_type where exceptiontp_id=".$_POST['razon']." and ".$_SESSION['usr_depart']." in (exceptiontp_depart)";
			$dtCountEx = $dbEx->selSql($sqlText);
			if($dtCountEx['0']['ce']!=NULL and $dtCountEx['0']['ce']>0){
				$insert .=", exceptionemp_authorizer=".$_SESSION['usr_id'];
			}
			
			//Ingresa la excepcion normal
			$comment = $_POST['comment'];
			if(strlen($_POST['ticket']>0)){
				$comment = "Ticket: ".$_POST['ticket']." ".$_POST['comment'];
			}
			if($exception ==0){
				$sqlText = "insert into exceptionxemp set employee_id=".$_POST['empleado'].", exceptionemp_date='".$fecha."', exceptionemp_hini='".$horaIni."', exceptionemp_hfin='".$horaFin."', exceptiontp_id=".$_POST['razon'].", exceptionemp_comment='".$comment."', exceptionemp_creator=".$_SESSION['usr_id']." ".$insert;
				$dbEx->insSql($sqlText);
				$sqlText = "select max(exceptionemp_id) as id from exceptionxemp where employee_id=".$_POST['empleado'];
				$dtEx = $dbEx->selSql($sqlText);
				$rslt = $dtEx['0']['id'];
			}
			//Ingresa exception de horas adicionales
			else if($exception ==1){
				$sqlText = "insert into exceptionxemp set employee_id=".$_POST['empleado'].", exceptionemp_date='".$fecha."', exceptionemp_hini='".$horaIni."', exceptionemp_hfin='".$horaFin."', exceptiontp_id=9, exceptionemp_comment='".$comment."', exceptionemp_creator=".$_SESSION['usr_id'];
				
				$sqlText = "select max(exceptionemp_id) as id from exceptionxemp where employee_id=".$_POST['empleado'];
				$dtEx = $dbEx->selSql($sqlText);
				$rslt = $dtEx['0']['id'];
			}
			else if($exception ==2){
				//Ingresa parte de las horas exception del tipo seleccionado por el usuario
				$horasPagar = number_format($horasPagar,2);
				$formatH = explode(".",$horasPagar);
				$horaP = zerofill($formatH[0],2);
				$minutosP = $formatH[1];
				$x = zerofill((($minutosP * 60)/100),2);
				$horasFin = $horaP.':'.$x.':00';
				$nuevahoraFin = sumarHoras($horaIni,$horasFin); 
				$comentario = $comment." Note: The difference of hours entered for this exception are in the following exception of additional hours because the agent fulfilled over 88 hours";
				
				$sqlText = "insert into exceptionxemp set employee_id=".$_POST['empleado'].", exceptionemp_date='".$fecha."', exceptionemp_hini='".$horaIni."', exceptionemp_hfin='".$nuevahoraFin."', exceptiontp_id=".$_POST['razon'].", exceptionemp_comment='".$comentario."', exceptionemp_creator=".$_SESSION['usr_id']." ".$insert; 
				
				$dbEx->insSql($sqlText);
				$sqlText = "select max(exceptionemp_id) as id from exceptionxemp where employee_id=".$_POST['empleado'];
				$dtEx = $dbEx->selSql($sqlText);
				//$rslt = $horasFin." ".$nuevahoraFin." ".$horasPagar ;
				$rslt = $dtEx['0']['id'];
				
				//Ingresa exception de horas adicionales
				$horasExc = number_format($horasExc,2);
				$formatH = explode(".", $horasExc);
				$horaP = zerofill($formatH[0],2);
				$minutosP = $formatH[1]; 
				$x = zerofill((($minutosP * 60)/100),2);
				$horasFin = $horaP.':'.$x.':00';
				$nuevahoraIni = $nuevahoraFin;
				$nuevahoraFin = sumarHoras($nuevahoraIni,$horasFin);
				$comentario = $comment." Additional hours created by exception ".$rslt;
				$sqlText = "insert into exceptionxemp set employee_id=".$_POST['empleado'].", exceptionemp_date='".$fecha."', exceptionemp_hini='".$nuevahoraIni."', exceptionemp_hfin='".$nuevahoraFin."', exceptiontp_id=9, exceptionemp_comment='".$comentario."', exceptionemp_creator=".$_SESSION['usr_id']; 
				$dbEx->insSql($sqlText);
				
			}
	
		} //Fin de comprobacion de tipo de exception de nivel 1
		else{
		$sqlText = "insert into exceptionxemp set employee_id=".$_POST['empleado'].", exceptionemp_date='".$fecha."', exceptionemp_hini='".$horaIni."', exceptionemp_hfin='".$horaFin."', exceptiontp_id=".$_POST['razon'].", exceptionemp_comment='".$comment."', exceptionemp_creator=".$_SESSION['usr_id'];
		$dbEx->insSql($sqlText);
		$sqlText = "select max(exceptionemp_id) as id from exceptionxemp where employee_id=".$_POST['empleado'];
		$dtEx = $dbEx->selSql($sqlText);
			$rslt = $dtEx['0']['id'];
		}
		echo $rslt;
	break;
	
	//Carga los datos de una exception ya registrada
	case 'loadException':
		$sqlText = "select ex.exceptionemp_id, ex.employee_id, date_format(exceptionemp_date,'%d/%m/%Y') as f1 , exceptionemp_hini, exceptionemp_hfin, ex.exceptiontp_id, exceptionemp_comment, tp.exceptiontp_name, username, firstname, lastname, id_supervisor from exceptionxemp ex inner join employees e on ex.employee_id=e.employee_id inner join exceptions_type tp on ex.exceptiontp_id=tp.exceptiontp_id where exceptionemp_id=".$_POST['idE'];
		$dtEx = $dbEx->selSql($sqlText);
		if($_POST['opcion']==1){ //si la opcion es 1 mostramos los datos
			$rslt = cargaPag("../exception/dataException.php");
		}
		else if($_POST['opcion']==2){ //si la opcion es 2 se actualiza la exception
			$sqlText = "select exceptionemp_approved from exceptionxemp where exceptionemp_id=".$_POST['idE'];
			$dtAprov = $dbEx->selSql($sqlText);
			if($dtAprov['0']['exceptionemp_approved']=='A'){ //Si la exception ya fue aprobada ya no se podra modificar
				$rslt = -1;	
				echo $rslt;
				break;
			}
			else{
			$rslt = cargaPag("../exception/updateException.php");
			
			$sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1";
			$dtTpEx = $dbEx->selSql($sqlText);
			$optTp = "";
			foreach($dtTpEx as $dtTp){
				$sel = "";
				if($dtTp['exceptiontp_id']==$dtEx['0']['exceptiontp_id']){
					$sel = "selected";	
				}
				$optTp .='<option value="'.$dtTp['exceptiontp_id'].'" '.$sel.'>'.$dtTp['exceptiontp_name'].'</option>';
			}
			$tiempoIni = explode(":",$dtEx['0']['exceptionemp_hini']);
			$tiempoFin = explode(":",$dtEx['0']['exceptionemp_hfin']);
			$optHoraIni = "";
			for($i=0;$i<=23; $i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoIni[0]==$n){ $sel = "selected";}
				$optHoraIni .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';
			}
			$optMinutosIni = "";
			for($i=0; $i<=59;$i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoIni[1]==$n){$sel="selected";}
				$optMinutosIni .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';	
			}
			$optHoraFin = "";
			for($i=0;$i<=23; $i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoFin[0]==$n){ $sel = "selected";}
				$optHoraFin .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';
			}
			$optMinutosFin = "";
			for($i=0; $i<=59;$i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoFin[1]==$n){$sel="selected";}
				$optMinutosFin .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';	
			}
			$rslt = str_replace("<!--optException-->",$optTp,$rslt);
			$rslt = str_replace("<!--horasIni-->",$optHoraIni,$rslt);
			$rslt = str_replace("<!--minutosIni-->",$optMinutosIni,$rslt);
			$rslt = str_replace("<!--horasFin-->",$optHoraFin,$rslt);
			$rslt = str_replace("<!--minutosFin-->",$optMinutosFin,$rslt);
			
		}
		}
		$sqlText = "select firstname, lastname from employees where  employee_id=".$dtEx['0']['id_supervisor'];
		$dtSup = $dbEx->selSql($sqlText);
		$nombreSup = "";
		if($dbEx->numrows>0){
			$nombreSup = $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];	
		}
		
		$rslt = str_replace("<!--exception_id-->",$dtEx['0']['exceptionemp_id'],$rslt);
		$rslt = str_replace("<!--employee_id-->",$dtEx['0']['employee_id'],$rslt);
		$rslt = str_replace("<!--date-->",$dtEx['0']['f1'],$rslt);
		$rslt = str_replace("<!--horaIni-->",$dtEx['0']['exceptionemp_hini'],$rslt);
		$rslt = str_replace("<!--horaFin-->",$dtEx['0']['exceptionemp_hfin'],$rslt);
		$rslt = str_replace("<!--idTpException-->",$dtEx['0']['exceptiontp_id'],$rslt);
		$rslt = str_replace("<!--comment-->",$dtEx['0']['exceptionemp_comment'],$rslt);
		$rslt = str_replace("<!--tp_name-->",$dtEx['0']['exceptiontp_name'],$rslt);
		$rslt = str_replace("<!--badge-->",$dtEx['0']['username'],$rslt);
		$rslt = str_replace("<!--nombre-->",$dtEx['0']['firstname'],$rslt);
		$rslt = str_replace("<!--apellido-->",$dtEx['0']['lastname'],$rslt);
		$rslt = str_replace("<!--supervisor-->",$nombreSup,$rslt);
		
		echo $rslt;
		
	break;
	
	
	//Carga los datos de una exception ya registrada
	case 'loadExceptionReport':
		$sqlText = "select ex.exceptionemp_id, ex.employee_id, date_format(exceptionemp_date,'%d/%m/%Y') as f1 , exceptionemp_hini, exceptionemp_hfin, ex.exceptiontp_id, exceptionemp_comment, tp.exceptiontp_name, username, firstname, lastname, id_supervisor from exceptionxemp ex inner join employees e on ex.employee_id=e.employee_id inner join exceptions_type tp on ex.exceptiontp_id=tp.exceptiontp_id where exceptionemp_id=".$_POST['idE'];
		$dtEx = $dbEx->selSql($sqlText);
		if($_POST['opcion']==1){ //si la opcion es 1 mostramos los datos
			$rslt = cargaPag("../exception/dataExceptionReport.php");
		}
		else if($_POST['opcion']==2){ //si la opcion es 2 se actualiza la exception
			$sqlText = "select exceptionemp_approved from exceptionxemp where exceptionemp_id=".$_POST['idE'];
			$dtAprov = $dbEx->selSql($sqlText);
			if($dtAprov['0']['exceptionemp_approved']=='A'){ //Si la exception ya fue aprobada ya no se podra modificar
				$rslt = -1;	
				echo $rslt;
				break;
			}
			else{
			$rslt = cargaPag("../exception/updateExceptionReport.php");
			
			$sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1";
			$dtTpEx = $dbEx->selSql($sqlText);
			$optTp = "";
			foreach($dtTpEx as $dtTp){
				$sel = "";
				if($dtTp['exceptiontp_id']==$dtEx['0']['exceptiontp_id']){
					$sel = "selected";	
				}
				$optTp .='<option value="'.$dtTp['exceptiontp_id'].'" '.$sel.'>'.$dtTp['exceptiontp_name'].'</option>';
			}
			$tiempoIni = explode(":",$dtEx['0']['exceptionemp_hini']);
			$tiempoFin = explode(":",$dtEx['0']['exceptionemp_hfin']);
			$optHoraIni = "";
			for($i=0;$i<=23; $i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoIni[0]==$n){ $sel = "selected";}
				$optHoraIni .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';
			}
			$optMinutosIni = "";
			for($i=0; $i<=59;$i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoIni[1]==$n){$sel="selected";}
				$optMinutosIni .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';	
			}
			$optHoraFin = "";
			for($i=0;$i<=23; $i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoFin[0]==$n){ $sel = "selected";}
				$optHoraFin .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';
			}
			$optMinutosFin = "";
			for($i=0; $i<=59;$i++){
				$sel = "";
				$n = zerofill($i, 2);
				if($tiempoFin[1]==$n){$sel="selected";}
				$optMinutosFin .='<option value="'.$n.'" '.$sel.'>'.$n.'</option>';	
			}
			$rslt = str_replace("<!--optException-->",$optTp,$rslt);
			$rslt = str_replace("<!--horasIni-->",$optHoraIni,$rslt);
			$rslt = str_replace("<!--minutosIni-->",$optMinutosIni,$rslt);
			$rslt = str_replace("<!--horasFin-->",$optHoraFin,$rslt);
			$rslt = str_replace("<!--minutosFin-->",$optMinutosFin,$rslt);
			
		}
		}
		$sqlText = "select firstname, lastname from employees where  employee_id=".$dtEx['0']['id_supervisor'];
		$dtSup = $dbEx->selSql($sqlText);
		$nombreSup = "";
		if($dbEx->numrows>0){
			$nombreSup = $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];	
		}
		
		$rslt = str_replace("<!--exception_id-->",$dtEx['0']['exceptionemp_id'],$rslt);
		$rslt = str_replace("<!--employee_id-->",$dtEx['0']['employee_id'],$rslt);
		$rslt = str_replace("<!--date-->",$dtEx['0']['f1'],$rslt);
		$rslt = str_replace("<!--horaIni-->",$dtEx['0']['exceptionemp_hini'],$rslt);
		$rslt = str_replace("<!--horaFin-->",$dtEx['0']['exceptionemp_hfin'],$rslt);
		$rslt = str_replace("<!--idTpException-->",$dtEx['0']['exceptiontp_id'],$rslt);
		$rslt = str_replace("<!--comment-->",$dtEx['0']['exceptionemp_comment'],$rslt);
		$rslt = str_replace("<!--tp_name-->",$dtEx['0']['exceptiontp_name'],$rslt);
		$rslt = str_replace("<!--badge-->",$dtEx['0']['username'],$rslt);
		$rslt = str_replace("<!--nombre-->",$dtEx['0']['firstname'],$rslt);
		$rslt = str_replace("<!--apellido-->",$dtEx['0']['lastname'],$rslt);
		$rslt = str_replace("<!--supervisor-->",$nombreSup,$rslt);
		
		echo $rslt;
		
	break;
	
	//Guarda actualizacion de exception
	case 'saveUpdateException':
		$fecha = "";
		$tiempoIni = $_POST['horaIni'].":".$_POST['minutosIni'].":00";
		$tiempoFin = $_POST['horaFin'].":".$_POST['minutosFin'].":00";
		if(isset($_POST['fecha']) && $_POST['fecha']!=''){
			$fecha = $oFec->cvDtoY($_POST['fecha']);
		}
		//Verificamos el tipo de exception seleccionada
		$sqlText = "select exceptiontp_level from exceptions_type where exceptiontp_id=".$_POST['razon'];
		$nivelExc = $dbEx->selSql($sqlText);
		if($nivelExc['0']['exceptiontp_level']==1){
			//Id de empleado
			$sqlText = "select employee_id from exceptionxemp where exceptionemp_id=".$_POST['idEx'];
			$dtEmp = $dbEx->selSql($sqlText);
			
			//Comprueba si ya posee 88 horas, de ser asi cambia la Exception a Hora adicional.
			$sqlText = "select paystub_id, date_format(paystub_fin,'%d/%m/%Y')as maxFecPay  from paystub where paystub_fin=(select max(paystub_fin) from paystub where paystub_fin<='".$fecha."')";
			
			$fechaUltimoPay = $dbEx->selSql($sqlText);
			$InicioPago = "";
			$ndias = 0;
			if($dbEx->numrows>0){
				$InicioPago = DiasFecha($fechaUltimoPay['0']['maxFecPay'],"1","sumar");
				$ndias = n_dias($InicioPago,$fecha);
			}
			$totalHoras = 0;
			$horasPagar = restaHoras($tiempoIni,$tiempoFin);
			$formatHora = explode(":",$horasPagar);
			$minutos = number_format((($formatHora[1]*100)/60),0);
			$horasPagar  = $formatHora[0].'.'.$minutos;
			if(strlen($InicioPago)>0 and $ndias<=14 and $ndias>0){
				$exception = 0;
				
				//Suma las horas del payroll
				$sqlText = "select sum(payroll_htotal) as total from payroll where employee_id=".$dtEmp['0']['employee_id']." and payroll_date between date '".$InicioPago."' and '".$fecha."'";
				$totalPayroll = $dbEx->selSql($sqlText);
				$totalHoras = $totalHoras + $totalPayroll['0']['total'];
				//Suma horas de las exception en el periodo
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtEmp['0']['employee_id']." and (exceptionemp_date between date '".$InicioPago."' and '".$fecha."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
				$dtEx = $dbEx->selSql($sqlText);
				$horasException = 0;
				if($dbEx->numrows>0){
					$horas = $dtEx['0']['hora']; 
					$min = $dtEx['0']['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasException = $horas.".".$formatMinutos[1];	
				}
				$totalHoras = $totalHoras + $horasException;
				//Suma horas de AP
				$sqlText = "select sum(hours_ap) as hap from apxemp where employee_id=".$dtEmp['0']['employee_id']." and id_tpap in(1,7) and hours_ap!='' and (startdate_ap between date '".$InicioPago."' and '".$fecha."') and approved_status='A'";
				$horasAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$totalHoras = $totalHoras + $horasAp['0']['hap'];	
				}
				if($totalHoras>=88){
					$exception = 1;
					$horasExc = $horasPagar;
				}
				else{
					$total = $totalHoras + $horasPagar;
					if($total>88){
						$exception = 2;
						$horasExc = $total - 88;
						$horasPagar = $horasPagar-$horasExc;	
					}
					else{
						$exception = 0;	
					}	
				}
			}
			else{
				$exception = 0;	
			}
			//Ingresa la excepcion normal
			if($exception ==0){
				$sqlText = "update exceptionxemp set exceptionemp_date='".$fecha."', exceptionemp_hini='".$tiempoIni."', exceptionemp_hfin='".$tiempoFin."', exceptiontp_id=".$_POST['razon'].", exceptionemp_comment='".$_POST['comment']."' where exceptionemp_id=".$_POST['idEx'];
			$dbEx->updSql($sqlText);
			}
			else if($exception ==1){
				$sqlText = "update exceptionxemp set exceptionemp_date='".$fecha."', exceptionemp_hini='".$tiempoIni."', exceptionemp_hfin='".$tiempoFin."', exceptiontp_id=9, exceptionemp_comment='".$_POST['comment']."' where exceptionemp_id=".$_POST['idEx'];
			$dbEx->updSql($sqlText);	
			
			}
			else if($exception ==2){
				//Actualiza la exception segun lo seleccionado
				$horasPagar = number_format($horasPagar,2);
				$formatH = explode(".",$horasPagar);
				$horaP = zerofill($formatH[0],2);
				$minutosP = $formatH[1];
				$x = zerofill((($minutosP * 60)/100),2);
				$horasFin = $horaP.':'.$x.':00';
				$nuevahoraFin = sumarHoras($tiempoIni,$horasFin); 
				$comentario = $_POST['comment']." Note: The difference of hours entered for this exception are in the following exception of additional hours because the agent fulfilled over 88 hours";
				$sqlText = "update exceptionxemp set exceptionemp_date='".$fecha."', exceptionemp_hini='".$tiempoIni."', exceptionemp_hfin='".$nuevahoraFin."', exceptiontp_id=".$_POST['razon'].", exceptionemp_comment='".$comentario."' where exceptionemp_id=".$_POST['idEx'];
				$dbEx->updSql($sqlText);
				
				//ingresa la nueva exception
				$horasExc = number_format($horasExc,2);
				$formatH = explode(".", $horasExc);
				$horaP = zerofill($formatH[0],2);
				$minutosP = $formatH[1]; 
				$x = zerofill((($minutosP * 60)/100),2);
				$horasFin = $horaP.':'.$x.':00';
				$nuevahoraIni = $nuevahoraFin;
				$nuevahoraFin = sumarHoras($nuevahoraIni,$horasFin);
				$comentario = $_POST['comment']." Additional hours created by exception ".$_POST['idEx'];
				$sqlText = "insert into exceptionxemp set employee_id=".$dtEmp['0']['employee_id'].", exceptionemp_date='".$fecha."', exceptionemp_hini='".$nuevahoraIni."', exceptionemp_hfin='".$nuevahoraFin."', exceptiontp_id=9, exceptionemp_comment='".$comentario."', exceptionemp_creator=".$_SESSION['usr_id']; 
				$dbEx->insSql($sqlText);
				
			}
			
		}
		//Si la exception es nivel 2 no comprueba nada
		else{
			$sqlText = "update exceptionxemp set exceptionemp_date='".$fecha."', exceptionemp_hini='".$tiempoIni."', exceptionemp_hfin='".$tiempoFin."', exceptiontp_id=".$_POST['razon'].", exceptionemp_comment='".$_POST['comment']."' where exceptionemp_id=".$_POST['idEx'];
			$dbEx->updSql($sqlText);
		}
		
		echo $_POST['idEx'];
	break;
	
	//Funcion para generar reporte de exception de los empleados que supervisa
	case 'rptException':
		$rslt = cargaPag("../exception/filtroRptException.php");
		$filtro = "";
		//Selecciona los tipos de excepciones, si es supervisor le muestra todos los tipos y sino le muestra solo los designados en la base de datos segun el departamento del usuario
		if($_SESSION['usr_rol']=='SUPERVISOR' or $_SESSION['usr_rol']=='GERENTE DE AREA'){
		 $sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1";
		}
		else{
			$sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1 and (".$_SESSION['usr_depart']." in (exceptiontp_depart))";	
		}
		$dtEx = $dbEx->selSql($sqlText);
		foreach($dtEx as $dtE){
			$optEx .='<option value="'.$dtE['exceptiontp_id'].'">'.$dtE['exceptiontp_name'].'</option>';
		}
		//Selecciona los empleados
		if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro =" and (name_role='AGENTE' or name_role='SUPERVISOR') ";
		}
		else if($_SESSION['usr_rol']=='SUPERVISOR'){
			$filtro =" and id_supervisor=".$_SESSION['usr_id'];
		}
		else{
			$filtro = " and (name_role='AGENTE' or name_role='SUPERVISOR')";	
		}
		
		$sqlText = "select e.employee_id, username, firstname, lastname from employees e inner join plazaxemp pe on pe.employee_id=e.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role where status_plxemp='A' and user_status=1 ".$filtro." order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		if($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].'&nbsp;'.$dtE['lastname'].'</option>';	
			}
		}
		else{
			$optEmp .='<option value="0">It has no employees supervised</option>';	
		}
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optTpExc-->",$optEx,$rslt);
		echo $rslt;
	break;
	
	//Segun filtros seleccionados genera reporte de Exceptions
	case 'loadRptException':
		$filtro = " where user_status=1 and pe.status_plxemp='A' ";
		if($_SESSION['usr_rol']=='SUPERVISOR'){
			$filtro .=" and e.id_supervisor=".$_SESSION['usr_id'];
		}
		else if($_SESSION['usr_rol']=='GERENTE DE AREA'){
			$filtro .=" ";
		}
		else{
			$filtro .=" and (name_role='AGENTE' or name_role='SUPERVISOR') and ".$_SESSION['usr_depart']." in (exceptiontp_depart)";	
		}
		
		if(strlen($_POST['fechaIni'])>0){
			$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
			$filtro .= " and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."'";
		}
		if($_POST['tpException']>0){
			$filtro .= " and ex.exceptiontp_id=".$_POST['tpException'];
		}
		if($_POST['optEmp']>0){
			$filtro .= " and e.employee_id=".$_POST['optEmp'];	
		}
		if(isset($_POST['nombreEmp']) && $_POST['nombreEmp']!=''){
			$filtro .= " and (e.firstname like '%".strtoupper($_POST['nombreEmp'])."%' or e.lastname like '%".strtoupper($_POST['nombreEmp'])."%')";		
		}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			 $filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";
		}
		if($_POST['tpReport']==1){//Genera reporte en detalle
			$sqlText = "select distinct(ex.exceptionemp_id) as exceptionemp_id, e.employee_id, date_format(exceptionemp_date,'%d/%m/%Y') as f1, exceptionemp_hini, exceptionemp_hfin, ex.exceptiontp_id, exceptionemp_comment, exceptionemp_approved, exceptionemp_authorizer, exceptionemp_creator, exceptiontp_name, e.username, firstname, lastname from exceptionxemp ex inner join exceptions_type tp on ex.exceptiontp_id=tp.exceptiontp_id inner join employees e on e.employee_id=ex.employee_id inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join user_roles ur on ur.id_role=pd.id_role  ".$filtro." order by exceptionemp_date desc, ex.exceptionemp_id desc";
			$dtEx = $dbEx->selSql($sqlText);
			$tblExceptions ="";
			$tblExceptions .='<div id="lyReport"></div>';
			$tblExceptions .= '<table width="950" class="tblResult" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
			if($dbEx->numrows>0){
				$tblExceptions .='<tr><td colspan="9">Matches: '.$dbEx->numrows.'</td></tr>';
				$tblExceptions .='<tr class="showItem"><td width="2%">N&deg;</td><td width="5%">BADGE</td><td width="20%">EMPLOYEE</td><td width="10%">TYPE EXCEPTION</td><td width="5%">DATE</td><td width="5%">TOTAL TIME</td><td width="20%">OBSERVATIONS</td><td width="10%">STATUS</td><td width="3%"></td><td width="15%">CREATOR</td><td width="15%">AUTHORIZER</td></tr>';
				foreach($dtEx as $dtE){
					$estado = "";
					if($dtE['exceptionemp_approved']=='A'){
						$estado = '<font color="#009999"><b> Approved</font>';
					}
					else if($dtE['exceptionemp_approved']=='R'){
						$estado = '<font color="#990000"><b> Rejected</font>';
					}
					else if($dtE['exceptionemp_approved']=='P'){
						$estado = '<font color="#FF9900"><b>In progress</font>';	
					}
					//Busca el creador de la exception
					$creador = "";
					if($dtE['exceptionemp_creator']>0){
						$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['exceptionemp_creator'];
						$dtAutor = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){
							$creador = $dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'];
						}
					}
					
					//Busca el autorizador de la exception
					$autorizador = "";
					if($dtE['exceptionemp_authorizer']>0){
						$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['exceptionemp_authorizer'];
						$dtAutor = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){
							$autorizador = $dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'];
						}
					}
					
					$sqlText = "select TIMEDIFF( exceptionemp_hfin, exceptionemp_hini ) as diffHoras from exceptionxemp where exceptionemp_id= ".$dtE['exceptionemp_id'];
					$dtDiffHoras = $dbEx->selSql($sqlText);
					
					$tiempoTotal = $dtDiffHoras['0']['diffHoras'];
					
					//$tiempoTotal = restaHoras($dtE['exceptionemp_hini'],$dtE['exceptionemp_hfin']);
					$tblExceptions .='<tr class="rowCons">
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['exceptionemp_id'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['username'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['firstname']." ".$dtE['lastname'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['exceptiontp_name'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['f1'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$tiempoTotal.'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['exceptionemp_comment'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')" align="center">'.$estado.'</td>
					<td><img src="images/elim.png" title="Click to delete" onClick="deleteExceptionEmp('.$dtE['exceptionemp_id'].')"></td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$creador.'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$autorizador.'</td>
					</tr>';
				}
			}
			else{
				$tblExceptions .='<tr><td colspan="9">No matches</td></tr>';	
			}
			$tblExceptions .='</table>';
		}
		else if($_POST['tpReport']==2){ //Genera reporte totales
			
			$sqlText = "select e.employee_id, username, firstname, lastname, sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join employees e on e.employee_id=ex.employee_id ".$filtro." group by employee_id order by firstname";
			$dtEx = $dbEx->selSql($sqlText);
			$tblExceptions = '<table width="825" class="tblResult" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
			if($dbEx->numrows>0){
				$tblExceptions .='<tr><td colspan="4">Matches: '.$dbEx->numrows.'</td></tr>';
				$tblExceptions .='<tr class="showItem"><td width="10%">BADGE</td><td width="50%">EMPLOYEE</td><td width="30%">TOTAL TIME</td></tr>';	
				foreach($dtEx as $dtE){
					
					$horas = $dtE['hora']; 
					$min = $dtE['minutos']; 
					$minutos = $min%60; 
					$minutos = round($minutos/60,2);
					$formatMinutos = explode(".",$minutos);
					$h=0; 
					$h=(int)($min/60); 
					$horas+=$h;
					$horasException = $horas.".".$formatMinutos[1];
					
					$tblExceptions .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>'.round($horasException,2).'</td></tr>';
				}
			}
			else{
				$tblExceptions .='<tr><td colspan="4">No matches</td></tr>';	
			}
			$tblExceptions .= '</table>';
		}
	echo $tblExceptions;	
	break;
	//Funciones para el reporte de Exception con todos los empleados y todos los filtros
	case 'rptTotalException';
		$rslt = cargaPag("../exception/filtroRptTotalException.php");
		
		//Carga datos para filtros de exception
		$sqlText = "select exceptiontp_id, exceptiontp_name from exceptions_type where exceptiontp_status=1";
		$dtEx = $dbEx->selSql($sqlText);
		$optEx = "";
		foreach($dtEx as $dtE){
			$optEx .='<option value="'.$dtE['exceptiontp_id'].'">'.$dtE['exceptiontp_name'].'</option>';
		}
		//Selecciona los empleados
		$sqlText = "select employee_id, username, firstname, lastname from employees where user_status=1 order by firstname";
		$dtEmp = $dbEx->selSql($sqlText);
		$optEmp = "";
		if($dbEx->numrows>0){
			foreach($dtEmp as $dtE){
				$optEmp .='<option value="'.$dtE['employee_id'].'">'.$dtE['firstname'].'&nbsp;'.$dtE['lastname'].'</option>';	
			}
		}
		else{
			$optEmp .='<option value="0">No employees</option>';	
		}

		$sqlText = "select * from account order by name_account";
		$dtC = $dbEx->selSql($sqlText);
		$optC = "";
		foreach($dtC as $dtC){
				$optC .='<option value="'.$dtC['ID_ACCOUNT'].'">'.$dtC['NAME_ACCOUNT'].'</option>';
		}
		$sqlText ="select * from depart_exc where status_depart = 1 order by name_depart";
		$dtD = $dbEx->selSql($sqlText);
		
		$optD = '<select id="lsDpto" class="txtPag" onchange="getPoscFiltros(this.value)">';
		$optD .= '<option value="0">[ALL]</option>';
			foreach($dtD as $dtD){
				$optD .='<option value="'.$dtD['ID_DEPART'].'">'.$dtD['NAME_DEPART'].'</option>';	
			}
		$optD .='</select>';
		
		$sqlText = "select * from places order by name_place";
		$dtP = $dbEx->selSql($sqlText);
		$optP ='<select id="lsPosc" class="txtPag">[ALL]</option>';
		$optP .= '<option value="0">[ALL]</option>';
		foreach($dtP as $dtP){
			$optP .='<option value="'.$dtP['ID_PLACE'].'">'.$dtP['NAME_PLACE'].'</option>';	
		}
		$optP .='</select>';
		
		
		$sqlText = "select e.employee_id, firstname, lastname ".
				"from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id ".
				"inner join placexdep pd on pe.id_placexdep=pd.id_placexdep ".
				"inner join places pl on pd.id_place=pl.id_place ".
				"where pe.status_plxemp = 'A' and nivel_place=2 and user_status=1 order by firstname";
		$dtSup = $dbEx->selSql($sqlText);
		$optSup = "";
		foreach($dtSup as $dtS){
			$optSup .='<option value="'.$dtS['employee_id'].'">'.$dtS['firstname'].'&nbsp;'.$dtS['lastname'].'</option>';	
		}
		$rslt = str_replace("<!--optSuperv-->",$optSup,$rslt);
		$rslt = str_replace("<!--optCuenta-->",$optC,$rslt);
		$rslt = str_replace("<!--optDepart-->",$optD,$rslt);
		$rslt = str_replace("<!--optPlaza-->",$optP,$rslt);
		$rslt = str_replace("<!--optEmp-->",$optEmp,$rslt);
		$rslt = str_replace("<!--optTpExc-->",$optEx,$rslt);
		echo $rslt;
		
	break;
	case 'loadRptTotalException':
		$filtro = " where user_status=1 and pe.id_plxemp = get_idultimaplaza(e.employee_id) ";
		$filtroTotalEmp = " where user_status=1 and pe.id_plxemp = get_idultimaplaza(e.employee_id) ";
		$filtroTotalExc = "";
		if($_POST['type'] == 'pendientes'){

			$sqlText = "select e.employee_id, trim(name_place) name_place, name_depart ".
  			"from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id ".
  			"inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".
  			"inner join places p on p.id_place=pd.id_place ".
  			"inner join depart_exc d on d.id_depart = pd.id_depart ".
  			"where e.employee_id=".$_SESSION['usr_id'].
  			" and e.user_status=1 and status_plxemp='A'";

  			$dtPlaza = $dbEx->selSql($sqlText);

			$filtro .= " and exceptionemp_approved='P' ".
					"and (('".$dtPlaza['0']['name_place']."' = 'IT MANAGER' and tp.exceptiontp_id in (1,2,3,4) ) ".
					"or ('".$_SESSION['usr_rol']."' = 'WORKFORCE' ) ".
					"or ('".$_SESSION['usr_rol']."' = 'GERENCIA') )";
		}

		if($_POST['cuenta']>0){
			$filtro .= " and pd.id_account=".$_POST['cuenta'];
			$filtroTotalEmp .= " and pd.id_account=".$_POST['cuenta'];
		}
		if($_POST['depart']>0){
			$filtro .= " and pd.id_depart=".$_POST['depart'];
			$filtroTotalEmp .= " and pd.id_depart=".$_POST['depart'];
			}
		if($_POST['posicion']>0){
			$filtro .= " and pd.id_position=".$_POST['posicion'];
			$filtroTotalEmp .= " and pd.id_position=".$_POST['posicion'];
			}
		if($_POST['superv']>0){
			$filtro .= " and e.id_supervisor=".$_POST['superv'];
			$filtroTotalEmp .= " and e.id_supervisor=".$_POST['superv'];
			}
		if(strlen($_POST['fechaIni'])>0){
			$fechaIni = $oFec->cvDtoY($_POST['fechaIni']);
			$fechaFin = $oFec->cvDtoY($_POST['fechaFin']);
			$filtro .= " and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."'";
			$filtroTotalExc .= " and exceptionemp_date between date '".$fechaIni."' and '".$fechaFin."'";
		}
		if($_POST['tpException']>0){
			$filtro .= " and ex.exceptiontp_id=".$_POST['tpException'];
			$filtroTotalExc .= " and ex.exceptiontp_id=".$_POST['tpException'];
		}
		if($_POST['empleado']>0){
			$filtro .= " and e.employee_id=".$_POST['empleado'];
			$filtroTotalEmp .= 	" and e.employee_id=".$_POST['empleado'];
		}
		if(isset($_POST['nombre']) && $_POST['nombre']!=''){
			$filtro .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";	
			$filtroTotalEmp .= " and (e.firstname like '%".strtoupper($_POST['nombre'])."%' or e.lastname like '%".strtoupper($_POST['nombre'])."%')";	
		}
		if(isset($_POST['badge']) && $_POST['badge']!=''){
			 $filtro .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";
			 $filtroTotalEmp .= " and (e.username like '%".strtoupper($_POST['badge'])."%')";
		}
		if($_POST['status']){
			$filtro .=" and exceptionemp_approved='".$_POST['status']."' ";
			$filtroTotalExc .=" and exceptionemp_approved='".$_POST['status']."' ";
		}
		
		if($_POST['tpReport']==1){//Genera reporte en detalle
			$sqlText = "select distinct(ex.exceptionemp_id) as exceptionemp_id, ".
			" e.employee_id, date_format(exceptionemp_date,'%d/%m/%Y') as f1, ".
			" exceptionemp_hini, exceptionemp_hfin, ex.exceptiontp_id, ".
			" exceptionemp_comment, exceptionemp_approved, exceptionemp_authorizer, ".
			" exceptionemp_creator, exceptiontp_name, e.username, firstname, lastname ".
			" from exceptionxemp ex inner join exceptions_type tp on ex.exceptiontp_id=tp.exceptiontp_id ".
			" inner join employees e on e.employee_id=ex.employee_id ".
			" inner join plazaxemp pe on e.employee_id=pe.employee_id ".
			" inner join placexdep pd on pe.id_placexdep = pd.id_placexdep 
			".$filtro.
			" order by exceptionemp_date desc, ex.exceptionemp_id desc";
			$dtEx = $dbEx->selSql($sqlText);
			$tblExceptions ="";
			$tblExceptions .='<div id="lyReport"></div>';
			$tblExceptions .= '<table width="925" class="tblResult" bordercolor="#069" align="center" cellpadding="2" cellspacing="1">';
			if($dbEx->numrows>0){
				$tblExceptions .='<tr><td colspan="11">Matches: '.$dbEx->numrows.'</td>';
				$tblExceptions .='<td align="right"><form target="_blank" action="exception/xls_rptexceptionDetail.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtro" value="'.$filtro.'"></td></tr>';
				$tblExceptions .='<tr><td class="txtForm" colspan="12" align="center">DETAIL REPORT OF EXCEPTIONS TO '.$_POST['fechaIni'].' THE '.$_POST['fechaFin'].'</td></tr>';
				$tblExceptions .='<tr class="showItem">
				<td width="2%">N&deg;</td>
				<td width="5%">BADGE</td>
				<td width="20%">EMPLOYEE</td>
				<td width="10%">TYPE EXCEPTION</td>
				<td width="5%">DATE</td>
				<td width="5%">TOTAL TIME</td>
				<td width="20%">OBSERVATIONS</td>
				<td width="10%">STATUS</td>
				<td width="3%"></td>
				<td width="3%"></td>
				<td width="15%">CREATOR</td>
				<td width="15%">AUTHORIZER</td></tr>';

				foreach($dtEx as $dtE){
					$estado = "";
					$imagen = "";
					if($dtE['exceptionemp_approved']=='A'){
						$estado = '<font color="#009999"><b> Approved</font>';
						$imagen .='<img src="images/rejectedBtn1.png" title="Click to reject" width="30px" onclick="rejectException('.$dtE['exceptionemp_id'].')">&nbsp;&nbsp;';
					}
					else if($dtE['exceptionemp_approved']=='R'){
						$estado = '<font color="#990000"><b> Rejected</font>';
						$imagen = '<img src="images/list_check.png" title="Click to approve" onclick="aprovException('.$dtE['exceptionemp_id'].')">&nbsp;&nbsp;&nbsp;&nbsp;';
					}
					else if($dtE['exceptionemp_approved']=='P'){
						$estado = '<font color="#FF9900"><b>In progress</font>';
						$imagen = '<img src="images/list_check.png" title="Click to approve" onclick="aprovException('.$dtE['exceptionemp_id'].')">&nbsp;&nbsp;&nbsp;&nbsp;';
						$imagen .='<img src="images/rejectedBtn1.png" title="Click to reject" width="30px" onclick="rejectException('.$dtE['exceptionemp_id'].')">&nbsp;&nbsp;';	
					}
					
					$elim = "";
					if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='GERENTE DE AREA'){
						$elim = '<img src="images/elim.png" title="Click to delete" onClick="deleteException('.$dtE['exceptionemp_id'].')">';
					}
					
					//Busca el creador de la exception
					$creador = "";
					if($dtE['exceptionemp_creator']>0){
						$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['exceptionemp_creator'];
						$dtAutor = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){
							$creador = $dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'];
						}
					}
					
					//Busca el autorizador de la exception
					$autorizador = "";
					if($dtE['exceptionemp_authorizer']>0){
						$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['exceptionemp_authorizer'];
						$dtAutor = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){
							$autorizador = $dtAutor['0']['firstname']." ".$dtAutor['0']['lastname'];
						}
					}
					
					$sqlText = "select TIMEDIFF( exceptionemp_hfin, exceptionemp_hini ) as diffHoras from exceptionxemp where exceptionemp_id= ".$dtE['exceptionemp_id'];
					$dtDiffHoras = $dbEx->selSql($sqlText);
					
					$tiempoTotal = $dtDiffHoras['0']['diffHoras'];
					//$tiempoTotal = restaHoras($dtE['exceptionemp_hini'],$dtE['exceptionemp_hfin']);
					$tblExceptions .='<tr class="rowCons">
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['exceptionemp_id'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['username'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['firstname']." ".$dtE['lastname'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['exceptiontp_name'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['f1'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$tiempoTotal.'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')">'.$dtE['exceptionemp_comment'].'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')" align="center">'.$estado.'</td>
					<td>'.$imagen.'</td>
					<td align="center">'.$elim.'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')" align="center">'.$creador.'</td>
					<td onclick="loadExceptionReport('.$dtE['exceptionemp_id'].')" align="center">'.$autorizador.'</td>
					</tr>';
				}
			}
			else{
				$tblExceptions .='<tr><td colspan="9">No matches</td></tr>';	
			}
			$tblExceptions .='</table>';
		}
		else if($_POST['tpReport']==2){ //Genera reporte totales
			$sqlText = "select distinct(e.employee_id), e.username, firstname, lastname ".
						"from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id ".
						"inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".
						$filtroTotalEmp.
						" order by firstname";

			$dtEmp = $dbEx->selSql($sqlText);
			$tblExceptions = '<table width="825" class="tblResult" bordercolor="#069" align="center" cellpadding="2" cellspacing="2">';
			if($dbEx->numrows>0){
				$tblExceptions .='<tr><td colspan="2">Matches: '.$dbEx->numrows.'</td>';
				$tblExceptions .= '<td align="right"><form target="_blank" action="exception/xls_rptexception.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Export to excel" />&nbsp;&nbsp;<input type="hidden" name="filtroTotalEmp" value="'.$filtroTotalEmp.'"><input type="hidden" name="filtroTotalExc" value="'.$filtroTotalExc.'"></tr>';
				$tblExceptions .='<tr><td class="txtForm" colspan="3" align="center">TOTAL REPORT OF EXCEPTIONS TO '.$_POST['fechaIni'].' THE '.$_POST['fechaFin'].'</td></tr>';
				$tblExceptions .='<tr class="showItem"><td width="10%">BADGE</td><td width="50%">EMPLOYEE</td><td width="30%">TOTAL TIME</td></tr>';
				foreach($dtEmp as $dtE){
					$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex where ex.employee_id=".$dtE['employee_id']." ".$filtroTotalExc." group by employee_id ";
					$dtEx = $dbEx->selSql($sqlText);
					$horasException = "0.0";
					if($dbEx->numrows>0){
						$horas = $dtEx['0']['hora']; 
						$min = $dtEx['0']['minutos']; 
						$minutos = $min%60; 
						$minutos = round($minutos/60,2);
						$formatMinutos = explode(".",$minutos);
						$h=0; 
						$h=(int)($min/60); 
						$horas+=$h;
						$horasException = $horas.".".$formatMinutos[1];	
						$tblExceptions .='<tr class="rowCons"><td>'.$dtE['username'].'</td><td>'.$dtE['firstname'].' '.$dtE['lastname'].'</td><td>'.round($horasException,2).'</td></tr>';
					}
				}
			}
			else{
				$tblExceptions .='<tr><td colspan="4">No matches</td></tr>';
			}
			$tblExceptions .= '</table>';
		}
	echo $tblExceptions;			
			
	break;
	
	case 'deleteException':
		$sqlText = 'delete from exceptionxemp where exceptionemp_id='.$_POST['idE'];
		$dbEx->updSql($sqlText);
		echo "2";
	break; 
	
	case 'aprovException':
		$sqlText = "update exceptionxemp set exceptionemp_approved='A', exceptionemp_authorizer=".$_SESSION['usr_id']." where exceptionemp_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);
		echo "2";
	break;
	
	case 'rejectException':
		$sqlText = "update exceptionxemp set exceptionemp_approved='R', exceptionemp_authorizer=".$_SESSION['usr_id']." where exceptionemp_id=".$_POST['idE'];
		$dbEx->updSql($sqlText);
		echo "2";
	break;
}

?>
