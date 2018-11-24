
var singleSelect = true;  // Allows an item to be selected once only
var sortSelect = true;  // Only effective if above flag set to true
var sortPick = true;  // Will order the picklist in sort sequence

// Initialise - invoked on load
function initIt() {
  var selectList = document.getElementById("sel1");
  var selectOptions = selectList.options;
  var selectIndex = selectList.selectedIndex;
  var pickList = document.getElementById("sel2");
  var pickOptions = pickList.options;
  pickOptions[0] = null;  // Remove initial entry from picklist (was only used to set default width)
  if (!(selectIndex > -1)) {
    selectOptions[0].selected = true;  // Set first selected on load
    selectOptions[0].defaultSelected = true;  // In case of reset/reload
  }
  selectList.focus();  // Set focus on the selectlist
}

// Adds a selected item into the picklist
function addIt() {
  var selectList = document.getElementById("sel1");
  var selectIndex = selectList.selectedIndex;
  var selectOptions = selectList.options;
  var pickList = document.getElementById("sel2");
  var pickOptions = pickList.options;
  var pickOLength = pickOptions.length;
  // An item must be selected
  while (selectIndex > -1) {
    pickOptions[pickOLength] = new Option(selectList[selectIndex].text);
    pickOptions[pickOLength].value = selectList[selectIndex].value;
    // If single selection, remove the item from the select list
    if (singleSelect) {
      selectOptions[selectIndex] = null;
    }
    if (sortPick) {
      var tempText;
      var tempValue;
      // Sort the pick list
      while (pickOLength > 0 && pickOptions[pickOLength].value < pickOptions[pickOLength-1].value) {
        tempText = pickOptions[pickOLength-1].text;
        tempValue = pickOptions[pickOLength-1].value;
        pickOptions[pickOLength-1].text = pickOptions[pickOLength].text;
        pickOptions[pickOLength-1].value = pickOptions[pickOLength].value;
        pickOptions[pickOLength].text = tempText;
        pickOptions[pickOLength].value = tempValue;
        pickOLength = pickOLength - 1;
      }
    }
    selectIndex = selectList.selectedIndex;
    pickOLength = pickOptions.length;
  }
  selectOptions[0].selected = true;
}

// Deletes an item from the picklist
function delIt() {
  var selectList = document.getElementById("sel1");
  var selectOptions = selectList.options;
  var selectOLength = selectOptions.length;
  var pickList = document.getElementById("sel2");
  var pickIndex = pickList.selectedIndex;
  var pickOptions = pickList.options;
  while (pickIndex > -1) {
    // If single selection, replace the item in the select list
    if (singleSelect) {
      selectOptions[selectOLength] = new Option(pickList[pickIndex].text);
      selectOptions[selectOLength].value = pickList[pickIndex].value;
    }
    pickOptions[pickIndex] = null;
    if (singleSelect && sortSelect) {
      var tempText;
      var tempValue;
      // Re-sort the select list
      while (selectOLength > 0 && selectOptions[selectOLength].value < selectOptions[selectOLength-1].value) {
        tempText = selectOptions[selectOLength-1].text;
        tempValue = selectOptions[selectOLength-1].value;
        selectOptions[selectOLength-1].text = selectOptions[selectOLength].text;
        selectOptions[selectOLength-1].value = selectOptions[selectOLength].value;
        selectOptions[selectOLength].text = tempText;
        selectOptions[selectOLength].value = tempValue;
        selectOLength = selectOLength - 1;
      }
    }
    pickIndex = pickList.selectedIndex;
    selectOLength = selectOptions.length;
  }
}

