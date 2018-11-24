<?php
  require_once("../db_funcs.php");
  $dbEx = new DBX;
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=formchat.xls");
	  $sqlText = "select ID_MONITCHATEMP, e.EMPLOYEE_ID, QA_AGENT, FIRSTNAME, LASTNAME, ID_SUPERVISOR, date_format(monitchatemp_date,'%d/%m/%Y') as f1, MONITCHATEMP_QUALIFICATION, MONITCHATEMP_REASON, MONITCHATEMP_ACCOUNT, FAILCHAT_ID, SKILL_ID, MONITCHATEMP_FAIL, MONITCHATEMP_COMMENT  from monitoringchat_emp m inner join employees e on e.employee_id=m.employee_id where id_monitchatemp=".$_POST['filtro'];
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
			<tr><td colspan="4" align="center" bgcolor="#97D3F4"><b>Chat Monitoring Form Number <?php echo $dtMonit['0']['ID_MONITCHATEMP'];?></b></td></tr>
			<tr><td width="15%"><b>QA: </td><td colspan="3"><?php echo $dtQa['0']['firstname'].' '.$dtQa['0']['lastname'];?></td></tr>
			<tr><td><b>Agent name: </td><td colspan="3"><?php echo $dtMonit['0']['FIRSTNAME'].' '.$dtMonit['0']['LASTNAME'];?></td></tr>
			<tr><td><b>Account #: </td><td colspan="3"><?php echo $dtMonit['0']['MONITCHATEMP_ACCOUNT'];?></td></tr>
			<tr><td><b>Date: </td><td colspan="3"><?php echo $dtMonit['0']['f1'];?></td></tr>
			<tr><td><b>Reason for chat: </td><td colspan="3"><?php echo $dtMonit['0']['MONITCHATEMP_REASON']; ?></td></tr>
			<tr><td><b>Skill: </td><td colspan="3"><?php echo $nomSkill;?></td></tr>
			
			<table align="center" cellpadding="2" cellspacing="2" border="1">
			
            <?php
			$sqlText = "select * from itemchat_monitoring where id_monitchatemp=".$_POST['filtro'];
			$dtItems = $dbEx->selSql($sqlText);
			$totalY = 0;
			$totalN = 0;
			$totalNA = 0;
			foreach($dtItems as $dtI){
				$sqlText = "select * from form_monitoring_chat f inner join category_form_chat c on f.id_catchat=c.id_catchat where f.id_formchat=".$dtI['ID_FORMCHAT'];
				$dtDatosItems = $dbEx->selSql($sqlText);
				$nuevaIdCat = $dtDatosItems['0']['ID_CATCHAT'];
				if($idCat != $nuevaIdCat){
					$idCat = $nuevaIdCat;
					?>
					<tr bgcolor="#97D3F4" align="center"><td colspan="4"><b><?php echo $dtDatosItems['0']['CATCHAT_NAME'];?></b></td></tr>
                <?php
				}
				?>
				<tr><td align="center"><?php echo $dtDatosItems['0']['FORMCHAT_ITEM'];?></td><td colspan="2"><?php echo $dtDatosItems['0']['FORMCHAT_TEXT'];?></td><td><?php echo $dtI['ITEMCHAT_RESP'];?></td></tr>
				<?php
                if($dtI['ITEMCHAT_RESP']=='Y'){
					$totalY = $totalY + 1;	
				}
				else if($dtI['ITEMCHAT_RESP']=='N'){
					$totalN = $totalN + 1;	
				}
				else if($dtI['ITEMCHAT_RESP']=='NA'){
					$totalNA = $totalNA + 1;	
				}
			}	
			?>
			<tr bgcolor="#97D3F4"><td colspan="4" align="right"><b>PERCENT CHAT QUALITY SCORE &nbsp;&nbsp;&nbsp;&nbsp; <?php echo number_format($dtMonit['0']['MONITCHATEMP_QUALIFICATION'],2);?>%</b></td></tr>
			<tr><td colspan="4"><b>Total Yes: <?php echo $totalY.'  Total No: '.$totalN.' Total N/A: '.$totalNA;?></b></td></tr>
			<?php 
			if($dtMonit['0']['FAILCHAT_ID']>0){
				$sqlText = "select * from category_autofail_chat where failchat_id=".$dtMonit['0']['FAILCHAT_ID'];
				$dtFail = $dbEx->selSql($sqlText);
			?>
				<tr><td colspan="4">FAIL: <?php echo $dtFail['0']['FAILCHAT_TEXT'];?></td></tr>
				<tr><td colspan="4"><?php echo $dtMonit['0']['MONITCHATEMP_FAIL'];?></td></tr>
			<?php 
            }
			?>
            <tr><td colspan="4">Comments: <?php echo $dtMonit['0']['MONITCHATEMP_COMMENT'];?></td></tr>
			
			</table>
            <?php
		}
  
?>