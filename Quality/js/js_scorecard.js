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
function msjQuality(){
	$("#lyMensaje").css("display","block");
  $("#lyContent").css("display","none");
	document.getElementById("titulo").innerHTML = "Quality Score";
}
function newMonitoring(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=newMonitoring",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});
}
function getSuperv(IdE){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=getSuperv&idE="+IdE,
	success: function(rslt){
		$("#txtSuperv").attr("value",rslt);
		}
	});
}

function loadForm(){
	IdE = $("#lsAgent").val();
	TpEval = $("#lsTpEval").val();
	TpSkill = $("#lsTpSkill").val();
	if(IdE<=0){
		alert("You must select a agent");	
		return false;
	}
	if(TpEval<=0){
		alert("Error: You must select a evaluation type");
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=loadForm&tpEval="+TpEval+"&idE="+IdE+"&tpSkill="+TpSkill,
	success: function(rslt){
		document.getElementById("lyform").innerHTML = rslt;
		}
	});
	
}
function getSubFail(IdF){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=getSubFail&idF="+IdF,
	success: function(rslt){
		document.getElementById("lySubFail").innerHTML = rslt;
		}
	});
}

<!--Recupera los datos de la evaluacion por medio de arreglos de Items y Comentarios-->
function saveFormCS(){
	ContactId = $("#txtContactId").val();
	Agente = $("#lsAgent").val();
	Query = $("#lsQuery").val();
	Cuenta = $("#txtAccount").val();
	IdEva = $("#lsTpEval").val();
	Razon = $("#txtReason").val();
	Fail = $("#txtFail").val();
	ListFail = $("#lsFail").val();
	ListSubFail = $("#lsSubFail").val();
	Items = document.getElementsByName('item[]');
	Comment1 = $("#txtComments1").val();
	Comment2 = $("#txtComments2").val();
	Comment3 = $("#txtComments3").val();
	Comment4 = $("#txtComments4").val();
	Comment5 = $("#txtComments5").val();
	Comment6 = $("#txtComments6").val();
	Comment7 = $("#txtComments7").val();
	Comment8 = $("#txtComments8").val();
	Comment9 = $("#txtComments9").val();
	Comment10 = $("#txtComments10").val();
	Comment11 = $("#txtComments11").val();
	IdSkill = $("#idSk").val();
	Ncat = $("#nCat").val();
	Nitems = $("#nItems").val();

	if(ListFail>=1){
		if(ListSubFail<=0){
			alert("Error: You must select a subcategory auto fail");
			$("#lsSubFail").focus();
			return false;
		}	
	}
	
	var ArrayItems ="";
	for(i=0; i<Nitems; i++){
		if(i>0){
			if(Items[i].value == 0){
				Items[i].value = 3;	
			}
			ArrayItems +=" "+Items[i].value;	
		}
		else{
			if(Items[i].value == 0){
				Items[i].value = 3;		
			}
			ArrayItems +=""+Items[i].value;			
		}	
	}
	document.getElementById('lyMsg').style.display = 'block';
	document.getElementById('btnSaveCS').disabled = true;
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveFormCS&contactId="+ContactId+"&agente="+Agente+"&cuenta="+Cuenta+"&idEva="+IdEva+"&razon="+Razon+"&fail="+Fail+"&listFail="+ListFail+"&listSubFail="+ListSubFail+"&arrayItems="+ArrayItems+"&comment1="+Comment1+"&comment2="+Comment2+"&comment3="+Comment3+"&comment4="+Comment4+"&comment5="+Comment5+"&comment6="+Comment6+"&comment7="+Comment7+"&comment8="+Comment8+"&comment9="+Comment9+"&comment10="+Comment10+"&comment11="+Comment11+"&idSkill="+IdSkill+"&query="+Query,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation successfully saved");
				loadMonitoringCS(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});
}
function saveFormSales(){
	Agente = $("#lsAgent").val();
	Query = $("#lsQuery").val();
	EnrollID = $("#enrollID").val();
	Fail = $("#txtFail").val();
	ListFail = $("#lsFail").val();
	Items = document.getElementsByName('item[]');
	<!--Comments = document.getElementsByName('txtComments[]');-->
	Comment1 = $("#txtComments1").val();
	Comment2 = $("#txtComments2").val();
	Comment3 = $("#txtComments3").val();
	Comment4 = $("#txtComments4").val();
	Comment5 = $("#txtComments5").val();
	Comment6 = $("#txtComments6").val();
	Comment7 = $("#txtComments7").val();
	Comment8 = $("#txtComments8").val();
	Comment9 = $("#txtComments9").val();
	Comment10 = $("#txtComments10").val();
	Comment11 = $("#txtComments11").val();
	IdSkill = $("#idSk").val();
	Ncat = $("#nCat").val();
	Nitems = $("#nItems").val();
	
	var ArrayItems ="";
	for(i=0; i<Nitems; i++){
		if(i>0){
			if(Items[i].value == 0){
				Items[i].value = 3;	
			}
			ArrayItems +=" "+Items[i].value;	
		}
		else{
			if(Items[i].value == 0){
				Items[i].value = 3;		
			}
			ArrayItems +=""+Items[i].value;			
		}	
	}
	document.getElementById('lyMsg').style.display = 'block';
	document.getElementById('btnSaveSales').disabled = true;
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveFormSales&agente="+Agente+"&enrollId="+EnrollID+"&fail="+Fail+"&listFail="+ListFail+"&arrayItems="+ArrayItems+"&comment1="+Comment1+"&comment2="+Comment2+"&comment3="+Comment3+"&comment4="+Comment4+"&comment5="+Comment5+"&comment6="+Comment6+"&comment7="+Comment7+"&comment8="+Comment8+"&comment9="+Comment9+"&comment10="+Comment10+"&comment11="+Comment11+"&idSkill="+IdSkill+"&query="+Query,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation successfully saved");
				loadMonitoringSales(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});
}

