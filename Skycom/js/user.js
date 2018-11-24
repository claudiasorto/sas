function InApp(){
  msgError = "";band = 0;
  login = $("#txtUser").val();
  clave = $("#txtClave").val();
  if(login.length<=0){
    msgError = msgError + '- Enter your <font class="ui-state-error-text"> username</font><br>';
    band = -1;	
  }
  if(clave.length <= 0){
    msgError = msgError + '- Enter your <font class="ui-state-error-text">Password</font><br>';
    band = -1;
  }
  
  if(band==-1){ //se encontraron errores    
    crearDiagOK("SKYCOM", msgError, false, 170, 430, "");
  } else{
    crearDiagLoad("loading...", "loading page, Please wait...", true, 125,300);
    $.ajax({
      type: "POST",
      url: "ajax/ajx_user.php",
      data: "Do=inApp&Login="+login+"&Clave="+clave,
      success: function(rslt){    	
        $("#dialog-load").dialog("close");
	switch(parseInt(rslt)){
	  case -1: //usuario no encontrado
	    crearDiagOK("SKYCOM", 'Error: The user name does not correspond to any user of the system', true, 200, 450, "");
	    $('#txtUser').attr("value","");
            $('#txtClave').attr("value","");
            break;
	  case -2: //password incorrecto
            crearDiagOK("SKYCOM", "Error: The password entered is wrong", true, 200, 450, "$('#txtClave').focus()");
            $('#txtClave').attr("value","");
	    break;
	  case -3: //usuario inactivo
            crearDiagOK("SKYCOM", "Error: The user is Off! <br> Please contact the administrator", true, 200, 450, "$('#txtClave').focus()");
            break;
	  case 2:
            crearDiagOK("SKYCOM", 'Welcome to the SkyCom Administrative System.', true, 200, 450, "location.href='home.php';");
            break;
	 }
      },
      error: function(rslt) {
        crearDiagOK("SKYCOM", 'There was an error in the execution '+rslt+'. <br>try again', true, 200, 450, "");
      }
    });
  }	
}

function closeApp(){
  crearDiagYN("SKYCOM", "Do you want logout?", true, 170, 370, "logOut();")
}

