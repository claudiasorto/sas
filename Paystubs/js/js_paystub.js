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

function lastPaystub(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=lastPaystub",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});
}
function uploadPay(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=uploadPay",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});	

}
function upFile(){
	document.getElementById('frmDoc').submit();	
}
function historicPaystub(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=historicPaystub",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});	
}
function loadPaystub(){
	IdP = $("#lsDate").val();
	if(IdP==0){
		alert("You must select a date");
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadPaystub&idP="+IdP,
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});		
}
function employeesPay(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=employeesPay",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}
function loadEmployeesPay(){
	Fecha = $("#lsDate").val();
	Emp = $("#lsEmp").val();	
	if(Fecha.length<=0){
		alert("You must select a date");
		return false;	
	}
	if(Emp<=0){
		alert("You must select a employee");
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadEmployeesPay&fecha="+Fecha+"&emp="+Emp,
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}
function createPay(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=createPay",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});
}
function newRegPaystub(){
	$("#lyCreate").css("display","block");
}
function saveNewPaystub(){
	FecEntrega = $("#fechaEntrega").val()
	FecIni = $("#fechaIni").val();
	FecFin = $("#fechaFin").val();
	if(FecEntrega.length <=0){
		alert("Error: you must select the delivery date");
		$("#fechaEntrega").focus();
		return false;
	}
	if(FecIni.length<=0){
		alert("Error: you must select the start date of the pay period")
		$("#fechaIni").val();
		return false;
	}
	if(FecFin.length<=0){
		alert("Error: you must select the end date of the pay period");
		$("#fechaFin").val();	
		return false;
	}
	if(compare_dates(FecIni,FecFin)){
		alert("Error: Enter correct data for the pay period");
		$("#fechaIni").focus();
		return false;
	}
	document.getElementById('lyMsg').style.display = 'block';
	document.getElementById('btnSavePaystub').disabled = true;

	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=saveNewPaystub&fecEntrega="+FecEntrega+"&fecIni="+FecIni+"&fecFin="+FecFin,
	success: function(rslt){
	
		if(rslt==-1){
			alert("Error: delivery date entered already exists");
			$("#lyMsg").css("display","none");
			$("#fechaEntrega").focus();
			return false;	
		}
		else if(rslt ==2){
			alert("created successfully");	
			$("#lyMsg").css("display","none");
			createPay();
		}

		else{
			alert("Execution problem, try again");
			$("#lyMsg").css("display","none");
			return false;	
		}
	}
	});	
	
}
function showUpdBtn(idPaystub){
	if(idPaystub.length <= 0 || idPaystub <= 0){
		document.getElementById("lyBtnUpd").innerHTML = "";	
	}
	else{
		document.getElementById("lyBtnUpd").innerHTML = '<input type="button" onClick="formUpdPaystub('+idPaystub+')" value="Update Paystub"/>';
	}
}

function formUpdPaystub(idPaystub){
	$("#lyUpdate").css("display","block");

	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=getFechaPaysub&idP="+idPaystub+"&label="+"paystub_delivery",
	success: function(rslt){
		document.getElementById("fechaEntregaUpd").value = rslt.replace(/^\s+|\s+$/gm,'');	
	}
	});
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=getFechaPaysub&idP="+idPaystub+"&label="+"paystub_ini",
	success: function(rslt){
		document.getElementById("fechaIniUpd").value= rslt.replace(/^\s+|\s+$/gm,'');	
	}
	});		
	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=getFechaPaysub&idP="+idPaystub+"&label="+"paystub_fin",
	success: function(rslt){
		document.getElementById("fechaFinUpd").value=rslt.replace(/^\s+|\s+$/gm,'');	
	}
	});	
				
}

