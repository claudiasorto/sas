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
function sumarHoras($h1,$h2)
{
    $dbExec = new DBX;
	$sqlText = "select sec_to_time((time_to_sec('".$h1."') + time_to_sec('".$h2."'))) result from dual";
	$result = $dbExec->selSql($sqlText);
	return $result['0']['result'];

}
function restarHoras($h1,$h2)
{
    $dbExec = new DBX;
	$sqlText = "select time_format(sec_to_time(if((time_to_sec('".$h1."') - time_to_sec('".$h2."'))<0,0,".
		"(time_to_sec('".$h1."') - time_to_sec('".$h2."')))),'%H:%i:%s') result from dual";
	$result = $dbExec->selSql($sqlText);
	return $result['0']['result'];

}
function zerofill($entero, $largo){
    // Limpiamos por si se encontraran errores de tipo en las variables
    $entero = (int)$entero;
    $largo = (int)$largo;
     
    $relleno = '';
     
    /**
     * Determinamos la cantidad de caracteres utilizados por $entero
     * Si este valor es mayor o igual que $largo, devolvemos el $entero
     * De lo contrario, rellenamos con ceros a la izquierda del numero
     **/
    if (strlen($entero) < $largo){
		$valor = $largo - strlen($entero);
        $relleno = str_repeat('0', $valor);
    }
    return $relleno . $entero;
}

//Funcion para obtener la columna en la cual se ha cargado las horas logeados, tiempo en banio, tiempo en lunch
function get_vicidal_col($request_id,$p_proceso){
	
	$dbE = new DBX;
	
	$rslt = "NA";
	
	$sqlText = 
	"select 'COL1' columna from payroll_batch where payroll_request_id = ".$request_id." and col1 = '".$p_proceso."' union ".
	"select 'COL2' columna from payroll_batch where payroll_request_id = ".$request_id." and col2 = '".$p_proceso."' union ".
	"select 'COL3' columna from payroll_batch where payroll_request_id = ".$request_id." and col3 = '".$p_proceso."' union ".
	"select 'COL4' columna from payroll_batch where payroll_request_id = ".$request_id." and col4 = '".$p_proceso."' union ".
	"select 'COL5' columna from payroll_batch where payroll_request_id = ".$request_id." and col5 = '".$p_proceso."' union ".
	"select 'COL6' columna from payroll_batch where payroll_request_id = ".$request_id." and col6 = '".$p_proceso."' union ".
	"select 'COL7' columna from payroll_batch where payroll_request_id = ".$request_id." and col7 = '".$p_proceso."' union ".
	"select 'COL8' columna from payroll_batch where payroll_request_id = ".$request_id." and col8 = '".$p_proceso."' union ".
	"select 'COL9' columna from payroll_batch where payroll_request_id = ".$request_id." and col9 = '".$p_proceso."' union ".
	"select 'COL10' columna from payroll_batch where payroll_request_id = ".$request_id." and col10 = '".$p_proceso."' union ".
	"select 'COL11' columna from payroll_batch where payroll_request_id = ".$request_id." and col11 = '".$p_proceso."' union ".
	"select 'COL12' columna from payroll_batch where payroll_request_id = ".$request_id." and col12 = '".$p_proceso."' union ".
	"select 'COL13' columna from payroll_batch where payroll_request_id = ".$request_id." and col13 = '".$p_proceso."' union ".
	"select 'COL14' columna from payroll_batch where payroll_request_id = ".$request_id." and col14 = '".$p_proceso."' union ".
	"select 'COL15' columna from payroll_batch where payroll_request_id = ".$request_id." and col15 = '".$p_proceso."' union ".
	"select 'COL16' columna from payroll_batch where payroll_request_id = ".$request_id." and col16 = '".$p_proceso."' union ".
	"select 'COL17' columna from payroll_batch where payroll_request_id = ".$request_id." and col17 = '".$p_proceso."' union ".
	"select 'COL18' columna from payroll_batch where payroll_request_id = ".$request_id." and col18 = '".$p_proceso."' union ".
	"select 'COL19' columna from payroll_batch where payroll_request_id = ".$request_id." and col19 = '".$p_proceso."' union ".
	"select 'COL20' columna from payroll_batch where payroll_request_id = ".$request_id." and col20 = '".$p_proceso."' union ".
	"select 'COL21' columna from payroll_batch where payroll_request_id = ".$request_id." and col21 = '".$p_proceso."' union ".
	"select 'COL22' columna from payroll_batch where payroll_request_id = ".$request_id." and col22 = '".$p_proceso."' union ".
	"select 'COL23' columna from payroll_batch where payroll_request_id = ".$request_id." and col23 = '".$p_proceso."' union ".
	"select 'COL24' columna from payroll_batch where payroll_request_id = ".$request_id." and col24 = '".$p_proceso."' union ".
	"select 'COL25' columna from payroll_batch where payroll_request_id = ".$request_id." and col25 = '".$p_proceso."' union ".
	"select 'COL26' columna from payroll_batch where payroll_request_id = ".$request_id." and col26 = '".$p_proceso."' union ".
	"select 'COL27' columna from payroll_batch where payroll_request_id = ".$request_id." and col27 = '".$p_proceso."' union ".
	"select 'COL28' columna from payroll_batch where payroll_request_id = ".$request_id." and col28 = '".$p_proceso."' union ".
	"select 'COL29' columna from payroll_batch where payroll_request_id = ".$request_id." and col29 = '".$p_proceso."' union ".
	"select 'COL30' columna from payroll_batch where payroll_request_id = ".$request_id." and col30 = '".$p_proceso."'";
	
	$datos = $dbE->selSql($sqlText);
	if ($dbE->numrows > 0) {
  		$rslt = $datos['0']['columna'];
	}
		
	return $rslt;
	
}