function logOut(){ //salimos de la aplicación
 crearDiagLoad("Procesando...", "cerrando sesi&oacute;n de usuario, espere...", true, 125,320);
  $.ajax({
    type: "POST",
    url: "ajax/ajx_user.php",
    data: "Do=logOut",
    success: function(rslt){
      $("#dialog-load").dialog("close");
      location.href = 'index.php';
    }, error: function(rslt) {
      crearDiagOK("SKYCOM", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
    }
  });
}

function usr_showList(nP){
  tipo = $("#lsTipoU").val();
  crearDiagLoad("Cargando...", "cargando listado de usuarios, espere...", true, 125,300);
  $.ajax({
    type: "POST",
    url: "ajax/ajx_user.php",
    data: "Do=usr_showList&nP="+nP+"&Tipo="+tipo,
    success: function(rslt){
      datRslt = rslt.split("|-|");
      $("#lyUsrRslt").attr("innerHTML", datRslt[0]);
      $("#lyUsrPag").attr("innerHTML", datRslt[1]);
      $("#dialog-load").dialog("close");
      $("input").attr("disabled",false);
    }, error: function(rslt) {
      crearDiagOK("SKYCOM", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
    }
  });
}

function usr_cancelPwd(){
  crearDiagYN("SKYCOM","Desea salir sin guardar los datos?",true,170,350,"location.href='home.php';");
}

function usr_savePwd(){
  if(usr_validaFrmPwd()){
    actP = $('#pwdAct').val();
    newP = $('#pwdNew').val();
    confP = $('#pwdConf').val();

    crearDiagLoad("Ejecutando", "Guardando datos, favor espere...", true, 100,300);
    $("input").attr("disabled", true);
    $.ajax({
      type: "POST",
      url: "ajax/ajx_user.php",
      data: "Do=usr_savePwd&ActP="+actP+"&NewP="+newP,
      success: function(rslt){
        $("#dialog-load").dialog("close");
        $("input").attr("disabled",false);
        if(parseInt(rslt)==2){
          crearDiagOK("SKYCOM", 'Su contrase&ntilde;a ha sido cambiada satisfactoriamente ', true, 200, 400, "location.href='home.php'");
        } else{
          crearDiagOK("SKYCOM", 'La contrase&ntilde;a actual es incorrecta, intente de nuevo', true, 200, 450, "");
          $('#pwdAct').focus();
        }
      }, error: function(rslt) {
        crearDiagOK("SKYCOM", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
      }
    });
  }
}

function usr_validaFrmPwd(){
  msgError = "";band = 0;
  actP = $('#pwdAct').val();
  newP = $('#pwdNew').val();
  confP = $('#pwdConf').val();

  if(actP.length<=0){
    msgError = msgError + '- Ingrese su contrase&ntilde;a <font class="ui-state-error-text">Actual</font><br>';
    band = -1;
  }
  if(newP.length <= 0){
    msgError = msgError + '- Ingrese su <font class="ui-state-error-text">Nueva</font> contrase&ntilde;a<br>';
    band = -1;
  }
  if(confP!=newP){
    msgError = msgError + '- La <font class="ui-state-error-text">Nueva</font> contrase&ntilde;a y la <font class="ui-state-error-text">Confirmaci&oacute;n</font> no coinciden<br>';
    band = -1;
  }

  if(band==-1){ //se encontraron rerrores
    crearDiagOK("Notificaci&oacuten de errores", msgError, false, 200, 430, "");
    return false;
  } else{
    return true;
  }
  
}


function usr_validaForm(){ //valida los datos requeridos en el formulario
  msgError = "";band = 0;
  tipo = $("#lsTipoU").val();
  nombre = $("#txtNombre").val();
  email = $("#txtMail").val();
  login = $("#txtLogin").val();
  clave = $("#txtPwd").val();
  conf = $("#txtConf").val();
  estado = $("#lsStatus").val();

  if(tipo==0){
    msgError = msgError + '- Seleccione el <font class="ui-state-error-text">Tipo de Usuario</font><br>';
    band = -1;	
  }
  if(nombre.length < 3){
    msgError = msgError + '- Ingrese el <font class="ui-state-error-text">nombre completo</font> del usuario<br>';
    band = -1;
  }
  if(email.length < 3){
    msgError = msgError + '- Ingrese el <font class="ui-state-error-text">correo electr&oacute;nico</font> del usuario<br>';
    band = -1;
  }
  if(login.length <= 0){
    msgError = msgError + '- Ingrese el <font class="ui-state-error-text">Login</font> de el usuario<br>';
    band = -1;
  }  
  if(clave.length <= 0){
    msgError = msgError + '- Ingrese la <font class="ui-state-error-text">Contrase&ntilde;a</font> de el usuario<br>';
    band = -1;
  }
  if(conf != clave){
    msgError = msgError + '- La <font class="ui-state-error-text">Confirmaci&oacute;n</font> de la  contrase&ntilde;a no coincide<br>';
    band = -1;
  }
  if(estado==0){
    msgError = msgError + '- Seleccione el <font class="ui-state-error-text">Status</font> para el usuario<br>';
    band = -1;
  }

  if(band==-1){ //se encontraron rerrores    
    crearDiagOK("Notificaci&oacuten de errores", msgError, false, 240, 430, "");
    return false;
  } else{
    return true;
  }
}

function usr_cancel(){
  crearDiagYN("SKYCOM","Desea salir sin guardar los datos?",true,170,350,"loadPage('../usr/usr_list.php');");
}

function usr_save(){ //guarda un nuevo usuario
  if(usr_validaForm()){
    tipo = $("#lsTipoU").val();
    nombre = $("#txtNombre").val();
    email = $("#txtMail").val();
    login = $("#txtLogin").val();
    clave = $("#txtPwd").val();
    conf = $("#txtConf").val();
    estado = $("#lsStatus").val();
    crearDiagLoad("Ejecutando", "Guardando datos, favor espere...", true, 100,300);
    $.ajax({
      type: "POST",
      url: "ajax/ajx_user.php",
      data: "Do=usr_save&Tipo="+tipo+"&Nombre="+nombre+"&Email="+email+"&Login="+login+
          "&Clave="+clave+"&Estado="+estado,
      success: function(rslt){
        $("#dialog-load").dialog("close");
        $("input").attr("disabled",false);
        crearDiagOK("SKYCOM", 'Se ha creado el nuevo usuario satisfactoriamente ', true, 200, 400, "loadPage('../usr/usr_list.php')");
      }, error: function(rslt) {
        crearDiagOK("SKYCOM", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
      }
    });  
  }
}

function usr_edit(idU){
  crearDiagLoad("Cargando...", "cargando datos a editar, espere...", true, 125,300);
  $.ajax({
    type: "POST",
    url: "ajax/ajx_user.php",
    data: "Do=usr_edit&ID="+idU,
    success: function(rslt){
      datRslt = rslt.split("|-|");
      $('#lyMain').attr('innerHTML', datRslt[0]);
      $("#dialog-load").dialog("close");
      eval(datRslt[1]);
    }, error: function(rslt) {
      crearDiagOK("SKYCOM", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
    }
  });
}

function usr_update(){  
  idU = $('#idU').val();
  tipo = $("#lsTipoU").val();
  nombre = $("#txtNombre").val();
  email = $("#txtMail").val();
  login = $("#txtLogin").val();
  clave = $("#txtPwd").val();
  conf = $("#txtConf").val();
  estado = $("#lsStatus").val();
  crearDiagLoad("Ejecutando", "Guardando datos, favor espere...", true, 100,300);
  $.ajax({
    type: "POST",
    url: "ajax/ajx_user.php",
    data: "Do=usr_update&Tipo="+tipo+"&Nombre="+nombre+"&Email="+email+"&Login="+login+
          "&Clave="+clave+"&Estado="+estado+"&ID="+idU,
    success: function(rslt){
      $("#dialog-load").dialog("close");
      $("input").attr("disabled",false);
      crearDiagOK("SKYCOM", 'Los datos del usuario han sido actualizados! ', true, 200, 400, "loadPage('../usr/usr_list.php')");
    }, error: function(rslt) {
      crearDiagOK("SKYCOM", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
    }
  });
}


function usr_del(idU){ //confirmación para borrar el registro seleccionado.
  crearDiagYN("SKYCOM", "Est&aacute; segur@ de querer borrar este usuario", true, 170, 400, "usr_borrar("+idU+")");
}

function usr_borrar(idU){ //se borra una atención
  crearDiagLoad("Eliminando...", "borrando registro, espere...", true, 125,300);
  $.ajax({
    type: "POST",
    url: "ajax/ajx_user.php",
    data: "Do=usr_borrar&ID="+idU,
    success: function(rslt){
      if(rslt==2){
        $("#dialog-load").dialog("close");
        $("#trUsr_"+idU).fadeOut(1000);
      }
    }, error: function(rslt) {
      crearDiagOK("SKYCOM", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
    }
  });
}
