<?php
require_once("../PHPWord.php");
 $PHPWord = new PHPWord();
 $section = $PHPWord->createSection();
 
 $styleTitulo = array('bold'=>true, 'size'=>12, 'name'=>'Calibri');
 
 $styleCuerpo = array('bold'=>false, 'size'=>12, 'name'=>'Calibri', 'spaceAfter'=>200);
 
 $styleFirma = array('bold'=>false, 'size'=>12, 'name'=>'Calibri');
 $styleParagraph = array('align'=>'center', 'spaceAfter'=>100);
 $styleDerecha = array('align'=>'right', 'spaceAfter'=>100);
 $styleJustif = array('align'=>'both', 'spaceAfter'=>100);
  
 $styleFecha = array('size'=>12, 'name'=>'Calibri'); 
 
 $section->addText($_POST['txtFechaActual'],$styleFecha, $styleDerecha);
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 $section->addText('Señores ',$styleTitulo);
 $section->addText('Instituto Salvadoreño del Seguro Social ',$styleTitulo);
 $section->addText('Presente, ',$styleTitulo);
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Por este medio hacemos constar que el Sr. _________________________, trabaja en Express Teleservices S.A. de C.V. desde el __________________ a la fecha, desempeñando el cargo de ____________________.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('El empleado con No. de afiliación ___________ presentó incapacidad el día ______________ con sello de clínica o servicio ______, por __ días a partir de la fecha.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 $section->addText('Se solicita una reposición de la incapacidad debido al deterioro de la inicial.',$styleCuerpo, $styleJustif);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('_____________________________',$styleFirma);
$section->addText('Gerente de Recursos Humanos y Contabilidad',$styleFirma);
$section->addText('Express Teleservices S.A. de C.V.',$styleFirma);


$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='constancia ISSS.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 