function saveUpdatePaystub(){
	if(confirm("Are you sure to update the selected paystub?")){
		IdP = $("#lsDelivery").val();
		FecIni = $("#fechaIniUpd").val();
		FecFin = $("#fechaFinUpd").val();
		FecEntrega = $("#fechaEntregaUpd").val();

		if(FecEntrega.length <=0){
			alert("Error: you must select the delivery date");
			$("#fechaEntregaUpd").focus();
			return false;
		}
		if(FecIni.length<=0){
			alert("Error: you must select the start date of the pay period")
			$("#fechaIniUpd").val();
			return false;
		}
		if(FecFin.length<=0){
			alert("Error: you must select the end date of the pay period");
			$("#fechaFinUpd").val();	
			return false;
		}
		if(compare_dates(FecIni,FecFin)){
			alert("Error: Enter correct data for the pay period");
			$("#fechaIniUpd").focus();
			return false;
		}
		document.getElementById('lyMsg').style.display = 'block';
		document.getElementById('btnUpdPaystub').disabled = true;

		$.ajax({
		type: "POST",
		url: "ajax/ajx_paystub.php",
		data: "Do=saveUpdatePaystub&idP="+IdP+"&fecEntrega="+FecEntrega+"&fecIni="+FecIni+"&fecFin="+FecFin,
		success: function(rslt){

			if(rslt==-1){
				alert("Error: The entered dates are the same from an exist paystub");
				$("#lyMsg").css("display","none");
				document.getElementById('btnUpdPaystub').disabled = false;
				$("#fechaEntregaUpd").focus();
				return false;	
			}
			else if(rslt == 2){

				$.ajax({
				type: "POST",
				url: "ajax/ajx_paystub.php",
				data: "Do=updPayment&idP="+IdP,
				success: function(rslt){
					if(rslt==2){
						alert("Payslip successfully updated");
						$("#lyMsg").css("display","none");
						createPay();
					}
					else{
						alert("Execution problem, try again");	
						document.getElementById('btnUpdPaystub').disabled = false;
						$("#lyMsg").css("display","none");
						return false;
					}
				
				}
				});	

			}
			else{
				alert("Execution problem, try again");	
				$("#lyMsg").css("display","none");
				document.getElementById('btnUpdPaystub').disabled = false;
				return false;
			}

		}
		});	
	}

}

function calcularPay(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=calcularPay",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});	
}
function loadCalculoPay(){
	IdP = $("#lsDate").val();
	Estado = $("#lsStatus").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadCalculoPay&idP="+IdP+"&estado="+Estado,
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}
function lastPay(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=lastPay",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});	
}
function historicPay(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=historicPay",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}
function loadPay(){
	IdP = $("#lsDate").val();
	if(IdP==0){
		alert("You must select a date");
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadPay&idP="+IdP,
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}
function employeesPayStubs(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=employeesPayStubs",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});	
}
function loadEmployeesPayStubs(){
	Fecha = $("#lsDate").val();
	Emp = $("#lsEmp").val();	
	if(Fecha<=0){
		alert("You must select a date");
		return false;	
	}
	if(Emp<=0){
		alert("You must select a employee");
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadEmployeesPayStubs&fecha="+Fecha+"&emp="+Emp,
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});		
}
function loadDetail(){
	$("#lyDescrip").css("display","block");	
}
function upOtherPay(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=upOtherPay",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}
function loadEmp(IdEstado){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadEmp&idEstado="+IdEstado,
	success: function(rslt){
		document.getElementById("lyEmp").innerHTML = rslt;
	}
	});	
}

