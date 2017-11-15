<?php
require('fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
//$pdf->AddFont('Oldendl');
//$pdf->SetFont('Oldendl','',35);
//$pdf->SetFont('Times','',12);

$pdf->AddFont('Kunstler','','Kunstler.php');
$pdf->SetFont('Kunstler','',35);


//$pdf->AddFont('Lucida','','Lucida.php');
//$pdf->SetFont('Lucida','',35);
$pdf->Cell(0,5,'LETRAS PARA DIPLOMAS',0,1,'C');
$pdf->Output();
?>
