<?php

################################################
# Clase para obtener jerarquia de aprobacion #
################################################

header("Content-Type: text/html; charset=utf-8");
require_once("db_funcs.php");
require_once("fecha_funcs.php");


class APPR
{
	
	function getCantApPendientes($nombreRol)
	{

		$dbEx = new DBX;

		if($nombreRol == 'GERENTE DE AREA'){

			$sqlText = "select count(ap.id_apxemp) as cantAprob ".
				"from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap ".
				"where ifnull(AUTOR_AREA,0)=0 ".
				"and approved_status = 'P'".
				"and appr_area='Y'";
		}
		elseif ($nombreRol == 'WORKFORCE') {
			$sqlText = "select count(ap.id_apxemp) as cantAprob ".
				"from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap ".
				"where ifnull(AUTOR_WORK,0)=0 ".
				"and approved_status = 'P'".
				"and appr_workforce='Y'";
		}
		elseif ($nombreRol == 'RECURSOS HUMANOS') {
			$sqlText = "select count(ap.id_apxemp) as cantAprob ".
				"from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap ".
				"where appr_hr='Y' ".
				"and ifnull(AUTOR_HR,0)=0 ".
				"and approved_status = 'P'";	
		}
		elseif($nombreRol == 'GERENCIA'){
			$sqlText = "select count(ap.id_apxemp) as cantAprob ".
				"from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap ".
				"where appr_generalman='Y' ".
				"and ifnull(AUTOR_GENERALMAN,0)=0 ".
				"and approved_status = 'P'";

		}
		else{
			$sqlText = "select 0 from dual";
		}

		$dtC = $dbEx->selSql($sqlText);
		return($dtC['0']['cantAprob']);

	}

	function getUltimaPlaza($idEmpleado){
		$dbEx = new DBX;

		$sqlText = "select pe.id_plxemp ".
			"from plazaxemp pe inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".
			"where pe.employee_id = ".$idEmpleado." ".
			"order by ifnull(end_date,sysdate()) desc ".
			"limit 1";

		$dtId = $dbEx->selSql($sqlText);
		return $dtId['0']['id_placexdep'];

	}

	function getApPendientes($nombreRol){

		$filtro = "";

		if($nombreRol == 'GERENTE DE AREA'){

			$filtro = "and ifnull(AUTOR_AREA,0)=0 ".
				"and appr_area='Y' ".
				"and approved_status = 'P'";
		}
		elseif ($nombreRol == 'WORKFORCE') {
			$filtro = "and ifnull(AUTOR_WORK,0)=0 ".
				"and appr_workforce='Y' ".
				"and approved_status = 'P'";
		}
		elseif ($nombreRol == 'RECURSOS HUMANOS') {
			$filtro = "and appr_hr='Y' ".
				"and ifnull(AUTOR_HR,0)=0 ".
				"and approved_status = 'P'";			
		}
		elseif($nombreRol == 'GERENCIA'){
			$filtro = "and appr_generalman='Y' ".
				"and ifnull(AUTOR_GENERALMAN,0)=0 ".
				"and approved_status = 'P'";

		}

		$sqlText = "select ap.id_apxemp, ap.id_tpap, tp.name_tpap, e.employee_id, e.username, ".
				"e.firstname, e.lastname, date_format(startdate_ap,'%d/%m/%Y') as f1, hours_ap, ".
		    	"date_format(storagedate_ap, '%d/%m/%Y') as f2, ap.autor_ap, comment_ap, ".
		    	"concat(sup.firstname,' ',sup.lastname) supervisor, a.name_account ".
			"from apxemp ap inner join type_ap tp on ap.id_tpap = tp.id_tpap ".
				"inner join employees e on e.employee_id = ap.employee_id ".
				"inner join plazaxemp pe on e.employee_id = pe.employee_id ".
				"inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".
				"inner join account a on pd.id_account = a.id_account ".
				"left outer join employees sup on sup.employee_id = e.id_supervisor ".
			"where pe.id_plxemp = get_idultimaplaza(e.employee_id) ".
				$filtro;

		return $sqlText;

	}