<!--Funciones de incidencias de pago-->
function payIncidents(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=payIncidents",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}
function formRegIncidents(){
	IdPay = $("#lsPay").val();
	IdEmp = $("#lsEmp").val();
	Badge = $("#txtBadge").val();
	if(IdEmp<=0){
		if(Badge.length<=0){
			alert("Error: You must select a employee or typing badge");
			$("#lsEmp").focus();
			return false;
		}	
	}
	if(Badge.length<=0){
		if(IdEmp<=0){
			alert("Error: You must select a employee or typing badge");
			$("#lsEmp").focus();
			return false;
		}
	}	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=formRegIncidents&idPay="+IdPay+"&idEmp="+IdEmp+"&badge="+Badge,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
	}
	});	
}
function CalcIncidences(){
	Payxemp_id = $("#txtPayxemp_ID").val();
	Nhoras = $("#txtnhoras").val();
	Nadditionalhours= $("#txtnadditionalhours").val();
	Salarydisc= $("#txtsalarydisc").val();
	Seventh= $("#txtseventh").val();
	Nhorasnoct= $("#txtnhorasnoct").val();
	Notdiurnal= $("#txtnotdiurnal").val();
	Notnoct= $("#txtnotnoct").val();
	Bono= $("#txtbono").val();
	Aguinaldo= $("#txtaguinaldo").val();
	Vacation= $("#txtvacation").val();
	Severance= $("#txtseverance").val();
	Otherincome= $("#txtotherincome").val();
    Attr1= $("#txtattribute1").val();
	Attr2= $("#txtattribute2").val();
	Attr3= $("#txtattribute3").val();
	Attr4= $("#txtattribute4").val();
	Attr5= $("#txtattribute5").val();
	Attr6= $("#txtattribute6").val();
	Attr7= $("#txtattribute7").val();
	Attr8= $("#txtattribute8").val();
	Attr9= $("#txtattribute9").val();
	Attr10= $("#txtattribute10").val();
	Attr11= $("#txtattribute11").val();
	Attr12= $("#txtattribute12").val();
	Attr13= $("#txtattribute13").val();
	Attr14= $("#txtattribute14").val();
	Attr15= $("#txtattribute15").val();
	Attr16= $("#txtattribute16").val();
	Attr17= $("#txtattribute17").val();
	Attr18= $("#txtattribute18").val();
	Attr19= $("#txtattribute19").val();
	Attr20= $("#txtattribute20").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=CalcIncidences&payxemp_id="+Payxemp_id+"&nhoras="+Nhoras+"&nadditionalhours="+Nadditionalhours+"&salarydisc="+Salarydisc+"&seventh="+Seventh+"&nhorasnoct="+Nhorasnoct+"&notdiurnal="+Notdiurnal+"&notnoct="+Notnoct+"&bono="+Bono+"&aguinaldo="+Aguinaldo+"&vacation="+Vacation+"&severance="+Severance+"&otherincome="+Otherincome+"&attribute1="+Attr1+"&attribute2="+Attr2+"&attribute3="+Attr3+"&attribute4="+Attr4+"&attribute5="+Attr5+"&attribute6="+Attr6+"&attribute7="+Attr7+"&attribute8="+Attr8+"&attribute9="+Attr9+"&attribute10="+Attr10+"&attribute11="+Attr11+"&attribute12="+Attr12+"&attribute13="+Attr13+"&attribute14="+Attr14+"&attribute15="+Attr15+"&attribute16="+Attr16+"&attribute17="+Attr17+"&attribute18="+Attr18+"&attribute19="+Attr19+"&attribute20="+Attr20,
	success: function(rslt){
		$("#lySave").css("display","block");
		document.getElementById("lyVariaciones").innerHTML = rslt;
	}
	});	
	
}
function SaveIncidence(){
	Payxemp_id = $("#txtPayxemp_ID").val();
	Payinc_id = $("#txtPayInc_ID").val();
	Nhoras = $("#txtnhoras").val();
	Salary = $("#txtsalary").val();
	Nadditionalhours= $("#txtnadditionalhours").val();
	Additionalhours = $("#txtadditionalhours").val();
	Salarydisc= $("#txtsalarydisc").val();
	Seventh= $("#txtseventh").val();
	Nhorasnoct= $("#txtnhorasnoct").val();
	Horasnoct = $("#txthorasnoct").val();
	Notdiurnal= $("#txtnotdiurnal").val();
	Otdiurnal = $("#txtotdiurnal").val();
	Notnoct= $("#txtnotnoct").val();
	Otnoct = $("#txtotnoct").val();
	Bono= $("#txtbono").val();
	Aguinaldo= $("#txtaguinaldo").val();
	Vacation= $("#txtvacation").val();
	Severance= $("#txtseverance").val();
	Otherincome= $("#txtotherincome").val();
	TotalIngresos = $("#txtTotalIngresos").val();

	Attr1= $("#txtattribute1").val();
	Attr2= $("#txtattribute2").val();
	Attr3= $("#txtattribute3").val();
	Attr4= $("#txtattribute4").val();
	Attr5= $("#txtattribute5").val();
	Attr6= $("#txtattribute6").val();
	Attr7= $("#txtattribute7").val();
	Attr8= $("#txtattribute8").val();
	Attr9= $("#txtattribute9").val();
	Attr10= $("#txtattribute10").val();
	Attr11= $("#txtattribute11").val();
	Attr12= $("#txtattribute12").val();
	Attr13= $("#txtattribute13").val();
	Attr14= $("#txtattribute14").val();
	Attr15= $("#txtattribute15").val();
	Attr16= $("#txtattribute16").val();
	Attr17= $("#txtattribute17").val();
	Attr18= $("#txtattribute18").val();
	Attr19= $("#txtattribute19").val();
	Attr20= $("#txtattribute20").val();
	
	Received = $("#txtliquid").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=SaveIncidence&payxemp_id="+Payxemp_id+"&nhoras="+Nhoras+"&salary="+Salary+
			"&nadditionalhours="+Nadditionalhours+"&additionalhours="+Additionalhours+
			"&salarydisc="+Salarydisc+"&seventh="+Seventh+"&nhorasnoct="+Nhorasnoct+
			"&horasnoct="+Horasnoct+"&notdiurnal="+Notdiurnal+"&otdiurnal="+Otdiurnal+
			"&notnoct="+Notnoct+"&otnoct="+Otnoct+"&bono="+Bono+"&aguinaldo="+Aguinaldo+
			"&vacation="+Vacation+"&severance="+Severance+"&otherincome="+Otherincome+
			"&attr1="+Attr1+"&attr2="+Attr2+"&attr3="+Attr3+"&attr4="+Attr4+
			"&attr5="+Attr5+"&attr6="+Attr6+"&attr7="+Attr7+"&attr8="+Attr8+
			"&attr9="+Attr9+"&attr10="+Attr10+"&attr11="+Attr11+"&attr12="+Attr12+
			"&attr13="+Attr13+"&attr14="+Attr14+"&attr15="+Attr15+"&attr16="+Attr16+
			"&attr17="+Attr17+"&attr18="+Attr18+"&attr19="+Attr19+"&attr20="+Attr20+
			"&received="+Received+"&payinc_id="+Payinc_id+"&totalIngresos="+TotalIngresos,
	success: function(rslt){
		alert(rslt);
		if(rslt==2){
			alert("Incidence successfully saved");	
			formRegIncidents()
		}
		else{
		 	alert("Execution problem, try again");
			return false;
		}
	}
	});		
	
}
function payIncidentesReport(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=payIncidentesReport",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}

function loadReportIncidences(){
	PayId = $("#lsDate").val();
	Emp = $("#lsEmp").val();
	Badge = $("#txtBadge").val();
	Nombre = $("#txtNombre").val();
	Estado = $("#lsStatus").val();
	if(PayId<=0){
		alert("Error: You must select a payment date");	
		$("#lsDate").focus();
		return false;
	}	
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadReportIncidences&payId="+PayId+"&emp="+Emp+"&badge="+Badge+"&nombre="+Nombre+"&estado="+Estado,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
	}
	});
}

