<?php

//Reporte en excel de detalle de horas en llamada
  header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
  header("Content-Disposition: attachment; filename=rpt_callDetails.xls");
  require_once("../db_funcs.php");
  require_once("../fecha_funcs.php");
  $dbEx = new DBX;
  $oFec = new OFECHA;
  
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
	
	$fec_ini = $oFec->cvDtoY($_POST['fechaIni']);
	$fec_fin = $oFec->cvDtoY($_POST['fechaFin']);

	$filtro = "";
	$filtro2 = "";
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
	//Obtiene los nombres de los agentes
	$sqlText = "select distinct(c.calldet_agentno) as agent, calldet_name, callubic_name, callubic_badge from time_calldetails c inner join time_calldetails_ubic u on c.calldet_agentno=u.calldet_agentno where calldet_logindate between date '".$fec_ini." 00:00:00' and '".$fec_fin." 23:59:59' ".$filtro." ".$filtro2." order by calldet_name";
	$dtAgent = $dbEx->selSql($sqlText);
	?>
	<table width="800px" align="center" cellpadding="2" cellspacing="1" border="1" >
    <?php
		if($dbEx->numrows>0){ ?>
        
			<tr bgcolor="#FFFFFF"><th colspan="7" align="center">Period of <?php echo $_POST['fechaIni']; ?> to the <?php echo $_POST['fechaFin'];?></th></tr>
			<tr><td><b>Agent N&deg;</b></td><td><b>Location</b></td><td><b>Badge</b></td><td><b>Name</b></td><td><b>Day time hours</b></td><td><b>Nigth Time hours ESA</b></td><td><b>Nigth Time hours USA</b></td><td><b>Total seconds</b></td><td><b>Total Hours</b></td></tr>
            <?php 
			foreach($dtAgent as $dtE){
				$sqlText = "select sum(calldet_duration) as sumHoras from time_calldetails where calldet_agentno=".$dtE['agent']." and calldet_logindate between date '".$fec_ini." 00:00:00' and '".$fec_fin." 23:59:59' ".$filtro;
				$dtTiempo = $dbEx->selSql($sqlText);
				$tiempoTotal = 0;
				if($dbEx->numrows>0){
					$tiempoTotal = $dtTiempo['0']['sumHoras'];	
				}
				?>
				<tr class="rowCons"><td><?php echo $dtE['agent'];?></td><td><?php echo $dtE['callubic_name'];?></td><td><?php echo $dtE['callubic_badge']; ?></td><td><?php echo $dtE['calldet_name'];?></td>	
                <?php 
				$sumaDia = '00:00:00';
				$sumaNoche = '00:00:00';
				$sumaNocheUSA = '00:00:00';
				
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
					?>
					<td><?php echo $diaDecimal;?></td><td><?php echo $nocheDecimal;?></td><td><?php echo $nocheUSADecimal;?></td><td><?php echo $tiempoTotal;?></td><td><?php echo $tiempoTotalDecimal;?></td></tr>
                    <?php 
			}
		}
		else{ ?>
			<tr><td>No matches</td></tr>
		<?php } ?>
		</table>
 