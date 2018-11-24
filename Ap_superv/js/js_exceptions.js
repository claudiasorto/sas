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
function newException(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=newException",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	 }
	});		
}
function newExceptionAllEmp(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=newExceptionAllEmp",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	 }
	});
}

function saveException(){
	Empleado = $("#lsEmp").val()
	Fecha = $("#fecha").val();
	HoraInicial = $("#lsHoraIni").val();
	MinutoInicial = $("#lsMinutosIni").val();
	HoraFinal = $("#lsHoraFin").val();
	MinutoFinal = $("#lsMinutosFin").val();
	Razon = $("#lsRazon").val();
	Comment = $("#txtComment").val();
	Ticket = $("#txtTicket").val();
	
	if(Empleado.length<=0){
		alert("Error: You must select a employee");
		$("#lsEmp").focus();
		return false;	
	}
	if(Fecha.length <=0){
		alert("Error: You must select a date");
		$("#fecha").focus();	
		return false;
	}
	<!--if(HoraInicial <=0){
		<!--alert("Error: You must enter the start time");
		<!--$("#lsHoraIni").focus();	
		<!--return false;
	<!--}
	if(HoraFinal <=0){
		alert("Error: You must enter the end time");
		$("#lsHoraFin").focus();	
		return false;
	}
	if(Razon <=0){
		alert("Error: You must select a reason");	
		$("#lsRazon").focus();
		return false;
	}
	if(Razon >=1 && Razon <=4){
		if(Ticket.length<=0){
			alert("Error: must enter the ticket number of the exception");
			$("#txtTicket").focus();
			return false;
		}
	}
	if(Comment.length <=0){
		alert("Error: You must enter a comment");
		$("#txtComment").focus();	
		return false;
	}
	if(HoraInicial>HoraFinal){
		alert("Error: Start time can not be greater than the end time");	
		$("#lsHoraIni").focus();
		return false;
	}
	
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=saveException&empleado="+Empleado+"&fecha="+Fecha+"&horaInicial="+HoraInicial+"&minutoInicial="+MinutoInicial+"&horaFinal="+HoraFinal+"&minutoFinal="+MinutoFinal+"&razon="+Razon+"&comment="+Comment+"&ticket="+Ticket,
	success: function(rslt){
		if(rslt>0){
			alert("Exception successfully saved");
			loadException(rslt);
		}
		else{
				alert("Error: Execution problem, try again");
				return false;
			}
	 }
	});
}
function loadException(IdE){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=loadException&idE="+IdE+"&opcion=1",
	success: function(rslt){
		document.getElementById("content").innerHTML = rslt;
	 }
	});
}

function loadExceptionReport(IdE){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=loadExceptionReport&idE="+IdE+"&opcion=1",
	success: function(rslt){
		document.getElementById("lyReport").innerHTML = rslt;
	 }
	});
}

function updateException(){
	IdEx = $("#idException").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=loadException&idE="+IdEx+"&opcion=2",
	success: function(rslt){
		if(rslt==-1){
			alert("Operation not permitted");
			return false;
		}
		else{
			document.getElementById("content").innerHTML = rslt;
		}
	 }
	});	
}
function updateExceptionReport(){
	IdEx = $("#idException").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=loadExceptionReport&idE="+IdEx+"&opcion=2",
	success: function(rslt){
		if(rslt==-1){
			alert("Operation not permitted");
			return false;
		}
		else{
			document.getElementById("lyReport").innerHTML = rslt;
		}
	 }
	});	
}

