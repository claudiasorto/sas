<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=formsales.xls"); 
  
  $sqlText = "select ID_MONITSALESEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitsales_date,'%d/%m/%Y') as f1, MONITSALES_QUALIFICATION, MONITSALES_ENROLLID, FAIL_ID, SKILL_ID, MONITSALES_FAIL, MONITSALES_COMMENT1, MONITSALES_COMMENT2, MONITSALES_COMMENT3, MONITSALES_COMMENT4, MONITSALES_COMMENT5, MONITSALES_COMMENT6, MONITSALES_COMMENT7, MONITSALES_COMMENT8, MONITSALES_COMMENT9, MONITSALES_COMMENT10, MONITSALES_COMMENT11  from monitoringsales_emp m inner join employees e on e.employee_id=m.employee_id where id_monitsalesemp=".$_POST['filtro']; 
  
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
			<tr><td colspan="4" align="center" bgcolor="#006666"><font color="#FFFFFF"><b>Outbound Wireless New Service</b></font></td></tr>
			<tr><td width="100"><b>QA: </b></td><td width="300"><?php echo $dtQa['0']['firstname'].' '.$dtQa['0']['lastname'];?></td><td width="100"><b>Date: </b></td><td width="100"><?php echo $dtMonit['0']['f1'];?></td></tr>
			<tr><td><b>Agent name: </b></td><td><?php echo $dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'];?>
            </td><td><b>Enrollment ID: </b></td><td><?php echo $dtMonit['0']['MONITCSEMP_ENROLLID'];?></td></tr>
			<tr><td><b>Supervisor: </b></td><td colspan="3"><?php echo $dtSup['0']['firstname'].' '.$dtSup['0']['lastname'];?></td></tr>
            <tr><td><b>Skill: </b></td><td colspan="3"><?php echo $nomSkill;?></td></tr>
			</table><br>
			<table cellpadding="2" cellspacing="2" border="1">
            <?php 
			
			$sqlText = "select * from itemsales_monitoring where id_monitsalesemp=".$_POST['filtro'];
			$dtItems = $dbEx->selSql($sqlText);
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_sales f inner join category_form_sales c on f.id_catsales=c.id_catsales where f.id_formsales=".$dtI['ID_FORMSALES'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATSALES'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					 ?>
					<tr class="showItem"><td colspan="4" align="center" bgcolor="#006666"><font color="#FFFFFF"><b><?php echo $dtDatosItems['0']['CATSALES_NAME'];?></b></font></td></tr>
				<?php }
				?>
				
				<tr><td align="center"><?php echo $dtDatosItems['0']['FORMSALES_ITEM'];?></td><td colspan="2"><?php echo$dtDatosItems['0']['FORMSALES_TEXT'];?></td><td><?php echo $dtI['ITEMSALES_RESP'];?></td></tr>
			<?php
            }	
			?>
			<tr><td colspan="4" align="right" bgcolor="#006666"><font color="#FFFFFF"><b>QA PERCENTAGE TOTAL SCORE &nbsp;&nbsp;&nbsp;&nbsp; <?php echo number_format($dtMonit['0']['MONITSALES_QUALIFICATION'],2);?>%</b></font></td></tr>
			<?php
			$sqlText = "select distinct(f.id_catsales) as idC, catsales_name from itemsales_monitoring i inner join form_monitoring_sales f on i.id_formsales=f.id_formsales inner join category_form_sales c on c.id_catsales=f.id_catsales where id_monitsalesemp=".$_POST['filtro'];
			$dtCat = $dbEx->selSql($sqlText);
			if($dtMonit['0']['FAIL_ID']>0){
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtMonit['0']['FAIL_ID'];
				$dtFail = $dbEx->selSql($sqlText);
				$sqlText = "select * from category_monit_autofail where fail_id=".$dtFail['0']['FAIL_IDFATHER'];
				$dtFailF = $dbEx->selSql($sqlText);
				?>
				<tr><td colspan="4"><b>FAIL: </b> <?php echo $dtFailF['0']['FAIL_TEXT'].'->'.$dtFail['0']['FAIL_TEXT'];?></td></tr>
				<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_FAIL'];?></td></tr>
			<?php
            }
			
			$n = 1;
			foreach($dtCat as $dtC){?>
				<tr><td colspan="4"><b><?php echo $dtC['catsales_name'];?></b></td></tr>
				<?php 
                if($n==1){ ?>
					<tr><td colspan="4" ><?php echo $dtMonit['0']['MONITSALES_COMMENT1'];?></td></tr>
                <?php
				}
				else if($n==2){ ?>
					<tr><td colspan="4" ><?php echo $dtMonit['0']['MONITSALES_COMMENT2'];?></td></tr>
				<?php 
				}
				else if($n==3){ ?>
					<tr><td colspan="4" ><?php echo $dtMonit['0']['MONITSALES_COMMENT3']; ?></td></tr>
				<?php }
				else if($n==4){ ?>
					<tr><td colspan="4" ><?php echo $dtMonit['0']['MONITSALES_COMMENT4'];?></td></tr>
				<?php }
				else if($n==5){?>
					<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_COMMENT5'];?></td></tr>
				<?php }
				else if($n==6){ ?>
					<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_COMMENT6'];?></td></tr>
				<?php  }
				else if($n==7){ ?>
					<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_COMMENT7']; ?></td></tr>
				<?php }
				else if($n==8){?>
					<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_COMMENT8'];?></td></tr>
				<?php }
				else if($n==9){ ?>
					<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_COMMENT9'];?></td></tr>
				<?php  }
				else if($n==10){ ?>
					<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_COMMENT10'];?></td></tr>
				<?php }
				else if($n==11){ ?>
					<tr><td colspan="4"><?php echo $dtMonit['0']['MONITSALES_COMMENT11']; ?></td></tr>
				<?php }
				$n = $n+1;
			}
			?></table><?php
			
		}
		
?>