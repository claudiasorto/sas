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

function hoursCompletionReport(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_hourScorecard.php",
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
	url: "ajax/ajx_hourScorecard.php",
	data: "Do=loadRepScorecard&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&jefe="+Jefe+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge+"&status="+Status,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
		}
	});		
}