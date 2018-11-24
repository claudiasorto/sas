<?php 
  require_once("db_funcs.php");
    $dbEx = new DBX;
  if($_SESSION['usr_id']>0){
	$sqlText = "select ap.app_id, app_name, app_descrip, app_image, app_link FROM exc_aplicaciones ap INNER JOIN appxuser apu ON ap.app_id=apu.app_id WHERE  EMPLOYEE_ID=".$_SESSION['usr_id'];
	$dtApp = $dbEx->selSql($sqlText);  
	}
  
  else{          
        header("location.ref='index.php';");
  }   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ingreso a la Intranet de Skycom</title>

<link rel="stylesheet" type="text/css" href="css/estilos.css" /> 
<link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui-1.8.16.custom.css"/>
<link rel="shortcut icon" href="images/logo.png">
<script src="js/common.js"></script>
<script src="js/jquery-1.5.1.min.js"></script>
<script src="js/jquery-ui-1.8.16.custom.min.js"></script>
<script src="js/user.js"></script>

<script language="javascript">

  function abrirApp(url,app){
    var ancho = window.screen.availWidth - 10;
    var alto = window.screen.availHeight - 30;

    var omenu = window.open(url, "pagina"+app, "width="+ancho+", height="+alto+",left=0,top=0,directories=no,location=no,titlebar=no,resizable=no,scrollbars=yes");
    omenu.focus();	  
  }

  function centerH(){
	if( document.body &&  document.body.clientWidth  ) 
    	w = document.body.clientWidth/2;
  	else
		w = window.innerWidth/2;

	return w;
  }
  //función para obtener el ancho de la ventana.
function getWidth() {
    var helper;
    if (null == (helper = document.getElementById('styleSwapHelper'))) {
        var helper = document.createElement('div');
        helper.style.position = 'absolute';
        helper.style.margin = '0';
        helper.style.padding = '0';
        helper.style.right = '0';
        helper.style.width = '10px';
        document.getElementsByTagName('body')[0].appendChild(helper);
    }
    return helper.offsetLeft + 10;
}

//función para obtener el alto de la ventana.
function getHeight() {
    if (self.innerHeight) { // MOS
        y = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientWidth) { // IE6 Strict
        y = document.documentElement.clientHeight;	
    } else if (document.body.clientHeight) { // IE quirks
        y = document.body.clientHeight;
    }

	return y;
}
  
  function showPwd(){
    x = parseInt(getWidth());
	y = parseInt(getHeight());	
	ly = document.getElementById('lyBlock'); 
	ly.style.display='block';
	ly.style.width = x + "px";
	ly.style.height = y + "px";	
	
	lyM = document.getElementById('lyPass');
	lyM.style.display='block';
	c = parseInt(centerH()) - 175;
	lyM.style.left = c + "px";
	lyM.style.top = "100px";
  }
  
   function hidePassScreen(){
    document.getElementById('txtPwdAct').value='';
	document.getElementById('txtPwdNew').value='';
	document.getElementById('txtPwdConf').value='';
	//document.getElementById('lyMsg').innerHTLM = '';
    document.getElementById('lyPass').style.display='none';
	document.getElementById('lyBlock').style.display='none';
  }

  
  function chgPwd(){
    passA = document.getElementById('txtPwdAct').value;
	passN = document.getElementById('txtPwdNew').value;
	passC = document.getElementById('txtPwdConf').value;
	
	if(passA.length <= 0){
	  alert("Enter your current password!");
	  document.getElementById('txtPwdAct').focus();
	  return false;
	}
		
	if(passN.length <= 0){
	  alert("Enter the new password!");
	  document.getElementById('txtPwdNew').focus();
	  return false;
	}
	
	if(passN!=passC){
	  alert("The new password and confirmation do not match!");
	  document.getElementById('txtPwdNew').value='';
	  document.getElementById('txtPwdConf').value='';
	  document.getElementById('txtPwdNew').focus();
	  return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_user.php",
	data: "Do=chgPwd&passAct="+passA+"&passNew="+passN,
	success: function(rslt){
		 if(rslt==2){
		      alert('Your password has been changed successfully!');
			  hidePassScreen();
			}
		if(rslt==0){
		      alert('The current password entered is incorrect!');
			  document.getElementById('txtPwdAct').value='';
			  document.getElementById('txtPwdNew').value='';
			  document.getElementById('txtPwdConf').value='';
	  		  document.getElementById('txtPwdAct').focus();
			}		
	}
	});		
  }
  function procesaHome(){  
  alert("aqui");  
    if(objReq.readyState==4){
	  if(objReq.status==200){
	    resp = objReq.responseText;
	    switch(accion){
		  case 1://cambio de idioma

		    if(resp==2){
		      alert('Your password has been changed successfully!');
			  hidePassScreen();
			}
			if(resp==0){
		      alert('The current password entered is incorrect!');
			  document.getElementById('txtPwdAct').value='';
			  document.getElementById('txtPwdNew').value='';
			  document.getElementById('txtPwdConf').value='';
	  		  document.getElementById('txtPwdAct').focus();
			}			
		    break;	
			
		  case 2://
		    location.href='index.php';
		    break;
		}
	  }
	}
  }
  
