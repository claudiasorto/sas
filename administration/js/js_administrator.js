function updatePass(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_administrator.php",
	data: "Do=updatePass",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});		
}
function UpdPass(){
	IdE =  $("#lsEmp").val();
	Pass = $("#txtPwdNew").val();
	ConfirmPass =$("#txtPwdConfirm").val();
	if(IdE ==0){
		alert("You must select a employee");
		return false;	
	}
	if(Pass != ConfirmPass){
		alert("The new password and confirmation do not match");
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_administrator.php",
	data: "Do=UpdPass&idE="+IdE+"&pass="+Pass,
	success: function(rslt){
		if(rslt==2){
			alert("Your password has been changed successfully!");
			$("#content").css("display","none");
			$("#msj").css("display","block");	
		}
		else{
			alert("Execution problem, try again");
			return false;	
		}
	}
	});	
}
function newApp(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_administrator.php",
	data: "Do=newApp",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});	
}
function loadAppxemp(IdE){
	if(IdE>0){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=loadAppxemp&idE="+IdE,
		success: function(rslt){
			$("#lyaplication").css("display","block");
			document.getElementById("lyaplication").innerHTML = rslt;
		}
		});
	}
	else{
		document.getElementById("lyaplication").innerHTML = "";
		$("#lyaplication").css("display","none");
	}
}
function formNewApp(IdE){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=formNewApp&idE="+IdE,
		success: function(rslt){
			$("#lyNewApp").css("display","block");
			document.getElementById("lyNewApp").innerHTML = rslt;
		}
		});
}
function saveApp(IdE){
	IdApp = $("#lsApp").val();
	if(IdApp<=0){
		alert("Select a application");
		return false;	
	}
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=saveApp&idE="+IdE+"&idApp="+IdApp,
		success: function(rslt){
			if(rslt==2){
				alert("New application save sucessfully");
				$("#lyNewApp").css("display","none");
				loadAppxemp(IdE);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
		});	
}
function deleteApp(IdApp, IdE){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=deleteApp&idApp="+IdApp+"&idE="+IdE,
		success: function(rslt){
			if(rslt==2){
				alert("Remove application sucessfully");
				loadAppxemp(IdE);	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
		});		
}
function loadAccount(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=loadAccount",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
		});			
}
function formNewAccount(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=formNewAccount",
		success: function(rslt){
			$("#lyNewAcc").css("display","block");
			document.getElementById("lyNewAcc").innerHTML = rslt;
		}
		});		
}
function saveAcc(){
	NameAcc = $("#txtNameAcc").val();
	Descrip = $("#txtDesc").val();
	Type = $("#lsType").val();
	if(NameAcc.length<=0){
		alert("Error: Enter the name of the account");	
		$("#txtNameAcc").focus();
		return false;
	}
	if(Type<=0){
		alert("Error: You must select a account type");
		$("#lsType").focus();
		return false;	
	}
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=saveAcc&nameAcc="+NameAcc+"&descrip="+Descrip+"&type="+Type,
		success: function(rslt){
			if(rslt==2){
				alert("New accout save sucessfully");
				$("#lyNewAcc").css("display","none");
				loadAccount();	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
		});		
}
function loadDepto(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=loadDepto",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
		});	
}
function formNewDep(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=formNewDep",
		success: function(rslt){
			$("#lyNewDep").css("display","block");
			document.getElementById("lyNewDep").innerHTML = rslt;
		}
		});	
}
function saveDepto(){
	NameDep = $("#txtNameDep").val();
	Descrip = $("#txtDesc").val();
	if(NameDep.length<=0){
		alert("Error: Enter the name of the department");	
		$("#txtNameDep").focus();
		return false;
	}
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=saveDepto&nameDep="+NameDep+"&descrip="+Descrip,
		success: function(rslt){
			if(rslt==2){
				alert("New department save sucessfully");
				$("#lyNewDep").css("display","none");
				loadDepto();	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
		});		
}
function loadPosc(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=loadPosc",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
		});	
}
function formNewPosc(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=formNewPosc",
		success: function(rslt){
			$("#lyNewPosc").css("display","block");
			document.getElementById("lyNewPosc").innerHTML = rslt;
		}
		});
}
function savePosc(){
	NamePosc = $("#txtNamePosc").val();
	Descrip = $("#txtDesc").val();
	Level = $("#lsLevel").val();
	if(NamePosc.length<=0){
		alert("Error: Enter the name of the position");	
		$("#txtNamePosc").focus();
		return false;
	}
	if(Level <=0){
		alert("Error: Select a position level");
		$("#lsLevel").focus();
		return false;	
	}
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=savePosc&namePosc="+NamePosc+"&descrip="+Descrip+"&level="+Level,
		success: function(rslt){
			if(rslt==2){
				alert("New position save sucessfully");
				$("#lyNewPosc").css("display","none");
				loadPosc();	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
		});		
}

