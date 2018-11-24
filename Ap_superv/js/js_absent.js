<!--Funciones de Ausentismos -->
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
function newAbsent(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=newAbsent",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
			}
	});		
}
function newAbsentAllDays(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=newAbsentAllDays",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
			}
	});	
}
function loadFormAbsentDay(){
	Fecha = $("#fecha").val();
	Sup = $("#lsSup").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=loadFormAbsentDay&fecha="+Fecha+"&sup="+Sup,
	success: function(rslt){
			$("#lyComment").css("display","none");
			document.getElementById("lyData").innerHTML = rslt;
			}
	});	
}


function absent(IdE, Estado){
	Fecha = $("#fecha").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=absent&idE="+IdE+"&estado="+Estado+"&fecha="+Fecha,
	success: function(rslt){
			if(rslt==0){
				alert("Status changed successfully");
				loadFormAbsentDay();	
			}
			else{
				$("#lyComment").css("display","block");
				document.getElementById("lyComment").innerHTML = rslt;
			}
	 }
	});	
}
function cancelAbsent(){
	$("#lyComment").css("display","none");	
}

function saveAbsent(){
	IdE = $("#txtEmp").val();
	Estado = $("#lsTpAb").val();
	Observ = $("#txtObserv").val();
	Fecha = $("#fecha").val();
	if(Estado!='P'){
		if(Observ.length <=0){
			alert("Error: You must enter observations");
			return false;
		}
	}

	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=saveAbsent&idE="+IdE+"&estado="+Estado+"&observ="+Observ+"&fecha="+Fecha,
	success: function(rslt){
			if(rslt==2){
				alert("Saved successfully");
				$("#lyComment").css("display","none");
				loadFormAbsentDay();
			}
			else{
				alert("Execution problem, Try again");
				return false;
			}
	 }
	});	
}

function repAbsent(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=repAbsent",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	 }
	});	
}

function loadReportAbsent(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Accion = $("#idAccion").val();
	IdEmp = $("#lsEmp").val();
	Cuenta = $("#lsCuenta").val();
	Jefe = $("#lsSup").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	Status = document.getElementById('sel2');
	if(FechaIni.length>=1){
		if(FechaFin.length>=1){
			if(compare_dates(FechaIni,FechaFin)){
				alert("Error: Enter correct data for the evaluation period");
				$("#fecha_ini").focus();
				return false;
			}
		}
	}
	else{
	 	alert("You must select a period");
		return false;	
	}
	var ArrayStatus = "";
	if(Status.options[0].value == "-"){
		alert("You must select at least one status");
		$("#sel1").focus();
		return false;
	}
	if(Status.length<=0){
		alert("You must select at least one status");	
		$("#sel1").focus();
		return false;
	}
	for(i=0; i<Status.length; i++){
		if(i>0){
			ArrayStatus +=" "+Status.options[i].value;
		}
		else{
			ArrayStatus +=""+Status.options[i].value;	
		}
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=loadReportAbsent&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&idEmp="+IdEmp+"&accion="+Accion+"&cuenta="+Cuenta+"&jefe="+Jefe+"&arrayStatus="+ArrayStatus+"&nombre="+Nombre+"&badge="+Badge,
	success: function(rslt){
			document.getElementById("lyRpt").innerHTML = rslt;
	 }
	});		
}

function pasar() {
	obj=document.getElementById('sel1');
	if (obj.selectedIndex==-1) return;
    for (i=0; opt=obj.options[i]; i++)
    if (opt.selected) {
    	valor=opt.value; // almacenar value
    	txt=obj.options[i].text; // almacenar el texto
    	obj.options[i]=null; // borrar el item si está seleccionado
    	obj2=document.getElementById('sel2');
      if (obj2.options[0].value=='-') // si solo está la opción inicial borrarla
        obj2.options[0]=null;
    	opc = new Option(txt,valor);
    	eval(obj2.options[obj2.options.length]=opc);
  }	
}

function loadRptAbsComplete(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_absent.php",
	data: "Do=loadRptAbsComplete",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	 }
	});		
}
function upDayOffAbsent(){
	$.ajax({
    	type: "POST",
      	url: "ajax/ajx_absent.php",
      	data: "Do=upDayOffAbsent",
      	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
   	 });	
}
function upFile(){
	fechaInicial = $("#fechaIni").val();
	fechaFinal = $("#fechaFin").val();
	if(compare_dates(fechaInicial,fechaFinal)){
		alert("Error: Enter correct data for the evaluation period (monday to saturday)");
		$("#fechaIni").focus();
		return false;	
	}
	if(fechaInicial.length<=0){
		alert("Error: Enter the initial date");
		$("#fechaIni").focus();
		return false;	
	}
	if(fechaFinal.length<=0){
		alert("Error: Enter the end date");
		$("#fechaFin").focus();
		return false;
	}
	document.getElementById('frmDoc').submit();	
}
function newAbsentUnrestricted(){
	$.ajax({
    	type: "POST",
      	url: "ajax/ajx_absent.php",
      	data: "Do=newAbsentUnrestricted",
      	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
   	 });	
}

function consultAbsent(){
	IdE = $("#lsEmp").val();
	Fecha = $("#fecha").val();
	if(IdE<=0){
		alert("Error: You must select a agent");	
		$("#lsEmp").focus();
		return false;
	}
	if(Fecha.length<=0){
		alert("Error: You must select a date");
		$("#fecha").focus();
		return false;	
	}
	
	var fecha_actual = new Date();
	var fec_actual = fecha_actual.getDate()+"/"+(fecha_actual.getMonth()+1)+"/"+fecha_actual.getFullYear();
	if(compare_dates(Fecha,fec_actual)){
		alert("Error: The selected date can not be greater than the current date");
		$("#fecha").focus();
		return false;
	}
	$.ajax({
    	type: "POST",
      	url: "ajax/ajx_absent.php",
      	data: "Do=consultAbsent&idE="+IdE+"&fecha="+Fecha,
      	success: function(rslt){
			document.getElementById("lyForm").innerHTML = rslt;
		}
   	 });
}
function saveUnrestAbsent(){
	IdE = $("#txtIdE").val();
	Fecha = $("#txtFecha").val();
	TpAbs = $("#lsTpAb").val();
	Comment = $("#txtComment").val();
	if(TpAbs!='O' || TpAbs!='P'){
		if(Comment.length<=0){
			alert("Error: Field observations must be filled");	
			$("#txtComments").focus();
			return false;
		}	
	}
	$.ajax({
    	type: "POST",
      	url: "ajax/ajx_absent.php",
      	data: "Do=saveUnrestAbsent&idE="+IdE+"&fecha="+Fecha+"&tpAbs="+TpAbs+"&comment="+Comment,
      	success: function(rslt){
			if(rslt==2){
				alert("Absenteeism successfully saved");
				$("#lyForm").css("display","block");
				newAbsentUnrestricted();
			}
			else{
				alert("Execution problem, Try again");
				return false;	
			}
		}
   	 });	
}
function changeStatus(IdE){
	Fecha = $("#fecha").val();
	$.ajax({
    	type: "POST",
      	url: "ajax/ajx_absent.php",
      	data: "Do=changeStatus&idE="+IdE+"&fecha="+Fecha,
      	success: function(rslt){
			$("#lyComment").css("display","block");
			document.getElementById("lyComment").innerHTML = rslt;
		}
   	 });
}