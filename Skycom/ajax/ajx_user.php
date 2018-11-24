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
			$_SESSION["usr_apellido"] = $dtUs['0']['LASTNAME'];
			$sqlText = "select name_role, pd.id_role, pd.id_depart from user_roles u inner join placexdep pd on u.id_role=pd.id_role inner join plazaxemp pe on pd.id_placexdep=pe.id_placexdep inner join employees e on pe.employee_id=e.employee_id where pe.status_plxemp='A' and e.employee_id=".$_SESSION['usr_id'];
			$dtAc = $dbEx->selSql($sqlText);
			$_SESSION["usr_idrol"] = $dtAc['0']['id_role'];
			$_SESSION["usr_rol"] = $dtAc['0']['name_role'];
			$_SESSION["usr_depart"] = $dtAc['0']['id_depart'];
			//$_SESSION["permisos"] = $dtAc['0']['permissions'];
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
  
  case 'chgPwd': //Actualiza el pw del usuario
  	//comprobamos que el password sea el correcto
	  $sqlText = "select employee_id from employees where employee_id=".$_SESSION["usr_id"]." and user_pwd='".md5($_POST['passAct'])."'";
	  $dtV = $dbEx->selSql($sqlText);
	  
	  if($dbEx->numrows>0){
	    //actualizamos el password
		$sqlText = "update employees set user_pwd='".md5( trim($_POST['passNew']) )."' where employee_id=".$_SESSION["usr_id"];
	    $dbEx->insSql($sqlText);
	    $rslt = 2;
	  }else{
	    $rslt = 0;
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