function saveFormNS(){
		Agente = $("#lsAgent").val();
		Time = $("#txtTime").val();
		Enroll = $("#txtEnroll").val();
		Contact = $("#txtContact").val();
		Fail = $("#txtFail").val();
		ListFail = $("#lsFail").val();
		ListSubFail = $("#lsSubFail").val();
		Query = $("#lsQuery").val();
		Items = document.getElementsByName('item[]');
		Comment1 = $("#txtComments1").val();
		Comment2 = $("#txtComments2").val();
		Comment3 = $("#txtComments3").val();
		Comment4 = $("#txtComments4").val();
		Comment5 = $("#txtComments5").val();
		Comment6 = $("#txtComments6").val();
		Comment7 = $("#txtComments7").val();
		Comment8 = $("#txtComments8").val();
		Comment9 = $("#txtComments9").val();
		IdSkill = $("#idSk").val();
		Ncat = $("#nCat").val();
		Nitems = $("#nItems").val();
	if(ListFail>=1){
		if(ListSubFail<=0){
			alert("Error: You must select a subcategory auto fail");
			$("#lsSubFail").focus();
			return false;
		}	
	}
	var ArrayItems ="";

	for(i=0; i<Nitems; i++){
		if(i>0){
			if(Items[i].value == 0){
				Items[i].value = 3;	
			}
			ArrayItems +=" "+Items[i].value;	
		}
		else{
			if(Items[i].value == 0){
				Items[i].value = 3;		
			}
			ArrayItems +=""+Items[i].value;			
		}	
	}
	document.getElementById('lyMsg').style.display = 'block';
	document.getElementById('btnSaveNS').disabled = true;
	
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveFormNS&agente="+Agente+"&time="+Time+"&enrollId="+Enroll+"&contact="+Contact+"&fail="+Fail+"&listFail="+ListFail+"&listSubFail="+ListSubFail+"&arrayItems="+ArrayItems+"&comment1="+Comment1+"&comment2="+Comment2+"&comment3="+Comment3+"&comment4="+Comment4+"&comment5="+Comment5+"&comment6="+Comment6+"&comment7="+Comment7+"&comment8="+Comment8+"&comment9="+Comment9+"&idSkill="+IdSkill+"&query="+Query,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation successfully saved");
				loadMonitoringNS(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});	
}

function saveFormChat(){
	Agente = $("#lsAgent").val();
	Account = $("#txtAccount").val();
	Reason = $("#txtReason").val();
	Fail = $("#txtFail").val();
	ListFail = $("#lsFail").val();
	Items = document.getElementsByName('item[]');
	Comment = $("#txtComments").val();
	IdSkill = $("#idSk").val();
	Ncat = $("#nCat").val();
	Nitems = $("#nItems").val();
	var ArrayItems ="";
	for(i=0; i<Nitems; i++){
		if(i>0){
			if(Items[i].value == 0){
				Items[i].value = 3;	
			}
			ArrayItems +=" "+Items[i].value;	
		}
		else{
			if(Items[i].value == 0){
				Items[i].value = 3;		
			}
			ArrayItems +=""+Items[i].value;			
		}	
	}
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveFormChat&agente="+Agente+"&account="+Account+"&reason="+Reason+"&fail="+Fail+"&listFail="+ListFail+"&arrayItems="+ArrayItems+"&comment="+Comment+"&idSkill="+IdSkill,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation successfully saved");
				loadMonitoringChat(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});		
}

