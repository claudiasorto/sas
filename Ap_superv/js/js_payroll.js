<!-- Funciones para planilla-->
function onlyNumber(objeto,e){
  var keynum;
  var keychar;
  var numcheck;

  if(window.event){ /*/ IE*/
	keynum = e.keyCode
  } else if(e.which){ /*/ Netscape/Firefox/Opera/*/
	keynum = e.which
  }


  if((keynum>=35 && keynum<=37) || (keynum==8||keynum==9||keynum==39)) {
    return true;
  }

  if((keynum>=48&&keynum<=57) || (keynum>=96&&keynum<=105)){
    return true;
  } else {
    return false;
  }

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
function fecha( cadena ) {  
  
   //Separador para la introduccion de las fechas  
   var separador = "/"  
  
   //Separa por dia, mes y año  
   if ( cadena.indexOf( separador ) != -1 ) {  
        var posi1 = 0  
        var posi2 = cadena.indexOf( separador, posi1 + 1 )  
        var posi3 = cadena.indexOf( separador, posi2 + 1 )  
        this.dia = cadena.substring( posi1, posi2 )  
        this.mes = cadena.substring( posi2 + 1, posi3 ) -1;  
        this.anio = cadena.substring( posi3 + 1, cadena.length )  
   } else {  
        this.dia = 0  
        this.mes = 0  
        this.anio = 0     
   }  
}  

function newPayroll(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_payroll.php",
	data: "Do=newPayroll",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	 }
	});	
}

function upPayrollFile(){
	document.getElementById('lyMsg').style.display = 'block';
	document.getElementById('frmDoc').submit();	
}

function upFileWait(){
	document.getElementById('lyMsg').style.display = 'block';
	document.getElementById('btnUp').disabled = true;
	document.getElementById('frmDoc').submit();	
}

function newRegHora(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_payroll.php",
	data: "Do=newRegHora",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	 }
	});	
}
function loadPayxEmp(){
	Fecha = $("#fecha").val();
	if(Fecha.length <=0){
		alert("Error: You must select a date");
		$("#fecha").focus();
		return false;	
	}
	
	var fechaActual = new Date();
	dia = fechaActual.getDate();
    mes = fechaActual.getMonth() +1;
    anno = fechaActual.getFullYear();
 
    if (dia <10) dia = "0" + dia;
    if (mes <10) mes = "0" + mes;  
 
    fechaHoy = dia + "/" + mes + "/" + anno;
	if(compare_dates(Fecha, fechaHoy)){
		alert("Error: Payroll to enter must be this day or maximum of three days previous");
		$("#fecha").focus();
		return false;
	}
	/**
	var n=0; i=0; flag=false;
	
	var fechaElejida = new fecha(Fecha);
	var fechaElej = new Date(fechaElejida.anio, fechaElejida.mes, fechaElejida.dia);
	var nuevaFecha = new Date(fechaElejida.anio, fechaElejida.mes, fechaElejida.dia);
 
	while(n<=2){
		fechaElej = new Date(fechaElejida.anio, fechaElejida.mes, fechaElejida.dia);
		nuevaFecha = new Date(fechaElejida.anio, fechaElejida.mes, fechaElejida.dia);
		nuevaFecha.setDate(fechaElej.getDate() + i);
		if((nuevaFecha.getDate()+"/"+nuevaFecha.getMonth()+1 +"/"+nuevaFecha.getFullYear()) == (fechaActual.getDate()+"/"+fechaActual.getMonth()+1 +"/"+fechaActual.getFullYear())){
			flag = true;
			n = 4;
		}
		else{
			if(nuevaFecha.getDay()!=0){
				n = n + 1;
			}	
			i = i +1;
		}		
	}
	
	if(flag == false){
		alert("Error: Payroll to enter must be this day or maximum of two days previous");
		$("#fecha").focus();
		return false;
	}
	*/
	$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=loadPayxEmp&fecha="+Fecha,
		success: function(rslt){
			document.getElementById("lyPayDiary").innerHTML = rslt;	
		}
	});	
}
function savePayroll(){	
	Fecha = $("#fecha").val();
	Hora = document.getElementsByName('selHora[]');
	var ArrayHora = "";
	for(i=0; i<Hora.length; i++){
		if(i>0){
			ArrayHora +=" "+Hora[i].value;	
		}	
		else{
			ArrayHora  +=""+Hora[i].value;
		}
	}
	
	$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=savePayroll&hora="+ArrayHora+"&fecha="+Fecha,
		success: function(rslt){
			 if(rslt==2){
				alert("Successful record of hours");
				loadPayxEmp();
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
	});	
}
function reportPayroll(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=reportPayroll",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;	
		}
	});		
}