	function getFirmas($idAp){
		$dbEx = new DBX;

		$sqlText = 
		"select ap.id_apxemp, ".
			"concat(e.firstname,' ',e.lastname) empleado, ".
			"get_ultimaplaza(e.employee_id) plaza_empleado, ".
			"'' result_empleado, ".
			"'________________________' linea_empleado, ".
			"concat(cr.firstname,' ',cr.lastname) creado_por, ".
			"get_ultimaplaza(cr.employee_id) plaza_creado, ".
			"'Aprobado' result_creado, ".
			"'________________________' linea_creado, ".
		    "if(appr_area='Y',ifnull(concat(gar.firstname,' ',gar.lastname),'Pendiente de aprobacion'),'') ger_area, ".
		    "if(appr_area='Y',ifnull(get_ultimaplaza(gar.employee_id),'Gerente de Area'),'') plaza_ger_are, ".
		    "if(appr_area='Y',elt(field(ap.approved_area,'S','N'),'Aprobado','Rechazado'),'') result_area, ".
		    "if(appr_area='Y',elt(field(ap.approved_area,'S','N'),'________________________','________________________'),'') linea_area,".
		    "if(appr_workforce='Y',ifnull(concat(wr.firstname,' ',wr.lastname),'Pendiente de aprobacion'),'') worforce, ".
		    "if(appr_workforce='Y',ifnull(get_ultimaplaza(wr.employee_id),'Workforce'),'') plaza_wr, ".
		    "if(appr_workforce='Y',elt(field(ap.approved_work,'S','N'),'Aprobado','Rechazado'),'') result_wr, ".
		    "if(appr_workforce='Y',elt(field(ap.approved_work,'S','N'),'________________________','________________________'),'') linea_wr, ".
		    "if(appr_hr='Y',ifnull(concat(hr.firstname,' ',hr.lastname),'Pendiente de aprobacion'),'') hr, ".
		    "if(appr_hr='Y',ifnull(get_ultimaplaza(hr.employee_id),'Recursos Humanos'),'') plaza_hr, ".
		    "if(appr_hr='Y',elt(field(ap.approved_hr,'S','N'),'Aprobado','Rechazado'),'') result_hr, ".
		    "if(appr_hr='Y',elt(field(ap.approved_hr,'S','N'),'________________________','________________________'),'') linea_hr, ".
		    "if(appr_generalman='Y',ifnull(concat(gr.firstname,' ',gr.lastname),'Pendiente de aprobacion'),'') gerente, ".
		    "if(appr_generalman='Y',ifnull(get_ultimaplaza(gr.employee_id),'Gerente general'),'') plaza_gerente, ".
		    "if(appr_generalman='Y',elt(field(ap.approved_general,'S','N'),'Aprobado','Rechazado'),'') result_gerente, ".
		    "if(appr_generalman='Y',elt(field(ap.approved_general,'S','N'),'________________________','________________________'),'') linea_gerente, ".
		    "apt.inactive_employee ".
		"from apxemp ap inner join employees e on ap.employee_id = e.employee_id ".
			"inner join employees cr on cr.employee_id = ap.autor_ap ".
			"inner join type_ap apt on apt.id_tpap = ap.id_tpap ".
			"left outer join employees gar on gar.employee_id = ap.autor_area ".
			"left outer join employees wr on wr.employee_id = ap.autor_work ".
			"left outer join employees hr on hr.employee_id = ap.autor_hr ".
			"left outer join employees gr on gr.employee_id = ap.autor_generalman ".
		"where ap.id_apxemp = ".$idAp;

		$dtF = $dbEx->selSql($sqlText);

		$rslt = '';

		$rslt .= '<tr><td><br><br></tr></tr><tr class="txtPag">';
				//No mostrar seccion de firmas de empleado si es AP de baja
				/*if($dtF['0']['inactive_employee'] == 'Y'){
					$rslt .= '<td colspan="2" align="center"><br><br><br></td>';

				}
				else{*/
					$rslt .= '<td colspan="2" align="center">'.$dtF['0']['linea_empleado'].
											'<br>'.$dtF['0']['empleado'].
											'<br>'.$dtF['0']['plaza_empleado'].
											'<br>'.$dtF['0']['result_empleado'].'</td>';
				//}
				$rslt .= '<td colspan="2" align="center">'.$dtF['0']['linea_wr'].
											'<br>'.$dtF['0']['worforce'].
											'<br>'.$dtF['0']['plaza_wr'].
											'<br>'.$dtF['0']['result_wr'].'</td></tr>'.
				'<tr class="txtPag">'.
				'<td colspan="2" align="center">'.$dtF['0']['linea_creado'].
											'<br>'.$dtF['0']['creado_por'].
											'<br>'.$dtF['0']['plaza_creado'].
											'<br>'.$dtF['0']['result_creado'].'</td>'.
				'<td colspan="2" align="center">'.$dtF['0']['linea_hr'].
											'<br>'.$dtF['0']['hr'].
											'<br>'.$dtF['0']['plaza_hr'].
											'<br>'.$dtF['0']['result_hr'].'</td></tr>'.
				'<tr class="txtPag">'.
				'<td colspan="2" align="center">'.$dtF['0']['linea_area'].
											'<br>'.$dtF['0']['ger_area'].
											'<br>'.$dtF['0']['plaza_ger_are'].
											'<br>'.$dtF['0']['result_area'].'</td>'.
				'<td colspan="2" align="center">'.$dtF['0']['linea_gerente'].
											'<br>'.$dtF['0']['gerente'].
											'<br>'.$dtF['0']['plaza_gerente'].
											'<br>'.$dtF['0']['result_gerente'].'</td></tr>';

		return $rslt;

	}

