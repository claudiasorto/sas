<?php
require_once("../PHPWord.php");
 $PHPWord = new PHPWord();
 $section = $PHPWord->createSection();
 
 $styleTitulo = array('bold'=>true, 'size'=>14, 'name'=>'Calibri');
 
 $styleCuerpo = array('bold'=>false, 'size'=>12, 'name'=>'Calibri', 'spaceAfter'=>300);
 
 $styleFirma = array('bold'=>false, 'size'=>12, 'name'=>'Calibri');
 $styleParagraph = array('align'=>'center', 'spaceAfter'=>100);
 $styleDerecha = array('align'=>'right', 'spaceAfter'=>100);
 $styleJustif = array('align'=>'both', 'spaceAfter'=>100);
  
 $styleFecha = array('size'=>12, 'name'=>'Calibri'); 
 
 $section->addText('',$styleCuerpo);
 $section->addText('Sres.  ',$styleCuerpo);
 $section->addText('Dirección General de Migración y Extranjería. ',$styleCuerpo);
 $section->addText('Presente.',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Nosotros EXPRESS TELESERVICES S.A. DE C.V.  con número de identificación tributaria cero seis uno cuatro uno uno uno uno uno uno uno cero tres cuatro, con todo respeto solicitamos le conceda permiso de trabajo en nuestra empresa a la Sra. __________________________________, con número de pasaporte ____________________ quien ha  aprobado todo el proceso de admisión para desempeñar el cargo de _______________.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Y para los usos que estime convenientes se extiende la presente en la ciudad de San Salvador, el día ________________________.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('_____________________________',$styleFirma,$styleParagraph);
$section->addText('Recursos Humanos',$styleFirma,$styleParagraph);
$section->addText('Express Teleservices',$styleFirma,$styleParagraph);


$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='carta para extranjeria.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 