  <?php
require_once("db_funcs.php");
require_once("ap_funcs.php");
$dbEx = new DBX;
$apFun = new APPR;

  if(isset($_SESSION['logged_app']) && $_SESSION['logged_app']==1){
  	  
  } else{
    //no se ha logueado
	header("location: ../Skycom/index.php");
  }
  
  $sqlText = "select e.employee_id, trim(name_place) name_place, name_depart ".
  			"from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id ".
  			"inner join placexdep pd on pd.id_placexdep=pe.id_placexdep ".
  			"inner join places p on p.id_place=pd.id_place ".
  			"inner join depart_exc d on d.id_depart = pd.id_depart ".
  			"where e.employee_id=".$_SESSION['usr_id'].
  			" and e.user_status=1 and status_plxemp='A'";

  $dtPlaza = $dbEx->selSql($sqlText);
  
  //Obtener AP Rechazadas
  $tblResult = '<br><br><table class="backTablaMain" width="800" align="center" cellpadding="4" cellspacing="4">';
  $sqlText = "select count(1) as cantRechaz from apxemp where autor_ap=".$_SESSION['usr_id']." and (approved_work='N' or approved_area='N' or approved_hr='N' or approved_general='N')";
  $dtRechaz = $dbEx->selSql($sqlText);
  	if($dtRechaz['0']['cantRechaz']>0){
		$tblResult .='<tr><td><li type="circle"><b>Usted tiene '.$dtRechaz['0']['cantRechaz'].' Acciones de Personal Rechazadas <a href="#" onClick="reporteApRechazada()" >Haga click para verlas</a></li></td></tr>';  
	}
	
	
	$cantAp = $apFun->getCantApPendientes($_SESSION['usr_rol']);

	if($cantAp){
		$tblResult .='<tr><td><li type="circle"><b>Usted tiene '.$cantAp.' Acciones de Personal pendientes de aprobaci&oacute;n <a href="#" onClick="autorizarap()" >Haga click para verlas</a></li></td></tr>';	
	}
	

	//Ver si tiene exceptions pendientes de aprobacion

	//Exception de IT
	if($dtPlaza['0']['name_place']=='IT MANAGER'){
	//if(true){
		$sqlText = "select count(1) as cantEx ".
					"from exceptionxemp ex inner join employees e on ex.employee_id = e.employee_id ".
					"where exceptiontp_id in (1,2,3,4) and exceptionemp_approved='P' and user_status=1 ";	
		$dtExcep = $dbEx->selSql($sqlText);
		if($dtExcep['0']['cantEx']>0){
			$tblResult .='<tr><td><li type="circle"><b>Usted tiene '.$dtExcep['0']['cantEx'].' Exception por aprobar <a href="#" onClick="autorizarExcepciones()" >Haga click para verlas</a></li></td></tr>';	
		}
	}
	
	//Gerencia todas
	if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
		$sqlText = "select count(1) as cantEx ".
					"from exceptionxemp ex inner join employees e on ex.employee_id = e.employee_id ".
					"where exceptionemp_approved='P' and user_status=1 ";	
		$dtExcep = $dbEx->selSql($sqlText);
		if($dtExcep['0']['cantEx']>0){
			$tblResult .='<tr><td><li type="circle"><b>Usted tiene '.$dtExcep['0']['cantEx'].' Exception por aprobar <a href="#" onClick="autorizarExcepciones()" >Haga click para verlas</a></li></td></tr>';	
		}
	}
	
  
  $tblResult .='</table>';
  	

?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Personnel Administration</title>
    <link rel="shortcut icon" href="images/logo.png">
    
    <link rel="stylesheet" href="css/estilos.css" />
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
	<link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui-1.8.16.custom.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="calendar/skins/aqua/theme.css" title="Aqua" />
	<link rel="stylesheet" type="text/css" href="sdmenu/sdmenu.css" />

	<script type="text/javascript" src="calendar/calendar.js"></script>
	<!-- import the language module -->
	<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
	<!-- helper script that uses the calendar -->
	<script type="text/javascript" src="js/calen_js.js"></script>

	<script src="js/jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="sdmenu/sdmenu.js"></script>
	<script src="js/js_ap.js"></script>
    <script src="js/js_absent.js"></script>
    <script src="js/js_payroll.js"></script>
    <script src="js/js_exceptions.js"></script>
    <script src="js/js_schedules.js"></script>
	<script type="text/javascript" language="javascript">
  	function closeThis(){
    if(confirm("Desea cerrar la aplicación")){
        window.close();
    	}
 	}
 	</script>
    
	<script type="text/javascript"  language="javascript">
	
   var myMenu;
	window.onload = function() {
		myMenu = new SDMenu("my_menu");
		myMenu.init();
	};

	</script>
</head>
<body>
  <div id="header">
<!--	<h3>MT</h3>
    <p>Copyright Â© 2017.</p>
	-->
