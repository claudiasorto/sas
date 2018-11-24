function loadPage(pag){  //funcion para cargar paginas principales.
  crearDiagLoad("Cargando...", "cargando p&aacute;gina, espere...", true, 125,300);
  $.ajax({
    type: "POST",
    url: "ajax/ajx_common.php",
    data: "Do=loadPage&Pag="+pag,
    success: function(rslt){
      datRslt = rslt.split("|-|");
      $('#lyMain').attr('innerHTML', datRslt[0]);
      $("#dialog-load").dialog("close");
      eval(datRslt[1]);
    },
    error: function(rslt) {
      crearDiagOK("ERROR", 'Ha habido un error en la ejecuci&oacuten '+rslt+'. <br>Intente nuevamente', true, 200, 450, "");
    }
  });
}

function SHFiltro(){ //motrar u ocultar layer de filtro	
  if($("#chkFilt").attr("checked"))
    $("#lyFilt").css("display", "block");
  else
    $("#lyFilt").css("display", "none");
}

function getMunis(idD,sel,tip){ 
  //funcion para obtener el listado de municipios dado un id de departamento
  //idD : Id del departamento para el que se mostraran los municipios
  //sel: Id del municipio que debe aparecer seleccionado., si lo hay.
  //tip: 1: para ser usado en formulario, 2: para ser usado en reporte  
  if(idD!=0){
    $('#lyMuni').attr('innerHTML', '<span class="credit">cargando municipios...</span>');
    $.ajax({
      type: "POST",
      url: "ajax/app_ajax.php",
      data: "Do=getMunis&ID="+idD+"&Sel="+sel+"&Tip="+tip,
      success: function(rslt){
        $('#lyMuni').attr('innerHTML', rslt);
      }
    });
  } else{
    $('#lsMuni').attr("value",0);
  }
}

function getSelDepto(idZ,sel,tip){
  //funcion para obtener los departamentos dado un id de zona
  //idZ: id de zona
  //sel: Id del deparatamento que debe aparecer seleccionado
  //tip: 1: para ser usado en formulario, 2: para ser usado en reporte
  if(idZ!=0){
    $('#lyDepto').attr('innerHTML', '<span class="credit">cargando departamentos...</span>');
    $.ajax({
      type: "POST",
      url: "ajax/app_ajax.php",
      data: "Do=getSelDepto&ID="+idZ+"&Sel="+sel+"&Tip="+tip,
      success: function(rslt){
        $('#lyDepto').attr('innerHTML', rslt);
      }
    });
  } else{
    $('#lsDepto').attr("value",0)
  }
}

function crearDiagYN(titulo,msg,tipomodal,alto,ancho,funcYes){
	$("#txtMsgDiag").attr("innerHTML",msg);
  $("#dialog-confirm").css("display","block");
 	$("#dialog-confirm").attr("title",titulo);
	$("#dialog-confirm").dialog

 ({
    resizable: false,
    height:alto,
    width: ancho,
    modal: tipomodal,
    buttons: {
      "Yes": function() {
          $( this ).dialog( "close" );
          eval(funcYes);
      },
      "No": function(){
          $( this ).dialog( "close" );
      }
    }
  });
}

function crearDiagOK(titulo,msg,tipomodal,alto,ancho,funcOK){
  $( "#txtMsgDiagOK" ).attr("innerHTML", msg);
  $( "#dialog-ok" ).attr("title", titulo);
  $( "#dialog-ok" ).dialog({
    resizable: true,
    height:alto,
    width: ancho,
    modal: tipomodal,
    buttons: {
      "Accept": function() {
          $( this ).dialog( "close" );
          eval(funcOK);
      }
    }
  });
}

function crearDiagLoad(titulo,msg,tipomodal,alto,ancho){
   $( "#txtMsgDiagLoad" ).attr("innerHTML", msg);
   $( "#dialog-load" ).attr("title", titulo);
   $( "#dialog-load" ).dialog({
    resizable: true,
    height:alto,
    width: ancho,
    modal: tipomodal,
    buttons: {}
  });
}

function crearDiagShow(titulo,msg,tipomodal,alto,ancho){
   $( "#txtMsgDiagDatos" ).attr("innerHTML", msg);
   $( "#dialog-show" ).attr("title", titulo);
   $( "#dialog-show" ).dialog({
    resizable: true,
    height:alto,
    width: ancho,
    modal: tipomodal,
    buttons: {}
  });
}

function closeShow(){ //cerrar el cuadro de dialogo para mostrar información
 $( "#dialog-show" ).dialog("close");
}

function getCld(idF){ 
  $("#"+idF).datepicker();
}