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
function newDPR(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=newDPR",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		$("#lyData").css("display","none");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});	
}
function loadFormDpr(){
	Fecha = $("#fecha").val();
	if(Fecha.length<=0){
		alert("Must select a date");	
		$("#fecha").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=loadFormDpr&fecha="+Fecha,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});
	
}

function saveDpr(){
	IdSup = $("#txtIdSup").val();
	Fecha = $("#txtFecha").val();
	Comment = $("#txtComments").val();
	
	commentAHT = document.getElementsByName('txtAht[]');
	var ArrayAHT = "";
	for(i=0; i<commentAHT.length; i++){
		if(i>0){
			ArrayAHT +="***************"+commentAHT[i].value;	
		}	
		else{
			ArrayAHT  +=""+commentAHT[i].value;
		}
	}
	
	commentHours = document.getElementsByName('txtHours[]');
	var ArrayHours = "";
	for(i=0; i<commentHours.length; i++){
		if(i>0){
			ArrayHours +="***************"+commentHours[i].value;	
		}	
		else{
			ArrayHours  +=""+commentHours[i].value;
		}
	}
	
	commentQA = document.getElementsByName('txtQA[]');
	var ArrayQA = "";
	for(i=0; i<commentQA.length; i++){
		if(i>0){
			ArrayQA +="***************"+commentQA[i].value;	
		}	
		else{
			ArrayQA  +=""+commentQA[i].value;
		}
	}
	
	minutesLate = document.getElementsByName('txtMinutosLate[]');
	var ArrayMinutesLate = "";
	for(i=0; i<minutesLate.length; i++){
		if(i>0){
			ArrayMinutesLate +="***************"+minutesLate[i].value;	
		}	
		else{
			ArrayMinutesLate  +=""+minutesLate[i].value;
		}
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=saveDpr&sup="+IdSup+"&fecha="+Fecha+"&comment="+Comment+"&arrayAht="+ArrayAHT+"&arrayHours="+ArrayHours+"&arrayQA="+ArrayQA+"&arrayMinutesLate="+ArrayMinutesLate,
	success: function(rslt){
		if(rslt>0){
			alert("Record saved successfully");
			loadDpr(rslt);
		}
		else{
			alert("Execution problem, try again");
			return false;	
		}
	}
});
		
}

function loadDpr(Id){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=loadDpr&id="+Id,
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});	
}
function DPRSupervisor(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=DPRSupervisor",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		$("#lyData").css("display","none");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});		
}

function loadDprSup(){
	FechaIni = $("#fechaIni").val();
	FechaFin = $("#fechaFin").val();
	Sup = $("#lsSup").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=loadDprSup&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&sup="+Sup,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
}
function loadDrpDate(Id){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=loadDpr&id="+Id,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
}

function weeklyPerformance(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=weeklyPerformance",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		$("#lyData").css("display","none");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});
}

function loadWeeklyPerformance(){
	FechaIni = $("#fechaIni").val();
	FechaFin = $("#fechaFin").val();
	Sup = $("#lsSup").val();
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
	url: "ajax/ajx_dpr.php",
	data: "Do=loadWeeklyPerformance&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&sup="+Sup,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
	
}

function getDetPerformance(IdS){
	FechaIni = $("#fechaIni").val();
	FechaFin = $("#fechaFin").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_dpr.php",
	data: "Do=getDetPerformance&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&idS="+IdS,
	success: function(rslt){
		$("#lydown"+IdS).css("display","none");
		$("#lyup"+IdS).css("display","block");
		$("#lyDet"+IdS).css("display","block");
		$("#lyDet"+IdS).fadeOut(600,function(){
			document.getElementById("lyDet"+IdS).innerHTML = rslt;	
		});
		$("#lyDet"+IdS).fadeIn(600);
		}
	});		
}
function quitDetails(IdS){
	$("#lyup"+IdS).css("display","none");
	$("#lyDet"+IdS).css("display","none");
	$("#lydown"+IdS).css("display","block");
}