// Selection - invoked on submit
function selIt(btn) {
  var pickList = document.getElementById("sel2");
  var pickOptions = pickList.options;
  var pickOLength = pickOptions.length;
  if (pickOLength < 1) {
    alert("No Selections in the Picklist\nPlease Select using the [->] button");
    return false;
  }
  for (var i = 0; i < pickOLength; i++) {
    pickOptions[i].selected = true;
  }
  return true;
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


function newHorario(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=newHorario",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
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
function saveHorario(){
	Agentes = document.getElementById('sel2');
	EntradaHora = $("#lsEntradaHora").val();
	EntradaMinutos = $("#lsEntradaMinutos").val();
	Break1EntradaHora = $("#lsBreak1EntradaHora").val();
	Break1EntradaMinutos = $("#lsBreak1EntradaMinutos").val();
	Break1SalidaHora = $("#lsBreak1SalidaHora").val();
	Break1SalidaMinutos = $("#lsBreak1SalidaMinutos").val();
	LunchEntradaHora = $("#lsLunchEntradaHora").val();
	LunchEntradaMinutos = $("#lsLunchEntradaMinutos").val();
	LunchSalidaHora = $("#lsLunchSalidaHora").val();
	LunchSalidaMinutos = $("#lsLunchSalidaMinutos").val();
	Break2EntradaHora = $("#lsBreak2EntradaHora").val();
	Break2EntradaMinutos = $("#lsBreak2EntradaMinutos").val();
	Break2SalidaHora = $("#lsBreak2SalidaHora").val();
	Break2SalidaMinutos = $("#lsBreak2SalidaMinutos").val();
	SalidaHora = $("#lsSalidaHora").val();
	SalidaMinutos = $("#lsSalidaMinutos").val();
	Fecha = $("#fecha").val();
	Fecha2 = $("#fecha2").val();
	Fecha3 = $("#fecha3").val();
	Fecha4 = $("#fecha4").val();
	Fecha5 = $("#fecha5").val(); 
	Off = $("#chOff").attr("checked");
	if(Off){
		Offcheck = 1;	
	}
	else{
		Offcheck = 0;	
	}
	if(Fecha.length<=0){
		alert("Must select a date");	
		$("#fecha").focus();
		return false;
	}
	
	var ArrayAgents = "";
	
	if(Agentes.length<=0){
		alert("Must select at least one agent");	
		$("#sel1").focus();
		return false;
	}
	for(i=0; i<Agentes.length; i++){
		if(i>0){
			ArrayAgents +=" "+Agentes.options[i].value;	
		}
		else{
			ArrayAgents +=""+Agentes.options[i].value;	
		}
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=saveHorario&arrayAgentes="+ArrayAgents+"&entradaHora="+EntradaHora+"&entradaMinutos="+EntradaMinutos+"&break1EntradaHora="+Break1EntradaHora+"&break1EntradaMinutos="+Break1EntradaMinutos+"&break1SalidaHora="+Break1SalidaHora+"&break1SalidaMinutos="+Break1SalidaMinutos+"&lunchEntradaHora="+LunchEntradaHora+"&lunchEntradaMinutos="+LunchEntradaMinutos+"&lunchSalidaHora="+LunchSalidaHora+"&lunchSalidaMinutos="+LunchSalidaMinutos+"&break2EntradaHora="+Break2EntradaHora+"&break2EntradaMinutos="+Break2EntradaMinutos+"&break2SalidaHora="+Break2SalidaHora+"&break2SalidaMinutos="+Break2SalidaMinutos+"&salidaHora="+SalidaHora+"&salidaMinutos="+SalidaMinutos+"&fecha="+Fecha+"&offCheck="+Offcheck+"&fecha2="+Fecha2+"&fecha3="+Fecha3+"&fecha4="+Fecha4+"&fecha5="+Fecha5,
	success: function(rslt){
		if(rslt==1){
			alert("Schedule saved successfully");
			
		}
		else if(rslt ==2){
			alert("Check out time can not be longer than the time of entry");
			$("#lsEntradaHora").focus();
			return false;
		}
		else if(rslt ==3){
			alert("Check out time to break can not be greater than the time of entry");
			$("#lsBreak1EntradaHora").focus();
			return false;
		}
		else if(rslt == 4){
			alert("Check out time to break can not be less than the time of entry to turn");
			$("#lsBreak1EntradaHora").focus();
			return false;
		}
		else if(rslt == 5){
			alert("Lunch time can not be within an hour of break");
			$("#lsLunchEntradaHora").focus();
			return false;
		}
		else if(rslt ==6){
			alert("Time of entry of lunch can not be greater than Check out time");
			$("#lsLunchEntradaHora").focus();
			return false;
		}
		else if(rslt ==7){
			alert("Check out time to break can not be greater than the time of entry");
			$("#lsBreak2EntradaHora").focus();
			return false;
		}
		else if(rslt == 8){
			alert("Lunch time can not be within an hour of break");
			$("#lsBreak2EntradaHora").focus();
			return false;
		}
		else if(rslt ==9){
			alert("You cant take both breaks at the same time");
			$("#lsBreak2EntradaHora").focus();
			return false;
		}
		else{
			alert("Execution problem, try again");	
			return false;
		}
	}
	});	
}

function getDetalleHorario(){
	Fecha = $("#fecha").val();
	if(Fecha.length<=0){
		alert("select a date");	
		$("#fecha").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=getDetalleHorario&fecha="+Fecha,
	success: function(rslt){
		$("#lyDetHorario").css("display","block");
		document.getElementById("lyDetHorario").innerHTML = rslt;
	}
	});		
}

