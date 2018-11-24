<?php
require_once("../PHPWord.php");
 $PHPWord = new PHPWord();
 $section = $PHPWord->createSection();
 
 $styleTitulo = array('bold'=>true, 'size'=>12, 'name'=>'Calibri');
 
 $styleCuerpo = array('bold'=>false, 'size'=>12, 'name'=>'Calibri', 'spaceAfter'=>10);
 
 $styleFirma = array('bold'=>false, 'size'=>12, 'name'=>'Calibri');
 $styleParagraph = array('align'=>'center', 'spaceAfter'=>100);
 $styleDerecha = array('align'=>'right', 'spaceAfter'=>100);
 $styleJustif = array('align'=>'both', 'spaceAfter'=>100);
  
 $styleFecha = array('size'=>12, 'name'=>'Calibri'); 
  $styleTable = array('borderSize'=>0, 'borderColor'=>'FFFFFF');
 $styleCell = array('valign'=>'center');
 
 $section->addText('A quien corresponda: ',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 $section->addText('',$styleCuerpo);
 
 $section->addText('Por medio de la presente se hace constar que el Sr.______________________________, labora para esta empresa desde el día _________________, desempeñando el cargo de _______________________. Devengando en concepto de sueldos y salarios la cantidad de ____________________ dólares de lo cual se hacen las siguientes retenciones:',$styleCuerpo, $styleJustif);
  $section->addText('',$styleCuerpo);
 
 $PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleParagraph);

// Add table
$tabla = $section->addTable('myOwnTableStyle');

$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('Salario Mensual',$styleCuerpo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleCuerpo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('Bonificaciones',$styleCuerpo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleCuerpo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('TOTAL DEVENGADO MENSUAL',$styleTitulo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleTitulo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('RETENCIONES:',$styleTitulo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleTitulo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('ISSS 3%',$styleCuerpo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleCuerpo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('AFP 6.25%',$styleCuerpo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleCuerpo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('Renta',$styleCuerpo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleCuerpo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('Total retenciones:',$styleCuerpo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleCuerpo);
$tabla->addRow();
$tabla->addCell(4000, $styleCell)->addText('LIQUIDO MENSUAL:',$styleTitulo);
$tabla->addCell(1000, $styleCell)->addText('$',$styleTitulo);


$section->addText('',$styleCuerpo);
$section->addText('Y para los usos que el interesado estime convenientes se extiende la presente constancia en San Salvador, El Salvador a los __ días del mes de ___ de dos mil ___.',$styleCuerpo, $styleJustif);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);

$section->addText('_____________________________',$styleFirma);
$section->addText('Gerente de Recursos Humanos y Contabilidad',$styleFirma);
$section->addText('Express Teleservices S.A. de C.V.',$styleFirma);


$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='constancia de sueldos bancos.docx'");
readfile($temp_file);
unlink($temp_file);


?>