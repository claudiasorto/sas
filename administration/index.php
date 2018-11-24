<?php
require_once("db_funcs.php");
$dbEx = new DBX;

  if(isset($_SESSION['logged_app']) && $_SESSION['logged_app']==1){
  	  
  } else{
    //no se ha logueado
	header("location: ../Skycom/index.php");
  }

?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Administration</title>
    <link rel="shortcut icon" href="images/logo.png">
    
    <link rel="stylesheet" href="css/estilos.css" />
    <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <link rel="stylesheet" type="text/css" href="sdmenu/sdmenu.css" />
    <link rel="stylesheet" type="text/css" media="all" href="calendar/skins/aqua/theme.css" title="Aqua" />
    
    <script src="js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="sdmenu/sdmenu.js"></script> 
    <script src="js/js_administrator.js"></script>
    <script type="text/javascript" src="calendar/calendar.js"></script>
    <!-- import the language module -->
    <script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
    <!-- helper script that uses the calendar -->
    <script type="text/javascript" src="js/calen_js.js"></script>


	<script type="text/javascript" language="javascript">
  	function closeThis(){
    if(confirm("To close the application")){
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
	<!-- <h3>ExpressTeleservices</h3>
    <p>Copyright © 2012.</p> -->
	
</div>
<div id="plantilla">
	<div id="posts">
	  <div id="lyMain">
     	 <div id="msj" style="display:block">

      		<h2 class="title">Administration!</h2><br>
      		<img src="images/LogoSkycom.png" width="300px" />
      	</div>
        <div id="content" style="display:none"></div>
      </div>
	</div>  
  
  	<div id="Inicio" class="boxed">
	  <h2 class="heading" style="cursor:pointer;" id="mnuStart"><a title="Home" href="index.php"><img src="images/home.png" width="25" border="0" align="top" /></a></h2>		
	</div>
	 <div style="float: left" id="my_menu" class="sdmenu">
     
      <div class="collapsed">
	     <span>Operations</span>
			<ul>
				<li><a href="#" onClick="updatePass()" >Reset Password</a></li>
                <li><a href="#" onClick="newApp()">Aplications</a></li>
                <li><a href="#" onClick="loadAccount()">Accounts</a></li>
                <li><a href="#" onClick="loadDepto()">Departments</a></li>
                <li><a href="#" onClick="loadPosc()">Positions</a></li>
                <li><a href="#" onClick="loadPlacexDep()">Assign position</a></li>
                <li><a href="#" onClick="apSetup()">AP Setup</a></li>
			</ul>
            
	</div>
	<div id="blogroll" class="boxed">
		<h2 class="heading"><font style="color:#CCCCCC; cursor: pointer;">USER</font></h2>
		<div class="content">
			<ul>
			  <li><img src="images/buddy2.PNG" align="top" width="28" />&nbsp;<font color="#003366"><b><?php echo $_SESSION['usr_nombre']?></b></font></li>
			</ul>
       <div id="blogroll" class="boxed">
          <ul>
            <li>
            <font style="color:#CCCCCC; cursor: pointer;" title="close aplication" onclick="closeThis()"><img src="images/shut_down.png" width="30%"></font>
            </li>
            </ul>
       </div>
		</div>
	</div>
</div>

</body>
</html>
