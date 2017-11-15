<?php
require('phplot/phplot.php');
require('mem_image.php');
$data = array(
  array('Ene', 20, 2, 4), array('Feb', 21, 3, 4), array('Mar', 20, 4, 4),
  array('Abr', 10, 5, 4), array('May',  3, 6, 4), array('Jun',  7, 7, 4),
  array('Jul', 10, 8, 4), array('Ago', 15, 9, 4), array('Sep', 20, 5, 4),
  array('Oct', 18, 4, 4), array('Nov', 16, 7, 4), array('Dic', 14, 3, 4),
);
$graph = new PHPlot(500, 200);
$graph->SetImageBorderType('plain');
$graph->SetPlotType('bars');
$graph->SetDataType('text-data');
$graph->SetDataValues($data);
$graph->SetTitle('COLEGIO DE NUESTRA SEÑORA DE CHIQUINQUIRA');
$graph->SetShading(0);
$graph->SetLegend(array('Bajos', 'Altos', 'Basicos'));
$graph->SetXTickLabelPos('none');
$graph->SetXTickPos('none');
$graph->SetPrintImage(false);
$graph->DrawGraph();

$pdf = new PDF_MemImage();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(10,5,'ESTA ES LA PRIMERA GRAFICA',0,1);
$pdf->Cell(10,5,'ESTA ES LA PRIMERA GRAFICA',0,1);
$pdf->Cell(10,5,'ESTA ES LA PRIMERA GRAFICA',0,1);
$pdf->Cell(10,5,'ESTA ES LA PRIMERA GRAFICA',0,1);
$pdf->Cell(10,5,'ESTA ES LA PRIMERA GRAFICA',0,1);
$pdf->Cell(10,5,'ESTA ES LA PRIMERA GRAFICA',0,1);
$pdf->GDImage($graph->img,30,200,140);
$pdf->Output();
?>
