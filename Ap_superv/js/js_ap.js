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


function newap(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=newAp",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});	
}
function emp(IdAp){	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=emp&idAp="+IdAp,
	success: function(rslt){
		document.getElementById("lyemp").innerHTML = rslt;
 
	}
	});	
}
function empxdep(IdD){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=empxdep&idD="+IdD,
	success: function(rslt){
		document.getElementById("lyempdep").innerHTML = rslt;
	}
	});		
}

function frmap(){
	Emp = $("#lsemp").val();
	Ap = $("#lstap").val();
	if(Emp <= 0){
		alert('Error: Debe seleccionar un empleado');
		$("#lsemp").focus();
		return false;
	}
	if(Ap <= 0){
		alert('Error: Debe seleccionar un tipo de acción de personal ');
		$("#lstap").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=frmap&emp="+Emp+"&ap="+Ap,
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
	url: "ajax/ajx_rrhh.php",
	data: "Do=getDepart&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyDepart").innerHTML = rslt;
	}
	});	
}
function getDepartTras(IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getDepartTras&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyDepart").innerHTML = rslt;
	}
	});	
}

function getPosc(IdD, IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getPosc&idD="+IdD+"&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyPlaza").innerHTML = rslt;
	}
	});	
}

function getPosc2(IdD, IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getPosc2&idD="+IdD+"&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyPlaza").innerHTML = rslt;
	}
	});	
}

function getSuperv(IdP){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getSuperv&idP="+IdP,
	success: function(rslt){
		document.getElementById("lySuperv").innerHTML = rslt;
	}
	});		
}

function consultap(IdE){
	$("#cons_ap").css("display","block");
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=consultap&idE="+IdE,
	success: function(rslt){
			document.getElementById("cons_ap").innerHTML = rslt;
	}
	});
}
function upcons(){
	$("#cons_ap").css("display","none");
}

function update_ap(){
	Id = $("#apxemp").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=loadApxE&id="+Id+"&accion=2"+"&autorizar=0",
	success: function(rslt){
		if(rslt=="-1"){
			alert("No está permitido realizar modificaciones ya que la acción de personal se encuentra en su etapa de aprobación");
			return false;
			}
		if(rslt=="-2"){
			alert("Acción no permitida");
			return false;
			}
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;	
	}
	});		
}