function loadDataInc(IdEmp,IdPay){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=formRegIncidents&idPay="+IdPay+"&idEmp="+IdEmp,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
	}
	});
}

function cambiarEstadoIncidencia(IdP,Estado){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=cambiarEstadoIncidencia&idP="+IdP+"&estado="+Estado,
	success: function(rslt){
		if(rslt==2){
			alert("Successfully completed state change");
			loadReportIncidences();
		}
		else{
			alert("Execution problem, try again");
			return false;
		}
	}
	});	
}

function updPayment(IdP){
	if(confirm("Are you sure to update the selected payment?")){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_paystub.php",
		data: "Do=updPayment&idP="+IdP,
		success: function(rslt){
			if(rslt==2){
				alert("Successfully updated payment");
				loadCalculoPay();
			}
			else{
				alert("Execution problem, try again");	
				return false;
			}
		
		}
		});	
	}
}
function enablePaystub(IdP){
	if(confirm("Are you sure to enable paystub?")){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_paystub.php",
		data: "Do=enablePaystub&idP="+IdP,
		success: function(rslt){
			if(rslt==2){
				alert("paystub enabled for all employees");
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

function acceptPaystub(IdP){
	if(confirm("Are you sure to accept payment?")){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_paystub.php",
		data: "Do=acceptPaystub&idP="+IdP,
		success: function(rslt){
			if(rslt==2){
				alert("Payment has been accepted successfully");
				lastPay();
			}
			else{
				alert("Execution problem, try again");	
				return false;
			}
		}
		});	
	}	
}

function acceptPaystubHist(IdP){
	if(confirm("Are you sure to accept payment?")){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_paystub.php",
		data: "Do=acceptPaystub&idP="+IdP,
		success: function(rslt){
			if(rslt==2){
				alert("Payment has been accepted successfully");
				loadPay();
			}
			else{
				alert("Execution problem, try again");	
				return false;
			}
		}
		});	
	}		
}
function payStatusReport(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=payStatusReport",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}
function loadReportPayStatus(){
	IdPay = $("#lsDate").val();
	Estatus = $("#lsStatus").val();
	Employee = $("#lsEmp").val();
	Badge = $("#txtBadge").val();
	Nombre = $("#txtNombre").val();
	EstadoPay = $("#lsStatusPay").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadReportPayStatus&idPay="+IdPay+"&estatus="+Estatus+"&employee="+Employee+"&badge="+Badge+"&nombre="+Nombre+"&estadoPay="+EstadoPay,
	success: function(rslt){
		document.getElementById("lyData").innerHTML = rslt;
	}
	});
}