	function verificarAprobAp($IdAp){
		$dbEx = new DBX;
		$result = 0;
		$sqlText = "select APPROVED_STATUS from apxemp where ID_APXEMP  = ".$IdAp;

		$dtA = $dbEx->selSql($sqlText);
		if($dtA['0']['APPROVED_STATUS'] == 'A'){
			$result = 1;
		}
		//Si posee todas las aprobaciones actualiza a aprobado
		else{
			$sqlText = 
				"update apxemp ap set approved_status = 'A' ".
				"where ap.id_apxemp = ".$IdAp." ".
				"and 1 = ( ".
					"select 1 ".
				    "from type_ap tp ".
				    "where tp.id_tpap = ap.id_tpap ".
						"and ((tp.appr_area = 'Y' and approved_area = 'S') or tp.appr_area = 'N') ".
						"and ((tp.appr_workforce = 'Y' and approved_work = 'S') or tp.appr_workforce = 'N') ".
						"and ((tp.appr_hr = 'Y' and approved_hr = 'S') or tp.appr_hr = 'N') ".
						"and ((tp.appr_generalman = 'Y' and approved_general = 'S') or tp.appr_generalman = 'N') ".
						"and ap.approved_status = 'P')";

			$dbEx->updSql($sqlText);

			$rows = $dbEx->affectedRows;
            if ($rows > 0) {
				$result = 1;
   			}

   			//Si la ap esta aprobada y es de tipo baja se dara de baja la plaza activa del empleado y al empleado
			if($result == 1){
				$sqlText = "update employees set user_status=0 where employee_id = ".
								"(select ape.employee_id ".
								"from apxemp ape inner join type_ap tp on ape.id_tpap = tp.id_tpap ".
								"where tp.inactive_employee = 'Y' and ape.approved_status = 'A' ".
								"and id_apxemp = ".$IdAp." ) ";

				$dbEx->updSql($sqlText);
	    
				$sqlText = "update plazaxemp set status_plxemp='I', end_date = CURDATE() ".
							"where employee_id = (select ape.employee_id ".
								"from apxemp ape inner join type_ap tp on ape.id_tpap = tp.id_tpap ".
								"where tp.inactive_employee = 'Y' ".
								"and ape.approved_status = 'A' ".
								"and id_apxemp = ".$IdAp." ) ".
	    					"and status_plxemp = 'A' ";

	    		$dbEx->updSql($sqlText);

			}

		}

		return $result;

	}