function sv_appermiso(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Fec_ini = $("#fecha_inicio").val();
	Fec_fin = $("#fecha_fin").val();
	Observ = $("#txtObserv").val();
	Horas = $("#txtHoras").val();
	if(Fec_ini.length <= 0){
		alert("Error: La fecha inicial no puede quedar en blanco");
		$("#fechainicio").focus();
		return false;
		}
	if(Fec_fin.length <= 0){
		alert("Error: La fecha final no puede quedar en blanco");
		$("#fechafin").focus();
		return false;
		}
	if(compare_dates(Fec_ini,Fec_fin)){
		alert("Error: La fecha final no puede ser menor a la fecha inicial");
		$("#fechainicio").focus();
		return false;
		}
	if(Horas <= 0){
		alert("Error: Las horas de permiso no pueden ser igual a 0");
		$("#txtHoras").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para el permiso");
		$("#txtObserv").focus();
		return false;
		}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_appermiso&idAp="+IdAp+"&idE="+IdE+"&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&observ="+Observ+"&horas="+Horas,
	success: function(rslt)
	{
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			sendNotification(rslt);
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}

function sv_traslados(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Fec_ini = $("#fecha_inicio").val();
	Dpto = $("#lsDpto").val();
	Plaza = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	Observ = $("#txtObserv").val();
	IdCs = $("#idCs").val();
	IdPxe = $("#idPxe").val();

	if(Observ.length <=0){ Observ = '';}
	if(Fec_ini.length <= 0){
		alert("Error: La fecha inicial no puede quedar en blanco");
		$("#fechainicio").focus();
		return false;
		}
	if(Dpto == 0){
		alert("Error: Debe seleccionar el departamento al cual ha sido trasladado");
		$("#lsDpto").focus();
		return false;
		}
	if(Plaza ==0 || Plaza.length <= 0){
		alert("Error: Debe seleccionar la posición del empleado");
		$("#lsPosc").focus();
		return false; 
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para el traslado");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_traslados&idAp="+IdAp+"&idE="+IdE+"&fec_ini="+Fec_ini+"&dpto="+Dpto+"&plaza="+Plaza+"&superv="+Superv+"&observ="+Observ+"&idCs="+IdCs+"&idPxe="+IdPxe,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}

function saveup_traslado(){
	Id = $("#apxemp").val();
	Fec_ini = $("#fecha_inicio").val();
	Cuenta = $("#lsCuenta").val();
	Depto = $("#lsDpto").val();
	Superv = $("#lsSuperv").val();
	Posicion = $("#lsPosc").val();
	Observ = $("#txtObserv").val();
	
	if(Fec_ini.length<=0 ){
		alert("Error: Debe seleccionar la fecha efectiva del traslado");
		$("#fecha_inicio").focus();
		return false;
		}
	if(Cuenta==0){
		alert("Error: Debe seleccionar una cuenta");
		$("#lsCuenta").focus();
		}
	if(Depto==0){
		alert("Error: Debe seleccinar un departamento");
		$("#lsDpto").focus()
		return false;
		}
	if(Posicion == 0 || Posicion.length <= 0){
		alert("Error: Debe seleccionar la posición del empleado");
		$("#lsPosc").focus();
		return false;
		}
	if(Superv == 0 || Superv.length <= 0){
		alert("Error: Debe seleccionar el supervisor del empleado");
		$("#lsSuperv").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para el traslado");
		$("#txtObserv").focus();
		return false;
		}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_traslado&id="+Id+"&fec_ini="+Fec_ini+"&cuenta="+Cuenta+"&depto="+Depto+"&posicion="+Posicion+"&supervisor="+Superv+"&observ="+Observ,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}

function sv_reingresos(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Fec_ini = $("#fecha_inicio").val();
	Dpto = $("#lsDpto").val();
	Plaza = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	Observ = $("#txtObserv").val();
	IdCs = $("#idCs").val();
	IdPxe = $("#idPxe").val();
	if(Fec_ini.length <= 0){
		alert("Error: La fecha inicial no puede quedar en blanco");
		$("#fechainicio").focus();
		return false;
		}
	if(Dpto == 0){
		alert("Error: Debe seleccionar el departamento al cual ha sido trasladado");
		$("#lsDpto").focus();
		return false;
		}
	if(Plaza ==0){
		alert("Error: Debe seleccionar la posición del empleado");
		$("#lsPosc").focus();
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para el reingreso");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_reingresos&idAp="+IdAp+"&idE="+IdE+"&fec_ini="+Fec_ini+"&dpto="+Dpto+"&plaza="+Plaza+"&superv="+Superv+"&observ="+Observ+"&idCs="+IdCs+"&idPxe="+IdPxe,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			sendNotification(rslt);
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
		}
	});
}

function sv_vacacion(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Dias = $("#txtdias").val();
	Fec_ini = $("#fecha_inicio").val();
	Observ = $("#txtObserv").val();
	if(Fec_ini.length <= 0){
		alert("Error: La fecha inicial no puede quedar en blanco");
		$("#fechainicio").focus();
		return false;
		}
	if(Dias.length == 0){
		alert("Error: Debe ingresar la cantidad de días solicitados para vacación");
		$("#txtdias").focus();
		return false;
		}
	if(Dias == 0){
		alert("Error: La cantidad de días de vacación debe ser mayor a 0");
		$("#txtdias").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para la vacación");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_vacaciones&idAp="+IdAp+"&idE="+IdE+"&fec_ini="+Fec_ini+"&dias="+Dias+"&observ="+Observ,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			sendNotification(rslt);
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}
function saveup_vacacion(){
	Id = $("#apxemp").val();
	Dias = $("#txtDias").val();
	Fec_ini = $("#fecha_ini").val();
	Observ = $("#txtObserv").val();
	if(Fec_ini.length <= 0){
		alert("Error: La fecha inicial no puede quedar en blanco");
		$("#fechainicio").focus();
		return false;
		}
	if(Dias.length == 0){
		alert("Error: Debe ingresar la cantidad de días solicitados para vacación");
		$("#txtdias").focus();
		return false;
		}
	if(Dias == 0){
		alert("Error: La cantidad de días de vacación debe ser mayor a 0");
		$("#txtDias").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar un comentario relevante para la vacación");
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_vacaciones&id="+Id+"&dias="+Dias+"&fec_ini="+Fec_ini+"&observ="+Observ,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
		
}

function suspend(Opc){
	if(Opc==3){
 		$("#lySusp").css("display","block");
		document.getElementById("idDisc").value = 3;}
	if(Opc==2){
		$("#lySusp").css("display","none");
		document.getElementById("idDisc").value = 2;}
	if(Opc==1){
		$("#lySusp").css("display","none");
		document.getElementById("idDisc").value = 1;}
}
function sv_disciplinarias(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Falta = $("#lsFalta").val();
	Disc = $("#idDisc").val();
	Dias = $("#diasSusp").val();
	Fecha = $("#fecha_inicio").val();
	Observ = $("#txtObserv").val();
	
	if(Falta <=0){
		alert("Error: Debe elegir un tipo de falta disciplinaria ");
		$("#lsFalta").focus();
		return false;
		}
	if(Disc.length==0){
		alert("Error: Debe elegir el tipo de acción disciplinaria a aplicar");
		$("#optDisc").focus();
		return false;
		}
	if(Disc==3){
		if(Fecha.length <=0){
			alert("Error: Debe seleccionar la fecha de inicio de la suspensión");
			$("#fecha_inicio").focus();
			return false;
			}
		if(Dias.length <=0){
			alert("Error: Debe ingresar la cantidad de días de suspensión");
			$("#diasSusp").focus();
			return false;
			}
		}
	else{
		//var f = new Date();
		//Fecha = f.getDate() + "/" + f.getMonth() + "/" + f.getFullYear();
		Dias = 0;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para la sanción disciplinaria");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_disciplinarias&idAp="+IdAp+"&idE="+IdE+"&falta="+Falta+"&disc="+Disc+"&dias="+Dias+"&fecha="+Fecha+"&observ="+Observ,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			sendNotification(rslt);
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}

function saveup_disciplinaria(){
	Id = $("#apxemp").val();
	TpDisc = $("#lsTpDisc").val();
	Observ = $("#txtObserv").val();	
	DiasSuspension = $("#diasSusp").val();
	FechaInicio = $("#fecha_inicio").val();
	TpSancion = $("#lsTpSancion").val();
	if(TpSancion==3){
		if(DiasSuspension<=0){
			alert("Error: Debe seleccionar los dias de suspensión");
			return false;
		}
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_disciplinaria&id="+Id+"&observ="+Observ+"&tpDisc="+TpDisc+"&diasSuspension="+DiasSuspension+"&fechaInicio="+FechaInicio+"&tpSancion="+TpSancion,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}

function getBlockSuspension(TpSancion){
	if(TpSancion==1){
		$("#lySuspen").css("display","none");	
	}	
	else if(TpSancion == 2){
		$("#lySuspen").css("display","none");
	}
	else if(TpSancion == 3){
		$("#lySuspen").css("display","block");
	}
}

function getDescFalta(IdF, IdE){
	if(IdF==0){
	 	$("#lyDescripF").css("display","none");
		return false;
	}
	getTpDisciplinaria(IdF, IdE);
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getDescFalta&idF="+IdF,
	success: function(rslt){
		$("#lyDescripF").css("display","block");
		document.getElementById("lyDescripF").innerHTML = rslt;
	}
	});		
}

function getTpDisciplinaria(IdF,IdE){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getTpDisciplinaria&idF="+IdF+"&idE="+IdE,
	success: function(rslt){
		$("#lyTpDisc").css("display","block");
		document.getElementById("lyTpDisc").innerHTML = rslt;
	}
	});		
}

function sv_incapacidades(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Fecha_ini = $("#fecha_inicio").val();
	Fecha_fin = $("#fecha_fin").val();
	Tipo = $("#lstipo").val();
	Incap = $("#lsIncap").val();
	Observ = $("#txtObserv").val();
	Horas = $("#txtHoras").val();
	if(Fecha_ini.length <=0){
		alert("Error: Debe ingresar una fecha inicial de la incapacidad");
		$("#fecha_inicio").focus();
		return false;
		}
	if(Fecha_fin.length<=0){
		alert("Error: Debe ingresar la fecha final de la incapacidad");
		$("#fecha_fin").focus();
		return false;
		}
	if(compare_dates(Fecha_ini,Fecha_fin)){
		alert("Error: La fecha final no puede ser menor a la fecha inicial");
		$("#fecha_inicio").focus();
		return false;
		}
	if(Tipo == 0){
		alert("Error: Debe seleccionar un tipo de falta disciplinaria");
		$("#lstipo").focus();
		return false;
		}
	
	if(Incap == 0){
		alert("Error: Debe seleccionar la entidad que emitió la incapacidad");
		$("#lsIncap").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar un comentario relevante para la incapacidad");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_incapacidades&idAp="+IdAp+"&idE="+IdE+"&fecha_ini="+Fecha_ini+"&fecha_fin="+Fecha_fin+"&tipo="+Tipo+"&incap="+Incap+"&observ="+Observ+"&horas="+Horas,
	success: function(rslt){
			if(rslt>0){
				alert("Acción de personal guardada satisfactoriamente");
				sendNotification(rslt);
				loadApxE(rslt);	
			}
			else{
				alert("Error en la ejecución, presione F5 e intente nuevamente");
				return false;	
			}
	}
	});	
}
function saveup_incapacidad(){
	IdAp = $("#apxemp").val();
	Fecha_ini = $("#fecha_ini").val();
	Fecha_fin = $("#fecha_fin").val();
	Tipo = $("#tipo").val();
	Center = $("#center").val();
	Observ = $("#txtObserv").val();
	Horas = $("#txtHoras").val();
	if(Fecha_ini.length <=0){
		alert("Error: Debe ingresar una fecha inicial de la incapacidad");
		$("#fecha_inicio").focus();
		return false;
		}
	if(Fecha_fin.length<=0){
		alert("Error: Debe ingresar la fecha final de la incapacidad");
		$("#fecha_fin").focus();
		return false;
		}
	if(compare_dates(Fecha_ini,Fecha_fin)){
		alert("Error: La fecha final no puede ser menor a la fecha inicial");
		$("#fecha_inicio").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar un comentario relevante para la incapacidad");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_incapacidad&idAp="+IdAp+"&fecha_ini="+Fecha_ini+"&fecha_fin="+Fecha_fin+"&tipo="+Tipo+"&center="+Center+"&observ="+Observ+"&horas="+Horas,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});	
}

