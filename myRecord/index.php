<?php
require_once("db_funcs.php");
$dbEx = new DBX;

  if(isset($_SESSION['logged_app']) && $_SESSION['logged_app']==1){
  	  
  } else{
    //no se ha logueado
	header("location: ../Skycom/index.php");
  }
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"[]>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>My record</title>

	<link rel="shortcut icon" href="images/logo.png">
	<link rel="stylesheet" href="css/estilos.css" />
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" media="all" href="calendar/skins/aqua/theme.css" title="Aqua" />
    
	<script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="calendar/calendar.js"></script>
    <script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
    <script type="text/javascript" src="js/calen_js.js"></script>
    <script type="text/javascript" src="js/js_myrecord.js"></script>
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
                        <div class="art-layout-cell art-sidebar1">
<div class="art-box art-vmenublock">
    <div class="art-box-body art-vmenublock-body">
         <div class="art-box art-vmenublockcontent">
             <div class="art-box-body art-vmenublockcontent-body">
                <ul class="art-vmenu">
					<li>
						<a href="#" class="active" onclick="mis_ap()">Personnel actions</a>
					</li>	
					<li>
						<a href="#" onclick="payroll()" >Payroll</a>
					</li>	
					<li>
						<a href="#" onclick="absenteeism()">Absenteeism</a>
					</li>	
					<li>
						<a href="#" onclick="exception()">Exceptions</a>
                    </li>
                    <li>
                    	<a href="#" onclick="evaluations()">Scores</a>
                    </li>
                    <li>
                    	<a href="#" onclick="schedules()">Schedules</a>
                    </li>
                    <li>
                    	<a href="#" onclick="Metrics()">Ranking by Metrics</a>
                    </li>
                   <?php
				   if($_SESSION['usr_rol']=='GERENCIA'){
					   ?>
					   <li>
					   <a href="#" onclick="hrRequest()">HR Request</a>
					   </li>
                       <?php
					}
				   ?>

                
                         <div class="cleared"></div>
                    </div>
                </div>
		<div class="cleared"></div>
    </div>
</div>

                          <div class="cleared"></div>
                        </div>
                        <div class="art-layout-cell art-content">
<div class="art-box art-post">
    <div class="art-box-body art-post-body">

                                             
	<div id="lyMensaje">
        <h1>Welcome to my record in Skycom!</h1>
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
    </div>
    <div id="lyContent"></div>
      <br /><br /><br /><br />


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
    		<div class="cleared"></div>
        </div>
    </div>
    <div class="cleared"></div>
    <div class="cleared"></div>
</div>

</body>
</html>
