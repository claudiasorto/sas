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
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 $section->addText('A QUIEN INTERESE: ',$styleTitulo);
 
 $section->addText('Por la presente hacemos constar que el Sr. ____________________,  trabajó en EXPRESS TELESERVICES S.A. DE C.V. desde el ____________ hasta el _____________, desempeñando el cargo de ________________, y devengando un salario mensual de ___________ dólares ($______) más bono de ___________ dólares ($_____), dicho salario cuenta con los debidos descuentos de ley.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Durante este periodo demostró responsabilidad y dedicación en el desempeño de sus funciones, estableció buenas relaciones interpersonales con equipo y beneficiarios.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Por lo que consideramos que es una persona capaz de desempeñar las funciones que se le asignen, lo recomendamos y le expresamos nuestro agradecimiento por su colaboración y servicios brindados.',$styleCuerpo, $styleJustif);
 
 $section->addText('',$styleCuerpo);
 $section->addText('Y para los usos que este estime convenientes, se le extiende la presente en la ciudad de San Salvador, El Salvador a los __ días del mes de _____ de dos mil trece.',$styleCuerpo, $styleJustif);

$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('_____________________________',$styleFirma);
$section->addText('Recursos Humanos',$styleCuerpo);
$section->addText('Express Teleservices S.A. de C.V.',$styleCuerpo);


$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='recomendacion ex-empleado.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 