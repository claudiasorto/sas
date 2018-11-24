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
 
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 $section->addText('Señores Embajada Americana. ',$styleTitulo);
 $section->addText('Presente. ',$styleTitulo);
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Por este medio hacemos constar que ______________________ trabaja en Express Teleservices S.A. de C.V. desde el ____________ a la fecha, desempeñando el cargo de ______________________, devengando un salario mensual de ____________________ ($______) en concepto de ____________________.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Y para los usos que estime conveniente se extiende la presente en la ciudad de San Salvador, a los __ días del mes de ______ de dos mil trece.',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('    _____________________________        _____________________________',$styleFirma, $styleParagraph);
$section->addText(' Gerente de Recursos Humanos                              Gerente General',$styleFirma,$styleParagraph);
$section->addText('     Express Teleservices S.A. de C.V.             Express Teleservices S.A. de C.V.',$styleFirma,$styleParagraph);

$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='constancia para embajada.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 