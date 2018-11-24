<?php
require_once("db_funcs.php");
$dbEx = new DBX;

  if(isset($_SESSION['logged_app']) && $_SESSION['logged_app']==1){
  	  
  } else{
    //no se ha logueado
	header("location: ../Skycom/index.php");
  }
  
  $sqlText = "select e.employee_id, name_place, name_depart from employees e inner join plazaxemp pe on e.employee_id=pe.employee_id inner join placexdep pd on pd.id_placexdep=pe.id_placexdep inner join places p on p.id_place=pd.id_place inner join depart_exc d on d.id_depart = pd.id_depart where e.employee_id=".$_SESSION['usr_id']." and e.user_status=1 and status_plxemp='A'";

  $dtPlaza = $dbEx->selSql($sqlText);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"[]>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Agent scorecard</title>
    <link rel="shortcut icon" href="images/logo.png">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" media="all" href="calendar/skins/aqua/theme.css" title="Aqua" />
    <link rel="stylesheet" href="css/estilos.css" type="text/css" media="screen" />
    <!--link rel="stylesheet" href="style_scroll.css" type="text/css" media="screen"/-->
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="calendar/calendar.js"></script>
    <script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
    <script type="text/javascript" src="js/calen_js.js"></script>
    <script type="text/javascript" src="js/js_scorecard.js"></script>
    <script type="text/javascript" src="js/js_phonemetric.js"></script>
    <script type="text/javascript" src="js/js_agentScorecard.js"></script>
    <script type="text/javascript" src="js/js_dpr.js"></script>
    <script type="text/javascript" src="jquery.mousewheel.js"></script>
    
    <script>
	$(function(){
		$("#page-wrap").wrapInner("<table cellspacing='30'><tr>");
		$(".post").wrap("<td>");
	});
	</script>
    
    <script type="text/javascript" language="javascript">
  	function closeThis(){
    if(confirm("Desea cerrar la aplicación")){
        window.close();
    	}
 	}
 	</script>

</head>
<body>
<div id="art-main">
    <div class="cleared reset-box"></div>
    <div class="art-box art-sheet">
        <div class="art-box-body art-sheet-body">
            <div class="art-header">
                <div class="art-logo">
                     <h1 class="art-logo-name"><font color="#FFFFFF"><a href="./index.php">Agent Scorecard</a></font></h1>
               </div>
                
            </div>
            <div class="cleared reset-box"></div>
<div class="art-bar art-nav">
<div class="art-nav-outer">
	<ul class="art-hmenu">
    	<li>
        	<a title="Home" href="index.php" class="active"><img src="images/home2.png" width="30" border="0" align="top" /></a>
		</li>
        <li><a href="#"  ><img src="images/user.png" align="top" width="30" />&nbsp;<b><?php echo $_SESSION['usr_nombre']?></b></a></li>
        <li><a href="#" onclick="closeThis()" class="active" >
            <font style="cursor: pointer;" title="Close application" ><img src="images/close_black.png" width="30"></a></font>
        </li>
	</ul>
</div>
</div>
<div class="cleared reset-box"></div>
<div class="art-layout-wrapper">
    <div class="art-content-layout">
        <div class="art-content-layout-row">
            <div class="art-layout-cell art-content">
				<div class="art-box art-post">
   					 <div class="art-box-body art-post-body">
						
                              <div class="art-postcontent">
									<div class="art-content-layout">
    									<div class="art-content-layout-row">
   											<div class="art-layout-cell layout-item-0" style="width: 100%;">
       											<div id="lyMensaje">
      		 										<h2 class="art-postheader"><div id="titulo">AGENT SCORECARD<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div></h2> 
       											</div>
                                               		<div id="lyContent">
                                                	</div>
                                                	
   											</div>
    									</div>
									</div>
								<div class="art-content-layout">
    							<div class="art-content-layout-row"></div>
								</div>
							</div>
                <div class="cleared"></div>
                
			<div class="cleared"></div>
   		 </div>
		</div>

                          <div class="cleared"></div>
                        </div>
                        <div class="art-layout-cell art-sidebar1">
<div class="art-box art-vmenublock">
    <div class="art-box-body art-vmenublock-body">
                <div class="art-box art-vmenublockcontent">
                    <div class="art-box-body art-vmenublockcontent-body">
                <ul class="art-vmenu">
	
	<li>
    	<a href="#">Phone metrics</a>
        <ul>
        <?php if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){ ?>
        	<li><a href="#" onclick="uploadPhoneMetrics()">Upload phone metrics</a></li>
            <!-- <li><a href="#" onclick="filtrosReportAHT()">Averages for calls</a></li> -->
     <?php }
	  if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='SUPERVISOR'){
		   ?>  
            <li><a href="#" onclick="filtrosPhoneMetrics()">Phone metrics report</a></li>
            <!--<li><a href="#" onclick="latenessReport()">Lateness report</a></li>-->
            <li><a href="#" onclick="hoursCompletionReport()">Lateness report</a></li>
            <?php } ?>
        </ul>
    </li>   
    
    <li>
    <?php if(($dtPlaza['0']['name_place']=='QUALITY AGENT' or $_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='SUPERVISOR')){ ?>
		<a href="#" onclick="msjQuality()" class="active">Quality Score</a>
		<ul class="active">
			<li><a href="#" onclick="newMonitoring()">QA Monitoring Forms</a></li>
			<li><a href="#" onclick="MonitLog()">Monitoring log</a></li>
            <li><a href="#" onclick="ReportsQa()">Reports QA</a></li>
             <li><a href="#" onclick="ReportSbs()">Supervisor SBS</a></li>
            <li><a href="#" onclick="filtrosWeekly()">Weekly Report</a></li>
            <li><a href="#" onclick="filtrosMonthly()">Monthly Report</a></li>
            <li><a href="#" onclick="filtrosLobAverage()">LOB Average</a></li>
            <li><a href="#" onclick="filtrosRosalindWeekly()">OKC Weekly Report</a></li>
		</ul>
        <?php }?>
	</li>
    <li>
    <?php if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE'){ ?>
    	<a href="#">Scorecard</a>
    	<ul>
        	<li><a href="#" onclick="searchScorecard()">Scorecard</a></li>
        </ul>
    
    <?php } ?>
    </li>
    <li>
    <?php if($_SESSION['usr_rol']=='GERENCIA' or $_SESSION['usr_rol']=='WORKFORCE' or $_SESSION['usr_rol']=='SUPERVISOR'){ ?>
		<a href="#">Daily Performance Review</a>
        <ul>
        	<li><a href="#" onclick="newDPR()">Daily Performance Review</a></li>
            <li><a href="#" onclick="DPRSupervisor()">Performance by Supervisor</a></li>
            <li><a href="#" onclick="weeklyPerformance()">Weekly Performance Review</a></li>
        </ul>
	<?php } ?>
    </li>
</ul> 
     <div class="cleared"></div>
          </div>
           </div>
		<div class="cleared"></div>
    </div>
</div>

         <div class="cleared"></div>
             </div>
                    </div>
                </div>
            </div>
            <div class="cleared"></div>
            <div class="art-footer">
                <div class="art-footer-body">
                            <div class="art-footer-text">
                               <p><img src="images/LogoSkycom.png" width="200" /></p>
                               <!--<p>Copyright © 2012. ExpressTeleservices  All Rights Reserved.</p> -->
                           </div>
                    <div class="cleared"></div>
                </div>
            </div>

        </div>
    </div>
    <div class="cleared"></div>

    <div class="cleared"></div>
</div>

</body>
</html>
