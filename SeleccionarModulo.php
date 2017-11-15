<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?PHP 
header('Content-Type: text/html; charset=ISO-8859-1');
session_start();
require_once("funciones/funciones.php");
$ip = getRealIP();
/*Validacion de session*/
if($ip != $_SESSION['ip'])header("Location: ../index.php");
if(!isset($_SESSION["access_token"]))header("Location: ../index.php");

else if($_SESSION['nivel']==3)header("Location: ModuloEstudiante/index.php");
else if($_SESSION['nivel']<1)header("Location: ../index.php");
?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:Seleccionar Modulo:.</title>
<link rel="icon" type="image/png" href="images/favicon.png" />
<script src="js/jquery-1.10.2.js"></script>
<script src="js/jquery-ui.js"></script>

<link rel="stylesheet" href="css/jquery-ui.css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet"><!-- Fuente Google-->
<link rel="stylesheet" type="text/css" href="estilosSeleccionarModulo.css?v=2"/>
<script>
$(function(){
	$("#dialogo").dialog({
		height: "auto",
		width: 400,
		dialogClass: "hide-close",
		autoOpen: false,
		resizable: false,
		draggable: false,
		open: function(){$("#telon").css("display", "block");},
		close: function(){$("#telon").css("display", "none");},
		show: {effect: "clip",duration: 350}
	});
	ResizeFondo();
	$(window).resize(function(){
		ResizeFondo();
	});
	$("body").on("click",".opcion",function(){
		var href = $(this).data("href");
		window.location.href = href;
	});
});
function ResizeFondo(){
	var h = $(document).height();
	$("body").css("min-height",h+"px");
}
</script>
</head>

<body>
<div id="contenedor_opciones">
	<div id="titulo_opciones">
		¿Con que rol desea ingresar?
	</div>	
	<div id="opciones">
		<?PHP
			$roles = array(1=>array('ADMINISTRADOR','images/administrador.png','ModuloAdmin/'),
							2=>array('TUTOR','images/profesor.png','ModuloMonitor/'),
							3=>array('ESTUDIANTES','images/estudiante.png','ModuloEstudiante/'));
			
			foreach($roles as $niv => $contenido){
				$opcion = '<div class="opcion"  data-href="'.$contenido[2].'">
								<div class="opcion_imagen eOpcion"  style="background-image:url('.$contenido[1].')">
								&nbsp;
								</div>
								<div class="opcion_descripcion eOpcion">
								'.$contenido[0].'
								</div>
							</div>';
				if($_SESSION['nivel'] <= $niv && $_SESSION['nivel'] >= 1)echo $opcion;
				
			}
		?>
	</div>
</div>
</body>
</html>
