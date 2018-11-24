function validSession(){
    $.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=getSession",
	success: function(rslt){
		if(rslt.length==0 || rslt == ''){
			alert("Sesión no valida, necesita logearse nuevamente");
			location.reload();
		}
	}
	});

}


function onlyNumber(objeto,e){
  var keynum;
  var keychar;
  var numcheck;


  if(window.event){ /*/ IE*/
	keynum = e.keyCode
  } else if(e.which){ /*/ Netscape/Firefox/Opera/*/
	keynum = e.which
  }


  if((keynum>=35 && keynum<=37) || (keynum==8||keynum==9||keynum==39)) {
    return true;
  }

  if((keynum>=48&&keynum<=57) || (keynum>=96&&keynum<=105)){
    return true;
  } else {
    return false;
  }

}

function putGNit(objC){
  	nitP = objC.value;
	if(nitP.length==4 || nitP.length==11 || nitP.length==15){
		objC.value = nitP + '-';
	}
}

function compare_dates(fecha, fecha2)  
  {  
    var xMonth=fecha.substring(3, 5);  
    var xDay=fecha.substring(0, 2);  
    var xYear=fecha.substring(6,10);  
    var yMonth=fecha2.substring(3, 5);  
    var yDay=fecha2.substring(0, 2);  
    var yYear=fecha2.substring(6,10);  
    if (xYear> yYear)  
    {  
        return(true)  
    }  
    else  
    {  
      if (xYear == yYear)  
      {   
        if (xMonth> yMonth)  
        {  
            return(true)  
        }  
        else  
        {   
          if (xMonth == yMonth)  
          {  
            if (xDay> yDay)  
              return(true);  
            else  
              return(false);  
          }  
          else  
            return(false);  
        }  
      }  
      else  
        return(false);  
    }  
}  


function newEmp(){
	validSession();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=newEmp",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});		
}
function getDepart(IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=getDepart&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyDepart").innerHTML = rslt;
	}
	});	
}
function getPosc(IdD, IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=getPosc&idD="+IdD+"&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyPosc").innerHTML = rslt;
	}
	});	
}

function getSuperv(IdP){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=getSuperv&idP="+IdP,
	success: function(rslt){
			document.getElementById("lySuperv").innerHTML = rslt;
	}
	});	
}
function getDepartFiltros(IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=getDepartFiltros&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyDepart").innerHTML = rslt;
	}
	});
}
function getPoscFiltros(IdD){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=getPoscFiltros&idD="+IdD,
	success: function(rslt){
		document.getElementById("lyPosc").innerHTML = rslt;
	}
	});	
}
function getPoscFiltros2(IdD, IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=getPoscFiltros2&idD="+IdD+"&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyPosc").innerHTML = rslt;
	}
	});	
}