function loadMonitoringCS(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringCS&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				$("#lyMensaje").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyContent").innerHTML = rslt;
			}
		}
	});	
}
function loadMonitoringCSbyReport(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringCS&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				document.getElementById("lyData").innerHTML = rslt;
			}
		}
	});	
}


function loadMonitoringSales(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringSales&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				$("#lyMensaje").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyContent").innerHTML = rslt;
			}
		}
	});
}
function loadMonitoringSalesbyReport(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringSales&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				document.getElementById("lyData").innerHTML = rslt;
			}
		}
	});
}

function loadMonitoringChatbyReport(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringChat&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				document.getElementById("lyData").innerHTML = rslt;
			}
		}
	});	
}

function loadMonitoringNS(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringNS&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				$("#lyMensaje").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyContent").innerHTML = rslt;
			}
		}
	});	
}
function loadMonitoringChat(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringChat&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				$("#lyMensaje").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyContent").innerHTML = rslt;
			}
		}
	});	
}
function loadMonitoringNSbyReport(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=loadMonitoringNS&idM="+IdM,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				document.getElementById("lyData").innerHTML = rslt;
			}
		}
	});	
}

function MonitLog(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=MonitLog",
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				$("#lyMensaje").css("display","none");
				$("#lyData").css("display","none");
				$("#lyAutorizar").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyContent").innerHTML = rslt;
			}
		}
	});		
}
function load_Monitlog(){
	Fec_ini = $("#fecha_ini").val();
	Fec_fin = $("#fecha_fin").val();
	TpEval = $("#lsTpEval").val();
	IdEmp = $("#lsEmp").val();
	if(IdEmp <=-1){
		alert("Does not have permission to perform this action");	
		$("#lsEmp").focus();
		return false;
	}
	if(TpEval<=0){
		alert("Error: You must select a evaluation type");
		$("#lsTpEval").focus();
		return false;
	}
	if(compare_dates(Fec_ini, Fec_fin)){
		alert("Error: The start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;
	}

	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=load_Monitlog&fec_ini="+Fec_ini+"&fec_fin="+Fec_fin+"&tpEval="+TpEval+"&idEmp="+IdEmp,
		success: function(rslt){
			if(rslt==-1){
				alert("Execution problem, try again");
				return false;
			}
			else{
				$("#lyAutorizar").css("display","none");
				document.getElementById("lyData").innerHTML = rslt;
			}
		}
	});	
}
function ReportsQa(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=Reports&tpRep=1",
		success: function(rslt){
				$("#lyMensaje").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyContent").innerHTML = rslt;
		}
	});			
}
function ReportSbs(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=Reports&tpRep=2",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
});		
}
function getEmployees(IdS){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=getEmployees&idS="+IdS,
		success: function(rslt){
			document.getElementById("lyEmp").innerHTML = rslt;
		}
	});	
}
function getMultipleEmployees(IdS){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=getMultipleEmployees&idS="+IdS,
		success: function(rslt){
			document.getElementById("lyEmp").innerHTML = rslt;
		}
	});	
}

