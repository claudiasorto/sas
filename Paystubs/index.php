<?php
require_once("db_funcs.php");
$dbEx = new DBX;

  if(isset($_SESSION['logged_app']) && $_SESSION['logged_app']==1){
  	  
  } else{
    //no se ha logueado
	header("location: ../Skycom/index.php");
  }
  $sqlText = "select name_place from places pl inner join placexdep pd on pl.id_place=pd.id_place inner join plazaxemp pe on pe.id_placexdep=pd.id_placexdep inner join employees e on e.employee_id=pe.employee_id where e.employee_id=".$_SESSION['usr_id']." and pe.status_plxemp='A' and user_status=1";
  $dtCont = $dbEx->selSql($sqlText);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"[]>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en">
<head>
    <!--
    Created by Artisteer v3.1.0.48375
    Base template (without user's data) checked by http://validator.w3.org : "This page is valid XHTML 1.0 Transitional"
    -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Pay stubs</title>


	<link rel="shortcut icon" href="images/logo.png">
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <!--[if IE 6]><link rel="stylesheet" href="style.ie6.css" type="text/css" media="screen" /><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" href="style.ie7.css" type="text/css" media="screen" /><![endif]-->
    <link rel="stylesheet" href="css/default.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/estilos.css" />
    <link rel="stylesheet" type="text/css" media="all" href="calendar/skins/aqua/theme.css" title="Aqua" />

    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <script type="text/javascript" src="js/js_paystub.js"></script>
    <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="calendar/calendar.js"></script>
	<!-- import the language module -->
	<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
	<!-- helper script that uses the calendar -->
	<script type="text/javascript" src="js/calen_js.js"></script>
    <script type="text/javascript" language="javascript">
  	function closeThis(){
    if(confirm("Desea cerrar la aplicación")){
        window.close();
    	}
 	}
 	</script>

</head>
<body>
<div id="art-page-background-glare-wrapper">
    <div id="art-page-background-glare"></div>
</div>
<div id="art-main">
    <div class="cleared reset-box"></div>
    <div class="art-box art-sheet">
        <div class="art-box-body art-sheet-body">
<div class="art-bar art-nav">
<div class="art-nav-outer">
	<ul class="art-hmenu">
	
		<li>
        	<a title="Home" href="index.php" class="active"><img src="images/home2.png" width="35" border="0" align="top" /></a>
		</li>
        <li><a href="#" ><img src="images/user.png" align="top" width="32" />&nbsp;<font color="#003366"><b><?php echo $_SESSION['usr_nombre']?></b></font></a></li>
        <li><a href="#" onclick="closeThis()" >
            <font style="color:#CCCCCC; cursor: pointer;" title="Close application" ><img src="images/close_black.png" width="32"></font></a>
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
<div class="art-post-inner art-article">
								<div id="msj">
                                <h2 class="art-postheader">Welcome tu Paystubs
                                </h2>
                                <br /><br />
                                Below you Will find your Pay Stubs Information
                                <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                                </div>
                                <div id="lyContent"></div>
                                    <div class="art-postcontent">
							<p><br /></p>
                </div>
                <div class="cleared"></div>
                </div>

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
		<a href="#" class="active">Pay stubs</a>
		<ul>
			<!--<li><a href="#" onclick="lastPaystub()">Last pay stub</a> </li>
			<li><a href="#" onclick="historicPaystub()">Historic pay stub</a></li>-->
			    <li><a href="#" onclick="workSchHours()" >Worked and scheduled hours</a></li>
            	<li><a href="#" onclick="lastPay()">Last pay stub</a> </li>
				<li><a href="#" onclick="historicPay()">Historic pay stub</a></li>
            
		</ul>
	</li>
    <?php 
	//$_SESSION['usr_rol']!='MANTENIMIENTO' and $_SESSION['usr_rol']!='AGENTE' or 
	if($_SESSION['usr_rol']=='GERENCIA' or $dtCont['0']['name_place']=='ACCOUNTING MANAGER'  or $_SESSION['usr_rol']=='GERENTE DE AREA' or $_SESSION['usr_rol']=='SUPERVISOR'){ ?>	
	<li>
		<a href="#">Administration of Pay stubs</a>
        <ul>
        <li><a href="#" onclick="employeesPayStubs()">Pay stub employees </a></li>
        <?php
		
		if($_SESSION['usr_rol']=='GERENCIA' or $dtCont['0']['name_place']=='ACCOUNTING MANAGER'){
			?>
            <li><a href="#" onclick="calcularPay()">Payments summary</a></li>
            <li><a href="#" onclick="createPay()">Create paystub</a> </li>
            <!--<li><a href="#" onclick="upOtherPay()">Upload other paystubs</a></li>-->
            <li><a href="#" onclick="payIncidents()">Payment incidents</a></li>
            <li><a href="#" onclick="payIncidentesReport()">Payment incidents report</a></li>
            <li><a href="#" onclick="payStatusReport()">Paystub status report</a></li>
            <li><a href="#" onclick="legalDiscSetup()">Legal discounts</a></li>
            <li><a href="#" onclick="discountSetup()">Discounts setup</a></li>
            <li><a href="#" onclick="incidencesTickets()">Trouble tickets</a></li>
            <?php
		 }?>
        </ul>
	</li>
    <?php } ?>	
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
            <br />
            <div class="art-footer">
                <div class="art-footer-body">
                            <div class="art-footer-text">
                            	<p><img src="images/LogoSkycom.png" width="200" /></p>
                                <!--<p>Copyright © 2012. ExpressTeleservices All Rights Reserved.</p> -->
                            </div>
                    <div class="cleared"></div>
                </div>
            </div>
    		<div class="cleared"></div>
        </div>
    </div>

</div>

</body>
</html>