function sv_retiros(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Fec_actual = $("#actual").val();
	Ultimo = $("#fecha_inicio").val();
	Observ = $("#txtObserv").val();
	Plxemp = $("#plxemp").val();
	if(Ultimo.length<=0){
		alert("Error: Debe seleccionar último día de trabajo");
		$("#fecha_inicio").focus();
		return false;
	}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para la acción de personal");
		$("#txtObserv").focus();
		return false;
		}
		
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_retiros&idAp="+IdAp+"&idE="+IdE+"&ultimo="+Ultimo+"&observ="+Observ+"&plxemp="+Plxemp,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			sendNotification(rslt);
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}	
	}
	});	
}

function saveup_retiros(){
	Id = $("#apxemp").val();
	Fec_ini = $("#fecha_inicio").val();
	Observ = $("#txtObserv").val();
	if(Fec_ini.length<=0 ){
		alert("Error: Debe seleccionar el último día de trabajo");
		$("#fecha_inicio").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para la acción de personal");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_retiros&id="+Id+"&fec_ini="+Fec_ini+"&observ="+Observ,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}
function sv_puesto(){
	IdE = $("#idE").val();
	IdAp = $("#idAp").val();
	Plaza = $("#lsPlaza").val();
	Fec_ini = $("#fecha_inicio").val();
	Salario = $("#txtSalario").val();
	Prueba = $("#txtPrueba").val();
	Observ = $("#txtObserv").val();
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDpto").val();
	Posicion = $("#lsPosc").val();
	Supervisor = $("#lsSuperv").val();
	
	if(Depart == 0 || Depart.length <=0){
		alert("Error: Debe seleccionar el departamento del empleado");
		$("#lsDpto").focus();
		return false;
		}
	if(Posicion ==0 || Posicion.length <= 0){
		alert("Error: Debe seleccionar la posición del empleado");
		$("#lsPosc").focus();
		return false; 
		}

	if(Fec_ini.length<=0){
		alert("Error: Debe seleccionar Fecha efectiva");
		$("#fecha_inicio").focus();
		return false;	
	}
	if(Salario.length==0){
		alert("Error: Debe ingresar el nuevo salario");
		$("#txtSalario").focus();
		return false;	
	}
	if(Salario<=0){
		alert("Error: El Salario debe ser mayor a 0");
		$("#txtSalario").focus();
		return false;	
	}
	if(Prueba.length<=0){
		Prueba = 0;	
	}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para la acción de personal");
		$("#txtObserv").focus();
		return false;
		}
	if(Supervisor<=0){
		alert("Error: Debe seleccionar un jefe inmediato");
		$("#lsSuperv").focus();
		return false;	
	}
	if(Plaza == 4){
		alert("Error: El tipo de plaza candidato no puede ser seleccionado en este proceso");
		$("#lsPlaza").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_puesto&idAp="+IdAp+"&idE="+IdE+"&plaza="+Plaza+"&fec_ini="+Fec_ini+"&salario="+Salario+"&prueba="+Prueba+"&observ="+Observ+"&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&supervisor="+Supervisor,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			sendNotification(rslt);
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});	
	
}