function chequearPaystub(IdPay,IdEmp){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=chequearPaystub&idPay="+IdPay+"&idE="+IdEmp,
	success: function(rslt){
		$("#lyIncidencias").css("display","block");
		document.getElementById("lyIncidencias").innerHTML = rslt;
	}
	});	
}
function saveTicketPaystub(){
	IdPay = $("#idPaystub").val();	
	IdTicket = $("#idTicket").val();
	IdEmp = $("#idEmp").val();
	Comment = $("#txtComment").val();
	if(Comment.length<=0){
		alert("Enter paystub observations");
		return false;	
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=saveTicketPaystub&idPay="+IdPay+"&idE="+IdEmp+"&idTicket="+IdTicket+"&comment="+Comment,
	success: function(rslt){
		if(rslt==2){
			alert("Incidence of payment successfully saved");
			$("#lyIncidencias").css("display","none");
			lastPay();
		}
		else{
			alert("Execution problem, Try again");	
			return false;
		}
	}
	});
}
function incidencesTickets(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=incidencesTickets",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		$("#lyData").css("display","none");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});		
}

function loadPaystubTicket(){
	Paystub = $("#lsPaystub").val();
	Status = $("#lsStatus").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=loadPaystubTicket&paystub="+Paystub+"&status="+Status+"&nombre="+Nombre+"&badge="+Badge,
	success: function(rslt){
		$("#lyData").css("display","block");
		document.getElementById("lyData").innerHTML = rslt;
	}
	});
}

function acceptTicket(IdT){
	if(confirm("Are you sure to accept this incidence of payment?")){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=acceptTicket&idT="+IdT,
	success: function(rslt){
		if(rslt==2){
			alert("Incidence accepted successfully");
		 	loadPaystubTicket();
		}
		else{
			alert("Execution problem, try again");
		 	return false;	
		}
	}
	});	
	}
}

function rejectTicket(IdT){
	if(confirm("Are you sure to reject this incidence of payment?")){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=rejectTicket&idT="+IdT,
	success: function(rslt){
		if(rslt==2){
			alert("Incidence successfully rejected");
		 	loadPaystubTicket();
		}
		else{
			alert("Execution problem, try again");
		 	return false;	
		}
	}
	});	
	}
}

function discountSetup(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=discountSetup",
	success: function(rslt){

		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});
}

function saveDiscountSetup(){
    Label = $("#txtLabel").val();
	Attribute = $("#lsAttr").val();
	if(Label.length<=0){
		alert("Enter label value");
		return false;
	}
	if(Attribute == 0){
		alert("Select flexfield value");
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=saveDiscountSetup&label="+Label+"&attribute="+Attribute,
	success: function(rslt){
	    if(rslt==0){
			alert("Already exist an active discount with the name "+Label);
		}
		else if(rslt==2){
			alert("Configuration saved successfully");
            discountSetup();
		}
		else{
			alert("Execution problem, Try again");
			return false;
		}
	}
	});
}

function updDiscountForm(DiscountId){
     $.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=updDiscountForm&discountId="+DiscountId,
	success: function(rslt){
		$("#lyUpd"+DiscountId).css("display","block");
		document.getElementById("lyUpd"+DiscountId).innerHTML = rslt;
	}
	});
}