	function getDatosAp($idAp){
		$sqlText = 
			"select ap.id_apxemp, ".
				"apt.id_tpap, ".
			    "apt.name_tpap, ".
			    "e.employee_id, ".
			    "e.firstname, ".
			    "e.lastname, ".
			    "e.username, ".
			    "ap.id_center, ".
			    "date_format(startdate_ap, '%d/%m/%Y') as f1, ".
			    "date_format(enddate_ap,'%d/%m/%Y') as f2, ".
			    "date_format(storagedate_ap,'%d/%m/%Y') as f3, ".
			    "hours_ap, ".
			    "id_tpdisciplinary, ".
			    "typesanction_ap, ".
			    "typeincap_ap, ".
			    "comment_ap, ".
			    "rejected_comments, ".
			    "name_account, ".
			    "name_depart, ".
			    "name_place ".
			"from apxemp ap inner join employees e on ap.employee_id = e.employee_id ".
				"inner join type_ap apt on apt.id_tpap = ap.id_tpap ".
				"inner join plazaxemp pe on e.employee_id = pe.employee_id ".
				"inner join placexdep pd on pe.id_placexdep = pd.id_placexdep ".
				"inner join depart_exc d on pd.id_depart = d.id_depart ".
				"inner join places p on pd.id_place = p.id_place ".
				"inner join account a on pd.id_account = a.id_account ".
			"where ap.id_apxemp = ".$idAp." ".
			"order by ifnull(pe.end_date,sysdate()) desc ".
			"limit 1";		

		return $sqlText;

	}

	function getPersonasAp($idAp){
		$sqlText = 
			"select concat(e.firstname,' ',e.lastname) empleado, ".
				"get_ultimaplaza(e.employee_id) plaza_empleado, ".
				"concat(cr.firstname,' ',cr.lastname) creado_por, ".
				"get_ultimaplaza(cr.employee_id) plaza_creado, ".
			    "if(appr_area='Y',ifnull(concat(gar.firstname,' ',gar.lastname),'Pendiente de aprobacion'),'') ger_area, ".
			    "if(appr_area='Y',ifnull(get_ultimaplaza(gar.employee_id),'Gerente de Area'),'') plaza_ger_are, ".
			    "if(appr_workforce='Y',ifnull(concat(wr.firstname,' ',wr.lastname),'Pendiente de aprobacion'),'')  worforce, ".
			    "if(appr_workforce='Y',ifnull(get_ultimaplaza(wr.employee_id),'Workforce'),'') plaza_wr, ".
			    "if(appr_hr='Y',ifnull(concat(hr.firstname,' ',hr.lastname),'Pendiente de aprobacion'),'') hr, ".
			    "if(appr_hr='Y',ifnull(get_ultimaplaza(hr.employee_id),'Recursos Humanos'),'') plaza_hr, ".
			    "if(appr_generalman='Y',ifnull(concat(gr.firstname,' ',gr.lastname),'Pendiente de aprobacion'),'') gerente, ".
			    "if(appr_generalman='Y',ifnull(get_ultimaplaza(gr.employee_id),'Gerente general'),'') plaza_gerente, ".
			    "(select concat(em.firstname,' ',em.lastname) ".
					"from employees em ".
			        "where em.employee_id = (case ".
						"when approved_area ='N' then autor_area ".
						"when approved_work = 'N' then autor_work ".
			            "when approved_hr = 'N' then autor_hr ".
			            "when approved_general = 'N' then autor_generalman end) ".
			        ") rechazado_por, ".
				"get_ultimaplaza((select em.employee_id ".
					"from employees em ".
			        "where em.employee_id = (case ".
						"when approved_area ='N' then autor_area ".
						"when approved_work = 'N' then autor_work ".
			            "when approved_hr = 'N' then autor_hr ".
			            "when approved_general = 'N' then autor_generalman end)) ".
			    ") plaza_rechazado ".
			"from apxemp ap inner join employees e on ap.employee_id = e.employee_id ".
				"inner join employees cr on cr.employee_id = ap.autor_ap ".
				"inner join type_ap apt on apt.id_tpap = ap.id_tpap ".
				"left outer join employees gar on gar.employee_id = ap.autor_area ".
				"left outer join employees wr on wr.employee_id = ap.autor_work ".
				"left outer join employees hr on hr.employee_id = ap.autor_hr ".
				"left outer join employees gr on gr.employee_id = ap.autor_generalman ".
			"where ap.id_apxemp = ".$idAp;

		return $sqlText;
	}
	