function saveup_puesto(){
	Id = $("#apxemp").val();
	Plaza = $("#lsTipoPlaza").val();
	Fec_ini = $("#fecha_inicio").val();
	Salario = $("#txtSalario").val();
	Prueba = $("#txtPrueba").val();
	Observ = $("#txtObserv").val();
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDpto").val();
	Posicion = $("#lsPosc").val();
	Supervisor = $("#lsSuperv").val();


	if(Depart == 0 || Depart.length <= 0){
		alert("Error: Debe seleccionar el departamento del empleado");
		$("#lsDpto").focus();
		return false;
		}
	if(Posicion ==0 || Posicion.length <= 0){
		alert("Error: Debe seleccionar la posición del empleado");
		$("#lsPosc").focus();
		return false; 
		}
	
	if(Fec_ini.length<=0){
		alert("Error: Debe seleccionar Fecha efectiva");
		$("#fecha_inicio").focus();
		return false;	
	}
	if(Salario.length==0){
		alert("Error: Debe ingresar el nuevo salario");
		$("#txtSalario").focus();
		return false;	
	}
	if(Salario<=0){
		alert("Error: El Salario debe ser mayor a 0");
		$("#txtSalario").focus();
		return false;	
	}
	if(Prueba.length<=0){
		Prueba = 0;	
	}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para la acción de personal");
		$("#txtObserv").focus();
		return false;
		}
    if(Plaza == 4){
		alert("Error: El tipo de plaza candidato no puede ser seleccionado en este proceso");
        $("#lsTipoPlaza").focus();
		return false;
	}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_puesto&id="+Id+"&plaza="+Plaza+"&fec_ini="+Fec_ini+"&salario="+Salario+"&prueba="+Prueba+"&observ="+Observ+"&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&supervisor="+Supervisor,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}	
	}
	});	
}

