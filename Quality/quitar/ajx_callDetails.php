<?php
//Funciones para call details	
header("Content-Type: text/html; charset=utf-8");
require_once("../db_funcs.php");
require_once("../fecha_funcs.php");
 
$dbEx = new DBX;
$oFec = new OFECHA;
  function cargaPag($urlToLoad){ //funcion para cargar una pagina
    $pagLoad = '';
    $fp=fopen($urlToLoad,"r") or die("Error al abrir el fichero");
    $pagLoad = fread($fp,30000);
    return $pagLoad;
  }
  function calcular_tiempo_trasnc($hora1,$hora2){ 
    $separar[1]=explode(':',$hora1); 
    $separar[2]=explode(':',$hora2); 

	$total_minutos_trasncurridos[1] = ($separar[1][0]*60)+$separar[1][1]; 
	$total_minutos_trasncurridos[2] = ($separar[2][0]*60)+$separar[2][1]; 
	$total_minutos_trasncurridos = $total_minutos_trasncurridos[1]-$total_minutos_trasncurridos[2]; 
	if($total_minutos_trasncurridos<=59) return($total_minutos_trasncurridos.' Minutos'); 
	elseif($total_minutos_trasncurridos>59){ 
		$HORA_TRANSCURRIDA = round($total_minutos_trasncurridos/60); 
		if($HORA_TRANSCURRIDA<=9) $HORA_TRANSCURRIDA='0'.$HORA_TRANSCURRIDA; 
		$MINUITOS_TRANSCURRIDOS = $total_minutos_trasncurridos%60; 
		if($MINUITOS_TRANSCURRIDOS<=9) $MINUITOS_TRANSCURRIDOS='0'.$MINUITOS_TRANSCURRIDOS; 
		return ($HORA_TRANSCURRIDA.':'.$MINUITOS_TRANSCURRIDOS.':00'); 
	}
	
  }
  
  function restaHoras($horaIni, $horaFin){
	return (date("H:i:s", strtotime("00:00:00") + strtotime($horaFin) - strtotime($horaIni) ));
}
function sumahoras ($hora1,$hora2){
	$hora1=explode(":",$hora1);
	$hora2=explode(":",$hora2);
	$horas=(int)$hora1[0]+(int)$hora2[0];
	$minutos=(int)$hora1[1]+(int)$hora2[1];
	$segundos=(int)$hora1[2]+(int)$hora2[2];
	$horas+=(int)($minutos/60);
	$minutos=(int)($minutos%60)+(int)($segundos/60);
	$segundos=(int)($segundos%60);
	return (intval($horas)<10?'0'.intval($horas):intval($horas)).':'.($minutos<10?'0'.$minutos:$minutos).':'.($segundos<10?'0'.$segundos:$segundos);
}
  