function loadPlacexDep(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=loadPlacexDep",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
		});	
}
function formNewPxDep(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=formNewPxDep",
		success: function(rslt){
			$("#lyNewPxDep").css("display","block");
			document.getElementById("lyNewPxDep").innerHTML = rslt;
		}
		});
}
function savePxDep(){
	Account = $("#lsAccount").val();
	Depart = $("#lsDepart").val();
	Posicion = $("#lsPosc").val();
	Rol = $("#lsRol").val();
	if(Account <=0){
		alert("Error: You must select a account");
		$("#lsAccount").focus();
		return false;	
	}
	if(Depart <=0){
		alert("Error: You must select a department");
		$("#lsDepart").focus();
		return false;	
	}
	if(Posicion <=0){
		alert("Error: You must select a position");
		$("#lsPosc").focus();
		return false;	
	}
	if(Rol <=0){
		alert("Error: You must select a role");
		$("#lsRol").focus();
		return false;	
	}
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=savePxDep&account="+Account+"&depart="+Depart+"&posicion="+Posicion+"&rol="+Rol,
		success: function(rslt){
			if(rslt == 1){
				alert("Error: The selected combination already exists, verify the selected data");
				return false;
			}
			if(rslt==2){
				alert("New position save sucessfully");
				$("#lyNewPosc").css("display","none");
				loadPlacexDep();	
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
		});			
}
function apSetup(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=apSetup",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
		});	

}
function newAP(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=newAP",
		success: function(rslt){
			$("#lyAPType").css("display","block");		
			document.getElementById("lyAPType").innerHTML = rslt;
		}
		});	

}
function cancelAP(){
	$("#lyAPType").css("display","none");
}

function saveAP(){
	name =  $("#txtName").val();
	startDate =  $("#lsStartDate").val();
	endDate =  $("#lsEndDate").val();
	time = $("#lsTime").val();
	salary = $("#lsSalary").val();
	inactive = $("#lsInactive").val();
	areaManager = $("#lsAreaManager").val();
	workforce = $("#lsWorkforce").val();
	hr = $("#lsHR").val();
	generalManager = $("#lsGeneralManager").val();

	if(name.length <= 0){
		alert("Enter an AP name");
		$("#txtName").focus();
		return false;
	}
	
	if(startDate == 0){
		alert("Indicate if the AP will have an initial date");
		$("#lsStartDate").focus();
		return false;
	}

	if(endDate == 0){
		alert("Indicate if the AP will have a final date");
		$("#lsEndDate").focus();
		return false;
	}

	if(time == 0){
		alert("Indicate if the AP will have hours");
		$("#lsTime").focus();
		return false;
	}

	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=saveAP&name="+name+"&startDate="+startDate+"&endDate="+endDate+"&time="+time+
			"&salary="+salary+"&inactive="+inactive+"&areaManager="+areaManager+"&workforce="+workforce+"&hr="+hr+"&generalManager="+generalManager,
		success: function(rslt){

			if(rslt == -1){
				alert("The name inserted is already exists");
				$("#txtName").focus();
				return false;
			}
			if(rslt == -2){
				alert("An error occurred during the creation please try again");
				return false;
			}
			
			if(rslt > 0){
				alert("successfully saved");
				apSetup();
			}
			else{
				alert("An error occurred during the creation please try again");
				return false;
			}

		}
		});		
}

function editAP(id_tpAP){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=editAP&id_tpAP="+id_tpAP,
		success: function(rslt){
			$("#lyAPType").css("display","block");		
			document.getElementById("lyAPType").innerHTML = rslt;
		}
		});	
}
function saveEditAP(id_tpAP){
	name =  $("#txtName").val();
	startDate =  $("#lsStartDate").val();
	endDate =  $("#lsEndDate").val();
	time =  $("#lsTime").val();
	effectiveEnd =  $("#EffectiveEnd").val();
	salary = $("#lsSalary").val();
	inactive = $("#lsInactive").val();
	areaManager = $("#lsAreaManager").val();
	workforce = $("#lsWorkforce").val();
	hr = $("#lsHR").val();
	generalManager = $("#lsGeneralManager").val();

	if(name.length <= 0){
		alert("Enter an AP name");
		$("#txtName").focus();
		return false;
	}

	$.ajax({
		type: "POST",
		url: "ajax/ajx_administrator.php",
		data: "Do=saveEditAP&id_tpAP="+id_tpAP+"&name="+name+"&startDate="+startDate+"&endDate="+endDate+
			"&time="+time+"&effectiveEnd="+effectiveEnd+
			"&salary="+salary+"&inactive="+inactive+"&areaManager="+areaManager+"&workforce="+workforce+
			"&hr="+hr+"&generalManager="+generalManager,
		success: function(rslt){

			if(rslt == -1){
				alert("The name inserted is already exists");
				$("#txtName").focus();
				return false;
			}
			if(rslt == -2){
				alert("An error occurred during the creation please try again");
				return false;
			}
			
			if(rslt > 0){
				alert("successfully saved");
				apSetup();
			}
			else{
				alert("An error occurred during the creation please try again");
				return false;
			}
		}
		});
	
}