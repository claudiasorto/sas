<?php

header("Content-Type: text/html; charset=utf-8");

require_once("../db_funcs.php");
$dbEx = new DBX;

switch($_POST['Do']){	 	  
  case 'inApp'://ingreso al sistema
    $rslt = 0;
    //comprobamos que el usuario exista.
      $sqlText = "select * from employees where username='".$_POST['Login']."'";
      $dtUs = $dbEx->selSql($sqlText);
      if($dbEx->numrows > 0){ //El usuario existe
        //comprobamos que el password sea el correcto
        if(md5($_POST['Clave'])==$dtUs['0']['USER_PWD']){
          //comprobamos que el usuario se encuentre activo
          if($dtUs['0']['USER_STATUS']==1){
            $rslt = 2; //Validación satisfactoria
            //creamos variables de sesion relativas al usuario
            //session_register("usr_id","usr_nick","usr_nombre","usr_tipo","logged_app");
            $_SESSION["usr_id"] = $dtUs['0']['EMPLOYEE_ID'];
            $_SESSION["usr_nick"] = $dtUs['0']['USERNAME'];
            $_SESSION["usr_nombre"] = $dtUs['0']['FIRSTNAME'];
            $_SESSION["usr_rol"] = $dtUs['0']['ID_ROLE'];
			$_SESSION["usr_sup"] = $dtUs['0']['ID_SUPERVISOR'];
            $_SESSION["logged_app"] = 1;
            //agregamos el registro en la tabla de log
            //$sqlText = "insert into ADM_LOGS";
          } else{
            $rslt = -3; //usuario inactivo
          }
        } else{
          $rslt = -2; //Password incorrecto
        }
      } else{
        $rslt = -1; //El usuario no existe
      }

    echo $rslt;
  break;

  case 'logOut': //cerramos aplicación
    $rslt = 1;
    $_SESSION["usr_id"] = 0;
    $_SESSION["usr_nick"] = 0;
    $_SESSION["usr_nombre"] = 0;
    $_SESSION["usr_rol"] = 0;
	$_SESSION["usr_sup"] = 0;
    $_SESSION["logged_app"] = 0;
    session_destroy();
    echo $rslt;
  break;

  case 'usr_savePwd': //Cambia el password del usuario
   //comprobamos q el password actual sea el correcto
    $sqlText = "select * from users_exc where user_id=".$_SESSION['usr_id']." and user_pwd='".md5($_POST['ActP'])."'";
    $dtUs = $dbEx->selSql($sqlText);
    if($dbEx->numrows > 0){
         // cambiamos el password
      $sqlText = "update users_exc set user_pwd='".md5($_POST['NewP'])."' where user_id=".$_SESSION['usr_id'];
      $dbEx->updSql($sqlText);
      $rslt = '2';
    }else{
      $rslt = '-1';
    }

    echo $rslt;
  break;

  case 'usr_showList': //muestro el listado de usuarios
    $rslt = '';
    $filtro = '';    
    if($_POST['Tipo']!=0){
      $filtro = ' and t.tpu_id='.$_POST['Tipo'];
    }
 
    //seteamos los datos para paginación
    $registros = 25; //Registros a mostrar en la página
    if(isset($_POST['nP'])){$numP = $_POST['nP'];}
    else{$numP=1;}
    $inicio = ($numP - 1) * $registros;
    //obtenemos el total de registros
    $sqlText = "select u.*, t.tpu_nombre ".
       " from ort_users u ".
       " inner join ort_tipouser t on t.tpu_id=u.tpu_id ".
       " where 1 ".$filtro;
    $dtTotR = $dbEx->selSql($sqlText);
    $totalRegs = $dbEx->numrows;
    $totalPag = ceil($totalRegs / $registros);
   //--------------------------------------------------------------------------------------------
    
    $sqlText = "select u.*, t.tpu_nombre ".
        " from ort_users u ".
        " inner join ort_tipouser t on t.tpu_id=u.tpu_id ".
        " where 1 ".$filtro." LIMIT ". $inicio .",". $registros;
    $dtUsr = $dbEx->selSql($sqlText);
    $tblU = '<table cellpadding="0" cellspacing="0" width="100%">';
    if($dbEx->numrows > 0){
      $tblU .='<tr class="ui-state-active"><td>Nombre</td><td style="padding-right:15px;">Email</td><td>Login</td><td style="padding-right:15px;">Tipo</td><td>Estatus</td><td>--</td></tr>';
      $tblU .='<tr><td height="5"></td></tr>';
      foreach($dtUsr as $drU){
        if($drU['usr_status']==1){$status='Activo';}
        else{$status='Inactivo';}
        $tblU .='<tr id="trUsr_'.$drU['usr_id'].'" class="trList" onmouseover="this.className=\'trListRoll\'" onmouseout="this.className=\'trList\'">'.
          '<td style="padding-right:15px;">'.$dbEx->rmvChars($drU['usr_nombre']).'</td><td style="padding-right:15px;">'.$drU['usr_mail'].'</td><td style="padding-right:15px;">'.$drU['usr_login'].'</td>'.
          '<td style="padding-right:15px;">'.$drU['tpu_nombre'].'</td><td style="padding-right:15px;">'.$status.'</td>'.
                '<td><a href="javascript: usr_edit('.$drU['usr_id'].')" title="editar usuario"><img border="0" src="images/form_edit.png" /></a>'.
                '&nbsp;&nbsp;<a href="javascript: usr_del('.$drU['usr_id'].')" title="borrar usuario"><img border="0" src="images/delete.png" /></a></td></tr>';
        $tblU .='<tr><td height="3"></td></tr>';
      }
      $tblU .= '</table>';
      //colocamos los datos para la paginaci&oacute;n
      $linkPag = '';
      if($totalPag >= 1){
       $linkPag .= '<span class="txtPag">P&aacute;g. '.$numP.' de '.$totalPag.',</span>&nbsp;&nbsp;&nbsp;&nbsp;';
       $linkPag .= '<span class="txtPag">Ir a P&aacute;g.&nbsp;</span>'.
          '<input type="text" size="2" title="digite el n&uacute;mero de p&aacute;gina al que desea ir" id="txtIr">'.
          '&nbsp;<input type="button" value="Ir" class="btnIr" onclick="usr_showList($(\'#txtIr\').val());">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      }      
      if($numP > 1){
            $pl = $numP-1;
        $linkPag .= '&nbsp;&nbsp;<a href="javascript: usr_showList('. $pl.')" title="p&aacute;gina anterior">'.
            '<img border="0" src="images/izquierda.png" /></a>';
      }
      if($numP < $totalPag){
            $pl = $numP+1;
        $linkPag .= '&nbsp;&nbsp;<a href="javascript: usr_showList('.$pl.')" class="Add" title="p&aacute;gina siguiente">'.
            '<img border="0" src="images/derecha.png" /></a>';
      }     
    }else{
      $tblU = "<span class='credit'>No se encontraron Usuarios...</span>";
    }

    echo $tblU.'|-|'.$linkPag;
  break;

  case 'usr_save'://proceso para guardar un nuevo usuario.
     $sqlText = 'insert into ort_users  set ';
     $sqlText .= ' usr_nombre= "'.$_POST['Nombre'].'", ';
     $sqlText .= ' usr_mail= "'.$_POST['Email'].'", ';
     $sqlText .= ' usr_login= "'.$_POST['Login'].'", ';
     $sqlText .= ' usr_pwd= "'.md5($_POST['Clave']).'", ';
     $sqlText .= ' usr_status= '.$_POST['Estado'].', ';
     $sqlText .= ' tpu_id= '.$_POST['Tipo'];
     $dbEx->insSql($sqlText);
     if($dbEx->Error!=''){
       $rslt = $dbEx->Error;
     } else{
       //todo bien
       $rslt = $dbEx->insertID;
     }
     
     echo $rslt;
  break;

  case 'usr_borrar': //procedimiento para borrar un usuario
    $sqlText = "delete from ort_users where usr_id=".$_POST['ID'];
    $dbEx->updSql($sqlText);
    $rslt='2';
    
    echo $rslt;
  break;


  case 'usr_edit': //procedimiento para cargar datos de un usuario
    $rslt = cargaPag("../usr/usr_edit.php");
    //obtenemos los datos del usuario
    $sqlText = "select u.*
        from ort_users u
        where u.usr_id=".$_POST['ID'];
    $dtU = $dbEx->selSql($sqlText);

    $rslt = str_replace("<!--idU-->", $dtU['0']['usr_id'], $rslt);
    $rslt = str_replace("<!--optTipoU-->",  getTipoU($dbEx,$dtU['0']['tpu_id']) , $rslt);
    $rslt = str_replace("<!--usrNombre-->",  $dbEx->rmvChars($dtU['0']['usr_nombre']) , $rslt);
    $rslt = str_replace("<!--usrEmail-->",  $dtU['0']['usr_mail'] , $rslt);
    $rslt = str_replace("<!--usrLogin-->",  $dtU['0']['usr_login'] , $rslt);
    
    
    $js = '$("#lsStatus").attr("value",'.$dtU['0']['usr_status'].');$("#txtNombre").focus();';

    echo $rslt.'|-|'.$js;
  break;
    
  case 'usr_update'://proceso para actualizar datos de usuario.
     $sqlText = 'update ort_users  set ';
     $sqlText .= ' usr_nombre= "'.$_POST['Nombre'].'", ';
     $sqlText .= ' usr_mail= "'.$_POST['Email'].'", ';
     $sqlText .= ' usr_login= "'.$_POST['Login'].'", ';
     if(trim($_POST['Clave'])!=""){
       $sqlText .= ' usr_pwd= "'.md5($_POST['Clave']).'", ';
     }
     $sqlText .= ' usr_status= '.$_POST['Estado'].', ';
     $sqlText .= ' tpu_id= '.$_POST['Tipo'].' ';
     $sqlText .= ' Where usr_id='.$_POST['ID'];
     $dbEx->updSql($sqlText);
     if($dbEx->Error!=''){
       $rslt = $dbEx->Error;
     } else{
       //todo bien
       $rslt = $_POST['ID'];
     }

     echo $rslt;
  break;

  default:
    echo '<div class="trList" align="center">No se puede ejecutar la petici&oacute;n!</div>';
  break;
}

  
?>

