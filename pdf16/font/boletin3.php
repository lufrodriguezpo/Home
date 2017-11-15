<?php
class PDF extends FPDF {
	function Header(){
		global $nomcol,$nomcol2,$escudo,$header,$curso,$codalum,$per,$jorna,$codcol,$config,$nomestu,$fotoalu,$margender;
		global $margensup,$nomcol3,$esca,$escb,$escc,$escd,$fota,$fotb,$fotc,$fotd;	
		$this->Cell(12.5,0.1,"",0,0,'C',false);
		$this->SetX(10);
		$this->SetY($margensup);			
		$this->SetFont('Arial','B',13);
		$this->SetX(10);
		$this->Cell(12.5,5,"",0,0,'C',false);
		$this->Cell(95,5,$nomcol,0,0,'C',false);
		$this->Cell(6,5,"",0,0,'C',false);
		$this->SetFont('Arial','B',9);
		if($col=='00058')$this->Cell(14,5,"",0,0,'C',false);
		else $this->Cell(14,5,jornadas($jorna),0,0,'C',false);
		$this->Cell(2.5,5,"",0,0,'C',false);
		$this->Cell(15.5,5,$codalum,0,0,'C',false);
		$this->Cell(2.5,5,"",0,0,'C',false);
		$this->Cell(11,5,procur($curso),0,0,'C',false);
		$this->Cell(3,5,"",0,0,'C',false);
		$this->Cell(18.5,5,periodo1($per),0,0,'C',false);
		$this->Cell(3,5,"",0,0,'C',false);
		$this->Cell(12.5,5,$config[2],0,0,'C',false);
		$this->Ln();
		$this->SetFont('Arial','B',7);
		$this->Cell(12.5,3.5,"",0,0,'C',false);
		$this->Cell(95,3.5,$nomcol2,0,0,'C',false);
		$this->Ln();
		$this->SetX(10);
		$this->Cell(12.5,5,"",0,0,'C',false);
		$this->Cell(95,5,$nomcol3,0,0,'C',false);
		$this->SetFont('Arial','B',10);		
		$this->Cell(6,5,"",0,0,'C',false);
		$this->Cell(82.5,5,$nomestu,0,0,'L',false);
		$this->Ln(7.5);
		if($col=='00058')$ws=array(101,7,7,14,14,14,14,27);
		else $ws=array(95,7,7,7,7.1,7.1,7.05,7.05,6.9,6.9,6.9,6.9,25.7);
		//else $ws=array(95,7,7,7,14.2,14.1,13.8,13.8,30.1);
		$this->Ln();
		$this->SetX($margender);		
		if($col=='00058'){
			$this->Image('../barra1.jpg',$margender,$margensup+21,197.6,6);
			for($i=0;$i<count($header);$i++){
		 	$this->SetFont('Arial','B',8);	
		 	if($i=="0"){
				$this->SetFont('Arial','B',14);
				$this->Cell($ws[$i],6,$header[$i],1,0,'C',FALSE);
			}else{
				if($i=="6"){
					$this->SetFont('Arial','B',6);
					$this->Cell($ws[$i],6,$header[$i],1,0,'C',FALSE);
				}else $this->Cell($ws[$i],6,$header[$i],1,0,'C',FALSE); 
			}
		}
		}else{
			$this->Image('../barra1.jpg',$margender,$margensup+21,197.5,6);
			for($i=0;$i<count($header);$i++){
				//$this->SetFont('Arial','B',8);				
				if($i==3 ||$i==2 ||$i==5 ||$i==7 ||$i==9 ||$i==11 || $i==13)$this->SetFont('Arial','',7);
				else $this->SetFont('Arial','B',9);
				$this->Cell($ws[$i],6,$header[$i],1,0,'C',FALSE);
			}
		}
		$ty=0;
		$this->Ln();
	}
	function Footer(){
		$ty=$this->GetY();
		//$this->Cell(30,6,$ty,1,0,'C',FALSE);
	}	
	function Firma(){
		$this->SetFont('Arial','B',8);	
		$this->Ln(7);
		$this->Cell(35,5,"Observaciones:",0,0,'L',FALSE);
		$this->Cell(95,5,"","B",0,'L',FALSE);
		$this->Cell(5,5,"",0,0,'C',FALSE);
		$this->Cell(63,5,"",0,0,'C',FALSE);
		$this->Ln();
		$this->Cell(130,5,"","B",0,'C',FALSE);
		$this->Cell(5,5,"",0,0,'C',FALSE);
		$this->Cell(63,5,"","B",0,'C',FALSE);
		$this->Ln();
		$this->Cell(130,5,"","B",0,'C',FALSE);
		$this->Cell(5,5,"",0,0,'C',FALSE);
		$this->Cell(63,5,"DIRECTOR DE GRUPO",0,0,'C',FALSE);		
	}
	function NotasArea($notas){	
		if(!empty($notas)){
			foreach ($notas as $indice=>$datos){
				$area=substr($indice,0,4);
				for($tz=1;$tz<5;$tz++){
					$ntarea[$area][$tz]=$ntarea[$area][$tz]+round($datos[$tz],1);
					$ntarea[$area][$tz+5]=$ntarea[$area][$tz+5]+1;
				}
			}	
		}
		return $ntarea;
	}	
	function ImprimaAcademico($header,$data,$areas,$ran,$ntarea,$per,$fdr1,$margender,$fotoalu,$fota,$puesto,$indica,$areas1,$np1){	
		global $config,$npesto;
		//print_r($ntarea);
		$this->SetX($margender);
		$this->SetFont('Arial','B',14);
		$this->SetX($margender);
		$this->SetFont('Arial','B',14);
		if(!empty($fotoalu))$this->Image($fotoalu,$fota[0],$fota[1],$fota[2],$fota[3]);
		$w=array(101,7,7,7,7,7,7,7,7,7,7,18);
		$this->SetLeftMargin($margender);	
		$this->SetFillColor(180,180,180);
		$this->SetTextColor(0);
		$this->SetDrawColor(0,0,0);
		$this->SetLineWidth(.15);
		$this->SetFont('Arial','B',12);
		$this->SetX($margender);	
		$this->SetLeftMargin($margender);	
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);		
		$contap=0;
		$contab=0;
		$nprom=0;
		if(!empty($areas1)){
			foreach($areas1 as $row) {
				$this->SetFillColor(180,180,180);
				$ntc=substr($row,2,2);
				$this->SetFont('Arial','B',10);
				$narea=$row;				
				$ty=$this->GetY();
				if($ty>264)$ty=$margensup+33;
				$this->MultiCell(95,6,$areas[$row],0,'C');
				$this->Line($margender,$ty,$margender+197.5,$ty);
				$sy=$this->GetY();
				if($ty>$sy)$ty=$margensup+35;	
				$this->Ln(-6);
				if(!empty($ntarea[$row][6]))$narea1[1]=$ntarea[$row][1]/$ntarea[$row][6];
				if(!empty($ntarea[$row][7]))$narea1[2]=$ntarea[$row][2]/$ntarea[$row][7];
				if(!empty($ntarea[$row][8]))$narea1[3]=$ntarea[$row][3]/$ntarea[$row][8];
				if(!empty($ntarea[$row][9]))$narea1[4]=$ntarea[$row][4]/$ntarea[$row][9];
				$this->Cell(95,6,"",0,0,'C');
				$this->Cell(7,6,"",0,0,'C');
				$this->Cell(7,6,"",0,0,'C');
				$this->Cell(7,6,"",0,0,'C');
				$this->Cell(7,6,fallas(number_format($narea1[1], 1, '.', '')),0,0,'C');
				$this->Cell(7,6,"",0,0,'C');
				$this->Cell(7,6,fallas(number_format($narea1[2], 1, '.', '')),0,0,'C');	
				$this->Cell(7,6,"",0,0,'C');	
				$this->Cell(7,6,fallas(number_format($narea1[3], 1, '.', '')),0,0,'C');	
				$this->Cell(7,6,"",0,0,'C');	
				$this->Cell(7,6,fallas(number_format($narea1[4], 1, '.', '')),0,0,'C');	
				$this->Cell(7,6,"",0,0,'C');		
				$this->Cell(26,6,busquedese($narea1[$per],$ran),0,0,'C');		
				$this->Line($margender+102, $ty,$margender+102,$sy); 
				$this->Line($margender+109, $ty,$margender+109,$sy); 
				$this->SetLineWidth(0.15);
				$this->Line($margender+116.05, $ty,$margender+116.05,$sy); 
				$this->SetLineWidth(0.05);
				$this->Line($margender+123.1, $ty,$margender+123.1,$sy); 
				$this->SetLineWidth(0.15);
				$this->Line($margender+130.15, $ty,$margender+130.15,$sy); 
				$this->SetLineWidth(0.05);
				$this->Line($margender+137.2, $ty,$margender+137.2,$sy); 
				$this->SetLineWidth(0.15);
				$this->Line($margender+144.25, $ty,$margender+144.25,$sy); 
				$this->SetLineWidth(0.05);
				$this->Line($margender+151.3, $ty,$margender+151.3,$sy); 
				$this->SetLineWidth(0.15);
				$this->Line($margender+158.05, $ty,$margender+158.05,$sy); 
				$this->SetLineWidth(0.05);
				$this->Line($margender+165.1, $ty,$margender+165.1,$sy); 
				$this->SetLineWidth(0.15);
				$this->Line($margender+171.85, $ty,$margender+171.85,$sy); 
				$y=$this->GetY();
				$this->Ln();
				$this->ImprimeMateriasAcademico($data,$ntc,$ran,$per,$fdr1,$indica);	
			}
		}
		$this->SetLineWidth(0.05);
		$this->Ln();
		$this->SetFont('Arial','B',10);
		$this->Cell(49,5,"PROMEDIO :",0,0,'C');
		$this->Cell(49,5,$puesto[0],0,0,'C');
		$this->Cell(49,5,"PUESTO EN EL CURSO:",0,0,'C');
		$this->Cell(49,5,$puesto[1],0,0,'C');
		$this->Ln();
	}	
	//////******
	function Imprimeindicadores($fdr,$indica,$r){
		$this->SetFont('Arial','',7);
		global $margender;		
		if($r=="D"){
			$s=1;
			$tip="DIFICULTADES";
		}else{
		 	$s=2;
			$tip="RECOMENDACIONES";			
		}
		$ppt=0;
		foreach($fdr as $vsa=>$txt){						
			if(!empty($indica[$txt][$s])){
				if($ppt==0){
					$ppt=1;
					$this->SetX($margender);	
					$this->Cell(30,4,$tip,0,0,'L');
				}
				$this->SetFont('Arial','',10);	
				$this->SetX(30+$margender);
				$txt=$indica[$txt][$s];
				$tff=substr($txt,1,1);				
				if($tff==":" ||$tff=="_"||$tff==" "||$tff=="."){
					$ttc=trim(substr($txt,2));
					$tfg=substr($ttc,1,1);
					if($tfg==":" ||$tfg=="_"||$tfg==" "||$tfg=="."||$tfg=="-")$htn=trim(substr($ttc,2));
					else $htn=Frase($ttc);
					$this->MultiCell(167,5,$htn,0);
				}else $this->MultiCell(167,5,$txt,0);
			}
		}
		$this->SetX($margender);
		$this->Cell(197.5,1," ","T",0,'C');
		$this->Ln(0);
	}
	function Imprimeprocesos($fdr){
		$this->SetFont('courier','',7);
		global $margender;		
		$this->Cell(30,4,"FORTALEZAS",0,0,'L');
		foreach($fdr as $vsa=>$txt){			
			$this->SetX(30+$margender);
			$tff=substr($txt,1,1);
			$this->SetFont('Arial','',10);
			if($tff==":" ||$tff=="_"||$tff==" "||$tff=="."){
				$ttc=trim(substr($txt,2));
				$tfg=substr($ttc,1,1);
				if($tfg==":" ||$tfg=="_"||$tfg==" "||$tfg=="."||$tfg=="-")$htn=trim(substr($ttc,2));
				else $htn=Frase($ttc);
				$this->MultiCell(167,5,$htn,0);
			}else $this->MultiCell(167,5,trim($txt),0,0); 
		}
		$this->Cell(197.5,1," ","T",0,'C');
		$this->Ln(0);		
	}
	function ImprimeMateriasAcademico($data,$coda,$ran,$per,$fdr1,$indica){	
		global $config,$col,$margensup,$margender;
		$this->SetFont('Arial','',9);
		$this->SetFillColor(0,0,0);		
		$w=array(90,7,7,7);
		$fill=false;
		if(!empty($data)){
			foreach($data[1] as $idx=>$row){
				$this->SetFont('Arial','',9);
				if (substr($idx,2,2)==$coda) {
					$this->SetFillColor(180,180,180);
					//****
					$ntc=substr($row,2,2);
					$this->SetFont('Arial','',9);
					$narea=$row;				
					$ty=$this->GetY();
					if($ty>264)$ty=$margensup+33;
					$this->MultiCell(95,6,$row[0],0,'C');
					$this->Line($margender,$ty,$margender+197.5,$ty);
					$sy=$this->GetY();	
					if($ty>$sy)$ty=$margensup+33;
					$this->Ln(-6);	
					$this->Line($margender+102, $ty,$margender+102,$sy); 
					$this->Line($margender+109, $ty,$margender+109,$sy); 
					$this->SetLineWidth(0.15);
					$this->Line($margender+116.05, $ty,$margender+116.05,$sy); 
					$this->SetLineWidth(0.05);
					$this->Line($margender+123.1, $ty,$margender+123.1,$sy); 
					$this->SetLineWidth(0.15);
					$this->Line($margender+130.15, $ty,$margender+130.15,$sy); 
					$this->SetLineWidth(0.05);
					$this->Line($margender+137.2, $ty,$margender+137.2,$sy); 
					$this->SetLineWidth(0.15);
					$this->Line($margender+144.25, $ty,$margender+144.25,$sy); 
					$this->SetLineWidth(0.05);
					$this->Line($margender+151.3, $ty,$margender+151.3,$sy); 
					$this->SetLineWidth(0.15);
					$this->Line($margender+158.05, $ty,$margender+158.05,$sy); 
					$this->SetLineWidth(0.05);
					$this->Line($margender+165.1, $ty,$margender+165.1,$sy); 
					$this->SetLineWidth(0.15);
					$this->Line($margender+171.85, $ty,$margender+171.85,$sy); 
					$fls1=$data[2][$idx][1]+$data[2][$idx][2]+$data[2][$idx][3]+$data[2][$idx][4];
					//$fls12=$data[2][$idx][1]." ".$data[2][$idx][2]." ".$data[2][$idx][3]." ".+$data[2][$idx][4];
					$this->Cell(95,6,"",0,0,'C');
					$this->Cell(7,6,$row[11],1,0,'C');
					$this->Cell(7,6,fallas($data[2][$idx][$per])." ".$fls12,0,0,'C');
					$this->Cell(7,6,fallas($fls1),0,0,'C');
					$this->Cell(7,6,fallas(number_format($row[1], 1, '.', '')),0,0,'C');	
					$this->Cell(7,6,fallas(number_format($row[7], 1, '.', '')),0,0,'C');	
					$this->Cell(7,6,fallas(number_format($row[2], 1, '.', '')),0,0,'C');	
					$this->Cell(7,6,fallas(number_format($row[8], 1, '.', '')),0,0,'C');	
					$this->Cell(7,6,fallas(number_format($row[3], 1, '.', '')),0,0,'C');	
					$this->Cell(7,6,fallas(number_format($row[9], 1, '.', '')),0,0,'C');	
					$this->Cell(7,6,fallas(number_format($row[4], 1, '.', '')),0,0,'C');	
					$this->Cell(7,6,fallas(number_format($row[10], 1, '.', '')),0,0,'C');	
					$this->Cell(30,6,busquedese($row[$per],$ran),0,0,'C');				
					$this->Ln();
					$this->Cell(197.5,1," ","T",0,'C');
					$this->Ln(0);
					if(!empty($fdr1[$idx]['S']))$this->Imprimeprocesos($fdr1[$idx]['S']);
					if(!empty($fdr1[$idx]['N']))$this->Imprimeindicadores($fdr1[$idx]['N'],$indica[$idx],'D');
					if(!empty($fdr1[$idx]['N']))$this->Imprimeindicadores($fdr1[$idx]['N'],$indica[$idx],'R');//*/
				}
			}
		}		
	}
}
	/////*******
	
?>
