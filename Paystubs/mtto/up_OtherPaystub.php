<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
$dbEx = new DBX;
$fechaFunc = new OFECHA; 

$csv = array();
//Verificamos que se ha seleccionado fecha
if(strlen($_POST['idPaystub'])<=0){
	echo '<script>alert("You must select a date");</script>';
	die();
}
else if($_FILES['flDoc']['size']==0){
	echo '<script>alert("You must select a file in CSV format"; location.href="../index.php";)</script>';
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
				$csv[$row]['nhoras'] = $data[2];
				$csv[$row]['salary'] = $data[3];
				$csv[$row]['nadditionalhours'] = $data[4];
				$csv[$row]['additionalhours'] = $data[5];
				$csv[$row]['salarydisc'] = $data[6];
				$csv[$row]['seventh'] = $data[7];
				$csv[$row]['nhorasnoct'] = $data[8];
				$csv[$row]['horasnoct'] = $data[9];
				$csv[$row]['notdiurnal'] = $data[10];
				$csv[$row]['otdiurnal'] = $data[11];
				$csv[$row]['notnoct'] = $data[12];
				$csv[$row]['otnoct'] = $data[13];
				$csv[$row]['bono'] = $data[14];
				$csv[$row]['aguinaldo'] = $data[15];
				$csv[$row]['vacation'] = $data[16];
				$csv[$row]['severance'] = $data[17];
				$csv[$row]['otherincome'] = $data[18];
				$csv[$row]['isr'] = $data[19];
				$csv[$row]['isss'] = $data[20];
				$csv[$row]['afp'] = $data[21];
				$csv[$row]['emi'] = $data[22];
				$csv[$row]['cheff'] = $data[23];
				$csv[$row]['cafeteria'] = $data[24];
				$csv[$row]['damagedequip'] = $data[25];
				$csv[$row]['otherdesc'] = $data[26];
				$csv[$row]['loans'] = $data[27];
				$csv[$row]['advances'] = $data[28];
				$csv[$row]['liquid'] = $data[29];
				$csv[$row]['note'] = $data[30];
			
				$row++;

			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a file in format CSV");location.href="../index.php";</script>';
		die();	
	}

	for($i=0; $i< $row; $i++ ){
		$sqlText = "select employee_id from employees where username='".$csv[$i]['badge']."'";
		$dtE = $dbEx->selSql($sqlText);
		//Si el badge ingresado coincide con un empleado sige el proceso, sino lo ignora
		if($dbEx->numrows>0){
			$sqlText = "select * from paystubxemp where paystub_id=".$_POST['idPaystub']." and employee_id=".$dtE['0']['employee_id'];
			$dtPay = $dbEx->selSql($sqlText);
			//Si ya posee un paystub para esa fecha realizara un update
			if($dbEx->numrows>0){
				$sqlText = "update paystubxemp set ";
				$sqlText .= " payxemp_nhoras='".$csv[$i]['nhoras']."',";
				$sqlText .= " payxemp_salary='".$csv[$i]['salary']."',";
				$sqlText .= " payxemp_nadditionalhours='".$csv[$i]['nadditionalhours']."',";
				$sqlText .= " payxemp_additionalhours='".$csv[$i]['additionalhours']."',";
				$sqlText .= " payxemp_salarydisc='".$csv[$i]['salarydisc']."',";
				$sqlText .= " payxemp_seventh='".$csv[$i]['seventh']."',";
				$sqlText .= " payxemp_nhorasnoct = '".$csv[$i]['nhorasnoct']."',";
				$sqlText .= " payxemp_horasnoct = '".$csv[$i]['horasnoct']."',";
				$sqlText .= " payxemp_notdiurnal = '".$csv[$i]['notdiurnal']."',";
				$sqlText .= " payxemp_otdiurnal = '".$csv[$i]['otdiurnal']."',";
				$sqlText .= " payxemp_notnoct = '".$csv[$i]['notnoct']."',";
				$sqlText .= " payxemp_otnoct = '".$csv[$i]['otnoct']."',";
				$sqlText .= " payxemp_bono = '".$csv[$i]['bono']."',";
				$sqlText .= " payxemp_aguinaldo = '".$csv[$i]['aguinaldo']."',";
				$sqlText .= " payxemp_vacation = '".$csv[$i]['vacation']."',";
				$sqlText .= " payxemp_severance = '".$csv[$i]['severance']."',";
				$sqlText .= " payxemp_otherincome = '".$csv[$i]['otherincome']."',";
				$sqlText .= " payxemp_isr = '".$csv[$i]['isr']."',";
				$sqlText .= " payxemp_isss = '".$csv[$i]['isss']."',";
				$sqlText .= " payxemp_afp = '".$csv[$i]['afp']."',";
				$sqlText .= " payxemp_emi = '".$csv[$i]['emi']."',";
				$sqlText .= " payxemp_cheff = '".$csv[$i]['cheff']."',";
				$sqlText .= " payxemp_cafeteria = '".$csv[$i]['cafeteria']."',";
				$sqlText .= " payxemp_damagedequip = '".$csv[$i]['damagedequip']."',";
				$sqlText .= " payxemp_otherdesc = '".$csv[$i]['otherdesc']."',";
				$sqlText .= " payxemp_loans = '".$csv[$i]['loans']."',";
				$sqlText .= " payxemp_advances = '".$csv[$i]['advances']."',";
				$sqlText .= " payxemp_liquid = '".$csv[$i]['liquid']."',";
				$sqlText .= " payxemp_note = '".$csv[$i]['note']."'";
				$sqlText .= " where payxemp_id=".$dtPay['0']['PAYXEMP_ID'];
				
				$dbEx->updSql($sqlText);
			}
			//Si no existe paystub se registra uno nuevo y sus datos
			else{
				$sqlText = "insert into paystubxemp set employee_id=".$dtE['0']['employee_id'].",";
				$sqlText .= " paystub_id = ".$_POST['idPaystub'].", ";
				$sqlText .= " payxemp_nhoras='".$csv[$i]['nhoras']."',";
				$sqlText .= " payxemp_salary='".$csv[$i]['salary']."',";
				$sqlText .= " payxemp_nadditionalhours='".$csv[$i]['nadditionalhours']."',";
				$sqlText .= " payxemp_additionalhours='".$csv[$i]['additionalhours']."',";
				$sqlText .= " payxemp_salarydisc='".$csv[$i]['salarydisc']."',";
				$sqlText .= " payxemp_seventh='".$csv[$i]['seventh']."',";
				$sqlText .= " payxemp_nhorasnoct = '".$csv[$i]['nhorasnoct']."',";
				$sqlText .= " payxemp_horasnoct = '".$csv[$i]['horasnoct']."',";
				$sqlText .= " payxemp_notdiurnal = '".$csv[$i]['notdiurnal']."',";
				$sqlText .= " payxemp_otdiurnal = '".$csv[$i]['otdiurnal']."',";
				$sqlText .= " payxemp_notnoct = '".$csv[$i]['notnoct']."',";
				$sqlText .= " payxemp_otnoct = '".$csv[$i]['otnoct']."',";
				$sqlText .= " payxemp_bono = '".$csv[$i]['bono']."',";
				$sqlText .= " payxemp_aguinaldo = '".$csv[$i]['aguinaldo']."',";
				$sqlText .= " payxemp_vacation = '".$csv[$i]['vacation']."',";
				$sqlText .= " payxemp_severance = '".$csv[$i]['severance']."',";
				$sqlText .= " payxemp_otherincome = '".$csv[$i]['otherincome']."',";
				$sqlText .= " payxemp_isr = '".$csv[$i]['isr']."',";
				$sqlText .= " payxemp_isss = '".$csv[$i]['isss']."',";
				$sqlText .= " payxemp_afp = '".$csv[$i]['afp']."',";
				$sqlText .= " payxemp_emi = '".$csv[$i]['emi']."',";
				$sqlText .= " payxemp_cheff = '".$csv[$i]['cheff']."',";
				$sqlText .= " payxemp_cafeteria = '".$csv[$i]['cafeteria']."',";
				$sqlText .= " payxemp_damagedequip = '".$csv[$i]['damagedequip']."',";
				$sqlText .= " payxemp_otherdesc = '".$csv[$i]['otherdesc']."',";
				$sqlText .= " payxemp_loans = '".$csv[$i]['loans']."',";
				$sqlText .= " payxemp_advances = '".$csv[$i]['advances']."',";
				$sqlText .= " payxemp_liquid = '".$csv[$i]['liquid']."',";
				$sqlText .= " payxemp_note = '".$csv[$i]['note']."'";
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
		echo '<script>alert("Execution problem, check the uploaded file and try again"); location.href="../index.php";</script>';	
	} 
	
}
?>
