<?php
  //Procedimientos en PHP para la parte de Tests
  //llamados a través de AJAX.  
  header("Cache-Control: no-cache, must-revalidate" ); 
  header("Pragma: no-cache" );


  require_once("../db_funcs.php");
  $dbEx = new DBX;


 
  switch($_POST['Do']){	
      	  
	//////////////////////////// 
////////////UNIVERSIDADES///////////////	 
	//////////////////////////// 
	
    case 'getUnivs': //obtenemos el listado de Universidades
	  $tamPag = 10;
	  $initPag = ($_POST['Pag'] - 1) * $tamPag;	  
	  $rslt = '';
	  $sqlText = "select * from wsi_universidades";
	  $dtUnivT = $dbEx->selSql($sqlText);
	  $totalRows = $dbEx->numrows;
	  $totPags = ceil($totalRows / $tamPag);
	  
	  if($totalRows > 0){
	    $sqlText = "select * from wsi_universidades order by univ_nombre LIMIT $initPag,$tamPag";
		$dtUniv = $dbEx->selSql($sqlText);
	    $rslt .= '<table cellpadding="0" cellspacing="0" width="600" border="1" bordercolor="#E6E6E6">';
	    $rslt .= '<tr class="backTablaForm" align="center" height="20"><td width="440">UNIVERSIDAD</td><td>--</td></tr>';
		$rslt .= '<tr><td colspan="2" bgcolor="#FFFFFF"></td></tr>';
		foreach($dtUniv as $dr){
	     $rslt .= '<tr class="trList" >'.
		    '<td><span style="width:440px;">'.$dr['univ_nombre'].'</span></td><td align="center"><a href="javascript: editUniv('.$dr['univ_id'].')"><img src="../images/edit.jpg" border="0" alt="editar universidad" /></a>&nbsp;&nbsp;<a href="javascript: delUniv('.$dr['univ_id'].')"><img src="../images/delete.jpg" border="0" alt="eliminar universidad" /></a></td></tr>';
	    }
		$rslt .= '</table>';
		
		$rslt .= '|-|';
	    //hacemos la paginación
	    $pagAct = "Pag. ".$_POST['Pag']." de ".$totPags;
	    $pagAnt = $_POST['Pag'] - 1;
	    $pagNxt = $_POST['Pag'] + 1;
	    $btnIr = '<input type="text" size="3" style="width:20px; height:14px;" id="txtNumPag" />&nbsp;<input type="button" value=" Ir " class="btn" style=" height:20px;" onclick="getUnivs(document.getElementById(\'txtNumPag\').value)"  />';
	    if($_POST['Pag'] > 1)
	      $nextBack = '<a href="javascript: getUnivs('.$pagAnt.')"><img src="../../ginesys/images/backp.jpg" alt="p&aacute;gina anterior" border="0" /></a>&nbsp;&nbsp;';
	    if($_POST['Pag'] < $totPags)
	      $nextBack .= '<a href="javascript: getUnivs('.$pagNxt.')"><img src="../../ginesys/images/nextp.jpg" alt="p&aacute;gina siguiente" border="0" /></a>';
		
	     $rslt .= '<table cellpadding="0" cellspacing="0" width="620">';
	     $rslt .= '<tr class="numPags"><td style="padding-left:10px;" width="100">'.$pagAct.'</td><td align="center" width="400">'.$btnIr.'</td><td>'.   $nextBack.'</td></tr>';
	     $rslt .= '</table>';
	  
	  } else{
	    $rslt .= '<span class="alertMsg">No se encontraron Universidades</span>|-|<span></span>';
	  }	  	   
	  	   
	  echo $rslt;
	  break;
	  
	case 'saveUniv': //proceso para guardar una Universidad
	  $sqlText = "insert into wsi_universidades (univ_nombre, univ_direccion) values ".
	    "('".$dbEx->rmvSpecialChar($_POST['Nombre'])."','".$dbEx->rmvSpecialChar($_POST['Direcc'])."')";
		
	  $dbEx->insSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
	case 'delUniv': //proceso para eliminar Universidad
	  $sqlText = "delete from wsi_universidades where univ_id=".$_POST['ID'];
		
	  $dbEx->updSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
	case 'editUniv': //proceso para extraer los datos de una universidad
	  $sqlText = "select * from wsi_universidades where univ_id=".$_POST['ID'];
		
	  $dtU = $dbEx->selSql($sqlText);	  
	  $rslt = $dtU['0']['univ_nombre'].'|-|'.$dtU['0']['univ_direccion'];	
	  echo $rslt;
	  break;
	
	case 'actUniv': //proceso para actualizar una Universidad
	  $sqlText = "update wsi_universidades set univ_nombre='".$dbEx->rmvSpecialChar($_POST['Nombre'])."', univ_direccion='".$dbEx->rmvSpecialChar($_POST['Direcc'])."' ".
	    " where univ_id=".$_POST['idU'];
		
	  $dbEx->updSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;	  	
 
	  
	//////////////////////////// 
////////////CARRERAS//////////////	 
	//////////////////////////// 	 
	 
	  
    case 'getCarrs': //obtenemos el listado de Carreras
	  $tamPag = 12;
	  $initPag = ($_POST['Pag'] - 1) * $tamPag;	  
	  $rslt = '';
	  $sqlText = "select * from wsi_carreras";
	  $dtCarrT = $dbEx->selSql($sqlText);
	  $totalRows = $dbEx->numrows;
	  $totPags = ceil($totalRows / $tamPag);
	  
	  if($totalRows > 0){
	    $sqlText = "select * from wsi_carreras order by carr_nombre LIMIT $initPag,$tamPag";
		$dtCarrs = $dbEx->selSql($sqlText);
	    $rslt .= '<table cellpadding="0" cellspacing="0" width="600" border="1" bordercolor="#E6E6E6">';
	    $rslt .= '<tr class="backTablaForm" align="center" height="20"><td width="440">CARRERA</td><td>--</td></tr>';
		$rslt .= '<tr><td colspan="2" bgcolor="#FFFFFF"></td></tr>';
		foreach($dtCarrs as $dr){
	     $rslt .= '<tr class="trList" >'.
		    '<td><span style="width:440px;">'.$dr['carr_nombre'].'</span></td><td align="center"><a href="javascript: editCarr('.$dr['carr_id'].')"><img src="../images/edit.jpg" border="0" alt="editar carrera" /></a>&nbsp;&nbsp;<a href="javascript: delCarr('.$dr['carr_id'].')"><img src="../images/delete.jpg" border="0" alt="eliminar carrera" /></a></td></tr>';
	    }
		$rslt .= '</table>';
		
		$rslt .= '|-|';
	    //hacemos la paginación
	    $pagAct = "Pag. ".$_POST['Pag']." de ".$totPags;
	    $pagAnt = $_POST['Pag'] - 1;
	    $pagNxt = $_POST['Pag'] + 1;
	    $btnIr = '<input type="text" size="3" style="width:20px; height:14px;" id="txtNumPag" />&nbsp;<input type="button" value=" Ir " class="btn" style=" height:20px;" onclick="getCarrs(document.getElementById(\'txtNumPag\').value)"  />';
	    if($_POST['Pag'] > 1)
	      $nextBack = '<a href="javascript: getCarrs('.$pagAnt.')"><img src="../../ginesys/images/backp.jpg" alt="p&aacute;gina anterior" border="0" /></a>&nbsp;&nbsp;';
	    if($_POST['Pag'] < $totPags)
	      $nextBack .= '<a href="javascript: getCarrs('.$pagNxt.')"><img src="../../ginesys/images/nextp.jpg" alt="p&aacute;gina siguiente" border="0" /></a>';
		
	     $rslt .= '<table cellpadding="0" cellspacing="0" width="620">';
	     $rslt .= '<tr class="numPags"><td style="padding-left:10px;" width="100">'.$pagAct.'</td><td align="center" width="400">'.$btnIr.'</td><td>'.   $nextBack.'</td></tr>';
	     $rslt .= '</table>';
	  
	  } else{
	    $rslt .= '<span class="alertMsg">No se encontraron Carreras</span>|-|<span></span>';
	  }	  	   
	  	   
	  echo $rslt;
	  break;

	case 'saveCarr': //proceso para guardar una Carrera
	  $sqlText = "insert into wsi_carreras (carr_nombre, carr_descrip) values ".
	    "('".$dbEx->rmvSpecialChar($_POST['Nombre'])."','".$dbEx->rmvSpecialChar($_POST['Desc'])."')";
		
	  $dbEx->insSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
    case 'delCarr': //proceso para eliminar Carrera
	  $sqlText = "delete from wsi_carreras where carr_id=".$_POST['ID'];
		
	  $dbEx->updSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
	case 'editCarr': //proceso para extraer los datos de una carrera
	  $sqlText = "select * from wsi_carreras where carr_id=".$_POST['ID'];
		
	  $dtC = $dbEx->selSql($sqlText);	  
	  $rslt = $dtC['0']['carr_nombre'].'|-|'.$dtC['0']['carr_descrip'];	
	  echo $rslt;
	  break;
	
	case 'actCarr': //proceso para actualizar una Carrera
	  $sqlText = "update wsi_carreras set carr_nombre='".$dbEx->rmvSpecialChar($_POST['Nombre'])."', carr_descrip='".$dbEx->rmvSpecialChar($_POST['Desc'])."' ".
	    " where carr_id=".$_POST['idC'];
		
	  $dbEx->updSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
		//////////////////////////// 
////////////CONCURSOS//////////////	 
	//////////////////////////// 
	  
	case 'getCons': //obtenemos el listado de concursos
	  $tamPag = 15;
	  $initPag = ($_POST['Pag'] - 1) * $tamPag;	  
	  if($_POST['Est']==0)
	    $filtro = "";
	  else 	
	    $filtro = " where con_estatus=".$_POST['Est'];
		
	  $rslt = '';
	  $sqlText = "select * from wsi_concursos ".$filtro;
	  $dtConT = $dbEx->selSql($sqlText);
	  $totalRows = $dbEx->numrows;
	  $totPags = ceil($totalRows / $tamPag);
	  
	  if($totalRows > 0){
	    $sqlText = "select *, DATE_FORMAT(con_fechafin,'%d/%m/%Y') as fechF from wsi_concursos ".$filtro." order by con_id desc LIMIT $initPag,$tamPag";
		$dtCon = $dbEx->selSql($sqlText);
	    $rslt .= '<table cellpadding="0" cellspacing="0" width="600" border="1" bordercolor="#E6E6E6">';
	    $rslt .= '<tr class="backTablaForm" align="center" height="20"><td width="400">CONCURSO</td><td width="125">Fecha Fin.</td><td>--</td></tr>';
		$rslt .= '<tr><td colspan="3" bgcolor="#FFFFFF"></td></tr>';
		foreach($dtCon as $dr){
	     $rslt .= '<tr class="trList" >'.
		    '<td>'.$dr['con_nombre'].'</td><td style="padding-left:30px;">'.$dr['fechF'].'</td><td align="center"><a href="javascript: editCon('.$dr['con_id'].')"><img src="../images/edit.jpg" border="0" alt="editar concurso" /></a>&nbsp;&nbsp;<a href="javascript: delCon('.$dr['con_id'].')"><img src="../images/delete.jpg" border="0" alt="eliminar concurso" /></a></td></tr>';
	    }
		$rslt .= '</table>';
		
		$rslt .= '|-|';
	    //hacemos la paginación
	    $pagAct = "Pag. ".$_POST['Pag']." de ".$totPags;
	    $pagAnt = $_POST['Pag'] - 1;
	    $pagNxt = $_POST['Pag'] + 1;
	    $btnIr = '<input type="text" size="3" style="width:20px; height:14px;" id="txtNumPag" />&nbsp;<input type="button" value=" Ir " class="btn" style=" height:20px;" onclick="getCons(document.getElementById(\'txtNumPag\').value)"  />';
	    if($_POST['Pag'] > 1)
	      $nextBack = '<a href="javascript: getCons('.$pagAnt.')"><img src="../../ginesys/images/backp.jpg" alt="p&aacute;gina anterior" border="0" /></a>&nbsp;&nbsp;';
	    if($_POST['Pag'] < $totPags)
	      $nextBack .= '<a href="javascript: getCons('.$pagNxt.')"><img src="../../ginesys/images/nextp.jpg" alt="p&aacute;gina siguiente" border="0" /></a>';
		
	     $rslt .= '<table cellpadding="0" cellspacing="0" width="620">';
	     $rslt .= '<tr class="numPags"><td style="padding-left:10px;" width="100">'.$pagAct.'</td><td align="center" width="400">'.$btnIr.'</td><td>'.   $nextBack.'</td></tr>';
	     $rslt .= '</table>';
	  
	  } else{
	    $rslt .= '<span class="alertMsg">No se encontraron concursos...</span>|-|<span></span>';
	  }	  	   
	  	   
	  echo $rslt;
	  break;	  	
	  
	case 'delCon': //proceso para eliminar concurso
	  //borramos el archivo
	  $sqlText = "select con_doc from wsi_concursos where con_id=".$_POST['ID'];
	  $dtArch = $dbEx->selSql($sqlText);
	  if(strlen($dtArch['0']['con_doc'])>4)
	    unlink($uploaddir.$dtArch['0']['con_doc']); //borramos el archivo
		  
	  $sqlText = "delete from wsi_concursos where con_id=".$_POST['ID'];		
	  $dbEx->updSql($sqlText);
	  $rslt = 2;	
	  
	  echo $rslt;
	  break;
	  
	case 'editCon': //proceso para extraer los datos de un concurso
	  $sqlText = "select *, date_format(con_fechaini,'%d/%m/%Y') as f1, date_format(con_fechafin,'%d/%m/%Y') as f2 from wsi_concursos where con_id=".$_POST['ID'];
		
	  $dtC = $dbEx->selSql($sqlText);	  
	  $rslt = $dtC['0']['con_nombre'].'|-|'.$dtC['0']['con_descrip'].'|-|'.$dtC['0']['con_precio'].'|-|'.$dtC['0']['f1'].'|-|'.$dtC['0']['f2'].'|-|'.$dtC['0']['con_estatus'];
	  	
	  echo $rslt;
	  break;
	
	case 'actProy': //proceso para actualizar un proyecto
	  $sqlText = "update wsi_proyectos set proy_nombre='".$_POST['Nombre']."', proy_direccion='".$_POST['Direcc']."' ".
	    " where proy_id=".$_POST['idU'];
		
	  $dbEx->updSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
	case 'getStuds': //obtenemos el listado de Estudiantes
	  $tamPag = 15;
	  $initPag = ($_POST['Pag'] - 1) * $tamPag;	  	  
	  $rslt = '';
	  $sqlText = "select * from wsi_usuarios where usr_tipo=2 and usr_estatus=".$_POST['Estatus'];
	  $dtUnivT = $dbEx->selSql($sqlText);
	  $totalRows = $dbEx->numrows;
	  $totPags = ceil($totalRows / $tamPag);
	  
	  if($totalRows > 0){
	    $sqlText = "select * from wsi_usuarios where usr_tipo=2 and usr_estatus=".$_POST['Estatus']." order by usr_nombre LIMIT $initPag,$tamPag";
		$dtStud = $dbEx->selSql($sqlText);		
	    $rslt .= '<table cellpadding="0" cellspacing="0" width="630" border="1" bordercolor="#E6E6E6">';
	    $rslt .= '<tr class="backTablaForm" align="center" height="20" width="80%"><td>ESTUDIANTE</td><td>--</td></tr>';
		$rslt .= '<tr><td colspan="2" bgcolor="#FFFFFF"></td></tr>';
		foreach($dtStud as $dr){
		  if($_POST['Estatus']==2){
		    $funct = '<a href="javascript: desact('.$dr['usr_id'].')" class="aRed">desactivar</a>';
		  } else{
		    $funct = '<a href="javascript: activ('.$dr['usr_id'].')" class="aBlue">activar</a>';
		  }
		  
	     $rslt .= '<tr class="trList" >'.
		    '<td onMouseOver="this.className=\'trListRoll\'" onMouseOut="this.className=\'trList\'" style="cursor:pointer;" onclick="getDS('.$dr['usr_id'].')"><span style="width:440px;">'.$dr['usr_nombre'].'</span></td><td align="center">'.$funct.'</td></tr>';
	    }
		$rslt .= '</table>';
		
		$rslt .= '|-|';
	    //hacemos la paginación
	    $pagAct = "Pag. ".$_POST['Pag']." de ".$totPags;
	    $pagAnt = $_POST['Pag'] - 1;
	    $pagNxt = $_POST['Pag'] + 1;
	    $btnIr = '<input type="text" size="3" style="width:20px; height:14px;" id="txtNumPag" />&nbsp;<input type="button" value=" Ir " class="btn" style=" height:20px;" onclick="getStuds('.$_POST['Estatus'].',document.getElementById(\'txtNumPag\').value)"  />';
	    if($_POST['Pag'] > 1)
	      $nextBack = '<a href="javascript: getStuds('.$_POST['Estatus'].','.$pagAnt.')"><img src="../../ginesys/images/backp.jpg" alt="p&aacute;gina anterior" border="0" /></a>&nbsp;&nbsp;';
	    if($_POST['Pag'] < $totPags)
	      $nextBack .= '<a href="javascript: getStuds('.$_POST['Estatus'].','.$pagNxt.')"><img src="../../ginesys/images/nextp.jpg" alt="p&aacute;gina siguiente" border="0" /></a>';
		
	     $rslt .= '<table cellpadding="0" cellspacing="0" width="620">';
	     $rslt .= '<tr class="numPags"><td style="padding-left:10px;" width="100">'.$pagAct.'</td><td align="center" width="400">'.$btnIr.'</td><td>'.   $nextBack.'</td></tr>';
	     $rslt .= '</table>';
	  
	  } else{
	    $rslt .= '<span class="alertMsg">No se encontraron Estudiantes...</span>|-|<span></span>';
	  }	  	   
	  	   
	  echo $rslt;
	  break;
	  
  case 'activ'://Activamos a un estudiante
     
	 $sqlText = "update wsi_usuarios set usr_estatus=2 where usr_id=".$_POST['IdS'];
	 $dbEx->updSql($sqlText);
	 //Enviamos correo al usuario para avisar que esta activado
	 $sqlText = "select usr_nombre, usr_correo from wsi_usuarios where usr_id=".$_POST['IdS'];
	 $dtUsr = $dbEx->selSql($sqlText);
	 
	 $cabeceras .= "Content-type: text/html; charset=iso-8859-1  \r\n";
	 $cabeceras .=  "From: White Studio Inc. <jpchinchilla@jpchost.com> \r\n";
	 $cabeceras .= "Reply-To: jpchinchilla@jpchost.com \r\n";
	$cabeceras .= "Bcc: jpchinchilla@hotmail.com \r\n";
	$mensaje = 'Hola, '.$dtUsr['0']['usr_nombre'].'<br><br> Tu cuenta en <a href="http://www.jpchost.com/white/participantes.php">White Studio Inc</a> ha sido activada. Ya puedes participar en los diferentes proyectos que tenemos en nuestro Web Site<br><br>'.
		   'Para poder ver los proyectos actuales y comenzar a postear tus propuestas haz click <a href="http://www.jpchost.com/white/participantes.php">aqui</a>';
		   
		mail($_POST['Email'],"Cuenta de usuario activada en White Studio Inc. ",$mensaje,$cabeceras);
	 
	 
	 $rslt=2;
	 echo $rslt;
	 break;
	 
  case 'desact'://Activamos a un estudiante
     
	 $sqlText = "update wsi_usuarios set usr_estatus=100 where usr_id=".$_POST['IdS'];
	 $dbEx->updSql($sqlText);	 
	 
	 $rslt=2;
	 
	 echo $rslt;
	 break;
	 
  case 'getDS'://obtenemos los datos de un estudiante
	$sqlText = "select u.*, uv.univ_nombre, c.carr_nombre ".
		" from wsi_usuarios u ".
		" inner join wsi_universidades uv on uv.univ_id=u.univ_id ".
		" inner join wsi_carreras c on c.carr_id=u.carr_id ".
		" where usr_id=".$_POST['IdS'];
		
	$dtUs = $dbEx->selSql($sqlText);
	
	//Armamos la tabla
	$rslt = '<table bgcolor="#FFFFFF" border="1" bordercolor="#000000">'.
	  '<tr><td style="padding:7px;">'.
	    '<table cellpadding="0" cellspacing="0" width="450">'.
		  '<tr><td class="numPags" align="left" width="100">Nombre: </td><td class="trList" align="left">'.$dtUs['0']['usr_nombre'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">Tel. principal: </td><td class="trList" align="left">'.$dtUs['0']['usr_telefono'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">Tel. celular: </td><td class="trList" align="left">'.$dtUs['0']['usr_celular'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">E-mail: </td><td class="trList" align="left">'.$dtUs['0']['usr_correo'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">DUI: </td><td class="trList" align="left">'.$dtUs['0']['usr_dui'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">NIT: </td><td class="trList" align="left">'.$dtUs['0']['usr_nit'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">Carnet: </td><td class="trList" align="left">'.$dtUs['0']['usr_carnet'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">Universidad: </td><td class="trList" align="left">'.$dtUs['0']['univ_nombre'].'</td></tr>'.
		  '<tr><td colspan="2" height="7"></td></tr>'.
		  '<tr><td class="numPags" align="left" width="100">Carrera: </td><td class="trList" align="left">'.$dtUs['0']['carr_nombre'].'</td></tr>'.
		  '<tr><td colspan="2" height="20"></td></tr>'.
		  '<tr><td colspan="2" align="right"><a class="aRed" href="javascript: closeDat();">[X] CERRAR</a></td></tr>'.
		'</table>'.
	  '</td></tr>'.
	 '</table>';
		  
    echo $rslt;
	break;
	
	case 'getJudges': //obtenemos el listado de Jueces
	  $tamPag = 10;
	  $initPag = ($_POST['Pag'] - 1) * $tamPag;	  
	  $rslt = '';
	  $sqlText = "select * from wsi_usuarios where usr_tipo=1";
	  $dtJuezT = $dbEx->selSql($sqlText);
	  $totalRows = $dbEx->numrows;
	  $totPags = ceil($totalRows / $tamPag);
	  
	  if($totalRows > 0){
	    $sqlText = "select * from wsi_usuarios where usr_tipo=1 order by usr_nombre LIMIT $initPag,$tamPag";
		$dtJuez = $dbEx->selSql($sqlText);
	    $rslt .= '<table cellpadding="0" cellspacing="0" width="600" border="1" bordercolor="#E6E6E6">';
	    $rslt .= '<tr class="backTablaForm" align="center" height="20"><td width="440">NOMBRE</td><td>--</td></tr>';
		$rslt .= '<tr><td colspan="2" bgcolor="#FFFFFF"></td></tr>';
		foreach($dtJuez as $dr){
	     $rslt .= '<tr class="trList" >'.
		    '<td><span style="width:440px;">'.$dr['usr_nombre'].'</span></td><td align="center"><a href="javascript: editJudge('.$dr['usr_id'].')"><img src="../images/edit.jpg" border="0" alt="editar juez" /></a>&nbsp;&nbsp;<a href="javascript: delJudge('.$dr['usr_id'].')"><img src="../images/delete.jpg" border="0" alt="eliminar juez" /></a></td></tr>';
	    }
		$rslt .= '</table>';
		
		$rslt .= '|-|';
	    //hacemos la paginación
	    $pagAct = "Pag. ".$_POST['Pag']." de ".$totPags;
	    $pagAnt = $_POST['Pag'] - 1;
	    $pagNxt = $_POST['Pag'] + 1;
	    $btnIr = '<input type="text" size="3" style="width:20px; height:14px;" id="txtNumPag" />&nbsp;<input type="button" value=" Ir " class="btn" style=" height:20px;" onclick="getUnivs(document.getElementById(\'txtNumPag\').value)"  />';
	    if($_POST['Pag'] > 1)
	      $nextBack = '<a href="javascript: getJudges('.$pagAnt.')"><img src="../../ginesys/images/backp.jpg" alt="p&aacute;gina anterior" border="0" /></a>&nbsp;&nbsp;';
	    if($_POST['Pag'] < $totPags)
	      $nextBack .= '<a href="javascript: getJudges('.$pagNxt.')"><img src="../../ginesys/images/nextp.jpg" alt="p&aacute;gina siguiente" border="0" /></a>';
		
	     $rslt .= '<table cellpadding="0" cellspacing="0" width="620">';
	     $rslt .= '<tr class="numPags"><td style="padding-left:10px;" width="100">'.$pagAct.'</td><td align="center" width="400">'.$btnIr.'</td><td>'.   $nextBack.'</td></tr>';
	     $rslt .= '</table>';
	  
	  } else{
	    $rslt .= '<span class="alertMsg">No se encontraron Jueces</span>|-|<span></span>';
	  }	  	   
	  	   
	  echo $rslt;
	  break;
	  
  case 'saveJudge':// guardar un juez
    
	$sqlText = "insert into wsi_usuarios(usr_nombre, usr_telefono, usr_celular, usr_dui, usr_nit, usr_correo, usr_password, usr_tipo, usr_estatus, univ_id, carr_id) values('".$_POST['Nombre']."', '".$_POST['Tel']."', '".$_POST['Cel']."', '".$_POST['Dui']."', '".$_POST['Nit']."', '".$_POST['Email']."', '".$_POST['Pwd']."', 1,".$_POST['Est'].", ".$_POST['Univ'].", ".$_POST['Carr'].")";
	
	$dbEx->insSql($sqlText);
	$rslt = 2;
	
	echo $rslt;
	break;
	
  case 'delJudge':// borramos registro de juez
    
	$sqlText = "delete from wsi_usuarios where usr_id=".$_POST['ID'];
	$dbEx->updSql($sqlText);
	
	$rslt = 2;
	
	echo $rslt;
	break;
	
  case 'editJudge'://Extraemos los datos para editar el juez    
	 $sqlText = "select * from wsi_usuarios where usr_id=".$_POST['ID'];
	 $dtJ = $dbEx->selSql($sqlText);
	 
	 $rslt = $dtJ['0']['usr_nombre'].'|-|'.$dtJ['0']['usr_nit'].'|-|'.$dtJ['0']['usr_dui'].'|-|'.$dtJ['0']['usr_telefono'].'|-|'.$dtJ['0']['usr_celular'].'|-|'.$dtJ['0']['usr_correo'].'|-|'.$dtJ['0']['usr_password'].'|-|'.$dtJ['0']['usr_estatus'].'|-|'.$dtJ['0']['univ_id'].'|-|'.$dtJ['0']['carr_id'];
	 
	 echo $rslt;
	 break;
	 
  case 'actJudge'://Actualizamos
	 
	 $sqlText = "update wsi_usuarios set usr_nombre='".$_POST['Nombre']."', usr_telefono='".$_POST['Tel']."', usr_celular='".$_POST['Cel']."', usr_dui='".$_POST['Dui']."', usr_nit='".$_POST['Nit']."', usr_correo='".$_POST['Email']."', usr_password='".$_POST['Pwd']."',  usr_estatus=".$_POST['Est'].", univ_id=".$_POST['Univ'].", carr_id=".$_POST['Carr']." where usr_id=".$_POST['idJ'];
	 
	 $dbEx->updSql($sqlText);
	 
	 $rslt=2;
	 echo $rslt;
	 break;
	 
//////////////////////////// 
////////////ITEMS A EVALUAR///////////////	 
	//////////////////////////// 
	
    case 'getItems': //obtenemos el listado de Items a evaluar
	  $tamPag = 12;
	  $initPag = ($_POST['Pag'] - 1) * $tamPag;	  
	  $rslt = '';
	  $sqlText = "select * from wsi_item";
	  $dtItemT = $dbEx->selSql($sqlText);
	  $totalRows = $dbEx->numrows;
	  $totPags = ceil($totalRows / $tamPag);
	  
	  if($totalRows > 0){
	    $sqlText = "select * from wsi_item order by item_nombre LIMIT $initPag,$tamPag";
		$dtItem = $dbEx->selSql($sqlText);
	    $rslt .= '<table cellpadding="0" cellspacing="0" width="600" border="1" bordercolor="#E6E6E6">';
	    $rslt .= '<tr class="backTablaForm" align="center" height="20"><td width="440">ITEMS A EVALUAR</td><td>--</td></tr>';
		$rslt .= '<tr><td colspan="2" bgcolor="#FFFFFF"></td></tr>';
		foreach($dtItem as $dr){
	     $rslt .= '<tr class="trList" >'.
		    '<td><span style="width:440px;">'.$dr['item_nombre'].'</span></td><td align="center"><a href="javascript: editItem('.$dr['item_id'].')"><img src="../images/edit.jpg" border="0" alt="editar item" /></a>&nbsp;&nbsp;<a href="javascript: delItem('.$dr['item_id'].')"><img src="../images/delete.jpg" border="0" alt="eliminar item" /></a></td></tr>';
	    }
		$rslt .= '</table>';
		
		$rslt .= '|-|';
	    //hacemos la paginación
	    $pagAct = "Pag. ".$_POST['Pag']." de ".$totPags;
	    $pagAnt = $_POST['Pag'] - 1;
	    $pagNxt = $_POST['Pag'] + 1;
	    $btnIr = '<input type="text" size="3" style="width:20px; height:14px;" id="txtNumPag" />&nbsp;<input type="button" value=" Ir " class="btn" style=" height:20px;" onclick="getItems(document.getElementById(\'txtNumPag\').value)"  />';
	    if($_POST['Pag'] > 1)
	      $nextBack = '<a href="javascript: getItems('.$pagAnt.')"><img src="../../ginesys/images/backp.jpg" alt="p&aacute;gina anterior" border="0" /></a>&nbsp;&nbsp;';
	    if($_POST['Pag'] < $totPags)
	      $nextBack .= '<a href="javascript: getItems('.$pagNxt.')"><img src="../../ginesys/images/nextp.jpg" alt="p&aacute;gina siguiente" border="0" /></a>';
		
	     $rslt .= '<table cellpadding="0" cellspacing="0" width="620">';
	     $rslt .= '<tr class="numPags"><td style="padding-left:10px;" width="100">'.$pagAct.'</td><td align="center" width="400">'.$btnIr.'</td><td>'.   $nextBack.'</td></tr>';
	     $rslt .= '</table>';
	  
	  } else{
	    $rslt .= '<span class="alertMsg">No se encontraron Items</span>|-|<span></span>';
	  }	  	   
	  	   
	  echo $rslt;
	  break;
	  
	case 'saveItem': //proceso para guardar un Item de evaluación
	  $sqlText = "insert into wsi_item (item_nombre) values ".
	    "('".$dbEx->rmvSpecialChar($_POST['Nombre'])."')";
		
	  $dbEx->insSql($sqlText);
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
	case 'editItem': //proceso para extraer los datos de un item de evaluacion
	  $sqlText = "select * from wsi_item where item_id=".$_POST['ID'];
		
	  $dtI = $dbEx->selSql($sqlText);	  
	  $rslt = $dtI['0']['item_nombre'];	
	  
	  echo $rslt;
	  
	  break;
	  
	case 'actItem': //proceso para actualizar un Item
	  $sqlText = "update wsi_item set item_nombre='".$dbEx->rmvSpecialChar($_POST['Nombre'])."' where item_id=".$_POST['idI'];		
	  $dbEx->updSql($sqlText);
	  
	  $rslt = 2;	
	  echo $rslt;
	  break;
	  
	case 'delItem': //proceso para eliminar un Item
	  $sqlText = "delete from wsi_item where item_id=".$_POST['ID'];		
	  $dbEx->updSql($sqlText);
	  
	  $rslt = 2;	
	  echo $rslt;
	  break;
	
  default: 
	  echo '<div class="alertMsg" align="center">El proceso no existe a&uacute;n!</div>';
	  break;
}

	  