function saveUpdDiscSetup(DiscountId){
    Label = $("#txtLabelUpd"+DiscountId).val();
    StartDate = $("#start_date"+DiscountId).val();
    EndDate = $("#end_date"+DiscountId).val();
    if(Label.length<=0){
		alert("Enter label value");
		return false;
	}
	if(StartDate.length<=0){
		alert("Enter effective start date");
		return false;
	}
    $.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=saveUpdDiscSetup&discountId="+DiscountId+"&label="+Label+"&startDate="+StartDate+"&endDate="+EndDate,
	success: function(rslt){
		if(rslt == 0){
            alert("Cannot be updated because already exist an active discount with the same label or a most recent label created");
		}
		else if(rslt==2){
			alert("Configuration updated Successfully");
			document.getElementById("txtLabelUpd"+DiscountId).innerHTML = "";
			document.getElementById("start_date"+DiscountId).innerHTML = "";
			document.getElementById("end_date"+DiscountId).innerHTML = "";
			$("#lyUpd"+DiscountId).css("display","none");
			discountSetup();
  		}
  		else{
        	alert("Execution problem, Try again");
			return false;
		}
	}
	});
}

function workSchHours(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=workSchHours",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});
}

function rptWorkSchHours(){
	fechaInicio = $("#fechaIni").val();
    fechaFin = $("#fechaFin").val();

    if(fechaInicio.length <= 0){
    	alert("Debe seleccionar una fecha de inicio");
		$("#fechaIni").focus();
		return false;
    }
    if(fechaFin.length <= 0){
    	alert("Debe seleccionar una fecha fin");
		$("#fechaFin").focus();
		return false;
    }

 	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=rptWorkSchHours&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin,
	success: function(rslt){
		if(rslt == -1){
			alert("Sesión no valida, necesita logearse nuevamente");
			location.reload();	
		}
		else if(rslt == -2){
			alert("La fecha final debe ser mayor o igual a la fecha inicial");
			return false;
		}

		else{
			$("#lyData").css("display","block");
			document.getElementById("lyData").innerHTML = rslt;
		}
	}
	});   

}
function legalDiscSetup(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=legalDiscSetup",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#lyContent").css("display","block");
		document.getElementById("lyContent").innerHTML = rslt;
	}
	});
}

function saveLegalDisc(){
	country = $("#lsCountry").val();
    name = $("#txtName").val();
    flagCalculo = $("#lsFlagCalculo").val()
    perc = $("#txtPerc").val();
    bottonAmount = $("#txtBottonAmount").val();
    topAmount = $("#txtTopAmount").val();
    overExcess = $("#txtOverExcess").val();
    fixedFee = $("#txtFixedFee").val();
    flagPension = $("#lsFlagPension").val();
    maxQuotable = $("#txtMaxQuotable").val();
    startDate = $("#fecIni").val();

    if(country <= 0){
    	alert("Pais es requerido");
		$("#lsCountry").focus();
		return false;
    }
	if(name.length <= 0){
    	alert("Nombre de descuento es requerido");
		$("#txtName").focus();
		return false;
    }
    if(perc.length <= 0){
    	alert("Porcentaje de descuento es requerido");
		$("#txtPerc").focus();
		return false;
    }
    if(startDate.length <= 0){
    	alert("Fecha efectiva inicial del descuento es requerida");
		$("#fecIni").focus();
		return false;
    }
    if(flagCalculo == 'NA'){
    	alert("Indicar si el calculo es sobre monto gravado o no gravado");
    	$("#lsFlagCalculo").focus();
    	return false;
    }

	$.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=saveLegalDisc&country="+country+"&name="+name+"&perc="+perc+"&startDate="+startDate+
			"&bottonAmount="+bottonAmount+"&topAmount="+topAmount+"&flagCalculo="+flagCalculo+"&overExcess="+overExcess+
			"&fixedFee="+fixedFee+"&flagPension="+flagPension+"&maxQuotable="+maxQuotable,
	success: function(rslt){
		if(rslt==0){
			alert("Existe un registro activo con este mismo nombre "+name);
			$("#txtName").focus();
			return false;

		}
		else if (rslt == -1) {
			alert("Fecha de inicio del descuento debe ser igual o mayor a la fecha actual");
			$("#fecIni").focus();
			return false;
		}
		else if (rslt == -2) {
			alert("Porcentaje de descuento debe ser numerico y mayor o igual a cero");
			$("#txtPerc").focus();
			return false;
		}
		else if (rslt == -3) {
			alert("Los limites de monto inicial y final deben ser numericos y mayor o igual a cero");
			$("#txtBottonAmount").focus();
			return false;
		}
		else if (rslt == -4) {
			alert("Sobre exceso de, debe ser valor numerico mayor o igual a cero");
			$("#txtOverExcess").focus();
			return false;
		}
		else if (rslt == -5) {
			alert("Cuota fija, debe ser valor numerico mayor o igual a cero");
			$("#txtFixedFee").focus();
			return false;
		}

		else if(rslt==2){
			alert("Configuracion guardada exitosamente");
            legalDiscSetup();
		}
		else{
			alert("Error desconocido, intentar nuevamente");
			return false;
		}
	}
	});
}

