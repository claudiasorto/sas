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

function mis_ap(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=mis_ap",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;	
	}
	});
}

function AceptarAp(IdAp){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=AceptarAp&idAp="+IdAp,
	success: function(rslt)
		{
		if(rslt==2){
			alert("Ap accepted by agent");	
			mis_ap()
		}		
		else{
			alert("execution problem, try again");
			return false;	
		}
	}
	});	
}

function payroll(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=payroll",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;	
	}
	});	
}
function loadPayroll(){
	FecIni = $("#fecha_ini").val();
	FecFin = $("#fecha_fin").val();	
	if(FecIni.length>=1){
		if(FecFin.length>=1){
			if(compare_dates(FecIni,FecFin)){
				alert("Error: Enter correct data for the evaluation period");
				$("#fec_ini").focus();
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
			$("#fec_ini").focus();
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
	url: "ajax/ajx_myrecord.php",
	data: "Do=loadPayroll&fecIni="+FecIni+"&fecFin="+FecFin,
	success: function(rslt)
			{
			document.getElementById("lyData").innerHTML = rslt;	
	}
	});
}
function absenteeism(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=absenteeism",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;
	}
	});	
}

function reportAbsent(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	if(FechaIni.length<=0){
		alert("Error: must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: must select an end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: the start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=reportAbsent&fechaIni="+FechaIni+"&fechaFin="+FechaFin,
	success: function(rslt)
		{
			document.getElementById("lyData").innerHTML = rslt;
	}
	});
}

function exception(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=exception",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}
function reportException(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	if(FechaIni.length<=0){
		alert("Error: must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: must select an end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: the start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=reportException&fechaIni="+FechaIni+"&fechaFin="+FechaFin,
	success: function(rslt)
		{
			document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}

function evaluations(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=evaluations",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}

function reportEvaluations(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Maker = $("#lsMaker").val();
	if(FechaIni.length<=0){
		alert("Error: must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: must select an end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: the start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=reportEvaluations&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&maker="+Maker,
	success: function(rslt)
		{
			document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}

function schedules(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=schedules",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;
	}
	});	
}

function reportSchedules(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	if(FechaIni.length<=0){
		alert("Error: must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: must select an end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: the start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=reportSchedules&fechaIni="+FechaIni+"&fechaFin="+FechaFin,
	success: function(rslt)
		{
			document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}
function Metrics(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=Metrics",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}
function loadMetrics(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	if(FechaIni.length<=0){
		alert("Error: must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: must select an end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: the start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=loadMetrics&fechaIni="+FechaIni+"&fechaFin="+FechaFin,
	success: function(rslt)
		{
			document.getElementById("lyData").innerHTML = rslt;
	}
	});	
		
}
function hrRequest(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=hrRequest",
	success: function(rslt)
			{
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			$("#lyForm").css("display","none");
			$("#lyData").css("display","none");
			document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}

function newHrRequest(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=newHrRequest",
	success: function(rslt)
			{
			$("#lyForm").css("display","block");
			document.getElementById("lyForm").innerHTML = rslt;
	}
	});	
}

function saveRequest(){
	TpReq = $("#lsCategoria").val();
	Descrip = $("#txtDescrip").val();
	
	if(TpReq<=0){
		alert("Must select a category");
		$("#lsCategoria").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=saveRequest&tpReq="+TpReq+"&descrip="+Descrip,
	success: function(rslt)
			{
			if(rslt==2){
				alert("Successfully saved request");	
				hrRequest();
			}
	}
	});		
}

function getDetallesRequest(IdR){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=getDetallesRequest&idR="+IdR,
	success: function(rslt)
			{
			$("#lyDetalles").css("display","block");
			document.getElementById("lyDetalles").innerHTML = rslt;
	}
	});		
}

function getRequest(){
	Status = $("#lsStatus").val();
	TpReq = $("#lsTpReq").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=getRequest&status="+Status+"&tpReq="+TpReq,
	success: function(rslt)
		{
			$("#lyData").css("display","block");
			document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}
function editRequest(IdR){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=editRequest&idR="+IdR,
	success: function(rslt)
		{
		$("#lyForm").css("display","block");
			document.getElementById("lyForm").innerHTML = rslt;
	}
	});		
}

function saveEditRequest(){
	IdR = $("#txtIdR").val();
	Categoria = $("#lsCategoria").val();
	Descrip = $("#txtDescrip").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_myrecord.php",
	data: "Do=saveEditRequest&idR="+IdR+"&categoria="+Categoria+"&descrip="+Descrip,
	success: function(rslt)
		{
		if(rslt==2){
			alert("Successfully updated request");
			hrRequest();	
		}
		else{
			alert("Execution problem, try again");	
			return false;
		}
	}
	});
}