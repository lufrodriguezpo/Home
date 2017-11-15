<?php
require('../fpdf.php');

require('WriteTag.php');

$pdf=new PDF_WriteTag();
$pdf->SetMargins(20,15,15);
$pdf->SetFont('Arial','',9);

//$pdf->SetFont('courier','',12);
$pdf->AddPage();

$pdf->Cell(0,5,'Hola',1,0,'C');

// Stylesheet
//$pdf->SetStyle("p","Arial","N",12,"10,100,250",5);
$pdf->SetStyle("p","Arial","N",12,"0,0,0",0);
$pdf->SetStyle("h1","Times","N",18,"102,0,102",0);
$pdf->SetStyle("a","Times","BU",8,"0,0,255");
$pdf->SetStyle("pers","Times","I",0,"255,0,0");
$pdf->SetStyle("place","Arial","U",0,"153,0,0");
$pdf->SetStyle("vb","Times","B",0,"102,153,153");

$pdf->Ln(15);

// Text
$txt="<p>Que el estudiante <place>ALEJO BUITRAGO CAMARGO</place>, identificado con Cédula de Ciudadania. No. 
<pers>4.211.298 </pers> de <vb>Pesca Boyacá</vb>, curso y <vb>APROBO</vb>, 
el grado <pers>OCTAVO </pers> del curso <vb> 601 </vb> de Educación <pers>MEDIA</pers>,
durante el año <pers>2015</pers>, de acuerdo con lo regstrado en el folio <vb>1956</vb> 
del libro final de calificaciones de <vb>Educación Basica Primaria</vb> y la matricula  
número <vb>4.211.298</vb>, de los estudiantes de la <pers>INSTITUCION EDUCATIVA DISTRITAL COSTA RICA</pers>.</p>";

$pdf->WriteTag(0,5,$txt,0,"J",0,7);

$pdf->Ln(5);

$pdf->WriteTag(0,5,$txt,0,"J",0,7);
$pdf->Ln(5);

$txt="<a href='http://www.syscolegios.com'>Alejo Buitrago Camargo</a>";
$pdf->WriteTag(0,10,$txt,0,"R");



$pdf->Output();
?>