</script> 
</head>
<body>
<table width="90%">

<tr><td align="right" class="tit_login" colspan="3" align="right"><?php echo $_SESSION['usr_nombre']; ?>&nbsp;&nbsp;

<a href="#" onclick="closeApp()">
<img src="images/Shutdown.png" alt="Cerrar sesi&oacute;n...." width="40" height="40" border="0" /></a>
</td></tr>
<tr>
<td align="center" style="padding:10px;"><a href="#" onclick="showPwd();" class="alert" ><img src="images/change_pass.png"  alt="Change Password" width="250px" height="120px" border="0" id="imgPr1" title="Click to change password"/></a></td>
<!--<td align="center" style="padding:10px;"><a href="http://192.168.1.152/portal" class="alert" target="_blank"><img src="images/wonkas.jpg" alt="Ticket Portal" width="250px" height="120px" border="0" id="imgPr2" title="Click to enter the ticket system"/></a></td> -->
<?php 
$i=1;
foreach($dtApp as $dtA){
	$i++;
	$imgRoll = str_replace(".","1",$dtA['app_image']);
	?>
    
    <td align="center" style="padding:10px;">
    <a href="<?php echo $dtA['app_link'];?>"?app=<?php echo $dtA['app_id'];?>" class="alert" target="_blank"><img src="images/<?php echo $dtA['app_image']; ?>" border="0" alt="<?php echo $dtA['app_descrip'];?>" width="250px" height="120px" title="<?php echo $dtA['app_descrip'];?>"/></a></td>
    <?php
	if($i%3==0){ ?>
  </tr>
  <?php
	}//fin if
}// fin foreach
?>
<DIV class="block_screen" id="lyBlock" style=" position:absolute; display:none; width:0px; height:0; z-index:1; background-color:#AAAAAA; top:0px; left:0px;" align="center">
</DIV>
</table>
<div style="position:absolute; z-index:2; display:none;" id="lyPass">
    <table cellpadding="3" cellspacing="4" class="tblResult">
	  <tr><td align="center">
	    <table cellpadding="0" cellspacing="0" width="350" class="tblResult">
		  <tr><td colspan="2" height="5"></t	d></tr>
		  <tr><td colspan="2" align="left" class="tab_off" height="20" style="padding-left:10px;">Change Password</td></tr>
		  <tr><td colspan="2" height="10"></td></tr>
		  <tr>
		    <td class="itemForm" width="150">Current Password:</td>
			<td align="left"><input type="password" size="20" class="txtPag" id="txtPwdAct" /></td>
		  </tr>
		  <tr><td colspan="2" height="5"></td></tr>
		  <tr>
		    <td class="itemForm" width="150">New Password:</td>
			<td align="left"><input type="password" size="20" class="txtPag" id="txtPwdNew" /></td>
		  </tr>
		  <tr><td colspan="2" height="5"></td></tr>
		  <tr>
		    <td class="itemForm" width="150">Confirm Password:</td>
			<td align="left"><input type="password" size="20" class="txtPag" id="txtPwdConf" /></td>
		  </tr>
		  <tr><td colspan="2" height="3"></td></tr>
		  <tr><td colspan="2"><span id="LyMsg" class="loadMsg"></span></td></tr>
		  <tr><td colspan="2" height="3"></td></tr>
		  <tr><td colspan="2" height="10"></td></tr>
		  <tr><td colspan="2" align="left" style="padding-left:40px;"><input type="button" class="btn" value="Save new password" onclick="chgPwd();" /></td></tr>
		  <tr><td colspan="2" height="10"></td></tr>
		  <tr><td colspan="2" height="25" valign="bottom" align="right" style="padding-right:5px;">
		    <a href="javascript: hidePassScreen()" class="alert">[X] cerrar</a>
		  </td>
		  </tr>
		  <tr><td height="5"></td></tr>
		</table>
	  </td></tr>
	</table>
</div>
<div id="dialog-confirm" title="Empty the recycle bin?" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
    <span id="txtMsgDiag">texto</span></p>
</div>
<div id="dialog-ok" title="Empty the recycle bin?" style="display:none;">
	<p><span id="txtMsgDiagOK">texto</span></p>
</div>
<div id="dialog-load" title="Empty the recycle bin?" style="display:none;">
	<p><span id="txtMsgDiagLoad">texto</span></p>
</div>
<div id="dialog-show" title="datos" style="display:none;">
	<p><span id="txtMsgDiagDatos">texto</span></p>
</div>
</body>
</head>