function loadReportQa(){
	Monit = $("#lsMonit").val();
	Fecha_ini = $("#fecha_ini").val();
	Fecha_fin = $("#fecha_fin").val();
	Sup = $("#lsSup").val();	
	Emp = $("#lsEmp").val();
	Qa = $("#lsQa").val();
	Posicion = $("#lsPosicion").val();
	Cuenta = $("#lsCuenta").val();
	Report = $("#lsReport").val();
	if(Fecha_ini.length <= 0 && Fecha_fin.length <= 0){
		alert("Error: Must select the evaluation period");	
		$("#fecha_ini").focus();
		return false;
	}
	if(compare_dates(Fecha_ini, Fecha_fin)){
		alert("Error: The start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;
	}
	if(Report<=0){
		alert("Error: must select a type of report");
		$("#lsReport").focus();
		return false;
	}
	if(Report>=1 && Report<=2){
		if(Monit <=0){
			alert("Error: must select a type of evaluation");	
			$("#lsMonit").val();
			return false;
		}
	}
	if(Report==1){
		$.ajax({
			type: "POST",
			url: "ajax/ajx_scorecard.php",
			data: "Do=loadReportQaDetails&fecha_ini="+Fecha_ini+"&fecha_fin="+Fecha_fin+"&sup="+Sup+"&emp="+Emp+"&report="+Report+"&monit="+Monit+"&qa="+Qa+"&cuenta="+Cuenta+"&posicion="+Posicion,
			success: function(rslt){
				$("#lyMensaje").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyData").innerHTML = rslt;
			}
		});
	}
	else if(Report ==2){
		$.ajax({
			type: "POST",
			url: "ajax/ajx_scorecard.php",
			data: "Do=loadReportQaTotal&fecha_ini="+Fecha_ini+"&fecha_fin="+Fecha_fin+"&sup="+Sup+"&emp="+Emp+"&report="+Report+"&monit="+Monit+"&qa="+Qa+"&cuenta="+Cuenta+"&posicion="+Posicion,
			success: function(rslt){
				$("#lyMensaje").css("display","none");
				$("#lyContent").css("display","block");
				document.getElementById("lyData").innerHTML = rslt;
			}
		});	
	}
	else if(Report ==3){
		$.ajax({
			type: "POST",
			url: "ajax/ajx_scorecard.php",
			data: "Do=loadMonitoringReport&fecha_ini="+Fecha_ini+"&fecha_fin="+Fecha_fin+"&sup="+Sup+"&emp="+Emp+"&report="+Report+"&monit="+Monit+"&qa="+Qa+"&cuenta="+Cuenta+"&posicion="+Posicion,
			success: function(rslt){
			$("#lyMensaje").css("display","none");
			$("#lyContent").css("display","block");
			document.getElementById("lyData").innerHTML = rslt;
		}
	});		
	}
}

function loadReportSbs(){
	FechaIni = $("#fecha_ini").val();
 	FechaFin = $("#fecha_fin").val();
	if(FechaIni.length<=0){
		alert("Must select a start date");
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Must select a end date");
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("the start date can not be greater than the end date");
		$("#fecha_ini").focus();
		return false;	
	}
		
	Cuenta = $("#lsCuenta").val();
	Sup = $("#lsSup").val();
	Emp = $("#lsEmp").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=loadReportSbs&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&cuenta="+Cuenta+"&sup="+Sup+"&emp="+Emp,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
		}
	});	
	
}


function loadDetallePromedio(Fecha,IdE, Monit){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=loadDetallePromedio&fecha="+Fecha+"&idE="+IdE+"&monit="+Monit,
	success: function(rslt){
		document.getElementById("lyDetalle").innerHTML = rslt;
		}
	});		
}
function updEvaCS(IdM){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=updEvaCS&idM="+IdM,
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});
}

function saveUpFormCS(IdM){
	Emp = $("#lsEmp").val();
	ContactId = $("#txtContactId").val();
	Account = $("#txtAccount").val();
	Reason = $("#txtReason").val();
	Skill = $("#lsSkill").val();
	Fail = $("#txtFail").val();
	ListFail = $("#lsFail").val();
	ListSubFail = $("#lsSubFail").val();
	Items = document.getElementsByName('item[]');
	Comment1 = $("#txtComments1").val();
	Comment2 = $("#txtComments2").val();
	Comment3 = $("#txtComments3").val();
	Comment4 = $("#txtComments4").val();
	Comment5 = $("#txtComments5").val();
	Comment6 = $("#txtComments6").val();
	Comment7 = $("#txtComments7").val();
	Comment8 = $("#txtComments8").val();
	Comment9 = $("#txtComments9").val();
	Comment10 = $("#txtComments10").val();
	Comment11 = $("#txtComments11").val();
	Ncat = $("#nCat").val();
	Nitems = $("#nItems").val();
	
	if(ListFail>=1){
		if(ListSubFail<=0){
			alert("Error: You must select a subcategory auto fail");
			$("#lsSubFail").focus();
			return false;
		}	
	}
	
	var ArrayItems ="";
	for(i=0; i<Nitems; i++){
		if(i>0){
			ArrayItems +=" "+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}	
		}
		else{
			ArrayItems +=""+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}			
		}	
	}
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveUpFormCS&idM="+IdM+"&emp="+Emp+"&contactId="+ContactId+"&account="+Account+"&reason="+Reason+"&skill="+Skill+"&fail="+Fail+"&listFail="+ListFail+"&listSubFail="+ListSubFail+"&arrayItems="+ArrayItems+"&comment1="+Comment1+"&comment2="+Comment2+"&comment3="+Comment3+"&comment4="+Comment4+"&comment5="+Comment5+"&comment6="+Comment6+"&comment7="+Comment7+"&comment8="+Comment8+"&comment9="+Comment9+"&comment10="+Comment10+"&comment11="+Comment11,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation update successfully");
				loadMonitoringCS(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});	
}

