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
 $section->addText('A QUIEN INTERESE: ',$styleTitulo);
 
 $section->addText('Por la presente hacemos constar que el Sr. ___________________________, trabajó en EXPRESSTELESERVICES S.A. DE C.V. desempeñando el cargo de ______________________.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('El Sr. ________________ laboró bajo este cargo a partir de _____________, hasta ______________. Durante este periodo demostró responsabilidad y dedicación en el desempeño de sus funciones, estableció buenas relaciones interpersonales con equipo y beneficiarios.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Por lo que consideramos que es una persona capaz de desempeñar las funciones que se le asignen, lo recomendamos y le expresamos nuestro agradecimiento por su colaboración y servicios brindados.',$styleCuerpo, $styleJustif);
 
 $section->addText('',$styleCuerpo);
 $section->addText('Y para los usos que este estime convenientes, se le extiende la presente en la ciudad de San Salvador, El Salvador.',$styleCuerpo, $styleJustif);

$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('_____________________________',$styleFirma,$styleParagraph);
$section->addText('Gerente General',$styleFirma,$styleParagraph);


$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='carta de recomendacion.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 