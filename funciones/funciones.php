<?PHP 
function getRealIP() {
	if (!empty($_SERVER['HTTP_CLIENT_IP']))return $_SERVER['HTTP_CLIENT_IP'];
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))return $_SERVER['HTTP_X_FORWARDED_FOR'];
	return $_SERVER['REMOTE_ADDR'];
}
function ConstruirModulo($seccion, $codigo, $modulo){
	switch ($seccion) {
		case 1:
			return ContruirModuloFrontal($codigo, $modulo);
			break;
		case 2:
			return  ContruirModuloNav($codigo, $modulo);
			break;
		case 3:
			return "Por contruir";
			break;
	}
}
function ContruirModuloNav($codigo, $modulo){
	$card = $modulo['00'];
	
	$card = explode('/-/',$card);
	
	$nomSeccion = '<div class="mapa_seccion">
						<div class="mapa_seccion_img">
							&nbsp;
						</div>
						<div class="mapa_seccion_descripcion">
							'.$card[0].'
						</div>
					</div>';
					
	$opcionesHtml = '<div class="mapa_opcciones"><ul class="mapa_contenerdor_opciones">';
	$nop =0;
	foreach($modulo as $subCod => $opcion){
		if((int)$subCod != 0){
			$opcion = explode('/-/',$opcion);
			$opcionesHtml .= '<li class="mapa_opccion"  data-href="'.$opcion[2].'" data-tipo="'.$opcion[3].'" id="'.$codigo.'-'.$subCod.'">'.$opcion[0].'</li>';
			$nop++;
		}
	}
	$opcionesHtml .= '</ul></div>';
	$hrefSec = "";
	if($nop==0)$hrefSec = "data-href=".$card[5];
	$sessionHtml = '<li class="contenerdor_seccion" desplegada="false" '.$hrefSec.'>';
	$sessionHtml .= $nomSeccion;
	$sessionHtml .= $opcionesHtml;
	$sessionHtml .= '</li>';
	return $sessionHtml;
}
function ContruirModuloFrontal($codigo, $modulo){
	$card = $modulo['00'];
	
	$card = explode('/-/',$card);
	$cardHtml =		'<article class="card" id="'.$codigo.'">
						<div class="card_img" style="background-image:url('.$card[3].')">
							
							<div class="card_etiqueta">'.$card[4].'</div>
						</div>
						<div class="card_contenedor_contenido">
							<div class="card_titulo">
								'.$card[0].'
							</div>
							<div class="card_subtitulo">
								'.$card[1].'
							</div>
							<div class="card_descripcion">
								'.$card[2].'
							</div>
						</div>
					</article>';
	$menuHtml = '<article class="menu">';
	$nop =0;
	foreach($modulo as $subCod => $opcion){
		$opcion = explode('/-/',$opcion);
		if((int)$subCod != 0){
			$menuHtml .= '<div class="opcion_card" data-href="'.$opcion[2].'" data-tipo="'.$opcion[3].'" id="'.$codigo.'-'.$subCod.'">
							<div class="icono_opcion_card eOpcion_card">
								<img src="'.$opcion[1].'"/>
							</div>
							<div class="titulo_opcion_card eOpcion_card">
								'.$opcion[0].'
							</div>
							<div class="borde_opcion_card eOpcion_card">
								&nbsp;
							</div>
						</div>';
			$nop++;
		}
	}
	$menuHtml .= '</article>';
	$hrefSec = "";
	if($nop==0)$hrefSec = "data-href=".$card[5];
	$moduloHtml =  '<section class="seccion_cards" visible-menu="false" lado="'.$modulo["orientacion"].'" '.$hrefSec.'>';
	$moduloHtml .= $cardHtml;
	$moduloHtml .= $menuHtml;
	$moduloHtml .= '</section>';
	return $moduloHtml;
}
function ValidarCadena($cadena){
	$letrasPerm = array('a','A','Á','b','B','c','C','d','D','e','E','É','f','F','g','G','h','H','i','I','Í','j','J','k','K','l','L','m','M','n','N','ñ','Ñ',utf8_encode('ñ'),utf8_encode('Ñ'),'o','O','Ó','p','P','q','Q','r','R','s','S','t','T','u','U','Ú','v','V','w','W','x','X','y','Y','z','Z','1','2','3','4','5','6','7','8','9','0','#','°','¿','?','.',',','-','_','*','/','-','+','=','!','¡','(',')','{','}','$','|',' ','\n','<','>',':',';','&','%','@','
	','\n',102,10);
	for($i=0;$i<strlen($cadena);$i++){
		$val = $cadena[$i];
		if(!in_array((int)ord($val),$letrasPerm) && !in_array($val,$letrasPerm) && !in_array(utf8_encode($val),$letrasPerm) && !in_array(utf8_decode($val),$letrasPerm) ){
			echo "<--".$val."-->";
			return 0;
		}
	}
	return 1;
}

function agregar($tabla,$campos, $variables,$duplicados,$p=0){
	global $mysqli;
	$insertar ="INSERT INTO $tabla ".$campos;
	$insertar .= " VALUES ".$variables;
	$insertar .= " ON DUPLICATE KEY UPDATE ".$duplicados;
	if($p==1)echo $insertar.'<hr>';
	else $resultado=$mysqli->query($insertar) or die ("* ERROR AL GRABAR<< $tabla >> *".$mysqli->error);
}

function GuardarArchivo($Archivo,$path,$nombre,$src){
	if(file_exists($src))unlink($src);
	if (is_uploaded_file($Archivo['tmp_name'])){ 
		copy($Archivo['tmp_name'], "$path/$nombre"); 
		echo "El archivo se ha subido correctamente al servidor<p>";
	}
	else echo "Error al subir el archivo";
}

?>
