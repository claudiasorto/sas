<?php
require_once("../PHPWord.php");
 /* 
 header('Content-type: application/vnd.ms-word');
 header("Content-Disposition: attachment; filename=carta de descuento de planilla.doc");
 header("pragma: no-cache");
 header("expire: 0");
 
 */
 $PHPWord = new PHPWord();
 $section = $PHPWord->createSection();
 
 $styleTitulo = array('bold'=>true, 'size'=>14, 'name'=>'Calibri');
 
 $styleCuerpo = array('bold'=>false, 'size'=>12, 'name'=>'Calibri');
 
 $styleJustif = array('align'=>'both', 'spaceAfter'=>100);
 
 /*
 $styleTable = array('borderSize'=>0, 'borderColor'=>'FFFFFF', 'cellMargin'=>100);
 $styleCell = array('valign'=>'center');
 */
  $styleFirma = array('bold'=>false, 'size'=>12, 'name'=>'Calibri');
  $styleParagraph = array('align'=>'center', 'spaceAfter'=>100);
 
 $section->addText('A QUIEN INTERESE: ',$styleTitulo);
 
 $section->addText('Por medio de la presente hago constar que al agente ______________ por error de planilla se le pago un extra de $ --.-- en la planilla del xx/xx/xxxx.',$styleCuerpo, $styleJustif);
 
 $section->addText('Por lo mismo firmamos este documento en el cual el agente nos autoriza a hacer el descuento respectivo de estos $--.-- de la siguiente manera: $--.-- en la planilla del xx/xx/xxxx y $ --.-- en la planilla del xx/xx/xxxx.',$styleCuerpo,$styleJustif);

$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('    _____________________________        _____________________________',$styleFirma,$styleParagraph);
$section->addText('                 Gerente General                          Agente de Servicio al Cliente',$styleFirma,$styleParagraph);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('    _____________________________        _____________________________',$styleFirma, $styleParagraph);
$section->addText('    Recursos Humanos                                  Contabilidad',$styleFirma,$styleParagraph);
/*
$tabla = $section->addTable();
$tabla->addRow(400);
$tabla->addCell(2000, $styleCell)->addText('_____________________________',$styleFirma);
$tabla->addCell(2000, $styleCell)->addText('_____________________________',$styleFirma);

$tabla->addRow();
$tabla->addCell(2000, $styleCell)->addText('Gerente General',$styleFirma);
$tabla->addCell(2000, $styleCell)->addText('Agente de Servicio al Cliente',$styleFirma);

$tabla->addRow();
$tabla->addCell(2000, $styleCell)->addText('_____________________________',$styleFirma);
$tabla->addCell(2000, $styleCell)->addText('_____________________________',$styleFirma);

$tabla->addRow();
$tabla->addCell(2000, $styleCell)->addText('Recursos Humanos',$styleFirma);
$tabla->addCell(2000, $styleCell)->addText('Contabilidad',$styleFirma);
*/

$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='carta de descuento de planilla.docx'");
readfile($temp_file); // or echo file_get_contents($temp_file);
unlink($temp_file);


?>
 