function updEvaSales(IdM){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=updEvaSales&idM="+IdM,
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});
}
function updEvaChat(IdM){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=updEvaChat&idM="+IdM,
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});
}
function saveUpFormSales(IdM){
	Emp = $("#lsEmp").val();
	EnrollId = $("#txtEnrollId").val();
	Fail = $("#txtFail").val();
	ListFail = $("#lsFail").val();
	Skill = $("#lsSkill").val();
	Items = document.getElementsByName('item[]');
	Comment1 = $("#txtComments1").val();
	Comment2 = $("#txtComments2").val();
	Comment3 = $("#txtComments3").val();
	Comment4 = $("#txtComments4").val();
	Comment5 = $("#txtComments5").val();
	Comment6 = $("#txtComments6").val();
	Comment7 = $("#txtComments7").val();
	Comment8 = $("#txtComments8").val();
	Comment9 = $("#txtComments9").val();
	Comment10 = $("#txtComments10").val();
	Comment11 = $("#txtComments11").val();
	Ncat = $("#nCat").val();
	Nitems = $("#nItems").val();
	
	if(ListFail>=1){
		if(ListSubFail<=0){
			alert("Error: You must select a subcategory auto fail");
			$("#lsSubFail").focus();
			return false;
		}	
	}
	
	var ArrayItems ="";
	for(i=0; i<Nitems; i++){
		if(i>0){
			ArrayItems +=" "+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}	
		}
		else{
			ArrayItems +=""+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}			
		}	
	}
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveUpFormSales&idM="+IdM+"&emp="+Emp+"&enrollId="+EnrollId+"&skill="+Skill+"&fail="+Fail+"&listFail="+ListFail+"&arrayItems="+ArrayItems+"&comment1="+Comment1+"&comment2="+Comment2+"&comment3="+Comment3+"&comment4="+Comment4+"&comment5="+Comment5+"&comment6="+Comment6+"&comment7="+Comment7+"&comment8="+Comment8+"&comment9="+Comment9+"&comment10="+Comment10+"&comment11="+Comment11,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation update successfully");
				loadMonitoringSales(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});	
}

function updEvaNS(IdM){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=updEvaNS&idM="+IdM,
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
		}
	});	
}
function saveUpFormNS(IdM){
	Emp = $("#lsEmp").val();
	EnrollId = $("#txtEnrollId").val();
	ContactId = $("#txtContactId").val();
	Time = $("#txtTime").val();
	Skill = $("#lsSkill").val();
	Fail = $("#txtFail").val();
	ListFail = $("#lsFail").val();
	ListSubFail = $("#lsSubFail").val();
	Items = document.getElementsByName('item[]');
	Comment1 = $("#txtComments1").val();
	Comment2 = $("#txtComments2").val();
	Comment3 = $("#txtComments3").val();
	Comment4 = $("#txtComments4").val();
	Comment5 = $("#txtComments5").val();
	Comment6 = $("#txtComments6").val();
	Comment7 = $("#txtComments7").val();
	Comment8 = $("#txtComments8").val();
	Comment9 = $("#txtComments9").val();
	Ncat = $("#nCat").val();
	Nitems = $("#nItems").val();
	
	if(ListFail>=1){
		if(ListSubFail<=0){
			alert("Error: You must select a subcategory auto fail");
			$("#lsSubFail").focus();
			return false;
		}	
	}
	
	var ArrayItems ="";
	for(i=0; i<Nitems; i++){
		if(i>0){
			ArrayItems +=" "+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}	
		}
		else{
			ArrayItems +=""+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}			
		}	
	}
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveUpFormNS&idM="+IdM+"&emp="+Emp+"&enrollId="+EnrollId+"&contactId="+ContactId+"&time="+Time+"&skill="+Skill+"&fail="+Fail+"&listFail="+ListFail+"&listSubFail="+ListSubFail+"&arrayItems="+ArrayItems+"&comment1="+Comment1+"&comment2="+Comment2+"&comment3="+Comment3+"&comment4="+Comment4+"&comment5="+Comment5+"&comment6="+Comment6+"&comment7="+Comment7+"&comment8="+Comment8+"&comment9="+Comment9,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation update successfully");
				loadMonitoringNS(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});		
}