</div> 
<div id="plantilla">
	<div id="posts">
	  <div id="lyMain">
     	 <div id="msj" style="display:block">
      		<h2 class="title">Bienvenido al m&oacute;dulo de administraci&oacute;n de Personal!</h2><br>
      		<img src="images/LogoSkycom.png" width="300px" />
            <?php echo $tblResult; ?>
            
            
      	</div>
        <div id="content" style="display:none"></div>
      </div>
	</div>  
  
  	<div id="Inicio" class="boxed">
	  <h2 class="heading" style="cursor:pointer;" id="mnuStart"><a title="Home" href="index.php"><img src="images/home.png" width="25" border="0" align="top" /></a></h2>		
	</div>
	 <div style="float: left" id="my_menu" class="sdmenu">
     
      <div class="collapsed">
	     <span>Acciones de Personal</span>
			<ul>
				<li><a href="#" onClick="newap()" >Nueva acci&oacute;n de personal</a></li>
                <li><a href="#" onClick="reportap()" >Reportes</a></li>
                <li><a href="#" onClick="reporteApRechazada()" >Acciones de personal rechazadas</a></li>
                <?php
				if($_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='RECURSOS HUMANOS' or $_SESSION['usr_rol']=='GERENCIA'){ ?>
				<li><a href="#" onClick="autorizarap()" >AP por autorizar</a></li>		
				<?php
        		}	
				?>
        		<?php 
				if($_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='GERENCIA'){
				?>
            	<li><a href="#" onClick="cambiosWork()" >Traslados</a></li>
           		 <?php	
				}
				?>
                <li><a href="#" onClick="mis_ap()" >Mis acciones de personal</a></li>
			</ul>
		</div>
	<div class="collapsed">
        <span>Payroll</span>
        <ul>
        	<li><a href="#" onClick="newRegHora()">Time record</a></li>
            <li><a href="#" onClick="rptPayRoll()">Payroll Report</a></li>
            
            <?php 
			if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='WORKFORCE'){
			?>

                <li><a href="#" onClick="newRegHoraAll()">Time record All Employees </a></li>
             <?php 
			}
			if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $dtPlaza['0']['name_place']=='ACCOUNTING MANAGER' or $_SESSION['usr_rol']=='GERENTE DE AREA'){
			 ?>
            	<li><a href="#" onClick="reportPayroll()">Payroll Report All Employees </a></li>
            <?php
			}
			if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='SUPERVISOR' or $_SESSION['usr_rol']=='RECURSOS HUMANOS'){
			?>
            	<li><a href="#" onClick="newPayroll()">Payroll register</a></li>
            	<li><a href="#" onClick="uploadNightHours()">Upload night hours</a></li>
            <?php	
			}
			if($_SESSION['usr_rol']=='GERENCIA'){
			?>
            	<li><a href="#" onClick="deletePayroll()">Reset payroll</a></li>
            	<li><a href="#" onClick="cleanPayrollBatch()">Clean up db payroll batch</a></li>
            <?php
			}
			?>
        </ul>
	</div>
    <div class="collapsed">
        <span>Absenteeism</span>
             <ul>   
             	<li><a href="#" onClick="newAbsentAllDays()">Absenteeism registration</a></li>
				 <!--<li><a href="#" onClick="newAbsent()">Absenteeism registration </a></li> -->
            	<li><a href="#" onClick="repAbsent()">Absenteeism report</a></li>
                
                    <?php
					if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='RECURSOS HUMANOS'){
						?>
						<li><a href="#" onClick="loadRptAbsComplete()">Report Absenteeism All Employees</a></li>
                    <?php }
					if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){
					?>
                        <li><a href="#" onClick="upDayOffAbsent()">Upload Day Off</a></li>
                        <!--li><a href="#" onClick="newAbsentUnrestricted()">Registration unrestricted absenteeism</a></li-->
						<?php
					}
				?>
             </ul>
    </div>
    <div class="collapsed">
    	<span>Exception</span>
        	<ul>
            	<li><a href="#" onClick="newException()">Time Exception</a></li>
                <li><a href="#" onClick="rptException()">Exception Report</a></li>
            <?php 
				if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='GERENTE DE AREA'){
					?>
                    <li><a href="#" onClick="newExceptionAllEmp()">Time Exception All Employees</a></li>
            <?php   }      
				if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $dtPlaza['0']['name_depart']=='IT'){
            ?>
                    <li><a href="#" onClick="rptTotalException()">Exception Report All Employees</a></li>
                    <?php	
				}
			?>
             </ul>
    </div>
    
    <div class="collapsed">
    	<span>Schedules</span>
        	<ul>
			<?php if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='SUPERVISOR' or $_SESSION['usr_rol']=='RECURSOS HUMANOS')
			{ ?>
            	<li><a href="#" onClick="newHorario()">Assign New Schedule</a></li>
                <li><a href="#" onClick="uploadHorario()">Upload New Schedule</a></li>
                <li><a href="#" onClick="uploadProgHours()">Upload Programmed Hours</a></li>
                <!-- <li><a href="#" onClick="HorarioEspecial()">Offline Programming</a></li>     -->
				<?php } ?>
                <li><a href="#" onClick="reportHorario()">Schedules Report</a></li>
                <li><a href="#" onClick="EmpleadosPorHora()">Staffing Interval Report</a></li>
                <li><a href="#" onClick="reportActivities()">Offline Activity Report</a></li>
                <li><a href="#" onClick="reportProgrammedHours()">Programmed Hours Report</a></li>
            </ul>
    </div>
   
	<div id="blogroll" class="boxed">
		<h2 class="heading"><font style="color:#CCCCCC; cursor: pointer;">USER</font></h2>
		<div class="content">
			<ul>
			  <li><img src="images/buddy2.PNG" align="top" width="28" />&nbsp;<font color="#003366"><b><?php echo $_SESSION['usr_nombre']; ?></b></font></li>
			</ul>
       <div id="blogroll" class="boxed">
          <ul>
            <li>
            <font style="color:#CCCCCC; cursor: pointer;" title="cerrar aplicaci&oacute;n" onclick="closeThis()"><img src="images/shut_down.png" width="30%"></font>
            </li>
            </ul>
       </div>
		</div>
	</div>
</div>

</body>
</html>