$csv = array();

//Verificamos que se ha seleccionado fecha
if(strlen($_POST['fecha'])==0){
	echo '<script>alert("You must select a date");</script>';
	die();
}
else if(($_POST['lsPayrollType'])==0){
	echo '<script>alert("You must select a payroll type");</script>';
	die();
}
else if($_FILES['flDoc']['size']==0){
	echo '<script>alert("You must select a file in CSV format")</script>';
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
				$csv[$row]['row3'] = $data[2];
				$csv[$row]['row4'] = $data[3];
				$csv[$row]['row5'] = $data[4];
				$csv[$row]['row6'] = $data[5];
				$csv[$row]['row7'] = $data[6];
				$csv[$row]['row8'] = $data[7];
				$csv[$row]['row9'] = $data[8];
				$csv[$row]['row10'] = $data[9];
				$csv[$row]['row11'] = $data[10];
				$csv[$row]['row12'] = $data[11];
				$csv[$row]['row13'] = $data[12];
				$csv[$row]['row14'] = $data[13];
				$csv[$row]['row15'] = $data[14];
				$csv[$row]['row16'] = $data[15];
				$csv[$row]['row17'] = $data[16];
				$csv[$row]['row18'] = $data[17];
				$csv[$row]['row19'] = $data[18];
				$csv[$row]['row20'] = $data[19];
				$csv[$row]['row21'] = $data[20];
				$csv[$row]['row22'] = $data[21];
				$csv[$row]['row23'] = $data[22];
				$csv[$row]['row24'] = $data[23];
				$csv[$row]['row25'] = $data[24];
				$csv[$row]['row26'] = $data[25];
				$csv[$row]['row27'] = $data[26];
				$csv[$row]['row28'] = $data[27];
				$csv[$row]['row29'] = $data[28];
				$csv[$row]['row30'] = $data[29];
				$csv[$row]['row31'] = $data[30];
				$csv[$row]['row32'] = $data[31];
				
				$row++;

			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a file in format CSV")</script>';
		die();	
	}
	
	//Insertar la solicitud de carga de nomina
	$fecha = $fechaFunc->cvDtoY($_POST['fecha']);

	//Obtener la secuenta de la solicitud
	$sqlText = "SELECT auto_increment FROM information_schema.tables WHERE table_name='payroll_request' and table_schema = SCHEMA()";
	$req_id = $dbEx->selSql($sqlText);
	$request_id = $req_id['0']['auto_increment'];

	$sqlText = "insert into payroll_request( payroll_request_id, PAYROLL_TP_ID,CREATED_BY, REQUEST_DATE) ".
		" values(".$request_id.", ".$_POST['lsPayrollType'].",".$_SESSION['usr_id'].",'".$fecha."')";

	$dbEx->insSql($sqlText);
	
	//Evaluar el tipo de planilla para insertar los datos
	$sqlText = "select PAYROLL_TP_NAME from  payroll_type where PAYROLL_TP_ID =".$_POST['lsPayrollType'];
	
	$result = $dbEx->selSql($sqlText);
	$payroll_type = $result['0']['PAYROLL_TP_NAME'];

	//Insertar formato de acuerdo al tipo
	if ($payroll_type == "Vicidal"){
		//Insertar las lineas del csv
		for($i=0; $i< $row; $i++ ){		
			$sqlText = "insert into payroll_batch(PAYROLL_REQUEST_ID, EMPLOYEE_NAME, EMPLOYEE_ID, AGENT_ID, COL1, COL2, COL3, COL4, COL5, COL6, COL7, COL8, COL9, COL10, COL11, COL12, COL13, COL14, COL15, COL16, COL17, COL18, COL19, COL20, COL21, COL22, COL23, COL24, COL25, COL26, COL27, COL28, COL29, COL30) ";
			$sqlText .= "values (".$request_id.",'".$csv[$i]['row1']."', ifnull((select employee_id from employees where agent_id = '".$csv[$i]['row2']."'),0), ";
			$sqlText .= "'".$csv[$i]['row2']."','".$csv[$i]['row3']."','".$csv[$i]['row4']."','".$csv[$i]['row5']."','".$csv[$i]['row6']."','".$csv[$i]['row7']."','".$csv[$i]['row8']."','".$csv[$i]['row9']."','".$csv[$i]['row10']."','".$csv[$i]['row11']."','".$csv[$i]['row12']."','".$csv[$i]['row13']."','".$csv[$i]['row14']."',";
			$sqlText .= "'".$csv[$i]['row15']."','".$csv[$i]['row16']."','".$csv[$i]['row17']."','".$csv[$i]['row18']."','".$csv[$i]['row19']."','".$csv[$i]['row20']."','".$csv[$i]['row21']."','".$csv[$i]['row22']."','".$csv[$i]['row23']."','".$csv[$i]['row24']."','".$csv[$i]['row25']."','".$csv[$i]['row26']."','".$csv[$i]['row27']."','".$csv[$i]['row28']."','".$csv[$i]['row29']."','".$csv[$i]['row30']."','".$csv[$i]['row31']."','".$csv[$i]['row32']."')";

			$dbEx->insSql($sqlText);
		}
 	}
	else if($payroll_type == "Control biometrico de administracion"){
        for($i=0; $i< $row; $i++ ){
			if (strlen($csv[$i]['row1']) > 0){
            	$sqlText = "insert into payroll_batch(payroll_request_id, employee_id, agent_id, col1, col2, col3, col4, col5 ) ".
				"values(".$request_id.", ifnull((select employee_id from employees where agent_id = '".$csv[$i]['row1']."'),0), '".$csv[$i]['row1']."', '".$csv[$i]['row2']."', '".$csv[$i]['row3']."', '".$csv[$i]['row4']."', '".$csv[$i]['row5']."', '".$csv[$i]['row6']."')";
				$dbEx->insSql($sqlText);
   			}
		}
	}
	else if($payroll_type == "Shoretel"){
        for($i=0; $i< $row; $i++ ){
            if (strlen($csv[$i]['row7']) > 0){
				$sqlText = "insert into payroll_batch(payroll_request_id, employee_name, employee_id, col1, col2, col3, col4, col5, col6, col7, col8) ".
				"values(".$request_id.", '".$csv[$i]['row3']."', ".
				"(select if(count(1) = 0,if(length('".$csv[$i]['row3']."')>0,0, ".
				"(select p.employee_id from payroll_batch p where p.payroll_batch_id = ".
        		"(select max(p2.payroll_batch_id) from payroll_batch p2 where length(p2.employee_name) > 0)) ".
				"), employee_id) from employees where length('".$csv[$i]['row3']."') > 0 and firstname like ".
				"upper(concat(substr('".$csv[$i]['row3']."',1,instr('".$csv[$i]['row3']."',' ')-1),'%')) ".
 			 	"and lastname like upper(concat(substr('".$csv[$i]['row3']."',instr('".$csv[$i]['row3']."',' ')+1),'%'))), '".
 			 	$csv[$i]['row1']."', '".$csv[$i]['row2']."', '".$csv[$i]['row3']."', '".$csv[$i]['row4']."', '".$csv[$i]['row5']."', '".$csv[$i]['row6']."', '".$csv[$i]['row7']."', '".$csv[$i]['row8']."') ";

                $dbEx->insSql($sqlText);

			}
		}
	}
	else if($payroll_type == "Workforce"){
        for($i=0; $i< $row; $i++ ){


            if (strlen($csv[$i]['row1']) > 0 and 
            		(strlen($csv[$i]['row2']) > 0) and
            		(strlen($csv[$i]['row3']) > 0 or strlen($csv[$i]['row4']) > 0)){
            
                if(strlen($csv[$i]['row3']) <= 0 ){
                    $csv[$i]['row3'] = 0;
				}
				if(strlen($csv[$i]['row4']) <= 0 ){
                    $csv[$i]['row4'] = 0;
				}
            
				$sqlText = "insert into payroll_batch(payroll_request_id, employee_id, col1, col2, col3) ".
				"values(".$request_id.", ".
				"ifnull((select employee_id from employees where username = '".$csv[$i]['row1']."'),0), ".
				"sec_to_time(ifnull(".$csv[$i]['row3'].",0) * 3600), ".
				"sec_to_time(ifnull(".$csv[$i]['row4'].",0) * 3600), ".
				"'".$csv[$i]['row2']."' )";

				$dbEx->insSql($sqlText);

			}
		}
 	}
	
	//Recorrer todos los registros para guardar las horas trabajadas
	//Se guardara un registro por empleado

	$sqlText = "select DISTINCT(EMPLOYEE_ID) as employee_id ".
		"from payroll_batch ".
		"where PAYROLL_REQUEST_ID = ".$request_id." and employee_name <> 'USER' and employee_id > 0";
	$employees = $dbEx->selSql($sqlText);

	if($dbEx->numrows > 0){

		$login_time = "";
		$break1 = "";
		$break2 = "";
		$lunch = "";
		$batch = "";
  		$coach = "";

		//Accion a realizar por tipo de planilla
		//Para vicidal las columnas pueden cambiar en el formato por lo cual necesita identificar la columna en la cual viene cada dato
		if ($payroll_type == "Vicidal"){
			$login_time = get_vicidal_col($request_id,"LOGIN TIME");
			$break1 = get_vicidal_col($request_id,"BRK1");
			$break2 = get_vicidal_col($request_id,"BRK2");
			$lunch = get_vicidal_col($request_id,"LNCH");
			$bath = get_vicidal_col($request_id,"BATH");
			//No siempre vendra tiempo de coach por lo cual se agrega la porcion de query solo cuando este dato no venga nulo
			if (get_vicidal_col($request_id,"COACH") <> "NA") {
                $coach = " - (SUM(TIME_TO_SEC(".get_vicidal_col($request_id,"COACH")."))) ";
			};
		}
			
         $total_reg = $dbEx->numrows;

         //Obtener el total de tiempo trabajado por empleado
         for($i=0; $i<$total_reg; $i++){
         
            $totalHoras = "00:00:00";
         
            if ($payroll_type == "Vicidal"){
				$sqlText = "select SEC_TO_TIME((SUM(TIME_TO_SEC(".$login_time."))) ". // login
					" - if((SUM(TIME_TO_SEC(".$break1."))) > time_to_sec('00:15:00'),(SUM(TIME_TO_SEC(".$break1."))) - time_to_sec('00:15:00'),0 ) ". // break1
                    " - if((SUM(TIME_TO_SEC(".$break2."))) > time_to_sec('00:15:00'),(SUM(TIME_TO_SEC(".$break2."))) - time_to_sec('00:15:00'),0 ) ". // break2
                    " - if((SUM(TIME_TO_SEC(".$bath."))) > time_to_sec('00:10:00'),(SUM(TIME_TO_SEC(".$bath."))) - time_to_sec('00:10:00'),0 ) ". // bath
                    $coach.
                    " - (SUM(TIME_TO_SEC(".$lunch."))) ) ". // lunch
                    " as tiempo , '00:00:00' as noche ".
                    " from payroll_batch where employee_id = ".$employees[$i]['employee_id']." and PAYROLL_REQUEST_ID = ".$request_id;

   			}
   			else if ($payroll_type == "Control biometrico de administracion"){
				$sqlText = "select SEC_TO_TIME((time_to_sec((select if(count(1) = 0,'00:00',substr(col1,instr(col1,' ') + 1)) ".
					"from payroll_batch ".
					"where employee_id = ".$employees[$i]['employee_id']." and payroll_request_id = ".$request_id." and col3 = 1 limit 1)) -  ".
					"time_to_sec((select if(count(1) = 0,'00:00',substr(col1,instr(col1,' ') + 1)) ".
					"from payroll_batch  ".
					"where employee_id = ".$employees[$i]['employee_id']."	and payroll_request_id = ".$request_id." and col3 = 0 limit 1))) -  ".
    				"(time_to_sec((select if(count(1) = 0,'00:00',substr(col1,instr(col1,' ') + 1)) ".
					"from payroll_batch ".
					"where employee_id = ".$employees[$i]['employee_id']." and payroll_request_id = ".$request_id." and col3 = 4 limit 1)) - ".
    				"time_to_sec((select if(count(1) = 0,'00:00',substr(col1,instr(col1,' ') + 1)) ".
					"from payroll_batch ".
					"where employee_id = ".$employees[$i]['employee_id']." and payroll_request_id = ".$request_id." and col3 = 5 limit 1)))) ".
    				"as tiempo , '00:00:00' as noche ".
					"from dual";
   			}
   			else if ($payroll_type == "Shoretel"){
				$sqlText = "select SEC_TO_TIME((SUM(TIME_TO_SEC(col7)))) as tiempo , '00:00:00' as noche".
					"from payroll_batch ".
					"where employee_id = ".$employees[$i]['employee_id']." and payroll_request_id = ".$request_id." and instr(col7,'Total') = 0";
			}
			else if ($payroll_type == "Workforce"){
				
				$sqlText = "select time_format(sec_to_time(sum(time_to_sec(pb.col1)) ),'%H:%i:%s') as tiempo, ".
				" time_format(sec_to_time(sum(time_to_sec(pb.col2)) ),'%H:%i:%s') as noche ".
				" from payroll_batch pb ".
				" where pb.employee_id = ".$employees[$i]['employee_id'].
				" and pb.PAYROLL_REQUEST_ID = ".$request_id.
				" and pb.payroll_batch_id in ( ".
					" select min(pb2.payroll_batch_id) ".
					" from payroll_batch pb2 ".
					" where pb2.employee_id = pb.employee_id ".
					" and pb2.payroll_request_id = pb.payroll_request_id ".
					" group by pb2.col3 )";

   			}

              	$tiempo_trab = $dbEx -> selSql($sqlText);

			  	if($dbEx->numrows > 0){
                    //horas del payroll
                    $horasPay = $tiempo_trab['0']['tiempo'];
                    $horasNoche = $tiempo_trab['0']['noche'];
                    
                    //Verifica que horas trabajadas no se mayor a las horas programadas - acciones de personas - excepciones 
                    //Si exception=0 se registra el payroll, exception=1 se crea solo la exception, exception=2 crea payroll y exception con cantidad de horas de la diferencia.
                    $sqlText = "select time_format(sec_to_time(((SUM(TIME_TO_SEC(sch_departure))) - (SUM(TIME_TO_SEC(sch_entry)))) - ".
					 		" ((SUM(ifnull(TIME_TO_SEC(sch_lunchin),0))) - (SUM(ifnull(TIME_TO_SEC(sch_lunchout),0))))),'%H:%i:%s') horas_prog  ".
							" from schedules ".
							" where employee_id = ".$employees[$i]['employee_id'].
							" and sch_date = '".$fecha."' ";

					$HProg = "00:00:00";

						
					$dtHProg = $dbEx->selSql($sqlText);
					if($dbEx->numrows > 0 and $dtHProg['0']['horas_prog'] <> ""){
                        $HProg = $dtHProg['0']['horas_prog'];
					}

					$sqlText = "select SEC_TO_TIME((SUM(TIME_TO_SEC(exceptionemp_hfin))) - (SUM(TIME_TO_SEC(exceptionemp_hini)))) as time_excep ".
                        "from exceptionxemp ex inner join exceptions_type et on et.exceptiontp_id=ex.exceptiontp_id ".
                        " where ex.employee_id=".$employees[$i]['employee_id'].
                        " and exceptionemp_date= '".$fecha."' ".
                        " and exceptionemp_approved='A' ".
                        " and exceptiontp_level=1 group by ex.employee_id";
			        
			        $dtEx = $dbEx->selSql($sqlText);
					$horasException = "00:00:00";
					if($dbEx->numrows>0 and $dtEx['0']['time_excep'] <> ""){
						$horasException = $dtEx['0']['time_excep'];
						$totalHoras = sumarHoras($totalHoras,$horasException);
					}

					$totalHoras = sumarHoras($totalHoras,$horasPay);
					
					if($HProg=='00:00:00' and $totalHoras <> '00:00:00'){
						$exception = 1;
					}
					else if ($HProg <> $totalHoras and $totalHoras <> '00:00:00') {
						$exception = 2;
						$horasExc = restarHoras($totalHoras,$HProg);
						$horasPay = restarHoras($horasPay, $horasExc);
						if($horasExc == '00:00:00'){
							$exception = 0;
						}
					}
					else{
						$exception = 0;
					}


					//Ingresa el payroll unicamente
					
					if($exception ==0){
						$dia = restarHoras($horasPay, $horasNoche);
						$noche = $horasNoche;

						$sqlText = "select payroll_id, payroll_daytime, payroll_nigth from payroll where payroll_date='".$fecha."' ".
						"and employee_id=".$employees[$i]['employee_id'];
						$dtC = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){
							//Se actualizan las horas nocturas si existe un registro de horas nocturas siempre y cuando no se este cargando hora noctura en el csv
							$sqlText = "update payroll set payroll_htotal='".$horasPay."', payroll_daytime='".$dia."', payroll_nigth='".$noche."' where payroll_id=".$dtC['0']['payroll_id'];
							$dbEx->updSql($sqlText);
						}
						else{
							$sqlText = "insert into payroll set employee_id=".$employees[$i]['employee_id'].", payroll_date='".$fecha."', payroll_htotal='".$horasPay."', payroll_daytime='".$dia."', payroll_nigth='".$noche."'";
							$dbEx->insSql($sqlText);
						}
					}
					//Ingresa la exception
					else if($exception ==1){
						$sqlText = "insert into exceptionxemp set employee_id=".$employees[$i]['employee_id'].", exceptionemp_date='".$fecha."', ".
						 "exceptionemp_hini='00:00:00', exceptionemp_hfin='".$totalHoras."', ".
						 "exceptiontp_id=9, ".
						 "exceptionemp_comment='exceeds the programmed hours of ".$HProg." hours' ";

						$dbEx->insSql($sqlText);

						$sqlText = "select payroll_id, payroll_daytime, payroll_nigth from payroll where payroll_date='".$fecha."' and employee_id=".$employees[$i]['employee_id'];
						$dtC = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){
							$sqlText = "delete from payroll where payroll_id=".$dtC['0']['payroll_id'];
							$dbEx->updSql($sqlText);
						}

					}
					//Ingresa la hora del payroll y la hora de la exception
					else if($exception == 2){
						$sqlText = "insert into exceptionxemp set employee_id=".$employees[$i]['employee_id'].", exceptionemp_date='".$fecha."', ".
						" exceptionemp_hini='00:00:00', exceptionemp_hfin='".$horasExc."', ".
						"exceptiontp_id=9, exceptionemp_comment='exceeds the programmed hours of ".$HProg." hours'";

						$dbEx->insSql($sqlText);
						
						$dia = restarHoras($horasPay,$horasNoche);
						$noche = $horasNoche;

						$sqlText = "select payroll_id, payroll_daytime, payroll_nigth from payroll where payroll_date='".$fecha."' and employee_id=".$employees[$i]['employee_id'];
						$dtC = $dbEx->selSql($sqlText);
						if($dbEx->numrows>0){

							$sqlText = "update payroll set payroll_htotal='".$horasPay."', payroll_daytime='".$dia."', payroll_nigth='".$noche."' where payroll_id=".$dtC['0']['payroll_id'];
							$dbEx->updSql($sqlText);
						}
						else{
							$sqlText = "insert into payroll set employee_id=".$employees[$i]['employee_id'].", payroll_date='".$fecha."', payroll_htotal='".$horasPay."', payroll_daytime='".$dia."', payroll_nigth='".$noche."'";
							$dbEx->insSql($sqlText);
						}
					}

               }// Fin de proceso si encontro nuevo registro de hora para la fecha

         	} //Fin del for
			
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
