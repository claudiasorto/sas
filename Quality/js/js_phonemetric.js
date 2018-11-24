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
function uploadPhoneMetrics(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=uploadPhoneMetrics",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});		
}
function upFile(){
	document.getElementById('frmDoc').submit();	
}
function latenessReport(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=latenessReport",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});	
}

function loadLateness(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPos").val();
	Jefe = $("#lsJefe").val();
	FechaIni = $("#fechaIni").val();
	FechaFin = $("#fechaFin").val();
	Emp = $("#lsEmp").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	if(FechaIni.length<=0){
		alert("Must select an evaluation period");	
		$("#fechaIni").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Must select an evaluation period");	
		$("#fechaFin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Start date can not be greater than the end date");
		$("#fechaIni").focus();
		return false;
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=loadLateness&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&jefe="+Jefe+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
}

function detalleLateness(IdE){
	FechaIni = $("#fechaIni").val();
	FechaFin = $("#fechaFin").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=detalleLateness&idE="+IdE+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin,
	success: function(rslt){
		document.getElementById("lyDetalles").innerHTML = rslt;
		}
	});	
}
function filtrosReportAHT(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=filtrosReportAHT",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});	
}
function loadAveragesCall(){
	Cuenta = $("#lsCuenta").val();
	Sup = $("#lsSup").val();
	Emp = $("#lsEmp").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	FecIni = $("#fecha_ini").val();
	FecFin = $("#fecha_fin").val();
	if(compare_dates(FecIni, FecFin)){
		alert("The start date of evaluation can not be greater than the end date");	
		$("#fecha_ini").focus();
		return false;
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=loadAveragesCall&cuenta="+Cuenta+"&sup="+Sup+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge+"&fecIni="+FecIni+"&fecFin="+FecFin,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
}

function filtrosPhoneMetrics(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=filtrosPhoneMetrics",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});		
}
function loadPhoneMetrics(){
	Cuenta = $("#lsCuenta").val();
	Sup = $("#lsSup").val();
	Emp = $("#lsEmp").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	FecIni = $("#fecha_ini").val();
	FecFin = $("#fecha_fin").val();
	if(compare_dates(FecIni, FecFin)){
		alert("The start date of evaluation can not be greater than the end date");	
		$("#fecha_ini").focus();
		return false;
	}	
	if(FecIni.length<=0){
		alert("Must select the start date");
		$("#fecha_ini").focus();
		return false;
	}
	if(FecFin.length<=0){
		alert("must select the end date");	
		$("#fecha_fin").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=loadPhoneMetrics&cuenta="+Cuenta+"&sup="+Sup+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge+"&fecIni="+FecIni+"&fecFin="+FecFin,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
}

function hoursCompletionReport(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=hoursCompletionReport",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});		
}

function loadRepScorecard(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPos").val();
	Jefe = $("#lsJefe").val();
	FechaIni = $("#fechaIni").val();
	FechaFin = $("#fechaFin").val();
	Emp = $("#lsEmp").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	Status = $("#lsStatus").val();
	if(FechaIni.length<=0){
		alert("Must select an evaluation period");	
		$("#fechaIni").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Must select an evaluation period");	
		$("#fechaFin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Start date can not be greater than the end date");
		$("#fechaIni").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_phonemetric.php",
	data: "Do=loadRepScorecard&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&jefe="+Jefe+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge+"&status="+Status,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
		}
	});		
}