function saveUpFormChat(IdM){
	Fail = $("#txtFail").val();
	ListFail = $("#lsFail").val();
	Items = document.getElementsByName('item[]');
	Comment = $("#txtComment").val();
	Ncat = $("#nCat").val();
	Nitems = $("#nItems").val();
	var ArrayItems ="";
	for(i=0; i<Nitems; i++){
		if(i>0){
			ArrayItems +=" "+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}	
		}
		else{
			ArrayItems +=""+Items[i].value;
			if(Items[i].value == 0){
				j = i+1;
				alert("Must respond to the item: "+j);
				return false;	
			}			
		}	
	}
		$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=saveUpFormChat&idM="+IdM+"&fail="+Fail+"&listFail="+ListFail+"&arrayItems="+ArrayItems+"&comment="+Comment,
		success: function(rslt){
			if(rslt>0){
				alert("Evaluation update successfully");
				loadMonitoringChat(rslt);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
			}
		});	
}

function changeAttachChat(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=changeAttachChat&idM="+IdM,
		success: function(rslt){
			document.getElementById("lyDoc").innerHTML = rslt;
		}
	});		
}
function upFileChat(){
  ar = document.getElementById('flDoc').value;
  if(ar.length <= 0){
    alert("Seleccione el archivo a subir!");
	document.getElementById('flDoc').focus;
	return false;
  }
  //return false;
  document.getElementById('lyMsgCv').style.display = 'block';
  document.getElementById('btnUp').disabled = true;
  document.getElementById('btnCancel').disabled = true;
  document.getElementById('frmCv').submit();
}

function loadPage(pag){
   MonitLog()  	   	
}

function changeAttachCS(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=changeAttachCS&idM="+IdM,
		success: function(rslt){
			document.getElementById("lyDoc").innerHTML = rslt;
		}
	});		
}
function upFileCS(){
  ar = document.getElementById('flDoc').value;
  if(ar.length <= 0){
    alert("Seleccione el archivo a subir!");
	document.getElementById('flDoc').focus;
	return false;
  }
  //return false;
  document.getElementById('lyMsgCv').style.display = 'block';
  document.getElementById('btnUp').disabled = true;
  document.getElementById('btnCancel').disabled = true;
  document.getElementById('frmCv').submit();
}

function changeAttachSales(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=changeAttachSales&idM="+IdM,
		success: function(rslt){
			document.getElementById("lyDoc").innerHTML = rslt;
		}
	});		
}
function upFileSales(){
  ar = document.getElementById('flDoc').value;
  if(ar.length <= 0){
    alert("Seleccione el archivo a subir!");
	document.getElementById('flDoc').focus;
	return false;
  }
  //return false;
  document.getElementById('lyMsgCv').style.display = 'block';
  document.getElementById('btnUp').disabled = true;
  document.getElementById('btnCancel').disabled = true;
  document.getElementById('frmCv').submit();
}

function changeAttachNS(IdM){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_scorecard.php",
		data: "Do=changeAttachNS&idM="+IdM,
		success: function(rslt){
			document.getElementById("lyDoc").innerHTML = rslt;
		}
	});		
}
function upFileNS(){
  ar = document.getElementById('flDoc').value;
  if(ar.length <= 0){
    alert("Seleccione el archivo a subir!");
	document.getElementById('flDoc').focus;
	return false;
  }
  //return false;
  document.getElementById('lyMsgCv').style.display = 'block';
  document.getElementById('btnUp').disabled = true;
  document.getElementById('btnCancel').disabled = true;
  document.getElementById('frmCv').submit();
}

function cancelDoc(){
	document.getElementById("lyDoc").innerHTML = "";	
}

function filtrosWeekly(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=filtrosWeekly",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
});		
}

