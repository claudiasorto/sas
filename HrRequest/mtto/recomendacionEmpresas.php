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
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 $section->addText('A QUIEN INTERESE: ',$styleTitulo);
 
 $section->addText('Por medio de la presente, tenemos el placer de informarles que la empresa __________________, es un valioso elemento para nuestra institución, nos presta sus servicios de __________________ excediendo nuestras expectativas en higiene, calidad y buen servicio.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Nuestras relaciones comerciales han sido hasta hoy de total satisfacción.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Y por lo tanto para los usos que estime conveniente se extiende la presente en la ciudad de San Salvador.',$styleCuerpo, $styleJustif);
 

$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('_____________________________',$styleFirma,$styleParagraph);
$section->addText('Recursos Humanos',$styleFirma,$styleParagraph);
$section->addText('Express Teleservices S.A. de C.V.',$styleFirma,$styleParagraph);


$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='carta de recomendacion de empresas.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 