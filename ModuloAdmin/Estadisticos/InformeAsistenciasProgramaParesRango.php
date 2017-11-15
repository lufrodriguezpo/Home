<?php session_start();
error_reporting(0);
require_once("../../funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])header("Location: ../../");
if(!isset($_SESSION["access_token"]))header("Location: ../../");
$nivel =$_SESSION['nivel'];
if((int)$nivel !=1)header("Location: ../../");

/*Proceso de la pagina*/
require_once("../../funciones/conexion.php");
require('../../pdf16/fpdf.php');
$anio = $_SESSION['anio'];
$sems = $_SESSION['semestre'];

$consulta="SELECT codarea,area FROM areas WHERE anio='$anio' AND sems='$sems' ORDER BY area";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$Areas[$codarea] = utf8_decode($registro['area']);
}

$consulta="SELECT codalu,codcita,codarea FROM citaspares WHERE anio='$anio' AND sems='$sems' AND estado='5'";
$resultado=$mysqli->query($consulta) or die("Connection failed: " . $mysqli->error.' - '.$mysqli->close().'Ok.');
while($registro = $resultado->fetch_assoc()){
	$codarea = $registro['codarea'];
	$Citas[$codarea]++;
}
class PDF extends FPDF {
	function Header() {
		$this->Image('../../images/logo_sesquiR.jpg',5,120,20);
		$this->Image('../../images/escudo_unal.png',90,10,0,40);
		$this->Ln(37);
		$this->SetFont('Arial','B',16);
		$this->Cell(0,6,'Vicerrectoría Sede Bogotá',0,1,'C');
		$this->SetFont('Arial','B',14);
		$this->Cell(0,6,'Dirección Académica',0,1,'C');
		$this->Ln(5);
	}
	function Proceso(){
		global $Citas,$Areas,$anio,$sems;
		$wt = 180;
		$w = array(70,30);
		$dif = (180-(array_sum($w)))/2;
		
		$this->SetFont('Arial','',10);
		$semsRom = array('','I','II','Intersemestral');
		$text = "A continuación se presentan el numero de citas realizadas en las diferentes areas, para el año ".$anio." y semestre ".$semsRom[$sems].':';
		$this->Cell(15);
		$this->MultiCell($wt-30,4,$text,0,'J');
		$this->Ln(2);
		
		$this->SetFont('Arial','B',12);
		$this->Cell($dif);
		$this->Cell($w[0],6,'AREA',1,0,'C');
		$this->Cell($w[1],6,'CANTIDAD',1,1,'C');
		$semsRom = array('','I','II');
		$nt = 0;
		foreach($Areas as $codarea => $area){
			$this->SetFont('Arial','',10);
			$this->Cell($dif);
			$coor = array($this->GetX(),$this->GetY());
			$this->MultiCell($w[0],6,$area,1,'L');
			$h = $this->GetY()-$coor[1];
			$this->SetXY($coor[0]+$w[0],$coor[1]);
			$this->Cell($w[1],$h,(int)$Citas[$codarea],1,1,'C');
			$nt+=(int)$Citas[$codarea];
		}
		$this->SetFont('Arial','B',10);
		$this->Cell($dif);
		$this->Cell($w[0],$h,'Total',1,0,'L');
		$this->Cell($w[1],$h,$nt,1,1,'C');
		$this->Ln(30);
		$this->Cell(65);
		
		$this->SetFont('Arial','',11);
		$this->Cell(50,6,'Firma del Responsable','T',1,'C');
	}
	function Footer(){
		$this->SetY(-15);
		$this->SetFont('Arial','I',7);
		$pie = 'Generado por Tutos-UN '.date(' l, ').date('d').' de '.date('m');
		$pie .= ' de '.date('Y').' a las '.date('g:i a');
		$this->Cell(0,10,$pie,0,0,'C');
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,1,'R');
	}
}
$pdf = new PDF('P','mm','letter');
$pdf->SetMargins(20, 10 , 20);
$pdf->SetAutoPageBreak( true ,15);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Proceso();
$pdf->Output();
?>