function saveUpdateException(){
	IdEx = 	$("#idException").val();
	Fecha = $("#fecha").val();
	HoraIni = $("#lsHoraIni").val();
	MinutosIni = $("#lsMinutosIni").val();
	HoraFin = $("#lsHoraFin").val();
	MinutosFin = $("#lsMinutosFin").val();
	Razon = $("#lsReason").val();
	Comment = $("#txtComment").val();

	if(Fecha.length <=0){
		alert("Error: You must select a date");
		$("#fecha").focus();	
		return false;
	}
	<!--if(HoraIni <=0){
		<!--alert("Error: You must enter the start time");
		<!--$("#lsHoraIni").focus();	
		<!--return false;
	<!--}
	if(HoraFin <=0){
		alert("Error: You must enter the end time");
		$("#lsHoraFin").focus();	
		return false;
	}
	if(Razon <=0){
		alert("Error: You must select a reason");	
		$("#lsReason").focus();
		return false;
	}
	if(Comment.length <=0){
		alert("Error: You must enter a comment");
		$("#txtComment").focus();	
		return false;
	}
	if(HoraIni>HoraFin){
		alert("Error: Start time can not be greater than the end time");	
		$("#lsHoraIni").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=saveUpdateException&idEx="+IdEx+"&fecha="+Fecha+"&horaIni="+HoraIni+"&minutosIni="+MinutosIni+"&horaFin="+HoraFin+"&minutosFin="+MinutosFin+"&razon="+Razon+"&comment="+Comment,
	success: function(rslt){
		if(rslt>0){
			alert("Exception successfully saved");
			loadException(rslt);
		}
		else{
				alert("Error: Execution problem, try again");
				return false;
		}
	 }
	});	
}

function saveUpdateExceptionReport(){
	IdEx = 	$("#idException").val();
	Fecha = $("#fecha").val();
	HoraIni = $("#lsHoraIni").val();
	MinutosIni = $("#lsMinutosIni").val();
	HoraFin = $("#lsHoraFin").val();
	MinutosFin = $("#lsMinutosFin").val();
	Razon = $("#lsReason").val();
	Comment = $("#txtComment").val();

	if(Fecha.length <=0){
		alert("Error: You must select a date");
		$("#fecha").focus();	
		return false;
	}

	if(HoraFin <=0){
		alert("Error: You must enter the end time");
		$("#lsHoraFin").focus();	
		return false;
	}
	if(Razon <=0){
		alert("Error: You must select a reason");	
		$("#lsReason").focus();
		return false;
	}
	if(Comment.length <=0){
		alert("Error: You must enter a comment");
		$("#txtComment").focus();	
		return false;
	}
	if(HoraIni>HoraFin){
		alert("Error: Start time can not be greater than the end time");	
		$("#lsHoraIni").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=saveUpdateException&idEx="+IdEx+"&fecha="+Fecha+"&horaIni="+HoraIni+"&minutosIni="+MinutosIni+"&horaFin="+HoraFin+"&minutosFin="+MinutosFin+"&razon="+Razon+"&comment="+Comment,
	success: function(rslt){
		if(rslt>0){
			alert("Exception successfully saved");
			loadRptTotalException();
		}
		else{
				alert("Error: Execution problem, try again");
				return false;
		}
	 }
	});	
}

