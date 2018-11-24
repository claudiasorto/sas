<?php 
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
require_once("../salary_funcs.php");
$dbEx = new DBX;
$fechaFunc = new OFECHA;
$sFunc = new SAL;

$csv = array();
$csvDescuento = array();
$csvBono = array();
$csvAguinaldo= array();
$csvSeverance= array();
$csvOtherIncome = array();
$csvSeven = array();
$csvAttr = array();


//verificamos la fecha seleccionada
if($_POST['lsDelivery']==0){
	echo '<script>alert("You must select a date");</script>';
	die();
}

    //Archivo de descuento
	$rowDescuento = 0;
	if($_FILES['flDescuento']['size']>0){
		$ext = strtolower(end(explode('.',$_FILES['flDescuento']['name'])));
		$type = $_FILES['flDescuento']['type'];
		$tmpName = $_FILES['flDescuento']['tmp_name'];
		if($ext == 'csv'){
			if(($handle = fopen($tmpName,'r')) !=FALSE){
				set_time_limit(0);
				while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
					$num = count($data);
					//obtiene los valores del CSV
					$csvDescuento[$rowDescuento]['badge'] = $data[0];
					$csvDescuento[$rowDescuento]['data'] = $data[1];
					$sqlText = "select employee_id from employees where username='".$data['0']."'";
					$dtIdEmp = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$csvDescuento[$rowDescuento]['employee_id'] = $dtIdEmp['0']['employee_id'];
					}
					else{
						$csvDescuento[$rowDescuento]['employee_id']=0;
					}
					$rowDescuento++;
				}
				fclose($handle);
			}
		}
	else{
		echo '<script>alert("You must select a document of Salary discounts in format CSV");return false;</script>';
		die();
		}
	}
	
	//Archivo de bono
	$rowBono = 0;
	if($_FILES['flBono']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flBono']['name'])));
	$type = $_FILES['flBono']['type'];
	$tmpName = $_FILES['flBono']['tmp_name'];
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CSV
				$csvBono[$rowBono]['badge'] = $data[0];
				$csvBono[$rowBono]['data'] = $data[1];
				$sqlText = "select employee_id from employees where username='".$data['0']."'";
				$dtIdEmp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$csvBono[$rowBono]['employee_id'] = $dtIdEmp['0']['employee_id'];
				}
				else{
					$csvBono[$rowBono]['employee_id']=0;
				}
				$rowBono++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of bonus in format CSV");return false;</script>';
		die();
		}
	}
	//Archivo de Aguinaldo
	$rowAguinaldo = 0;
	if($_FILES['flAguinaldo']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flAguinaldo']['name'])));
	$type = $_FILES['flAguinaldo']['type'];
	$tmpName = $_FILES['flAguinaldo']['tmp_name'];
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvAguinaldo[$rowAguinaldo]['badge'] = $data[0];
				$csvAguinaldo[$rowAguinaldo]['data'] = $data[1];
				$sqlText = "select employee_id from employees where username='".$data['0']."'";
				$dtIdEmp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$csvAguinaldo[$rowAguinaldo]['employee_id'] = $dtIdEmp['0']['employee_id'];
				}
				else{
					$csvAguinaldo[$rowAguinaldo]['employee_id']=0;
				}
				$rowAguinaldo++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of Aguinaldo in format CSV");return false;</script>';
		die();
		}
	}
	//Archivo de Severance
	$rowSeverance = 0;
	if($_FILES['flSeverance']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flSeverance']['name'])));
	$type = $_FILES['flSeverance']['type'];
	$tmpName = $_FILES['flSeverance']['tmp_name'];
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvSeverance[$rowSeverance]['badge'] = $data[0];
				$csvSeverance[$rowSeverance]['data'] = $data[1];
				$sqlText = "select employee_id from employees where username='".$data['0']."'";
				$dtIdEmp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$csvSeverance[$rowSeverance]['employee_id'] = $dtIdEmp['0']['employee_id'];
				}
				else{
					$csvSeverance[$rowSeverance]['employee_id']=0;
				}
				$rowSeverance++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of Severance in format CSV");return false;</script>';
		die();
		}
	}
	
 //Archivo de Otros Ingresos
	$rowOtroIng = 0;
	if($_FILES['flOtherIncome']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flOtherIncome']['name'])));
	$type = $_FILES['flOtherIncome']['type'];
	$tmpName = $_FILES['flOtherIncome']['tmp_name'];
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvOtherIncome[$rowOtroIng]['badge'] = $data[0];
				$csvOtherIncome[$rowOtroIng]['data'] = $data[1];
				$sqlText = "select employee_id from employees where username='".$data['0']."'";
				$dtIdEmp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$csvOtherIncome[$rowOtroIng]['employee_id'] = $dtIdEmp['0']['employee_id'];
				}
				else{
					$csvOtherIncome[$rowOtroIng]['employee_id']=0;
				}
				$rowOtroIng++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of Other Income in format CSV");return false;</script>';
		die();
		}
	}
	
	//Archivos de dia septimo
	$rowSeven = 0;
	if($_FILES['flSeven']['size']>0){
	$ext = strtolower(end(explode('.',$_FILES['flSeven']['name'])));
	$type = $_FILES['flSeven']['type'];
	$tmpName = $_FILES['flSeven']['tmp_name'];
	if($ext == 'csv'){
		if(($handle = fopen($tmpName,'r')) !=FALSE){
			set_time_limit(0);
			while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
				$num = count($data);
				//obtiene los valores del CVS
				$csvSeven[$rowSeven]['badge'] = $data[0];
				$csvSeven[$rowSeven]['data'] = $data[1];
				$sqlText = "select employee_id from employees where username='".$data['0']."'";
				$dtIdEmp = $dbEx->selSql($sqlText);
				if($dbEx->numrows>0){
					$csvSeven[$rowSeven]['employee_id'] = $dtIdEmp['0']['employee_id'];
				}
				else{
					$csvSeven[$rowSeven]['employee_id']=0;
				}
				$rowSeven++;
			}
			fclose($handle);
		}
	}
	else{
		echo '<script>alert("You must select a document of seventh day discount in format CSV");return false;</script>';
		die();
		}
	}

	//Archivos de los descuentos configurados
    $sqlText = "select ps.disc_id, ps.disc_label, pa.disc_attributename ".
		"from pay_discount_setup ps, pay_discount_attr pa ".
		"where ps.disc_attributeid = pa.disc_attributeid ".
		"and ifnull(ps.disc_end_date,sysdate() + 1) > sysdate() ".
    	"order by ps.disc_label";
    	
   $dtDisc = $dbEx->selSql($sqlText);
   $rowDesAttr = 0;
   if($dbEx->numrows>0){
        foreach($dtDisc as $dtD){
            $attribute = $dtD['disc_attributename'];
            if($_FILES[$attribute]['size']>0){
             	$ext = strtolower(end(explode('.',$_FILES[$attribute]['name'])));
				$type = $_FILES[$attribute]['type'];
				$tmpName = $_FILES[$attribute]['tmp_name'];
			 	if($ext == 'csv'){
			 	    if(($handle = fopen($tmpName,'r')) !=FALSE){
						set_time_limit(0);
						while(($data = fgetcsv($handle,1000,',','"','\n'))!=FALSE){
							$num = count($data);
							//obtiene los valores del CSV
							$csvAttr[$rowDesAttr]['badge'] = $data[0];
							$csvAttr[$rowDesAttr]['data'] = $data[1];
							$csvAttr[$rowDesAttr]['attribute'] = $dtD['disc_attributename'];
							$sqlText = "select employee_id from employees where username='".$data['0']."'";
							$dtIdEmp = $dbEx->selSql($sqlText);
							if($dbEx->numrows>0){
								$csvAttr[$rowDesAttr]['employee_id'] = $dtIdEmp['0']['employee_id'];
							}
							else{
								$csvAttr[$rowDesAttr]['employee_id']=0;
							}
							$rowDesAttr++;
						}
						fclose($handle);
					}
			 	}
			 	else{
					echo '<script>alert("You must select '.$dtD['disc_label'].' discount document in CSV format");</script>';
					die();
				}
			 }
		}
   }

	//Termina de recorrer los CSV
	
	//Recorrer todos los empleados creados en la planilla seleccionada
	//Actualizar las boletas de pago con los datos cargados

   $sqlText = "select pe.payxemp_id, pe.employee_id, p.paystub_ini, p.paystub_fin, p.paystub_delivery ".
   			"from paystubxemp pe inner join paystub p on pe.paystub_id = p.paystub_id ".
   			"where p.paystub_id = ".$_POST['lsDelivery'];

   	$dtPaystub = $dbEx->selSql($sqlText);
   	if ($dbEx->numrows > 0) {
   		foreach ($dtPaystub as $dtP) {

			for($i=0; $i<$rowDescuento; $i++){
				if($csvDescuento[$i]['employee_id']==$dtP['employee_id']){

					$sqlText = "update paystubxemp set ".
								"payxemp_salarydisc = '".$csvDescuento[$i]['data']."' ".
								"where payxemp_id = ".$dtP['payxemp_id'];
					$dbEx->updSql($sqlText);
					$i = $rowDescuento;

				}
			}
			for($i=0; $i<$rowBono; $i++){
				if($csvBono[$i]['employee_id']==$dtP['employee_id']){
					
					$sqlText = "update paystubxemp set ".
								"payxemp_bono = '".$csvBono[$i]['data']."' ".
								"where payxemp_id = ".$dtP['payxemp_id'];
					$dbEx->updSql($sqlText);
					$i = $rowBono;
				}
			}
			for($i=0; $i<$rowAguinaldo; $i++){
				if($csvAguinaldo[$i]['employee_id']==$dtP['employee_id']){

					$sqlText = "update paystubxemp set ".
								"payxemp_aguinaldo = '".$csvAguinaldo[$i]['data']."' ".
								"where payxemp_id = ".$dtP['payxemp_id'];
					$dbEx->updSql($sqlText);
					$i = $rowAguinaldo;
				}
			}
			for($i=0; $i<$rowSeverance; $i++){
				if($csvSeverance[$i]['employee_id']==$dtP['employee_id']){

					$sqlText = "update paystubxemp set ".
								"payxemp_severance = '".$csvSeverance[$i]['data']."' ".
								"where payxemp_id = ".$dtP['payxemp_id'];
					$dbEx->updSql($sqlText);
					$i = $rowSeverance;
				}
			}
			for($i=0; $i<$rowOtroIng; $i++){
				if($csvOtherIncome[$i]['employee_id']==$dtP['employee_id']){

					$sqlText = "update paystubxemp set ".
								"payxemp_otherincome = '".$csvOtherIncome[$i]['data']."' ".
								"where payxemp_id = ".$dtP['payxemp_id'];
					$dbEx->updSql($sqlText);
					$i = $rowOtroIng;
				}
			}
			for($i=0; $i<$rowSeven; $i++){
				if($csvSeven[$i]['employee_id']==$dtE['employee_id']){

					$sqlText = "update paystubxemp set ".
								"payxemp_seventh = '".$csvSeven[$i]['data']."' ".
								"where payxemp_id = ".$dtP['payxemp_id'];
					$dbEx->updSql($sqlText);
					$i = $rowSeven;
				}
			}  

			//Actualizar planilla por empleado
			$boletaRslt = $sFunc->calcularPagoEmpleado(
							$dtP['employee_id'],
							$_POST['lsDelivery'],
							$dtP['paystub_ini'], 
							$dtP['paystub_fin'],
							$dtP['paystub_delivery']
					);


   		}//Termina de actualizar los datos cargados


   	}

	$rslt = 2;
	if($rslt ==2){
		echo '<script>alert("Pay stub upload successful"); window.parent.createPay();</script>';
	}
	else{
		echo '<script>alert("Execution problem, check the uploaded file and try again"); return false;</script>';
	}

?>