function loadWeeklyReport(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Cuenta = $("#lsCuenta").val();
	Sup = $("#lsSup").val();
	Agentes = document.getElementById('sel2');
	Qa = $("#lsQa").val();
	Status = $("#lsStatus").val();
	if(FechaIni.length<=0){
		alert("Error: You must select the start date");
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: You must select the end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: must select an evaluation period");
		$("#fecha_ini").focus();
		return false;
	}
	
	var ArrayAgents = "";
	var Agentes0 = 1;
	if(Agentes.length<=0){
		Agentes0 = 0;
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
	url: "ajax/ajx_scorecard.php",
	data: "Do=loadWeeklyReport&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&cuenta="+Cuenta+"&sup="+Sup+"&qa="+Qa+"&status="+Status+"&arrayAgentes="+ArrayAgents+"&agentes0="+Agentes0,
	success: function(rslt){
		if(rslt==1){
			alert("The start date must be day Monday");
			$("#fecha_ini").focus();
			return false;
		}
		if(rslt==2){
			alert("The end date should be Saturday");
			$("#fecha_fin").focus();
			return false;
		}
		if(rslt==3){
			alert("The period must be in the same week");
			$("#fecha_ini").focus();
			return false;
		}
		
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
});		
}

function filtrosMonthly(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=filtrosMonthly",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
});		
}

function loadMonthlyReport(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Cuenta = $("#lsCuenta").val();
	Sup = $("#lsSup").val();
	Agentes = document.getElementById('sel2');
	Qa = $("#lsQa").val();
	Status = $("#lsStatus").val();
	if(FechaIni.length<=0){
		alert("Error: You must select the start date");
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: You must select the end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: must select an evaluation period");
		$("#fecha_ini").focus();
		return false;
	}
	
	var ArrayAgents = "";
	var Agentes0 = 1;
	if(Agentes.length<=0){
		Agentes0 = 0;
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
	url: "ajax/ajx_scorecard.php",
	data: "Do=loadMonthlyReport&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&cuenta="+Cuenta+"&sup="+Sup+"&qa="+Qa+"&status="+Status+"&arrayAgentes="+ArrayAgents+"&agentes0="+Agentes0,
	success: function(rslt){
		if(rslt==1){
			alert("The start date must be day Monday");
			$("#fecha_ini").focus();
			return false;
		}
		if(rslt==2){
			alert("The end date should be Saturday");
			$("#fecha_fin").focus();
			return false;
		}
		
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
});		
}
function filtrosLobAverage(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=filtrosLobAverage",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
});			
}

function loadLobAverage(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Cuenta = $("#lsCuenta").val();
	Sup = $("#lsSup").val();
	Status = $("#lsStatus").val();
	if(FechaIni.length<=0){
		alert("Error: You must select the start date");
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: You must select the end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: must select an evaluation period");
		$("#fecha_ini").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=loadLobAverage&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&cuenta="+Cuenta+"&sup="+Sup+"&status="+Status,
	success: function(rslt){
		if(rslt==1){
			alert("The start date must be day Monday");
			$("#fecha_ini").focus();
			return false;
		}
		if(rslt==2){
			alert("The end date should be Saturday");
			$("#fecha_fin").focus();
			return false;
		}
		
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});			
}
function veryfKey(e,t){
	if(e.keyCode){k=e.keyCode;}	
	else{k=e.which;}
	//comprobamos si la tecla presionada es ENTER
	if(k==13){
	    if(t==1){
		alert ('Enter your password');
		document.getElementById("txtClave").focus();
	  }
	  else{	   
	    document.getElementById("lyBtnSave").focus();
		saveDelEvaCS();
	  }
	}
  }
  
function delEvaCS(IdCS){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=delEvaCS&idCS="+IdCS,
	success: function(rslt){
		$("#lyAutorizar").css("display","block");
		document.getElementById("lyAutorizar").innerHTML = rslt;
	}
	});		
}


function saveDelEvaCS(){
	IdCS = $("#idCS").val();
	User = $("#txtUser").val();
	Clave = $("#txtClave").val();
	if(User.length<=0){
		alert("You must enter a username quality supervisor or management");
		$("#txtUser").focus();
		return false;	
	}	
	if(Clave.length<=0){
		alert("Enter your password");
		$("#txtClave").focus();
		return false;
	}  
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=saveDelEvaCS&idCS="+IdCS+"&user="+User+"&clave="+Clave,
	success: function(rslt){
		if(rslt==0){
			alert("Username does not exist");
			$("#txtUser").focus();
			return false;	
		}
		else if(rslt==1){
			alert("wrong password");
			$("#txtClave").focus();
			return false;	
		}
		else if(rslt==2){
			alert("not active user");
			$("#txtUser").focus();
			return false;	
		}
		else if(rslt==4){
			alert("unauthorized user");
			$("#txtUser").focus();
			return false;
		}
		else if(rslt ==3){
			alert("Record deleted successfully");
			load_Monitlog()
		}
	}
	});	
	  
}
function delEvaSales(IdSales){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=delEvaSales&idSales="+IdSales,
	success: function(rslt){
		$("#lyAutorizar").css("display","block");
		document.getElementById("lyAutorizar").innerHTML = rslt;
	}
	});		
}


