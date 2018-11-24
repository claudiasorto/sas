
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login to Intranet Skycom</title>
<link rel="stylesheet" type="text/css" href="css/estilos.css" /> 
<link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui-1.8.16.custom.css"/>
<link rel="shortcut icon" href="images/logo.png">
<script src="js/common.js"></script>
<script src="js/jquery-1.5.1.min.js"></script>
<script src="js/jquery-ui-1.8.16.custom.min.js"></script>
<script src="js/user.js"></script>
<script language="javascript">
 function veryfKey(e,t){
	if(e.keyCode){k=e.keyCode;}	
	else{k=e.which;}
	//comprobamos si la tecla presionada es ENTER
	if(k==13){
	    if(t==1){
		alert ('Debe Ingresar su Contraseña');
		document.getElementById("txtClave").focus();
	  }
	  else{	   
	    document.getElementById("lyBtnSave").focus();
		InApp();
	  }
	}
  }
</script>
</head>
<body onload="$('#txtUser').focus();">
<br /><br /><br />
<table align="center" cellpadding="2" cellspacing="0" border="1" style=" border-bottom-style:groove">
 <tr bgcolor="#378DD3"><td align="center"><img src="images/LogoSkycom.png" width="300" /></td></tr>
 <tr><td align="left">
  <table width="440" cellpadding="0" cellspacing="2" class="ui-state-active ui-corner-all">
    <tr><td height="5" colspan="2"></td></tr>
    <tr><td colspan="2" class="ui-state-default" style="padding-left:10px;">Enter your username and password</td></tr>
    <tr><td height="10" colspan="2"></td></tr>
    <tr><td height="10" colspan="2"></td></tr>
    <tr>
      <td width="117" class="ui-state-active" align="right" style="padding:5px;">Username:</td>
      <td width="321" class="ui-state-active" style="padding:5px;"><input type="text" size="30" maxlength="20" tabindex="1" id="txtUser" class="txtFormBlue" onkeydown="veryfKey(event,1)" /></td>
    </tr>
    <tr><td height="5" colspan="2"></td></tr>
    <tr>
      <td class="ui-state-active" align="right" style="padding:5px;">Password:</td>
      <td class="ui-state-active" style="padding:5px;"><input type="password" class="txtFormBlue" id="txtClave" tabindex="2" size="30" maxlength="20" onkeydown="veryfKey(event,2)" /></td>
    </tr>
    <tr><td height="10" colspan="2"></td></tr>
    <tr><td height="5" colspan="2" bgcolor="#FFFFFF"></td></tr>
    <tr bgcolor="#FFFFFF">
      <td colspan="2" align="center"><a href="javascript: InApp()" style="color:#036;"><span id="lyBtnSave" class="ui-corner-all btn">&nbsp;Log in</span></a>
  </td> 
	</tr>
    <tr><td height="5" colspan="2" bgcolor="#FFFFFF"></td></tr>
    <tr><td height="10" colspan="2"></td></tr>
  </table> 
 </td></tr>
</table>
<table align="center">
</table>
<div id="dialog-confirm" title="Empty the recycle bin?" style="display:none;">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><span id="txtMsgDiag">texto</span></p>
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
</html>