function loadApxE(Id){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=loadApxE&id="+Id+"&accion=1"+"&autorizar=0",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;	
	}
	});	
}
function saveup_permiso(){
	Id = $("#apxemp").val();
	Fec_ini = $("#fecha_ini").val();
	Fec_fin = $("#fecha_fin").val();
	Observ = $("#txtObserv").val();
	Horas = $("#txtHoras").val();
	if(Fec_ini.length <= 0){
		alert("Error: La fecha inicial no puede quedar en blanco");
		$("#fechainicio").focus();
		return false;
		}
	if(Fec_fin.length <= 0){
		alert("Error: La fecha final no puede quedar en blanco");
		$("#fechafin").focus();
		return false;
		}
	if(compare_dates(Fec_ini,Fec_fin)){
		alert("Error: La fecha final no puede ser menor a la fecha inicial");
		$("#fechainicio").focus();
		return false;
		}
	if(Horas <= 0){
		alert("Error: Las horas de permiso no pueden ser igual a 0");
		$("#txtHoras").focus();
		return false;
		}
	if(Observ.length<=0){
		alert("Error: Debe ingresar una observación relevante para la acción de personal");
		$("#txtObserv").focus();
		return false;
		}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_permiso&id="+Id+"&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&observ="+Observ+"&horas="+Horas,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});	
}