function save_emp(){
	Cod=$("#txtCod").val();
	Nombre=$("#txtNombre").val();
	Apellido=$("#txtApellido").val();
	Status=$("#lsStatus").val();
	Cuenta=$("#lsCuenta").val();
	Depart=$("#lsDepart").val();
	Posc = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	TipoPlaza = $("#lsTipoPlaza").val();
	Fec_admis = $("#fec_admis").val();
	Salario = $("#txtSalario").val();
	Bono = $("#txtBono").val();
	Pais = $("#lsCountry").val();
	NumCuenta = $("#txtNumCuenta").val();
	Dui = $("#txtDui").val();
	Nit = $("#txtNit").val();
	Isss = $("#txtIsss").val();
	Crecer = $("#txtAFPcrecer").val();
	Confia = $("#txtAFPconfia").val();
	Carnetmin = $("#txtCarnetMin").val();
	Ipsfa = $("#txtIpsfa").val();
	Fec_nac = $("#fec_nac").val();
	Direccion = $("#txtDireccion").val();
	Email = $("#txtEmail").val();
	Cel = $("#txtCel").val();
	Tel = $("#txtTel").val();
	Profesion = $("#txtProfesion").val();
	Headset = $("txtHeadset").val();
	Locker = $("#txtLocker").val();
	Prueba = $("#txtPrueba").val();
	Coment = $("#txtComent").val();
	AgentID = $("#txtAgentId").val();
	NotificationFlag = $("#lsNotificacion").val();

	if(Salario==""){Salario=0;}
	if(Bono==""){Bono=0;}
	if(Fec_nac.length<=0){Fec_nac="";}
	if(Prueba==""){Prueba=0;}
	if(Cod.length<=0){
		alert("Error: Debe ingresar Badge del empleado");
		$("#txtCod").focus();
		return false;
	}
	if(Nombre.length<=0){
		alert("Error: Debe ingresar el/los nombres del empleado");
		$("#txtNombre").focus();
		return false;
	}
	if(Apellido.length<=0){
		alert("Error: Debe ingresar el/los apellidos del empleado");
		$("#txtApellido").focus();
		return false;
	}
	if(Fec_admis.length<=0){
			alert("Error: Debe ingresar la fecha de ingreso a la empresa");
			$("#fec_admis").focus();
			return false;
		}
    // Estas validaciones solo se haran para empleados activos o inactivos 

	if(Status >= 0){
		if(Cuenta==0 || Cuenta == null ){
			alert("Error: Debe seleccionar una cuenta");
			$("lsCuenta").focus();
			return false;
		}
		if(Depart==0 || Depart == null){
			alert("Error: Debe seleccionar un departamento");
			$("lsDepart").focus();
			return false;
		}
		if(Posc==0 || Posc == null){
			alert("Error: Debe seleccionar una posicion para el empleado");
			$("lsPosc").focus();
			return false;
		}
		if(Superv==-1){
			alert("Error: Debe seleccionar un supervisor para el agente");
			$("#lsSuperv").focus();
			return false;
		}
 	}
 	else {
        Superv = 0;
	}
	if(Pais<= 0){
		alert("Error: Debe seleccionar un pais");
		$("#lsCountry").focus();
		return false;	
	}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=save_emp&cod="+Cod+"&nombre="+Nombre+"&apellido="+Apellido+
		"&status="+Status+"&cuenta="+Cuenta+"&depart="+Depart+"&posc="+Posc+
		"&superv="+Superv+"&fec_admis="+Fec_admis+"&salario="+Salario+
		"&bono="+Bono+"&numcuenta="+NumCuenta+"&dui="+Dui+"&nit="+Nit+
		"&isss="+Isss+"&crecer="+Crecer+"&confia="+Confia+"&carnetmin="+Carnetmin+
		"&ipsfa="+Ipsfa+"&fec_nac="+Fec_nac+"&direccion="+Direccion+"&email="+Email+
		"&cel="+Cel+"&tel="+Tel+"&profesion="+Profesion+"&headset="+Headset+
		"&locker="+Locker+"&prueba="+Prueba+"&coment="+Coment+"&tipoPlaza="+TipoPlaza+
		"&agentID="+AgentID+"&pais="+Pais+"&notificationFlag="+NotificationFlag,
	success: function(rslt){
			if(rslt==-1){
				alert("Error: El Codigo de Empleado ingresado ya existe");
				$("#txtCod").focus()
				return false;	
			}
			else if(rslt == -2){
				alert("Sesión no valida, necesita logearse nuevamente");
				location.reload();
			}
			else if(rslt>0){
				alert("Empleado registrado satisfactoriamente");
				loadEmp(rslt);	
			}
			else{
				alert("Error en la ejecucion, verificar datos de empleado");
				rptEmp();	
			}
	}
	});	
}

function loadEmp(IdE){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=loadEmp&idE="+IdE+"&accion=1",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	}
	});	
	
}

function rptEmp(TipoRte){
	validSession();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=rptEmp&tipoRte="+TipoRte,
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
		
	}
	});	
}

