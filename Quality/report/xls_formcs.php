<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=formcs.xls"); 
  
  $sqlText = "select ID_MONITCSEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitcsemp_date,'%d/%m/%Y') as f1, MONITCSEMP_QUALIFICATION, MONITCSEMP_CALLREASON, MONITCSEMP_CONTACTID, MONITCSEMP_ACCOUNT, FAIL_ID, SKILL_ID, MONITCSEMP_FAIL, MONITCSEMP_COMMENT1, MONITCSEMP_COMMENT2, MONITCSEMP_COMMENT3, MONITCSEMP_COMMENT4, MONITCSEMP_COMMENT5, MONITCSEMP_COMMENT6, MONITCSEMP_COMMENT7, MONITCSEMP_COMMENT8, MONITCSEMP_COMMENT9, MONITCSEMP_COMMENT10, MONITCSEMP_COMMENT11  from monitoringcs_emp m inner join employees e on e.employee_id=m.employee_id where id_monitcsemp=".$_POST['filtro']; 
  
  $dtMonit = $dbEx->selSql($sqlText);
		
	$nomSkill = "";
		if($dtMonit['0']['SKILL_ID']>0){
			$sqlText = "select skill_name from tp_skills where skill_id=".$dtMonit['0']['SKILL_ID'];
			$dtSkill = $dbEx->selSql($sqlText);
			$nomSkill = $dtSkill['0']['skill_name'];
	}
	
	$idCat = 0;
	$nuevaIdCat = 0;
	if($dbEx->numrows>0){
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['QA_AGENT'];
			$dtQa = $dbEx->selSql($sqlText);
			$sqlText = "select firstname, lastname from employees where employee_id=".$dtMonit['0']['ID_SUPERVISOR'];
			$dtSup = $dbEx->selSql($sqlText);
			?>
			<table align="center" cellpadding="2" cellspacing="2" border="1">
			<tr><td colspan="4" align="center" bgcolor="#006699"><font color="#FFFFFF"><b>Customer Service Monitoring Form</b></font></td></tr>
			<tr style="font:Tahoma; font-size:12px; color:#003"><td width="100"><b>QA: </b></td><td width="300"><?php echo $dtQa['0']['firstname'].' '.$dtQa['0']['lastname'];?></td><td width="100"><b>Contact ID: </b></td><td width="100"><?php echo $dtMonit['0']['MONITCSEMP_CONTACTID'];?></td></tr>
			<tr style="font:Tahoma; font-size:12px; color:#003"><td><b>Agent name: </b></td><td><?php echo $dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'];?>
            </td><td><b>Account #: </b></td><td><?php echo $dtMonit['0']['MONITCSEMP_ACCOUNT'];?></td></tr>
			<tr style="font:Tahoma; font-size:12px; color:#003"><td><b>Date: </b></td><td colspan="3" align="left"><?php echo $dtMonit['0']['f1'];?></td></tr>
			<tr style="font:Tahoma; font-size:12px; color:#003"><td><b>Supervisor: </b></td><td colspan="3"><?php echo $dtSup['0']['firstname'].' '.$dtSup['0']['lastname'];?></td></tr>
			<tr style="font:Tahoma; font-size:12px; color:#003"><td><b>Call Reason: </b></td><td colspan="3"><?php echo $dtMonit['0']['MONITCSEMP_CALLREASON'];?></td></tr>
            <tr style="font:Tahoma; font-size:12px; color:#003"><td><b>Skill: </b></td><td colspan="3"><?php echo $nomSkill;?></td></tr>
			</table><br>
			<table cellpadding="2" cellspacing="2" border="1" bordercolor="#003366">
            <?php 
			
			$sqlText = "select * from itemcs_monitoring where id_monitcsemp=".$_POST['filtro'];
			$dtItems = $dbEx->selSql($sqlText);
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_cs f inner join category_form_cs c on f.id_catcs=c.id_catcs where f.id_formcs=".$dtI['ID_FORMCS'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATCS'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					 ?>
					<tr><td colspan="4" align="center" bgcolor="#006699"><font color="#FFFFFF"><b><?php echo $dtDatosItems['0']['CATCS_NAME'];?></b></font></td></tr>
				<?php }
				?>
				
				<tr style="font:Tahoma; font-size:12px; color:#003"><td align="center"><?php echo $dtDatosItems['0']['FORMCS_ITEM'];?></td><td colspan="2"><?php echo$dtDatosItems['0']['FORMCS_TEXT'];?></td><td><?php echo $dtI['ITEMCS_RESP'];?></td></tr>
			<?php
            }	
			?>
			<tr style="font:Tahoma; font-size:12px;"><td colspan="4" align="right" bgcolor="#006699"><font color="#FFFFFF"><b>QA PERCENTAGE TOTAL SCORE &nbsp;&nbsp;&nbsp;&nbsp; <?php echo number_format($dtMonit['0']['MONITCSEMP_QUALIFICATION'],2);?>%</b></font></td></tr>
			<?php
			$sqlText = "select distinct(f.id_catcs) as idC, catcs_name from itemcs_monitoring i inner join form_monitoring_cs f on i.id_formcs=f.id_formcs inner join category_form_cs c on c.id_catcs=f.id_catcs where id_monitcsemp=".$_POST['filtro'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtFail['0']['FAIL_IDFATHER'];
				$dtFailF = $dbEx->selSql($sqlText);
				?>
				<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4">FAIL: <?php echo $dtFailF['0']['FAIL_TEXT'].'->'.$dtFail['0']['FAIL_TEXT'];?></td></tr>
				<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_FAIL'];?></td></tr>
			<?php
            }
			
			$n = 1;
			foreach($dtCat as $dtC){?>
				<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><b><?php echo $dtC['catcs_name'];?></b></td></tr>
				<?php 
                if($n==1){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4" ><?php echo $dtMonit['0']['MONITCSEMP_COMMENT1'];?></td></tr>
                <?php
				}
				else if($n==2){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4" ><?php echo $dtMonit['0']['MONITCSEMP_COMMENT2'];?></td></tr>
				<?php 
				}
				else if($n==3){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4" ><?php echo $dtMonit['0']['MONITCSEMP_COMMENT3']; ?></td></tr>
				<?php }
				else if($n==4){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4" ><?php echo $dtMonit['0']['MONITCSEMP_COMMENT4'];?></td></tr>
				<?php }
				else if($n==5){?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_COMMENT5'];?></td></tr>
				<?php }
				else if($n==6){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_COMMENT6'];?></td></tr>
				<?php  }
				else if($n==7){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_COMMENT7']; ?></td></tr>
				<?php }
				else if($n==8){?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_COMMENT8'];?></td></tr>
				<?php }
				else if($n==9){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_COMMENT9'];?></td></tr>
				<?php  }
				else if($n==10){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_COMMENT10'];?></td></tr>
				<?php }
				else if($n==11){ ?>
					<tr style="font:Tahoma; font-size:12px; color:#003"><td colspan="4"><?php echo $dtMonit['0']['MONITCSEMP_COMMENT11']; ?></td></tr>
				<?php }
				$n = $n+1;
			}
			?></table><?php
			
		}
		
?>