	function sendNotification($idAP){
		$dbEx = new DBX;

		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$header .= "From:SAS<sas@iamskycom.com>";

		$subject = "Notificacion de Nueva Accion de personal No ".$idAP;
		
		$msj = 'Buen d&iacute;a<br/><br/>';
        $msj .= 'Se les notifica que se ha ingresado una nueva acci&oacute;n de personal para su revisi&oacute;n y/o aprobaci&oacute;n<br/><br/>';
        $msj .= 'Ingrese a su lista de trabajo en <a href="http://sistema.skycomcallcenter.com/Skycom/">http://sistema.skycomcallcenter.com/Skycom/</a> para tomar acci&oacute;n<br>';
        $msj .= '<br/><b>No de AP: </b> '.$idAP;

		//Obtiene los correos de creado por, empleado y los roles de aprobacion
		$sqlText = "select creado_por, ".
				"cr_email, ".
				"cr_notification_flag, ".
			    "empleado, ".
			    "email, ".
			    "notification_flag, ".
			    "fecha_registro, ".
				"fecha_efectiva, ".
    			"fecha_fin, ".
    			"name_tpap, ".
    			"comment_ap, ".
				"if(rol<>'GERENTE DE AREA' and appr_area='Y','GERENTE DE AREA','X') appr_area, ".
				"if(rol<>'WORKFORCE' and appr_workforce='Y','WORKFORCE','X') appr_work, ".
				"if(rol<>'RECURSOS HUMANOS' and appr_hr='Y','RECURSOS HUMANOS','X') appr_hr, ".
				"if(rol<>'GERENCIA' and appr_generalman='Y','GERENCIA','X') appr_generalman, ".
				"inactive_employee ".
				"from( ".
				"select concat(cr.firstname,' ',cr.lastname) creado_por, ".
					"cr.email cr_email, ".
					"cr.notification_flag cr_notification_flag, ".
			        "concat(em.firstname,' ',em.lastname) empleado, ".
					"em.email email, ".
					"em.notification_flag notification_flag, ".
					"date_format(STORAGEDATE_AP,'%d-%M-%Y') fecha_registro, ".
					"date_format(STARTDATE_AP,'%d-%M-%Y') fecha_efectiva, ".
    				"date_format(ENDDATE_AP,'%d-%M-%Y') fecha_fin, ".
    				"apt.name_tpap, ".
    				"comment_ap, ".
					"apt.appr_area, ".
					"apt.appr_workforce, ".
					"apt.appr_hr, ".
					"apt.appr_generalman, ".
					"cr.employee_id, ".
					"apt.inactive_employee, ".
					"(select name_role ".
					"from placexdep pd inner join user_roles ur on pd.id_role = ur.id_role ".
						"inner join plazaxemp pe on pd.id_placexdep = pe.id_placexdep ".
					"where pe.id_plxemp = get_idultimaplaza(cr.employee_id) ) rol ".
				"from apxemp ap inner join type_ap apt on apt.id_tpap = ap.id_tpap ".
					"inner join employees cr on cr.employee_id = ap.autor_ap ".
			        "inner join employees em on em.employee_id = ap.employee_id ".
				"where ap.id_apxemp = ".$idAP.") a ";


		$dtHeader = $dbEx->selSql($sqlText);
		$correos = "";
		if($dbEx->numrows>0){

			if(strlen($dtHeader['0']['cr_email']) >0 && 
				$dtHeader['0']['cr_email'] <> 'no@email.com' &&
				$dtHeader['0']['cr_notification_flag'] == 'Y' ){
				$correos .= $dtHeader['0']['cr_email'];
			}

			if(strlen($dtHeader['0']['email']) >0 && 
				$dtHeader['0']['email'] <> 'no@email.com' &&
				$dtHeader['0']['notification_flag'] == 'Y' ){

				if(strlen($correos)>0){
					$correos .= ',';
				}
				$correos .= $dtHeader['0']['email'];
			}

			if($dtHeader['0']['inactive_employee'] == 'Y'){
				if(strlen($correos)>0){
					$correos .= ',';
				}
				$correos .= 'it@skycomcallcenter.com';
			}

			$msj .= '<br/><b>Tipo de AP: </b> '.$dtHeader['0']['name_tpap'];
	        $msj .= '<br/><b>Colaborador: </b> '.$dtHeader['0']['empleado'];
	        $msj .= '<br/><b>Creado por: </b> '.$dtHeader['0']['creado_por'];
	        $msj .= '<br/><b>Fecha de Ingreso: </b> '.$dtHeader['0']['fecha_registro'];
	        $msj .= '<br/><b>Fecha Efectiva: </b> '.$dtHeader['0']['fecha_efectiva'];
	        $msj .= '<br/><b>Fecha Fin: </b> '.$dtHeader['0']['fecha_fin'];
	        $msj .= '<br/><b>Comentario: </b> '.$dtHeader['0']['comment_ap'];
	        $msj .= '<br/><br/><br/>Nota Aclaratoria: Este es un correo informativo dise&ntilde;ado SOLO para el env&iacute;o de notificaciones a personal de la compa&ntilde;&iacute;a. No est&aacute; programado para recibir mensajes, por lo que agradecemos no responder directamente a esta cuenta de correo electr&oacute;nico.';

			//Obtener los correos 
			$sqlText = "select e.email ".
				"from employees e inner join plazaxemp pe on e.employee_id = pe.employee_id ".
					"inner join placexdep pd on pd.id_placexdep = pe.id_placexdep ".
					"inner join user_roles ur on pd.id_role = ur.id_role ".
				"where pe.id_plxemp = get_idultimaplaza(e.employee_id) ".
					"and notification_flag = 'Y' ".
					"and ur.name_role in ('".$dtHeader['0']['appr_area']."', ".
						"'".$dtHeader['0']['appr_work']."',".
						"'".$dtHeader['0']['appr_hr']."',".
						"'".$dtHeader['0']['appr_generalman']."') ".
					"and length(e.email) > 0 ".
					"and e.email <> 'no@email.com' ".
					"and e.user_status = 1 ";

			$dtDet = $dbEx->selSql($sqlText);
			if($dbEx->numrows>0){
				
				foreach ($dtDet as $dtD) {
					if(strlen($correos)>0){
						$correos .= ',';
					}
					$correos .= $dtD['email'];
				}

			}

		}

		if(mail($correos,$subject, $msj,$header)){
			echo 'EXITO';
		}else{
			echo 'ERROR';
		}
		
		
	}

}