function load_rptemp(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	Fec_ini = $("#fec_ini").val();
	Fec_fin = $("#fec_fin").val();
	Nombre = $("#txtNombre").val();
	Username = $("#txtUsername").val();
	Estado = $("#lsEstado").val();
	Ini_Retiro = $("#ini_retiro").val();
	Fin_Retiro = $("#fin_retiro").val();
	if(Fec_ini.length>=1){
		if(Fec_fin.length>=1){
			if(compare_dates(Fec_ini,Fec_fin)){
				alert("Error: Ingrese datos correctos para el período de evaluación");
				$("#fec_ini").focus();
				return false;
			}
		}
		else{
		alert("Error: Debe seleccionar la fecha final");
		$("#fec_fin").focus();
		return false;
		}
	}
	if(Fec_fin.length>=1){
		if(Fec_ini.length<=0){
			alert("Error: Debe seleccionar la fecha inicial");
			$("#fec_ini").focus();
			return false;
			}
	}
	else{
		Fec_ini ="";
		Fec_fin ="";	
	}
	
	if(Ini_Retiro.length>=1){
		if(Fin_Retiro.length>=1){
			if(compare_dates(Ini_Retiro,Fin_Retiro)){
				alert("Error: Ingrese datos correctos para el período de evaluación");
				$("#ini_retiro").focus();
				return false;
			}
		}
		else{
		alert("Error: Debe seleccionar la fecha final");
		$("#fin_retiro").focus();
		return false;
		}
	}
	if(Fin_Retiro.length>=1){
		if(Ini_Retiro.length<=0){
			alert("Error: Debe seleccionar la fecha inicial");
			$("#ini_retiro").focus();
			return false;
			}
	}
	else{
		Ini_Retiro ="";
		Fin_Retiro ="";	
	}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=load_rptemp&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&superv="+Superv+"&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&nombre="+Nombre+"&username="+Username+"&estado="+Estado+"&ini_retiro="+Ini_Retiro+"&fin_retiro="+Fin_Retiro,
	success: function(rslt){
		document.getElementById("datos_rpt").innerHTML = rslt;
	}
	});	
		
}
function update_emp(IdE){
	FlagUpd = $("#flagUpdate").val();
	if(FlagUpd==2){
		alert("Acción no permitida");
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=loadEmp&idE="+IdE+"&accion=2",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	}
	});	
}
function sv_updateemp(IdE){
	Badge = $("#txtBadge").val();
	Nombre = $("#txtNombre").val();
	Apellido = $("#txtApellido").val();
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posc = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	TipoPlaza = $("#lsPlaza").val();
	Fec_admis = $("#fec_admis").val();
	PerPrueba = $("#txtPrueba").val();
	Salario = $("#txtSalario").val();
	Bono = $("#txtBono").val();
	Cta = $("#txtCta").val();
	Dui = $("#txtDui").val();
	Nit = $("#txtNit").val();
	Isss = $("#txtIsss").val();
	Crecer = $("#txtCrecer").val();
	Confia = $("#txtConfia").val();
	Minor = $("#txtMinor").val();
	Ipsfa = $("#txtIpsfa").val();
	Fec_nac = $("#fec_nac").val();
	Direccion = $("#txtDireccion").val();
	Email = $("#txtEmail").val();
	Celular = $("#txtCelular").val()
	Tel = $("#txtTel").val();
	Profesion = $("#txtProfesion").val();
	Locker = $("#txtLocker").val();
	Estado = $("#lsEstado").val();
	PhoneLogin = $("#txtPhoneLogin").val();
	AgentID = $("#txtAgentID").val();
	Pais = $("#lsCountry").val();
	NotificationFlag = $("#lsNotificacion").val();

	if(Badge.length<=0){
		alert("Error: Debe ingresar el código del empleado");
		$("#txtCod").focus();
		return false;
	}
	if(Nombre.length<=0){
		alert("Error: Debe ingresar el/los nombres del empleado");
		$("#txtNombre").focus();
		return false;
	}
	if(Apellido.length<=0){
		alert("Error: Debe ingresar el/los apellidos del empleado");
		$("#txtApellido").focus();
		return false;
	}
    if(Estado >= 0){
		if(Cuenta==0 || Cuenta == null){
			alert("Error: Debe seleccionar una cuenta");
			$("lsCuenta").focus();
			return false;
		}
		if(Depart==0 || Depart == null){
			alert("Error: Debe seleccionar un departamento");
			$("lsDepart").focus();
			return false;
		}
		if(Posc==0 || Posc == null){
			alert("Error: Debe seleccionar una posición para el empleado");
			$("lsPosc").focus();
			return false;
		}
		if(Superv==-1){
		alert("Error: Debe seleccionar un supervisor para el agente");
		$("#lsSuperv").focus();
		return false;
		}
 	}
 	else{
        Superv = 0;
	}
	if(Fec_admis.length<=0){
		alert("Error: Debe ingresar la fecha de ingreso a la empresa");
		$("#fec_admis").focus();
		return false;
	}
	if(Pais <= 0){
		alert("Error: Debe seleccionar un pais");
		$("#lsCountry").focus();
		return false;
	}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=sv_updateemp&idE="+IdE+"&cod="+Badge+"&nombre="+Nombre+"&apellido="+
		Apellido+"&cuenta="+Cuenta+"&depart="+Depart+"&posc="+Posc+"&superv="+
		Superv+"&fec_admis="+Fec_admis+"&perPrueba="+PerPrueba+"&salario="+
		Salario+"&bono="+Bono+"&numcuenta="+Cta+"&dui="+Dui+"&nit="+Nit+"&isss="+Isss+
		"&crecer="+Crecer+"&confia="+Confia+"&carnetmin="+Minor+"&ipsfa="+Ipsfa+
		"&fec_nac="+Fec_nac+"&direccion="+Direccion+"&email="+Email+"&cel="+Celular+
		"&tel="+Tel+"&profesion="+Profesion+"&locker="+Locker+"&tipoPlaza="+TipoPlaza+
		"&estado="+Estado+"&phoneLogin="+PhoneLogin+"&agentID="+AgentID+"&pais="+Pais+
		"&notificationFlag="+NotificationFlag,
	success: function(rslt){
			if(rslt==-1){
				alert("Error: El Codigo de Empleado ingresado ya existe");
				$("#txtBadge").focus()
				return false;	
			}
			else{
				alert("Empleado registrado satisfactoriamente");
				loadEmp(rslt);	
			}
	}
	});	
}
function load_rpthistemp(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	Fec_ini = $("#fec_ini").val();
	Fec_fin = $("#fec_fin").val();
	Nombre = $("#txtNombre").val();
	Username = $("#txtUsername").val();
	Estado = $("#lsEstado").val();
	Ini_Retiro = $("#ini_retiro").val();
	Fin_Retiro = $("#fin_retiro").val();
	if(Fec_ini.length>=1){
		if(Fec_fin.length>=1){
			if(compare_dates(Fec_ini,Fec_fin)){
				alert("Error: Ingrese datos correctos para el período de evaluación");
				$("#fec_ini").focus();
				return false;
			}
		}
		else{
		alert("Error: Debe seleccionar la fecha final");
		$("#fec_fin").focus();
		return false;
		}
	}
	if(Fec_fin.length>=1){
		if(Fec_ini.length<=0){
			alert("Error: Debe seleccionar la fecha inicial");
			$("#fec_ini").focus();
			return false;
			}
	}
	else{
		Fec_ini ="";
		Fec_fin ="";
	}

	if(Ini_Retiro.length>=1){
		if(Fin_Retiro.length>=1){
			if(compare_dates(Ini_Retiro,Fin_Retiro)){
				alert("Error: Ingrese datos correctos para el período de evaluación");
				$("#ini_retiro").focus();
				return false;
			}
		}
		else{
		alert("Error: Debe seleccionar la fecha final");
		$("#fin_retiro").focus();
		return false;
		}
	}
	if(Fin_Retiro.length>=1){
		if(Ini_Retiro.length<=0){
			alert("Error: Debe seleccionar la fecha inicial");
			$("#ini_retiro").focus();
			return false;
			}
	}
	else{
		Ini_Retiro ="";
		Fin_Retiro ="";
	}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=load_rpthistemp&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&superv="+Superv+"&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&nombre="+Nombre+"&username="+Username+"&estado="+Estado+"&ini_retiro="+Ini_Retiro+"&fin_retiro="+Fin_Retiro,
	success: function(rslt){
		document.getElementById("datos_rpt").innerHTML = rslt;
	}
	});
}
function formNewAttach(idEmp){
 	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=formNewAttach&idE="+idEmp,
	success: function(rslt){
	    document.getElementById('lyNewAttach').style.display = 'block';
		document.getElementById("lyNewAttach").innerHTML = rslt;
	}
	});
 }
