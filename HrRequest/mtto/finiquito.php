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
 
 
 $section->addText('Yo,JORGE OSWALDO ARAUJO ALVARADO, de 38 años de edad, de nacionalidad salvadoreña, ESTUDIANTE, del domicilio de OPICO, Departamento de LA LIBERTAD con Documento Único de Identidad Número CERO TRES DOS  CINCO OCHO DOS CUATRO OCHO GUIÓN SEISpor medio del presente documento OTORGO: a) Que este día hemos dado por terminado, con Express Teleservices S.A. DE C.V., Departamento de San Salvador,  por mutuo consentimiento y sin responsabilidad para ninguna de las partes, el Contrato Individual de Trabajo que desde el día CUATRO de OCTUBRE de dos mil DOCE hasta el día ONCE de MARZO de dos mil trece nos vinculaba y en virtud del cual desempeñaba últimamente el cargo de Agente de SERVICIO AL CLIENTE, y b) Que como consecuencia de lo anterior, en esta fecha  por este medio renuncio voluntariamente al desempeño del mencionado cargo. HAGO CONSTAR: Que la empresa Express Teleservices S.A. DE C.V., no me adeuda ninguna suma de dinero en concepto de indemnización, salarios ordinarios, ni extraordinarios, ni en concepto de vacaciones, aguinaldo, días de asueto, descansos semanales o descansos compensatorios, ni en concepto de recargos por trabajo en días de asueto o descanso semanal, o por trabajo en horas extras o nocturnas; ni en concepto de otra prestación de naturaleza laboral, puesto que todas las cantidades que devengué en esos conceptos, mientras estuve al servicio de Express Teleservices S.A. DE C.V. fueron canceladas en su oportunidad. Finalmente HAGO CONSTAR: Que no tengo nada pendiente que reclamar a Express Teleservices, en lo que se relaciona con el Contrato Individual de Trabajo que nos vinculó; como consecuencia declaro libre y solvente de toda obligación derivada de dicho contrato y exento de toda responsabilidad para conmigo, extendiéndole amplio y total FINIQUITO. En fe de lo dicho, firmo el presente documento en la ciudad de San Salvador, a los VEINITIUNO días del mes de MARZO de dos mil trece.-  ',$styleCuerpo, $styleJustif);
 $section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('',$styleCuerpo);
$section->addText('_____________________________',$styleFirma,$styleDerecha);
$section->addText('JORGE OSWALDO ARAUJO ALVARADO',$styleFirma,$styleDerecha);



$temp_file = tempnam(sys_get_temp_dir(), 'PHPWord');
$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
$objWriter->save($temp_file);
header("Content-Disposition: attachment; filename='finiquito.docx'");
readfile($temp_file);
unlink($temp_file);


?>
 