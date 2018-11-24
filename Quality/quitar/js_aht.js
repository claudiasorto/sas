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

function formUpAHT(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_aht.php",
	data: "Do=formUpAHT",
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

function filtrosReportAHT(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_aht.php",
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
	url: "ajax/ajx_aht.php",
	data: "Do=loadAveragesCall&cuenta="+Cuenta+"&sup="+Sup+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge+"&fecIni="+FecIni+"&fecFin="+FecFin,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
}