function reportHorario(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=reportHorario&opcion=1",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});	
}
function loadRepHorario(){
	Cuenta = $("#lsCuenta").val();
	Depto = $("#lsDepart").val();
	Posicion = $("#lsPosicion").val();
	Employee = $("#lsEmp").val();
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	Jefe = $("#lsJefe").val();
	if(FechaIni.length<=0){
		alert("You must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("You must select an end date");
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("The start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;	
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=loadRepHorario&cuenta="+Cuenta+"&depto="+Depto+"&posicion="+Posicion+"&employee="+Employee+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&nombre="+Nombre+"&badge="+Badge+"&jefe="+Jefe,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});		
}
function EmpleadosPorHora(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=reportHorario&opcion=2",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});	
}

function loadRepPersonasPorHorario(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPosicion").val();
	Emp = $("#lsEmp").val();
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Hora = $("#lsHora").val();
	Minutos = $("#lsMinuto").val();
	if(FechaIni.length<=0){
		alert("You must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("You must select an end date");
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("The start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=loadRepPersonasPorHorario&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&emp="+Emp+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&hora="+Hora+"&minutos="+Minutos,
	success: function(rslt){
		$("#lyDetalles").css("display","none");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});
		
}

function loadDetPersonas(Fecha,Hora){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPosicion").val();
	Emp = $("#lsEmp").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=loadDetPersonas&fecha="+Fecha+"&hora="+Hora+"&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posicion+"&emp="+Emp,
	success: function(rslt){
		$("#lyDetalles").css("display","block");
		document.getElementById("lyDetalles").innerHTML = rslt;
	}
	});
}

function HorarioEspecial(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=HorarioEspecial",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});		
}
function saveHorarioEspecial(){
	Agentes = document.getElementById('sel2');
	TpAct = $("#lsTpAct").val();
	Fecha = $("#fecha").val();
	SalidaHora = $("#lsSalidaHora").val();
	SalidaMinutos = $("#lsSalidaMinutos").val();
	EntradaHora = $("#lsEntradaHora").val();
	EntradaMinutos = $("#lsEntradaMinutos").val();
	
	var ArrayAgents = "";
	
	if(Agentes.length<=0){
		alert("Error: Must select at least one agent");	
		$("#sel1").focus();
		return false;
	}
	for(i=0; i<Agentes.length; i++){
		if(i>0){
			ArrayAgents +=" "+Agentes.options[i].value;	
		}
		else{
			ArrayAgents +=""+Agentes.options[i].value;	
		}
	}
	if(Fecha.length<=0){
		alert("Error: You must select a date");
		$("#fecha").focus();	
		return false;
	}
	if(TpAct<=0){
		alert("Error: Select an type of activity");
		$("#lsTpAct").focus();
		return false;
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=saveHorarioEspecial&arrayAgents="+ArrayAgents+"&tpAct="+TpAct+"&fecha="+Fecha+"&salidaHora="+SalidaHora+"&salidaMinutos="+SalidaMinutos+"&entradaHora="+EntradaHora+"&entradaMinutos="+EntradaMinutos,
	success: function(rslt){
		if(rslt==-1){
			alert("Error: The start time can not be greater than the end time");
			$("#lsSalidaHora").focus();
			return false;
		}
		else if(rslt>=0){
			alert("successfully programmed activity");
			HorarioEspecial()
			HorarioEspecialDia(rslt);
		}
		else{
			alert("Execution problem, try again");
			return false;
		}
	}
	});
}

function HorarioEspecialDia(Fecha){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=HorarioEspecialDia&fecha="+Fecha,
	success: function(rslt){
		$("#lyDetHorario").css("display","block");
		document.getElementById("lyDetHorario").innerHTML = rslt;
	}
	});		
}

function reportActivities(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=reportActivities",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});	
}
function loadRepActivities(){
	Cuenta = $("#lsCuenta").val();
	Depto = $("#lsDepart").val();
	Posicion = $("#lsPosicion").val();
	Employee = $("#lsEmp").val();
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	if(FechaIni.length<=0){
		alert("You must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("You must select an end date");
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("The start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;	
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=loadRepActivities&cuenta="+Cuenta+"&depto="+Depto+"&posicion="+Posicion+"&employee="+Employee+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&nombre="+Nombre+"&badge="+Badge,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});		
}

function reportProgrammedHours(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=reportProgrammedHours",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});
}
function loadRepProgHours(){
	Cuenta = $("#lsCuenta").val();
	Depto = $("#lsDepart").val();
	Posicion = $("#lsPosicion").val();
	Employee = $("#lsEmp").val();
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	Jefe = $("#lsJefe").val();
	if(FechaIni.length<=0){
		alert("You must select a start date");	
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("You must select an end date");
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("The start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;	
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=loadRepProgHours&cuenta="+Cuenta+"&depto="+Depto+"&posicion="+Posicion+"&employee="+Employee+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&nombre="+Nombre+"&badge="+Badge+"&jefe="+Jefe,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});			
}

function uploadHorario(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=uploadHorario",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});
}
function upFile(){
	document.getElementById('frmDoc').submit();	
}

function loadPageSchedules(){
	uploadHorario();
}
function uploadProgHours(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_schedules.php",
	data: "Do=uploadProgHours",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});	
}
function loadPageProgHours(){
	uploadProgHours();	
}