function updLegalDiscForm(DiscountId){
     $.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=updLegalDiscForm&discountId="+DiscountId,
	success: function(rslt){
		$("#lyUpd"+DiscountId).css("display","block");
		document.getElementById("lyUpd"+DiscountId).innerHTML = rslt;
	}
	});
}

function saveUpdLegalDisc(DiscountId){
    country = $("#lsCountry"+DiscountId).val(); 
    name = $("#txtLabelUpd"+DiscountId).val();
    flagCalculo = $("#lsFlagCalculo"+DiscountId).val();
    perc = $("#txtPerc"+DiscountId).val();
    bottonAmount = $("#txtBottonAmount"+DiscountId).val();
    topAmount = $("#txtTopAmount"+DiscountId).val();
    overExcess = $("#txtOverExcess"+DiscountId).val();
    fixedFee = $("#txtFixedFee"+DiscountId).val();
    flagPension = $("#lsFlagPension"+DiscountId).val();
    maxQuotable = $("#txtMaxQuotable"+DiscountId).val();
    endDate = $("#end_date"+DiscountId).val();
    

	if(country.length<=0){
		alert("Pais es requerido");
		return false;
	}    
    if(name.length<=0){
		alert("Nombre de descuento es requerido");
		return false;
	}
	if(perc.length<=0){
		alert("Porcentaje de descuento es requerido");
		return false;
	}

    $.ajax({
	type: "POST",
	url: "ajax/ajx_paystub.php",
	data: "Do=saveUpdLegalDisc&discountId="+DiscountId+"&name="+name+"&country="+country+"&endDate="+endDate
		+"&perc="+perc+"&flagCalculo="+flagCalculo+"&bottonAmount="+bottonAmount+"&topAmount="+topAmount
		+"&overExcess="+overExcess+"&fixedFee="+fixedFee+"&flagPension="+flagPension+"&maxQuotable="+maxQuotable,
	success: function(rslt){
		if(rslt == 0){
            alert("No se puede actualizar ya que existe un registro con el mismo nombre");
            return false;
		}
		else if (rslt == -2) {
			alert("Porcentaje de descuento debe ser mayor a cero");
			return false;
		}
		else if(rslt == -1){
			alert("registro no pudo ser actualizado");
			return false;
		}
		else if (rslt == -3) {
			alert("Los limites de monto inicial y final deben ser numericos y mayor o igual a cero");
			$("#txtBottonAmount").focus();
			return false;
		}
		else if (rslt == -4) {
			alert("Sobre exceso de, debe ser valor numerico mayor o igual a cero");
			$("#txtOverExcess").focus();
			return false;
		}
		else if (rslt == -5) {
			alert("Cuota fija, debe ser valor numerico mayor o igual a cero");
			$("#txtFixedFee").focus();
			return false;
		}
		else if(rslt==2){
			alert("Configuration updated Successfully");
			document.getElementById("txtLabelUpd"+DiscountId).innerHTML = "";
			document.getElementById("end_date"+DiscountId).innerHTML = "";
			document.getElementById("lsCountry"+DiscountId).innerHTML = "";
			$("#lyUpd"+DiscountId).css("display","none");
			legalDiscSetup();
  		}
  		else{
        	alert("Execution problem, Try again");
			return false;
		}
	}
	});
}
