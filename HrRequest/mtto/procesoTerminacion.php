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
 $styleTable = array('borderSize'=>1, 'borderColor'=>'000000');
 $styleCell = array('valign'=>'center');
 $styleCellNegro = array('valign'=>'center', 'background-color: black');
 $styleBlanco = array('bold'=>false, 'size'=>12, 'name'=>'Calibri', 'spaceAfter'=>10, 'color'=>'FFFFF');
 
 $section->addText('Fecha: '.$_POST['txtFechaActual'],$styleFecha, $styleDerecha);
 $section->addText('',$styleCuerpo);
 $section->addText('Proceso de Terminación: ',$styleTitulo,$styleParagraph);
 $section->addText('',$styleCuerpo);
 $section->addText('Nombre del agente:________________________________ Departamento:_______________________________',$styleCuerpo);
 $section->addText('Supervisor responsable:____________________________');
 $section->addText('',$styleCuerpo);
 $section->addText('Causal de terminación de contrato (Supervisor):',$styleCuerpo);
 
 $PHPWord->addTableStyle('myOwnTableStyle', $styleTable, $styleParagraph);

// Add table
$tabla = $section->addTable('myOwnTableStyle');

$tabla->addRow();
$tabla->addCell(2000, $styleCellNegro)->addText('Abandono de trabajo: ',$styleBlanco);
$tabla->addCell(6000, $styleCell)->addText('(2 días laborales consecutivos ó 3 no consecutivos en un mes calendario)',$styleCuerpo);
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
header("Content-Disposition: attachment; filename='proceso de terminacion.docx'");
readfile($temp_file);
unlink($temp_file);


?>