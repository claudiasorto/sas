<?php 
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$fechaFunc = new OFECHA; 
function suma_fechas($fec,$ndias)
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

$csv = array();
//Verificamos que se ha seleccionado fecha
if(strlen($_POST['fecha'])==0){
	echo '<script>alert("You must select a date");</script>';
	die();
}
else if($_FILES['flDoc']['size']==0){
	echo '<script>alert("You must select a document in format CSV")</script>';
	die();
}
else{
	$ext = strtolower(end(explode('.',$_FILES['flDoc']['name']))); 
	$type = $_FILES['flDoc']['type'];
	$tmpName = $_FILES['flDoc']['tmp_name'];
	//Comprobar si el archivo es CSV
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			$row = 0;
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene valores del CSV
				$csv[$row]['row1'] = $data[0];
				$csv[$row]['row2'] = $data[1];
			
				$row++;

			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document in format CSV")</script>';
		die();	
	}
	$fecha = $fechaFunc->cvDtoY($_POST['fecha']);
	
	$ultimaFecha = DiasFecha($_POST['fecha'],"1","restar");
	
	$sqlText = "select paystub_id, date_format(paystub_fin,'%d/%m/%Y')as maxFecPay  from paystub where paystub_fin=(select max(paystub_fin) from paystub where paystub_fin<'".$fecha."')";
	
	$fechaUltimoPay = $dbEx->selSql($sqlText);

	$InicioPago = "";
	$ndias = 0;
	if($dbEx->numrows>0){
		$InicioPago = DiasFecha($fechaUltimoPay['0']['maxFecPay'],"1","sumar");
		$ndias = n_dias($InicioPago,$fecha);

	}
	
	
	//$ultimaFecha = DiasFecha(($fechaFunc->cvFecha($InicioPago)),"13","sumar");
	/*echo '<script>alert("'.$ultimaFecha.'");location.href="../index.php";</script>';*/
	
	for($i=0; $i< $row; $i++ ){
		$sqlText = "select employee_id from employees where username='".$csv[$i]['row1']."'";
		$dtE = $dbEx->selSql($sqlText);
		//verifica que el badge existe
		if($dbEx->numrows>0){
			/*echo '<script>alert("'.$dtE['0']['employee_id'].'");window.parent.loadPage("../newPayroll.php");</script>';*/
			$totalHoras = 0;
			//Verifica que exista una fecha de inicio de periodo y que los dias entre la ultima fecha de pago y la fecha que se sube el payroll son 14 y que la fecha subida no es igual a la primera fecha de pago
			if(strlen($InicioPago)>0 and $ndias<=14 and $ndias>0){
				//Si exception=0 se registra el payroll, exception=1 se crea solo la exception, exception=2 crea payroll y exception con cantidad de horas de la diferencia.
				$exception = 0;
				$horasPay = $csv[$i]['row2'];
				//Suma las horas del payroll
				
				$sqlText = "select sum(payroll_htotal) as total from payroll where employee_id=".$dtE['0']['employee_id']." and payroll_date between date '".$InicioPago."' and '".$ultimaFecha."'";
				/*echo '<script>alert("'.$sqlText.'");location.href="../index.php";</script>';*/
				
				$totalPayroll = $dbEx->selSql($sqlText);
				$totalHoras = $totalHoras + $totalPayroll['0']['total'];
				//Suma horas de las exception en el periodo
				$sqlText = "select sum(HOUR(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as hora, sum(MINUTE(TIMEDIFF(exceptionemp_hfin, exceptionemp_hini))) as minutos from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id where ex.employee_id=".$dtE['0']['employee_id']." and (exceptionemp_date between date '".$InicioPago."' and '".$fecha."') and exceptionemp_approved='A' and exceptiontp_level=1 group by ex.employee_id";
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
				$sqlText = "select sum(hours_ap) as hap from apxemp where employee_id=".$dtE['0']['employee_id']." and id_tpap in(1,7) and hours_ap!='' and (startdate_ap between date '".$InicioPago."' and '".$fecha."') and approved_status='A'";
				$horasAp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$totalHoras = $totalHoras + $horasAp['0']['hap'];	
				}
				
				if($totalHoras>=88){
					$exception = 1;
					$horasExc = $csv[$i]['row2'];
				}
				else{
					$total = $totalHoras + $csv[$i]['row2'];
					if($total>88){
						$exception = 2;
						$horasExc = $total - 88;
						$horasPay = $csv[$i]['row2']-$horasExc;	
					}
					else{
						$exception = 0;
						$horasPay = $csv[$i]['row2'];	
					}	
				}
			}
			else{
				$exception = 0;	
				$horasPay = $csv[$i]['row2'];
			}
			//Ingresa el payroll unicamente
			if($exception ==0){
				$sqlText = "select payroll_id, payroll_daytime, payroll_nigth from payroll where payroll_date='".$fecha."' and employee_id=".$dtE['0']['employee_id'];
				$dtC = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$dia = $horasPay;
					$noche = 0;
					if($dtC['0']['payroll_nigth']>0){
						$dia = $horasPay - $dtC['0']['payroll_nigth'];
						$noche = $dtC['0']['payroll_nigth'];
					}
					$sqlText = "update payroll set payroll_htotal=".$horasPay.", payroll_daytime=".$dia.", payroll_nigth=".$noche." where payroll_id=".$dtC['0']['payroll_id'];
					$dbEx->updSql($sqlText);
				}
				else{
					$sqlText = "insert into payroll set employee_id=".$dtE['0']['employee_id'].", payroll_date='".$fecha."', payroll_htotal=".$horasPay.", payroll_daytime=".$horasPay.", payroll_nigth=0";
					$dbEx->insSql($sqlText);	
				}
			}
			//Ingresa la exception
			else if($exception ==1){
				$horasExc = number_format($horasExc,2);
				$formatH = explode(".", $horasExc);
				$horaP = zerofill($formatH[0],2);
				$minutosP = $formatH[1]; 
				$x = zerofill((($minutosP * 60)/100),2);
				$horaFin = $horaP.":".$x.":00";
				$sqlText = "insert into exceptionxemp set employee_id=".$dtE['0']['employee_id'].", exceptionemp_date='".$fecha."', exceptionemp_hini='00:00:00', exceptionemp_hfin='".$horaFin."', exceptiontp_id=9, exceptionemp_comment='exceeded the normal time of 88 hours'";
				$dbEx->insSql($sqlText);
				
				$sqlText = "select payroll_id, payroll_daytime, payroll_nigth from payroll where payroll_date='".$fecha."' and employee_id=".$dtE['0']['employee_id'];
				$dtC = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$sqlText = "delete from payroll where payroll_id=".$dtC['0']['payroll_id'];	
					$dbEx->updSql($sqlText);
				}
			
			}
			//Ingresa la hora del payroll y la hora de la exception
			else if($exception == 2){
				$horasExc = number_format($horasExc,2);
				$formatH = explode(".", $horasExc);
				$horaP = zerofill($formatH[0],2);
				$minutosP = $formatH[1]; 
				$x = zerofill((($minutosP * 60)/100),2);
				$horaFin = $horaP.':'.$x.':00';
				$sqlText = "insert into exceptionxemp set employee_id=".$dtE['0']['employee_id'].", exceptionemp_date='".$fecha."', exceptionemp_hini='00:00:00', exceptionemp_hfin='".$horaFin."', exceptiontp_id=9, exceptionemp_comment='exceeded the normal time of 88 hours'";
				$dbEx->insSql($sqlText);
				
				$sqlText = "select payroll_id, payroll_daytime, payroll_nigth from payroll where payroll_date='".$fecha."' and employee_id=".$dtE['0']['employee_id'];
				$dtC = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
				$dia = $horasPay;
					$noche = 0;
					if($dtC['0']['payroll_nigth']>0){
						$dia = $horasPay - $dtC['0']['payroll_nigth'];
						$noche = $dtC['0']['payroll_nigth'];
					}
					$sqlText = "update payroll set payroll_htotal=".$horasPay.", payroll_daytime=".$dia.", payroll_nigth=".$noche." where payroll_id=".$dtC['0']['payroll_id'];
					$dbEx->updSql($sqlText);
				}
				else{
					$sqlText = "insert into payroll set employee_id=".$dtE['0']['employee_id'].", payroll_date='".$fecha."', payroll_htotal=".$horasPay.", payroll_daytime=".$horasPay.", payroll_nigth=0";
					$dbEx->insSql($sqlText);	
				}
			}
		}
	}
	
	$rslt = 2;
	if($rslt ==2){
		echo '<script>alert("Upload successfully");window.parent.loadPage("../newPayroll.php");</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again"); return false;</script>';	
	}
}
?>