function reportap(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=reportap",
	success: function(rslt)
			{
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	}
	});		
}
function getDepart2(IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getDepart2&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyDepart").innerHTML = rslt;
	}
	});	
}
function getEmp2(IdD,IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getEmp2&idD="+IdD+"&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyempdep").innerHTML = rslt;
	}
	});		
}

function loadrpt(){
	Cuenta = $("#lsCuenta").val();
	Dpto = $("#lsDpto").val();
	IdAp = $("#lsAp").val();
	IdAg = $("#lsAg").val();
	Fec_ini = $("#fecha_ini").val();
	Fec_fin = $("#fecha_fin").val();
	Emp = $("#txtEmp").val();
	Badge = $("#txtBadge").val();
	Estado = $("#lsEstado").val();
	if(Fec_ini.length>=1){
		if(Fec_fin.length>=1){
			if(compare_dates(Fec_ini,Fec_fin)){
				alert("Error: Ingrese datos correctos para el período de evaluación");
				$("#fecha_ini").focus();
				return false;
			}
		}
	}
	else{
	 Fec_ini="";
	 Fec_fin="";	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=loadrpt&dpto="+Dpto+"&idAp="+IdAp+"&idAg="+IdAg+"&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&cuenta="+Cuenta+"&emp="+Emp+"&badge="+Badge+"&estado="+Estado,
	success: function(rslt)
			{
			document.getElementById("datosrpt").innerHTML = rslt;
		}
	});	
}
function getDepartFiltros(IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getDepartFiltros&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyDepart").innerHTML = rslt;
	}
	});
}
function getDepartFiltros2(IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getDepartFiltros2&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyDepart").innerHTML = rslt;
	}
	});
}
function getPoscFiltros(IdD){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getPoscFiltros&idD="+IdD,
	success: function(rslt){
		document.getElementById("lyPlaza").innerHTML = rslt;
	}
	});	
}
function getPoscFiltros2(IdD, IdC){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=getPoscFiltros2&idD="+IdD+"&idC="+IdC,
	success: function(rslt){
		document.getElementById("lyPlaza").innerHTML = rslt;
	}
	});	
}

