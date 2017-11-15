<?php
require('WriteTag2.php');

$pdf=new PDF_WriteTag();
//$pdf->SetMargins(30,15,25);
$pdf->SetFont('Times','',12);
$pdf->AddPage();
$txt="Alejo asjhdkjshf dshdshjkdskjfdhjkf hsjkfhsdjkhfjksdh fsjk l";

$pdf->Cell(40,5,'Código: ',0,0,'L',false);
$pdf->WriteTag(0,10,$txt,0,"R");


$pdf->Output();
?>
