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


function searchScorecard(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_agentScorecard.php",
	data: "Do=searchScorecard",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyData").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});
}
function loadSearchScorecard(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPos").val();
	Jefe = $("#lsJefe").val();
	FechaIni = $("#fechaIni").val();
	FechaFin = $("#fechaFin").val();
	Emp = $("#lsEmp").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	Top = $("#lsTop").val();
	if(FechaIni.length<=0){
		alert("Must select a start date");
		$("#fechaIni").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Must select an end date");
		$("#fechaFin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("The start date can not be greater than the end date");	
		$("#fechaIni").focus();
		return false;
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_agentScorecard.php",
	data: "Do=loadSearchScorecard&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&jefe="+Jefe+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge+"&top="+Top,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});
}