function autorizarap(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=autorizarap",
	success: function(rslt){
		if(rslt==-1){
			alert("Su sesión ha expirado, por favor identifiquese nuevamente");	
			document.location.href = "../../ExpressTel/index.php";
		}
		else{
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
		}
	});		
}
function loadAutor(Id){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=loadApxE&id="+Id+"&accion=1"+"&autorizar=1",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;	
	}
	});			
}
function ResultAutorAp(Id, Accion){
	if(Accion==0){Accion='S';}
	if(Accion==1){Accion='N';
		$("#lyComentRechazo").css("display","block");	
	}
	else{
		$.ajax({
		type: "POST",
		url: "ajax/ajx_rrhh.php",
		data: "Do=ResultAutorAp&id="+Id+"&accion="+Accion,
		success: function(rslt)
			{
			alert("Autorización registrada satisfactoriamente");
			//loadApxE(rslt);
			autorizarap();
			}
		});
	}
}
function sv_AutorizacionRechazo(){
	Id = $("#apxemp").val();
	Comment = $("#txtComentRechazo").val();
	if(Comment.length<=0){
		alert("Error: Debe ingresar las observaciones de rechazo de la acción de personal");
		$("txtComentRechazo").focus();
		return false;
	}
	$.ajax({
		type: "POST",
		url: "ajax/ajx_rrhh.php",
		data: "Do=ResultRechazAp&id="+Id+"&comment="+Comment,
		success: function(rslt)
		{
			if(rslt>0){
				alert("Registro satisfactorio");
				loadApRechazada(rslt,'autorizarap');
			}
			else {
				alert("Error en la ejecución, Intente nuevamente");
				return false;	
			}
		}
	});	
}
function loadApRechazada(Id,Origen){
	if(Origen == 'autorizarap'){
		Origen = 'autorizarap()';
	}
	else{
		Origen = 'reporteApRechazada()';
	}

	$.ajax({
		type: "POST",
		url: "ajax/ajx_rrhh.php",
		data: "Do=loadApRechazada&id="+Id+"&Origen="+Origen,
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
	});	
}
function reporteApRechazada(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_rrhh.php",
		data: "Do=reporteApRechazada",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
	});		
}

function mis_ap(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=mis_ap",
	success: function(rslt)
			{
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;	
	}
	});
}
function cambiosWork(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=cambiosWork",
	success: function(rslt)
			{
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;	
	}
	});	
}
function EditSuperv(IdE){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=EditSuperv&idE="+IdE,
	success: function(rslt)
			{
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;	
	}
	});		
}
function sv_EditSup(){
	IdE = $("#idE").val();
	Cuenta = $("#lsCuenta").val();
	Dpto = $("#lsDpto").val();
	Plaza = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	IdPxe = $("#idPxe").val();
	
	if(Dpto == 0){
		alert("Error: Debe seleccionar el departamento al cual ha sido trasladado");
		$("#lsDpto").focus();
		return false;
		}
	if(Plaza ==0){
		alert("Error: Debe seleccionar la posición del empleado");
		$("#lsPosc").focus();
		}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_EditSup&idE="+IdE+"&cuenta="+Cuenta+"&dpto="+Dpto+"&plaza="+Plaza+"&superv="+Superv+"&idPxe="+IdPxe,
	success: function(rslt){
			alert("Traslado realizado satisfactoriamente");
			loadEditSup(rslt);
			}
	});	
}
function loadEditSup(idE){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=loadEditSup&idE="+IdE,
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
			}
	});		
}
function deleteAp(idAp){
	if(confirm("Está seguro de querer eliminar este registro?")){
    $.ajax({
      type: "POST",
      url: "ajax/ajx_rrhh.php",
      data: "Do=deleteAp&id="+idAp,
      success: function(rslt){       
        if(rslt=='2'){
          alert("Se ha borrado el registro satisfactoriamente!");
		  reporteApRechazada();
        }
		else if(rslt=='1'){
			alert("Por el momento esta acción no puede ser llevada a cabo");
			return false;
		}
		else{
			alert("Error en la ejecución, Intente nuevamente");	
		}
      }
    });
  }
}
function deleteApReport(idAp){
	if(confirm("Está seguro de querer eliminar este registro?")){
    $.ajax({
      type: "POST",
      url: "ajax/ajx_rrhh.php",
      data: "Do=deleteAp&id="+idAp,
      success: function(rslt){       
        if(rslt=='2'){
          alert("Se ha borrado el registro satisfactoriamente!");
		  loadrpt();
        }
		else if(rslt=='1'){
			alert("Por el momento esta acción no puede ser llevada a cabo");
			return false;
		}
		else{
			alert("Error en la ejecución, Intente nuevamente");	
		}
      }
    });
  }
}

