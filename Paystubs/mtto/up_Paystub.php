<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$fechaFunc = new OFECHA; 

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
				$csv[$row]['badge'] = $data[0];
				$csv[$row]['name'] = $data[1];
				$csv[$row]['nHoras'] = $data[2];
				$csv[$row]['descHoras'] = $data[3];
				$csv[$row]['horasPagar'] = $data[4];
				$csv[$row]['salario'] = $data[5];
				$csv[$row]['nHorasNoct'] = $data[6];
				$csv[$row]['HorasNoct'] = $data[7];
				$csv[$row]['nHorasExDia'] = $data[8];
				$csv[$row]['HorasExDia'] = $data[9];
				$csv[$row]['nHorasExNoct'] = $data[10];
				$csv[$row]['HorasExNoct'] = $data[11];
				$csv[$row]['Aguinaldo'] = $data[12];
				$csv[$row]['Vacaciones'] = $data[13];
				$csv[$row]['Indem'] = $data[14];
				$csv[$row]['Bono'] = $data[15];
				$csv[$row]['OtroIng'] = $data[16];
				$csv[$row]['TotalIng'] = $data[17];
				$csv[$row]['isss'] = $data[18];
				$csv[$row]['afp'] = $data[19];
				$csv[$row]['isr'] = $data[20];
				$csv[$row]['totalDeduc'] = $data[21];
				$csv[$row]['emi'] = $data[22];
				$csv[$row]['cheff'] = $data[23];
				$csv[$row]['cafet'] = $data[24];
				$csv[$row]['equipo'] = $data[25];
				$csv[$row]['optica'] = $data[26];
				$csv[$row]['prestamo'] = $data[27];
				$csv[$row]['adelanto'] = $data[28];
				$csv[$row]['totalDesc'] = $data[29];
				$csv[$row]['IniPer'] = $data[30];
				$csv[$row]['FinPer'] = $data[31];
				$csv[$row]['FecEntrega'] = $data[32];
				$csv[$row]['liquido'] = $data[33];
			
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

	for($i=0; $i< $row; $i++ ){
		$sqlText = "select employee_id from employees where username='".$csv[$i]['badge']."'";
		$dtE = $dbEx->selSql($sqlText);
		//Si el badge ingresado coincide con un empleado sige el proceso, sino lo ignora
		if($dbEx->numrows>0){
			$sqlText = "select * from payslip where payslip_date='".$fecha."' and employee_id=".$dtE['0']['employee_id'];
			$dtPay = $dbEx->selSql($sqlText);
			//Si ya posee un paystub para esa fecha realizara un update
			if($dbEx->numrows>0){
				$sqlText = "update payslip set ";
				$sqlText .= " nhours='".$csv[$i]['nHoras']."',";
				$sqlText .= " disc_hours='".$csv[$i]['descHoras']."',";
				$sqlText .= " hours_pay='".$csv[$i]['horasPagar']."',";
				$sqlText .= " payslip_salary='".$csv[$i]['salario']."',";
				$sqlText .= " nhours_noct='".$csv[$i]['nHorasNoct']."',";
				$sqlText .= " hours_noct='".$csv[$i]['HorasNoct']."',";
				$sqlText .= " nhoursext_di = '".$csv[$i]['nHorasExDia']."',";
				$sqlText .= " hoursext_di = '".$csv[$i]['HorasExDia']."',";
				$sqlText .= " nhoursext_noct = '".$csv[$i]['nHorasExNoct']."',";
				$sqlText .= " hoursext_noct = '".$csv[$i]['HorasExNoct']."',";
				$sqlText .= " aguinaldo = '".$csv[$i]['Aguinaldo']."',";
				$sqlText .= " vacation = '".$csv[$i]['Vacaciones']."',";
				$sqlText .= " indemnity = '".$csv[$i]['Indem']."',";
				$sqlText .= " bonus_payslip = '".$csv[$i]['Bono']."',";
				$sqlText .= " other_income = '".$csv[$i]['OtroIng']."',";
				$sqlText .= " total_income = '".$csv[$i]['TotalIng']."',";
				$sqlText .= " isss_payslip = '".$csv[$i]['isss']."',";
				$sqlText .= " afp_payslip = '".$csv[$i]['afp']."',";
				$sqlText .= " isr_payslip = '".$csv[$i]['isr']."',";
				$sqlText .= " total_deduc = '".$csv[$i]['totalDeduc']."',";
				$sqlText .= " emi = '".$csv[$i]['emi']."',";
				$sqlText .= " cheff = '".$csv[$i]['cheff']."',";
				$sqlText .= " cafeteria = '".$csv[$i]['cafet']."',";
				$sqlText .= " damaged_equip = '".$csv[$i]['equipo']."',";
				$sqlText .= " optical = '".$csv[$i]['optica']."',";
				$sqlText .= " loans = '".$csv[$i]['prestamo']."',";
				$sqlText .= " advances = '".$csv[$i]['adelanto']."',";
				$sqlText .= " total_desc = '".$csv[$i]['totalDesc']."',";
				$sqlText .= " inicio_per = '".$csv[$i]['IniPer']."',";
				$sqlText .= " fin_per = '".$csv[$i]['FinPer']."',";
				$sqlText .= " fec_delivery = '".$csv[$i]['FecEntrega']."',";
				$sqlText .= " liquid = '".$csv[$i]['liquido']."'";
				$sqlText .= " where payslip_id=".$dtPay['0']['PAYSLIP_ID'];
				
				$dbEx->updSql($sqlText);
			}
			//Si no existe paystub se registra uno nuevo y sus datos
			else{
				$sqlText = "insert into payslip set employee_id=".$dtE['0']['employee_id'].",";
				$sqlText .= " payslip_date='".$fecha."',";
				$sqlText .= " nhours='".$csv[$i]['nHoras']."',";
				$sqlText .= " disc_hours='".$csv[$i]['descHoras']."',";
				$sqlText .= " hours_pay='".$csv[$i]['horasPagar']."',";
				$sqlText .= " payslip_salary='".$csv[$i]['salario']."',";
				$sqlText .= " nhours_noct='".$csv[$i]['nHorasNoct']."',";
				$sqlText .= " hours_noct='".$csv[$i]['HorasNoct']."',";
				$sqlText .= " nhoursext_di = '".$csv[$i]['nHorasExDia']."',";
				$sqlText .= " hoursext_di = '".$csv[$i]['HorasExDia']."',";
				$sqlText .= " nhoursext_noct = '".$csv[$i]['nHorasExNoct']."',";
				$sqlText .= " hoursext_noct = '".$csv[$i]['HorasExNoct']."',";
				$sqlText .= " aguinaldo = '".$csv[$i]['Aguinaldo']."',";
				$sqlText .= " vacation = '".$csv[$i]['Vacaciones']."',";
				$sqlText .= " indemnity = '".$csv[$i]['Indem']."',";
				$sqlText .= " bonus_payslip = '".$csv[$i]['Bono']."',";
				$sqlText .= " other_income = '".$csv[$i]['OtroIng']."',";
				$sqlText .= " total_income = '".$csv[$i]['TotalIng']."',";
				$sqlText .= " isss_payslip = '".$csv[$i]['isss']."',";
				$sqlText .= " afp_payslip = '".$csv[$i]['afp']."',";
				$sqlText .= " isr_payslip = '".$csv[$i]['isr']."',";
				$sqlText .= " total_deduc = '".$csv[$i]['totalDeduc']."',";
				$sqlText .= " emi = '".$csv[$i]['emi']."',";
				$sqlText .= " cheff = '".$csv[$i]['cheff']."',";
				$sqlText .= " cafeteria = '".$csv[$i]['cafet']."',";
				$sqlText .= " damaged_equip = '".$csv[$i]['equipo']."',";
				$sqlText .= " optical = '".$csv[$i]['optica']."',";
				$sqlText .= " loans = '".$csv[$i]['prestamo']."',";
				$sqlText .= " advances = '".$csv[$i]['adelanto']."',";
				$sqlText .= " total_desc = '".$csv[$i]['totalDesc']."',";
				$sqlText .= " inicio_per = '".$csv[$i]['IniPer']."',";
				$sqlText .= " fin_per = '".$csv[$i]['FinPer']."',";
				$sqlText .= " fec_delivery = '".$csv[$i]['FecEntrega']."',";
				$sqlText .= " liquid = '".$csv[$i]['liquido']."'";
;
				$dbEx->insSql($sqlText);
			}
		}
	}
	$rslt = 2;
	if($rslt ==2){
		echo '<script>alert("Pay stub upload successful");location.href="../index.php";</script>';	
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again"); return false;</script>';	
	} 
	
}
?>