function rptException(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=rptException",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	 }
	});		
}
function loadRptException(){
	FecIni = $("#fechaIni").val();
	FecFin = $("#fechaFin").val();
	TpException = $("#lsException").val();
	OptEmp = $("#lsEmp").val();
	NombreEmp = $("#txtEmp").val();
	Badge = $("#txtBadge").val();
	TpReport = $("#lsTpReport").val();
	if(FecIni.length>=1){
		if(FecFin.length>=1){
			if(compare_dates(FecIni,FecFin)){
				alert("Error: Enter correct data for the evaluation period");
				$("#fechaIni").focus();
				return false;
			}
		}
		else{
		alert("Error: You must select the end date");
		$("#fechaFin").focus();
		return false;
		}
	}
	if(FecFin.length>=1){
		if(FecIni.length<=0){
			alert("Error: You must select the start date");
			$("#fechaIni").focus();
			return false;
			}
	}
	else{
		alert("Error: Must select the evaluation period");
		$("#fechaIni").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=loadRptException&fechaIni="+FecIni+"&fechaFin="+FecFin+"&tpException="+TpException+"&optEmp="+OptEmp+"&nombreEmp="+NombreEmp+"&badge="+Badge+"&tpReport="+TpReport,
	success: function(rslt){
		document.getElementById("data").innerHTML = rslt;
	 }
	});
}
function rptTotalException(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=rptTotalException",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	 }
	});		
}
function loadRptTotalException(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDpto").val();
	Posc = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	FecIni = $("#fechaIni").val();
	FecFin = $("#fechaFin").val();
	Empleado = $("#lsEmp").val();
	Badge = $("#txtBadge").val();
	Nombre = $("#txtEmp").val();
	Status = $("#lsStatus").val();
	TpException = $("#lsException").val();
	TpReport = $("#lsTpReport").val();
	if(FecIni.length>=1){
		if(FecFin.length>=1){
			if(compare_dates(FecIni,FecFin)){
				alert("Error: Enter correct data for the evaluation period");
				$("#fechaIni").focus();
				return false;
			}
		}
		else{
		alert("Error: You must select the end date");
		$("#fec_fin").focus();
		return false;
		}
	}
	if(FecFin.length>=1){
		if(FecIni.length<=0){
			alert("Error: You must select the start date");
			$("#fechaIni").focus();
			return false;
			}
	}
	else{
		alert("Error: Must select the evaluation period");
		$("#fec_ini").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=loadRptTotalException&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posc+"&superv="+Superv+"&fechaIni="+FecIni+"&fechaFin="+FecFin+"&empleado="+Empleado+"&badge="+Badge+"&nombre="+Nombre+"&tpException="+TpException+"&tpReport="+TpReport+"&status="+Status,
	success: function(rslt){
		document.getElementById("data").innerHTML = rslt;
	 }
	});			
}
function autorizarExcepciones(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_exceptions.php",
	data: "Do=loadRptTotalException&tpReport=1&type=pendientes",
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
function deleteExceptionEmp(IdE){
	if(confirm("Are you sure you want to delete this exception?")){
		$.ajax({
    	type: "POST",
      	url: "ajax/ajx_exceptions.php",
      	data: "Do=deleteException&idE="+IdE,
      	success: function(rslt){       
        	if(rslt=='2'){
          		alert("The record is deleted successfully!");
		 		loadRptException();
       		 }
			else if(rslt=='1'){
				alert("By the time this action can not be performed");
				return false;
			}
			else{
				alert("Error: Execution problem, try again");	
			}
     	}
   	 });	
	}
}

function deleteException(IdE){
	FecIni = $("#fechaIni").val();
	if(confirm("Are you sure you want to delete this exception?")){
		$.ajax({
    	type: "POST",
      	url: "ajax/ajx_exceptions.php",
      	data: "Do=deleteException&idE="+IdE,
      	success: function(rslt){       
        	if(rslt=='2'){
          		alert("The record is deleted successfully!");
          		try{
			 		if(FecIni.length>=1){
	          			loadRptTotalException();
	          		}
	          	}
	          	catch(err){
          			autorizarExcepciones();
          		}
       		 }
			else if(rslt=='1'){
				alert("By the time this action can not be performed");
				return false;
			}
			else{
				alert("Error: Execution problem, try again");	
			}
     	}
   	 });	
	}
}
function aprovException(IdE){
	FecIni = $("#fechaIni").val();

	if(confirm("Are you sure want to approve this exception")){
		$.ajax({
    	type: "POST",
      	url: "ajax/ajx_exceptions.php",
      	data: "Do=aprovException&idE="+IdE,
      	success: function(rslt){
        	if(rslt=='2'){
          		alert("The record is approved successfully!");
		 		try{
			 		if(FecIni.length>=1){
	          			loadRptTotalException();
	          		}
	          	}
	          	catch(err){
          			autorizarExcepciones();
          		}
		 		
       		 }
			else if(rslt=='1'){
				alert("By the time this action can not be performed");
				return false;
			}
			else{
				alert("Error: Execution problem, try again");	
			}
     	}
   	 });
	}	
}
function rejectException(IdE){
	FecIni = $("#fechaIni").val();
	if(confirm("Are you sure want to reject this exception")){
		$.ajax({
    	type: "POST",
      	url: "ajax/ajx_exceptions.php",
      	data: "Do=rejectException&idE="+IdE,
      	success: function(rslt){
        	if(rslt=='2'){
          		alert("The record is rejected successfully!");
          		try{
			 		if(FecIni.length>=1){
	          			loadRptTotalException();
	          		}
	          	}
	          	catch(err){
          			autorizarExcepciones();
          		}
       		 }
			else if(rslt=='1'){
				alert("By the time this action can not be performed");
				return false;
			}
			else{
				alert("Error: Execution problem, try again");	
			}
     	}
   	 });
	}	
}
