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
  
 
 $section->addText($_POST['txtFechaActual'],$styleFecha, $styleDerecha);
 $section->addText('',$styleCuerpo);
 $section->addText('Sres. Express Teleservices',$styleTitulo);
 $section->addText('Presente,',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Deseándole éxitos en sus labores cotidianas, aprovecho la presente para presentarles mi carta de renuncia, efectiva a partir del día de ________________, el motivo de la misma es _____________________. ',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Les agradezco la oportunidad que me brindaron al abrirmelas puertas de tan prestigiosa empresa y de antemano muchas gracias por su comprensión.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Atentamente,',$styleCuerpo, $styleJustif);

$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('_____________________________',$styleFirma);
$section->addText('Nombre de Agente',$styleCuerpo);


$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='Renuncia.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 