function getUltimasSanciones(IdTpSanc, IdE){
	if(IdTpSanc>0){
		$.ajax({
     	type: "POST",
      	url: "ajax/ajx_rrhh.php",
      	data: "Do=getUltimasSanciones&idTpSanc="+IdTpSanc+"&idE="+IdE,
     	success: function(rslt){       
        	document.getElementById("lyUltimasFaltas").innerHTML = rslt;	
     	 	}
   	 	});
	 	$.ajax({
     	type: "POST",
      	url: "ajax/ajx_rrhh.php",
      	data: "Do=getNombreSanciones&idTpSanc="+IdTpSanc,
     	success: function(rslt){       
        	document.getElementById("lyNombreSancion").innerHTML = rslt;	
     	 	}
   	 	});
	}
	else{
		$("#lyUltimasFaltas").css("display","none");	
	}
}
function sv_contratoAviso(){
	IdE = $("#idE").val();
	IdAp = $("#idAp").val();
	TpDisc = $("#lsTpDisc").val();
	$.ajax({
     	type: "POST",
    	url: "ajax/ajx_rrhh.php",
      	data: "Do=sv_contratoAviso&idE="+IdE+"&idAp="+IdAp+"&tpDisc="+TpDisc,
     	success: function(rslt){   
			if(rslt>0){
		    	alert("Contrato de aviso guardado satisfactoriamente");
	        	loadApxE(rslt);	
			}
			else{
				alert("Error en la ejecución, presione F5 e intente nuevamente");	
				return false;
			}
     	 }
   	 });	
}
function sv_upContratoAviso(){
	IdAp = $("#apxemp").val();
	Fecha = $("#fecha_inicio").val();
	TpDisc = $("#lsTpDisc").val();
	$.ajax({
     	type: "POST",
    	url: "ajax/ajx_rrhh.php",
      	data: "Do=sv_upContratoAviso&idAp="+IdAp+"&fecha="+Fecha+"&tpDisc="+TpDisc,
     	success: function(rslt){   
			if(rslt>0){
		    	alert("Contrato de aviso actualizado satisfactoriamente");
	        	loadApxE(rslt);	
			}
			else{
				alert("Error en la ejecución, presione F5 e intente nuevamente");	
				return false;
			}
     	 }
   	 });	
}

function sv_apgenerica(){
	IdAp = $("#idAp").val();
	IdE = $("#idE").val();
	Fec_ini = $("#fecha_inicio").val();
	Fec_fin = $("#fecha_fin").val();
	Observ = $("#txtObserv").val();
	Horas = $("#txtHoras").val();
	
	if(Observ.length<=0){
		alert("Error: Debe ingresar un comentario");
		$("#txtObserv").focus();
		return false;
		}

	if(Fec_ini  === undefined || Fec_ini === null){
		Fec_ini = '';
	}
	if(Fec_fin  === undefined || Fec_fin === null){
		Fec_fin = '';
	}
	if(Horas === undefined || Horas === null){
		Horas = '';
	}
	

	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sv_apgenerica&idAp="+IdAp+"&idE="+IdE+"&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&observ="+Observ+"&horas="+Horas,
	success: function(rslt)
			{
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			sendNotification(rslt);
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});
}

function saveup_generico(){
	Id = $("#apxemp").val();
	Fec_ini = $("#fecha_ini").val();
	Fec_fin = $("#fecha_fin").val();
	Observ = $("#txtObserv").val();
	Horas = $("#txtHoras").val();

	if(Observ.length<=0){
		alert("Error: Debe ingresar un comentario");
		$("#txtObserv").focus();
		return false;
	}

	if(Fec_ini  === undefined || Fec_ini === null){
		Fec_ini = '';
	}
	if(Fec_fin  === undefined || Fec_fin === null){
		Fec_fin = '';
	}
	if(Horas === undefined || Horas === null){
		Horas = '';
	}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=saveup_generico&id="+Id+"&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&observ="+Observ+"&horas="+Horas,
	success: function(rslt){
		if(rslt>0){
			alert("Acción de personal guardada satisfactoriamente");
			loadApxE(rslt);	
		}
		else{
			alert("Error en la ejecución, presione F5 e intente nuevamente");
			return false;	
		}
	}
	});	
}

function sendNotification(idAP){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_rrhh.php",
	data: "Do=sendNotification&idAP="+idAP,
	success: function(rslt){
		if(rslt=='ERROR'){
			alert(rslt);
		}
	}
	});
}
