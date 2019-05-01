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
    <!--
    Created by Artisteer v3.1.0.48375
    Base template (without user's data) checked by http://validator.w3.org : "This page is valid XHTML 1.0 Transitional"
    -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Employees</title>


	<link rel="stylesheet" href="css/estilos.css" />
  <link rel="shortcut icon" href="images/logo.png">
  <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
  <link rel="stylesheet" type="text/css" media="all" href="calendar/skins/aqua/theme.css" title="Aqua" />
    <!--[if IE 6]><link rel="stylesheet" href="style.ie6.css" type="text/css" media="screen" /><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" href="style.ie7.css" type="text/css" media="screen" /><![endif]-->

  <script type="text/javascript" src="jquery.js"></script>
  <script type="text/javascript" src="script.js"></script>
  <script src="js/jquery-1.5.1.min.js"></script>
    <!-- import the calendar script -->
	<script type="text/javascript" src="calendar/calendar.js"></script>
	<!-- import the language module -->
	<script type="text/javascript" src="calendar/lang/calendar-es.js"></script>
	<!-- helper script that uses the calendar -->
	<script type="text/javascript" src="js/calen_js.js"></script>
    <script src="js/js_emp.js"></script>
    <script type="text/javascript" language="javascript">
  function closeThis(){
    if(confirm("Desea cerrar la aplicación")){
        window.close();
    }
  }
 </script>
   <style type="text/css">
.art-post .layout-item-0 { padding-right: 10px;padding-left: 10px; }
   .ie7 .art-post .art-layout-cell {border:none !important; padding:0 !important; }
   .ie6 .art-post .art-layout-cell {border:none !important; padding:0 !important; }
   </style>

</head>
<body>
<div id="art-page-background-glare-wrapper">
    <div id="art-page-background-glare"></div>
</div>
<div id="art-main">
    <div class="cleared reset-box"></div>
    <div class="art-box art-sheet">
        <div class="art-box-body art-sheet-body">
            <div class="art-header">
                <div class="art-logo">
                
                <table><tr><td width="800"><h1 class="art-logo-name"><a href="index.php">Sistema para la Administraci&oacute;n de Personal</a></h1><br />
                 </td>
                    <!--<h2 class="art-logo-text">Acciones de Personal</h2> -->
					<td width="112" align="center">
                    <img src="images/buddy2.PNG" align="top" width="28" />&nbsp;<span class="statPend"><br /><?php echo $_SESSION['usr_nombre']?></span><br />
                    <a title="ir a inicio" href="index.php"><img src="images/home.png" width="25" border="0" align="top" /></a>
                    <a href="javascript: closeThis()" title="cerrar aplicaci&oacute;n"><img src="images/exit.png" align="top" border="0" width="25" /></a></td></tr></table>
 
                                                </div>
                
            </div>
            <div class="cleared reset-box"></div>
<div class="art-bar art-nav">
<div class="art-nav-outer">
	<ul class="art-hmenu">
		<li>
			<a href="#" onclick="newEmp()" class="active">Registro de Empleados</a>
		</li>	
		<li>
			<a href="#" onclick="rptEmp('PlazaActiva')" >Reporte de Empleados</a>
		</li>
		<li>
			<a href="#" onclick="rptEmp('Historico')" >Historico de Empleados</a>
		</li>
	</ul>
</div>
</div>

<div class="art-box-body art-post-body">
        			<div id="msj" style="display:block">
                    <h2 class="art-postheader"  >
                    <br /><br /><br /><br />
                    <img src="images/LogoSkycom.png" width="300" /></td>
                 <td width="373"><br /><br />
                   Bienvenido(a)</h2><br />
                   <h3>Este sistema permite el registro de empleados, gestión de sus datos personales y generaci&oacute;n de informes de personal</h3>
                   <br /><br /><br /><br /><br />
                                        
                  
                     </div>
                     <div id="content" style="display:none"></div>
          </div>
          
          <div class="art-footer">
                <div class="art-footer-body">
                            <div class="art-footer-text">
                           <!-- <p>Copyright © 2012. ExpressTeleservices.</p> -->
                                                            </div>
                    <div class="cleared"></div>
                </div>
            </div>
          
        </div>
    </div>
</div>

</body>
</html>
