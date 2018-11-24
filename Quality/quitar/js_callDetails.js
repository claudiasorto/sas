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
function formUpCallDetails(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_callDetails.php",
	data: "Do=formUpCallDetails",
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
function RepotCall(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_callDetails.php",
	data: "Do=RepotCall",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});	
}
function loadReportCall(){
	Cuenta = $("#lsCuenta").val();
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Ubicacion = $("#lsUbicacion").val();
	if(FechaIni.length<=0){
		alert("Error: must select a period of evaluation");
		$("#fecha_ini").focus();
		return false;	
	}
	if(FechaFin.length<=0){
		alert("Error: must select a period of evaluation");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: The start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_callDetails.php",
	data: "Do=loadReportCall&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&cuenta="+Cuenta+"&ubicacion="+Ubicacion,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
		}
	});		
}
function formDeleteCall(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_callDetails.php",
	data: "Do=formDeleteCall",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});	
}

function deleteCall(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_callDetails.php",
	data: "Do=RepotCall",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});		
}
function deleteCalls(){
	FecIni = $("#fecha_ini").val();
	FecFin = $("#fecha_fin").val();
	if(FecIni.length<=0){
		alert("You must select a period");
		$("#fecha_ini").focus();	
		return false;
	}	
	if(FecFin.length<=0){
		alert("You must select a period");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FecIni,FecFin)){
		alert("The start date can not be greater than the end date of evaluation");
		$("#fecha_ini").focus();
		return false;
	}
	if(confirm("Are you sure to delete these records?")){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_callDetails.php",
		data: "Do=deleteCalls&fecIni="+FecIni+"&fecFin="+FecFin,
		success: function(rslt){
				if(rslt==2){
					alert("Action completed successfully");
					return false;
				}
				else{
					alert("Execution problem, try again");	
					return false;
				}
			}
		});
	}
}