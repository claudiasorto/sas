<?php
require_once("db_funcs.php");
$dbEx = new DBX;
 if(isset($_SESSION['logged_app']) && $_SESSION['logged_app']==1){
  	  
  } else{
    //no se ha logueado
	header("location: ../ExpressTel/index.php");
  }
  
  $sqlText = "select firstname, lastname, username, h.hrreq_id, h.tpreq_id, hrreq_authorizer, hrreq_content, date_format(hrreq_date,'%d/%m/%Y') as fecReq, date_format(hrreq_dayresponse,'%d/%m/%Y') as fecRespuesta, hrreq_response, hrreq_status, tpreq_name from hrrequest h inner join type_request tr on h.tpreq_id=tr.tpreq_id inner join employees e on e.employee_id=h.employee_id where hrreq_status='O'";
  $dtReq = $dbEx->selSql($sqlText);
	$tblResult = '<table cellpadding="3" cellspacing="1" width="80%" border="1" class="backTablaMain" align="center" bordercolor="#BFD1DF">';
	$tblResult .='<tr><td colspan="9">Matches: '.$dbEx->numrows.'</td></tr>';
	$tblResult .='<tr class="showItem" >
		<td width="2%">#</td>
		<td width="5%">Badge</td>
		<td width="17%">Employee</td>
		<td width="15%">Category</td>
		<td width="5%">Date</td>
		<td width="16%">Assigned to</td>
		<td width="10%">Date of resolution</td>
		<td width="20%">Reply</td>
		<td width="5%">Form</td>
		<td width="5%"></td></tr>';
		if($dbEx->numrows>0){
			foreach($dtReq as $dtR){
				$responsable = "";
				if($dtR['hrreq_authorizer']>0){
					$sqlText = "select firstname, lastname from employees where employee_id=".$dtR['hrreq_authorizer'];
					$dtA = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						$responsable = $dtA['0']['firstname']." ".$dtA['0']['lastname'];	
					}
				}
				$btn = "";
				if($dtR['hrreq_status']=='O'){
					$btn = '<img src="images/close.png" alt="Close request" title="Close request"  width="80" onclick="closeReq('.$dtR['hrreq_id'].')" >';
				}
				
				$tblResult .='<tr class="rowCons" onclick="getDetallesRequest('.$dtR['hrreq_id'].')">
				<td>'.$dtR['hrreq_id'].'</td>
				<td>'.$dtR['username'].'</td>
				<td>'.$dtR['firstname'].' '.$dtR['lastname'].'</td>
				<td>'.$dtR['tpreq_name'].'</td>
				<td>'.$dtR['fecReq'].'</td>
				<td>'.$responsable.'</td>
				<td>'.$dtR['fecRespuesta'].'</td>
				<td>'.$dtR['hrreq_response'].'</td>
				<td><img src="images/llenar_formulario.jpg" alt="create document" title="create document" style="cursor:pointer" width="40" onclick="createDoc('.$dtR['hrreq_id'].','.$dtR['tpreq_id'].')" ></td>
				<td>'.$btn.'</td>
				</tr>';
			}
		}
		$tblResult .='</table>';
		


  
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"[]>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" xml:lang="en">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>HR Request</title>



    <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" media="all" href="calendar/skins/aqua/theme.css" title="Aqua" />
    <link rel="stylesheet" href="css/estilos.css" type="text/css" media="screen" />
	<link rel="shortcut icon" href="images/favicon.ico">
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="calendar/calendar.js"></script>
    <script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
    <script type="text/javascript" src="js/calen_js.js"></script>
    <script type="text/javascript" src="js/js_request.js"></script>
    
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
                <script type="text/javascript" src="swfobject.js"></script>
                        <script type="text/javascript">
                        jQuery((function (swf) {
                            return function () {
                                swf.switchOffAutoHideShow();
                                swf.registerObject("art-flash-object", "9.0.0", "expressInstall.swf");
                            }
                        })(swfobject));
                        </script>
                        <div id="art-flash-area">
                        <div id="art-flash-container">
                        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="431" id="art-flash-object">
                            <param name="movie" value="images/flash.swf" />
                            <param name="quality" value="high" />
                        	<param name="scale" value="exactfit" />
                        	<param name="wmode" value="transparent" />
                        	<param name="flashvars" value="color1=0xFFFFFF&amp;alpha1=.65&amp;framerate1=13&amp;loop=true&amp;wmode=transparent" />
                            <param name="swfliveconnect" value="true" />
                            <!--[if !IE]>-->
                            <object type="application/x-shockwave-flash" data="images/flash.swf" width="100%" height="431">
                                <param name="quality" value="high" />
                        	    <param name="scale" value="exactfit" />
                        	    <param name="wmode" value="transparent" />
                        	    <param name="flashvars" value="color1=0xFFFFFF&amp;alpha1=.65&amp;framerate1=13&amp;loop=true&amp;wmode=transparent" />
                                <param name="swfliveconnect" value="true" />
                            <!--<![endif]-->
                              	<div class="art-flash-alt"><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></div>
                            <!--[if !IE]>-->
                            </object>
                            <!--<![endif]-->
                        </object>
                        </div>
                        </div>
                        <div class="art-logo">
                             <h1 class="art-logo-name"><a href="#">HR Request</a></h1>
                             
                             
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
                        <div class="art-layout-cell art-sidebar1">
<div class="art-box art-vmenublock">
    <div class="art-box-body art-vmenublock-body">
                <div class="art-box art-vmenublockcontent">
                    <div class="art-box-body art-vmenublockcontent-body">
                
     <br /><br />
     <ul class="art-vmenu">
		<li>
		<a href="#" onclick="formGetRequest()" class="active">Requests</a>
		<!--<ul class="active">
			<li>
                <a href="./new-page/new-page.html">Subpage 1</a>

            </li>
			<li>
                <a href="./new-page/new-page-2.html">Subpage 2</a>

            </li>
			<li>
                <a href="./new-page/new-page-3.html">Subpage 3</a>

            </li>
		</ul>-->
		</li>	
		<li>
			<a href="#" onclick="hrForms()">Forms</a>
		</li>
        <li>
        	<a href="#">Summary</a>
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
                        <div class="art-layout-cell art-content">
<div class="art-box art-post">
    <div class="art-box-body art-post-body">

		
          <div id="msj">
        <!-- <h2 class="art-postheader">Human Resource Request<br /><br />
         </h2>-->
         
         <br />
		</div>
         
         <br />
         <div id="lyContent">
                
         <?php echo $tblResult; ?>
         </div>
		<br /><br />

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
                                <p><img src="images/logo.png" width="200" /></p>
                               <p>Copyright © 2013. ExpressTeleservices  All Rights Reserved.</p>
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
