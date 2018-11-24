<?php
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_absent.xls");
  require_once("../db_funcs.php");
  $dbEx = new DBX;
    $sqlText = "select e.employee_id, username, firstname, lastname, id_supervisor, name_account from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep = pe.id_placexdep inner join account a on a.id_account=pd.id_account inner join user_roles ur on ur.id_role=pd.id_role ".$_POST['filtro']." order by firstname";

  $dtEmp = $dbEx->selSql($sqlText);
 ?>
 <table cellpadding="0" cellspacing="0" border="1" bordercolor="#000000">
 <?php 
 if($dbEx->numrows>0){
 ?>
 <tr bgcolor="#003366"><td><font color="#FFFFFF">BADGE</td><td><font color="#FFFFFF">Employee</td><td><font color="#FFFFFF">Account</td><td><font color="#FFFFFF">Supervisor</td><td><font color="#FFFFFF">Date</td><td><font color="#FFFFFF">Status</td><td><font color="#FFFFFF">Observations</td></tr>
 <?php 
 for($i = $_POST['start']; $i <=$_POST['end']; $i +=86400){
				foreach($dtEmp as $dtE){
					$sqlText = "select absent_id, absent_status, absent_comment from absenteeism where employee_id=".$dtE['employee_id']." and absent_date='".date('Y-m-d',$i)."'".$_POST['filtro1'];
					$dtA = $dbEx->selSql($sqlText);
					$coment = "";
					$estado = "";
					if($dbEx->numrows>0){
						if($dtA['0']['absent_status']=='A'){
							$estado = "Unjustified Absence";	
						}
						else if($dtA['0']['absent_status']=='AJ'){
							$estado = "Justified Absence";
						}
						else if($dtA['0']['absent_status']=='T'){
							$estado = "Tardy";	
						}
						else if($dtA['0']['absent_status']=='O'){
							$estado = "Day Off";
						}
						else if($dtA['absent_status']=='P'){
							$estado = "Present";	
						}
						$coment = $dtA['0']['absent_comment'];
					}
					else if($_POST['estado']){
						$estado = "Present";	
						$coment = "";
					}
					if(strlen($estado)>0){
						$n = $n+1;
						$sqlText = "select firstname, lastname from employees where employee_id=".$dtE['id_supervisor'];
						$dtSup = $dbEx->selSql($sqlText);
						$nombreSup = "";
						if($dbEx->numrows>0){
								$nombreSup = $dtSup['0']['firstname']." ".$dtSup['0']['lastname'];	
						}
					?>	
						<tr><td><?php echo $dtE['username']; ?></td><td><?php echo $dtE['firstname'].' '.$dtE['lastname']; ?></td><td><?php echo $dtE['name_account']; ?></td><td><?php echo $nombreSup; ?></td><td><?php echo date('m/d/Y',$i); ?></td><td><?php echo $estado; ?></td><td><?php echo $coment; ?></td></tr>
					<?php
                    }
				}
			}
 }
 ?>