function load_rptPayroll(){
	Cuenta = $("#lsCuenta").val();
	Depart = $("#lsDpto").val();
	Posc = $("#lsPosc").val();
	Superv = $("#lsSuperv").val();
	FecIni = $("#fec_ini").val();
	FecFin = $("#fec_fin").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtUsername").val();
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
		url: "ajax/ajx_payroll.php",
		data: "Do=load_rptPayroll&cuenta="+Cuenta+"&depart="+Depart+"&posicion="+Posc+"&superv="+Superv+"&fecIni="+FecIni+"&fecFin="+FecFin+"&nombre="+Nombre+"&badge="+Badge,
		success: function(rslt){
			document.getElementById("datos_rpt").innerHTML = rslt;	
		}
	});		
}
function rptPayRoll(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=rptPayRoll",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;	
		}
	});		
}
function load_rptPayrollxSup(){
	FecIni = $("#fec_ini").val();
	FecFin = $("#fec_fin").val();
	Emp = $("#lsEmp").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtUsername").val();
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
		url: "ajax/ajx_payroll.php",
		data: "Do=load_rptPayrollxSup&fecIni="+FecIni+"&fecFin="+FecFin+"&emp="+Emp+"&nombre="+Nombre+"&badge="+Badge,
		success: function(rslt){
			document.getElementById("datos_rpt").innerHTML = rslt;		
		}
	});	
}
function newRegHoraAll(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_payroll.php",
	data: "Do=newRegHoraAll",
	success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
	 }
	});		
}
function loadPayxEmpAll(){
	Fecha = $("#fecha").val();
	Superv = $("#lsSuperv").val();
	Employee = $("#lsEmp").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtUsername").val();
	
	if(Fecha.length <=0){
		alert("Error: You must select a date");
		$("#fecha").focus();
		return false;	
	}
	
	var fechaActual = new Date();
	dia = fechaActual.getDate();
    mes = fechaActual.getMonth() +1;
    anno = fechaActual.getFullYear();
 
    if (dia <10) dia = "0" + dia;
    if (mes <10) mes = "0" + mes;  
 
    fechaHoy = dia + "/" + mes + "/" + anno;
	if(compare_dates(Fecha, fechaHoy)){
		alert("Error: Payroll to enter must be this day or days previous");
		$("#fecha").focus();
		return false;
	}
	$.ajax({
	type: "POST",
	url: "ajax/ajx_payroll.php",
	data: "Do=loadPayxEmpAll&fecha="+Fecha+"&superv="+Superv+"&employee="+Employee+"&nombre="+Nombre+"&badge="+Badge,
	success: function(rslt){
			document.getElementById("lyPayDiary").innerHTML = rslt;
	 }
	});	
}
function savePayrollAll(){
	Fecha = $("#fecha").val();
	Superv = $("#txtSuperv").val();
	Employee = $("#txtEmployee").val();
	Nombre = $("#txtNombre").val();
	Badge = $("#txtBadge").val();
	Hora = document.getElementsByName('selHora[]');
	var ArrayHora = "";
	for(i=0; i<Hora.length; i++){
		if(i>0){
			ArrayHora +=" "+Hora[i].value;	
		}	
		else{
			ArrayHora  +=""+Hora[i].value;
		}
	}
	
	$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=savePayrollAll&hora="+ArrayHora+"&fecha="+Fecha+"&superv="+Superv+"&employee="+Employee+"&nombre="+Nombre+"&badge="+Badge,
		success: function(rslt){
			 if(rslt==2){
				alert("Successful record of hours");
				loadPayxEmpAll();
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
	});	
		
}
function deletePayroll(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=deletePayroll",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
	});	
}
function resetPayroll(){
	FechaIni = $("#fec_ini").val();
	FechaFin = $("#fec_fin").val();
	if(confirm("warning, do you want to reset payroll?")){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=resetPayroll&fechaIni="+FechaIni+"&fechaFin="+FechaFin,
		success: function(rslt){
			 if(rslt==2){
				alert("Reset Successful");
				deletePayroll();
			}
			else{
				alert("Execution problem, try again");
				return false;	
			}
		}
	});	
	}
}
function cleanPayrollBatch(){
	$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=cleanPayrollBatch",
		success: function(rslt){
			$("#msj").css("display","none");
			$("#content").css("display","block");
			document.getElementById("content").innerHTML = rslt;
		}
	});
}
function cleanDbPayrollBatch(){
	FechaIni = $("#fec_ini").val();
	FechaFin = $("#fec_fin").val();
	if(confirm("warning, do you want to clean up the database record of payroll batch? "+
				"this action does not modify the payroll if this is already paid")){
		$.ajax({
		type: "POST",
		url: "ajax/ajx_payroll.php",
		data: "Do=cleanDbPayrollBatch&fechaIni="+FechaIni+"&fechaFin="+FechaFin,
		success: function(rslt){
			if(rslt==1){
				alert("There is no information in the database for the selected period.");
			}
			else if(rslt==2){
				alert("Database clean up Successful");
				cleanPayrollBatch();
			}
			else{
				alert("Execution problem, try again");
				return false;
			}
		}
	});
	}
}
function loadPage(Pag){
   newPayroll();	   	
}
function uploadNightHours(){
	$.ajax({
	type: "POST",
	url: "ajax/ajx_payroll.php",
	data: "Do=uploadNightHours",
	success: function(rslt){
		$("#msj").css("display","none");
		$("#content").css("display","block");
		document.getElementById("content").innerHTML = rslt;
	}
	});
}
function loadPageNightHours(){
	uploadNightHours();
}

