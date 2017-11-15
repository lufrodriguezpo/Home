<?php
require('../fpdf.php');
class PDF extends FPDF {
function Header() {
	global $title;
	$this->SetFont('Arial','B',15);
	$w=$this->GetStringWidth($title)+6;
	$this->Image('E00032011.JPG',20,8,15);
	$this->SetX((210-$w)/2);
	$this->SetDrawColor(0,60,200);
	$this->SetFillColor(230,230,0);
	$this->SetTextColor(220,50,50);
	$this->SetLineWidth(.1);
	$this->Cell($w,9,$title,1,1,'C',true);
	$this->Ln(2);
}

function Footer() {
    $w=array(50,50,20,20);
	$this->SetY($this->GetY());
	$this->Cell(array_sum($w),0,'','T');
	$this->SetY(-15);
	$this->SetFont('Arial','I',8);
	$this->SetTextColor(128);
	$this->Cell(0,10,'Página No. '.$this->PageNo(),0,0,'C');
}

function LoadData() {
	$conn=mysql_connect("localhost","root", "");	
	mysql_select_db("Notas2000",$conn);
    $conalu="SELECT nombres,apellidos,direccion,telefono FROM alumnos WHERE codcolegio='00047' LIMIT 0,207 ";
    $result = mysql_query($conalu,$conn) or die("ERROR EN LA CONSULTA ALUMNOS".mysql_error());
    if (mysql_affected_rows($conn)>0) while ($dato=mysql_fetch_array($result)) $data[]=$dato;
    mysql_close($conn);
	return $data;
}
function Firmas(){
    $txt1='JavaScript es un lenguaje interpretado en el cliente por el navegador al momento';
	$txt1.=' de cargarse la pagina, es multiplataforma, orientado a eventos con manejo de';
	$txt1.=' objetos, cuyo codigo se incluye directamente en el mismo documento HTML. ';
    $txt1.='En las usb salen los grados 7A-B, aunque tengan profesor diferente, ejemplo';
	$txt1.=' 7A Juan Fernando y 7B Luis Gabriel.';
	$this->SetFont('Times','',12);
	$this->SetX(25);	
	$this->MultiCell(100,5,$txt1);
	$this->Ln();
}

function FancyTable($header,$data) {
	//Colores, ancho de línea y fuente en negrita
	$this->SetFillColor(255,0,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,0,0);
	$this->SetLineWidth(.2);
	$this->SetFont('','B');
	//Cabecera
	$this->SetLeftMargin(25);	
	$w=array(50,50,20,20);
	for($i=0;$i<count($header);$i++) $this->Cell($w[$i],7,$header[$i],1,0,'C',1);
	$this->Ln();
	//Restauración de colores y fuentes
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	//Datos

	$fill=false;
	foreach($data as $row) 	{
		$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
		$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
		$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
		$this->Cell($w[3],6,number_format($row[3]),'LR',0,'C',$fill);
		$this->Ln();
		$fill=!$fill;
	}
	$this->Cell(array_sum($w),0,'','T');
	$this->Firmas();
}
}

$pdf=new PDF();
$header=array('Nombres','Apellidos','Direccion','Telefono');
$data=$pdf->LoadData();
$title='COLEGIO LA PRESENTACION DE NEIVA';
$pdf->SetTitle($title);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output();
?>