function upFile(){
  ar = document.getElementById('flDoc').value;
  idEmp = document.getElementById('idE').value;
  if(ar.length <= 0){
    alert("Seleccione el archivo a subir!");
	document.getElementById('flDoc').focus;
	return false;
  }
  //return false;
  document.getElementById('lyMsgAttach').style.display = 'block';
  document.getElementById('btnUp').disabled = true;
  document.getElementById('frmAttach').submit();
  getDocList(idEmp);
}
function getDocList(idEmp){
	$.ajax({
	type: "POST",
  	url: "ajax/ajx_emp.php",
	data: "Do=getDocumentList&idE="+idEmp,
	success: function(rslt){
		document.getElementById('lyDocs').innerHTML = rslt;
		document.getElementById('lyMsgAttach').style.display = 'none';
  		document.getElementById('lyNewAttach').style.display = 'none';
	}
	});
}
function update_photo(idEmp){
 	$.ajax({
	type: "POST",
	url: "ajax/ajx_emp.php",
	data: "Do=formNewPhoto&idE="+idEmp,
	success: function(rslt){
	    document.getElementById('lyFormPhoto').style.display = 'block';
		document.getElementById("lyFormPhoto").innerHTML = rslt;
	}
	});
 }
function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}
 
function upPhoto(){
  ar = document.getElementById('flPhoto').value;
  idEmp = document.getElementById('idE').value;
  if(ar.length <= 0){
    alert("Seleccione la foto a cargar!");
	document.getElementById('flPhoto').focus;
	return false;
  }
  //return false;
  document.getElementById('lyMsgPhoto').style.display = 'block';
  document.getElementById('frmPhoto').submit();
  sleep(30);
  getPhoto(idEmp);
}
function getPhoto(idEmp){
	$.ajax({
	type: "POST",
  	url: "ajax/ajx_emp.php",
	data: "Do=getPhoto&idE="+idEmp,
	success: function(rslt){
		document.getElementById('lyPhoto').innerHTML = rslt;
		document.getElementById('lyMsgPhoto').style.display = 'none';
  		document.getElementById('lyFormPhoto').style.display = 'none';
	}
	});
}

function deleteFile(attachID,idEmp){
    if(confirm("Esta seguro que desea eliminar este archivo?")){
		$.ajax({
		type: "POST",
  		url: "ajax/ajx_emp.php",
		data: "Do=deleteFile&attachID="+attachID,
		success: function(rslt){
		    alert(rslt);
	  		getDocList(idEmp);
		}
		});
	}

}