switch($_POST['Do']){
	case 'formUpCallDetails':
	
		$rslt = cargaPag("../mttoCallDetails/formUpDetails.php");
		
		echo $rslt; 
	break;
	
	case 'RepotCall':
		$rslt = cargaPag("../mttoCallDetails/mttoFiltrosCall.php");
		
		echo $rslt;
	break;
	
	case 'loadReportCall':
		$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
		$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);
		$filtro2 = "";

		//Obtiene los nombres de los agentes
		$filtro = "";
		if($_POST['cuenta']>0){
			$filtro .=" and id_account=".$_POST['cuenta'];	
		}
		if($_POST['ubicacion']>0){
			if($_POST['ubicacion']==1){
				$filtro2 .=" and callubic_name like '%Expresstel%'";
			}
			else if($_POST['ubicacion']==2){
				$filtro2 .=" and callubic_name like '%Skycom%'";
			}
		}
		
		$sqlText = "select distinct(c.calldet_agentno) as agent, calldet_name, callubic_name, callubic_badge from time_calldetails c inner join time_calldetails_ubic u on c.calldet_agentno=u.calldet_agentno where calldet_logindate between date '".$fec_ini." 00:00:00' and '".$fec_fin." 23:59:59' ".$filtro." ".$filtro2." order by calldet_name";
		$dtAgent = $dbEx->selSql($sqlText);
		
		$rslt = '<table class="tblRepQA" width="900px" align="center" cellpadding="2" cellspacing="1" >';
		if($dbEx->numrows>0){
			$rslt .='<tr ><td colspan="9" align="right"><form target="_blank" action="mttoCallDetails/xls_rptCall.php" method="post"><input type="image" src="images/excel.png" alt="Exportar a excel" width="30" style="cursor:pointer" title="Export to excel" /><input type="hidden" name="fechaIni" value="'.$_POST['fechaIni'].'"><input type="hidden" name="fechaFin" value="'.$_POST['fechaFin'].'"><input type="hidden" name="cuenta" value="'.$_POST['cuenta'].'"/><input type="hidden" name="ubicacion" value="'.$_POST['ubicacion'].'"/></td></tr>';
			$rslt .='<tr bgcolor="#FFFFFF"><th colspan="9" align="center">Period of '.$_POST['fechaIni']." to the ".$_POST['fechaFin']."</th></tr>";
			
			$rslt .='<tr class="showItem"><td width="5%">Agent N&deg;</td><td width="10%">Location</td><td width="10%">Badge</td><td width="25%">Name</td><td width="10%">Day time hours</td><td width="10%">Nigth Time hours ESA</td><td width="10%">Nigth Time hours USA</td><td width="10%">Total seconds</td><td width="10%">Total Hours</td></tr>';
			foreach($dtAgent as $dtE){
				$sqlText = "select sum(calldet_duration) as sumHoras from time_calldetails where calldet_agentno=".$dtE['agent']." and calldet_logindate between date '".$fec_ini." 00:00:00' and '".$fec_fin." 23:59:59' ".$filtro;
				$dtTiempo = $dbEx->selSql($sqlText);
				$tiempoTotal = 0;
				if($dbEx->numrows>0){
					$tiempoTotal = $dtTiempo['0']['sumHoras'];	
				}
				
				$rslt .='<tr class="rowCons"><td>'.$dtE['agent'].'</td><td>'.$dtE['callubic_name'].'</td><td>'.$dtE['callubic_badge'].'</td><td>'.$dtE['calldet_name'].'</td>';	
				$sumaDia = '00:00:00';
				$sumaNoche = '00:00:00';
				$sumaNocheUSA = '00:00:00';
				//for($i = $start; $i <=$end; $i+=86400){
					$sqlText = "select date_format(calldet_logindate,'%H:%i:%s') as h1, date_format(calldet_logoffdate,'%H:%i:%s') as h2 from time_calldetails where calldet_agentno=".$dtE['agent']." and calldet_logindate between date '".$fec_ini." 00:00:00' and '".$fec_fin." 23:59:59' ".$filtro;
					$dtTiempoxEmp = $dbEx->selSql($sqlText);
					if($dbEx->numrows>0){
						foreach($dtTiempoxEmp as $dtT){
							
							$h1 = explode(":",$dtT['h1']);
							$h2 = explode(":",$dtT['h2']);
							//Evalua si la hora de inicio es menor a la hora final para ver si son dias diferentes
							if($h1[0]<=$h2[0]){
								if($h1[0]>=00 and $h1[0]<06){
									if($h2[0]<06){
										$sumaNoche = sumahoras($sumaNoche,restaHoras($dtT['h1'],$dtT['h2']));
										$sumaNocheUSA = sumahoras($sumaNocheUSA,restaHoras($dtT['h1'],$dtT['h2']));	
									}
									else if($h2[0]>=06){
										$sumaNoche = sumahoras($sumaNoche,restaHoras($dtT['h1'],'06:00:00'));
										$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras($dtT['h1'],'06:00:00'));
										if($h2[0]<19){
											$sumaDia = sumahoras($sumaDia, restaHoras('06:00:00',$dtT['h2']));	
										}
										else if($h2[0]>=19){
											$sumaDia = sumahoras($sumaDia, restaHoras('06:00:00','19:00:00'));
											$sumaNoche = sumahoras($sumaNoche, restaHoras('19:00:00',$dtT['h2']));
											if($h2[0]>=21){
												$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras('21:00:00',$dtT['h2']));
											}
											
										}
									}
								}
								else if($h1[0]>=06 and $h1[0]<19){
									if($h2[0]<19){
										$sumaDia = sumahoras($sumaDia, restaHoras($dtT['h1'],$dtT['h2']));	
									}
									else if($h2[0]>=19){
										$sumaDia = sumahoras($sumaDia, restaHoras($dtT['h1'],'19:00:00'));
										$sumaNoche = sumahoras($sumaNoche, restaHoras('19:00:00',$dtT['h2']));
										if($h2[0]>=21){
												$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras('21:00:00',$dtT['h2']));
											}
									}
								}
								else if($h1[0]>=19){
									$sumaNoche = sumahoras($sumaNoche, restaHoras($dtT['h1'],$dtT['h2']));
									if($h1[0]>=21){
											$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras($dtT['h1'],$dtT['h2']));
									}
									else if($h2[0]>=21){
										$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras('21:00:00',$dtT['h2']));	
									}
								}
								
								
							}//Termina comparacion con horas	

							//Evalua si son de dias diferentes
						else if($h1[0]>$h2[0]){
							$horaFinalMN = '23:59:59';
							$horaInicioMN = '00:00:00';
							
							//Hace conteo para las horas del primer dia
							if($h1[0]>=00 and $h1[0]<06){
								$sumaNoche = sumahoras($sumaNoche,restaHoras($dtT['h1'],'06:00:00'));
								$sumaNocheUSA = sumahoras($sumaNocheUSA,restaHoras($dtT['h1'],'06:00:00'));
								$sumaDia = sumahoras($sumaDia, restaHoras('06:00:00','19:00:00'));
								$sumaNoche = sumahoras($sumaNoche, restaHoras('19:00:00',$horaFinalMN));
								$sumaNocheUSA = sumahoras($sumaNocheUSA,restaHoras('21:00:00',$horaFinalMN));	
							}	
							else if($h1[0]>=06 and $h1[0]<19){
								$sumaDia = sumahoras($sumaDia, restaHoras($dtT['h1'],'19:00:00'));	
								$sumaNoche = sumahoras($sumaNoche, restaHoras('19:00:00',$horaFinalMN));
								$sumaNocheUSA = sumahoras($sumaNocheUSA,restaHoras('21:00:00',$horaFinalMN));
							}
							else if($h1[0]>=19 and $h1[0]<=23){
								$sumaNoche = sumahoras($sumaNoche, restaHoras($dtT['h1'],$horaFinalMN));
								if($h1[0]>=21){
									$sumaNocheUSA = sumahoras($sumaNocheUSA,restaHoras($dtT['h1'],$horaFinalMN));	
								}
								else{
									$sumaNocheUSA = sumahoras($sumaNocheUSA,restaHoras('21:00:00',$horaFinalMN));	
								}
							}
							//Hace conteo para horas del segundo dia
							if($h2[0]>=00 and $h2[0]<06){
								$sumaNoche = sumahoras($sumaNoche, restaHoras($horaInicioMN,$dtT['h2']));
								$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras($horaInicioMN, $dtT['h2']));	
							}
							else if($h2[0]>=06 and $h2[0]<19){
								$sumaNoche = sumahoras($sumaNoche, restaHoras($horaInicioMN, '06:00:00'));
								$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras($horaInicioMN, '06:00:00'));	
								$sumaDia = sumahoras($sumaDia, restaHoras('06:00:00',$dtT['h2']));
							}
							else if($h2[0]>=19 and $h2[0]<=23){
								$sumaNoche = sumahoras($sumaNoche, restaHoras($horaInicioMN,'06:00:00'));
								$sumaNocheUSA = sumahoras($sumaNocheUSA, restaHoras($horaInicioMN, '06:00:00'));	
								$sumaDia = sumahoras($sumaDia, restaHoras('06:00:00','19:00:00'));
								$sumaNoche = sumahoras($sumaDia,restaHoras('19:00:00',$dtT['h2']));
								if($h2[0]>=21){
									$sumaNocheUSA = sumahoras($sumaNocheUSA,restaHoras('21:00:00',$dtT['h2']));
								}
							}
							
						}//Termina con horas para dias diferentes
							
							
						}//termina foreach para empleado
					}//termina if
					$tiempoTotalDecimal = $tiempoTotal/3600;
					$diaDecimal = 0;
					if($sumaDia>'00:00:00'){
						$formatDiaDecimal = explode(":",$sumaDia);
						$diaDecimal = $formatDiaDecimal[0] + (($formatDiaDecimal[1])/60) + ((($formatDiaDecimal[2]*100)/3600)/100);
					}
					$nocheDecimal = 0;
					if($sumaNoche>'00:00:00'){
						$formatNocheDecimal = explode(":",$sumaNoche);
						$nocheDecimal = $formatNocheDecimal[0] + (($formatNocheDecimal[1])/60) + ((($formatNocheDecimal[2]*100)/3600)/100);
					}
					
					$nocheUSADecimal = 0;
					if($sumaNocheUSA>'00:00:00'){
						$formatNocheUSADecimal = explode(":",$sumaNocheUSA);
						$nocheUSADecimal = $formatNocheUSADecimal[0] + (($formatNocheUSADecimal[1])/60) + ((($formatNocheUSADecimal[2]*100)/3600)/100);
					}
					$dia = 0;
					if($tiempoTotalDecimal - $nocheDecimal >0){
						$dia = $tiempoTotalDecimal - $nocheDecimal;
					}
					
					$rslt .='<td>'.$dia.'</td><td>'.$nocheDecimal.'</td><td>'.$nocheUSADecimal.'</td><td>'.$tiempoTotal.'</td><td>'.$tiempoTotalDecimal.'</td></tr>';
			}
		}
		else{
			$rslt .='<tr><td>No matches</td></tr>';	
		}
		$rslt .='</table>';
		echo $rslt;
	break;
	
	case 'formDeleteCall':
		$rslt = cargaPag("../mttoCallDetails/formDeleteCall.php");
		echo $rslt;
	break;
	
	case 'deleteCalls':
		$fecIni = $oFec->cvDtoY($_POST['fecIni']);
		$fecFin = $oFec->cvDtoY($_POST['fecFin']);
		$sqlText = "delete from time_calldetails where calldet_logindate between date '".$fecIni." 00:00:00' and '".$fecFin." 23:59:59'";
		$dbEx->updSql($sqlText);
		echo "2";
	break;
	
}
?>