function saveDelEvaSales(){
	IdSales = $("#idSales").val();
	User = $("#txtUser").val();
	Clave = $("#txtClave").val();
	if(User.length<=0){
		alert("You must enter a username quality supervisor or management");
		$("#txtUser").focus();
		return false;	
	}	
	if(Clave.length<=0){
		alert("Enter your password");
		$("#txtClave").focus();
		return false;
	}  
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=saveDelEvaSales&idSales="+IdSales+"&user="+User+"&clave="+Clave,
	success: function(rslt){
		if(rslt==0){
			alert("Username does not exist");
			$("#txtUser").focus();
			return false;	
		}
		else if(rslt==1){
			alert("wrong password");
			$("#txtClave").focus();
			return false;	
		}
		else if(rslt==2){
			alert("not active user");
			$("#txtUser").focus();
			return false;	
		}
		else if(rslt==4){
			alert("unauthorized user");
			$("#txtUser").focus();
			return false;
		}
		else if(rslt ==3){
			alert("Record deleted successfully");
			load_Monitlog()
		}
	}
	});	
	  
}

function delEvaNS(IdNS){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=delEvaNS&idNS="+IdNS,
	success: function(rslt){
		$("#lyAutorizar").css("display","block");
		document.getElementById("lyAutorizar").innerHTML = rslt;
	}
	});		
}


function saveDelEvaNS(){
	IdNS = $("#idNS").val();
	User = $("#txtUser").val();
	Clave = $("#txtClave").val();
	if(User.length<=0){
		alert("You must enter a username quality supervisor or management");
		$("#txtUser").focus();
		return false;	
	}	
	if(Clave.length<=0){
		alert("Enter your password");
		$("#txtClave").focus();
		return false;
	}  
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=saveDelEvaNS&idNS="+IdNS+"&user="+User+"&clave="+Clave,
	success: function(rslt){
		if(rslt==0){
			alert("Username does not exist");
			$("#txtUser").focus();
			return false;	
		}
		else if(rslt==1){
			alert("wrong password");
			$("#txtClave").focus();
			return false;	
		}
		else if(rslt==2){
			alert("not active user");
			$("#txtUser").focus();
			return false;	
		}
		else if(rslt==4){
			alert("unauthorized user");
			$("#txtUser").focus();
			return false;
		}
		else if(rslt ==3){
			alert("Record deleted successfully");
			load_Monitlog()
		}
	}
	});	
	  
}

function filtrosRosalindWeekly(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_scorecard.php",
	data: "Do=filtrosRosalindWeekly",
	success: function(rslt){
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
});		
}


function loadRosalindWeeklyReport(){
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Cuenta = $("#lsCuenta").val();
	Sup = $("#lsSup").val();
	Agentes = document.getElementById('sel2');
	Qa = $("#lsQa").val();
	Status = $("#lsStatus").val();
	if(FechaIni.length<=0){
		alert("Error: You must select the start date");
		$("#fecha_ini").focus();
		return false;
	}
	if(FechaFin.length<=0){
		alert("Error: You must select the end date");	
		$("#fecha_fin").focus();
		return false;
	}
	if(compare_dates(FechaIni,FechaFin)){
		alert("Error: must select an evaluation period");
		$("#fecha_ini").focus();
		return false;
	}
	
	var ArrayAgents = "";
	var Agentes0 = 1;
	if(Agentes.length<=0){
		Agentes0 = 0;
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
	url: "ajax/ajx_scorecard.php",
	data: "Do=loadRosalindWeeklyReport&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&cuenta="+Cuenta+"&sup="+Sup+"&qa="+Qa+"&status="+Status+"&arrayAgentes="+ArrayAgents+"&agentes0="+Agentes0,
	success: function(rslt){
		if(rslt==1){
			alert("The start date must be day Monday");
			$("#fecha_ini").focus();
			return false;
		}
		if(rslt==2){
			alert("The end date should be Saturday");
			$("#fecha_fin").focus();
			return false;
		}
		if(rslt==3){
			alert("The period must be in the same week");
			$("#fecha_ini").focus();
			return false;
		}
		
		$("#lyMensaje").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
});		
}

