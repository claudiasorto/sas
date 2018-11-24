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

function formGetRequest(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=formGetRequest",
	success: function(rslt)
		{
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;	
	}
	});			
}


function getRequest(){
	Status = $("#lsStatus").val();
	TpReq = $("#lsTpReq").val();
	FechaIni = $("#fecha_ini").val();
	FechaFin = $("#fecha_fin").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	if(FechaIni.length>=1){
		if(FechaFin.length<=0){
			alert("Please enter the end date");
			$("#fecha_fin").focus();
			return false;
		}
	}
	if(FechaFin.length>=1){
		if(FechaIni.length<=0){
			alert("Please enter the start date");
			$("#fecha_ini").focus();
			return false;
		}
	}
	if(FechaIni.length>=1 && FechaFin.length>=1){
		if(compare_dates(FechaIni,FechaFin)){
			alert("The start date can not be greater than the end date");	
			$("#fecha_ini").focus();
			return false;
		}
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=getRequest&status="+Status+"&tpReq="+TpReq+"&fechaIni="+FechaIni+"&fechaFin="+FechaFin+"&nombre="+Nombre+"&badge="+Badge,
	success: function(rslt)
		{
			$("#lyClose").css("display","none");
			$("#lyFormDoc").css("display","none");
			$("#lyForm").css("display","block");
			document.getElementById("lyForm").innerHTML = rslt;	
	}
	});	
}

function closeReq(IdR){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=closeReq&idR="+IdR,
	success: function(rslt)
		{
			$("#lyClose").css("display","block");
			document.getElementById("lyClose").innerHTML = rslt;	
	}
	});		
}

function saveCloseRequest(){
	IdR = $("#txtIdR").val();
	Descrip = $("#txtDescrip").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=saveCloseRequest&idR="+IdR+"&descrip="+Descrip,
	success: function(rslt)
		{
		if(rslt==2){
			alert("Request successfully updated");
			getRequest();
		}
		else{
			alert("Execution problem, try again");
			return false;	
		}	
	}
	});	
		
}

function hrForms(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=hrForms",
	success: function(rslt)
		{
			$("#msj").css("display","none");	
			$("#lyContent").css("display","block");
			document.getElementById("lyContent").innerHTML = rslt;
	}
	});	
}

function createDoc(IdR, IdTpReq){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=createDoc&idR="+IdR+"&idTpReq="+IdTpReq,
	success: function(rslt)
		{
			$("#lyFormDoc").css("display","block");
			document.getElementById("lyFormDoc").innerHTML = rslt;	
	}
	});	
}

function saveDoc(){
	Dinero = $("#txtDinero").val();
	IdPaystub = $("#lsPaytub").val();
	Firmas = document.getElementById('sel2');
	IdR = $("#txtIdR").val();
	Descripcion = $("#txtDescripcion").val();
	
	var ArrayFirmas = "";
	
	if(Firmas.length<=0){
		alert("Must select at least one employee");	
		$("#sel1").focus();
		return false;
	}
	for(i=0; i<Firmas.length; i++){
		if(i>0){
			ArrayFirmas +=" "+Firmas.options[i].value;	
		}
		else{
			ArrayFirmas +=""+Firmas.options[i].value;	
		}
	}
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=saveDoc&dinero="+Dinero+"&idPaystub="+IdPaystub+"&arrayFirmas="+ArrayFirmas+"&idR="+IdR+"&descripcion="+Descripcion,
	success: function(rslt)
		{
		if(rslt==2){
			alert("successfully completed document");
			loadDoc(IdR);
		}	
		else{
			alert("execution problem, try again");	
			return false;
		}
	}
	});
}

function loadDoc(IdR){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_request.php",
	data: "Do=loadDoc&idR"+IdR,
	success: function(rslt)
		{
		if(rslt==2){
			alert("successfully completed document");
			loadDoc(IdR);
		}	
		else{
			alert("execution problem, try again");	
			return false;
		}
	}
	});	
}